<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Invoice #{{ $invoice->no_invoice }} | {{ config('app.name') }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <link rel="icon" type="image/x-icon" href="{{ asset('assets/radiusqu/img/favicon.png') }}" />
  <link rel="stylesheet" href="{{ asset('assets/radiusqu/dist/css/style.css') }}" />
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
  <!-- Bootstrap Icons (for convenience) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" />
  <style>
    /* --------------------------------------------
       Base / Global Styles
    --------------------------------------------- */

    html,
    body {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI",
        Roboto, "Helvetica Neue", Arial, sans-serif;
      color: #34334f;
    }

    /* Container that holds the invoice + sidebar columns */
    .invoice-container {
      background-color: #f6f7ff;
      border-radius: 8px;
      padding: 1rem;
    }

    @media (min-width: 768px) {
      .invoice-container {
        padding: 2rem;
      }
    }

    .custom-card {
      background-color: #ffffff;
      border: none;
      border-radius: 8px;
      box-shadow: 0 6px 16px rgba(18, 19, 77, 0.06);
      padding: 1.5rem;
      margin-bottom: 1rem;
    }

    div#payment-options {
      max-height: 14rem;
      overflow: auto;
      padding-right: .25rem;
    }

    @media (min-width: 992px) {
      .custom-card {
        padding: 2rem;
      }
    }

    .section-spacer {
      margin-bottom: 1.25rem;
    }

    .badge-pill {
      padding: 0.25rem 1.25rem;
      border-radius: 8px;
      background-color: #f7f7f7;
      font-size: 0.85rem;
      color: #52525b;
      font-weight: 600;
    }

    .badge-pill.paid {
      background-color: #22c55e;
      color: #ffffff;
    }

    .text-gray-500 {
      color: #6c757d !important;
    }

    .invoice-title {
      font-weight: 600;
      font-size: 1.25rem;
      margin-bottom: 0.25rem;
    }

    .invoice-subtitle {
      font-size: 0.875rem;
      color: #6c757d;
      margin-bottom: 0;
    }

    .btn-custom-primary {
      background-color: #4945ff;
      color: #ffffff;
      border: none;
      border-radius: 6px;
      font-weight: 600;
      font-size: 18px;
    }

    .btn-custom-primary:hover {
      background-color: rgba(73, 69, 255, 0.9);
      color: #fff;
    }

    .btn-custom-link {
      color: #4945ff;
      text-decoration: none;
      padding: 0;
      border: none;
      background: none;
    }

    .btn-custom-link:hover {
      text-decoration: underline;
      color: #3f3ccf;
    }

    .radio-card {
      border: 1px solid #e4e4fa;
      border-radius: 6px;
      padding: 0.75rem;
      cursor: pointer;
      transition: border-color 0.1s, background-color 0.1s;
      margin-bottom: 0.75rem;
    }

    .radio-card:hover {
      border-color: #cfcff9;
    }

    .payment-selected {
      border-color: #4945ff !important;
      background-color: rgba(73, 69, 255, 0.05);
    }

    .text-success-label {
      color: #22c55e;
      font-size: 0.875rem;
      font-weight: 500;
    }

    .modal-overlay {
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.5);
      z-index: 1050;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1rem;
    }

    .modal-card {
      background-color: #ffffff;
      border-radius: 8px;
      box-shadow: 0 6px 16px rgba(18, 19, 77, 0.06);
      max-width: 420px;
      width: 100%;
      padding: 2rem;
      text-align: center;
    }

    .success-icon-circle {
      width: 3rem;
      height: 3rem;
      background-color: #d1fae5;
      border-radius: 9999px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 1rem auto;
    }

    .success-icon-circle svg {
      width: 1.5rem;
      height: 1.5rem;
      color: #22c55e;
    }

    .d-none {
      display: none !important;
    }
  </style>
</head>

<body>
  <!-- Outer container: centers content and sets width -->
  <div class="my-md-5 container my-4">
    <div class="invoice-container mx-auto">
      <div class="row gx-4 gy-4">
        <!-- Left Column: Main Invoice Section -->
        <div class="col-12 col-lg-8">
          <div class="custom-card">
            <div class="d-flex align-items-start section-spacer">
              <div class="me-3 rounded p-2" style="background-color: #E0EAFF;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                  style="color: #4945ff;">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
              </div>
              <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-center">
                  <div>
                    <h1 class="invoice-title">Invoice #{{ $invoice->no_invoice }}</h1>
                    <p class="invoice-subtitle">
                      Issued date: {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d.m.Y') }}
                    </p>
                  </div>
                  @if ($invoice->status->value === 1)
                    <span class="badge-pill">Unpaid</span>
                  @endif
                  @if ($invoice->status->value === 2)
                    <span class="badge-pill paid">Paid</span>
                  @endif
                  @if ($invoice->status->value === 3)
                    <span class="badge-pill">Cancel</span>
                  @endif
                  @if ($invoice->status->value === 4)
                    <span class="badge-pill">Expired</span>
                  @endif
                </div>
              </div>
            </div>
            <!-- From / To -->
            <div class="row section-spacer">
              <div class="col-12 col-md-6 mb-md-0 mb-3">
                <h3 class="fw-medium fs-6 mb-2">From:</h3>
                <div class="d-flex align-items-start">
                  <div class="flex-grow-1">
                    <p class="text-gray-500" style="white-space: normal; word-break: break-word;">
                      <strong class="fw-medium text-black">{{ $settings->name }}</strong><br>
                      {{ $settings->address }}
                    </p>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-6">
                <h3 class="fw-medium fs-6 mb-2">To:</h3>
                <p class="fw-medium mb-1">{{ $invoice->admin->name }}</p>
                <p class="mb-0 text-gray-500" style="white-space: normal; word-break: break-word;">
                  {{ $invoice->admin->address }}
                </p>
              </div>
            </div>

            <!-- Manual -->
            {{-- <div class="section-spacer">
              <div class="d-flex justify-content-between align-items-center border-bottom mb-3 pb-2">
                <div class="d-flex flex-grow-1 align-items-center">
                  <span class="fw-medium me-2">Account number:</span>
                  <span class="me-2">
                    11 1111 2222 4444 8000 3333 4948
                  </span>
                  <button class="d-flex flex-grow-1 justify-content-end btn btn-link text-decoration-none p-0"
                    title="Copy to clipboard">
                    <i class="bi bi-clipboard"></i>
                  </button>
                </div>
              </div>
              <div class="d-flex justify-content-between align-items-center border-bottom mb-3 pb-2">
                <div class="d-flex flex-grow-1 align-items-center">
                  <span class="fw-medium me-2">Bank name:</span>
                  <span class="me-2">
                    Bank Central Asia
                  </span>
                  <button class="d-flex flex-grow-1 justify-content-end btn btn-link text-decoration-none p-0"
                    title="Copy to clipboard">
                    <i class="bi bi-clipboard"></i>
                  </button>
                </div>
              </div>
              <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                <div class="d-flex flex-grow-1 align-items-center">
                  <span class="fw-medium me-2">Reference number:</span>
                  <span class="me-2">#{{ $invoice->no_invoice }}</span>
                  <button class="d-flex flex-grow-1 justify-content-end btn btn-link text-decoration-none p-0"
                    title="Copy to clipboard">
                    <i class="bi bi-clipboard"></i>
                  </button>
                </div>
              </div>
            </div> --}}
            @if ($activeGateway === 'manual')
              <div class="d-flex align-items-start mb-4 rounded p-3" style="background-color: #f0f0ff;">
                <p class="text-start">{!! nl2br(e($settings->bank_account)) !!}</p>
              </div>
            @endif

            <!-- Items Table -->
            <div class="section-spacer rounded border">
              <div class="row g-0 border-bottom fw-medium p-3" style="background-color: #f8f8fc;">
                <div class="col-6">ITEM</div>
                <div class="col-6 text-end">PRICE</div>
              </div>
              <div class="p-3">
                <div class="row mb-2">
                  <div class="col-6">Service : {{ $invoice->item }}</div>
                  <div class="col-6 text-end">
                    Rp{{ number_format($invoice->price, 0, '.', '.') }}
                  </div>
                </div>

                <hr />
                @if ($priceData->ppn > 0)
                  <div class="row mb-2">
                    <div class="col-6">PPN ({{ $priceData->ppnPercentage }}%)</div>
                    <div class="col-6 text-end">
                      Rp{{ number_format($priceData->ppn, 0, '.', '.') }}
                    </div>
                  </div>
                  <hr />
                @endif
                
                <!-- untuk masukin rincian adons -->
                @if($adons)
                <?php $total_ad = 0; ?>
                      @foreach($adons as $ad)
                      <?php $total_ad = $total_ad + $ad->price;?>
                      @if($ad->ppn > 0)
                        <?php 
                        $total_ppn_ad = $ad->ppn * $total_ad / 100;
                        ?>
                      @endif
                  <div class="row mb-2">
                    <div class="col-6">{{ $ad->description }}</div>
                    <div class="col-6 text-end">
                      Rp{{ number_format((($ad->qty * $ad->price) + $total_ppn_ad), 0, '.', '.') }}
                    </div>
                  </div>
                  @endforeach
                @endif
                <!-- untuk masukin rincian adons -->

                @if ($priceData->discount > 0)
                  <div class="row mb-2">
                    <div class="col-6">Discount ({{ $priceData->discountPercentage }}%)</div>
                    <div class="col-6 text-end">
                      -Rp{{ number_format($priceData->discount, 0, '.', '.') }}
                    </div>
                  </div>
                @endif
                @if ($priceData->discountCoupon > 0)
                  <div class="row mb-2">
                    <div class="col-6">Discount </div>
                    <div class="col-6 text-end">
                      -Rp{{ number_format($priceData->discountCoupon, 0, '.', '.') }}
                    </div>
                  </div>
                @endif
                @if ($priceData->adminFee > 0)
                  <div class="row mb-2">
                    <div class="col-6">Admin Fee</div>
                    <div class="col-6 text-end">
                      Rp{{ number_format($priceData->adminFee, 0, '.', '.') }}
                    </div>
                  </div>
                @endif
                <hr />
                <div class="row fw-medium">
                  <div class="col-6">Total incl. VAT</div>
                  <div class="col-6 text-end">
                    Rp{{ number_format(($invoice->price + $invoice->total_ppn + $invoice->price_adon  + $invoice->price_adon_monthly), 0, '.', '.') }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column: Payment Sidebar -->
        <div class="col-12 col-lg-4">
          <div class="custom-card">
            <!-- Summary -->
            <h3 class="h5 fw-semibold">Summary</h3>
            <div class="d-flex justify-content-between">
              <span>Payment:</span>
              <span>Rp{{ number_format(($invoice->price + $invoice->total_ppn + $invoice->price_adon  + $invoice->price_adon_monthly), 0, '.', '.') }}</span>
            </div>
            <div class="d-flex justify-content-between">
              <span>Payment method fee:</span>
              <span>Rp<span id="fee">0</span></span>
            </div>
            <hr />
            <div class="d-flex justify-content-between fw-semibold fs-5 mt-3">
              <span>Total Charge</span>
              <span>Rp<span id="charge">{{ number_format(($invoice->price + $invoice->total_ppn + $invoice->price_adon  + $invoice->price_adon_monthly) , 0, '.', '.') }}</span></span>
            </div>

            <!-- Cashback Guarantee -->
            <div class="d-flex align-items-start mt-4 rounded p-3" style="background-color: #f0f0ff;">
              <div class="me-3 rounded p-2" style="background-color: #d9d8ff; line-height: 0;">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                  style="color: #22c55e;">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
              </div>
              <div>
                <p class="fw-medium mb-0">100% Cashback Guarantee</p>
                <p class="mb-0 text-sm text-gray-500">
                  We protect your money
                </p>
              </div>
            </div>

            <!-- Pay Now Button -->
            @if ($invoice->status->value === 1)
              <!-- <form action="{{ route('admin.invoice_dinetkan.pay', $invoice->no_invoice) }}" method="POST" -->
                <!-- id="pay-now-form"> -->
                <!-- @csrf -->
                 
                <div class="fs-5 mt-3">      
                  <h3 class="h5 fw-semibold">Virtual Account</h3> 
                  <div>
                    <select class="form-control" name="payment_method" id="payment_method">
                      @foreach($paymentMethod as $key=>$val)
                      <option value="{{$key}}">{{$val}}</option>
                      @endforeach
                    </select>
                  </div>
                  <hr />   
                </div> 

                <div class="fs-5 mt-3 {{$invoice->virtual_account ? '' : 'd-none' }}" id="div_va">      
                  <h3 class="h5 fw-semibold">Virtual Account</h3> 
                  <div>
                    <span id="bank_name">
                      @if($invoice->bank_name)
                      {{$invoice->bank_name}}
                      @endif
                    </span>
                    <br>
                    <span id="va" class="d-flex align-items-center">
                      @if($invoice->virtual_account)
                      {{$invoice->virtual_account}}
                      @endif
                      <button class="btn btn-outline-primary btn-sm text-decoration-none ms-2 " id="va_copy">
                        <i class="bi bi-clipboard"></i> Copy
                      </button>
                    </span>
                  </div>
                  <hr />   
                </div> 
                
                <div>
                  <div class="accordion" id="accordionExample">
                    @foreach($panduan as $key=>$val)
                    <div class="accordion-item">
                      <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne{{Str::replace(' ','_',$key)}}" aria-expanded="true" aria-controls="collapseOne">
                          {{$key}}
                        </button>
                      </h2>
                      <div id="collapseOne{{Str::replace(' ','_',$key)}}" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                          <ul>
                            @foreach($val as $row)
                            <li>{{$row}}</li>
                            @endforeach
                          </ul>
                        </div>
                      </div>
                    </div>
                    @endforeach
                  </div>
                </div>
                <!-- <input type="hidden" name="payment_method" id="payment_method_input" />
                <input type="hidden" name="amount" value="{{ $priceData->total }}" /> -->
                <input type="hidden" name="invoice_id" id="invoice_id" value="{{ $invoice->id }}" />
                <button class="btn btn-custom-primary w-100 mt-4 py-3" id="pay-now-btn">
                  @if ($priceData->total > 0)
                    Pay now Rp<span id="payAmount">{{ number_format(($invoice->price + $invoice->total_ppn + $invoice->price_adon  + $invoice->price_adon_monthly), 0, '.', '.') }}</span>
                  @else
                    Confirm Payment
                  @endif
                </button>
              <!-- </form> -->
              <!-- <div class="alert alert-success mb-0 mt-4">
                This invoice is already paid.
              </div> -->
            @endif
            @if ($invoice->status->value === 2)
              <div class="alert alert-success mb-0 mt-4">
                This invoice is already paid.
              </div>
            @endif
            @if ($invoice->status->value === 3)
              <div class="alert alert-danger mb-0 mt-4">
                This invoice is Cancel.
              </div>
            @endif
            @if ($invoice->status->value === 4)
              <div class="alert alert-danger mb-0 mt-4">
                This invoice is Expired.
              </div>
            @endif

            <p class="mb-0 mt-4 text-center text-sm text-gray-500">
              Payment securely processed by <strong>{{ strtoupper($activeGateway) }}</strong>
            </p>
          </div>

          <!-- Timeline -->
          <div class="custom-card">
            <h3 class="h6 fw-semibold mb-4">Timeline</h3>
            <div class="d-flex align-items-center mb-3">
              <div class="rounded-circle me-3" style="background-color: #7b79ff; width: 0.5rem; height: 0.5rem;">
              </div>
              <div class="flex-grow-1 text-sm">
                Invoice created
              </div>
              <span class="text-sm text-gray-500">
                {{ \Carbon\Carbon::parse($invoice->created_at)->format('d.m.Y h:i A') }}
              </span>
            </div>

            @if($invoice->paid_date)
            <div class="d-flex align-items-center mb-3">
              <div class="rounded-circle me-3" style="background-color: #7b79ff; width: 0.5rem; height: 0.5rem;">
              </div>
              <div class="flex-grow-1 text-sm">
                Invoice paid
              </div>
              <span class="text-sm text-gray-500">
                {{ \Carbon\Carbon::parse($invoice->paid_date)->format('d.m.Y h:i A') }}
              </span>
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Success Modal -->
  <div class="modal-overlay d-none" id="successModal">
    <div class="modal-card">
      <div class="success-icon-circle">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
      </div>
      <h2 class="fs-5 fw-semibold mb-3">
        Your payment was successful!
      </h2>
      <p class="mb-4 text-sm text-gray-500">
        Thank you for your payment. Your invoice has been marked as paid.
      </p>
      <button class="btn btn-primary w-100" id="thanks-btn">
        Thanks!
      </button>
    </div>
  </div>

  <!-- jQuery and Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- @if ($activeGateway === 'midtrans' && $config && $config->isProduction())
    <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ $config->client_key }}"></script>
  @elseif($activeGateway === 'midtrans' && $config)
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ $config->client_key }}"></script>
  @endif

  @if ($activeGateway === 'duitku' && $config && $config->isProduction())
    <script src="https://app-prod.duitku.com/lib/js/duitku.js"></script>
  @elseif($activeGateway === 'duitku' && $config)
    <script src="https://app-sandbox.duitku.com/lib/js/duitku.js"></script>
  @endif -->

  <script>
    const baseurl = "{{ url('/') }}";
    $('#pay-now-btn').on('click', function(e) {
      const invoice_id = $('#invoice_id').val();
      const payment_method = document.getElementById('payment_method').value;
      const bank_name = $('#payment_method option:selected').text();
      
      if(payment_method == '' || payment_method == null || payment_method == 'undefined' || payment_method == 0){
        alert('virtual account didnt select');
        return;
      }
      Swal.fire({
          title: 'Please wait...',
          text: 'Processing Data ....',
          allowOutsideClick: false,
          showConfirmButton: false,
          didOpen: () => {
              Swal.showLoading();
          }
      });
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      $.ajax({
          url: baseurl + "/admin/invoice_dinetkan_generate",
          type: 'POST',
          data: {
            invoice_id: invoice_id,
            payment_method: payment_method,
            bank_name: bank_name
          },
          success: function(data) {
            // document.getElementById('va').textContent = data.vaNumber;
            // document.getElementById('bank_name').textContent = data.vaNumber;
            // $('#div_va').removeClass("d-none");
            location.reload();
            Swal.close();
          },
          error: function(xhr, status, error) {
            Swal.close();
          }
        });
    });
  </script>
  <script>
    const formatter = new Intl.NumberFormat('id-ID', {
      style: 'decimal',
      minimumFractionDigits: 0
    });

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $(document).ready(function() {
      const $successModal = $("#successModal");

      const selectedMethod = $("input[name='payment-method']:checked").val();
      $("#payment_method_input").val(selectedMethod);

      // $('#pay-now-form').on('submit', function(e) {
      //   e.preventDefault();
      //   const paymentMethod = $('#payment_method_input').val();
      //   const amount = $(this).find('input[name="amount"]').val();

      //   $('#pay-now-btn').prop('disabled', true);

      //   $.ajax({
      //     url: $(this).attr('action'),
      //     type: 'POST',
      //     data: {
      //       payment_method: paymentMethod
      //     },
      //     success: function(data) {
      //       if (data.url) {
      //         location.href = data.url;
      //       }

      //       @if ($activeGateway === 'midtrans')
      //         snap.pay(data.token, {
      //           onSuccess: function(result) {
      //             $successModal.css('display', 'flex').removeClass("d-none");
      //           },
      //           onPending: function(result) {
      //             location.reload();
      //           },
      //           onError: function(result) {
      //             location.reload();
      //           }
      //         });
      //       @elseif ($activeGateway === 'duitku')
      //         checkout.process(data.token, {
      //           successEvent: function(result) {
      //             $successModal.css('display', 'flex').removeClass("d-none");
      //           },
      //           pendingEvent: function(result) {
      //             location.reload();
      //           },
      //           errorEvent: function(result) {
      //             location.reload();
      //           },
      //           closeEvent: function(result) {
      //             location.reload();
      //           }
      //         });
      //       @else
      //         location.href = data.url;
      //       @endif

      //       $('#pay-now-btn').prop('disabled', false);
      //     },
      //     error: function(xhr, status, error) {
      //       $('#pay-now-btn').prop('disabled', false);
      //       alert("An error occurred. Please try again later.");
      //     }
      //   });
      // });

      // Hide modal on "Thanks!" button click
      $("#thanks-btn").on("click", function() {
        $successModal.hide().addClass("d-none");
      });

      // Radio-group highlight
      $("input[name='payment-method']").on("change", function() {
        const selectedVal = $(this).val();

        $(".radio-card").each(function() {
          const value = $(this).data("value");
          if (value === selectedVal) {
            $("#payment_method_input").val(value);
            $(this).addClass("payment-selected");
          } else {
            $(this).removeClass("payment-selected");
          }
        });

        // Update fee
        const fee = $(this).closest(".radio-card").data("fee");
        $("#fee").text(formatter.format(fee));

        // Update total
        const total = parseInt({{ $priceData->total }});
        const newTotal = total + fee;

        $("#charge").text(formatter.format(newTotal));
        $("#payAmount").text(formatter.format(newTotal));
      });

      // Close modal on outside click
      $(document).on("click", function(e) {
        if ($(e.target).is($successModal)) {
          $successModal.hide().addClass("d-none");
        }
      });

      // Close modal on ESC key
      $(document).on("keydown", function(e) {
        if (e.key === "Escape") {
          $successModal.hide().addClass("d-none");
        }
      });

      $("#va_copy").on("click", function() {
        const text = $("#va")
          .clone()               // clone to manipulate without affecting DOM
          .find("button").remove().end() // remove the button from the clone
          .text()                // get the text
          .trim();               // clean up whitespace

        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(text).then(function() {
              Swal.fire({
                  icon: 'success',
                  title: 'Success',
                  text: 'VA berhasil di copy',
                  showConfirmButton: false,
                  timer: 1000
              });
          }, function() {
              Swal.fire({
                  icon: 'error',
                  title: 'Gagal',
                  text: 'VA gagal di copy',
                  showConfirmButton: false,
                  timer: 1000
              });
          });
        } else {
          var textarea = document.createElement("textarea");
          textarea.value = text;
          document.body.appendChild(textarea);
          textarea.select();
          try {
            document.execCommand("copy");Swal.fire({
                  icon: 'success',
                  title: 'Success',
                  text: 'VA berhasil di copy',
                  showConfirmButton: false,
                  timer: 1000
              });
          } catch (err) {
              Swal.fire({
                  icon: 'error',
                  title: 'Gagal',
                  text: 'VA gagal di copy',
                  showConfirmButton: false,
                  timer: 1000
              });
          }
          document.body.removeChild(textarea);
        }
      });

    });
  </script>
</body>

</html>
