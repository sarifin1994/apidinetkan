<?php

namespace App\Http\Controllers\Report;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Enums\TransactionTypeEnum;
use App\Http\Controllers\Controller;
use App\Enums\TransactionCategoryEnum;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        //1 invoice, 2 hotspot, 3 manual transaksi
        $totalIncome = Transaksi::where('group_id', $request->user()->id_group)->where('type', TransactionTypeEnum::INCOME)->sum('price');
        $totalExpense = Transaksi::where('group_id', $request->user()->id_group)->where('type', TransactionTypeEnum::EXPENSE)->sum('price');
        $totalBalance = $totalIncome - $totalExpense;
        $incomeMonth = Transaksi::where('group_id', $request->user()->id_group)->where('type', TransactionTypeEnum::INCOME)
            ->whereYear('tanggal', Carbon::today()->year)
            ->whereMonth('tanggal', Carbon::today()->month)
            ->sum('price');
        $expenseMonth = Transaksi::where('group_id', $request->user()->id_group)->where('type', TransactionTypeEnum::EXPENSE)
            ->whereYear('tanggal', Carbon::today()->year)
            ->whereMonth('tanggal', Carbon::today()->month)
            ->sum('price');
        $balanceMonth = $incomeMonth - $expenseMonth;

        $totalTransactions = Transaksi::where('group_id', $request->user()->id_group)->count();

        if (request()->ajax()) {
            $start_date = request()->get('start_date');
            $end_date = request()->get('end_date');
            if (!empty($start_date) && !empty($end_date)) {
                $transactions = Transaksi::query()->where('group_id', $request->user()->id_group)->whereBetween('tanggal', [Carbon::parse($start_date)->format('Y-m-d 00:00:00'), Carbon::parse($end_date)->format('Y-m-d 23:59:59')]);
            } else {
                $transactions = Transaksi::query()->where('group_id', $request->user()->id_group);
            }

            return DataTables::of($transactions)
                ->addIndexColumn()
                ->editColumn('category', function ($row) {
                    return $row->category->label();
                })
                ->toJson();
        }

        $revenueCategories = [
            TransactionCategoryEnum::INVOICE,
            TransactionCategoryEnum::HOTSPOT,
            TransactionCategoryEnum::NEW_CLIENT,
            TransactionCategoryEnum::OTHER,
        ];

        $expenseCategories = [
            TransactionCategoryEnum::PARTNER_FEE,
            TransactionCategoryEnum::OPERATIONAL,
            TransactionCategoryEnum::BELANJA,
            TransactionCategoryEnum::OTHER
        ];

        $total = Transaksi::where('group_id', $request->user()->id_group)->count();

        return view('reports.transaction.index', compact(
            'totalIncome',
            'total',
            'totalExpense',
            'totalBalance',
            'balanceMonth',
            'incomeMonth',
            'expenseMonth',
            'totalTransactions',
            'revenueCategories',
            'expenseCategories'
        ));
    }

    public function show(Transaksi $transaction)
    {
        //return response
        return response()->json([
            'success' => true,
            'message' => 'Detail Data',
            'data' => $transaction,
        ]);
    }

    public function update(Request $request, Transaksi $transaction)
    {
        $price = str_replace('.', '', $request->price);
        $transaction->update([
            'tanggal' => $request->tanggal,
            'type' => $request->type,
            'category' => $request->category,
            'deskripsi' => $request->deskripsi,
            'category' => $request->category,
            'item' => TransactionCategoryEnum::fromValue($request->item)->label(),
            'price' => $price,
            'payment_method' => $request->payment_method,
        ]);
        activity()
            ->tap(function (Activity $activity) use ($request) {
                $activity->group_id = $request->user()->id_group;
            })
            ->event('Update')
            ->log('Update Transaction: ' . $request->deskripsi . '');
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $transaction,
        ]);
    }

    // public function store(Request $request)
    // {
    //     $price = str_replace('.', '', $request->price);
    //     $transaction = Transaksi::create([
    //         'tanggal' => $request->tanggal,
    //         'id_data' => 0,
    //         'admin' => $request->user()->username,
    //         'type' => $request->type,
    //         'item' => $request->item,
    //         'deskripsi' => $request->deskripsi,
    //         'price' => $price,
    //         'payment_method' => $request->payment_method,
    //     ]);
    //     if ($request->type === '1' || $request->type === '2' || $request->type === '3') {
    //         activity()
    // ->tap(function (Activity $activity) {
    //     $activity->group_id = $request->user()->id_group;
    // })
    //             ->event('Create')
    //             ->log('Create Transaction Pemasukan: ' . $request->deskripsi . '');
    //     } else {
    //         activity()
    // ->tap(function (Activity $activity) {
    //     $activity->group_id = $request->user()->id_group;
    // })
    //             ->event('Create')
    //             ->log('Create Transaction Pengeluaran: ' . $request->deskripsi . '');
    //     }
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Data Berhasil Disimpan',
    //         'data' => $transaction,
    //     ]);
    // }

    public function destroy(Request $request, $id)
    {
        $transaction = Transaksi::findOrFail($id);
        $deskripsi = Transaksi::where('id', $id)->first();
        activity()
            ->tap(function (Activity $activity) use ($request) {
                $activity->group_id = $request->user()->id_group;
            })
            ->event('Delete')
            ->log('Delete Transaction: ' . $deskripsi->deskripsi . '');
        $transaction->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Dihapus',
        ]);
    }
}
