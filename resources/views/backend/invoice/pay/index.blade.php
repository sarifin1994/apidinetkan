<!DOCTYPE html>
<html>

<head>
    <title>INV1 #{{ $invoice->no_invoice }} | {{ config('app.name') }}</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="robots" content="noindex,nofollow">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Core Css -->
    <link rel="stylesheet" href="{{ asset(path: 'assets/css/theme.bundle.css') }}" />


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
        integrity="sha512-SfTiTlX6kk+qitfevl/7LibUOeJWlt9rbyDn92a1DqWOw9vWG2MFoays0sgObmWazO5BQPiFucnnEAjpAB+/Sw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        body {
            background: #e7e9ed;
            /* font-size: 14px; */
            font-family: 'Inter', sans-serif;
        }

        table {
            white-space: nowrap;
            width: 100%;
            text-align: left;
            /* supaya rata kiri */
        }

        .invoice {
            background: none;
            border: none;
            padding: 0;
        }

        hr {
            margin: 1rem 0;
            color: inherit;
            border: 1;
            opacity: .25;
        }

        hr:not([size]) {
            height: 1px;
        }

        h4 {
            margin-top: 0;
            margin-bottom: .5rem;
            font-weight: 500;
        }

        h4 {
            font-size: calc(1.275rem + .3vw);
        }

        @media (min-width:1200px) {
            h4 {
                font-size: 1.5rem;
            }
        }

        p {
            margin-top: 0;
            margin-bottom: 1rem;
        }

        address {
            margin-bottom: 1rem;
            font-style: normal;
            line-height: inherit;
        }

        strong {
            font-weight: bolder;
        }

        a {
            color: #0d6efd;
            text-decoration: underline;
        }

        a:hover {
            color: #0a58ca;
        }

        table {
            caption-side: bottom;
            border-collapse: collapse;
        }

        tbody,
        td,
        tfoot,
        thead,
        tr {
            border-color: inherit;
            border-style: solid;
            border-width: 0;
        }

        ::-moz-focus-inner {
            padding: 0;
            border-style: none;
        }

        .container-fluid {
            width: 100%;
            margin-left: 0;
            margin-right: 0
        }

        .row {
            --bs-gutter-x: 1.5rem;
            --bs-gutter-y: 0;
            display: flex;
            flex-wrap: wrap;
            ;
        }

        .row>* {
            flex-shrink: 0;
            width: 100%;
            max-width: 100%;
            );
            margin-top: var(--bs-gutter-y);
        }

        .col-2 {
            flex: 0 0 auto;
            width: 16.66666667%;
        }

        .col-6 {
            flex: 0 0 auto;
            width: 50%;
        }

        @media (min-width:576px) {
            .col-sm-5 {
                flex: 0 0 auto;
                width: 41.66666667%;
            }

            .col-sm-6 {
                flex: 0 0 auto;
                width: 50%;
            }

            .col-sm-7 {
                flex: 0 0 auto;
                width: 58.33333333%;
            }
        }

        .table {
            --bs-table-bg: transparent;
            --bs-table-accent-bg: transparent;
            --bs-table-striped-color: #212529;
            --bs-table-striped-bg: rgba(0, 0, 0, 0.05);
            --bs-table-active-color: #212529;
            --bs-table-active-bg: rgba(0, 0, 0, 0.1);
            --bs-table-hover-color: #212529;
            --bs-table-hover-bg: rgba(0, 0, 0, 0.075);
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            vertical-align: top;
            border-color: #dee2e6;
        }

        .table>:not(caption)>*>* {
            padding: .5rem .5rem;
            background-color: var(--bs-table-bg);
            border-bottom-width: 1px;
            box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg);
        }

        .table>tbody {
            vertical-align: inherit;
        }

        .table>thead {
            vertical-align: bottom;
        }

        .table-bordered>:not(caption)>* {
            border-width: 1px 0;
        }

        .table-bordered>:not(caption)>*>* {
            border-width: 0 1px;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .card {
            margin-top: 10px;
            position: relative;
            display: flex;
            flex-direction: column;
            min-width: 0;
            word-wrap: break-word;
            background-color: #fff;
            background-clip: border-box;
            border: 1px solid rgba(0, 0, 0, .125);
            border-radius: .25rem;
        }

        .card-body {
            flex: 1 1 auto;
            padding: 1rem 1rem;
        }

        .card-header {
            padding: .5rem 1rem;
            margin-bottom: 0;
            background-color: rgba(0, 0, 0, .03);
            border-bottom: 1px solid rgba(0, 0, 0, .125);
        }

        .card-header:first-child {
            border-radius: calc(.25rem - 1px) calc(.25rem - 1px) 0 0;
        }

        .card-footer {
            padding: .5rem 1rem;
            background-color: rgba(0, 0, 0, .03);
            border-top: 1px solid rgba(0, 0, 0, .125);
        }

        .card-footer:last-child {
            border-radius: 0 0 calc(.25rem - 1px) calc(.25rem - 1px);
        }

        .align-top {
            vertical-align: top !important;
        }

        .shadow-none {
            box-shadow: none !important;
        }

        .border {
            border: 1px solid #dee2e6 !important;
        }

        .border-bottom-0 {
            border-bottom: 0 !important;
        }

        .align-items-center {
            align-items: center !important;
        }

        .mb-0 {
            margin-bottom: 0 !important;
        }

        .mb-3 {
            margin-bottom: 1rem !important;
        }

        .p-0 {
            padding: 0 !important;
        }

        .text-end {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .text-black-50 {
            color: rgba(0, 0, 0, .5) !important;
        }

        @media (min-width:576px) {
            .order-sm-0 {
                order: 0 !important;
            }

            .order-sm-1 {
                order: 1 !important;
            }

            .mb-sm-0 {
                margin-bottom: 0 !important;
            }

            .text-sm-start {
                text-align: left !important;
            }

            .text-sm-end {
                text-align: right !important;
            }
        }

        @media print {
            .d-print-none {
                display: none !important;
            }
        }

        body {
            background: #e7e9ed;
            font-size: 14px;
        }

        a,
        a:focus {
            color: #0071cc;
            text-decoration: none;
            -webkit-transition: all 0.2s ease;
            transition: all 0.2s ease;
        }

        a:hover,
        a:active {
            color: #005da8;
            -webkit-transition: all 0.2s ease;
            transition: all 0.2s ease;
        }

        a:focus,
        a:active {
            outline: none;
        }

        h4 {
            color: #0c2f54;
        }

        .text-1 {
            font-size: 12px !important;
        }

        .text-3 {
            font-size: 16px !important;
        }

        .text-4 {
            font-size: 18px !important;
        }

        .fw-500 {
            font-weight: 500 !important;
        }

        .fw-600 {
            font-weight: 600 !important;
        }

        hr {
            opacity: 0.15;
        }

        .table> :not(:last-child)> :last-child>* {
            border-bottom-color: inherit;
        }

        @media print {
            .table td {
                padding: 10px;
                background-color: transparent !important;
            }
        }

        .invoice-container {
            padding: 35px;
            max-width: 850px;
            background-color: #fff;
            border: 1px solid #ccc;
            -moz-border-radius: 6px;
            -webkit-border-radius: 6px;
            -o-border-radius: 6px;
            border-radius: 6px;
        }

        @media (max-width: 767px) {
            .invoice-container {
                padding: 35px 20px 70px 20px;
                margin-top: 0px;
                border: none;
                border-radius: 0px;
            }
        }

        /* Styles for Print and General Layout */
        @media print {
            .invoice-container {
                margin-top: 5px;
                padding-left: 30px;
                padding-right: 30px;
            }

            .layout-boxed,
            .content,
            .content-wrapper {
                background-color: #fff;
            }

            img {
                width: 200px;
            }

            @page {
                size: auto;
                margin: 0;
            }

            body {
                margin-top: -0.48cm;
                background: transparent;
            }

            .container-fluid {
                border: none;
            }

            .page-break {
                display: block;
                page-break-before: always;
            }

            .no-print,
            .no-print * {
                display: none !important;
            }

            /* } */

            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
            }

            a.label:hover {
                color: #333;
            }

            a.btn-primary:hover {
                color: #fff;
            }

            .btn-group-vertical>.btn,
            .btn-group>.btn {
                position: static;
            }
        }
    </style>
</head>

<body>
    <div class="container-xl px-4">

        <div class="row justify-content-center">
            <div class="col-sm-12 col-md-6">
                <section style="padding-top:20px;padding-bottom:20px" class="invoice">
                    <div style="margin:0 auto;" class="container-fluid invoice-container">
                        <header>
                            <div class="row align-items-center text-center">
                                <div class="btn-group-sm d-print-none mb-3">
                                    <a id="print_btn" href="javascript:window.print();"
                                        class="btn btn-default border shadow-none label"><i class="fa fa-print"></i>
                                        Print</a>
                                    <a onclick="window.close();" href="#"
                                        class="btn btn-default border shadow-none label"><i class="fa fa-times"></i>
                                        Close</a>
                                </div>
                                <div class="col-sm-7 text-center text-sm-start mb-3 mb-sm-0">
                                    <img id="logo" src="{{ asset('storage/logo/' . $company->logo) }}"
                                        title="" alt="" style="max-width:200px;" />
                                </div>
                                <div class="col-sm-5 text-center text-sm-end">
                                    <h4 class="mb-0" style="font-size: 20px; font-weight: bold;">INVOICE | <span>
                                            @if ($invoice->status === 'unpaid')
                                                <i class="fa fa-times"></i> <span
                                                    style="font-style:italic; color:red; font-weight: bold;">BELUM
                                                    BAYAR</span>
                                        </span>
                                    @else
                                        <i class="fa fa-check"></i> <span
                                            style="font-style:italic; color:green; font-weight: bold;">LUNAS</span></span>
                                        @endif
                                    </h4>
                                    <p class="mb-0" style="font-weight: bold">INV #{{ $invoice->no_invoice }}
                                    </p>
                                </div>
                            </div>
                        </header>

                        <main class="mt-8">
                            <!-- Billing Info -->
                            <div class="row">
                                <div class="col-sm-6 text-sm-end order-sm-1">
                                    <strong>Dibayarkan ke :</strong>
                                    <address>
                                        {{ $company->name }}<br>{{ $company->address }}<br>{{ $company->whatsapp }}
                                    </address>
                                </div>
                                <div class="col-sm-6 order-sm-0">
                                    <strong>Ditagihkan ke :</strong>
                                    <address>{{ $invoice->rpppoe->full_name }} |
                                        {{ $invoice->rpppoe->id_pelanggan }}<br>{{ $invoice->rpppoe->address }}<br>{{ $invoice->rpppoe->wa }}
                                    </address>
                                </div>

                            </div>

                            <!-- Service Summary -->
                            <div class="">
                                <div class="card-header"><span class="fw-bold fs-6">Ringkasan Layanan</span></div>

                                {{-- <div class="card-body p-0"> --}}
                                {{-- <div class="col-md-12"> --}}
                                <div class="table-responsive px-3">
                                    <table class="table table-striped">
                                        <thead>
                                            <!-- start row -->
                                            <tr>
                                                <th>Deskripsi</th>
                                                <th>Periode</th>
                                                <th>Jatuh Tempo</th>
                                                <th class="text-end">Total</th>
                                            </tr>
                                            <!-- end row -->
                                        </thead>
                                        <tbody>
                                            <!-- start row -->
                                            <tr>
                                                <td>{{ $invoice->item }} <br>{{ $invoice->subscribe }}</td>
                                                <td>{{ $periode_format }}</td>
                                                <td>{{ \Carbon\Carbon::parse($invoice->due_date)->translatedFormat('d F Y') }}</td>
                                                <td class="text-end">
                                                    Rp{{ number_format($invoice->price, 0, '.', '.') }}
                                                </td>
                                            </tr>
                                            
                                            @if(isset($invoice->rpppoe->addon))
                                            @foreach($invoice->rpppoe->addon as $ad)
                                            <tr>
                                                <td>Ad on | {{ $ad->description }}</td>
                                                <td></td>
                                                <td></td>
                                                <td class="text-end">
                                                    Rp{{ number_format($ad->price, 0, '.', '.') }}
                                                </td>
                                            </tr>
                                            @endforeach
                                            @endif
                                            <!-- end row -->

                                        </tbody>

                                    </table>

                                </div>
                                @php
                                    $total_ppn_add = 0;
                                    $total_price = 0;
                                    if(isset($invoice->rpppoe->addon)){
                                        foreach($invoice->rpppoe->addon as $ad){
                                            if($ad->ppn > 0){
                                                $total_ppn_add = total_ppn_add + ($ad->price * $ad->ppn / 100);
                                            }
                                            $total_price = $total_price + $ad->price + $total_ppn_add;
                                        }
                                    }

                                    $amount_ppn = ($invoice->price * $invoice->ppn) / 100;
                                    $amount_discount = $invoice->discount;
                                    $total = $invoice->price + $amount_ppn - $amount_discount + $midtrans->admin_fee + $total_price;
                                    $amount_ppn_format = number_format($amount_ppn, 0, '.', '.');
                                    $amount_discount_format = number_format($amount_discount, 0, '.', '.');
                                    $admin_fee_format = number_format($midtrans->admin_fee, 0, '.', '.');
                                    $amount_format = number_format($invoice->price, 0, '.', '.');
                                    $total_format = number_format($total, 0, '.', '.');
                                @endphp
                                <div class="col-md-12">
                                    <div class="pull-right mt-4 text-end">
                                        <p>
                                            <td colspan="3" class="text-end"><strong>Subtotal :</strong>
                                            </td>
                                            <td class="text-end">
                                                Rp{{ number_format($invoice->price, 0, '.', '.') }}</td>
                                        </p>
                                        <!-- <p>
                                            <td colspan="3" class="text-end"><strong>Biaya Admin :</strong>
                                            </td>
                                            <td class="text-end">
                                                Rp{{ $admin_fee_format }}</td>
                                        </p> -->
                                        @if ($invoice->discount !== null)
                                            <p>
                                                <td colspan="3" class="text-end"><strong>Discount :</strong>
                                                </td>
                                                <td class="text-end"> Rp{{ $amount_discount_format }}</td>
                                            </p>
                                        @endif
                                        @if ($invoice->ppn !== null)
                                            <p>
                                                <td colspan="3" class="text-end"><strong>PPN ({{ $invoice->ppn }}%)
                                                        :</strong><br><span class="text-1"><i>based on company &
                                                            country regulation</i></span></td>
                                                <td class="text-end">Rp{{ $amount_ppn_format }}</td>
                                            </p>
                                        @endif
                                            <p>
                                                <td colspan="3" class="text-end"><strong>Total Ad on :</strong>
                                                </td>
                                                <td class="text-end"> Rp{{ number_format($total_price, 0, '.', '.') }}</td>
                                            </p>
                                        <h4>
                                            <td colspan="3" class="text-end border-bottom-0 fs-5">
                                                <strong>Total
                                                    :</strong>
                                            </td>
                                            <td class="text-end border-bottom-0 fs-5">Rp{{ $total_format }}
                                            </td>
                                        </h4>
                                    </div>
                                    <div class="clearfix"></div>
                                                    {{-- <hr> --}}
                                    <div class="text-end mt-3">
                                        @if ($invoice->status === 'unpaid')
                                            <button id="pay-button" data-id="{{ $invoice->id }}"
                                                data-ppp="{{ $ppp->id }}"
                                                class="btn btn-primary print-page ms-6 align-items-center gap-1"
                                                type="button">
                                                <i class="fa fa-shopping-cart" aria-hidden="true"></i> Bayar Sekarang
                                                <div class="spinner-border spinner-border-sm text-light ms-2 d-none"
                                                    role="status" id="pay-spinner"></div>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </main>
                        <!-- Notes -->
                        <div class="clearfix mt-10"></div>
                        <hr>
                        <footer class="text-center" style="font-size: 14px; font-style: italic;">
                            <div class="card-body px-5 py-4">
                                <div class="small text-center">
                                    Pembayaran melalui link ini akan <b>terkonfirmasi secara otomatis</b><br>
                                    Harap bayar tepat waktu sebelum jatuh tempo untuk menghindari isolir oleh sistem</a>
                                </div>

                            </div>
                        </footer>
                        <hr>
                    </div>
                </section>
            </div>
        </div>
    </div>

    @if($midtrans->status === 1)
    <script src="https://app.midtrans.com/snap/snap.js" data-client-key="<?php echo $client_key; ?>"></script>
    @else
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?php echo $client_key; ?>"></script>
    @endif
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script type="text/javascript">
        document.getElementById('pay-button').onclick = function() {
            let button = this;
            let spinner = document.getElementById('pay-spinner');
            let label = button.querySelector('.label-text');
            let invoice_id = $(this).data('id');
            // let invoice_id = $(this).data('id');
            // let ppp_id = $(this).data('ppp');
            snap.pay('<?php echo $snap_token; ?>', {

                onSuccess: function(result) { 
                    location.reload();
                },
                onPending: function(result) {
                    location.reload();
                },
                onError: function(result) {
                    location.reload();
                }
            });
        };
    </script>


</body>

</html>

