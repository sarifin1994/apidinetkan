<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Invoice #{{ $billing->id }} | {{ config('app.name') }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
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
                    <h1 class="invoice-title"># {{ $billing->dinetkan_user_id }}_{{ $billing->id }}</h1>
                    <p class="invoice-subtitle">
                      Issued date: {{ \Carbon\Carbon::parse($billing->invoice_date)->format('d.m.Y') }}
                    </p>
                  </div>
                  @if ($billing->status == "unpaid")
                    <span class="badge-pill">Unpaid</span>
                  @else
                    <span class="badge-pill paid">Paid</span>
                  @endif
                </div>
              </div>
            </div>
            <!-- From / To -->
            <div class="row section-spacer"> </div>

            <!-- Items Table -->
            <div class="section-spacer rounded border">
              <div class="row g-0 border-bottom fw-medium p-3" style="background-color: #f8f8fc;">
                <div class="col-6">ITEM</div>
                <div class="col-6 text-end">PRICE</div>
              </div>
              <div class="p-3">
                <div class="row mb-2">
                  <div class="col-6">Total Pembayaran {{ $billing->month}} {{ $billing->year }}</div>
                  <div class="col-6 text-end">
                    Rp {{ number_format($total, 0, '.', '.') }}
                  </div>
                </div>
                <hr />

                <div class="row mb-2">
                  <div class="col-6">Detail</div>
                  <div class="col-6 text-end">
                    
                  </div>
                </div>
                <hr />

                <div class="row mb-2">
                  <div class="col-6">Total PPN</div>
                  <div class="col-6 text-end">
                    Rp {{ number_format($billing->total_ppn, 0, '.', '.') }}
                  </div>
                </div>
                <hr />
                
                <div class="row mb-2">
                  <div class="col-6">Total BHP</div>
                  <div class="col-6 text-end">
                    Rp {{ number_format($billing->total_bhp, 0, '.', '.') }}
                  </div>
                </div>
                <hr />
                
                <div class="row mb-2">
                  <div class="col-6">Total USO</div>
                  <div class="col-6 text-end">
                    Rp {{ number_format($billing->total_uso, 0, '.', '.') }}
                  </div>
                </div>
                <hr />
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
              <span>Rp{{ number_format($total, 0, '.', '.') }}</span>
            </div>
            <div class="d-flex justify-content-between">
              <span>Payment method fee:</span>
              <span>Rp<span id="fee">0</span></span>
            </div>
            <hr />
            <div class="d-flex justify-content-between fw-semibold fs-5 mt-3">
              <span>Total Charge</span>
              <span>Rp<span id="charge">{{ number_format($total, 0, '.', '.') }}</span></span>
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
            @if ($billing->status === 'unpaid')  
                <div class="fs-5 mt-3">
                  <div class="fs-5 mt-3 {{ $billing->virtual_account ? '' : 'd-none' }}" id="div_va">      
                    <h3 class="h5 fw-semibold">Virtual Account</h3> 
                    <div>
                      <span id="bank_name">
                        @if($billing->bank_name)
                          {{ $billing->bank_name }}
                        @endif
                      </span>
                      <br>
                      <span id="va">
                        @if($billing->virtual_account)
                          {{ $billing->virtual_account }}
                        @endif
                      </span>
                      <button class="btn btn-outline-primary btn-sm ms-2" onclick="copyToClipboard('va')">
                        Copy
                      </button>
                    </div>
                    <hr />   
                  </div> 

                   
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
                
                <input type="hidden" name="billing_id" id="billing_id" value="{{ $billing->id }}" />
                <button class="btn btn-custom-primary w-100 mt-4 py-3" id="pay-now-btn">
                  @if ($total > 0)
                    Pay now Rp <span id="payAmount">{{ number_format($total, 0, '.', '.') }}</span>
                  @else
                    Confirm Payment
                  @endif
                </button>
            @else
              <div class="alert alert-success mb-0 mt-4">
                This invoice is already paid.
              </div>
            @endif

            <p class="mb-0 mt-4 text-center text-sm text-gray-500">
              Payment securely processed by <strong>Radiusqu</strong>
            </p>
          </div>

          <!-- Timeline -->
          <div class="custom-card">
            <h3 class="h6 fw-semibold mb-4">Timeline</h3>
            <div class="d-flex align-s-center mb-3">
              <div class="rounded-circle me-3" style="background-color: #7b79ff; width: 0.5rem; height: 0.5rem;">
              </div>
              <div class="flex-grow-1 text-sm">
                Invoice created
              </div>
              <span class="text-sm text-gray-500">
                {{ \Carbon\Carbon::parse($billing->invoice_date)->format('d.m.Y @ h:i A') }}
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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    const baseurl = "{{ url('/') }}";
    $('#pay-now-btn').on('click', function(e) {
      const billing_id = $('#billing_id').val();
      const payment_method = document.getElementById('payment_method').value;
      const bank_name = $('#payment_method option:selected').text();
      // console.log(billing_id);
      // console.log(payment_method);
      if(payment_method == '' || payment_method == null || payment_method == 'undefined' || payment_method == 0){
        alert('virtual accoun didnt select');
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
          url: baseurl + "/admin/billing/member_dinetkan/mapping_service_pay/generate",
          type: 'POST',
          data: {
            billing_id: billing_id,
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
            location.reload();
            Swal.close();
          }
        });
    });
  </script>
  <script>
    function copyToClipboard(elementId) {
      const text_r = document.getElementById(elementId).innerText;
      const text = text_r.trim();
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(() => {
          alert('Copied to clipboard!');
        }).catch(() => {
          fallbackCopy(text);
        });
      } else {
        fallbackCopy(text);
      }
    }

    function fallbackCopy(text) {
      const tempInput = document.createElement('input');
      tempInput.value = text;
      document.body.appendChild(tempInput);
      tempInput.select();
      tempInput.setSelectionRange(0, 99999); // for mobile
      document.execCommand('copy');
      document.body.removeChild(tempInput);
      alert('Copied to clipboard!');
    }
  </script>
</body>

</html>
