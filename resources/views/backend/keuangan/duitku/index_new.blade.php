@extends('backend.layouts.app_new')
@section('main')
@section('title', 'Transaksi Duitku')
<!-- Content -->
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h2 class="page-title">Transaksi Duitku</h2>
            <ol class="breadcrumb" aria-label="breadcrumbs">
                <li class="breadcrumb-item">
                    <a href="#"><i class="ti ti-cash"></i> Keuangan</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Duitku</li>
            </ol>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            @include('backend.keuangan.midtrans.modal.withdraw')
        </div>
    </div>

    @if (multi_auth()->role === 'Admin' ||
            (multi_auth()->role === 'Kasir' &&
                optional(\App\Models\Setting\Role::where('shortname', multi_auth()->shortname)->first())->kasir_melihat_total_keuangan === 1))
        <div class="row g-3 mb-4">
            <!-- Saldo Cards -->
            @php
                $cards = [
                    ['title' => 'SALDO TERKINI', 'id' => 'totalSaldo', 'value' => $totalSaldo],
                    ['title' => 'THIS MONTH', 'id' => 'incomeMonth', 'value' => $incomeMonth],
                    ['title' => 'LAST MONTH', 'id' => 'incomeLastMonth', 'value' => $incomeLastMonth],
                    ['title' => 'THIS YEAR', 'id' => 'incomeYear', 'value' => $incomeYear],
                ];
            @endphp

            @foreach ($cards as $card)
                <div class="col-12 col-md-6 col-xxl-3">
                    <div class="card">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="text-muted mb-1">{{ $card['title'] }}</h4>
                                <h3 id="{{ $card['id'] }}">Rp{{ number_format($card['value'], 0, '.', '.') }}</h3>
                                @if ($card['id'] === 'totalSaldo')
                                    <small><a href="javascript:void(0)" id="pindah"
                                            data-nominal="{{ $card['value'] }}">PINDAHKAN SALDO</a></small>
                                @endif
                            </div>
                            <div class="icon icon-lg text-primary bg-light rounded-circle">
                                <i class="ti ti-wallet"></i>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Tabel Transaksi -->
    <div class="card">
        <div class="card-body table-responsive">
            <table id="myTable" class="table table-hover table-vcenter text-nowrap w-100">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tgl/Waktu</th>
                        <th>Transaksi</th>
                        <th>Kategori</th>
                        <th>Deskripsi</th>
                        <th>Nominal</th>
                        <th>Metode</th>
                        <th>Created by</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- DataTables akan load otomatis -->
                </tbody>
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

    // document.getElementById('getnominal').addEventListener('click', function() {
    //     document.getElementById('nominal_wd').value = totalSaldo;
    // });

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
                        table.ajax.reload()();
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
                        table.ajax.reload()();
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
                        table.ajax.reload()();
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
                        table.ajax.reload()();
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
                                table.ajax.reload()()
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
                    url: `/keuangan/duitku/pindah`,
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
                                table.ajax.reload()()
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
                                table.ajax.reload()()
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
