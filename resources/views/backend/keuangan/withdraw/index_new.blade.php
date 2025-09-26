@extends('backend.layouts.app_new')
@section('main')
@section('title', 'Withdrawal')
<!-- Content -->
<div class="container-fluid">
    <!-- Page header -->
    <div class="row align-items-center mb-4">
        <!-- Breadcrumb and Title -->
        <div class="col-md-6 mb-3 mb-md-0">
            <h2 class="fw-bold mb-1">Withdrawal Request</h2>
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="#" class="text-muted">
                        <i class="ti ti-file-invoice me-1"></i> Keuangan
                    </a>
                </li>
                <li class="breadcrumb-item active">Withdraw</li>
            </ul>
        </div>
    </div>

    <!-- Table Section -->
    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table id="myTable" class="table table-hover table-striped table-bordered nowrap w-100">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Tgl/Waktu</th>
                        <th>ID Penarikan</th>
                        <th>Nominal</th>
                        <th>Nomor Rekening</th>
                        <th>Atas Nama</th>
                        <th>Status</th>
                        <th>Created By</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function() {
        const delay = 1000;
        const elements = document.querySelectorAll('[data-value]');
        elements.forEach(function(el) {
            setTimeout(function() {
                el.innerHTML = el.getAttribute('data-value');
            }, delay);
        });
    });

    let table = $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        order: [[1, 'desc']],
        ajax: '/withdraw',
        columns: [
            {
                data: null,
                sortable: false,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: 'tanggal',
                name: 'tanggal',
                render: function(data) {
                    return moment(data).local().format('DD MMM YYYY HH:mm:ss');
                },
            },
            { data: 'id_penarikan', name: 'id_penarikan' },
            {
                data: 'nominal',
                name: 'nominal',
                render: $.fn.dataTable.render.number('.', ',', 0, ''),
            },
            { data: 'nomor_rekening', name: 'nomor_rekening' },
            { data: 'atas_nama', name: 'atas_nama' },
            {
                data: 'status',
                name: 'status',
                render: function(data) {
                    if (data == 1) {
                        return '<span class="badge bg-success">Success</span>';
                    } else if (data == 0) {
                        return '<span class="badge bg-warning text-dark">Pending</span>';
                    } else {
                        return '<span class="badge bg-secondary">Unknown</span>';
                    }
                }
            },
            { data: 'shortname', name: 'shortname' },
            { data: 'action', name: 'action' },
        ]
    });

    $('#myTable').on('click', '#delete', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: "Apakah anda yakin?",
            icon: 'warning',
            text: "Data yang sudah dihapus tidak dapat dikembalikan",
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: "Ya, Hapus",
            cancelButtonText: "Batal",
            confirmButtonColor: "#d33",
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: `/withdraw/${id}`,
                    type: "POST",
                    data: { _method: "DELETE" },
                    dataType: "json",
                    success: function(data) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: `${data.message}`,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        setTimeout(function() {
                            table.ajax.reload();
                        }, 1500);
                    },
                    error: function() {
                        $("#message").html("Some Error Occurred!");
                    }
                });
            }
        });
    });

    $('#myTable').on('click', '#pay', function() {
        let id = $(this).data('id');
        let id_penarikan = $(this).data('id_penarikan');
        let shortname = $(this).data('shortname');

        Swal.fire({
            title: "Konfirmasi Withdraw",
            icon: 'warning',
            text: `${id_penarikan} a.n ${shortname}`,
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: "Ya, Pay",
            cancelButtonText: "Batal",
            confirmButtonColor: "#d33",
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: `/withdraw/penarikan/pay`,
                    type: "POST",
                    data: {
                        id: id,
                        shortname: shortname
                    },
                    dataType: "json",
                    success: function(data) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: `${data.message}`,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        setTimeout(function() {
                            table.ajax.reload();
                        }, 1500);
                    },
                    error: function() {
                        $("#message").html("Some Error Occurred!");
                    }
                });
            }
        });
    });
</script>
@endpush
