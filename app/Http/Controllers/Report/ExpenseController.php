<?php

namespace App\Http\Controllers\Report;

use App\Enums\TransactionCategoryEnum;
use App\Enums\TransactionTypeEnum;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Midtrans\Transaction;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        if (request()->ajax()) {
            $transactions = Transaksi::query()->where('group_id', $request->user()->id_group)->where('type', TransactionTypeEnum::EXPENSE);
            return DataTables::of($transactions)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '<a href="javascript:void(0)" id="btn-edit-expense"
                    data-id="' .
                        $row->id .
                        '" class="badge b-ln-height badge-secondary">
                        <i class="fas fa-edit"></i>
                </a>

                <a href="javascript:void(0)"
                class="badge b-ln-height badge-danger" id="delete" data-id="' .
                        $row->id .
                        '">
                <i class="fas fa-trash-alt"></i>
                </a>';
                })

                ->toJson();
        }

        $categories = [
            TransactionCategoryEnum::PARTNER_FEE,
            TransactionCategoryEnum::OPERATIONAL,
            TransactionCategoryEnum::BELANJA,
            TransactionCategoryEnum::OTHER
        ];

        $total = Transaksi::where('group_id', $request->user()->id_group)->where('type', TransactionTypeEnum::EXPENSE)->count();
        $totalCash = Transaksi::where('group_id', $request->user()->id_group)->where('payment_method', 1)->where('type', TransactionTypeEnum::EXPENSE)->count();
        $totalTransfer = Transaksi::where('group_id', $request->user()->id_group)->where('payment_method', 2)->where('type', TransactionTypeEnum::EXPENSE)->count();

        return view('reports.expense.index', [
            'categories' => $categories,
            'total' => $total,
            'totalCash' => $totalCash,
            'totalTransfer' => $totalTransfer,
        ]);
    }

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

    public function store(Request $request)
    {
        $price = str_replace('.', '', $request->price);

        $transaction = Transaksi::create([
            'group_id' => $request->user()->id_group,
            'tanggal' => $request->tanggal . ' ' . Carbon::now()->format('H:i:s'),
            'admin' => $request->user()->username,
            'type' => $request->type,
            'category' => $request->item,
            'item' => TransactionCategoryEnum::fromValue($request->item)->label(),
            'deskripsi' => $request->deskripsi,
            'price' => $price,
            'payment_method' => $request->payment_method,
        ]);
        activity()
            ->tap(function (Activity $activity) use ($request) {
                $activity->group_id = $request->user()->id_group;
            })
            ->event('Create')
            ->log('Create Transaction Pengeluaran: ' . $request->deskripsi . '');
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $transaction,
        ]);
    }
}
