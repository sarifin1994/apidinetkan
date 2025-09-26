@extends('backend.layouts.app_new')
@section('main')
@section('title', 'Activity Log')
<!-- Content -->
<div class="container-fluid">
    <!-- Page header -->
     <div class="row align-items-center mb-7">
        <!-- Breadcrumb and Title -->
        <div class="col-md-6 mb-3 mb-md-0">
            <h4 class="main-title">Activity Log</h4>
            <ul class="app-line-breadcrumbs mb-0">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <span><i class="ti ti-logs f-s-16"></i> Activity</span>
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="javascript:void(0)">Log</a>
                </li>
            </ul>
        </div>
    </div><br />

    <!-- Page content -->
    <div class="row">
            <div class="card-body table-responsive">
                <table id="myTable" class="table table-responsive table-hover display nowrap" width="100%">
                    <thead>
                        <tr>
                            <th style="text-align:left!important">No</th>
                                <th>Time GMT+7</th>
                                <th>Topic</th>
                                <th>Activity</th>
                                <th>Causer</th>
                                <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
    </div>

</div>
@endsection

@push('scripts')
<script type="text/javascript">
let table = $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        order: [[1, 'desc']],
        ajax: '{{ url()->current() }}',
        columns: [{
                data: null,
                'sortable': false,
                width: '10px',
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: 'created_at',
                name: 'created_at',
                render: function(data, type, row, meta) {
                    return moment(data).local().format('DD/MM/YYYY HH:mm:ss');
                },
            },
            {
                data: 'event',
                name: 'event',
                render: function(data) {
                    if (data === 'Create') {
                        return '<span class="text-success">Create</span>'
                    } else if (data === 'Delete') {
                        return '<span class="text-danger">Delete</span>'
                    } else if (data === 'Update') {
                        return '<span class="text-warning">Update</span>'
                    }
                }
            },
            {
                data: 'description',
                name: 'description'
            },
            {
                data: 'username',
                name: 'username',
                sortable: false,
                searchable: false,
            },

            {
                data: 'role',
                name: 'role',
                sortable: false,
                searchable: false,
            },
        ]
    });
</script>
@endpush
