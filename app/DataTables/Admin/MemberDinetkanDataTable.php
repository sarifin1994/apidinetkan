<?php

namespace App\DataTables\Admin;

use App\Enums\UserStatusEnum;
use App\Models\MemberDinetkan;
use App\Models\ProductDInetkan;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class MemberDinetkanDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()

            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d/m/Y');
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d/m/Y');
            })
            ->editColumn('service', function ($row) {
                $service = '<button class="btn btn-light-danger btn-sm">un-assign</button>';
                $product_service = ProductDInetkan::where('id', $row->product_dinetkan_id)->first();
                if($product_service != null){
                    $service = '<button class="btn btn-light-success btn-sm">'.$product_service->product_name.'</button>';
                }
                return $service;
            })
            ->editColumn('action', function ($row) {
                $button = ' <a href="javascript:void(0)" class="edit-icon delete btn btn-light-danger btn-sm" data-id="'.$row->id.'">Hapus</a>';
                return view('inc.action', [
                    'edit'   => true,
                    'data'   => $row,
                    'prepend' => $button,
                ]);
            })
            ->rawColumns(['action','service']);
    }

    public function query(MemberDinetkan $model): QueryBuilder
    {
        return $model
            ->where('dinetkan_user_id', auth()->user()->dinetkan_user_id)
            ->select('*')
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
                    // feather.replace();
                }',
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('id_member')->title('User Id')->orderable(),
            Column::make('first_name')->title('First Name')->orderable()->searchable(),
            Column::make('last_name')->title('Last Name')->orderable()->searchable(),
            Column::make('email')->title('Email')->orderable()->searchable(),
            Column::make('wa')->title('Whatsapp')->orderable()->searchable(),
            Column::make('service')->title('Service')->orderable()->searchable(),
            Column::make('created_at')->title('Joined')->orderable()->searchable(false),
            Column::make('action')->title('Actions')->orderable(false)->searchable(false),
        ];
    }

    protected function filename(): string
    {
        return 'Member_dinetkan_' . date('YmdHis');
    }
}
