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
                  @if ($invoice->status->value === 0)
                    <span class="badge-pill">Unpaid</span>
                  @else
                    <span class="badge-pill paid">Paid</span>
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
                    <p class="text-gray-500">
                      <strong class="fw-medium text-black">{{ $company->name }}</strong><br>
                      {{ $company->address }}
                    </p>
                  </div>
                  <button class="d-flex justify-content-end btn btn-link text-decoration-none ms-2 p-0"
                    title="Copy to clipboard">
                    <i class="bi bi-clipboard fs-5"></i>
                  </button>
                </div>
              </div>
              <div class="col-12 col-md-6">
                <h3 class="fw-medium fs-6 mb-2">To:</h3>
                <p class="fw-medium mb-1">{{ $invoice->member->full_name }}</p>
                <p class="mb-0 text-gray-500">
                  {{ $invoice->member->address }}
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
                  <div class="col-6">{{ $invoice->item }}</div>
                  <div class="col-6 text-end">
                    Rp{{ number_format($invoice->price, 0, '.', '.') }}
                  </div>
                </div>

                <hr />
                @if ($priceData->ppn > 0)
                  <div class="row mb-2">
                    <div class="col-6">PPN ({{ $priceData->ppn }}%)</div>
                    <div class="col-6 text-end">
                      Rp{{ number_format($priceData->ppn, 0, '.', '.') }}
                    </div>
                  </div>
                @endif
                @if ($priceData->discount > 0)
                  <div class="row mb-2">
                    <div class="col-6">Discount ({{ $priceData->discount }}%)</div>
                    <div class="col-6 text-end">
                      -Rp{{ number_format($priceData->discount, 0, '.', '.') }}
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
                    Rp{{ number_format($priceData->total, 0, '.', '.') }}
                  </div>
                </div>
              </div>
            </div>
            <div class="d-flex align-items-center mt-4">
              <div class="me-auto text-gray-500"></div>
              <a href="{{ route('invoice.print', $invoice->no_invoice) }}"
                class="btn btn-light text-decoration-none d-flex align-items-center border shadow" target="_blank">
                <i class="bi bi-download me-1"></i>
                Download PDF
              </a>
            </div>
          </div>
        </div>

        <!-- Right Column: Payment Sidebar -->
        <div class="col-12 col-lg-4">
          <div class="custom-card">
            @if ($tripayMethods)
              <div class="d-flex align-items-center justify-content-between mb-4">
                <h2 class="h6 fw-semibold mb-0">Payment Method</h2>
              </div>

              <div id="payment-options" class="mb-4">
                @foreach ($tripayMethods as $method)
                  <div class="radio-card d-flex align-items-center justify-content-between"
                    data-value="{{ $method->code }}" data-fee="{{ $method->fee }}">
                    <div class="d-flex align-items-center">
                      <input class="form-check-input me-2" type="radio" name="payment-method"
                        id="{{ $method->code }}" value="{{ $method->code }}" @checked($loop->first) />
                      <label for="{{ $method->code }}" class="m-0">
                        <div class="d-flex align-items-center">
                          <div class="d-flex align-items-center justify-content-center bg-light rounded"
                            style="width: 2.25rem; height: 2rem;">
                            <span class="fw-bold text-sm">
                              {{ strtoupper(substr($method->code, 0, 3)) }}
                            </span>
                          </div>
                          <div class="ms-3">
                            <p class="fw-medium mb-0">
                              {{ $method->name }}
                            </p>
                          </div>
                        </div>
                      </label>
                    </div>
                  </div>
                @endforeach
              </div>
            @endif

            <!-- Summary -->
            <h3 class="h5 fw-semibold">Summary</h3>
            <div class="d-flex justify-content-between">
              <span>Payment:</span>
              <span>Rp{{ number_format($priceData->total, 0, '.', '.') }}</span>
            </div>
            <div class="d-flex justify-content-between">
              <span>Payment method fee:</span>
              <span>Rp<span id="fee">0</span></span>
            </div>
            <hr />
            <div class="d-flex justify-content-between fw-semibold fs-5 mt-3">
              <span>Total Charge</span>
              <span>Rp<span id="charge">{{ number_format($priceData->total, 0, '.', '.') }}</span></span>
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

            @if ($invoice->status->value === 0)
              <form action="{{ route('invoice.process', $invoice->no_invoice) }}" method="POST" id="pay-now-form">
                @csrf
                <input type="hidden" name="payment_method" id="payment_method_input" />
                <input type="hidden" name="amount" value="{{ $priceData->total }}" />
                <button type="submit" class="btn btn-custom-primary w-100 mt-4 py-3" id="pay-now-btn">
                  @if ($priceData->total > 0 && $activeGateway !== 'manual')
                    Pay now Rp<span id="payAmount">{{ number_format($priceData->total, 0, '.', '.') }}</span>
                  @else
                    Confirm Payment
                  @endif
                </button>
              </form>
            @else
              <div class="alert alert-success mb-0 mt-4">
                This invoice is already paid.
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
                {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d.m.Y @ h:i A') }}
              </span>
            </div>
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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
  </script>
  @if ($activeGateway === 'midtrans' && $config && $config->status === 1)
    <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ $config->client_key }}"></script>
  @elseif($activeGateway === 'midtrans' && $config)
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ $config->client_key }}"></script>
  @endif

  @if ($activeGateway === 'duitku' && $config && $config->status === 1)
    <script src="https://app-prod.duitku.com/lib/js/duitku.js"></script>
  @elseif($activeGateway === 'duitku' && $config)
    <script src="https://app-sandbox.duitku.com/lib/js/duitku.js"></script>
  @endif

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

      $('#pay-now-form').on('submit', function(e) {
        e.preventDefault();
        const paymentMethod = $('#payment_method_input').val();
        const amount = $(this).find('input[name="amount"]').val();

        if (!amount) {
          let wa = '{{ $company->wa }}';
          wa = wa.replace('+', '').replace(/^0/, '62');
          const url =
            `https://api.whatsapp.com/send?phone=${wa}&text=`;
          location.href = url;
          return;
        }

        $('#pay-now-btn').prop('disabled', true);

        $.ajax({
          url: $(this).attr('action'),
          type: 'POST',
          data: {
            payment_method: paymentMethod
          },
          success: function(data) {
            @if ($activeGateway === 'midtrans')
              snap.pay(data.token, {
                onSuccess: function(result) {
                  $successModal.css('display', 'flex').removeClass("d-none");
                },
                onPending: function(result) {
                  location.reload();
                },
                onError: function(result) {
                  location.reload();
                }
              });
            @elseif ($activeGateway === 'duitku')
              checkout.process(data.token, {
                successEvent: function(result) {
                  $successModal.css('display', 'flex').removeClass("d-none");
                },
                pendingEvent: function(result) {
                  location.reload();
                },
                errorEvent: function(result) {
                  location.reload();
                },
                closeEvent: function(result) {
                  location.reload();
                }
              });
            @else
              location.href = data.url;
            @endif

            $('#pay-now-btn').prop('disabled', false);
          },
          error: function(xhr, status, error) {
            $('#pay-now-btn').prop('disabled', false);
            alert("An error occurred. Please try again later.");
          }
        });
      });

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
            $("#payment_method_input").val(selectedVal);
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

      // Copy to clipboard
      $("button[title='Copy to clipboard']").on("click", function() {
        const text = $(this).prev().text().replace(/ {2,}/g, ' ').split('\n').filter(line => line.trim() !== '')
          .map(line => line.trim()).join('\n');

        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(text).then(function() {
            alert("Copied to clipboard!");
          }, function() {
            alert("Failed to copy to clipboard.");
          });
        } else {
          // Fallback method
          var textarea = document.createElement("textarea");
          textarea.value = text;
          document.body.appendChild(textarea);
          textarea.select();
          try {
            document.execCommand("copy");
            alert("Copied to clipboard!");
          } catch (err) {
            alert("Failed to copy to clipboard.");
          }
          document.body.removeChild(textarea);
        }
      });
    });
  </script>
</body>

</html>
