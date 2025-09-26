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
            <h1 class="fs-4 mb-0">OLT PON {{ $port = request()->segment(4) }}</h1>
        </div>
        <div class="col-12 col-sm-auto mt-4 mt-sm-0">
            <div class="row mb-3">

                <div class="col-auto">
                    <div class="form-group">
                        @php
                            $port = request()->segment(4);
                        @endphp
                        <select class="form-select" id="filter_pon" name="filter_pon">
                            {{-- <option value="all">ALL</option> --}}
                            @foreach ($data['pon'] as $row)
                                <option value="{{ $row['port_id'] }}" <?php if ($port == $row['port_id']) {
                                    echo ' selected="selected"';
                                } ?>>PON {{ $row['port_id'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-auto">
                    <a href="/olt/hsgq/dashboard" class="btn btn-primary text-light me-2">
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
                            <th>ONU NAME</th>
                            <th>MAC ADDRESS</th>
                            <th style="text-align:left!important">RX POWER</th>
                            <th style="text-align:left!important">STATUS</th>
                            <th style="text-align:left!important">LAST DEREGISTER TIME</th>
                            <th style="text-align:left!important">LAST DEREGISTER REASON</th>
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
        scrollX: true,
        lengthMenu: [64, 128, 256, 512],
        ajax: '{{ url()->current() }}',
        columns: [{
                data: 'onu_id',
                name: 'onu_id',
                render: function(data, type, row) {
                    return row.port_id + '/' + row.onu_id
                },
            },
            {
                data: 'onu_name',
                name: 'onu_name',
                render: function(data,type,row) {
                return '<a href="/olt/hsgq/pon/'+row.port_id+'/onu/'+row.onu_id+'" class="badge bg-primary-subtle text-primary">'+data+'</a>';
                }
            },
            {
                data: 'macaddr',
                name: 'macaddr',
                width: '200px',
                // render: function(data) {
                //     return '<span class="badge bg-light text-dark">'+data+'</span>'
                // }
            },
            {
                data: 'receive_power',
                name: 'receive_power',
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
                name: 'status',
                render: function(data) {
                    if (data === 'Online') {
                        return '<span class="badge bg-success-subtle text-success">Online</span>'
                    } else if (data === 'Offline') {
                        return '<span class="badge bg-danger-subtle text-danger">Offline</span>'
                    } else if (data === 'Initial') {
                        return '<span class="badge bg-warning-subtle text-warning">Initial</span>'
                    } else {
                        return data
                    }
                }
            },
            {
                data: 'last_down_time',
                name: 'last_down_time'
            },
            {
                data: 'last_down_reason',
                name: 'last_down_reason',
            },
        ]
    });
    $('#filter_pon').change(function() {
        window.location.href = '/olt/hsgq/pon/' + $('#filter_pon').val();
    });
</script>
@endpush
