@extends('backend.layouts.app')
@section('main')
@section('title', 'Withdrawal')
<!-- Content -->
<div class="container-lg">
    <!-- Page header -->
    <div class="row align-items-center mb-7">
        <div class="col-auto">
            <!-- Avatar -->
            <div class="avatar avatar-xl rounded text-primary">
                <span class="fs-2 material-symbols-outlined">
                    account_balance
                </span>
            </div>
        </div>
        <div class="col">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a class="text-body-secondary" href="#">Keuangan</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Withdraw</li>
                </ol>
            </nav>

            <!-- Heading -->
            <h1 class="fs-4 mb-0">Withdrawal Request</h1>
        </div>
        <div class="col-12 col-sm-auto mt-4 mt-sm-0">
        </div>
    </div>



    <div class="row">
        <div class="card-body table-responsive">
            <table id="myTable" class="table table-hover display nowrap" width="100%">
                <thead>
                    <tr>
                        <th style="text-align:left!important">#</th>
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



@endsection

@push('scripts')
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            const delay = 1000; // Delay 1,5 detik
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
            order: [
                1, 'desc'
            ],
            ajax: '/withdraw',
            columns: [{
                    data: null,
                    'sortable': false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'tanggal',
                    name: 'tanggal',
                    render: function(data, type, row, meta) {
                        return moment(data).local().format('DD MMM YYYY HH:mm:ss');
                    },
                },
                {
                    data: 'id_penarikan',
                    name: 'id_penarikan'
                },
                {
                    data: 'nominal',
                    name: 'nominal',
                    render: $.fn.dataTable.render.number('.', ',', 0, ''),
                },
                {
                    data: 'nomor_rekening',
                    name: 'nomor_rekening'
                },
                {
                    data: 'atas_nama',
                    name: 'atas_nama'
                },
                {
                    data: 'status',
                    name: 'status',
                    render: function(data, type, row) {
                        if (data == 1) {
                            return '<span class="badge bg-success">Success</span>';
                        } else if (data == 0) {
                            return '<span class="badge bg-warning text-dark">Pending</span>';
                        } else {
                            return '<span class="badge bg-secondary">Unknown</span>';
                        }
                    }
                },
                {
                    data: 'shortname',
                    name: 'shortname'
                },
                {
                    data: 'action',
                    name: 'action'
                },

            ]
        });

        $('#myTable').on('click', '#delete', function() {

            let id = $(this).data('id');

            Swal.fire({
                title: "Apakah anda yakin?",
                icon: 'warning',
                text: "Data yang sudah dihapus tidak dapat dikembalikan",
                showCancelButton: !0,
                reverseButtons: !0,
                confirmButtonText: "Ya, Hapus",
                cancelButtonText: "Batal",
                confirmButtonColor: "#d33",
                // cancelButtonColor: "#d33",
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $(
                                    'meta[name="csrf-token"]'
                                )
                                .attr('content')
                        }
                    });

                    $.ajax({
                        url: `/withdraw/${id}`,
                        type: "POST",
                        cache: false,
                        data: {
                            _method: "DELETE"
                        },
                        dataType: "json",

                        // tampilkan pesan Success
                        success: function(data) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: `${data.message}`,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            setTimeout(
                                function() {
                                    table.ajax.reload()
                                }, 1500);
                        },

                        error: function(err) {
                            $("#message").html(
                                "Some Error Occurred!"
                            )
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
                text: ""+id_penarikan+ " a.n " +shortname+"",
                showCancelButton: !0,
                reverseButtons: !0,
                confirmButtonText: "Ya, Pay",
                cancelButtonText: "Batal",
                confirmButtonColor: "#d33",
                // cancelButtonColor: "#d33",
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $(
                                    'meta[name="csrf-token"]'
                                )
                                .attr('content')
                        }
                    });

                    $.ajax({
                        url: `/withdraw/penarikan/pay`,
                        type: "POST",
                        cache: false,
                        data:{
                            id:id,
                            shortname:shortname
                        },
                        dataType: "json",

                        // tampilkan pesan Success
                        success: function(data) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: `${data.message}`,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            setTimeout(
                                function() {
                                    table.ajax.reload()
                                }, 1500);
                        },

                        error: function(err) {
                            $("#message").html(
                                "Some Error Occurred!"
                            )
                        }

                    });
                }
            });
        });
    </script>
@endpush
