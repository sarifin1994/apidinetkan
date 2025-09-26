<?php

namespace App\DataTables\Owner;

use App\Models\LicenseDinetkan;
use App\Models\User;
use App\Models\License;
use App\Enums\OltDeviceEnum;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class LicenseDinetkanDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->editColumn('payment_gateway', function ($license) {
                return $license->payment_gateway ? 'Yes' : 'No';
            })
            ->editColumn('whatsapp', function ($license) {
                return $license->whatsapp ? 'Yes' : 'No';
            })
            ->editColumn('invoice_addon', function ($license) {
                return $license->invoice_addon ? 'Yes' : 'No';
            })
            ->editColumn('users', function ($license) {
                return $license->hasMany(User::class, 'license_id')->count();
            })
            ->editColumn('category_id', function ($license) {
                $category = $license->category;
                return $category->name;
            })
            ->editColumn('ppn', function ($license) {
                if(!$license->ppn){
                    return "0%";
                }
                return $license->ppn."%";
            })
//            ->editColumn('ppn_otc', function ($license) {
//                if(!$license->ppn_otc){
//                    return "0%";
//                }
//                return $license->ppn_otc."%";
//            })
//            ->editColumn('color', function ($license) {
//                return '<div style="width: 30px; height: 30px; border-radius: 50%; background-color: ' . $license->color . '; border: 1px solid #ccc;"></div>';
//            })
            ->editColumn('action', function ($row) {
                return view('inc.action', [
                    'edit'   => true,
                    'delete' => 'dinetkan.license_dinetkan.destroy',
                    'data'   => $row
                ]);
            })
            ->rawColumns(['color', 'action']);
    }

    public function query(LicenseDinetkan $model): QueryBuilder
    {
        return $model->newQuery();
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('license-table')
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
//                    feather.replace();
                }',
            ]);
    }

    public function getColumns(): array
    {
        return [
            Column::make('id')->title('ID')->orderable(true),
            Column::make('name')->title('Name'),
            Column::make('price')->title('Price')->renderJs("number('.', ',', 0, '')"),
            Column::make('ppn')->title('PPN'),
//            Column::make('price_otc')->title('Price OTC')->renderJs("number('.', ',', 0, '')"),
//            Column::make('ppn_otc')->title('PPN OTC'),
//            Column::make('limit_nas')->title('Limit NAS'),
//            Column::make('limit_pppoe')->title('Limit PPPoE'),
//            Column::make('limit_hs')->title('Limit Hotspot'),
//            Column::make('limit_vpn')->title('Limit VPN'),
//            Column::make('limit_vpn_remote')->title('Limit VPN Remote'),
//            Column::make('limit_user')->title('Limit User'),
//            Column::make('olt_epon_limit')->title('OLT EPON Limit'),
//            Column::make('olt_gpon_limit')->title('OLT GPON Limit'),
//            Column::make('olt_epon')->title('OLT EPON')->orderable(false)->searchable(false),
//            Column::make('olt_gpon')->title('OLT GPON')->orderable(false)->searchable(false),
//            Column::make('olt_models')->title('OLT Models')->orderable(false)->searchable(false),
//            Column::make('payment_gateway')->title('Payment Gateway')->orderable(false)->searchable(false),
//            Column::make('whatsapp')->title('WhatsApp'),
//            Column::make('invoice_addon')->title('Invoice Addon'),
//            Column::make('max_buy')->title('Max Buy'),
//            Column::make('users')->title('Users'),
//            Column::make('color')->title('Color')->orderable(false)->searchable(false), // New color column
            Column::make('capacity')->title('Capacity'),
            Column::make('descriptions')->title('Descriptions'),
            Column::make('category_id')->title('Category'),
//            Column::make('type')->title('Type'),
//            Column::make('ppn')->title('PPN'),
            Column::make('komisi_mitra')->title('Komisi Mitra'),
            Column::make('action')->title('Actions')->orderable(false)->searchable(false),
        ];
    }

    protected function filename(): string
    {
        return 'License_dinetkan_' . date('YmdHis');
    }
}
