<?php

namespace App\DataTables\Admin\Hotspot;

use App\Models\HotspotUser;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class UserDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('password', function ($row) {
                return str_repeat('*', 6);
            })
            ->editColumn('profile', function ($row) {
                return $row->rprofile->name ?? '-';
            })
            ->editColumn('nas', function ($row) {
                return $row->radius->nasname ?? '-';
            })
            ->editColumn('reseller_id', function ($row) {
                return $row->reseller->name ?? '-';
            })
            ->editColumn('status', function ($row) {
                $status = $row->status;

                if ($status === 1) {
                    return '<span class="text-primary">new</span>';
                } else if ($status === 0) {
                    return '<span class="text-warning">off</span>';
                } else if ($status === 2) {
                    return '<span class="text-success">active</span>';
                } else if ($status === 3) {
                    return '<span class="text-danger">expired</span>';
                }

                return '<span class="text-danger">unknown</span>';
            })
            ->editColumn('action', function ($row) {
                return view('inc.action', [
                    'edit'   => true,
                    'delete' => 'admin.hotspot.user.destroy',
                    'data'   => $row
                ]);
            })
            ->rawColumns(['status', 'action']);
    }

    public function query(HotspotUser $model): QueryBuilder
    {
        return $model->newQuery()
            ->where('group_id', Auth::user()->id_group)
            ->with(['radius', 'reseller', 'rprofile', 'session'])
            ->orderBy('id', 'desc');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('hotspot-user-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->parameters([
                'language' => [
                    'emptyTable' => 'No Records Found',
                    'infoEmpty' => '',
                    'zeroRecords' => 'No Records Found',
                ],
                'drawCallback' => 'function(settings) {
                    if (settings._iRecordsDisplay === 0) {
                        $(settings.nTableWrapper).find(".dataTables_paginate").hide();
                    } else {
                        $(settings.nTableWrapper).find(".dataTables_paginate").show();
                    }
                    feather.replace();
                }',
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('No')->orderable(false)->searchable(false),
            Column::make('username')->title('Username'),
            Column::make('password')->title('Password'),
            Column::make('profile')->title('Profile'),
            Column::make('nas')->title('NAS'),
            Column::make('server')->title('Server'),
            Column::make('status')->title('Status'),
            Column::make('reseller_id')->title('Reseller'),
            Column::make('statusPayment')->title('Payment'),
            Column::make('action')->title('Actions')->orderable(false)->searchable(false),
        ];
    }

    protected function filename(): string
    {
        return 'HotspotUsers_' . date('YmdHis');
    }
}
