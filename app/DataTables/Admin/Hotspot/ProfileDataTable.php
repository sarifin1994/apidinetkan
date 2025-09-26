<?php

namespace App\DataTables\Admin\Hotspot;

use App\Models\HotspotProfile;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ProfileDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', 'hotspotprofile.action')
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(HotspotProfile $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('hotspotprofile-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            //->dom('Bfrtip')
            ->orderBy(1)
            ->selectStyleSingle();
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        /**
         * <thead>
                            <tr>
                                <th style="text-align:left!important">NO</th>
                                <th>NAMA PROFILE</th>
                                <th>HARGA</th>
                                <th>HARGA RSLR</th>
                                <th>RATE LIMIT</th>
                                <th>QUOTA</th>
                                <th>UPTIME</th>
                                <th>VALIDITY</th>
                                <th>SHARED</th>
                                <th>MACK LOCK</th>
                                <th>GROUP PROFILE</th>
                                <th>AKSI</th>
                            </tr>
                        </thead>

                        columns: [{
                data: null,
                'sortable': false,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: 'name',
                name: 'name',
            },
            {
                data: 'price',
                name: 'price',
                render: $.fn.dataTable.render.number('.', ',', 0, ''),
            },
            {
                data: 'reseller_price',
                name: 'reseller_price',
                render: $.fn.dataTable.render.number('.', ',', 0, ''),
            },
            {
                data: 'rateLimit',
                name: 'rateLimit'
            },
            {
                data: 'quota',
                name: 'quota',
                render: function bytesToSize(data) {
                    var sizes = ['Bytes', 'KB', 'MB', 'GB',
                        'TB'
                    ];
                    if (data == 'Unlimited') return 'Unlimited';
                    var i = parseInt(Math.floor(Math.log(
                        data) / Math.log(1024)));
                    if (i == 0) return data + ' ' + sizes[i];
                    return (data / Math.pow(1024, i)).toFixed(
                        1) + ' ' + sizes[i];
                }
            },
            {
                data: 'uptime',
                name: 'uptime',
                render: function convertSecondsToReadableString(
                    seconds) {
                    if (seconds === 'Unlimited') {
                        return 'Unlimited';
                    }
                    seconds = seconds || 0;
                    seconds = Number(seconds);
                    seconds = Math.abs(seconds);

                    const d = Math.floor(seconds / (3600 * 24));
                    const h = Math.floor(seconds % (3600 * 24) /
                        3600);
                    const m = Math.floor(seconds % 3600 / 60);
                    const s = Math.floor(seconds % 60);
                    const parts = [];

                    if (d > 0) {
                        parts.push(d + ' HARI');
                    }

                    if (h > 0) {
                        parts.push(h + ' JAM');
                    }

                    if (m > 0) {
                        parts.push(m + ' BULAN');
                    }

                    // if (s > 0) {
                    //     parts.push(s + ' second' + (s > 1 ? 's' :
                    //         ''));
                    // }

                    return parts.join(' ');
                }
            },
            {
                data: 'validity',
                name: 'validity',
                render: function convertSecondsToReadableString(
                    seconds) {
                    if (seconds === 'Unlimited') {
                        return 'Unlimited';
                    }
                    seconds = seconds || 0;
                    seconds = Number(seconds);
                    seconds = Math.abs(seconds);

                    const d = Math.floor(seconds / (3600 * 24));
                    const h = Math.floor(seconds % (3600 * 24) /
                        3600);
                    const m = Math.floor(seconds % 3600 / 60);
                    const s = Math.floor(seconds % 60);
                    const parts = [];

                    if (d > 0) {
                        parts.push(d + ' HARI');
                    }

                    if (h > 0) {
                        parts.push(h + ' JAM');
                    }

                    if (m > 0) {
                        parts.push(m + ' BULAN');
                    }

                    // if (s > 0) {
                    //     parts.push(s + ' second' + (s > 1 ? 's' :
                    //         ''));
                    // }

                    return parts.join(' ');
                }
            },
            {
                data: 'shared',
                name: 'shared'
            },
            {
                data: 'mac',
                name: 'mac',
                render: function(data) {
                    if (data === 0) {
                        return 'Disable'
                    }
                    return 'Enable'
                }
            },
            {
                data: 'groupProfile',
                name: 'groupProfile'
            },
            {
                data: 'action',
                name: 'action'
            }
        ]
         */
        return [
            Column::make('id')->title('Id')->orderable(true)->searchable(false),
            Column::make('name')->title('Profile Name')->orderable(true)->searchable(true),
            Column::make('price')->title('Price')
                ->orderable(true)
                ->searchable(true)
                ->render("number('.', ',', 0, '')"),
            Column::make('reseller_price')->title('Reseller Price')
                ->orderable(true)
                ->searchable(true)
                ->render("number('.', ',', 0, '')"),
            Column::make('rateLimit')->title('Rate Limit')->orderable(true)->searchable(true),
            Column::make('quota')->title('Quota')
                ->orderable(true)


        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'HotspotProfile_' . date('YmdHis');
    }
}
