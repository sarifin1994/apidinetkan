<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Title -->
    <title>INV3 #{{ $invoice->no_invoice }} | {{ config('app.name') }}</title>
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <!-- Required Meta Tag -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="handheldfriendly" content="true" />
    <meta name="MobileOptimized" content="width" />
    <meta name="author" content="" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/starbill/img/favicon.png') }}" />

    <!-- Core Css -->
    <link rel="stylesheet" href="{{ asset(path: 'assets/css/theme.bundle.css') }}" />
 
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,1,0" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha512-SfTiTlX6kk+qitfevl/7LibUOeJWlt9rbyDn92a1DqWOw9vWG2MFoays0sgObmWazO5BQPiFucnnEAjpAB+/Sw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>


<body>
    <div class="container-xl px-4">
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8 col-sm-11">
                <!-- Social login form-->
                <div class="card my-5">
                    <div class="card-body text-center">
                        <center><div style="margin-bottom: 30px" class="btn-group btn-group-sm d-print-none"><a id="print_btn" style="width: 90px;font-weight: bold;font-size: 14px;padding-top: 2px;padding-bottom: 3px" href="javascript:window.print();" class="btn btn-default border shadow-none label"><i class="fa fa-print"></i> Print</a><a onclick="window.close();" style="width: 90px;font-weight: bold;font-size: 14px;padding-top: 2px;padding-bottom: 3px" href="" class="btn btn-default border shadow-none label"><i class="fa fa-times"></i> Close</a></div></center>
                        <img src="{{ $company->logo ? asset('storage/logo/' . $company->logo) : 'https://img.freepik.com/premium-vector/online-mobile-payment-banking-service-concept-payment-approved-payment-done-vector-illustration-flat-design-web-banner-mobile-app_98702-1311.jpg' }}" width="300px" />
                    </div>
                    <hr class="my-0" />
                    <div class="container-xl">
                        <div class="card-body px-0">
                            <div class="invoice-inner-part h-100">
                                <div class="invoiceing-box">
                                    <div class="invoice-header d-flex align-items-center border-bottom p-3">
                                        <h4 class=" text-uppercase mb-0">Invoice</h4>
                                        <div class="ms-auto">
                                            <h4 class="invoice-number">#{{ $invoice->no_invoice }}</h4>
                                        </div>
                                    </div>
                                    <div class="p-3" id="custom-invoice" style="display: block;">
                                        <div class="invoice-125" id="printableArea" style="display: block;">
                                            <div class="row pt-3">
                                                <div class="col-md-12">
                                                    <div>
                                                        <address>
                                                            <h6>&nbsp;Dari,</h6>
                                                            <h6 class="fw-bold">&nbsp;{{ $company->name }}</h6>
                                                            <p class="ms-1">
                                                                {{ $company->address }}
                                                            </p>
                                                        </address>
                                                    </div>
                                                    <div class="text-end">
                                                        <address>
                                                            <h6>Kepada,</h6>
                                                            <h6 class="fw-bold invoice-customer">
                                                                {{ $invoice->rpppoe->full_name }}
                                                            </h6>
                                                            <p class="ms-4">
                                                                {{ $invoice->rpppoe->address }}
                                                            </p>
                                                            <p class="mt-4 mb-1">
                                                                <span>Tgl Invoice :</span>
                                                                <i class="ti ti-calendar"></i>
                                                                {{ date('d/m/Y', strtotime($invoice->invoice_date)) }}
                                                            </p>
                                                            <p>
                                                                <span>Jatuh Tempo :</span>
                                                                <i class="ti ti-calendar"></i>
                                                                {{ date('d/m/Y', strtotime($invoice->due_date)) }}
                                                            </p>
                                                            <p>
                                                                <span>Status :</span>
                                                                @if ($invoice->status === 'unpaid')
                                                                    <span class="text-danger">BELUM BAYAR</span>
                                                                @else
                                                                    <span class="text-success">SUDAH BAYAR</span>
                                                                @endif
                                                            </p>
                                                        </address>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="table-responsive mt-5">
                                                        <table class="table table-hover">
                                                            <thead>
                                                                <!-- start row -->
                                                                <tr>
                                                                    <th class="text-center">#</th>
                                                                    <th>Deskripsi</th>
                                                                    <th class="text-end">Periode</th>
                                                                    <th class="text-end">Total</th>
                                                                </tr>
                                                                <!-- end row -->
                                                            </thead>
                                                            <tbody>
                                                                <!-- start row -->
                                                                <tr>
                                                                    <td class="text-center">1</td>
                                                                    <td>{{ $invoice->item }} <br>{{$invoice->subscribe}}</td>
                                                                    <td class="text-end">{{ $periode_format }}</td>
                                                                    <td class="text-end">
                                                                        Rp{{ number_format($invoice->price, 0, '.', '.') }}
                                                                    </td>
                                                                </tr>
                                                                <!-- end row -->

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="pull-right mt-4 text-end">
                                                        @php
                                                            $amount_ppn = ($invoice->price * $invoice->ppn) / 100;
                                                            $amount_discount = $invoice->discount;
                                                            $total =
                                                                $invoice->price +
                                                                $amount_ppn -
                                                                $amount_discount +
                                                                $midtrans->admin_fee;
                                                            $amount_ppn_format = number_format(
                                                                $amount_ppn,
                                                                0,
                                                                '.',
                                                                '.',
                                                            );
                                                            $amount_discount_format = number_format(
                                                                $amount_discount,
                                                                0,
                                                                '.',
                                                                '.',
                                                            );
                                                            $admin_fee_format = number_format(
                                                                $midtrans->admin_fee,
                                                                0,
                                                                '.',
                                                                '.',
                                                            );
                                                            $amount_format = number_format(
                                                                $invoice->price,
                                                                0,
                                                                '.',
                                                                '.',
                                                            );
                                                            $total_format = number_format($total, 0, '.', '.');
                                                        @endphp

                                                        <p>Subtotal:
                                                            Rp{{ $amount_format }}</p>
                                                        @if($invoice->ppn !== null)
                                                        <p>PPN <small class="text-sm">({{ $invoice->ppn }}%)</small> :
                                                            Rp{{ $amount_ppn_format }}
                                                        </p>
                                                        @endif
                                                        @if($invoice->discount !== null)
                                                        <p>Discount :
                                                            Rp{{ $amount_discount_format }}
                                                        </p>
                                                        @endif
                                                        <p>Biaya Admin : Rp{{ $admin_fee_format }}
                                                        </p>
                                                        <hr>
                                                        <h3>
                                                            <b>Total :</b> Rp{{ $total_format }}
                                                        </h3>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                    <hr>
                                                    <div class="text-end">
                                                        @if ($invoice->status === 'unpaid')
                                                        <button id="pay-button" data-id="{{ $invoice->id }}"
                                                            data-ppp="{{ $ppp->id }}"
                                                            class="btn btn-primary print-page ms-6 align-items-center gap-1"
                                                            type="button">
                                                            <span class="material-symbols-outlined"
                                                                style="font-size:20px">shopping_bag_speed</span>
                                                            <span class="label-text">Bayar Sekarang</span>
                                                            <div class="spinner-border spinner-border-sm text-light ms-2 d-none"
                                                                role="status" id="pay-spinner"></div>
                                                        </button>
                                                        @endif
                                                       
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <hr class="my-0" />
                    <div class="card-body px-5 py-4">
                        <div class="small text-center">
                            Pembayaran melalui link ini akan <b>terkonfirmasi secara otomatis</b><br>
                            Harap bayar tepat waktu sebelum jatuh tempo untuk menghindari isolir oleh sistem</a>
                        </div>

                    </div>
                </div>
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
