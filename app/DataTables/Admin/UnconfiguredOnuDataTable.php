<?php

namespace App\DataTables\Admin;

use App\Models\OltDevice;
use Yajra\DataTables\Html\Column;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Modules\Olt\Services\OltService;
use Request;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\CollectionDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;

class UnconfiguredOnuDataTable extends DataTable
{
    public function dataTable($apiData): CollectionDataTable
    {
        return (new CollectionDataTable(collect($apiData)))
            ->addIndexColumn()  // Menambahkan kolom nomor urut
            ->editColumn('pon_type', fn($row) => $row['zxAnPonSrvChannelType'] ?? '-')  // Menampilkan nilai zxAnPonSrvChannelType sebagai PON Type
            ->editColumn('board', fn($row) => '')  // Kolom Board dikosongkan
            ->editColumn('port', fn($row) => $row['zxAnGponSrvUnConfOnuChannelId'] ?? '-')  // Menampilkan nilai zxAnGponSrvUnConfOnuChannelId sebagai Port
            ->editColumn('pon_description', fn($row) => '')  // Kolom PON Description dikosongkan
            ->editColumn('sn', fn($row) => $row['zxAnGponSrvUnConfOnuSn'] ?? '-')  // Menampilkan nilai zxAnGponSrvUnConfOnuSn sebagai SN
            ->editColumn('type', fn($row) => $row['zxAnGponSrvUnConfOnuType'] ?? '-')  // Menampilkan nilai zxAnGponSrvUnConfOnuType sebagai Type
            ->addColumn('action', fn($row) => '<button class="btn btn-primary" data-toggle="modal" data-target="#modalAction" data-id="' . $row['id'] . '">Action</button>') // Menambahkan tombol aksi yang memunculkan modal
            ->rawColumns(['action']);  // Menandai kolom 'action' sebagai raw untuk menampilkan HTML (tombol)
    }

    public function query(Request $request): array
    {
        $idOlt = $request->session()->get('id_olt');
        $olt = OltDevice::find($idOlt);

        $oltService = new OltService(
            $olt->model,
            $olt->host,
            $olt->username,
            $olt->password,
            $olt->token ?? '',
            $olt->udp_port ?? '',
            $olt->snmp_read_write ?? '',
            $olt->version ?? ''
        );


        $response = $oltService->getBoardInfo();
        // TYPE PON UNCOFIGURED
        $mapArray4 = array_column($response['response']['data_unconfigured_onu_type'], 'zxAnPonSrvChannelType', 'id');
        foreach ($response['response']['unconfigured_onu'] as &$item) {
            $id = $item['id'];
            $item['zxAnPonSrvChannelType'] = $mapArray4[$id] ?? null; // Tambahkan data dari array2
        }
        // TYPE PON UNCOFIGURED
        return  $response['response']['unconfigured_onu'] ?? [];
    }

    // public function ajax(): \Illuminate\Http\JsonResponse
    // {
    //     return $this->dataTable($this->query())
    //         ->toJson();
    // }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('unconfigured-olt-table')
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
            Column::make('DT_RowIndex')->title('No')->orderable(false)->searchable(false),  // Kolom nomor urut
            Column::make('zxAnPonSrvChannelType')->title('Pon Type'),  // Kolom untuk PON TYPE
            Column::make('board')->title('Board')->searchable(false)->orderable(false),  // Kolom untuk Board (kosongkan saja)
            Column::make('zxAnGponSrvUnConfOnuChannelId')->title('Port'),  // Kolom untuk Port
            Column::make('pon_description')->title('PON Description')->searchable(false)->orderable(false),  // Kolom PON Description (kosongkan saja)
            Column::make('zxAnGponSrvUnConfOnuSn')->title('SN'),  // Kolom untuk SN
            Column::make('zxAnGponSrvUnConfOnuType')->title('Type'),  // Kolom untuk Type
            Column::make('action')->title('Actions')->orderable(false)->searchable(false),  // Kolom untuk aksi (tombol untuk modal)
        ];
    }
}
