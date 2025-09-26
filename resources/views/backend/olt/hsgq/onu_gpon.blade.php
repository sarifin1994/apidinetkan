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
                            <option value="all">ALL</option>
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
                            <th>ONT ID</th>
                            <th>Name</th>
                            <th>Serial Number</th>
                            <th>State</th>
                            <th>Running State</th>
                            <th>Device Type</th>
                            <th>Receive Power</th>
                            <th>Last Up Time</th>
                            <th>Last Down Time</th>
                            <th>Last Down Cause</th>
                            <th>Description</th>
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
                data: 'identifier',
                name: 'identifier',
            },
            {
                data: 'ont_name',
                name: 'ont_name',
            },
             {
                data: 'ont_sn',
                name: 'ont_sn',
            },
            {
                data: 'state',
                name: 'state',
                render: function(data) {
                    if (data === 1) {
                        return '<span class="badge bg-success-subtle text-success">Active</span>'
                    } else if (data === 2) {
                        return '<span class="badge bg-danger-subtle text-danger">Nonaktif</span>'
                    } else if (data === 3) {
                        return '<span class="badge bg-warning-subtle text-warning">Initial</span>'
                    } else {
                        return data
                    }
                }
            },
           {
                data: 'rstate',
                name: 'rstate',
                render: function(data) {
                    if (data === 1) {
                        return '<span class="badge bg-success-subtle text-success">Online</span>'
                    } else if (data === 2) {
                        return '<span class="badge bg-danger-subtle text-danger">Offline</span>'
                    } else if (data === 3) {
                        return '<span class="badge bg-warning-subtle text-warning">Initial</span>'
                    } else {
                        return data
                    }
                }
            },
             {
                data: 'dev_type',
                name: 'dev_type',
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
                data: 'last_u_time',
                name: 'last_u_time'
            },
            {
                data: 'last_d_time',
                name: 'last_d_time'
            },
            {
                data: 'last_d_cause',
                name: 'last_d_cause',
            },
            {
                data: 'ont_description',
                name: 'ont_description',
            },
        ]
    });
    $('#filter_pon').change(function() {
        window.location.href = '/olt/hsgq/pon/' + $('#filter_pon').val();
    });

// // Event klik pada baris tabel
// $('#myTable tbody').on('click', 'tr', function () {
//     var rowData = table.row(this).data();

//     // Jika data port_id tidak ada di rowData, bisa juga menggunakan variabel global yang berisi port saat ini
//     var port = rowData.port_id || 1;  // Misalnya, jika tidak ada, default ke 1
//     var onu = rowData.identifier;

//     // Bangun URL berdasarkan format yang diinginkan
//     var url = "/olt/hsgq/pon/" + port + "/onu/" + onu;

//     // Arahkan ke URL tersebut
//     window.location.href = url;
// });

</script>
@endpush
