<?php

namespace Modules\Payments\Services;

use App\Mail\EmailInvoiceNotif;
use App\Models\AdminDinetkanInvoice;
use App\Models\Balancehistory;
use App\Models\Coupon;
use App\Models\Coupon_license;
use App\Models\Coupon_user;
use App\Models\Keuangan\Transaksi;
use App\Models\Keuangan\TransaksiMitra;
use App\Models\LicenseDinetkan;
use App\Models\MappingUserLicense;
use App\Models\MasterMikrotik;
use App\Models\Partnership\Mitra;
use App\Models\ServiceDetail;
use App\Models\User;
use App\Models\License;
use App\Models\UserDinetkan;
use App\Settings\SiteDinetkanSettings;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use App\Enums\DinetkanInvoiceStatusEnum;
use App\Enums\TransactionTypeEnum;
use App\Enums\TransactionCategoryEnum;
use App\Enums\TransactionPaymentMethodEnum;
use App\Enums\ServiceStatusEnum;
use App\Settings\SiteSettings;
use Illuminate\Support\Facades\Mail;
//use Modules\Payments\Repositories\Contracts\TransaksiRepositoryInterface;
use Illuminate\Support\Str;
use Modules\Payments\Repositories\Contracts\AdminDinetkanInvoiceRepositoryInterface;
use Modules\Payments\ValueObjects\PriceData;
use Modules\Payments\ValueObjects\PriceDataDinetkan;
use RouterOS\Client;
use RouterOS\Query;

final class AdminDinetkanPaymentService
{
    public function __construct(
        private AdminDinetkanInvoiceRepositoryInterface $AdminDinetkanInvoiceRepo,
//        private TransaksiRepositoryInterface $transaksiRepo,
//        private TripayService $tripayService,
        private DuitkuService $duitkuService,
        private SiteDinetkanSettings $settings,
    ) {}

//public function setTripayService(TripayService $tripayService): void
//{
//    $this->tripayService = $tripayService;
//}

public function setDuitkuService(DuitkuService $duitkuService): void
{
    $this->duitkuService = $duitkuService;
}

/**
 * Calculate license order price details.
 */
public function calculateLicenseOrderPrice(LicenseDinetkan $license, UserDinetkan $user, ?AdminDinetkanInvoice $invoice = null): PriceDataDinetkan
{
    if ($invoice) {
        return new PriceDataDinetkan(
            $invoice->price,
            $invoice->ppn,
            0,
            $invoice?->fee ?: 0,
                $invoice?->ppn ?: 0,
                $invoice?->discount ?: 0,
                0,
                $invoice?->discount_coupon ?: 0,
                $invoice->price_otc,
                $invoice->ppn_otc,
        );
        }

    $settings = app(SiteDinetkanSettings::class);
    $currentLicense = $invoice ? LicenseDinetkan::find($invoice->itemable_id) : LicenseDinetkan::find($user->license_id);
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

    return new PriceDataDinetkan(
        $price,
        $license->ppn,
        0,
        $settings->admin_fee,

        $license?->ppn ?: 0,
            $license?->discount ?: 0,
            0,
            $license?->discount_coupon ?: 0,
            $license->price_otc,
            $license->ppn_otc,
        );
    }


//    public function calculateLicenseOrderPrice(LicenseDinetkan $license, UserDinetkan $user, ?AdminDinetkanInvoice $invoice = null): PriceData
//    {
//        if ($invoice) {
//            return new PriceData(
//                $invoice->price,
//                $invoice->ppn,
//                0,
//                $invoice?->fee ?: 0,
//                    $invoice?->ppn ?: 0,
//                    $invoice?->discount ?: 0,
//                    0,
//                    $invoice?->discount_coupon ?: 0
//            );
//            }
//
//        $settings = app(SiteDinetkanSettings::class);
//        $currentLicense = $invoice ? LicenseDinetkan::find($invoice->itemable_id) : LicenseDinetkan::find($user->license_id);
//        $currentPrice = $currentLicense ? $currentLicense->price : 0;
//        $isProrate = $currentLicense && $currentLicense->id !== $license->id;
//
//        if ($isProrate && $currentPrice > 0) {
//            $priceDifference = $license->price - $currentPrice;
//
//            $nextDue = $user->next_due ? Carbon::parse($user->next_due) : Carbon::now()->addMonthNoOverflow();
//            $remainingDays = $invoice ? Carbon::parse($invoice->invoice_date)->diffInDays(Carbon::parse($invoice->due_date)) : Carbon::now()->diffInDays($nextDue);
//            $remainingDays = max($remainingDays, 0);
//
//            $price = ($remainingDays / 30) * $priceDifference;
//            $price = (int) round($price);
//        } else {
//            $price = $license->price;
//        }
//
//        return new PriceData(
//            $price,
//            $license->ppn,
//            0,
//            $settings->admin_fee
//        );
//    }

/**
 * Create or retrieve an unpaid AdminDinetkanInvoice for a license purchase or renewal.
 */
public function createLicenseInvoice(LicenseDinetkan $license, UserDinetkan $user,$couponCode = "", $is_otc = 0): AdminDinetkanInvoice
{
    // Check if existing invoice (for same license) is pending payment
    $existingInvoice = AdminDinetkanInvoice::where('group_id', $user->id_group)
        ->where('status', DinetkanInvoiceStatusEnum::UNPAID)
        ->where('itemable_id', $license->id)
        ->where('itemable_type', LicenseDinetkan::class)
        ->first();

    if ($existingInvoice) {
        return $existingInvoice;
    }

    $isRenewal = ($user->license_id === $license->id);
    $license = LicenseDinetkan::where('id', $license->id)->first();
    // If user has a next_due, assume we start from there; else start new period
    // If user has a next_due, assume we start from there; else start new period
    $dueDate = $user->dinetkan_next_due ? Carbon::parse($user->dinetkan_next_due ) : Carbon::now();
    $periodStart = $dueDate->format('Y-m-d');
    $periodEnd = Carbon::parse($dueDate)->addMonthNoOverflow()->format('Y-m-d');
    $subscribe = Carbon::parse($periodStart)->format('d/m/Y') . ' s/d ' . Carbon::parse($periodEnd)->format('d/m/Y');

//        $dueDate = $user->next_due ? Carbon::parse($user->next_due) : Carbon::now();
//        $periodStart = Carbon::parse($dueDate)->subMonthNoOverflow()->format('Y-m-d');
//        $periodEnd = Carbon::parse($dueDate)->addMonth()->format('Y-m-d');
////        $periodEnd = $dueDate->format('Y-m-d');
//        $subscribe = Carbon::parse($periodStart)->format('d/m/Y') . ' s/d ' . Carbon::parse($periodEnd)->format('d/m/Y');

    // Price calculation
    $priceData = $this->calculateLicenseOrderPrice($license, $user);
//        $priceDatanew = $this->get_promo_coupon($user,$license,$priceData, $couponCode);
//        $priceData = $priceDatanew['priceData'];
//        print_r($priceData);exit;
    $noInvoice = date('m') . rand(0000000, 9999999);

    $invoice = new AdminDinetkanInvoice([
        'group_id'      => $user->id_group,
        'itemable_id'   => $license->id,
        'itemable_type' => LicenseDinetkan::class,
        'no_invoice'    => $noInvoice,
        'item'          => "Service : ". $license->name,//($isRenewal ? 'License Renewal: ' : '') . $license->name,
        'price'         => $priceData->price,
        'ppn'           => $priceData->ppnPercentage,
        'price_otc'     => $is_otc == 1 ? $license->price_otc : 0,
        'ppn_otc'       => $is_otc == 1 ? $license->ppn_otc : 0,
        'fee'           => $priceData->adminFee,
        'discount'      => 0,
        'discount_coupon' => $priceData->discountCoupon,
        'invoice_date'  => Carbon::now(),
        'due_date'      => null, //$dueDate->format('Y-m-d'),
        'period'        => $periodEnd,
        'subscribe'     => $subscribe,
        'payment_type'  => 'Prabayar',
        'billing_period' => 'Fixed Date',
        'payment_url'   => route('admin.invoice_dinetkan', $noInvoice),
        'status'        => DinetkanInvoiceStatusEnum::NEW,
        'coupon_name'   => $couponCode,
        'dinetkan_user_id' => $user->dinetkan_user_id,
        'is_otc' => $is_otc
    ]);

    $this->AdminDinetkanInvoiceRepo->save($invoice);
    return $invoice;
}

public function createLicenseInvoiceMrc(LicenseDinetkan $license, UserDinetkan $user,$couponCode = ""): AdminDinetkanInvoice
{
    // Check if existing invoice (for same license) is pending payment
    $existingInvoice = AdminDinetkanInvoice::where('group_id', $user->id_group)
        ->where('status', DinetkanInvoiceStatusEnum::UNPAID)
        ->where('itemable_id', $license->id)
        ->where('itemable_type', LicenseDinetkan::class)
        ->first();

    if ($existingInvoice) {
        return $existingInvoice;
    }

    $isRenewal = ($user->mrc_license_dinetkan_id === $license->id);
    $license = LicenseDinetkan::where('id', $license->id)->first();
    // If user has a next_due, assume we start from there; else start new period
    $dueDate = $user->dinetkan_next_due ? Carbon::parse($user->dinetkan_next_due) : Carbon::now();
    $periodStart = $dueDate->format('Y-m-d');
    $periodEnd = Carbon::parse($dueDate)->addMonthNoOverflow()->format('Y-m-d');
    $subscribe = Carbon::parse($periodStart)->format('d/m/Y') . ' s/d ' . Carbon::parse($periodEnd)->format('d/m/Y');

    // Price calculation
    $priceData = $this->calculateLicenseOrderPrice($license, $user);
    //        $priceDatanew = $this->get_promo_coupon($user,$license,$priceData, $couponCode);
    //        $priceData = $priceDatanew['priceData'];
    //        print_r($priceData);exit;
    $noInvoice = date('m') . rand(0000000, 9999999);

    $invoice = new AdminDinetkanInvoice([
        'group_id'      => $user->id_group,
        'itemable_id'   => $license->id,
        'itemable_type' => LicenseDinetkan::class,
        'no_invoice'    => $noInvoice,
        'item'          => $license->name,
        'price'         => $priceData->price,
        'ppn'           => $priceData->ppnPercentage,
        'price_otc'     => 0,
        'ppn_otc'       => 0,
        'fee'           => $priceData->adminFee,
        'discount'      => 0,
        'discount_coupon' => $priceData->discountCoupon,
        'invoice_date'  => Carbon::now(),
        'due_date'      => $dueDate->format('Y-m-d'),
        'period'        => $periodEnd,
        'subscribe'     => $subscribe,
        'payment_type'  => 'Prabayar',
        'billing_period' => 'Fixed Date',
        'payment_url'   => route('admin.invoice_dinetkan', $noInvoice),
        'status'        => DinetkanInvoiceStatusEnum::UNPAID,
        'coupon_name'   => $couponCode,
        'dinetkan_user_id' => $user->dinetkan_user_id
    ]);

    $this->AdminDinetkanInvoiceRepo->save($invoice);

    // create mapping service
    MappingUserLicense::create(
        [
            'dinetkan_user_id' => $user->dinetkan_user_id,
            'license_id' => $license->id,
            'status' => ServiceStatusEnum::NEW,
            'no_invoice' => $noInvoice,
            'due_date' => $dueDate->format('Y-m-d'),
        ]
    );
    return $invoice;
}

/**
 * Create a Tripay transaction for admin invoice.
 */
public function createTripayTransaction(AdminDinetkanInvoice $invoice, string $paymentMethod): array
{
//    $user = $invoice->admin;
//
//    if (!$user) {
//        return null;
//    }
//
//    $ppn = ($invoice->price * $invoice->ppn) / 100;
//    $adminFee = $invoice->price > 0 ? $invoice->admin_fee : 0;
//    $total = $invoice->price + $ppn + $adminFee;
//
////    $signature = $this->tripayService->createSignature($invoice->no_invoice, $total);
//
//    $items = [
//        [
//            'sku'      => 'sku',
//            'name'     => $invoice->item,
//            'price'    => $invoice->price,
//            'quantity' => 1,
//        ],
//    ];
//
//    if ($ppn > 0) {
//        $items[] = [
//            'sku'      => 'ppn',
//            'name'     => 'PPN',
//            'price'    => $ppn,
//            'quantity' => 1,
//        ];
//    }
//
//    if ($adminFee > 0) {
//        $items[] = [
//            'sku'      => 'admin_fee',
//            'name'     => 'Biaya Admin',
//            'price'    => $adminFee,
//            'quantity' => 1,
//        ];
//    }
//
//    $data = [
//        'method'         => $paymentMethod,
//        'merchant_ref'   => $invoice->no_invoice,
//        'amount'         => $total,
//        'customer_name'  => $user->name ?? 'Admin',
//        'customer_email' => $user->email ?? 'no-reply@example.com',
//        'customer_phone' => $user->whatsapp ?? '0000000000',
//        'order_items'    => $items,
//        'callback_url' => route('notification.admin.tripay'),
//        'return_url'   => route('admin.invoice', $invoice->no_invoice),
//        'expired_time' => time() + (24 * 60 * 60),
//        'signature'    => $signature,
//    ];
//
//    $response = $this->tripayService->createTransaction($invoice, $data);
//
//    return $response;
}

public function createDuitkuTransaction(AdminDinetkanInvoice $invoice)
{
    $response = $this->duitkuService->createTransactionDinetkan($invoice, $this->settings);
    return $response['paymentUrl'];
}

/**
 * Handle Tripay notification for admin invoice payments.
 */
public function handleTripayNotification(array $requestData, ?string $callbackSignature): ?AdminDinetkanInvoice
{
//    $invoice = $this->tripayService->handleAdminNotification($requestData);
//
//    if ($invoice && $invoice->status === DinetkanInvoiceStatusEnum::UNPAID) {
//        $this->markInvoiceAsPaid($invoice);
//        return $invoice;
//    }
//
//    return null;
}



protected function enable_vlan($invoice)
{
    $mapping = MappingUserLicense::where('id', $invoice->id_mapping)->first();
    if($mapping){
        $service = ServiceDetail::where('service_id', $mapping->service_id)->first();
        if($service){
            $mikrotik = MasterMikrotik::where('id', $service->id_mikrotik)->first();
            if($mikrotik){
                $client = new Client([
                    'host' => $mikrotik->ip,
                    'user' => $mikrotik->username,
                    'pass' => $mikrotik->password,
                    'port' => $mikrotik->port, // port API Mikrotik kamu
                    'timeout' => 10,
                ]);
                $query = new Query('/interface/vlan/set');
                $query->equal('.id', $service->vlan_id);  // Ganti *F dengan ID VLAN yang ingin diubah
                $query->equal('disabled', 'no');  // 'no' untuk enable
                $hasil = $client->query($query)->read();
            }
        }
    }
}

/**
 * Handle Duitku notification for admin invoice payments.
 */
public function handleDuitkuNotification(array $requestData): ?AdminDinetkanInvoice
{

    $invoice = $this->duitkuService->handleAdminNotificationDinetkan($requestData);
    if ($invoice && $invoice->status === DinetkanInvoiceStatusEnum::UNPAID) {
        $this->markInvoiceAsPaid($invoice);
        $maxdate = Carbon::now()->addDays(5);
        $maxdateformat = Carbon::parse($maxdate)->format('d-m-Y');
        $totalppn = 0;
        if($invoice->ppn > 0){
            $totalppn = $invoice->price * $invoice->ppn / 100;
        }
        $totalotc = 0;
        if($invoice->price_otc > 0){
            $totalotc = $invoice->price_otc;
        }
        if($invoice->ppn_otc > 0){
            $totalppnotc = $invoice->price_otc * $invoice->ppn_otc / 100;
            $totalotc = $invoice->price_otc + $totalppnotc;
        }
        $total = $invoice->price + $totalppn + $totalotc;
        $total_format = number_format($total, 0, '.', '.');
        $user = UserDinetkan::where('dinetkan_user_id', $invoice->dinetkan_user_id)->first();
        $details = [
            'email' => $user->email,
            'subject' => 'Invoice Lunas',
            'fullname' => $user->first_name.' '.$user->last_name,
            'no_invoice' => $invoice->no_invoice,
            'invoice_date' => $invoice->invoice_date,
            'due_date' => $invoice->due_date,
            'total' => $total_format,
            'max_date' => $maxdateformat,
            'url' => route('admin.invoice_dinetkan', $invoice->no_invoice),
            'item' => $invoice->item,
            'view' => 'notif_invoice',
            'status' => $invoice->status->value
        ];
        $this->enable_vlan($invoice);
        $mapping = MappingUserLicense::query()->where('id', $invoice->id_mapping)->first();
        if($mapping){
            $license = LicenseDinetkan::query()->where('id', $mapping->license_id)->first();$mapping->update([
                'status' => ServiceStatusEnum::ACTIVE,
            ]);
            if($invoice->is_upgrade == 0){
                $mapping->update([
                    'due_date' => Carbon::parse($mapping->due_date)->addMonthsWithNoOverflow(1)
                ]);
            }

            $transaksix = Transaksi::create([
                'shortname' => 'dinetkan',
                'id_data' => $license->id,
                'tipe' => 'Pemasukan',
                'kategori' => 'Invoice',
                'deskripsi' => "Pembayaran Tagihan Pemakaian Bandwith ".Str::upper($user->shortname )." #$invoice->no_invoice ",
                'nominal' => $total,
                'tanggal' => Carbon::now(),
                'metode' => $invoice->bank_name,
                'created_by' => 'duitku',
            ]);

            if ($mapping->id_mitra && $license->komisi_mitra > 0) {
                $nama_mitra = Mitra::where('id_mitra', $mapping->id_mitra)->first();
                $transaksi = TransaksiMitra::create([
                    'shortname' => "dinetkan",
                    'id_data' => $invoice->id,
                    'tanggal' => Carbon::now(),
                    'tipe' => 'Pemasukan',
                    'kategori' => 'Komisi',
                    'deskripsi' => "Komisi $nama_mitra->name #$invoice->no_invoice a.n $user->first_name $user->last_name",
                    'mitra_id' => $nama_mitra->id,
                    'nominal' => $license->komisi_mitra,
                    'metode' => $invoice->bank_name,
                    'created_by' => "duitku",
                    'is_dinetkan' => 1
                ]);

                $balancehistory = Balancehistory::create([
                    'id_mitra' => $transaksi->mitra_id,
                    'id_reseller' => '',
                    'tx_amount' => $transaksi->nominal,
                    'notes' => $transaksi->deskripsi,
                    'type' => 'in',
                    'tx_date' => Carbon::now(),
                    'id_transaksi' => $transaksi->id
                ]);

//                $updatemitra = Mitra::where('shortname', multi_auth()->shortname)->where('id', $invoice->mitra_id)->first();
                $updatemitra = Mitra::where('id_mitra', $mapping->id_mitra)->first();
                if($updatemitra){
                    $lastbalance = $updatemitra->balance;
                    $updatemitra->update([
                        'balance' => $lastbalance + (int)$transaksi->nominal
                    ]);
                }
            }
        }

        $settings = $this->settings;
        $priceData = $this->calculateLicenseOrderPrice($invoice->itemable, $invoice->admin, $invoice);
        $pdf = Pdf::loadView('accounts.licensing_dinetkan.invoice_pdf',
            compact(
                'invoice',
                'priceData',
                'settings'
            ))->setPaper('a4', 'potrait');

        // Simpan ke storage sementara
        $pdfPath = storage_path("app/invoice/invoice_{$invoice->no_invoice}.pdf");
        $pdf->save($pdfPath);


        Mail::to($details['email'])->send(new EmailInvoiceNotif($details, $pdfPath));
        return $invoice;
    }

    return null;
}

/**
 * Mark invoice as paid and record the transaction.
 */
public function markInvoiceAsPaid(AdminDinetkanInvoice $invoice): void
{
    // Update the invoice status to paid
    $invoice->update([
//        'paid_date' => Carbon::today()->toDateString(),
        'paid_date' => Carbon::now()->toDateTimeString(),
    'status'    => DinetkanInvoiceStatusEnum::PAID,
    ]);

    // Retrieve the admin user and the associated license
    $adminUser = UserDinetkan::where('dinetkan_user_id', $invoice->dinetkan_user_id)
        ->where('role', 'Admin')
        ->first();
    $license = LicenseDinetkan::find($invoice->itemable_id);

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
//            'status'     => 1,  // Ensure the user is active
        // 'license_dinetkan_id' => $license->id,
        'mrc_license_dinetkan_id' => $license->id,
        'dinetkan_next_due'   => $next_due,
    ]);

    // Update all non-admin users in the group
//    $users = User::where('id_group', $adminUser->id_group)
//        ->where('role', '!=', 'Admin')
//        ->get();

//        foreach ($users as $user) {
//            $user->update([
//                'status'     => 1,
//                'license_id' => $license->id,
//            ]);
//        }

    // Record the transaction
//    $this->transaksiRepo->create([
//        'group_id'       => $invoice->group_id,
//        'invoice_id'     => $invoice->id,
//        'invoice_type'   => AdminDinetkanInvoice::class,
//        'type'           => TransactionTypeEnum::EXPENSE->value,
//            'category'       => TransactionCategoryEnum::LICENSE->value,
//            'item'           => 'License ' . $license->name,
//            'deskripsi'      => "Payment for " . strtolower($invoice->item),
//            'price'          => $invoice->price,
//            'tanggal'        => $invoice->paid_date,
//            'payment_method' => TransactionPaymentMethodEnum::TRANSFER->value,
//            'admin'          => 'system',
//        ]);

    $unpaidInvoices = AdminDinetkanInvoice::where('dinetkan_user_id', $adminUser->dinetkan_user_id)
        ->where('status', DinetkanInvoiceStatusEnum::UNPAID)
        ->where('itemable_id', $license->id)
        ->where('itemable_type', LicenseDinetkan::class)
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
        $checkCouponLic = Coupon_LicenseDinetkan::with('coupon')
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
                    $cekinvoice = AdminDinetkanInvoice::where('group_id', $user->id_group)
                        ->whereIn('status', [DinetkanInvoiceStatusEnum::PAID,DinetkanInvoiceStatusEnum::UNPAID])
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
                    $cekinvoice = AdminDinetkanInvoice::where('group_id', $user->id_group)
                        ->whereIn('status', [DinetkanInvoiceStatusEnum::PAID,DinetkanInvoiceStatusEnum::UNPAID])
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
                    $cekinvoice = AdminDinetkanInvoice::where('group_id', $user->id_group)
                        ->whereIn('status', [DinetkanInvoiceStatusEnum::PAID,DinetkanInvoiceStatusEnum::UNPAID])
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
                    $cekinvoice = AdminDinetkanInvoice::where('group_id', $user->id_group)
                        ->whereIn('status', [DinetkanInvoiceStatusEnum::PAID,DinetkanInvoiceStatusEnum::UNPAID])
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
