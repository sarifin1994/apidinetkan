@extends('backend.layouts.app_new')
@section('main')
@section('title', 'Transaksi Midtrans')
<!-- Content -->
<div class="container-fluid">
    <!-- Page header -->
    <div class="row align-items-center mb-7">
        <!-- Breadcrumb and Title -->
        <div class="col-md-6 mb-3 mb-md-0">
            <h4 class="main-title">Transaksi Midtrans</h4>
            <ul class="app-line-breadcrumbs mb-0">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <span><i class="ti ti-file-invoice f-s-16"></i> Keuangan</span>
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="javascript:void(0)">Midtrans</a>
                </li>
            </ul>
        </div>

        <!-- Buttons -->
        <div class="d-flex flex-wrap justify-content-md-end gap-2">
            @include('backend.keuangan.midtrans.modal.withdraw')
        </div>
    </div>
    <br />
    @if (multi_auth()->role === 'Admin' ||
            (multi_auth()->role === 'Kasir' &&
                optional(\App\Models\Setting\Role::where('shortname', multi_auth()->shortname)->first())->kasir_melihat_total_keuangan === 1))
        <div class="row g-4 mb-5">
            <!-- SALDO TERKINI -->
            <div class="col-12 col-md-6 col-xxl-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Saldo Terkini</h6>
                            <h4 class="fw-bold mb-2" id="totalSaldo"
                                data-value="Rp{{ number_format($totalSaldo, 0, '.', '.') }}">
                                <i class="ti ti-loader-2 ti-spin me-1"></i>
                            </h4>
                            @if ($midtrans->id_merchant == env('MERCHANT_MIDTRANS'))
                                <small><a href="javascript:void(0)" data-bs-toggle="modal"
                                        data-bs-target="#withdraw">Tarik Saldo</a></small>
                            @else
                                <small><a href="javascript:void(0)" id="pindah"
                                        data-nominal="{{ $totalSaldo }}">Pindahkan Saldo</a></small>
                            @endif
                        </div>
                        <div class="bg-light rounded-circle p-3 text-primary">
                            <i class="ti ti-wallet fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PEMASUKAN THIS MONTH -->
            <div class="col-12 col-md-6 col-xxl-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">This Month</h6>
                            <h4 class="fw-bold mb-0" id="incomeMonth"
                                data-value="Rp{{ number_format($incomeMonth, 0, '.', '.') }}">
                                <i class="ti ti-loader-2 ti-spin me-1"></i>
                            </h4>
                        </div>
                        <div class="bg-light rounded-circle p-3 text-success">
                            <i class="ti ti-calendar-event fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PEMASUKAN LAST MONTH -->
            <div class="col-12 col-md-6 col-xxl-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Last Month</h6>
                            <h4 class="fw-bold mb-0" id="incomeLastMonth"
                                data-value="Rp{{ number_format($incomeLastMonth, 0, '.', '.') }}">
                                <i class="ti ti-loader-2 ti-spin me-1"></i>
                            </h4>
                        </div>
                        <div class="bg-light rounded-circle p-3 text-warning">
                            <i class="ti ti-calendar-time fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PEMASUKAN THIS YEAR -->
            <div class="col-12 col-md-6 col-xxl-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">This Year</h6>
                            <h4 class="fw-bold mb-0" id="incomeYear"
                                data-value="Rp{{ number_format($incomeYear, 0, '.', '.') }}">
                                <i class="ti ti-loader-2 ti-spin me-1"></i>
                            </h4>
                        </div>
                        <div class="bg-light rounded-circle p-3 text-info">
                            <i class="ti ti-calendar fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <ul class="nav nav-tabs mb-3" id="midtransTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="transaksi-tab" data-bs-toggle="tab" data-bs-target="#transaksi"
                type="button" role="tab" aria-controls="transaksi" aria-selected="true">
                Transaksi
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="withdraw-tab" data-bs-toggle="tab" data-bs-target="#table-withdraw"
                type="button" role="tab" aria-controls="withdraw" aria-selected="false">
                Riwayat Penarikan
            </button>
        </li>
    </ul>

    <div class="tab-content" id="midtransTabsContent">
        <div class="tab-pane fade show active" id="transaksi" role="tabpanel" aria-labelledby="transaksi-tab">
            <div class="card-body table-responsive">
                <table id="myTable" class="table table-hover display nowrap" width="100%">
                    <thead>
                        <tr>
                            <th style="text-align:left!important">#</th>
                            <th>Tgl/Waktu</th>
                            <th>Transaksi</th>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                            <th>Nominal</th>
                            <th>Metode</th>
                            <th>Created by</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class="tab-pane fade" id="table-withdraw" role="tabpanel" aria-labelledby="withdraw-tab">
            <div class="card-body table-responsive">
                <table id="myTable2" class="table table-hover display nowrap" width="100%">
                    <thead>
                        <tr>
                            <th style="text-align:left!important">#</th>
                            <th>Tgl/Waktu</th>
                            <th>ID Penarikan</th>
                            <th>Nominal</th>
                            <th>Nomor Rekening</th>
                            <th>Atas Nama</th>
                            <th>Status</th>
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
            ajax: '{{ url()->current() }}',
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
                    data: 'tipe',
                    name: 'tipe',
                    render: function(data, type, row) {
                        if (data === 'Pemasukan') {
                            return "<span class='text-success'>Pemasukan</span>";
                        } else if (data === 'Pengeluaran') {
                            return "<span class='text-danger'>Pengeluaran</span>"
                        }
                    }
                },
                {
                    data: 'kategori',
                    name: 'kategori'
                },
                {
                    data: 'deskripsi',
                    name: 'deskripsi'
                },
                {
                    data: 'nominal',
                    name: 'nominal',
                    render: $.fn.dataTable.render.number('.', ',', 0, ''),
                },
                {
                    data: 'metode',
                    name: 'metode'
                },
                {
                    data: 'created_by',
                    name: 'created_by',
                },
                // {
                //     data: 'action',
                //     name: 'action',
                // },
            ]
        });

        let table2 = $('#myTable2').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            order: [
                1, 'desc'
            ],
            ajax: '/keuangan/withdraw/midtrans',
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
                }

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
                        url: `/keuangan/transaksi/${id}`,
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

        $('#pindah').on('click', function() {
            let nominal = $(this).data('nominal');
            Swal.fire({
                title: "Apakah anda yakin?",
                icon: 'warning',
                text: "Saldo akan dipindahkan ke Account Transfer",
                showCancelButton: !0,
                reverseButtons: !0,
                confirmButtonText: "Ya, Pindahkan",
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
                        url: `/keuangan/midtrans/pindah`,
                        type: "POST",
                        cache: false,
                        data: {
                            nominal: nominal
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

        $('#tarik_saldo').on('click', function() {

            Swal.fire({
                title: "Apakah anda yakin ingin melakukan withdraw?",
                icon: 'warning',
                text: "Penarikan saldo akan diproses maksimal 1 x 24 jam hari kerja",
                showCancelButton: !0,
                reverseButtons: !0,
                confirmButtonText: "Ya, Tarik saldo",
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
                        url: `/keuangan/midtrans/withdraw`,
                        type: "POST",
                        cache: false,
                        data: {
                            nominal: $('#total_saldo').val(),
                            nomor_rekening: $('#norek_wd').val(),
                            atas_nama: $('#atas_nama').val(),
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
                                    location.reload()
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
