@extends('backend.layouts.app')
@section('main')
@section('title', 'Activity Log')
<!-- Content -->
<div class="container-lg">
    <!-- Page header -->
    <div class="row align-items-center mb-7">
        <div class="col-auto">
            <!-- Avatar -->
            <div class="avatar avatar-xl rounded text-primary">
                <span class="fs-2 material-symbols-outlined">
                    library_books
                    </span>
            </div>
        </div>
        <div class="col">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a class="text-body-secondary" href="#">Activity</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Log</li>
                </ol>
            </nav>

            <!-- Heading -->
            <h1 class="fs-4 mb-0">Activity Log</h1>
        </div>
        <div class="col-12 col-sm-auto mt-4 mt-sm-0" >
        
        </div>
    </div>

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
