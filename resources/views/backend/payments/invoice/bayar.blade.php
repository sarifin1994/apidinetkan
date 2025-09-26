<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Title -->
  <title>INV #{{ $invoice->no_invoice }} | {{ config('app.name') }}</title>
  <!-- Required Meta Tag -->
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="handheldfriendly" content="true" />
  <meta name="MobileOptimized" content="width" />
  <meta name="author" content="" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="{{ asset('assets/radiusqu/img/favicon.png') }}" />

  <!-- Core Css -->
  <link rel="stylesheet" href="{{ asset('assets/radiusqu/dist/css/style.css') }}" />

  <link rel="stylesheet"
    href="{{ asset('assets/radiusqu/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('assets/radiusqu/dist/libs/select2/dist/css/select2.min.css') }}" />

</head>


<body>
  <div class="container-xl px-4">
    <div class="row justify-content-center">
      <div class="col-xl-5 col-lg-6 col-md-8 col-sm-11">
        <!-- Social login form-->
        <div class="card my-5">
          <div class="card-body text-center">
            <img
              src="https://img.freepik.com/premium-vector/online-mobile-payment-banking-service-concept-payment-approved-payment-done-vector-illustration-flat-design-web-banner-mobile-app_98702-1311.jpg"
              width="200px" />
          </div>
          <hr class="my-0" />
          <div class="container-xl">
            <div class="card-body px-0">
              <div class="invoice-inner-part h-100">
                <div class="invoiceing-box">
                  <div class="invoice-header d-flex align-items-center border-bottom p-3">
                    <h4 class="text-uppercase mb-0">Invoice</h4>
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
                                {{ $invoice->member->full_name }}
                              </h6>
                              <p class="ms-4">
                                {{ $invoice->member->address }}
                              </p>
                              <p class="mb-1 mt-4">
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
                                @if ($invoice->status->value === 0)
                                  <span class="text-danger">BELUM BAYAR</span>
                                @else
                                  <span class="text-success">SUDAH BAYAR</span>
                                @endif
                              </p>
                            </address>
                          </div>
                        </div>
                        <div class="col-md-12">
                          <div class="table-responsive custom-scrollbar mt-5">
                            <table class="table-hover table">
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
                                  <td>{{ $invoice->item }}</td>
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
                              $amount_discount = ($invoice->price * $invoice->discount) / 100;
                              $total = $invoice->price + $amount_ppn - $amount_discount + $midtrans->admin_fee;
                              $amount_ppn_format = number_format($amount_ppn, 0, '.', '.');
                              $amount_discount_format = number_format($amount_discount, 0, '.', '.');
                              $admin_fee_format = number_format($midtrans->admin_fee, 0, '.', '.');
                              $amount_format = number_format($invoice->price, 0, '.', '.');
                              $total_format = number_format($total, 0, '.', '.');
                            @endphp

                            <p>Subtotal:
                              Rp{{ $amount_format }}</p>
                            <p>PPN <small class="text-sm">({{ $invoice->ppn }}%)</small> :
                              Rp{{ $amount_ppn_format }}
                            </p>
                            <p>Discount <small class="text-sm">({{ $invoice->discount }}%)</small> :
                              Rp{{ $amount_discount_format }}
                            </p>
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
                            @if ($invoice->status->value === 0)
                              <button id="pay-button" data-id="{{ $invoice->id }}" data-ppp="{{ $ppp->id }}"
                                class="btn btn-primary btn-default print-page ms-6" type="button">
                                <span>
                                  <i class="ti ti-printer fs-5"></i>
                                  Bayar Sekarang
                                </span>
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
              Pembayaran melalui link ini akan <b>terkonfirmasi secara otomatis</b>. Terimakasih atas
              kepercayaan anda berlangganan internet di {{ $company->nickname }}</a>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>

  @if ($midtrans->status === 1)
    <script src="https://app.midtrans.com/snap/snap.js" data-client-key="<?php echo $client_key; ?>"></script>
  @else
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?php echo $client_key; ?>"></script>
  @endif
  <script type="text/javascript">
    document.getElementById('pay-button').onclick = function() {
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
  <script src="{{ asset('assets/radiusqu/dist/libs/jquery/dist/jquery.min.js') }}"></script>
</body>

</html>
