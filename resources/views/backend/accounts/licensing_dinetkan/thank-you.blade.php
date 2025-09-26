@extends('backend.layouts.app')

@section('title', 'Checkout')

@section('css')
  <style>
    .btn-gradient-checkout {
      background: linear-gradient(135deg, #4a90e2, #007aff);
      color: #ffffff;
      border: none;
      border-radius: 8px;
      transition: all 0.3s ease-in-out;
      font-size: 1rem;
    }

    .card.checkout {
      border-radius: 15px;
      background-color: #f9f9f9;
    }

    .text-secondary {
      color: #6c757d !important;
    }
  </style>
@endsection

@section('css')
  <div class="container-xl py-5">
    <div class="card checkout shadow-lg">
      <div class="card-header bg-light py-4 text-center">
        <img src="{{ asset('assets/radiusqu/img/logo-black.png') }}" style="width: 200px;" alt="Logo" />
        <h4 class="fw-bold text-primary mt-3">Thank You for Your Purchase!</h4>
        <p class="text-secondary">Order #{{ auth()->user()->username }} | {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
      </div>
      <div class="card-body px-md-5 px-3 py-4">
        <div class="mb-4 text-center">
          <img
            src="https://img.freepik.com/premium-vector/online-mobile-payment-banking-service-concept-payment-approved-payment-done-vector-illustration-flat-design-web-banner-mobile-app_98702-1311.jpg"
            alt="Payment Image" width="250">
        </div>
        <div class="text-center">
          <h5 class="fw-bold text-success">Your payment has been successfully processed. <i
              class="fas fa-check-circle"></i></h5>
          <h4 class="text-primary">Rp{{ number_format($invoice->price, 0, '.', '.') }}</h4>
        </div>
      </div>
      <div class="card-footer bg-light py-3 text-center">
        <p class="text-secondary">Check your license status:
          <a href="{{ route('admin.account.info.index') }}" class="text-primary">Go to Dashboard</a>
        </p>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script>
    // Additional JavaScript if needed
  </script>
@endsection
