<?php

namespace App\DataTables\Owner;

use App\Enums\UserStatusEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class UserDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('name', function ($row) {
                return $row->name;
            })
            ->editColumn('license_id', function ($row) {
                $license = $row->license;

                if (!$license) {
                    return '<span class="badge bg-danger">Unassigned</span>';
                }

                $color = $license->color ?? '#ccc';

                return '<span class="badge" style="background-color: ' . $color . '; color: #fff;">' . $license->name . '</span>';
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d/m/Y');
            })
            ->editColumn('last_login', function (User $row) {
                return $row->loginHistories->first()?->login_at->format('d/m/Y H:i') ?? 'Never';
            })
            ->editColumn('is_dinetkan', function (User $row) {
                if($row->is_dinetkan == 1) {
                    return "Mitra Dinetkan";
                }else{
                    return "";
                }
            })
            ->editColumn('action', function ($row) {
                $loginAs = '<a href="' . route('dinetkan.users.login-as', $row->id) . '" class="badge bg-warning"><i data-feather="log-in"></i></a>';
                $loginHistory = '<a href="javascript:void(0)" class="badge bg-info view-login-history" data-id="' . $row->id . '"><i data-feather="activity"></i></a>';

                return view('inc.action', [
                    'prepend' => $loginAs,
                    'edit'   => true,
                    'delete' => 'owner.users.destroy',
                    'data'   => $row,
                    'status' => [
                        'name' => 'status',
                        'route' => 'owner.users.status',
                        'value' => match ($row->status) {
                            UserStatusEnum::INACTIVE => true,
                            UserStatusEnum::ACTIVE => false,
                            UserStatusEnum::NEW => false,
                            UserStatusEnum::OVERDUE => true,
                        },
                        'confirmation' => 1,
                    ],
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
            ->rawColumns(['action', 'status', 'license_id']);
    }

    public function query(User $model): QueryBuilder
    {
        return $model
            ->with(['loginHistories', 'license'])
            ->where('role', 'Admin')
            ->where('is_dinetkan',0)
            ->orWhere('is_reguler', 1)
            ->leftJoin('login_histories', function ($join) {
                $join->on('users.id', '=', 'login_histories.user_id')
                    ->whereRaw('login_histories.id = (
                         SELECT id FROM login_histories
                         WHERE user_id = users.id
                         ORDER BY login_at DESC
                         LIMIT 1
                     )');
            })
            ->select('users.*', 'login_histories.login_at as last_login')
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
            Column::make('id')->title('ID')->orderable(),
            Column::make('name')->title('Name')->orderable()->searchable(),
            Column::make('email')->title('Email')->orderable()->searchable(),
            Column::make('username')->title('Username')->orderable()->searchable(),
            Column::make('whatsapp')->title('Whatsapp')->orderable()->searchable(),
            Column::make('license_id')->title('License')->orderable()->searchable(),
            Column::make('created_at')->title('Joined')->orderable()->searchable(false),
            Column::make('last_login')->title('Last Login')->orderable()->searchable(false),
            Column::make('is_dinetkan')->title('Mitra Dinetkan')->orderable()->searchable(false),
            Column::make('status')->title('Status')->orderable()->searchable(false),
            Column::make('action')->title('Actions')->orderable(false)->searchable(false),
        ];
    }

    protected function filename(): string
    {
        return 'User_' . date('YmdHis');
    }
}
