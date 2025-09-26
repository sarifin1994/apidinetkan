@extends('layouts.authentication.master')

@section('title', 'Reset Password')

@section('css')
@endsection

@section('css')
  <!-- loader starts-->
  <div class="loader-wrapper">
    <div class="loader">
      <div class="box"></div>
      <div class="box"></div>
      <div class="box"></div>
      <div class="box"></div>
      <div class="box"></div>
    </div>
  </div>
  <!-- loader ends-->
  <!-- tap on top starts-->
  <div class="tap-top"><i data-feather="chevrons-up"></i></div>
  <!-- tap on tap ends-->
  <!-- page-wrapper Start-->
  <div class="page-wrapper">
    <div class="container-fluid p-0">
      <div class="row">
        <div class="col-12">
          <div class="login-card login-dark">
            <div>
              <div><a class="logo" href="/"><img class="img-fluid for-light"
                    src="{{ asset('assets/images/logo/logo-1.png') }}" alt="looginpage"><img class="img-fluid for-dark"
                    src="{{ asset('assets/images/logo/logo.png') }}" alt="looginpage"></a></div>
              <div class="login-main">
                @if (session('status'))
                  <div class="alert bg-primary p-2" role="alert">
                    {{ session('status') }}
                  </div>
                @endif

                <form class="theme-form" method="POST" action="{{ route('password.email') }}">
                  @csrf
                  <h4>Reset Password</h4>
                  <div class="form-group">
                    <label class="col-form-label">Email Address</label>
                    <div class="form-input position-relative">
                      <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                        name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                        placeholder="test@gmail.com">
                      @error('email')
                        <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                        </span>
                      @enderror
                    </div>
                  </div>
                  <div class="row mb-0">
                    <button class="btn btn-primary btn-block w-100" type="submit">Send Password Reset Link </button>
                </form>
                <a href="{{ route('login') }}" class="text-center">
                  <p>
                    <i class="fa fa-long-arrow-left"></i>Back to Login Page
                  </p>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- page-wrapper Ends-->
@endsection

@section('scripts')
@endsection
