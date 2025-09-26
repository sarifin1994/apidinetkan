<?php

namespace App\DataTables\Owner;

use App\Enums\UserStatusEnum;
use App\Models\License;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class BillingDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->filterColumn('total', function ($query, $keyword) {
                $query->whereRaw('LOWER(license.price) LIKE ?', ["%{$keyword}%"]);
            })
            ->editColumn('license_id', function ($row) {
                $license = $row->license;

                if (!$license) {
                    return '<span class="badge bg-danger">Unassigned</span>';
                }

                $color = $license->color ?? '#ccc';

                return '<span class="badge" style="background-color: ' . $color . '; color: #fff;">' . $license->name . '</span>';
            })
            ->editColumn('status', function ($row) {
                if ($row->status === UserStatusEnum::INACTIVE) {
                    return '<span class="badge bg-danger">Disabled</span>';
                } else if ($row->status === UserStatusEnum::ACTIVE) {
                    return '<span class="badge bg-success">Active</span>';
                } else if ($row->status === UserStatusEnum::NEW) {
                    return '<span class="badge bg-warning">New</span>';
                } else if ($row->status === UserStatusEnum::OVERDUE) {
                    return '<span class="badge bg-danger">Expired</span>';
                }
            })
            ->editColumn('action', function ($row) {
                return '<a href="' . route('dinetkan.billing.renew', $row->id) . '" class="btn btn-xs btn-success" title="Renew">Renew</a>';
            })
            ->rawColumns(['action', 'license_id', 'status']);
    }

    public function query(User $model): QueryBuilder
    {
        return $model->select('users.*', 'license.price as total')
            ->leftJoin('license', 'users.license_id', '=', 'license.id')
            ->where('role', 'Admin')
            ->whereIn('status', [UserStatusEnum::ACTIVE, UserStatusEnum::OVERDUE])
            ->with('license');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('user-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0)
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
            Column::make('id')->title('ID')->orderable(true)->searchable(false),
            Column::make('username')->title('Username')->orderable(true)->searchable(true),
            Column::make('license_id')->title('License')->orderable(true)->searchable(true),
            Column::make('total')->title('Total')->orderable(true)->searchable(true)->renderJs("number('.', ',', 0, '')"),
            Column::make('next_due')->title('Next Due')->orderable(true)->searchable(true),
            Column::make('status')->title('Status')->orderable(true)->searchable(false),
            Column::make('action')->title('Actions')->orderable(false)->searchable(false),
        ];
    }

    protected function filename(): string
    {
        return 'User_' . date('YmdHis');
    }
}
