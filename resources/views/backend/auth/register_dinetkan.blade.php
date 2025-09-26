<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Radiusqu">
  <meta name="author" content="Putra Garsel Interkoneksi">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Dinetkan Register</title>

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="{{ asset('assets/old/radiusqu/img/favicon.png') }}">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

  <!-- Bootstrap 5 -->
  <link rel="stylesheet" href="{{ asset('assets/old/css/vendors/bootstrap.css') }}">

  <!-- Optional Icons -->
  <link rel="stylesheet" href="{{ asset('assets/old/css/font-awesome.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/old/css/vendors/icofont.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/old/css/vendors/themify.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/old/css/vendors/flag-icon.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/old/css/vendors/feather-icon.css') }}">

  <!-- App CSS -->
  <link rel="stylesheet" href="{{ asset('assets/old/css/color-1.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/old/css/responsive.css') }}">

  <!-- Google Recaptcha -->
  <!-- <script src="https://www.google.com/recaptcha/enterprise.js?render=6Lcti6IqAAAAACcz7-9pRqmbkU3rtywGtenqF1TD"></script> -->

  <style>
    .show-hide svg {
      width: 20px;
      height: 20px;
    }

    .show-hide .show .eye-close {
      display: none;
    }

    .show-hide :not(.show) .eye-open {
      display: none;
    }

    .login-card {
      max-width: 600px;
      margin: 3rem auto;
      padding: 2rem;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 0.75rem 1.5rem rgba(18, 38, 63, 0.03);
    }

    .logo img {
      max-height: 50px;
      margin-bottom: 1rem;
    }
  </style>
</head>

<body class="bg-light">

  <div class="container">
    <div class="login-card">
      <div class="text-center logo">
        <img src="{{ asset('assets/old/radiusqu/img/logo.png') }}" alt="registerpage" class="img-fluid">
      </div>
      <div class="login-main">
        <form method="POST" action="{{ route('register_dinetkan') }}">
          @csrf
          <h3 class="text-center mb-3">Create a New Account</h3>
          <p class="text-center mb-4">Fill in the details to register</p>

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="first_name" class="form-label">First Name</label>
              <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required>
              @error('first_name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label for="last_name" class="form-label">Last Name</label>
              <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" required>
              @error('last_name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="email" class="form-label">Email</label>
              <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label for="whatsapp" class="form-label">WhatsApp</label>
              <input type="tel" name="whatsapp" id="whatsapp" class="form-control @error('whatsapp') is-invalid @enderror" value="{{ old('whatsapp') }}" required>
              @error('whatsapp')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="company_name" class="form-label">Company Name</label>
              <input type="text" name="company_name" id="company_name" class="form-control @error('company_name') is-invalid @enderror" value="{{ old('company_name') }}" required>
              @error('company_name')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label for="password" class="form-label">Password</label>
              <div class="input-group">
                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">Show</button>
                @error('password')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <div class="input-group">
              <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
              <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">Show</button>
            </div>
          </div>

          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
            <label class="form-check-label" for="terms">
              I agree to the <a href="#" class="text-primary">terms and conditions</a>.
            </label>
          </div>

          <button type="submit" class="btn btn-primary w-100">Create Account</button>

          <hr>

          <div class="text-center">
            <p class="mb-0">Already have an account? <a href="{{ route('login') }}" class="text-primary"><i class="fas fa-sign-in-alt me-1"></i> Login</a></p>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- JS Scripts -->
  <script src="{{ asset('assets/old/js/jquery.min.js') }}"></script>
  <script src="{{ asset('assets/old/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
  <script>
    function togglePassword(id) {
      const input = document.getElementById(id);
      if (input.type === 'password') {
        input.type = 'text';
      } else {
        input.type = 'password';
      }
    }
  </script>
</body>

</html>
