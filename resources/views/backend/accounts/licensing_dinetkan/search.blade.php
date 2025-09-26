<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>Invoice</title>
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
      <div class="col-12 col-lg-12">
        <div class="custom-card">
          <div class="d-flex align-items-start section-spacer">
            <div class="flex-grow-1">
              <form method="get" action="{{route('admin.invoice_dinetkan_search')}}">
                <div class="input-group mb-3">
                  <input type="text" name="dinetkan_user_id" id="dinetkan_user_id" class="form-control" 
                    placeholder="Dinetkan User_id" aria-label="Dinetkan User_id" aria-describedby="button-addon2"
                    value="{{$user_id}}">
                  <button class="btn btn-primary" type="submit" id="button-addon2"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-12">
        <div class="custom-card">
          <div class="d-flex align-items-start section-spacer">
            <div class="flex-grow-1">
              @if(count($invoice)<= 0)
              <span>Data Not Found</span>
              @endif

              @if(count($invoice)>0)
              <div class="section-spacer rounded border">
                <div class="row g-0 border-bottom fw-medium p-3" style="background-color: #f8f8fc;">
                  <div class="col-4">No Invoice</div>
                  <div class="col-4">Service</div>
                  <div class="col-4 text-end">Total</div>
                </div>
                <div class="p-3">
                  @foreach ($invoice as $inv)
                  <?php 
                  $ppn = $inv->ppn;
                  $price = $inv->price;
                  $totalppn=0;
                  if($inv->ppn > 0){
                    $totalppn = $inv->price * $inv->ppn / 100;
                  }
                  $ppn_otc = $inv->ppn_otc;
                  $price_otc = $inv->price_otc;
                  $totalppn_otc=0;
                  if($inv->ppn_otc > 0){
                    $totalppn_otc = $inv->price_otc * $inv->ppn_otc / 100;
                  }

                  $totalprice = $price + $totalppn + $price_otc + $totalppn_otc;

                  ?>
                    <div class="row mb-2">
                      <div class="col-4">
                        <a href="{{route('admin.invoice_dinetkan', $inv->no_invoice)}}" title="Pay" target="_blank">#{{$inv->no_invoice}}</a>
                      </div>
                      <div class="col-4">{{$inv->item}}</div>
                      <div class="col-4 text-end">
                        Rp{{ number_format($totalprice, 0, '.', '.') }}
                      </div
                    </div>
                  @endforeach
                  
                </div>
              </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  

  <!-- jQuery and Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous">
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

  </script>
</body>

</html>
