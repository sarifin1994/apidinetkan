<?php

namespace App\DataTables\Admin\Hotspot;

use App\Models\HotspotOnline;
use App\Models\RadiusSession;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class OnlineDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->setRowId('id')
            ->editColumn('session_id', function ($row) {
                return '<span class="badge bg-success">' . $row->session_id . '</span>';
            })
            ->editColumn('mac', function ($row) {
                return '<span class="badge bg-primary">' . $row->mac . '</span>';
            })
            ->editColumn('input', function ($row) {
                return formatBytes($row->input) ?: '0 Bytes';
            })
            ->editColumn('output', function ($row) {
                return formatBytes($row->output) ?: '0 Bytes';;
            })
            ->editColumn('uptime', function ($row) {
                return formatTime($row->uptime);
            })
            ->rawColumns(['session_id', 'mac', 'input', 'output', 'uptime'])
        ;
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(RadiusSession $model): QueryBuilder
    {
        return $model->newQuery()
            ->where('shortname', Auth::user()->shortname)
            ->where([['status', 1], ['type', 1]]);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('hotspotonline-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->selectStyleSingle();
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('DT_RowIndex')->title('No')->searchable(false)->orderable(false),
            Column::make('session_id')->title('Session')->searchable(true)->orderable(true),
            Column::make('username')->title('Username')->searchable(true)->orderable(true),
            Column::make('ip')->title('IP')->searchable(true)->orderable(true),
            Column::make('mac')->title('MAC')->searchable(true)->orderable(true),
            Column::make('input')->title('Upload')->searchable(true)->orderable(true),
            Column::make('output')->title('Download')->searchable(true)->orderable(true),
            Column::make('uptime')->title('Uptime')->searchable(true)->orderable(true),
            Column::make('start')->title('Last Login')->searchable(true)->orderable(true),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'HotspotOnline_' . date('YmdHis');
    }
}
