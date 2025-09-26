@extends('backend.layouts.app_new')
@section('main')
@section('title', 'Invoice Paid')
<!-- Content -->
<div class="container-fluid">
    <!-- Page header -->
    <div class="row align-items-center mb-7">
        <!-- Breadcrumb and Title -->
        <div class="col-md-6 mb-3 mb-md-0">
            <h4 class="main-title">Invoice Paid</h4>
            <ul class="app-line-breadcrumbs mb-0">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <span><i class="ti ti-file-invoice f-s-16"></i> Invoice</span>
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="javascript:void(0)">Paid</a>
                </li>
            </ul>
        </div>

        <!-- Buttons -->
        <div class="d-flex flex-wrap justify-content-md-end gap-2">
            @if (multi_auth()->role !== 'Mitra')
                <!-- Tombol Print Massal -->
                <!-- Tombol Print Massal -->
                <button class="btn btn-success me-2 mb-2 d-flex align-items-center" data-bs-toggle="modal"
                    id="printMassal" data-bs-target="#print_invoice" disabled title="Cetak invoice massal">
                    <i class="ti ti-printer me-1"></i>
                    Print
                    <span class="row-count badge bg-dark text-light fs-7 ms-2"></span>
                </button>

                <!-- Tombol Export Excel -->
                <button onclick="downloadExcel()" class="btn btn-warning text-white mb-2 d-flex align-items-center"
                    id="btn-export" disabled title="Export ke Excel">
                    <i class="ti ti-file-export me-1"></i>
                    Export
                    <span class="row-count badge bg-dark text-light fs-7 ms-2"></span>
                </button>
            @endif
        </div>
    </div>
    @include('backend.invoice.paid.modal.show_invoice')
    @include('backend.invoice.unpaid.modal.print_invoice')
    <!-- Page content -->
    <br/>
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
                    <h3 class="text-warning mb-0" id="totalpaid"></h3>
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
                    <h3 class="text-success mb-0" id="totalnominal"></h3>
                    <p class="mg-b-35 f-w-600 text-dark-800 txt-ellipsis-1">TOTAL BAYAR</p>
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
                    <h3 class="text-success mb-0" id="totalkomisi"></h3>
                    <p class="mg-b-35 f-w-600 text-dark-800 txt-ellipsis-1">TOTAL FEE MITRA</p>
                </div>
            </div>
        </div>
        <div class="card-body table-responsive">
            <table id="myTable" class="table table-responsive table-hover display nowrap" width="100%">
                <thead>
                    <tr>
                        <th><input type="checkbox" class="form-check-input" id="head-cb"></th>
                        <th>ID</th>
                        <th style="text-align:left!important">No Invoice</th>
                        <th>Nama Lengkap</th>
                        <th>POP</th>
                        <th>Jth Tempo</th>
                        <th>Tgl Bayar</th>
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
        ajax: {
            url: '{{ url()->current() }}',
            dataSrc: function(json) {
                // Set the total unpaid and total tagihan from the response
                $('#totalpaid').text(json.totalpaid);
                if (json.totalnominal) {
                    $('#totalnominal').text('Rp' + formatRupiah(json.totalnominal));
                } else {
                    $('#totalnominal').text('Rp' + json.totalnominal);
                }
                if (json.totalkomisi) {
                    $('#totalkomisi').text('Rp' + formatRupiah(json.totalkomisi));
                } else {
                    $('#totalkomisi').text('Rp' + json.totalkomisi);
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
                visible: false,
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
                data: 'paid_date',
                name: 'paid_date',
                render: function(data, type, row, meta) {
                    return moment(data).local().format('DD/MM/YYYY');
                },
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
        $('#totalunpaid').html(table.totalunpaid);
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


    $('#myTable').on('click', '#unpay', function() {

        let id = $(this).data('id');
        let pelanggan_id = $(this).data('pelanggan_id');
        $.ajax({
            url: `/invoice/unpaid/getUnpaid/${id}`,
            type: "GET",
            data: {
                id: id,
                pelanggan_id: pelanggan_id,
            },
            success: function(data) {
                var invoice_id = data.id;
                var full_name = data.rpppoe.full_name;
                var no_invoice = data.no_invoice;
                var invoice_date = data.invoice_date;
                var amount = data.price;
                var ppn = data.ppn;
                var discount = data.discount;
                var amount_ppn = amount * ppn / 100;
                var amount_discount = amount * discount / 100;
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
                // collect data pppoe
                var ppp_user = data.rpppoe.username;
                var ppp_pass = data.rpppoe.value;
                var ppp_profile = data.rpppoe.profile;
                var ppp_status = data.rpppoe.status;
                var next_due = data.rpppoe.next_due;

                Swal.fire({
                    title: "Batalkan Pembayaran",
                    icon: 'warning',
                    text: "INV #" + no_invoice + " a.n " + full_name,
                    showCancelButton: true,
                    confirmButtonText: "Ya, Batalkan",
                    cancelButtonText: "Close",
                    reverseButtons: true,
                }).then(function(result) {
                    if (result.isConfirmed) {
                        // Tampilkan swal loading tanpa timer
                        Swal.fire({
                            title: "Unpay Invoice",
                            icon: "info",
                            html: "Invoice sedang dibatalkan. Harap tunggu...",
                            allowOutsideClick: false,
                            showConfirmButton: false,
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
                            'komisi': komisi,
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
                            'next_due': next_due,
                        };

                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                    'content')
                            }
                        });

                        $.ajax({
                            url: `/invoice/paid/unpayInvoice/${invoice}`,
                            type: "PUT",
                            cache: false,
                            data: dataku,
                            dataType: "json",
                            success: function(data) {
                                if (data.success) {
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
                                }
                            },
                            error: function(err) {
                                $("#message").html("Some Error Occurred!");
                            }
                        });
                    }
                });
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
                        var amount_discount = amount * discount / 100;
                        if (discount === null) {
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
                        var amount_discount = amount * discount / 100;
                        if (discount === null) {
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
        var error_ele = document.getElementsByClassName('alert text-sm');
        if (error_ele.length > 0) {
            for (var i = error_ele.length - 1; i >= 0; i--) {
                error_ele[i].remove();
            }
        }
        let id = $('#invoice_id').val();

        // collect data by id
        var data = {
            'item': $('#item').val(),
            'ppn': $('#ppn').val(),
            'discount': $('#discount').val(),
            'amount': $('#amount').val(),
        }


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // ajax proses
        $.ajax({
            url: `/invoice/unpaid/${id}`,
            type: "PUT",
            cache: false,
            data: data,
            dataType: "json",

            // tampilkan pesan Success

            success: function(data) {
                console.log(data);
                if (data.success) {
                    Swal.fire({

                        icon: 'success',
                        title: 'Success',
                        text: `${data.message}`,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    setTimeout(function() {
                        table.ajax.reload()
                        $('#show_invoice').modal('hide')
                    });
                } else {
                    $.each(data.error, function(key, value) {
                        var el = $(document).find('[name="' + key + '"]');
                        el.after($('<span class= "alert text-sm text-danger">' + value[0] +
                            '</div>'));
                    });
                }
            },

            error: function(err) {
                $("#message").html("Some Error Occurred!")
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

    $('#unpayMassal').on('click', function() {

        Swal.fire({
            title: "Batalkan Pembayaran",
            icon: 'warning',
            text: "Invoice yang dipilih akan dibatalkan",
            showCancelButton: true,
            confirmButtonText: "Ya, Batalkan",
            cancelButtonText: "Close",
            reverseButtons: true,
        }).then(function(result) {
            if (result.isConfirmed) {
                // Tampilkan modal loading tanpa timer
                Swal.fire({
                    title: "Unpay Invoice",
                    icon: "info",
                    html: "Invoice sedang dibatalkan. Harap tunggu...",
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                let payment_method = result.value; // jika ada nilai, meski input tidak digunakan
                let checked = $('#myTable tbody .row-cb:checked');
                let ids = [];
                $.each(checked, function(index, elm) {
                    ids.push(elm.value);
                });
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: `/invoice/paid/unpayMassal`,
                    type: "POST",
                    cache: false,
                    data: {
                        ids: ids,
                        payment_method: payment_method,
                    },
                    dataType: "json",
                    success: function(data) {
                        console.log(data);
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
    // var fill_amount = document.getElementById('fill_amount');
    // var fill_total = document.getElementById('fill_payment_total');
    price.addEventListener('keyup', function(e) {
        price.value = formatRupiah(this.value);
        total.value = formatRupiah(this.value);
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
