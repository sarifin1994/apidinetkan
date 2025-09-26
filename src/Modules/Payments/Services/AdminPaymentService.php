<?php

namespace Modules\Payments\Services;

use App\Models\Coupon;
use App\Models\Coupon_license;
use App\Models\Coupon_user;
use App\Models\Invoice;
use App\Models\User;
use App\Models\License;
use App\Models\AdminInvoice;
use Illuminate\Support\Carbon;
use App\Enums\InvoiceStatusEnum;
use App\Enums\TransactionTypeEnum;
use App\Enums\TransactionCategoryEnum;
use App\Enums\TransactionPaymentMethodEnum;
use App\Settings\SiteSettings;
use Modules\Payments\Repositories\Contracts\TransaksiRepositoryInterface;
use Modules\Payments\Repositories\Contracts\AdminInvoiceRepositoryInterface;
use Modules\Payments\ValueObjects\PriceData;

final class AdminPaymentService
{
    public function __construct(
        private AdminInvoiceRepositoryInterface $adminInvoiceRepo,
        private TransaksiRepositoryInterface $transaksiRepo,
        private TripayService $tripayService,
        private DuitkuService $duitkuService
    ) {}

    public function setTripayService(TripayService $tripayService): void
    {
        $this->tripayService = $tripayService;
    }

    public function setDuitkuService(DuitkuService $duitkuService): void
    {
        $this->duitkuService = $duitkuService;
    }

    /**
     * Calculate license order price details.
     */
    public function calculateLicenseOrderPrice(License $license, User $user, ?AdminInvoice $invoice = null): PriceData
    {
        if ($invoice) {
            return new PriceData(
                $invoice->price,
                $invoice->ppn,
                0,
                $invoice?->fee ?: 0,
                $invoice?->ppn ?: 0,
                $invoice?->discount ?: 0,
                0,
                $invoice?->discount_coupon ?: 0
        );
        }

        $settings = app(SiteSettings::class);
        $currentLicense = $invoice ? License::find($invoice->itemable_id) : License::find($user->license_id);
        $currentPrice = $currentLicense ? $currentLicense->price : 0;
        $isProrate = $currentLicense && $currentLicense->id !== $license->id;

        if ($isProrate && $currentPrice > 0) {
            $priceDifference = $license->price - $currentPrice;

            $nextDue = $user->next_due ? Carbon::parse($user->next_due) : Carbon::now()->addMonthNoOverflow();
            $remainingDays = $invoice ? Carbon::parse($invoice->invoice_date)->diffInDays(Carbon::parse($invoice->due_date)) : Carbon::now()->diffInDays($nextDue);
            $remainingDays = max($remainingDays, 0);

            $price = ($remainingDays / 30) * $priceDifference;
            $price = (int) round($price);
        } else {
            $price = $license->price;
        }

        return new PriceData(
            $price,
            $settings->ppn,
            0,
            $settings->admin_fee
        );
    }

    /**
     * Create or retrieve an unpaid AdminInvoice for a license purchase or renewal.
     */
    public function createLicenseInvoice(License $license, User $user,$couponCode = ""): AdminInvoice
    {
        // Check if existing invoice (for same license) is pending payment
        $existingInvoice = AdminInvoice::where('group_id', $user->id_group)
            ->where('status', InvoiceStatusEnum::UNPAID)
            ->where('itemable_id', $license->id)
            ->where('itemable_type', License::class)
            ->first();

        if ($existingInvoice) {
            return $existingInvoice;
        }

        $isRenewal = ($user->license_id === $license->id);

        // If user has a next_due, assume we start from there; else start new period
        $dueDate = $user->next_due ? Carbon::parse($user->next_due) : Carbon::now();
        $periodStart = $dueDate->format('Y-m-d');
        $periodEnd = Carbon::parse($dueDate)->addMonthNoOverflow()->format('Y-m-d');
        $subscribe = Carbon::parse($periodStart)->format('d/m/Y') . ' s/d ' . Carbon::parse($periodEnd)->format('d/m/Y');

        // Price calculation
        $priceData = $this->calculateLicenseOrderPrice($license, $user);
        $priceDatanew = $this->get_promo_coupon($user,$license,$priceData, $couponCode);
        $priceData = $priceDatanew['priceData'];
//        print_r($priceData);exit;
        $noInvoice = date('m') . rand(0000000, 9999999);

        $invoice = new AdminInvoice([
            'group_id'      => $user->id_group,
            'itemable_id'   => $license->id,
            'itemable_type' => License::class,
            'no_invoice'    => $noInvoice,
            'item'          => ($isRenewal ? 'License Renewal: ' : 'License Purchase: ') . $license->name,
            'price'         => $priceData->price,
            'ppn'           => $priceData->ppnPercentage,
            'fee'           => $priceData->adminFee,
            'discount'      => 0,
            'discount_coupon' => $priceData->discountCoupon,
            'invoice_date'  => Carbon::now(),
            'due_date'      => $dueDate->format('Y-m-d'),
            'period'        => $periodEnd,
            'subscribe'     => $subscribe,
            'payment_type'  => 'Prabayar',
            'billing_period' => 'Fixed Date',
            'payment_url'   => route('admin.invoice', $noInvoice),
            'status'        => InvoiceStatusEnum::UNPAID,
            'coupon_name'   => $couponCode
        ]);

        $this->adminInvoiceRepo->save($invoice);

        return $invoice;
    }

    /**
     * Create a Tripay transaction for admin invoice.
     */
    public function createTripayTransaction(AdminInvoice $invoice, string $paymentMethod): array
    {
        $user = $invoice->admin;

        if (!$user) {
            return null;
        }

        $ppn = ($invoice->price * $invoice->ppn) / 100;
        $adminFee = $invoice->price > 0 ? $invoice->admin_fee : 0;
        $total = $invoice->price + $ppn + $adminFee;

        $signature = $this->tripayService->createSignature($invoice->no_invoice, $total);

        $items = [
            [
                'sku'      => 'sku',
                'name'     => $invoice->item,
                'price'    => $invoice->price,
                'quantity' => 1,
            ],
        ];

        if ($ppn > 0) {
            $items[] = [
                'sku'      => 'ppn',
                'name'     => 'PPN',
                'price'    => $ppn,
                'quantity' => 1,
            ];
        }

        if ($adminFee > 0) {
            $items[] = [
                'sku'      => 'admin_fee',
                'name'     => 'Biaya Admin',
                'price'    => $adminFee,
                'quantity' => 1,
            ];
        }

        $data = [
            'method'         => $paymentMethod,
            'merchant_ref'   => $invoice->no_invoice,
            'amount'         => $total,
            'customer_name'  => $user->name ?? 'Admin',
            'customer_email' => $user->email ?? 'no-reply@example.com',
            'customer_phone' => $user->whatsapp ?? '0000000000',
            'order_items'    => $items,
            'callback_url' => route('notification.admin.tripay'),
            'return_url'   => route('admin.invoice', $invoice->no_invoice),
            'expired_time' => time() + (24 * 60 * 60),
            'signature'    => $signature,
        ];

        $response = $this->tripayService->createTransaction($invoice, $data);

        return $response;
    }

    public function createDuitkuTransaction(AdminInvoice $invoice)
    {
        $response = $this->duitkuService->createTransaction($invoice);
        return $response['paymentUrl'];
    }

    /**
     * Handle Tripay notification for admin invoice payments.
     */
    public function handleTripayNotification(array $requestData, ?string $callbackSignature): ?AdminInvoice
    {
        $invoice = $this->tripayService->handleAdminNotification($requestData);

        if ($invoice && $invoice->status === InvoiceStatusEnum::UNPAID) {
            $this->markInvoiceAsPaid($invoice);
            return $invoice;
        }

        return null;
    }

    /**
     * Handle Duitku notification for admin invoice payments.
     */
    public function handleDuitkuNotification(array $requestData): ?AdminInvoice
    {
        $invoice = $this->duitkuService->handleAdminNotification($requestData);

        if ($invoice && $invoice->status === InvoiceStatusEnum::UNPAID) {
            $this->markInvoiceAsPaid($invoice);
            return $invoice;
        }

        return null;
    }

    /**
     * Mark invoice as paid and record the transaction.
     */
    public function markInvoiceAsPaid(AdminInvoice $invoice): void
    {
        // Update the invoice status to paid
        $invoice->update([
            'paid_date' => Carbon::today()->toDateString(),
            'status'    => InvoiceStatusEnum::PAID,
        ]);

        // Retrieve the admin user and the associated license
        $adminUser = User::where('id_group', $invoice->group_id)
            ->where('role', 'Admin')
            ->first();
        $license = License::find($invoice->itemable_id);

        // Ensure the necessary data exists
        if (!$adminUser || !$license) {
            return;
        }

        // Determine whether to extend the due date or not
        $isRenewal = $adminUser->license_id === $license->id;
        $isProrate = $adminUser->license_id !== $license->id && $adminUser->next_due && Carbon::parse($adminUser->next_due)->isFuture();
        $isNew = !$isRenewal && !$isProrate;
        $shouldExtend = $isRenewal || $isNew;

        // Calculate the next due date correctly
        $next_due = $shouldExtend ? Carbon::parse($adminUser->next_due)->addMonthsNoOverflow(1) : $adminUser->next_due;

        // Update the admin user's license and next due date
        $adminUser->update([
            'status'     => 1,  // Ensure the user is active
            'license_id' => $license->id,
            'next_due'   => $next_due,
        ]);

        // Update all non-admin users in the group
        $users = User::where('id_group', $adminUser->id_group)
            ->where('role', '!=', 'Admin')
            ->get();

        foreach ($users as $user) {
            $user->update([
                'status'     => 1,
                'license_id' => $license->id,
            ]);
        }

        // Record the transaction
        $this->transaksiRepo->create([
            'group_id'       => $invoice->group_id,
            'invoice_id'     => $invoice->id,
            'invoice_type'   => AdminInvoice::class,
            'type'           => TransactionTypeEnum::EXPENSE->value,
            'category'       => TransactionCategoryEnum::LICENSE->value,
            'item'           => 'License ' . $license->name,
            'deskripsi'      => "Payment for " . strtolower($invoice->item),
            'price'          => $invoice->price,
            'tanggal'        => $invoice->paid_date,
            'payment_method' => TransactionPaymentMethodEnum::TRANSFER->value,
            'admin'          => 'system',
        ]);

        $unpaidInvoices = AdminInvoice::where('group_id', $invoice->group_id)
            ->where('status', InvoiceStatusEnum::UNPAID)
            ->where('itemable_id', $license->id)
            ->where('itemable_type', License::class)
            ->get();

        foreach ($unpaidInvoices as $unpaidInvoice) {
            $unpaidInvoice->delete();
        }
    }

    public function get_promo_coupon(User $user, License $license, PriceData $priceData, $couponCode){
        // validate coupon
        $cekCoupon = null;
        $totalDiscount = 0;
        $cekCouponUser = null;
        $checkCouponLic = null;
        $now = date('Y-m-d');
        if($couponCode != null && $couponCode != ''){
            $dataCoupon = Coupon::where('coupon_name', $couponCode)->first();
            if(!$dataCoupon){
                return array(
                    'success' => false,
                    'messages' => 'Coupon not found',
                    'priceData' => $priceData);
            }
            $cekCouponUser = Coupon_user::with('coupon')
                ->where('user_id', $user->id)
                ->where('coupon_id', $dataCoupon->id)->first();
            $checkCouponLic = Coupon_license::with('coupon')
                ->where('license_id', $license->id)
                ->where('coupon_id', $dataCoupon->id)->first();
            if($checkCouponLic){
                // cek masa berlakunya
                if($checkCouponLic->coupon->start_date <= $now && $now <= $checkCouponLic->coupon->end_date){
                    // masih berlaku

                    $cekCoupon = $checkCouponLic;

                    // jika mash berlaku cek juga single used / multiple use
                    if($cekCoupon->coupon->used == "single"){
                        // jika single used maka hanya bisa di pakai 1kali oleh user tersebut
                        // cek invoice yang sudah terbayar apakah ada pkode promo terpakai
                        $cekinvoice = AdminInvoice::where('group_id', $user->id_group)
                            ->whereIn('status', [InvoiceStatusEnum::PAID,InvoiceStatusEnum::UNPAID])
                            ->where('coupon_name',$cekCoupon->coupon->coupon_name)
                            ->first();
                        if($cekinvoice){
//                            if(date('Y-m') == date('Y-m', strtotime($cekinvoice->paid_date))){
//                                return array(
//                                    'success' => false,
//                                    'messages' => 'You have used this coupon',
//                                    'priceData' => null);
//                            }

                            return array(
                                'success' => false,
                                'messages' => 'You have used this coupon',
                                'priceData' => null);
                        }

                    }
                    if($cekCoupon->used == 'multiple'){
                        // jika multiple used maka bisa di pakai lebih dari 1 kali tapi hanya berlaku di bulan yang berbeda
                        // jika single used maka hanya bisa di pakai 1kali oleh user tersebut
                        // cek invoice yang sudah terbayar apakah ada pkode promo terpakai
                        $cekinvoice = AdminInvoice::where('group_id', $user->id_group)
                            ->whereIn('status', [InvoiceStatusEnum::PAID,InvoiceStatusEnum::UNPAID])
                            ->where('coupon_name',$cekCoupon->coupon->coupon_name)
                            ->first();
                        if($cekinvoice){
                            if(date('Y-m') == date('Y-m', strtotime($cekinvoice->invoice_date))){
                                return array(
                                    'success' => false,
                                    'messages' => 'You have used this coupon in this month',
                                    'priceData' => null);
                            }
                        }

                        $priceData->discountCoupon = $totalDiscount;
                        $priceDataNew = new PriceData(
                            $priceData->price,
                            $priceData->ppnPercentage,
                            $priceData->discountPercentage,
                            $priceData->adminFee,
                            $priceData->ppn,
                            $priceData->discount,
                            $priceData->total,
                            $priceData->discountCoupon
                        );
                        return array(
                            'success' => true,
                            'messages' => '',
                            'priceData' => $priceDataNew);

                    }

                    if($cekCoupon->coupon->type == 'percent'){
                        $totalDiscount = $priceData->price * ($cekCoupon->coupon->percent / 100);
                    }
                    if($cekCoupon->coupon->type == 'nominal'){
                        $totalDiscount = $cekCoupon->coupon->nominal;
                    }

                    $priceData->discountCoupon = $totalDiscount;
                    $priceDataNew = new PriceData(
                        $priceData->price,
                        $priceData->ppnPercentage,
                        $priceData->discountPercentage,
                        $priceData->adminFee,
                        $priceData->ppn,
                        $priceData->discount,
                        $priceData->total,
                        $priceData->discountCoupon
                    );
                    return array(
                        'success' => true,
                        'messages' => '',
                        'priceData' => $priceDataNew);
                }else{
                    return array(
                        'success' => true,
                        'priceData' => $priceData);
                }
                return array(
                    'success' => true,
                    'priceData' => $priceData);
            }
            if($cekCouponUser){
                // cek masa berlakunya
                if($cekCouponUser->coupon->start_date <= $now && $now <= $cekCouponUser->coupon->end_date){
                    // masih berlaku

                    $cekCoupon = $cekCouponUser;

                    // jika mash berlaku cek juga single used / multiple use
                    if($cekCoupon->coupon->used == "single"){
                        // jika single used maka hanya bisa di pakai 1kali oleh user tersebut
                        // cek invoice yang sudah terbayar apakah ada pkode promo terpakai
                        $cekinvoice = AdminInvoice::where('group_id', $user->id_group)
                            ->whereIn('status', [InvoiceStatusEnum::PAID,InvoiceStatusEnum::UNPAID])
                            ->where('coupon_name',$cekCoupon->coupon->coupon_name)
                            ->first();
                        if($cekinvoice){
                            if(date('Y-m') == date('Y-m', strtotime($cekinvoice->invoice_date))){

                                return array(
                                    'success' => false,
                                    'messages' => 'You have used this coupon',
                                    'priceData' => null);
                            }
                        }

                    }
                    if($cekCoupon->used == 'multiple'){
                        // jika multiple used maka bisa di pakai lebih dari 1 kali tapi hanya berlaku di bulan yang berbeda
                        // jika single used maka hanya bisa di pakai 1kali oleh user tersebut
                        // cek invoice yang sudah terbayar apakah ada pkode promo terpakai
                        $cekinvoice = AdminInvoice::where('group_id', $user->id_group)
                            ->whereIn('status', [InvoiceStatusEnum::PAID,InvoiceStatusEnum::UNPAID])
                            ->where('coupon_name',$cekCoupon->coupon->coupon_name)
                            ->first();
                        if($cekinvoice){
                            if(date('Y-m') == date('Y-m', strtotime($cekinvoice->invoice_date))){
                                return array(
                                    'success' => false,
                                    'messages' => 'You have used this coupon in this mont',
                                    'priceData' => null);
                            }
                        }

                        $priceData->discountCoupon = $totalDiscount;
                        $priceDataNew = new PriceData(
                            $priceData->price,
                            $priceData->ppnPercentage,
                            $priceData->discountPercentage,
                            $priceData->adminFee,
                            $priceData->ppn,
                            $priceData->discount,
                            $priceData->total,
                            $priceData->discountCoupon
                        );
                        return array(
                            'success' => true,
                            'messages' => '',
                            'priceData' => $priceDataNew);

                    }

                    if($cekCoupon->coupon->type == 'percent'){
                        $totalDiscount = $priceData->price * ($cekCoupon->coupon->percent / 100);
                    }
                    if($cekCoupon->coupon->type == 'nominal'){
                        $totalDiscount = $cekCoupon->coupon->nominal;
                    }

                    $priceData->discountCoupon = $totalDiscount;
                    $priceDataNew = new PriceData(
                        $priceData->price,
                        $priceData->ppnPercentage,
                        $priceData->discountPercentage,
                        $priceData->adminFee,
                        $priceData->ppn,
                        $priceData->discount,
                        $priceData->total,
                        $priceData->discountCoupon
                    );
                    return array(
                        'success' => true,
                        'messages' => '',
                        'priceData' => $priceDataNew);
                }else{
                    return array(
                        'success' => true,
                        'priceData' => $priceData);
                    }
                return array(
                    'success' => true,
                    'priceData' => $priceData);
            }
            return array(
                'success' => true,
                'priceData' => $priceData);
        }else{
            return array(
                'success' => true,
                'priceData' => $priceData);
            };
    }
}
