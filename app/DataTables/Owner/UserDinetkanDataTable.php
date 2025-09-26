<?php

namespace App\DataTables\Owner;

use App\Enums\UserStatusEnum;
use App\Enums\ServiceStatusEnum;
use App\Models\MappingUserLicense;
use App\Models\User;
use App\Models\UserDinetkan;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class UserDinetkanDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('name', function ($row) {
                return $row->name;
            })

//            ->editColumn('otc_license_dinetkan_id', function ($row) {
//                $license = $row->license_otc;
//
//                if (!$license) {
//                    return 'Unassigned';
//                }
//
//                $color = $license->color ?? '#0a58ca';
//
//                return '<span class="badge" style="background-color: ' . $color . '; color: #fff;">' . $license->name . '</span>';
//            })
            ->editColumn('mrc_license_dinetkan_id', function ($row) {
                $order = MappingUserLicense::where('dinetkan_user_id', $row->dinetkan_user_id)->where('status', ServiceStatusEnum::ACTIVE)->with('service')->first();


                if (!isset($order->service)) {
                    return 'Unassigned';
                }

                $color = $license->color ?? '#0a58ca';

                return '<span class="badge" style="background-color: ' . $color . '; color: #fff;">' . $order->service->name . '</span>';
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d/m/Y');
            })
            ->editColumn('last_login', function (UserDinetkan $row) {
                return $row->loginHistories->first()?->login_at->format('d/m/Y H:i') ?? 'Never';
            })
            ->editColumn('action', function ($row) {
                $loginAs = '<a href="' . route('dinetkan.users.login-as', $row->id) . '" class="badge bg-success"><i data-feather="log-in"></i></a>';
                $loginHistory = '';
                $loginAs .= '<a href="' . route('dinetkan.users_dinetkan.detail', $row->dinetkan_user_id) . '" class="badge badge-warning" ><i data-feather="eye"></i></a>';
                $loginAs .= '<a href="' . route('dinetkan.users_dinetkan.detail_cacti', $row->dinetkan_user_id) . '" class="badge badge-danger" ><i data-feather="bar-chart-2"></i></a>';

//                return view('inc.action', [
//                    'prepend' => $loginAs,
//                    'edit'   => true,
//                    'delete' => 'owner.users.destroy',
//                    'data'   => $row,
//                    'status' => [
//                        'name' => 'status',
//                        'route' => 'owner.users.status',
//                        'value' => match ($row->status) {
//                            UserStatusEnum::INACTIVE => true,
//                            UserStatusEnum::ACTIVE => false,
//                            UserStatusEnum::NEW => false,
//                            UserStatusEnum::OVERDUE => true,
//                        },
//                        'confirmation' => 1,
//                    ],
//                    'append' => $loginHistory,
//                ]);
                return view('inc.action', [
                    'prepend' => $loginAs,
                    'edit'   => true,
//                    'delete' => 'owner.users.destroy',
                    'data'   => $row,
//                    'status' => [
//                        'name' => 'status',
//                        'route' => 'owner.users.status',
//                        'value' => match ($row->status) {
//                        UserStatusEnum::INACTIVE => true,
//                            UserStatusEnum::ACTIVE => false,
//                            UserStatusEnum::NEW => false,
//                            UserStatusEnum::OVERDUE => true,
//                        },
//                        'confirmation' => 1,
//                    ],
                    'append' => $loginHistory,
                ]);
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
            ->rawColumns(['action', 'status', 'mrc_license_dinetkan_id']);
    }

    public function query(UserDinetkan $model): QueryBuilder
    {
        return $model
            ->where('role', 'Admin')
            ->where('is_dinetkan',1)
            ->newQuery();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('user-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(0, 'asc')
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
            Column::make('dinetkan_user_id')->title('User Id')->orderable(),
            Column::make('first_name')->title('First Name')->orderable()->searchable(),
            Column::make('last_name')->title('Last Name')->orderable()->searchable(),
            Column::make('email')->title('Email')->orderable()->searchable(),
            Column::make('username')->title('Username')->orderable()->searchable(),
            Column::make('whatsapp')->title('Whatsapp')->orderable()->searchable(),
//            Column::make('otc_license_dinetkan_id')->title('OTC')->orderable()->searchable(),
            Column::make('mrc_license_dinetkan_id')->title('MRC')->orderable()->searchable(),
            Column::make('created_at')->title('Joined')->orderable()->searchable(false),
            Column::make('last_login')->title('Last Login')->orderable()->searchable(false),
            Column::make('status')->title('Status')->orderable()->searchable(false),
            Column::make('action')->title('Actions')->orderable(false)->searchable(false),
        ];
    }

    protected function filename(): string
    {
        return 'User_' . date('YmdHis');
    }
}
