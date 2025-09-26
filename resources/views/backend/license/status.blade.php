<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Title -->
    <title>INV #{{ $user->order_number }} | {{ config('app.name') }}</title>
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

</head>


<body>
    <div class="container-xl px-4">
        <div class="row justify-content-center">
            <div class="col-xl-5 col-lg-6 col-md-8 col-sm-11">
                <!-- Social login form-->
                <div class="card my-5">
                    <div class="card-body text-center">
                        <img src="https://img.freepik.com/premium-vector/online-mobile-payment-banking-service-concept-payment-approved-payment-done-vector-illustration-flat-design-web-banner-mobile-app_98702-1311.jpg"
                            width="200px" />
                    </div>
                    <hr class="my-0" />
                    <div class="container-xl">
                        <div class="card-body px-0">
                            <div class="invoice-inner-part h-100">
                                <div class="invoiceing-box">
                                    <div class="invoice-header d-flex align-items-center border-bottom p-3">
                                        <h4 class="mb-2">ORDER LISENSI</h4>
                                        <div class="ms-auto">
                                            <h4 class="invoice-number">#{{ $user->order_number }}</h4>
                                        </div>
                                    </div>
                                    <div class="p-3" id="custom-invoice" style="display: block;">
                                        <div class="invoice-125" id="printableArea" style="display: block;">
                                            <div class="row pt-3">
                                                <div class="col-md-12">
                                                   
                                                    <div class="">
                                                        <address>
                                                            <h6>Hai, {{ multi_auth()->username}}  </h6>
                                                            <p>Terimakasih sudah melakukan pembayaran untuk lisensi Radiusqu,<br>Silakan cek status pembayaranmu dihalaman ini secara berkala</p>
                                                            
                                                            <p>
                                                                <span>Status :</span>
                                                                @if ($user->order_status === 'unpaid')
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
                                                                    <th>Nama</th>
                                                                    <th class="text-end">Total</th>
                                                                </tr>
                                                                <!-- end row -->
                                                            </thead>
                                                            <tbody>
                                                                <!-- start row -->
                                                                <tr>
                                                                    <td class="text-center">1</td>
                                                                    <td>Lisensi Radiusqu {{ $user->license->name }}</td>
                                                                    <td class="text-end">
                                                                        Rp{{ number_format($user->license->price, 0, '.', '.') }}
                                                                    </td>
                                                                </tr>
                                                                <!-- end row -->

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="pull-right mt-4 text-end">
                                                    

                                                        
                                                        <hr>
                                                        <h3>
                                                            <b>Total :</b> Rp{{ number_format($user->license->price, 0, '.', '.') }}
                                                        </h3>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                    <hr>
                                                    <div class="text-end">
                                                            <a href="/dashboard"
                                                                class="btn btn-primary btn-primary print-page ms-6"
                                                                type="button">
                                                                <span class="material-symbols-outlined" style="font-size:20px">
                                                                    reply
                                                                    </span>
                                                                    Kembali ke dashboard
                                                                </a>
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
                            Ubur ubur ikan lele, terimakasih le 🤪<br>
                            -- {{env('APP_URL')}} --</a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
