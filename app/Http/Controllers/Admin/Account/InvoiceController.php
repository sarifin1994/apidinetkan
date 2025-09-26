<?php

namespace App\Http\Controllers\Admin\Account;

use App\Enums\InvoiceStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\AdminInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        return view('accounts.invoices.index');
    }

    public function unpaid(Request $request)
    {
        if (! $request->ajax()) {
            return abort(404);
        }

        $invoices = AdminInvoice::query()
            ->where('group_id', $request->user()->id_group)
            ->where('status', InvoiceStatusEnum::UNPAID)
            ->where('due_date','>=', Carbon::now()->format('Y-m-d'))
            ->latest();
        return DataTables::of($invoices)
            ->addIndexColumn()
            ->editColumn('status', function ($row) {
                return match ($row->status) {
                    InvoiceStatusEnum::UNPAID => '<span class="badge bg-warning">Unpaid</span>',
                    InvoiceStatusEnum::PAID => '<span class="badge bg-success">Paid</span>',
                    default => '<span class="badge bg-danger">Canceled</span>',
                };
            })
            ->addColumn('action', function ($row) {
                return '<a href="' . route('admin.invoice', $row->no_invoice) . '" class="btn btn-xs btn-info" title="Pay">Pay</a>';
            })
            ->rawColumns(['action', 'status'])
            ->toJson();
    }

    public function paid(Request $request)
    {
        if (! $request->ajax()) {
            return abort(404);
        }

        $invoices = AdminInvoice::query()
            ->where('group_id', $request->user()->id_group)
            ->where('status', InvoiceStatusEnum::PAID)
            ->latest();

        return DataTables::of($invoices)
            ->addIndexColumn()
            ->editColumn('status', function ($row) {
                return match ($row->status) {
                    InvoiceStatusEnum::UNPAID => '<span class="badge bg-warning">Unpaid</span>',
                    InvoiceStatusEnum::PAID => '<span class="badge bg-success">Paid</span>',
                    default => '<span class="badge bg-danger">Canceled</span>',
                };
            })
            ->addColumn('action', function ($row) {
                return '<a href="' . route('admin.invoice', $row->no_invoice) . '" class="btn btn-xs btn-info" title="Pay">Pay</a>';
            })
            ->rawColumns(['action', 'status'])
            ->toJson();
    }

    public function expired(Request $request)
    {
        if (! $request->ajax()) {
            return abort(404);
        }

        $invoices = AdminInvoice::query()
            ->where('group_id', $request->user()->id_group)
            ->whereIn('status', [InvoiceStatusEnum::EXPIRED,InvoiceStatusEnum::CANCEL])
            ->where('due_date','<', Carbon::now())
            ->latest();
        return DataTables::of($invoices)
            ->addIndexColumn()
            ->editColumn('status', function ($row) {
                return match ($row->status) {
                InvoiceStatusEnum::EXPIRED => '<span class="badge bg-">warning</span>',
                    InvoiceStatusEnum::CANCEL => '<span class="badge bg-danger">CANCEL</span>',
                    default => '<span class="badge bg-danger">Canceled</span>',
                };
            })
            ->addColumn('action', function ($row) {
                return '';
            })
            ->rawColumns(['action', 'status'])
            ->toJson();
    }
}
