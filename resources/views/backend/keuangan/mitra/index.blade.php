@extends('backend.layouts.app')
@section('main')
@section('title', 'Transaksi Mitra')
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
                    <li class="breadcrumb-item active" aria-current="page">Mitra</li>
                </ol>
            </nav>

            <!-- Heading -->
            <h1 class="fs-4 mb-0">Transaksi Mitra</h1>
        </div>
        <div class="col-12 col-sm-auto mt-4 mt-sm-0">

            {{-- <button class="btn btn-success mb-2 text-white" data-bs-toggle="modal" data-bs-target="#export"> <span
                    class="material-symbols-outlined me-1">file_export</span> Export </button> --}}
            {{-- <button class="btn btn-warning me-2 mb-2" data-bs-toggle="modal" data-bs-target="#import"> <span
                    class="material-symbols-outlined me-1">file_save</span> Import </button> --}}

        </div>
    </div>
        <div class="row mb-5">
            @if(multi_auth()->role !== 'Mitra')
             <!-- Kartu Balance (Saldo Terkini) -->
             <div class="col-12 col-md-4 col-xxl-3 mb-4 mb-md-0">
                <div class="card bg-body-tertiary border-transparent">
                    <div class="card-body px-6 py-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <!-- Heading -->
                                <h4 class="fs-sm fw-normal text-body-secondary mb-1">
                                    TOTAL MITRA
                                    
                                </h4>
                                <!-- Text dengan spinner sementara -->
                                <div class="fs-lg fw-semibold" id="totalmitra"
                                    data-value="{{$totalmitra}}">
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
            @endif

            <!-- Kartu Balance (Saldo Terkini) -->
            <div class="col-12 col-md-4 col-xxl-3 mb-4 mb-md-0">
                <div class="card bg-body-tertiary border-transparent">
                    <div class="card-body px-6 py-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <!-- Heading -->
                                <h4 class="fs-sm fw-normal text-body-secondary mb-1">
                                    KOMISI BULAN INI
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
                                    KOMISI BULAN LALU
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
                                    KOMISI TAHUN INI
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



    <div class="row">
        <div class="card-body table-responsive">
            <table id="myTable" class="table table-responsive table-hover display nowrap" width="100%">
                <thead>
                    <tr>
                        <th style="text-align:left!important">#</th>
                        <th>Tgl/Waktu</th>
                        <th>Nama Mitra</th>
                        {{-- <th>Transaksi</th> --}}
                        <th>Kategori</th>
                        <th>Deskripsi</th>
                        <th>Nominal</th>
                        <th>Metode</th>
                        <th>Created by</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
    <hr>

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
                    data: 'nama_mitra',
                    name: 'nama_mitra',
                },
               
                // {
                //     data: 'tipe',
                //     name: 'tipe',
                //     render: function(data, type, row) {
                //         if (data === 'Pemasukan') {
                //             return "<span class='text-success'>Pemasukan</span>";
                //         } else if (data === 'Pengeluaran') {
                //             return "<span class='text-danger'>Pengeluaran</span>"
                //         }
                //     }
                // },
                
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
                {
                    data: 'action',
                    name: 'action',
                },
            ]
        });

        table.on('preXhr.dt', function(e, settings, data) {
            data.start_date = $('#mindate').val();
            data.end_date = $('#maxdate').val();
        });

        $('#maxdate').change(function() {
            table.ajax.reload();
            return false;
        });

        $('#filter_type').change(function() {
            var any_string = $(this).val().toString();
            table.column($(this).data('column'))
                .search(any_string, true, false)
                .draw();
        });

        $('#filter_kategori_income').change(function() {
            table.column($(this).data('column'))
                .search($(this).val())
                .draw();
        });
        $('#filter_kategori_expense').change(function() {
            table.column($(this).data('column'))
                .search($(this).val())
                .draw();
        });

        $('#filter_type').change(function() {
            var selectedValue = $(this).val();
            if (selectedValue === 'Pemasukan') {
                $('#income').show();
                $('#expense').hide();
            } else if (selectedValue === 'Pengeluaran') {
                $('#income').hide();
                $('#expense').show();
            } else {
                $('#income').hide();
                $('#expense').hide();
            }
        });

        $('#store_income').click(function(e) {
            e.preventDefault();

            // Hapus pesan error jika ada
            var error_ele = document.getElementsByClassName('alert text-sm');
            if (error_ele.length > 0) {
                for (var i = error_ele.length - 1; i >= 0; i--) {
                    error_ele[i].remove();
                }
            }

            // Simpan referensi tombol dan teks aslinya
            var btn = $(this);
            var originalText = btn.html();

            // Ubah tampilan tombol: nonaktifkan tombol dan tampilkan spinner progress activity
            btn.prop('disabled', true).html(
                '<span class="material-symbols-outlined spinner">progress_activity</span> Processing...');

            var data = {
                'tanggal': $('#tanggal_ci').val(),
                'tipe': 'Pemasukan',
                'kategori': $('#kategori_ci').val(),
                'deskripsi': $('#deskripsi_ci').val(),
                'nominal': $('#nominal_ci').val(),
                'metode': $('#metode_ci').val(),
            };

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Proses AJAX
            $.ajax({
                url: `/keuangan/transaksi`,
                type: "POST",
                cache: false,
                data: data,
                dataType: "json",
                success: function(data) {
                    // Kembalikan tombol ke kondisi semula
                    btn.prop('disabled', false).html(originalText);
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: `${data.message}`,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        setTimeout(function() {
                            $('#create_income').modal('hide');
                            location.reload();
                        }, 1500);
                    } else {
                        $.each(data.error, function(key, value) {
                            var el = $(document).find('[name="' + key + '"]');
                            el.after($('<span class="alert text-sm text-danger">' + value[0] +
                                '</span>'));
                        });
                    }
                },
                error: function(err) {
                    btn.prop('disabled', false).html(originalText);
                    $("#message").html("Some Error Occurred!");
                }
            });
        });


        $('#store_expense').click(function(e) {
            e.preventDefault();

            // Hapus pesan error jika ada
            var error_ele = document.getElementsByClassName('alert text-sm');
            if (error_ele.length > 0) {
                for (var i = error_ele.length - 1; i >= 0; i--) {
                    error_ele[i].remove();
                }
            }

            // Simpan referensi tombol dan teks aslinya
            var btn = $(this);
            var originalText = btn.html();

            // Ubah tampilan tombol: nonaktifkan tombol dan tampilkan spinner progress activity
            btn.prop('disabled', true).html(
                '<span class="material-symbols-outlined spinner">progress_activity</span> Memproses...');

            var data = {
                'tanggal': $('#tanggal_ce').val(),
                'tipe': 'Pengeluaran',
                'kategori': $('#kategori_ce').val(),
                'deskripsi': $('#deskripsi_ce').val(),
                'nominal': $('#nominal_ce').val(),
                'metode': $('#metode_ce').val(),
            };

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Proses AJAX
            $.ajax({
                url: `/keuangan/transaksi`,
                type: "POST",
                cache: false,
                data: data,
                dataType: "json",
                success: function(data) {
                    // Kembalikan tombol ke kondisi semula
                    btn.prop('disabled', false).html(originalText);
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: `${data.message}`,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        setTimeout(function() {
                            $('#create_expense').modal('hide');
                            location.reload();
                        }, 1500);
                    } else {
                        $.each(data.error, function(key, value) {
                            var el = $(document).find('[name="' + key + '"]');
                            el.after($('<span class="alert text-sm text-danger">' + value[0] +
                                '</span>'));
                        });
                    }
                },
                error: function(err) {
                    btn.prop('disabled', false).html(originalText);
                    $("#message").html("Some Error Occurred!");
                }
            });
        });


        $('#myTable').on('click', '#edit', function() {

            let transaksi = $(this).data('id');

            //fetch detail post with ajax
            $.ajax({
                url: `/keuangan/transaksi/${transaksi}`,
                type: "GET",
                cache: false,
                success: function(response) {
                    if (response.data.tipe === 'Pemasukan') {
                        $('#id_income').val(response.data.id),
                            $('#tanggal_ci_edit').val(moment(response.data.tanggal).local().format(
                                'DD MMM YYYY HH:mm:ss')),
                            $('#kategori_ci_edit').val(response.data.kategori),
                            $('#deskripsi_ci_edit').val(response.data.deskripsi),
                            $('#nominal_ci_edit').val(formatRupiah(response.data.nominal, 2, ',', '.')),
                            $('#metode_ci_edit').val(response.data.metode),
                            $('#edit_income').modal('show');
                    } else if (response.data.tipe === 'Pengeluaran') {
                        $('#id_expense').val(response.data.id),
                            $('#tanggal_ce_edit').val(moment(response.data.tanggal).local().format(
                                'DD MMM YYYY HH:mm:ss')),
                            $('#kategori_ce_edit').val(response.data.kategori),
                            $('#deskripsi_ce_edit').val(response.data.deskripsi),
                            $('#nominal_ce_edit').val(formatRupiah(response.data.nominal, 2, ',', '.')),
                            $('#metode_ce_edit').val(response.data.metode),
                            $('#edit_expense').modal('show');
                    }

                }
            });
        });

        $('#update_income').click(function(e) {
            e.preventDefault();

            // Hapus pesan error jika ada
            var error_ele = document.getElementsByClassName('form-text text-danger');
            if (error_ele.length > 0) {
                for (var i = error_ele.length - 1; i >= 0; i--) {
                    error_ele[i].remove();
                }
            }
            let transaksi = $('#id_income').val();

            // Kumpulkan data berdasarkan id
            var data = {
                'kategori': $('#kategori_ci_edit').val(),
                'deskripsi': $('#deskripsi_ci_edit').val(),
                'nominal': $('#nominal_ci_edit').val(),
                'metode': $('#metode_ci_edit').val(),
            };

            // Simpan referensi tombol dan teks aslinya
            var btn = $(this);
            var originalText = btn.html();
            // Ubah tampilan tombol: nonaktifkan dan tampilkan spinner progress activity
            btn.prop('disabled', true).html(
                '<span class="material-symbols-outlined spinner">progress_activity</span> Memproses...');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: `/keuangan/transaksi/${transaksi}`,
                type: "PUT",
                cache: false,
                data: data,
                dataType: "json",
                success: function(data) {
                    // Kembalikan tombol ke kondisi semula
                    btn.prop('disabled', false).html(originalText);
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: `${data.message}`,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        setTimeout(function() {
                            $('#edit_income').modal('hide');
                            location.reload();
                        }, 1500);
                    } else {
                        $.each(data.error, function(key, value) {
                            var el = $(document).find('[name="' + key + '"]');
                            el.after($('<div class="form-text text-danger">' + value[0] +
                                '</div>'));
                        });
                    }
                },
                error: function(err) {
                    btn.prop('disabled', false).html(originalText);
                    $("#message").html("Some Error Occurred!");
                }
            });
        });

        $('#update_expense').click(function(e) {
            e.preventDefault();

            // Hapus pesan error jika ada
            var error_ele = document.getElementsByClassName('form-text text-danger');
            if (error_ele.length > 0) {
                for (var i = error_ele.length - 1; i >= 0; i--) {
                    error_ele[i].remove();
                }
            }
            let transaksi = $('#id_expense').val();

            // Kumpulkan data berdasarkan id
            var data = {
                'kategori': $('#kategori_ce_edit').val(),
                'deskripsi': $('#deskripsi_ce_edit').val(),
                'nominal': $('#nominal_ce_edit').val(),
                'metode': $('#metode_ce_edit').val(),
            };

            // Simpan referensi tombol dan teks aslinya
            var btn = $(this);
            var originalText = btn.html();
            // Ubah tampilan tombol: nonaktifkan dan tampilkan spinner progress activity
            btn.prop('disabled', true).html(
                '<span class="material-symbols-outlined spinner">progress_activity</span> Memproses...');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: `/keuangan/transaksi/${transaksi}`,
                type: "PUT",
                cache: false,
                data: data,
                dataType: "json",
                success: function(data) {
                    // Kembalikan tombol ke kondisi semula
                    btn.prop('disabled', false).html(originalText);
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: `${data.message}`,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        setTimeout(function() {
                            $('#edit_expense').modal('hide');
                            location.reload();
                        }, 1500);
                    } else {
                        $.each(data.error, function(key, value) {
                            var el = $(document).find('[name="' + key + '"]');
                            el.after($('<div class="form-text text-danger">' + value[0] +
                                '</div>'));
                        });
                    }
                },
                error: function(err) {
                    btn.prop('disabled', false).html(originalText);
                    $("#message").html("Some Error Occurred!");
                }
            });
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
                        url: `/keuangan/mitra/${id}`,
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
            let nominal =  $(this).data('nominal');
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

        /* Tanpa Rupiah */
        var nominal_ce = document.getElementById('nominal_ce');
        nominal_ce.addEventListener('keyup', function(e) {
            nominal_ce.value = formatRupiah(this.value);
        });

        /* Tanpa Rupiah */
        var nominal_ce_edit = document.getElementById('nominal_ce_edit');
        nominal_ce_edit.addEventListener('keyup', function(e) {
            nominal_ce_edit.value = formatRupiah(this.value);
        });

        /* Tanpa Rupiah */
        var nominal_ci = document.getElementById('nominal_ci');
        nominal_ci.addEventListener('keyup', function(e) {
            nominal_ci.value = formatRupiah(this.value);
        });

        /* Tanpa Rupiah */
        var nominal_ci_edit = document.getElementById('nominal_ci_edit');
        nominal_ci_edit.addEventListener('keyup', function(e) {
            nominal_ci_edit.value = formatRupiah(this.value);
        });

        /* Fungsi */
        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? '' + rupiah : '');
        }

        $('#create_income').on('shown.bs.modal', function(e) {
            document.getElementById('tanggal_ci').valueAsDate = new Date();
        })
        $('#create_expense').on('shown.bs.modal', function(e) {
            document.getElementById('tanggal_ce').valueAsDate = new Date();
        })
        $("#periode").datepicker({
            format: "MM-yyyy",
            viewMode: "months",
            minViewMode: "months"
        });
    </script>
@endpush
