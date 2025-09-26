@extends('backend.layouts.app')
@section('main')
@section('title', 'Transaksi Midtrans')
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
                    <li class="breadcrumb-item active" aria-current="page">Midtrans</li>
                </ol>
            </nav>

            <!-- Heading -->
            <h1 class="fs-4 mb-0">Transaksi Midtrans</h1>
        </div>
        <div class="col-12 col-sm-auto mt-4 mt-sm-0">
            @include('backend.keuangan.midtrans.modal.withdraw')
            <!-- Action -->


            {{-- <button class="btn btn-success mb-2 text-white" data-bs-toggle="modal" data-bs-target="#export"> <span
                    class="material-symbols-outlined me-1">file_export</span> Export </button> --}}
            {{-- <button class="btn btn-warning me-2 mb-2" data-bs-toggle="modal" data-bs-target="#import"> <span
                    class="material-symbols-outlined me-1">file_save</span> Import </button> --}}

        </div>
    </div>
    @if (multi_auth()->role === 'Admin' ||
            (multi_auth()->role === 'Kasir' &&
                optional(\App\Models\Setting\Role::where('shortname', multi_auth()->shortname)->first())->kasir_melihat_total_keuangan === 1))

        <div class="row mb-5">

            <!-- Kartu Balance (Saldo Terkini) -->
            <div class="col-12 col-md-4 col-xxl-3 mb-4 mb-md-0">
                <div class="card bg-body-tertiary border-transparent">
                    <div class="card-body px-6 py-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <!-- Heading -->
                                <h4 class="fs-sm fw-normal text-body-secondary mb-1">
                                    SALDO TERKNI
                                </h4>
                                <!-- Text dengan spinner sementara -->
                                <div class="fs-lg fw-semibold" id="totalSaldo"
                                    data-value="Rp{{ number_format($totalSaldo, 0, '.', '.') }}">
                                    <span class="material-symbols-outlined spinner">progress_activity</span>
                                </div>
                                {{-- <small><a href="javascript:void(0)" data-bs-toggle="modal"
                                        data-bs-target="#withdraw">Tarik Saldo</a></small> --}}
                                @if ($midtrans->id_merchant == env('MERCHANT_MIDTRANS'))
                                    <small><a href="javascript:void(0)" data-bs-toggle="modal"
                                            data-bs-target="#withdraw">TARIK SALDO</a></small>
                                @else
                                    <small><a href="javascript:void(0)" id="pindah"
                                            data-nominal="{{ $totalSaldo }}">PINDAHKAN SALDO</a></small>
                                @endif
                            </div>
                            <div class="col-auto">
                                <!-- Avatar -->
                                <div class="avatar avatar-lg bg-body text-primary">
                                    <i class="fs-4" data-duoicon="discount"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kartu Balance (Saldo Terkini) -->
            <div class="col-12 col-md-4 col-xxl-3 mb-4 mb-md-0">
                <div class="card bg-body-tertiary border-transparent">
                    <div class="card-body px-6 py-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <!-- Heading -->
                                <h4 class="fs-sm fw-normal text-body-secondary mb-1">
                                    THIS MONTH
                                </h4>
                                <!-- Text dengan spinner sementara -->
                                <div class="fs-lg fw-semibold" id="incomeMonth"
                                    data-value="Rp{{ number_format($incomeMonth, 0, '.', '.') }}">
                                    <span class="material-symbols-outlined spinner">progress_activity</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <!-- Avatar -->
                                <div class="avatar avatar-lg bg-body text-primary">
                                    <i class="fs-4" data-duoicon="discount"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kartu Balance (Saldo Terkini) -->
            <div class="col-12 col-md-4 col-xxl-3 mb-4 mb-md-0">
                <div class="card bg-body-tertiary border-transparent">
                    <div class="card-body px-6 py-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <!-- Heading -->
                                <h4 class="fs-sm fw-normal text-body-secondary mb-1">
                                    LAST MONTH
                                </h4>
                                <!-- Text dengan spinner sementara -->
                                <div class="fs-lg fw-semibold" id="incomeLastMonth"
                                    data-value="Rp{{ number_format($incomeLastMonth, 0, '.', '.') }}">
                                    <span class="material-symbols-outlined spinner">progress_activity</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <!-- Avatar -->
                                <div class="avatar avatar-lg bg-body text-primary">
                                    <i class="fs-4" data-duoicon="discount"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kartu Balance (Saldo Terkini) -->
            <div class="col-12 col-md-4 col-xxl-3 mb-4 mb-md-0">
                <div class="card bg-body-tertiary border-transparent">
                    <div class="card-body px-6 py-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <!-- Heading -->
                                <h4 class="fs-sm fw-normal text-body-secondary mb-1">
                                    THIS YEAR
                                </h4>
                                <!-- Text dengan spinner sementara -->
                                <div class="fs-lg fw-semibold" id="incomeYear"
                                    data-value="Rp{{ number_format($incomeYear, 0, '.', '.') }}">
                                    <span class="material-symbols-outlined spinner">progress_activity</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <!-- Avatar -->
                                <div class="avatar avatar-lg bg-body text-primary">
                                    <i class="fs-4" data-duoicon="discount"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    @endif
    <ul class="nav nav-tabs mb-3" id="midtransTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="transaksi-tab" data-bs-toggle="tab" data-bs-target="#transaksi" type="button" role="tab" aria-controls="transaksi" aria-selected="true">
                Transaksi
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="withdraw-tab" data-bs-toggle="tab" data-bs-target="#table-withdraw" type="button" role="tab" aria-controls="withdraw" aria-selected="false">
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
