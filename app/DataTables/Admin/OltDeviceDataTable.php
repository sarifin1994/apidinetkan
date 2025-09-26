<?php

namespace App\DataTables\Admin;

use App\Models\OltDevice;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;

class OltDeviceDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('model', function ($row) {
                return $row->model->label();
            })
            ->editColumn('password', function ($row) {
                return str_repeat('*', 6);
            })
            ->editColumn('action', function ($row) {
                $login = '<a href="javascript:void(0)" id="login" data-id="' . $row->id . '" class="text-success">
                            <i class="ti ti-login"></i>
                        </a>';

                return view('inc.action', [
                    'prepend' => $login,
                    'edit'   => true,
                    'delete' => 'admin.olt.destroy',
                    'data'   => $row
                ]);
            })
            ->rawColumns(['action']);
    }

    public function query(OltDevice $model): QueryBuilder
    {
        return $model->newQuery()->where('group_id', Auth::user()->id_group)->orderBy('id', 'desc');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('olt-table')
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
            Column::make('name')->title('Name'),
            Column::make('model')->title('Model'),
            Column::make('type')->title('Type'),
            Column::make('host')->title('IP Address'),
            Column::make('username')->title('Username'),
            Column::make('password')->title('Password'),
            Column::make('action')->title('Actions')->orderable(false)->searchable(false),
        ];
    }

    protected function filename(): string
    {
        return 'OltDevices_' . date('YmdHis');
    }
}
