@extends('backend.layouts.app')
@section('main')
@section('title', 'Daftar ONU')
<div class="container-lg">
    <!-- Page header -->
    <div class="row align-items-center mb-7">
        <div class="col-auto">
            <!-- Avatar -->
            <div class="avatar avatar-xl rounded text-primary">
                <i class="fs-2" data-duoicon="airplay"></i>
            </div>
        </div>
        <div class="col">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a class="text-body-secondary" href="#">OLT</a></li>
                    <li class="breadcrumb-item active" aria-current="page">PON</li>
                </ol>
            </nav>

            <!-- Heading -->
            <h1 class="fs-4 mb-0">OLT PON {{ $port = request()->segment(5) }}</h1>
        </div>
        <div class="col-12 col-sm-auto mt-4 mt-sm-0">
            <div class="row mb-3">

                <div class="col-auto">
                    <div class="form-group">
                        @php
                            $port = request()->segment(4);
                        @endphp
                        <select class="form-select" id="filter_pon" name="filter_pon">
                            <option value="">ALL</option>
                            @foreach ($data['pon'] as $row)
                                <option value="{{ $row['port_id'] }}" @selected($ports == $row['port_id'])>
                                    PON {{ $row['port_id'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-auto">
                    <a href="/olt/hioso/dashboard" class="btn btn-primary text-light me-2">
                        <span class="material-symbols-outlined">
                            reply
                        </span> KEMBALI
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">

            <div class="card-body table-responsive">

                <table id="myTable" class="table table-hover display nowrap" width="100%">
                    <thead>
                        <tr>
                            <th>ONU ID</th>
                            <th>Name</th>
                            <th>MAC</th>
                            <th style="text-align:left!important">RX Power</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
    let table = $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ url()->current() }}',
        columns: [{
                data: 'id',
                name: 'id',
            },
            {
                data: 'name',
                name: 'name',
            },
            {
                data: 'mac'
            },

            {
                data: 'rx_power',
                render: function(data) {
                    if (data > -8) {
                        return '<span class="badge bg-success-subtle text-success">' + data + '</span>';
                    } else if (data < -8 && data > -25) {
                        return '<span class="badge bg-success-subtle text-success">' + data + '</span>';
                    } else if (data <= -25) {
                        return '<span class="badge bg-warning-subtle text-warning">' + data + '</span>';
                    } else {
                        return '<span class="badge bg-danger-subtle text-danger">' + data + '</span>';
                    }
                },
            },
            {
                data: 'status',
                render: function(data) {
                    if (data === 'Up') {
                        return '<span class="badge bg-success-subtle text-success">Online</span>'
                    } else if (data === 'Down') {
                        return '<span class="badge bg-danger-subtle text-danger">Offline</span>'
                    } else if (data === 'Initial') {
                        return '<span class="badge bg-warning-subtle text-warning">Initial</span>'
                    } else {
                        return data
                    }
                }
            },
            {
                data: 'id',
                name: 'actions',
                orderable: false,
                searchable: false,
                render: function(data,type,row) {
                    return `
            <button class="btn btn-sm btn-primary btn-rename" data-onuid="${row.id}" data-ponid="${row.id}" data-ponname="${row.name}">Rename</button>
            <button class="btn btn-sm btn-secondary btn-reboot" data-onuid="${row.id}" data-ponid="${row.id}" data-ponname="${row.name}">Reboot</button>
            <button class="btn btn-sm btn-danger btn-delete" data-onuid="${row.id}" data-ponid="${row.id}" data-ponname="${row.name}">Delete</button>
        `;
                }
            }
        ]
    });


    $('#filter_pon').change(function() {
        window.location.href = '/olt/hioso/pon/' + $('#filter_pon').val();
    });

   
</script>
@endpush
