@extends('backend.layouts.app_new')

@section('title', 'Order')

@section('css')
  <!-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/animate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/datatables.css') }}"> -->
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/select2.css') }}">
@endsection

@section('main')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6 ps-0">
          <h3>Order</h3>
        </div>
        <div class="col-sm-6 pe-0">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">
                <svg class="stroke-icon">
                  <use href="{{ asset('assets/old/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item">Account</li>
            <li class="breadcrumb-item active">Order</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <div class="container-fluid user-management-page">
    <div class="row">
      <div class="col-8">
        <div class="container">
          <div class="mx-auto">
            <div class="row gx-4 gy-4">
              <!-- Left Column: Main Invoice Section -->
              <div class="custom-card" style="background-color: #ffffff">
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
                          <strong class="fw-medium text-black">{{ $settings->name }}</strong><br>
                          {{ $settings->address }}
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
                    <p class="fw-medium mb-1">{{ $invoice->admin->name }}</p>
                    <p class="mb-0 text-gray-500" style="white-space: normal; word-break: break-word">
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
                      <hr />
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
                      <hr />
                    @endif
                    @if ($priceData->discountCoupon > 0)
                      <div class="row mb-2">
                        <div class="col-6">Discount </div>
                        <div class="col-6 text-end">
                          -Rp{{ number_format($priceData->discountCoupon, 0, '.', '.') }}
                        </div>
                      </div>
                      <hr />
                    @endif
                    @if ($priceData->adminFee > 0)
                      <div class="row mb-2">
                        <div class="col-6">Admin Fee</div>
                        <div class="col-6 text-end">
                          Rp{{ number_format($priceData->adminFee, 0, '.', '.') }}
                        </div>
                      </div>
                      <hr />
                    @endif
                    <div class="row fw-medium">
                      <div class="col-6">Total incl. VAT</div>
                      <div class="col-6 text-end">
                        Rp{{ number_format(($priceData->total + $invoice->price_adon), 0, '.', '.') }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Right Column: Payment Sidebar -->
              <div class="col-12 col-lg-4">
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-4" >
        <div class="row" style="background-color: #ffffff">
          <form id="saveForm" method="POST" action="{{ route('dinetkan.invoice_dinetkan.detail_update') }}">
            @csrf
            <input type="hidden" name="no_invoice" value="{{ $invoice->no_invoice }}">
            <div class="mb-3">
              <label for="status">Status</label>
              <select class="form-select" name="status" id="status">
                <!-- <option value="0">NEW</option> -->
                <option value="1">ACTIVE</option>
                <!-- <option value="2">INACTIVE</option>
                <option value="3">OVERDUE</option>
                <option value="4">SUSPEND</option> -->
              </select>
            </div>
            <div class="mb-3">
              <label for="pay_from">Pay From</label>
              <select class="form-select" name="pay_from" id="pay_from">
                <option value="cash">Cash</option>
                <option value="bank">Bank Transfer</option>
                <option value="va">Virtual Account</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="status">Notes</label>
              <input class="form-control" name="notes" id="notes">
            </div>
            <div class="mb-3">
              <button class="btn btn-primary" type="submit">Update</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <!-- <script src="{{ asset('assets/old/radiusqu/dist/js/moment.js') }}"></script>
  <script src="{{ asset('assets/old/js/datatable/datatables/jquery.dataTables.min.js') }}"></script> -->
  <!-- <script src="{{ asset('assets/old/js/datatable/datatables/datatable.custom.js') }}"></script> -->
<script>
  
  // const baseurl = baseUrl.clone().pop().pop();
  const baseurl = "{{ url('/') }}" //baseUrl.clone().pop().pop().pop().pop();
    // $(document).ready(function() {
    //   const form = $('#saveForm');
    //   // Form Submission
    //   form.on('submit', function(e) {
    //     e.preventDefault();

    //     $.ajax({
    //       url: form.attr('action'),
    //       method: 'POST',
    //       data: form.serialize(),
    //       success: function(response) {
    //         toastr.success(response.message);
    //       },
    //       error: function(xhr) {
    //         const errors = xhr.responseJSON.errors;
    //         let message = '';

    //         for (const key in errors) {
    //           message += errors[key] + '\n';
    //         }

    //         toastr.error(message);
    //       }
    //     });
    //   });
    // });

</script>

@endpush
