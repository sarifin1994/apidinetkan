@extends('backend.layouts.app')
@section('main')
@section('title', 'Transaksi Keuangan')
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
                    <li class="breadcrumb-item active" aria-current="page">Transaksi</li>
                </ol>
            </nav>
            <!-- Heading -->
            <h1 class="fs-4 mb-0">Transaksi</h1>
        </div>
        <div class="col-12 col-sm-auto mt-4 mt-sm-0">
            <!-- Action -->
            @include('backend.keuangan.transaksi.modal.create_income')
            @include('backend.keuangan.transaksi.modal.create_expense')
            @include('backend.keuangan.transaksi.modal.edit_income')
            @include('backend.keuangan.transaksi.modal.edit_expense')
            @include('backend.keuangan.transaksi.modal.export')

            <div class="btn-group">
                <button type="button" class="btn btn-primary me-2 mb-2 dropdown-toggle" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <span class="material-symbols-outlined">add</span> Create<span
                        class="row-count badge bg-dark text-light ms-1"></span>
                </button>
                <ul class="dropdown-menu">
                    <li><button class="dropdown-item" data-bs-toggle="modal"
                            data-bs-target="#create_income">Pemasukan</button></li>
                    <li><button class="dropdown-item" data-bs-toggle="modal"
                            data-bs-target="#create_expense">Pengeluaran</button></li>
                </ul>
            </div>
            <button class="btn btn-success mb-2 text-white" data-bs-toggle="modal" data-bs-target="#export">
                <span class="material-symbols-outlined me-1">file_export</span> Export
            </button>
            {{-- <button class="btn btn-warning me-2 mb-2" data-bs-toggle="modal" data-bs-target="#import">
                <span class="material-symbols-outlined me-1">file_save</span> Import
            </button> --}}
        </div>
    </div>
    @if (multi_auth()->role === 'Admin' ||
            (multi_auth()->role === 'Kasir' &&
                optional(\App\Models\Setting\Role::where('shortname', multi_auth()->shortname)->first())->kasir_melihat_total_keuangan === 1))
        <div class="row mb-5">
            <!-- Kartu Balance (Saldo Terkini) -->
            <div class="col-12 col-md-7 col-xxl-4 mb-4 mb-md-0">
                <div class="card bg-body-tertiary border-transparent">
                    <div class="card-body px-6 py-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <!-- Heading -->
                                <h4 class="fs-sm fw-normal text-body-secondary mb-1">
                                    SALDO TERKINI
                                </h4>
                                <!-- Text dengan spinner sementara -->
                                <div class="fs-lg fw-semibold" id="balance"
                                    data-value="Rp{{ number_format($totalBalance, 0, '.', '.') }}">
                                    <span class="material-symbols-outlined spinner">progress_activity</span>
                                </div>
                                <div class="fs-md">
                                    <small>Cash
                                        <span class="text-primary fw-semibold" id="totalCash"
                                            data-value="Rp{{ number_format($totalCash, 0, '.', '.') }}">
                                            <span class="material-symbols-outlined spinner">progress_activity</span>
                                        </span>
                                    </small>
                                    <small>Transfer
                                        <span class="text-warning fw-semibold" id="totalTransfer"
                                            data-value="Rp{{ number_format($totalTransfer, 0, '.', '.') }}">
                                            <span class="material-symbols-outlined spinner">progress_activity</span>
                                        </span>
                                    </small>
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

            <!-- Kartu Pemasukan (Bulan Ini) -->
            <div class="col-12 col-md-7 col-xxl-4 mb-4 mb-md-0">
                <div class="card bg-body-tertiary border-transparent">
                    <div class="card-body px-6 py-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <!-- Heading -->
                                <h4 class="fs-sm fw-normal text-body-secondary mb-1">
                                    PEMASUKAN BULAN INI
                                </h4>
                                <!-- Text dengan spinner sementara -->
                                <div class="fs-lg fw-semibold text-success" id="incomeMonth"
                                    data-value="Rp{{ number_format($incomeMonth, 0, '.', '.') }}">
                                    <span class="material-symbols-outlined spinner">progress_activity</span>
                                </div>
                                <div class="fs-md">
                                    <small>Hari Ini
                                        <span class="text-success fw-semibold" id="incomeDay"
                                            data-value="Rp{{ number_format($incomeDay, 0, '.', '.') }}">
                                            <span class="material-symbols-outlined spinner">progress_activity</span>
                                        </span>
                                    </small>
                                </div>
                            </div>
                            <div class="col-auto">
                                <!-- Avatar -->
                                <div class="avatar avatar-lg bg-body text-primary">
                                    <i class="fs-4" data-duoicon="slideshow"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kartu Pengeluaran (Bulan Ini) -->
            <div class="col-12 col-md-7 col-xxl-4 mb-4 mb-md-0">
                <div class="card bg-body-tertiary border-transparent">
                    <div class="card-body px-6 py-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <!-- Heading -->
                                <h4 class="fs-sm fw-normal text-body-secondary mb-1">
                                    PENGELUARAN BULAN INI
                                </h4>
                                <!-- Text dengan spinner sementara -->
                                <div class="fs-lg fw-semibold text-danger" id="expenseMonth"
                                    data-value="Rp{{ number_format($expenseMonth, 0, '.', '.') }}">
                                    <span class="material-symbols-outlined spinner">progress_activity</span>
                                </div>
                                <div class="fs-md">
                                    <small>Hari Ini
                                        <span class="text-danger fw-semibold" id="expenseDay"
                                            data-value="Rp{{ number_format($expenseDay, 0, '.', '.') }}">
                                            <span class="material-symbols-outlined spinner">progress_activity</span>
                                        </span>
                                    </small>
                                </div>
                            </div>
                            <div class="col-auto">
                                <!-- Avatar -->
                                <div class="avatar avatar-lg bg-body text-primary">
                                    <i class="fs-4" data-duoicon="credit-card"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="card-body table-responsive">
            <table id="myTable" class="table table-responsive table-hover display nowrap" width="100%">
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
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script type="text/javascript">
    // Fungsi update summary values (langsung mengganti innerHTML dengan nilai data-value)
    function updateSummaryValues() {
        const elements = document.querySelectorAll('[data-value]');
        elements.forEach(function(el) {
            el.innerHTML = el.getAttribute('data-value');
        });
    }

    // Fungsi formatNumber untuk memformat angka tanpa desimal (sesuaikan bila perlu)
    function formatNumber(value) {
        return new Intl.NumberFormat('id-ID', {
            maximumFractionDigits: 0
        }).format(value);
    }

    // Update nilai summary pada saat DOM pertama kali siap
    document.addEventListener("DOMContentLoaded", function() {
        setTimeout(updateSummaryValues, 1000);
    });

    // Inisialisasi DataTable
    let table = $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        order: [1, 'desc'],
        ajax: '{{ url()->current() }}',
        columns: [{
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
                }
            },
            {
                data: 'tipe',
                name: 'tipe',
                render: function(data) {
                    if (data === 'Pemasukan') {
                        return "<span class='text-success'>Pemasukan</span>";
                    } else if (data === 'Pengeluaran') {
                        return "<span class='text-danger'>Pengeluaran</span>";
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
                render: $.fn.dataTable.render.number('.', ',', 0, '')
            },
            {
                data: 'metode',
                name: 'metode'
            },
            {
                data: 'created_by',
                name: 'created_by'
            },
            {
                data: 'action',
                name: 'action'
            }
        ]
    });

    // Saat DataTable selesai request via AJAX, update summary jika terdapat data.summary
    table.on('xhr.dt', function(e, settings, json) {
        if (json.summary) {
            $('#balance').attr('data-value', 'Rp' + formatNumber(json.summary.totalBalance));
            $('#totalCash').attr('data-value', 'Rp' + formatNumber(json.summary.totalCash));
            $('#totalTransfer').attr('data-value', 'Rp' + formatNumber(json.summary.totalTransfer));
            $('#incomeMonth').attr('data-value', 'Rp' + formatNumber(json.summary.incomeMonth));
            $('#incomeDay').attr('data-value', 'Rp' + formatNumber(json.summary.incomeDay));
            $('#expenseMonth').attr('data-value', 'Rp' + formatNumber(json.summary.expenseMonth));
            $('#expenseDay').attr('data-value', 'Rp' + formatNumber(json.summary.expenseDay));
            updateSummaryValues();
        }
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

    // STORE INCOME
    $('#store_income').click(function(e) {
        e.preventDefault();
        var error_ele = document.getElementsByClassName('alert text-sm');
        for (var i = error_ele.length - 1; i >= 0; i--) {
            error_ele[i].remove();
        }
        var btn = $(this);
        var originalText = btn.html();
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
        $.ajax({
            url: `/keuangan/transaksi`,
            type: "POST",
            cache: false,
            data: data,
            dataType: "json",
            success: function(data) {
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
                        table.ajax.reload();
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
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Gagal menyimpan data',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    });

    // STORE EXPENSE
    $('#store_expense').click(function(e) {
        e.preventDefault();
        var error_ele = document.getElementsByClassName('alert text-sm');
        for (var i = error_ele.length - 1; i >= 0; i--) {
            error_ele[i].remove();
        }
        var btn = $(this);
        var originalText = btn.html();
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
        $.ajax({
            url: `/keuangan/transaksi`,
            type: "POST",
            cache: false,
            data: data,
            dataType: "json",
            success: function(data) {
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
                        table.ajax.reload();
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
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Gagal menyimpan data',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    });


    // EDIT
    $('#myTable').on('click', '#edit', function() {
        let transaksi = $(this).data('id');
        $.ajax({
            url: `/keuangan/transaksi/${transaksi}`,
            type: "GET",
            cache: false,
            success: function(response) {
                if (response.data.tipe === 'Pemasukan') {
                    $('#id_income').val(response.data.id);
                    $('#tanggal_ci_edit').val(moment(response.data.tanggal).local().format(
                        'DD MMM YYYY HH:mm:ss'));
                    $('#kategori_ci_edit').val(response.data.kategori);
                    $('#deskripsi_ci_edit').val(response.data.deskripsi);
                    $('#nominal_ci_edit').val(formatRupiah(response.data.nominal, 2, ',', '.'));
                    $('#metode_ci_edit').val(response.data.metode);
                    $('#edit_income').modal('show');
                } else if (response.data.tipe === 'Pengeluaran') {
                    $('#id_expense').val(response.data.id);
                    $('#tanggal_ce_edit').val(moment(response.data.tanggal).local().format(
                        'DD MMM YYYY HH:mm:ss'));
                    $('#kategori_ce_edit').val(response.data.kategori);
                    $('#deskripsi_ce_edit').val(response.data.deskripsi);
                    $('#nominal_ce_edit').val(formatRupiah(response.data.nominal, 2, ',', '.'));
                    $('#metode_ce_edit').val(response.data.metode);
                    $('#edit_expense').modal('show');
                }
            }
        });
    });

    // UPDATE INCOME
    $('#update_income').click(function(e) {
        e.preventDefault();
        var error_ele = document.getElementsByClassName('form-text text-danger');
        for (var i = error_ele.length - 1; i >= 0; i--) {
            error_ele[i].remove();
        }
        let transaksi = $('#id_income').val();
        var data = {
            'kategori': $('#kategori_ci_edit').val(),
            'deskripsi': $('#deskripsi_ci_edit').val(),
            'nominal': $('#nominal_ci_edit').val(),
            'metode': $('#metode_ci_edit').val(),
        };
        var btn = $(this);
        var originalText = btn.html();
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
                        table.ajax.reload();
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

    // UPDATE EXPENSE
    $('#update_expense').click(function(e) {
        e.preventDefault();
        var error_ele = document.getElementsByClassName('form-text text-danger');
        for (var i = error_ele.length - 1; i >= 0; i--) {
            error_ele[i].remove();
        }
        let transaksi = $('#id_expense').val();
        var data = {
            'kategori': $('#kategori_ce_edit').val(),
            'deskripsi': $('#deskripsi_ce_edit').val(),
            'nominal': $('#nominal_ce_edit').val(),
            'metode': $('#metode_ce_edit').val(),
        };
        var btn = $(this);
        var originalText = btn.html();
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
                        table.ajax.reload();
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

    // DELETE
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
                    url: `/keuangan/transaksi/${id}`,
                    type: "POST",
                    cache: false,
                    data: {
                        _method: "DELETE"
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
                    error: function(err) {
                        $("#message").html("Some Error Occurred!");
                    }
                });
            }
        });
    });

    /* Format Rupiah untuk input */
    var nominal_ce = document.getElementById('nominal_ce');
    nominal_ce.addEventListener('keyup', function() {
        nominal_ce.value = formatRupiah(this.value);
    });
    var nominal_ce_edit = document.getElementById('nominal_ce_edit');
    nominal_ce_edit.addEventListener('keyup', function() {
        nominal_ce_edit.value = formatRupiah(this.value);
    });
    var nominal_ci = document.getElementById('nominal_ci');
    nominal_ci.addEventListener('keyup', function() {
        nominal_ci.value = formatRupiah(this.value);
    });
    var nominal_ci_edit = document.getElementById('nominal_ci_edit');
    nominal_ci_edit.addEventListener('keyup', function() {
        nominal_ci_edit.value = formatRupiah(this.value);
    });

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

    $('#create_income').on('shown.bs.modal', function() {
        document.getElementById('tanggal_ci').valueAsDate = new Date();
    });
    $('#create_expense').on('shown.bs.modal', function() {
        document.getElementById('tanggal_ce').valueAsDate = new Date();
    });
    $("#periode").datepicker({
        format: "MM-yyyy",
        viewMode: "months",
        minViewMode: "months"
    });

    // Jika menggunakan AJAX untuk download file
    $(document).on('submit', '#exportForm', function(e) {
        e.preventDefault();
        var btn = $('#exportBtn');
        btn.prop('disabled', true).html(
            '<span class="material-symbols-outlined spinner">progress_activity</span> Exporting...');
        var form = $(this);

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            xhrFields: {
                responseType: 'blob'
            },
            success: function(response, status, xhr) {
                // Mengambil nama file dari header respons (jika tersedia)
                var filename = "";
                var disposition = xhr.getResponseHeader('Content-Disposition');
                if (disposition && disposition.indexOf('attachment') !== -1) {
                    var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                    var matches = filenameRegex.exec(disposition);
                    if (matches !== null && matches[1]) filename = matches[1].replace(/['"]/g, '');
                }
                var blob = new Blob([response], {
                    type: xhr.getResponseHeader('Content-Type')
                });
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = filename || "exported_data";
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Gagal menyimpan data',
                    showConfirmButton: false,
                    timer: 1500
                });
            },
            complete: function() {
                // Mengembalikan tombol ke keadaan awal
                btn.prop('disabled', false);
                btn.html('Export data');
            }
        });
    });
</script>
@endpush
