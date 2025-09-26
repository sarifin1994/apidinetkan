<?php

namespace Modules\Data\Services;

use App\Models\Invoice;
use App\Models\PppoeUser;
use App\Models\RadiusNas;
use App\Models\Transaksi;
use App\Models\PppoeMember;
use Illuminate\Http\Request;
use App\Models\BillingSetting;
use Illuminate\Support\Carbon;
use App\Enums\TransactionTypeEnum;
use App\Enums\TransactionCategoryEnum;
use Illuminate\Support\Facades\Process;
use Spatie\Activitylog\Models\Activity;

final class PppoeService
{
    public function disconnectUser(PppoeUser $pppoe, int $group_id): void
    {
        $secret = RadiusNas::where('group_id', $group_id)
            ->where('nasname', $pppoe->nas)
            ->value('secret');

        if ($pppoe->nas && $secret) {
            Process::path('/usr/bin/')->run("echo User-Name='{$pppoe->username}' | radclient -r 1 {$pppoe->nas}:3799 disconnect $secret");
        } else {
            // Disconnect from all NAS
            $nasList = RadiusNas::where('group_id', $group_id)->get();
            foreach ($nasList as $nas) {
                Process::path('/usr/bin/')->run("echo User-Name='{$pppoe->username}' | radclient -r 1 {$nas->nasname}:3799 disconnect {$nas->secret}");
            }
        }
    }

    public function generateServiceId(PppoeUser $user, PppoeMember $service)
    {
        // 2 digit tahun + 2 digit id group + 5 digit id pelanggan + 2 digit urutan layanan by pelanggan
        $year = Carbon::now()->format('y');
        $existingServiceId = PppoeMember::where('id_service', 'like', "%{$year}%")
            ->where('member_id', $service->member->id)
            ->count();
        $serviceId = $year .
            str_pad($user->group_id, 2, '0', STR_PAD_LEFT) .
            $service->member->id_member .
            str_pad($existingServiceId + 1, 2, '0', STR_PAD_LEFT);

        return $serviceId;
    }

    public function handleMemberUserCreation(Request $request, PppoeUser $user)
    {
        $today = Carbon::now()->format('Y-m-d');
        $twoMonthsBack = Carbon::createFromFormat('Y-m-d', $today)->subMonthsNoOverflow(2)->toDateString();
        $twoMonthsBackBC = Carbon::createFromFormat('Y-m-d', $today)->startOfMonth()->subMonthsNoOverflow(2)->toDateString();
        $tgl = date('d', strtotime($request->reg_date));
        $isNew = $request->member_id !== $user->member?->member_id;

        // Early checks
        if (!$isNew) {
            return response()->json(['error' => 'Failed to create user, member already exists!']);
        }

        if ($request->reg_date < $twoMonthsBack && $request->payment_type !== 'Pascabayar') {
            return response()->json(['error' => 'Failed to create user, please change active date!']);
        }

        if ($request->payment_type === 'Pascabayar' && $request->billing_period === 'Billing Cycle' && $request->reg_date < $twoMonthsBackBC) {
            return response()->json(['success' => 'Failed to create user, please change active date!']);
        }

        if (!$request->has('payment_status')) {
            $request->request->add(['payment_status' => 'unpaid']);
        }

        $existingMember = PppoeMember::where('pppoe_id', $user->id)->first();

        // Create user scenarios:
        $member = null;
        if ($request->payment_type === 'Prabayar' && $request->payment_status === 'paid' && $request->reg_date >= $twoMonthsBack) {
            $member = $this->handlePrabayarPaid($user, $request, $tgl);
        } elseif ($request->payment_type === 'Prabayar' && $request->payment_status === 'unpaid' && $request->reg_date > $twoMonthsBack) {
            $member = $this->handlePrabayarUnpaid($user, $request, $tgl);
        } elseif ($request->payment_type === 'Pascabayar' && $request->billing_period === 'Fixed Date' && $request->reg_date > $twoMonthsBack) {
            $member = $this->handlePascabayarFixedDate($user, $request, $tgl);
        } elseif ($request->payment_type === 'Pascabayar' && $request->billing_period === 'Billing Cycle' && $request->reg_date < $twoMonthsBackBC) {
            $member = $this->handlePascabayarBillingCycleProrate($user, $request, $tgl);
        } elseif ($request->payment_type === 'Pascabayar' && $request->billing_period === 'Billing Cycle' && $request->reg_date > $twoMonthsBackBC) {
            $member = $this->handlePascabayarBillingCycleStandard($user, $request, $tgl);
        }

        if ($existingMember) {
            $existingMember->delete();
        }

        if (!$member) {
            return response()->json(['error' => 'Invalid conditions for user creation.']);
        }

        $serviceId = $this->generateServiceId($user, $member);
        $member->update(['id_service' => $serviceId]);

        return $member;
    }

    public function handlePrabayarPaid(PppoeUser $user, Request $request, $tgl): PppoeMember
    {
        $next_due = Carbon::createFromFormat('Y-m-d', $request->reg_date)->addMonthsNoOverflow(1);
        $next_invoice = $next_due;
        $price = (int)str_replace('.', '', $request->amount);
        $total = (int)str_replace('.', '', $request->payment_total);
        $period = $next_due;
        $awal = date('d/m/Y', strtotime($request->reg_date));
        $akhir = date('d/m/Y', strtotime($period));

        $member = PppoeMember::create([
            'pppoe_id' => $user->id,
            'member_id' => $request->member_id,
            'profile_id' => $request->profile_id,
            'kode_area' => $request->kode_area,
            'payment_type' => $request->payment_type,
            'payment_status' => $request->payment_status,
            'billing_period' => $request->billing_period,
            'ppn' => $request->ppn,
            'discount' => $request->discount,
            'reg_date' => $request->reg_date,
            'next_due' => $next_due,
            'next_invoice' => $next_invoice,
            'tgl' => $tgl,
        ]);

        $no_invoice = date('m') . rand(0000000, 9999999);
        $invoice = Invoice::create([
            'group_id' => $request->user()->id_group,
            'pppoe_id' => $user->id,
            'member_id' => $member->member_id,
            'pppoe_member_id' => $member->id,
            'no_invoice' => $no_invoice,
            'item' => "Internet: {$request->username} | {$request->profile}",
            'price' => $price,
            'ppn' => $request->ppn,
            'discount' => $request->discount,
            'invoice_date' => $request->reg_date,
            'due_date' => $request->reg_date,
            'period' => $period,
            'subscribe' => "$awal s/d $akhir",
            'reg_date' => $request->reg_date,
            'next_due' => $next_due,
            'payment_type' => $request->payment_type,
            'billing_period' => $request->billing_period,
            'payment_url' => route('invoice.pay', $no_invoice),
            'paid_date' => $request->reg_date,
            'status' => 1,
        ]);

        $transaksi = Transaksi::create([
            'group_id' => $request->user()->id_group,
            'invoice_id' => $invoice->id,
            'invoice_type' => Invoice::class,
            'type' => TransactionTypeEnum::INCOME,
            'category' => TransactionCategoryEnum::INVOICE,
            'item' => 'Invoice',
            'deskripsi' => "Payment #{$invoice->no_invoice} a.n {$request->full_name}",
            'price' => $total,
            'tanggal' => Carbon::now(),
            'payment_method' => 1,
            'admin' => $request->user()->username,
        ]);

        $this->logActivity($request, 'Create', 'Create New Transaction Pemasukan: ' . $transaksi->deskripsi);

        return $member;
    }

    public function handlePrabayarUnpaid(PppoeUser $user, Request $request, $tgl): PppoeMember
    {
        $next_due = $request->reg_date;
        $next_invoice = Carbon::createFromFormat('Y-m-d', $request->reg_date)->addMonthsNoOverflow(1);
        $price = (int)str_replace('.', '', $request->amount);
        $period = $next_invoice;
        $awal = date('d/m/Y', strtotime($request->reg_date));
        $akhir = date('d/m/Y', strtotime($period));

        $member = PppoeMember::create([
            'pppoe_id' => $user->id,
            'member_id' => $request->member_id,
            'profile_id' => $request->profile_id,
            'kode_area' => $request->kode_area,
            'payment_type' => $request->payment_type,
            'payment_status' => $request->payment_status,
            'billing_period' => $request->billing_period,
            'ppn' => $request->ppn,
            'discount' => $request->discount,
            'reg_date' => $request->reg_date,
            'next_due' => $next_due,
            'next_invoice' => $next_invoice,
            'tgl' => $tgl,
        ]);

        $no_invoice = date('m') . rand(0000000, 9999999);
        Invoice::create([
            'group_id' => $request->user()->id_group,
            'pppoe_id' => $user->id,
            'member_id' => $member->member_id,
            'pppoe_member_id' => $member->id,
            'no_invoice' => $no_invoice,
            'item' => "Internet: {$request->username} | {$request->profile}",
            'price' => $price,
            'ppn' => $request->ppn,
            'discount' => $request->discount,
            'invoice_date' => $request->reg_date,
            'due_date' => $request->reg_date,
            'period' => $period,
            'subscribe' => "$awal s/d $akhir",
            'reg_date' => $request->reg_date,
            'next_due' => $next_due,
            'payment_type' => $request->payment_type,
            'billing_period' => $request->billing_period,
            'payment_url' => route('invoice.pay', $no_invoice),
            'paid_date' => $request->reg_date,
            'status' => 0,
        ]);

        return $member;
    }

    public function handlePascabayarFixedDate(PppoeUser $user, Request $request, $tgl): PppoeMember
    {
        $next_due = Carbon::createFromFormat('Y-m-d', $request->reg_date)->addMonthsNoOverflow(1);
        $next_invoice = $next_due;

        return PppoeMember::create([
            'pppoe_id' => $user->id,
            'member_id' => $request->member_id,
            'profile_id' => $request->profile_id,
            'kode_area' => $request->kode_area,
            'payment_type' => $request->payment_type,
            'payment_status' => $request->payment_status,
            'billing_period' => $request->billing_period,
            'ppn' => $request->ppn,
            'discount' => $request->discount,
            'reg_date' => $request->reg_date,
            'next_due' => $next_due,
            'next_invoice' => $next_invoice,
            'tgl' => $tgl,
        ]);
    }

    public function handlePascabayarBillingCycleProrate(PppoeUser $user, Request $request, $tgl): PppoeMember
    {
        $due_bc = BillingSetting::where('group_id', $request->user()->id_group)->value('due_bc');
        $next_due = Carbon::createFromFormat('Y-m-d', $request->reg_date)->setDay($due_bc)->addMonths(1);
        $next_invoice = Carbon::createFromFormat('Y-m-d', $request->reg_date)->startOfMonth()->addMonthsNoOverflow(1);

        $price = (int)str_replace('.', '', $request->amount);
        $monthDays = Carbon::parse($request->reg_date)->daysInMonth;
        $endOfMonth = Carbon::parse($request->reg_date)->endOfMonth()->toDateString();
        $usageDays = Carbon::parse($request->reg_date)->diffInDays($endOfMonth);
        $dailyPrice = (int)floor($price / $monthDays);
        $amount = $usageDays * $dailyPrice;

        return PppoeMember::create([
            'pppoe_id' => $user->id,
            'member_id' => $request->member_id,
            'profile_id' => $request->profile_id,
            'kode_area' => $request->kode_area,
            'payment_type' => $request->payment_type,
            'payment_status' => $request->payment_status,
            'billing_period' => $request->billing_period,
            'ppn' => $request->ppn,
            'discount' => $request->discount,
            'reg_date' => $request->reg_date,
            'next_due' => $next_due,
            'next_invoice' => $next_invoice,
            'tgl' => $tgl,
        ]);
    }

    public function handlePascabayarBillingCycleStandard(PppoeUser $user, Request $request, $tgl): PppoeMember
    {
        $due_bc = BillingSetting::where('group_id', $request->user()->id_group)->value('due_bc');
        $next_due = Carbon::createFromFormat('Y-m-d', $request->reg_date)->setDay($due_bc)->addMonths(1);
        $next_invoice = Carbon::createFromFormat('Y-m-d', $request->reg_date)->startOfMonth()->addMonths(1);

        return PppoeMember::create([
            'pppoe_id' => $user->id,
            'member_id' => $request->member_id,
            'profile_id' => $request->profile_id,
            'kode_area' => $request->kode_area,
            'payment_type' => $request->payment_type,
            'payment_status' => $request->payment_status,
            'billing_period' => $request->billing_period,
            'ppn' => $request->ppn,
            'discount' => $request->discount,
            'reg_date' => $request->reg_date,
            'next_due' => $next_due,
            'next_invoice' => $next_invoice,
            'tgl' => $tgl,
        ]);
    }

    private function logActivity(Request $request, $event, $message)
    {
        activity()
            ->tap(function (Activity $activity) use ($request) {
                $activity->group_id = $request->user()->id_group;
            })
            ->event($event)
            ->log($message);
    }
}
