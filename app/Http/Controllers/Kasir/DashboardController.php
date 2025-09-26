<?php

namespace App\Http\Controllers\Kasir;

use App\Models\Member;
use App\Models\Invoice;
use App\Models\PppoeUser;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Enums\InvoiceStatusEnum;
use App\Enums\PppoeUserStatusEnum;
use App\Enums\TransactionTypeEnum;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalMembers = Member::where('group_id', $request->user()->id_group)->count();
        $totalSuspends = PppoeUser::where('group_id', $request->user()->id_group)->where('status', PppoeUserStatusEnum::SUSPENDED->value)->count();
        $totalUnpaidInvoices = Invoice::where('group_id', $request->user()->id_group)->where('status', InvoiceStatusEnum::UNPAID)->count();
        $totalPaidInvoices = Invoice::where('group_id', $request->user()->id_group)->where('status', InvoiceStatusEnum::PAID)->count();

        return view('dashboards.kasir', compact('totalMembers', 'totalSuspends', 'totalUnpaidInvoices', 'totalPaidInvoices'));
    }

    public function revenueChart(Request $request)
    {
        $year = date('Y');

        $data = collect(range(1, 12))->map(function ($month) use ($year, $request) {
            $income = Transaksi::where('group_id', $request->user()->id_group)
                ->where('type', TransactionTypeEnum::INCOME)
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->sum('price');

            $expense = Transaksi::where('group_id', $request->user()->id_group)
                ->where('type', TransactionTypeEnum::EXPENSE)
                ->whereYear('tanggal', $year)
                ->whereMonth('tanggal', $month)
                ->sum('price');

            return [
                'month' => Carbon::create()->month($month)->format('M'),
                'income' => $income,
                'expense' => -$expense
            ];
        });

        return response()->json([
            'months' => $data->pluck('month'),
            'income' => $data->pluck('income'),
            'expense' => $data->pluck('expense'),
            'totalIncome' => $data->sum('income'),
            'totalExpense' => abs($data->sum('expense'))
        ]);
    }
}
