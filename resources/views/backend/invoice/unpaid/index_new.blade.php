@extends('backend.layouts.app_new')
@section('main')
@section('title', 'Invoice Unpaid')
<!-- Content -->
<div class="container-fluid">
    <!-- Page header -->

    <div class="row align-items-center mb-7">
        <!-- Breadcrumb and Title -->
        <div class="col-md-6 mb-3 mb-md-0">
            <h4 class="main-title">Invoice Unpaid</h4>
            <ul class="app-line-breadcrumbs mb-0">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <span><i class="ti ti-file-invoice f-s-16"></i> Invoice</span>
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="javascript:void(0)">Unpaid</a>
                </li>
            </ul>
        </div>

        <!-- Buttons -->
        <div class="d-flex flex-wrap justify-content-md-end gap-2">
            @if (multi_auth()->role !== 'Mitra')
                <button class="btn btn-primary me-2 mb-2" data-bs-toggle="modal" data-bs-target="#create_invoice">
                    <i class="ti ti-plus me-1"></i>Manual</button>
            @endif
            <button class="btn btn-danger me-2 mb-2" id="payMassal" disabled>
                <i class="ti ti-file-export me-1"></i>PAY <span class="row-count badge bg-dark text-white fs-7"></span>
            </button>
            @if (multi_auth()->role !== 'Mitra')
                <button class="btn btn-success me-2 mb-2 text-white" data-bs-toggle="modal" id="printMassal"
                    data-bs-target="#print_invoice" disabled>
                    <i class="ti ti-printer me-1"></i>Print <span
                        class="row-count badge bg-dark text-white fs-7"></span>
                </button>
                <button onclick="downloadExcel()" class="btn btn-warning text-white mb-2" id="btn-export" disabled>
                    <i class="ti ti-download me-1"></i>Export <span
                        class="row-count badge bg-dark text-white fs-7"></span>
                </button>
            @endif
        </div>
    </div>

    <br />
    @include('backend.invoice.unpaid.modal.create_invoice')
    @include('backend.invoice.unpaid.modal.show_invoice')
    @include('backend.invoice.unpaid.modal.print_invoice')
    <!-- Page content -->
    <div class="row mb-4">
        @if (session('error'))
            <div class="alert alert-light-border-danger d-flex align-items-center justify-content-between"
                role="alert">
                <p class="mb-0">
                    <i class="ti ti-x f-s-18 me-2"></i>{{ session('error') }}
                </p>
                <i class="ti ti-x" data-bs-dismiss="alert"></i>
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-light-border-primary d-flex align-items-center justify-content-between"
                role="alert">
                <p class="mb-0">
                    <i class="ti ti-check f-s-18 me-2"></i>{{ session('success') }}
                </p>
                <i class="ti ti-x" data-bs-dismiss="alert"></i>
            </div>
        @endif
        <div class="row">
            <!-- Card 1: User Total -->
            <div class="col-12 col-md-6 col-xxl-3 mb-4">
                <div class="card text-center h-100">
                    <span class="bg-primary h-50 w-50 d-flex-center rounded-circle m-auto eshop-icon-box">
                        <i class="ti ti-calendar f-s-24"></i>
                    </span>
                    <div class="card-body eshop-cards">
                        <span class="ripple-effect"></span>
                        <h3 class="text-primary mb-0" id="totaluser">PERIODE INVOICE</h3>
                        @php
                            $now = \Carbon\Carbon::now()->format('F-Y');
                        @endphp
                        <input type="text" readonly class="form-select p-0 fs-8 fw-bold border" id="periode"
                            name="periode" placeholder="Pilih Periode">
                    </div>
                </div>
            </div>

            <!-- Card 2: User New -->
            <div class="col-12 col-md-6 col-xxl-3 mb-4">
                <div class="card text-center h-100">
                    <span class="bg-warning h-50 w-50 d-flex-center rounded-circle m-auto eshop-icon-box">
                        <i class="ti ti-file-invoice f-s-24"></i>
                    </span>
                    <div class="card-body eshop-cards">
                        <span class="ripple-effect"></span>
                        <h3 class="text-warning mb-0" id="totalunpaid"></h3>
                        <p class="mg-b-35 f-w-600 text-dark-800 txt-ellipsis-1">TOTAL INVOICE</p>
                    </div>
                </div>
            </div>

            <!-- Card 3: User Active -->
            <div class="col-12 col-md-6 col-xxl-3 mb-4">
                <div class="card text-center h-100">
                    <span class="bg-success h-50 w-50 d-flex-center rounded-circle m-auto eshop-icon-box">
                        <i class="ti ti-moneybag f-s-24"></i>
                    </span>
                    <div class="card-body eshop-cards">
                        <span class="ripple-effect"></span>
                        <h3 class="text-success mb-0" id="totaltagihan"></h3>
                        <p class="mg-b-35 f-w-600 text-dark-800 txt-ellipsis-1">TOTAL TAGIHAN</p>
                    </div>
                </div>
            </div>

            <!-- Card 4: User Expired -->
            <div class="col-12 col-md-6 col-xxl-3 mb-4">
                <div class="card text-center h-100">
                    <span class="bg-danger h-50 w-50 d-flex-center rounded-circle m-auto eshop-icon-box">
                        <i class="ti ti-heart-handshake f-s-24"></i>
                    </span>
                    <div class="card-body eshop-cards">
                        <span class="ripple-effect"></span>
                        <h3 class="text-danger mb-0" id="totalkomisi"></h3>
                        <p class="mg-b-35 f-w-600 text-dark-800 txt-ellipsis-1">TOTAL FEE MITRA</p>
                    </div>
                </div>
            </div> 
        </div>
    </div>
    <br/>
   <div class="card-body table-responsive">
            <table id="myTable" class="table table-responsive table-hover display nowrap" width="100%">
                <thead>
                    <tr>
                        <th><input type="checkbox" class="form-check-input" id="head-cb"></th>
                        <th>ID</th>
                        <th style="text-align:left!important">No Invoice</th>
                        <th>Nama Lengkap</th>
                        <th>POP</th>
                        {{-- <th>Tgl Invoice</th> --}}
                        <th>Jth Tempo</th>
                        <th>Periode Langganan</th>
                        <th>Total</th>
                        <th>Fee Mitra</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
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
        order: [
            [1, 'desc']
        ],
        // ajax: '{{ url()->current() }}',
        ajax: {
            url: '{{ url()->current() }}',
            dataSrc: function(json) {
                // Set the total unpaid and total tagihan from the response
                $('#totalunpaid').text(json.totalunpaid);
                if (json.totaltagihan) {
                    $('#totaltagihan').text('Rp'+formatRupiah(json.totaltagihan));
                } else {
                    $('#totaltagihan').text('Rp'+json.totaltagihan);
                }
                if (json.totalkomisi) {
                    $('#totalkomisi').text('Rp'+formatRupiah(json.totalkomisi));
                } else {
                    $('#totalkomisi').text('Rp'+json.totalkomisi);
                }
                return json.data;
            }
        },
        columns: [{
                data: 'checkbox',
                'sortable': false,
                name: 'checkbox',
            },
            {
                data: 'id',
                name: 'id',
                visible:false,
            },
            {
                data: 'no_invoice',
                name: 'no_invoice',
                className: 'text-start',
                render: function(data, type, row) {
                    return '<a href="javascript:void(0)" class="text-primary" style="text-decoration:none" id="show_invoice" data-id=' +
                        row.id +
                        '>' + data + '</a>'
                },
            },
            {
                data: 'full_name',
                name: 'full_name',
                sortable: false,
            },
            {
                data: 'kode_area',
                name: 'kode_area',
                sortable: false,
            },
            // {
            //     data: 'invoice_date',
            //     name: 'invoice_date',
            //     render: function(data, type, row, meta) {
            //         return moment(data).local().format('DD/MM/YYYY');
            //     },
            // },
            {
                data: 'due_date',
                name: 'due_date',
                render: function(data, type, row, meta) {
                    return moment(data).local().format('DD/MM/YYYY');
                },
            },
            {
                data: 'subscribe',
                name: 'subscribe',
            },
          
            {
                data: 'total',
                name: 'total',
                render: $.fn.dataTable.render.number('.', ',', 0, ''),
            },
            {
                data: 'komisi',
                name: 'komisi',
                render: $.fn.dataTable.render.number('.', ',', 0, ''),
            },
            {
                data: 'action',
                name: 'action',
            },
        ]
    });

    table.on('preXhr.dt', function(e, settings, data) {
        data.periode = $('#periode').val();
    });

    $("#periode").datepicker({
        format: "MM-yyyy",
        viewMode: "months",
        minViewMode: "months"
    });

    $('#periode').change(function() {
        table.ajax.reload();
        return false;
    });

    // Variabel global untuk menyimpan baris yang terpilih
    var selectedRows = {};

    // Fungsi untuk memperbarui tampilan row count
    function updateRowCount() {
        $(".row-count").html(Object.keys(selectedRows).length);
    }

    // Event listener untuk checkbox per baris
    $('#myTable').on('click', '.row-cb', function() {
        var rowId = $(this).val();

        if ($(this).is(':checked')) {
            selectedRows[rowId] = true;
        } else {
            delete selectedRows[rowId];
        }
        updateRowCount();

        // Update header checkbox (hanya untuk halaman aktif)
        var allChecked = $('.row-cb').length > 0 && $('.row-cb:checked').length === $('.row-cb').length;
        $('#head-cb').prop('checked', allChecked);

        // Update status tombol enable
        $('#payMassal,#printMassal,#btn-export').prop('disabled', Object.keys(selectedRows).length === 0);
    });

    // Event listener untuk header checkbox di halaman aktif
    $('#head-cb').on('click', function() {
        var isChecked = $(this).is(':checked');
        $('.row-cb').each(function() {
            var rowId = $(this).val();
            $(this).prop('checked', isChecked);
            if (isChecked) {
                selectedRows[rowId] = true;
            } else {
                delete selectedRows[rowId];
            }
        });

        updateRowCount();
        $('#payMassal,#printMassal,#btn-export').prop('disabled', Object.keys(selectedRows).length === 0);
    });

    // Saat DataTable melakukan redraw (misalnya pada pagination), perbarui checkbox di halaman baru
    table.on('draw', function() {
        $('.row-cb').each(function() {
            var rowId = $(this).val();
            $(this).prop('checked', !!selectedRows[rowId]);
        });

        var allChecked = $('.row-cb').length > 0 && $('.row-cb:checked').length === $('.row-cb').length;
        $('#head-cb').prop('checked', allChecked);
    });


    const pelanggan_id = new Choices(
        '#pelanggan_id', {
            allowHTML: true,
            removeItemButton: true,
            placeholderValue: 'Pilih Nama Pelanggan',
        },
    )
    $('#create_invoice').on('hide.bs.modal', function() {
        $("#show_payment").hide();
        pelanggan_id.removeActiveItems();
        $('#fill_area').val('');
        $('#fill_username').val('');
        $('#fill_password').val('');
        $('#fill_profile').val('');
    });

    $("#pelanggan_id").on("change", function() {
        var id = $(this).val();
        if (id === null) {
            $("#show_payment").hide();
        } else {
            $("#show_payment").show();
        }
        $.ajax({
            url: `/invoice/unpaid/getPelanggan/${id}`,
            type: "GET",
            cache: false,
            data: {
                id: id,
                '_token': '{{ csrf_token() }}'
            },
            success: function(data) {
                if (data.kode_area === null) {
                    $('#fill_area').val('<i>NULL</i>');
                } else {
                    $('#fill_area').val(data.kode_area);
                }
                $('#fill_id_pelanggan').val(data.id_pelanggan);
                $('#fill_full_name').val(data.full_name);
                $('#fill_username').val(data.username);
                $('#fill_password').val(data.value);
                $('#fill_profile').val(data.profile);
                $('#fill_wa').val(data.wa);
                $('#fill_mitra_id').val(data.mitra_id);
                let profile_id = data.rprofile.id;
                if (profile_id) {
                    var today = moment().format('yyyy-MM-DD');
                    var reg_date = data.reg_date;
                    var reg_date_start = moment(reg_date).startOf(
                        'month').format('yyyy-MM-DD');
                    var reg_date_end = moment(reg_date).endOf(
                        'month').format('yyyy-MM-DD');
                    var reg_date_format = moment(reg_date).format(
                        'DD/MM/yyyy');
                    var reg_date_end_format = moment(reg_date_end)
                        .format(
                            'DD/MM/yyyy');
                    var payment_type = data.payment_type;
                    var billing_period = data.billing_period;
                    var next_due = data.next_invoice;

                    var day_cycle = moment(data.next_due).format(
                        'DD');
                    var next_due_cycle = moment(data
                            .next_invoice).set("date", day_cycle)
                        .format("yyyy-MM-DD");

                    var ppn = data.ppn;
                    var discount = data.discount;
                    var komisi = data.rprofile.fee_mitra;
                    var amount_price = data.rprofile.price;
                    var amount_ppn = amount_price * ppn / 100;
                    var amount_discount = discount || 0;

                    var amount = parseInt(amount_price) + parseInt(
                        amount_ppn) - parseInt(amount_discount);
                    rp_amount = formatRupiah(amount_price
                        .toString(), 2, ',', '.');
                    rp_total_amount = formatRupiah(amount
                        .toString(), 2, ',',
                        '.');
                    var username = data.username;
                    var paket = data.profile;
                    var tgl = data.tgl;
                    var count_invoice = data.rinvoice.length;
                    var next_due_format = moment(next_due).format(
                        'DD/MM/yyyy');
                    var next_due_plus = moment(next_due).add(1, 'M')
                        .format('YYYY-MM-DD');
                    var next_due_plus_format = moment(next_due_plus)
                        .format(
                            'DD/MM/yyyy');
                    var next_due_min = moment(next_due).subtract(1,
                            'M')
                        .format('YYYY-MM-DD');
                    var next_due_min_format = moment(next_due_min)
                        .format(
                            'DD/MM/yyyy');

                    var next_due_start = moment(next_due_min)
                        .startOf('month').format('DD/MM/yyyy');
                    var next_due_end = moment(next_due_min).endOf(
                        'month').format('DD/MM/yyyy');
                    var subscribe_prabayar = next_due_format +
                        " s/d " + next_due_plus_format;
                    var subscribe_pascabayar = next_due_min_format +
                        " s/d " + next_due_format;
                    var subscribe_cycle = next_due_start +
                        " s/d " + next_due_end;
                    var subscribe_cycle0 = reg_date_format +
                        " s/d " + reg_date_end_format;


                    jml_day = moment(reg_date_end).diff(
                        reg_date_start, 'days') + 1;
                    jml_usage = moment(reg_date_end).diff(reg_date,
                        'days') + 1;
                    daily_amount0 = amount_price / jml_day;
                    daily_amount = Math.trunc(daily_amount0);
                    prorate0 = jml_usage * daily_amount;
                    prorate = prorate0.toString();
                    rp_prorate = formatRupiah(prorate, 2, ',', '.');
                    //ppn diambil dari harga paket
                    amount_ppn_prorate = prorate0 * ppn / 100;
                    amount_discount_prorate = discount || 0;
                    prorate_with_ppn_discount = parseInt(prorate0) + parseInt(
                        amount_ppn_prorate) - parseInt(amount_discount_prorate);
                    prorate_with_ppn_discount = prorate_with_ppn_discount.toString();
                    rp_prorate_with_ppn_discount = formatRupiah(prorate_with_ppn_discount, 2, ',',
                        '.');

                    var item = "Internet: " + username +
                        " | " +
                        paket;

                    var item_cycle = "Internet: " + username +
                        " | " +
                        paket + " @aktif " + jml_usage + " hari";

                    if (payment_type === 'Prabayar' &&
                        billing_period === 'Fixed Date') {
                        $('#fill_invoice_date').val(today);
                        $('#fill_due_date').val(next_due);
                        $('#fill_subscribe').val(
                            subscribe_prabayar);
                        $('#fill_validity option:selected').text(
                            '1 Month');
                        $('#fill_payment_type option:selected')
                            .text(payment_type);
                        $('#fill_billing_period option:selected')
                            .text(billing_period);
                        $('#fill_item').val(item);
                        $('#fill_amount').val(rp_amount);
                        $('#fill_ppn').val(ppn);
                        $('#fill_discount').val(discount);
                        $('#fill_komisi').val(komisi);
                        $('#fill_payment_total').val(
                            rp_total_amount);

                        // on change
                        $("#fill_amount,#fill_ppn,#fill_discount").on(
                            "keyup change",
                            function() {
                                var jml = document
                                    .getElementById(
                                        'fill_amount').value;
                                var amount = jml.replace(/\./g,
                                    "");
                                var ppn = $('#fill_ppn').val();
                                var discount = $(
                                    '#fill_discount').val();
                                var amount_ppn = amount * ppn /
                                    100;
                                var amount_discount = discount;
                                if (discount === '') {
                                    var total_with_ppn =
                                        parseInt(
                                            amount) + parseInt(
                                            amount_ppn);
                                    var total_plus_ppn =
                                        total_with_ppn
                                        .toString();
                                    rp_total_plus_ppn =
                                        formatRupiah(
                                            total_plus_ppn,
                                            2, ',', '.');
                                    $('#fill_payment_total')
                                        .val(
                                            rp_total_plus_ppn);
                                } else {
                                    var total_with_ppn_discount =
                                        parseInt(amount) +
                                        parseInt(
                                            amount_ppn) -
                                        parseInt(
                                            amount_discount);
                                    var total_plus_ppn_discount =
                                        total_with_ppn_discount
                                        .toString();
                                    rp_total_plus_ppn_discount =
                                        formatRupiah(
                                            total_plus_ppn_discount,
                                            2, ',', '.');
                                    $('#fill_payment_total')
                                        .val(
                                            rp_total_plus_ppn_discount
                                        );
                                }
                            });
                    } else if (payment_type === 'Prabayar' &&
                        billing_period === 'Renewable') {
                        $('#fill_invoice_date').val(today);
                        $('#fill_due_date').val(next_due);
                        $('#fill_subscribe').val(
                            subscribe_prabayar);
                        $('#fill_validity option:selected').text(
                            '1 Month');
                        $('#fill_payment_type option:selected')
                            .text(payment_type);
                        $('#fill_billing_period option:selected')
                            .text(billing_period);
                        $('#fill_item').val(item);
                        $('#fill_amount').val(rp_amount);
                        $('#fill_ppn').val(ppn);
                        $('#fill_discount').val(discount);
                        $('#fill_komisi').val(komisi);
                        $('#fill_payment_total').val(
                            rp_total_amount);

                        // on change
                        $("#fill_amount,#fill_ppn,#fill_discount").on(
                            "keyup change",
                            function() {
                                var jml = document
                                    .getElementById(
                                        'fill_amount').value;
                                var amount = jml.replace(/\./g,
                                    "");
                                var ppn = $('#fill_ppn').val();
                                var discount = $(
                                    '#fill_discount').val();
                                var amount_ppn = amount * ppn /
                                    100;
                                var amount_discount = discount;
                                if (discount === '') {
                                    var total_with_ppn =
                                        parseInt(
                                            amount) + parseInt(
                                            amount_ppn);
                                    var total_plus_ppn =
                                        total_with_ppn
                                        .toString();
                                    rp_total_plus_ppn =
                                        formatRupiah(
                                            total_plus_ppn,
                                            2, ',', '.');
                                    $('#fill_payment_total')
                                        .val(
                                            rp_total_plus_ppn);
                                } else {
                                    var total_with_ppn_discount =
                                        parseInt(amount) +
                                        parseInt(
                                            amount_ppn) -
                                        parseInt(
                                            amount_discount);
                                    var total_plus_ppn_discount =
                                        total_with_ppn_discount
                                        .toString();
                                    rp_total_plus_ppn_discount =
                                        formatRupiah(
                                            total_plus_ppn_discount,
                                            2, ',', '.');
                                    $('#fill_payment_total')
                                        .val(
                                            rp_total_plus_ppn_discount
                                        );
                                }
                            });
                    }else if (payment_type === 'Pascabayar' &&
                        billing_period === 'Fixed Date') {
                        $('#fill_invoice_date').val(today);
                        $('#fill_due_date').val(next_due);
                        $('#fill_subscribe').val(
                            subscribe_pascabayar);
                        $('#fill_validity option:selected').text(
                            '1 Month');
                        $('#fill_payment_type option:selected')
                            .text(payment_type);
                        $('#fill_billing_period option:selected')
                            .text(billing_period);
                        $('#fill_item').val(item);
                        $('#fill_amount').val(rp_amount);
                        $('#fill_ppn').val(ppn);
                        $('#fill_discount').val(discount);
                        $('#fill_komisi').val(komisi);
                        $('#fill_payment_total').val(
                            rp_total_amount);

                        // on change
                        $("#fill_ppn,#fill_discount").on(
                            "keyup change",
                            function() {
                                var jml = document
                                    .getElementById(
                                        'fill_amount').value;
                                var amount = jml.replace(/\./g,
                                    "");
                                var ppn = $('#fill_ppn').val();
                                var discount = $(
                                    '#fill_discount').val();
                                var amount_ppn = amount * ppn /
                                    100;
                                var amount_discount = discount;
                                if (discount === '') {
                                    var total_with_ppn =
                                        parseInt(
                                            amount) + parseInt(
                                            amount_ppn);
                                    var total_plus_ppn =
                                        total_with_ppn
                                        .toString();
                                    rp_total_plus_ppn =
                                        formatRupiah(
                                            total_plus_ppn,
                                            2, ',', '.');
                                    $('#fill_payment_total')
                                        .val(
                                            rp_total_plus_ppn);
                                } else {
                                    var total_with_ppn_discount =
                                        parseInt(amount) +
                                        parseInt(
                                            amount_ppn) -
                                        parseInt(
                                            amount_discount);
                                    var total_plus_ppn_discount =
                                        total_with_ppn_discount
                                        .toString();
                                    rp_total_plus_ppn_discount =
                                        formatRupiah(
                                            total_plus_ppn_discount,
                                            2, ',', '.');
                                    $('#fill_payment_total')
                                        .val(
                                            rp_total_plus_ppn_discount
                                        );
                                }
                            });
                    } else if (payment_type === 'Pascabayar' &&
                        billing_period === 'Billing Cycle' &&
                        count_invoice > 0) {
                        $('#fill_invoice_date').val(today);
                        $('#fill_due_date').val(next_due_cycle);
                        $('#fill_subscribe').val(
                            subscribe_cycle);
                        $('#fill_validity option:selected').text(
                            '1 Month');
                        $('#fill_payment_type option:selected')
                            .text(payment_type);
                        $('#fill_billing_period option:selected')
                            .text(billing_period);
                        $('#fill_item').val(item);
                        $('#fill_amount').val(rp_amount);
                        $('#fill_ppn').val(ppn);
                        $('#fill_discount').val(discount);
                        $('#fill_komisi').val(komisi);
                        $('#fill_payment_total').val(
                            rp_total_amount);

                        // on change
                        $("#fill_ppn,#fill_discount").on(
                            "keyup change",
                            function() {
                                var jml = document
                                    .getElementById(
                                        'fill_amount').value;
                                var amount = jml.replace(/\./g,
                                    "");
                                var ppn = $('#fill_ppn').val();
                                var discount = $(
                                    '#fill_discount').val();
                                var amount_ppn = amount * ppn /
                                    100;
                                var amount_discount = discount;
                                if (discount === '') {
                                    var total_with_ppn =
                                        parseInt(
                                            amount) + parseInt(
                                            amount_ppn);
                                    var total_plus_ppn =
                                        total_with_ppn
                                        .toString();
                                    rp_total_plus_ppn =
                                        formatRupiah(
                                            total_plus_ppn,
                                            2, ',', '.');
                                    $('#fill_payment_total')
                                        .val(
                                            rp_total_plus_ppn);
                                } else {
                                    var total_with_ppn_discount =
                                        parseInt(amount) +
                                        parseInt(
                                            amount_ppn) -
                                        parseInt(
                                            amount_discount);
                                    var total_plus_ppn_discount =
                                        total_with_ppn_discount
                                        .toString();
                                    rp_total_plus_ppn_discount =
                                        formatRupiah(
                                            total_plus_ppn_discount,
                                            2, ',', '.');
                                    $('#fill_payment_total')
                                        .val(
                                            rp_total_plus_ppn_discount
                                        );
                                }
                            });
                    } else if (payment_type === 'Pascabayar' &&
                        billing_period === 'Billing Cycle' &&
                        count_invoice === 0 && reg_date !==
                        reg_date_start) {
                        $('#fill_invoice_date').val(today);
                        $('#fill_due_date').val(next_due_cycle);
                        $('#fill_subscribe').val(
                            subscribe_cycle0);
                        $('#fill_validity option:selected').text(
                            '1 Month Prorate');
                        $('#fill_payment_type option:selected')
                            .text(payment_type);
                        $('#fill_billing_period option:selected')
                            .text(billing_period);
                        $('#fill_item').val(item_cycle);
                        $('#fill_amount').val(rp_prorate);
                        $('#fill_ppn').val(ppn);
                        $('#fill_discount').val(discount);
                        $('#fill_komisi').val(komisi);
                        $('#fill_payment_total').val(
                            rp_prorate_with_ppn_discount);

                        // on change
                        $("#fill_ppn,#fill_discount").on(
                            "keyup change",
                            function() {
                                var jml = document
                                    .getElementById(
                                        'fill_amount').value;
                                var amount = jml.replace(/\./g,
                                    "");
                                var ppn = $('#fill_ppn').val();
                                var discount = $(
                                    '#fill_discount').val();
                                var amount_ppn = amount * ppn /
                                    100;
                                var amount_discount = discount;
                                if (discount === '') {
                                    var total_with_ppn =
                                        parseInt(
                                            amount) + parseInt(
                                            amount_ppn);
                                    var total_plus_ppn =
                                        total_with_ppn
                                        .toString();
                                    rp_total_plus_ppn =
                                        formatRupiah(
                                            total_plus_ppn,
                                            2, ',', '.');
                                    $('#fill_payment_total')
                                        .val(
                                            rp_total_plus_ppn);
                                } else {
                                    var total_with_ppn_discount =
                                        parseInt(amount) +
                                        parseInt(
                                            amount_ppn) -
                                        parseInt(
                                            amount_discount);
                                    var total_plus_ppn_discount =
                                        total_with_ppn_discount
                                        .toString();
                                    rp_total_plus_ppn_discount =
                                        formatRupiah(
                                            total_plus_ppn_discount,
                                            2, ',', '.');
                                    $('#fill_payment_total')
                                        .val(
                                            rp_total_plus_ppn_discount
                                        );
                                }
                            });
                    } else if (payment_type === 'Pascabayar' &&
                        billing_period === 'Billing Cycle' &&
                        count_invoice === 0 && reg_date ===
                        reg_date_start) {
                        $('#fill_invoice_date').val(today);
                        $('#fill_due_date').val(next_due_cycle);
                        $('#fill_subscribe').val(
                            subscribe_cycle0);
                        $('#fill_validity option:selected').text(
                            '1 Month');
                        $('#fill_payment_type option:selected')
                            .text(payment_type);
                        $('#fill_billing_period option:selected')
                            .text(billing_period);
                        $('#fill_item').val(item);
                        $('#fill_amount').val(rp_amount);
                        $('#fill_ppn').val(ppn);
                        $('#fill_discount').val(discount);
                        $('#fill_komisi').val(komisi);
                        $('#fill_payment_total').val(
                            rp_total_amount);

                        // on change
                        $("#fill_ppn,#fill_discount").on(
                            "keyup change",
                            function() {
                                var jml = document
                                    .getElementById(
                                        'fill_amount').value;
                                var amount = jml.replace(/\./g,
                                    "");
                                var ppn = $('#fill_ppn').val();
                                var discount = $(
                                    '#fill_discount').val();
                                var amount_ppn = amount * ppn /
                                    100;
                                var amount_discount = discount;
                                if (discount === '') {
                                    var total_with_ppn =
                                        parseInt(
                                            amount) + parseInt(
                                            amount_ppn);
                                    var total_plus_ppn =
                                        total_with_ppn
                                        .toString();
                                    rp_total_plus_ppn =
                                        formatRupiah(
                                            total_plus_ppn,
                                            2, ',', '.');
                                    $('#fill_payment_total')
                                        .val(
                                            rp_total_plus_ppn);
                                } else {
                                    var total_with_ppn_discount =
                                        parseInt(amount) +
                                        parseInt(
                                            amount_ppn) -
                                        parseInt(
                                            amount_discount);
                                    var total_plus_ppn_discount =
                                        total_with_ppn_discount
                                        .toString();
                                    rp_total_plus_ppn_discount =
                                        formatRupiah(
                                            total_plus_ppn_discount,
                                            2, ',', '.');
                                    $('#fill_payment_total')
                                        .val(
                                            rp_total_plus_ppn_discount
                                        );
                                }
                            });
                    } else {
                        alert('Data Billing Tidak Sesuai!');
                    }
                } else {

                }
            }
        });
    });

    $('#generate_invoice').click(function(e) {
        e.preventDefault();

        // Ambil tombol dan simpan konten aslinya
        var $btn = $(this);
        var originalBtnContent = $btn.html();

        // Nonaktifkan tombol dan tampilkan spinner menggunakan Bootstrap
        $btn.prop('disabled', true)
            .html(
                'Processing&nbsp;<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
            );

        // Kumpulkan data dari form
        var dataku = {
            'pelanggan_id': $('#pelanggan_id').val(),
            'id_pelanggan': $('#fill_id_pelanggan').val(),
            'today': $('#fill_invoice_date').val(),
            'payment_type': $('#fill_payment_type').val(),
            'billing_period': $('#fill_billing_period').val(),
            'next_due': $('#fill_due_date').val(),
            'amount': $('#fill_amount').val(),
            'ppn': $('#fill_ppn').val(),
            'discount': $('#fill_discount').val(),
            'username': $('#fill_username').val(),
            'paket': $('#fill_profile').val(),
            'subscribe': $('#fill_subscribe').val(),
            'item': $('#fill_item').val(),
            'full_name': $('#fill_full_name').val(),
            'password': $('#fill_password').val(),
            'profile': $('#fill_profile').val(),
            'wa': $('#fill_wa').val(),
            'mitra_id': $('#fill_mitra_id').val(),
            'komisi': $('#fill_komisi').val(),
        };

        // Setup CSRF token untuk AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Proses AJAX
        $.ajax({
            url: `/invoice/unpaid/generate`,
            type: "POST",
            cache: false,
            data: dataku,
            dataType: "json",
            success: function(data) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                setTimeout(function() {
                    location.reload();
                }, 1500);
            },
            error: function(err) {
                $("#message").html("Some Error Occurred!");
                // Kembalikan tombol ke kondisi semula jika terjadi error
                $btn.prop('disabled', false).html(originalBtnContent);
            }
        });
    });

    $('#myTable').on('click', '#pay', function() {
        let id = $(this).data('id');
        let pelanggan_id = $(this).data('pelanggan_id');

        $.ajax({
            url: `/invoice/unpaid/getUnpaid/${id}`,
            type: "GET",
            data: {
                id: id,
                pelanggan_id: pelanggan_id
            },
            success: function(data) {
                if(data.response_data_status == true){
                    var invoice_id = data.id;
                    var full_name = data.rpppoe.full_name;
                    var no_invoice = data.no_invoice;
                    var invoice_date = data.invoice_date;
                    var amount = data.price;
                    var ppn = data.ppn;
                    var discount = data.discount;
                    var amount_ppn = amount * ppn / 100;
                    var amount_discount = discount || 0;
                    var total_with_ppn_discount = parseInt(amount) + parseInt(amount_ppn) - parseInt(
                        amount_discount);
                    var total_plus_ppn_discount = total_with_ppn_discount.toString();
                    var komisi = data.komisi;
                    var mitra_id = data.mitra_id;
                    var payment_type = data.payment_type;
                    var billing_period = data.billing_period;
                    var payment_url = data.payment_url;
                    var subscribe = data.subscribe;
                    var periode = data.period;
                    var due_date = data.due_date;
                    var pelanggan_id = data.id_pelanggan;
                    var id_pelanggan = data.rpppoe.id_pelanggan;
                    var nas = data.rpppoe.nas;
                    var wa = data.rpppoe.wa;
                    // data pppoe
                    var ppp_user = data.rpppoe.username;
                    var ppp_pass = data.rpppoe.value;
                    var ppp_profile = data.rpppoe.profile;
                    var ppp_status = data.rpppoe.status;

                    // Tampilkan swal input untuk metode pembayaran
                    Swal.fire({
                        title: "Konfirmasi Pembayaran",
                        icon: 'warning',
                        input: "select",
                        inputOptions: {
                            'Cash': 'Cash',
                            'Transfer': 'Transfer',
                        },
                        inputPlaceholder: 'Metode Pembayaran',
                        text: "INV #" + no_invoice + " a.n " + full_name,
                        showCancelButton: true,
                        confirmButtonText: "Ya, Sudah Bayar",
                        cancelButtonText: "Batal",
                        reverseButtons: true,
                        customClass: {
                            input: 'form-select w-auto p-3 mx-10',
                        },
                        inputValidator: function(value) {
                            return new Promise(function(resolve) {
                                if (value !== '') {
                                    resolve();
                                } else {
                                    resolve('Harap pilih metode pembayaran');
                                }
                            });
                        }
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            // Tampilkan swal loading sederhana tanpa timer
                            Swal.fire({
                                title: "Pay Invoice",
                                icon: "info",
                                text: "Invoice sedang dibayar. Harap tunggu...",
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            let invoice = invoice_id;
                            var dataku = {
                                'invoice_id': invoice,
                                'pelanggan_id': pelanggan_id,
                                'id_pelanggan': id_pelanggan,
                                'wa': wa,
                                'subscribe': subscribe,
                                'period': periode,
                                'full_name': full_name,
                                'no_invoice': no_invoice,
                                'invoice_date': invoice_date,
                                'amount': amount,
                                'ppn': ppn,
                                'discount': discount,
                                'komisi': komisi,
                                'mitra_id': mitra_id,
                                'payment_total': total_plus_ppn_discount,
                                'payment_method': result.value,
                                'payment_type': payment_type,
                                'payment_url': payment_url,
                                'billing_period': billing_period,
                                'due_date': due_date,
                                'ppp_status': ppp_status,
                                'pppoe_user': ppp_user,
                                'pppoe_pass': ppp_pass,
                                'pppoe_profile': ppp_profile,
                                'nas': nas,
                            };
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                        'content')
                                }
                            });

                            $.ajax({
                                url: `/invoice/unpaid/payInvoice/${invoice}`,
                                type: "PUT",
                                cache: false,
                                data: dataku,
                                dataType: "json",
                                success: function(data) {
                                    if (data.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success',
                                            text: data.message,
                                            showConfirmButton: false,
                                            timer: 1500
                                        });
                                        setTimeout(function() {
                                            table.ajax.reload();
                                        }, 1500);
                                    }
                                },
                                error: function(err) {
                                    $("#message").html("Some Error Occurred!");
                                }
                            });
                        }
                    });
                }else{
                    Swal.fire({
                        title: "Pay Invoice",
                        icon: "error",
                        text: data.message,
                        showConfirmButton: true,
                    });
                }
            }
        });
    });


    $('#myTable').on('click', '#show_invoice', function() {
        let id = $(this).data('id');
        if (id) {
            $.ajax({
                url: `/invoice/unpaid/getUnpaid/${id}`,
                type: "GET",
                data: {
                    id: id,
                },
                success: function(data) {
                    $("#invoice_id").val(data.id);
                    $("#full_name").val(data.rpppoe.full_name);
                    $("#no_invoice").val(data.no_invoice);
                    $("#invoice_date").val(data.invoice_date);
                    $("#due_date").val(data.due_date);
                    $("#subscribe").val(data.subscribe);
                    $("#payment_type option:selected").val(data.payment_type).text(data
                        .payment_type);
                    $("#validity option:selected").text('1 Month');
                    $("#billing_period option:selected").val(data.billing_period).text(data
                        .billing_period);
                    $("#item").val(data.item);

                    var amount = data.price.toString();

                    rp_amount = formatRupiah(amount, 2, ',', '.');
                    $('#amount').val(rp_amount);
                    rp_total_amount = formatRupiah(amount, 2, ',', '.');
                    $('#ppn').val(data.ppn);
                    $('#discount').val(data.discount);
                    $('#payment_total').val(rp_total_amount);

                    $('#show_invoice').on('shown.bs.modal', function() {
                        var ppn = $('#ppn').val();
                        var discount = $('#discount').val();
                        var amount_ppn = amount * ppn / 100;
                        var amount_discount = discount;
                        if (discount === '') {
                            var total_with_ppn = parseInt(amount) + parseInt(amount_ppn);
                            var total_plus_ppn = total_with_ppn.toString();
                            rp_total_plus_ppn = formatRupiah(total_plus_ppn, 2, ',', '.');
                            $('#payment_total').val(rp_total_plus_ppn);
                        } else {
                            var total_with_ppn_discount = parseInt(amount) + parseInt(
                                amount_ppn) - parseInt(amount_discount);
                            var total_plus_ppn_discount = total_with_ppn_discount
                                .toString();
                            rp_total_plus_ppn_discount = formatRupiah(
                                total_plus_ppn_discount,
                                2, ',', '.');
                            $('#payment_total').val(rp_total_plus_ppn_discount);
                        }
                    })


                    $("#amount,#ppn,#discount").on("keyup change", function() {
                        var jml = document.getElementById('amount').value;
                        var amount = jml.replace(/\./g, "");
                        var ppn = $('#ppn').val();
                        var discount = $('#discount').val();
                        var amount_ppn = amount * ppn / 100;
                        var amount_discount = discount;
                        if (discount === '') {
                            var total_with_ppn = parseInt(amount) + parseInt(amount_ppn);
                            var total_plus_ppn = total_with_ppn.toString();
                            rp_total_plus_ppn = formatRupiah(total_plus_ppn, 2, ',', '.');
                            $('#payment_total').val(rp_total_plus_ppn);
                        } else {
                            var total_with_ppn_discount = parseInt(amount) + parseInt(
                                amount_ppn) - parseInt(amount_discount);
                            var total_plus_ppn_discount = total_with_ppn_discount
                                .toString();
                            rp_total_plus_ppn_discount = formatRupiah(
                                total_plus_ppn_discount,
                                2, ',', '.');
                            $('#payment_total').val(rp_total_plus_ppn_discount);
                        }
                    });



                }
            });
        } else {

        }
        $('#show_invoice').modal('show');
    });

    $('#editInvoice').click(function(e) {
        e.preventDefault();

        // Hapus pesan error yang ada
        var error_ele = document.getElementsByClassName('alert text-sm');
        if (error_ele.length > 0) {
            for (var i = error_ele.length - 1; i >= 0; i--) {
                error_ele[i].remove();
            }
        }

        let id = $('#invoice_id').val();

        // Kumpulkan data dari form
        var data = {
            'no_invoice': $('#no_invoice').val(),
            'item': $('#item').val(),
            'ppn': $('#ppn').val(),
            'discount': $('#discount').val(),
            'amount': $('#amount').val(),
        };

        // Setup CSRF token untuk AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Simpan konten asli tombol dan tampilkan spinner
        var originalBtnContent = $('#editInvoice').html();
        $('#editInvoice').prop('disabled', true)
            .html(
                'Processing&nbsp;<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
            );

        // Proses AJAX
        $.ajax({
            url: `/invoice/unpaid/${id}`,
            type: "PUT",
            cache: false,
            data: data,
            dataType: "json",
            success: function(data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    setTimeout(function() {
                        table.ajax.reload();
                        $('#show_invoice').modal('hide');
                        // Kembalikan tombol ke kondisi semula
                        $('#editInvoice').prop('disabled', false).html(originalBtnContent);
                    }, 1500);
                } else {
                    $.each(data.error, function(key, value) {
                        var el = $(document).find('[name="' + key + '"]');
                        el.after($('<span class="alert text-sm text-danger">' + value[0] +
                            '</span>'));
                    });
                    // Kembalikan tombol ke kondisi semula
                    $('#editInvoice').prop('disabled', false).html(originalBtnContent);
                }
            },
            error: function(err) {
                $("#message").html("Some Error Occurred!");
                $('#editInvoice').prop('disabled', false).html(originalBtnContent);
            }
        });
    });

    $('#myTable').on('click', '#resend', function() {
    let id = $(this).data('id');

    Swal.fire({
        title: "Kirim ulang pesan?",
        icon: 'warning',
        text: "Pesan notifikasi invoice terbit akan dikirim ulang",
        showCancelButton: true,
        reverseButtons: true,
        confirmButtonText: "Ya, Kirim",
        cancelButtonText: "Batal",
        confirmButtonColor: "#d33",
        showLoaderOnConfirm: true, // Menampilkan spinner saat tombol diklik
        preConfirm: () => {
            // Setup CSRF token untuk AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Jalankan AJAX dan kembalikan promise-nya
            return $.ajax({
                url: `/invoice/unpaid/resend`,
                type: "POST",
                cache: false,
                data: { id: id },
                dataType: "json"
            }).then(response => {
                if (!response.success) {
                    return Promise.reject(response.error);
                }
                return response;
            }).catch(error => {
                let errMsg = error.responseText || error.statusText || JSON.stringify(error) || "Unknown error";
                Swal.showValidationMessage(`Request failed: ${errMsg}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then(result => {
        if (result.isConfirmed && result.value) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: result.value.message,
                showConfirmButton: false,
                timer: 1500
            });

            setTimeout(() => {
                table.ajax.reload();
            }, 1500);
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
                    url: `/invoice/unpaid/${id}`,
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
                            });
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

    $('#payMassal').on('click', function() {

        Swal.fire({
            title: "Konfirmasi Pembayaran",
            icon: 'warning',
            input: "select",
            inputOptions: {
                'Cash': 'Cash',
                'Transfer': 'Transfer',
            },
            inputPlaceholder: 'Metode Pembayaran',
            text: "Invoice yang dipilih akan dibayarkan",
            showCancelButton: true,
            confirmButtonText: "Ya, Sudah Bayar",
            cancelButtonText: "Batal",
            reverseButtons: true,
            customClass: {
                input: 'form-select w-auto p-3 mx-10',
            },
            inputValidator: function(value) {
                return new Promise(function(resolve) {
                    if (value !== '') {
                        resolve();
                    } else {
                        resolve('Harap pilih metode pembayaran');
                    }
                });
            }
        }).then(function(result) {
            if (result.isConfirmed) {
                // Tampilkan modal dengan spinner tanpa timer
                Swal.fire({
                    title: "Pay Invoice",
                    icon: "info",
                    html: "Invoice sedang dibayarkan. Harap tunggu...",
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                let payment_method = result.value;
                let ids = Object.keys(selectedRows);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: `/invoice/unpaid/payMassal`,
                    type: "POST",
                    cache: false,
                    data: {
                        ids: ids,
                        payment_method: payment_method,
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
                        $('#head-cb').prop('checked', false);
                        $(".row-count").html('');
                    },
                    error: function(err) {
                        $("#message").html("Some Error Occurred!");
                    }
                });
            }
        });
    });


    $('#printMassal1').on('click', function() {

        let ids = Object.keys(selectedRows);
        let template = $('#template').val();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $(
                        'meta[name="csrf-token"]'
                    )
                    .attr('content')
            }
        });

        $.ajax({
            url: `/invoice/unpaid/print`,
            type: "POST",
            cache: false,
            data: {
                ids: ids,
                template: template,
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
                        let win = window.open();
                        win.document.write(data.data);
                    }, 1500);
                $('#head-cb').prop('checked', false)
                $(".row-count").html('');
            },

            error: function(err) {
                $("#message").html(
                    "Some Error Occurred!"
                )
            }

        });


    });

    function downloadExcel() {
        let ids = Object.keys(selectedRows);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $(
                        'meta[name="csrf-token"]'
                    )
                    .attr('content')
            }
        });
        $.ajax({
            xhrFields: {
                responseType: 'blob',
            },
            type: 'POST',
            url: `/invoice/unpaid/export`,
            data: {
                ids: ids,
            },
            success: function(result, status, xhr) {

                var disposition = xhr.getResponseHeader('content-disposition');
                var matches = /"([^"]*)"/.exec(disposition);
                var filename = (matches != null && matches[1] ? matches[1] : 'invoice.xlsx');

                // The actual download
                var blob = new Blob([result], {
                    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                });
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = filename;

                document.body.appendChild(link);

                link.click();
                document.body.removeChild(link);
            }
        });
    }


    // /* Tanpa Rupiah */
    var price = document.getElementById('amount');
    var total = document.getElementById('payment_total');
    var fill_amount = document.getElementById('fill_amount');
    var fill_total = document.getElementById('fill_payment_total');
    price.addEventListener('keyup', function(e) {
        price.value = formatRupiah(this.value);
        total.value = formatRupiah(this.value);
    });
    fill_amount.addEventListener('keyup', function(e) {
        fill_amount.value = formatRupiah(this.value);
        fill_total.value = formatRupiah(this.value);
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
</script>
@endpush
