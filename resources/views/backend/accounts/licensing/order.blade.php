@extends('backend.layouts.app')

@section('title', 'License Order')

@section('css')
  <style>
    .invoice-header {
      background: linear-gradient(135deg, #6c757d, #495057);
      color: #fff;
    }

    .invoice-card {
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
      border-radius: 15px;
      overflow: hidden;
    }

    .table-striped-custom tbody tr:nth-of-type(even) {
      background-color: rgba(108, 117, 125, 0.05);
    }

    .btn-checkout {
      background: linear-gradient(135deg, #6c757d, #495057);
      transition: all 0.3s ease;
    }

    .btn-checkout:hover {
      transform: translateY(-3px);
      box-shadow: 0 7px 14px rgba(0, 0, 0, 0.15);
    }

    /* Dark Theme Compatibility */
    body.dark-only .invoice-header {
      background: linear-gradient(135deg, #171829, #10101c);
      color: #f8f9fa;
    }

    body.dark-only .table-striped-custom tbody tr:nth-of-type(even) {
      background-color: rgba(16, 16, 28, 0.1);
    }

    body.dark-only .btn-checkout {
      background: linear-gradient(135deg, #2c2c34, #10101c);
    }

    body.dark-only .btn-checkout:hover {
      box-shadow: 0 7px 14px rgba(255, 255, 255, 0.15);
    }
  </style>
@endsection

@section('css')
  <div class="container-fluid invoice-container py-5">
    <div class="container-xl">
      <div class="card invoice-card border-0">
        <!-- Invoice Header -->
        <div class="card-header invoice-header d-flex justify-content-between align-items-center p-4">
          <div>
            <img src="{{ asset('assets/radiusqu/img/logo-black.png') }}" style="max-width: 200px;" alt="Logo" />
          </div>
          <div class="text-end">
            <h4 class="mb-1 text-white">Invoice</h4>
            <p class="text-white-50 mb-0">Order #{{ auth()->user()->username }}</p>
            <small class="text-white-50">{{ \Carbon\Carbon::now()->format('d/m/Y') }}</small>
          </div>
        </div>

        <!-- Invoice Body -->
        <div class="card-body p-4">
          <div class="row mb-4">
            <div class="col-md-6">
              <h5 class="text-primary">Bill To</h5>
              <div>{{ auth()->user()->name }}</div>
              <div>{{ auth()->user()->email }}</div>
              <div>{{ auth()->user()->whatsapp }}</div>
            </div>
            <div class="col-md-6 text-end">
              <h5 class="text-primary">From</h5>
              <div>Radiusqu Radius</div>
              <div>PT. Putra Garsel Interkoneksi</div>
              <div>support@radiusqu.com</div>
            </div>
          </div>

          <div class="table-responsive custom-scrollbar">
            <table class="table-striped-custom table">
              <thead class="table">
                <tr>
                  <th>Description</th>
                  <th class="text-end">Qty</th>
                  <th class="text-end">Price</th>
                  <th class="text-end">Total</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <div class="fw-bold">Radiusqu {{ $license->name }} Monthly</div>
                    <small class="text-muted">
                      {{ $license->limit_nas ? number_format($license->limit_nas, 0, '.', '.') : 'Unlimited' }} NAS /
                      {{ number_format($license->limit_pppoe, 0, '.', '.') }} PPPoE Users /
                      {{ number_format($license->limit_hs, 0, '.', '.') }} Hotspot Users
                    </small>
                  </td>
                  <td class="text-end">1</td>
                  <td class="text-end">Rp{{ number_format($priceData->price, 0, '.', '.') }}</td>
                  <td class="text-end">Rp{{ number_format($priceData->price, 0, '.', '.') }}</td>
                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="3" class="fw-bold text-end">Subtotal:</td>
                  <td class="text-end">Rp{{ number_format($priceData->price, 0, '.', '.') }}</td>
                </tr>
                <tr>
                  <td colspan="3" class="fw-bold text-end">Kode Promo:</td>
                  <td class="text-end">
                    <div class="input-group">
                    <input class="form-control" name="coupon" id="coupon" value="{{$couponCode}}">
                    <a class="btn btn-primary btn-xs" id="cekPromo" style="text-align:center"
                    href="/admin/account/licensing/{{$license->id}}">Check</a>
                    </div>
                  </td>
                </tr>
                <tr>
                  <td colspan="3" class="fw-bold text-end">Total Promo:</td>
                  <td class="text-end">Rp{{ number_format($priceData->discountCoupon, 0, '.', '.') }}</td>
                </tr>
                @if ($priceData->ppn)
                  <tr>
                    <td colspan="3" class="fw-bold text-end">PPN ({{ $priceData->ppnPercentage }}%):</td>
                    <td class="text-end">Rp{{ number_format($priceData->ppn, 0, '.', '.') }}</td>
                  </tr>
                @endif
                @if ($priceData->adminFee)
                  <tr>
                    <td colspan="3" class="fw-bold text-end">Admin Fee :</td>
                    <td class="text-end">Rp{{ number_format($priceData->adminFee, 0, '.', '.') }}</td>
                  </tr>
                @endif
                <tr>
                  <td colspan="3" class="fw-bold text-primary h5 text-end">Total:</td>
                  <td class="text-primary h5 text-end">Rp{{ number_format($priceData->total, 0, '.', '.') }}</td>
                </tr>
              </tfoot>
            </table>
          </div>

          <div class="mt-4 text-end">
            <form action="{{ route('admin.invoice.place-order', $license->id) }}" method="POST">
              @csrf
              <input type="hidden" name="license_id" value="{{ $license->id }}" />
              <input type="hidden" name="couponCode" value="{{$couponCode}}">
              <button class="btn btn-checkout rounded-pill px-4 py-2 text-white">
                <i class="fas fa-shopping-cart me-2"></i> Complete Checkout
              </button>
            </form>
          </div>
        </div>

        <!-- Invoice Footer -->
        <div class="card-footer bg-light p-4">
          <div class="text-muted text-center">
            <small>Payment is due within 1 day of invoice receipt. Thank you for your business!</small>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script>
    document.getElementById("coupon").addEventListener("input", function() {
    let couponValue = this.value.trim(); // Ambil nilai input
    let baseUrl = "/admin/account/licensing/{{$license->id}}"; // Dapatkan URL dasar
    let cekPromoLink = document.getElementById("cekPromo");

    if (couponValue) {
        cekPromoLink.href = baseUrl + "/" + encodeURIComponent(couponValue);
    } else {
        cekPromoLink.href = baseUrl;
    }
});
  </script>
@endsection
