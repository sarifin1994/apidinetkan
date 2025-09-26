<?php

namespace App\Http\Controllers\Report;

use App\Enums\TransactionCategoryEnum;
use App\Enums\TransactionPaymentMethodEnum;
use App\Enums\TransactionTypeEnum;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        if (request()->ajax()) {
            $transactions = Transaksi::query()->where('group_id', $request->user()->id_group)->where('type', TransactionTypeEnum::INCOME);
            return DataTables::of($transactions)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '<a href="javascript:void(0)" id="btn-edit-income"
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
            TransactionCategoryEnum::INVOICE,
            TransactionCategoryEnum::HOTSPOT,
            TransactionCategoryEnum::NEW_CLIENT,
            TransactionCategoryEnum::OTHER,
        ];

        $total = Transaksi::where('group_id', $request->user()->id_group)->where('type', TransactionTypeEnum::INCOME)->count();
        $totalCash = Transaksi::where('group_id', $request->user()->id_group)->where('payment_method', TransactionPaymentMethodEnum::CASH)->where('type', TransactionTypeEnum::INCOME)->count();
        $totalTransfer = Transaksi::where('group_id', $request->user()->id_group)->where('payment_method', TransactionPaymentMethodEnum::TRANSFER)->where('type', TransactionTypeEnum::INCOME)->count();

        return view('reports.revenue.index', [
            'categories' => $categories,
            'total' => $total,
            'totalCash' => $totalCash,
            'totalTransfer' => $totalTransfer,
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
            ->log('Create Transaction Pemasukan: ' . $request->deskripsi . '');
        return response()->json([
            'success' => true,
            'message' => 'Data Berhasil Disimpan',
            'data' => $transaction,
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
}
