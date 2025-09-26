<!DOCTYPE html>
<html lang="en" @if (Route::currentRouteName() == 'admin.rtl_layout') dir="rtl" @endif>
<head>
  @include('layouts.simple.head')
  @include('layouts.simple.css')
  
  <!-- Embedded styles for optional responsiveness -->
  <style>
    /* On smaller screens, adjust positions if you like */
    @media (max-width: 576px) {
      .tap-top {
        bottom: 50px !important; /* or 40px/20px; tweak to your liking */
      }
    }
  </style>
</head>

<body>
  <!-- loader starts -->
  <div class="loader-wrapper">
    <div class="loader">
      <div class="box"></div>
      <div class="box"></div>
      <div class="box"></div>
      <div class="box"></div>
      <div class="box"></div>
    </div>
  </div>
  <!-- loader ends -->

  <!-- Tap-to-Top Button -->
  <div
    class="tap-top"
    style="
      position: fixed;
      bottom: 80px;
      right: 20px;
      z-index: 9999;
      cursor: pointer;
    "
  >
    <i data-feather="chevrons-up"></i>
  </div>

  <!-- WhatsApp Button + Work Hour Info -->
  <div 
    style="
      position: fixed;
      bottom: 10px;
      right: 20px;
      z-index: 9998;
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      gap: 4px;
    "
  >
    <!-- Floating WhatsApp Button (lower on the page) -->
    <a
      href="https://wa.me/+6289528111112"
      target="_blank"
      rel="noopener noreferrer"
      class="badge rounded-pill badge-success d-flex align-items-center"
      style="
        position: relative;
        z-index: 9998;
      "
    >
      <!-- WhatsApp Icon -->
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="16"
        height="16"
        fill="currentColor"
        viewBox="0 0 16 16"
        class="me-1"
      >
        <path
          d="M13.601 2.368A7.717 7.717 0 0 0 8 .278 7.661 7.661 0 0 0 2.393 2.37 7.557 7.557 0 0 0 .276 8.005 7.623 7.623 0 0 0 1.53 12.43L.238 15.658l3.333-1.08a7.623 7.623 0 0 0 4.433 1.26h.003a7.557 7.557 0 0 0 5.636-2.115 7.711 7.711 0 0 0 0-10.654zM8 14.345c-1.152 0-2.277-.298-3.268-.863l-.234-.138-1.975.64.657-1.925-.156-.242A5.606 5.606 0 0 1 2.398 8 5.646 5.646 0 0 1 8 2.365c1.505 0 2.92.586 3.986 1.65a5.701 5.701 0 0 1 0 8.048A5.654 5.654 0 0 1 8 14.345z"
        ></path>
        <path
          d="M10.562 9.947c-.14-.07-.82-.406-.95-.453-.13-.047-.224-.07-.318.07-.093.14-.365.453-.446.546-.08.093-.163.106-.303.035-.14-.07-.59-.217-1.123-.693-.415-.37-.695-.826-.775-.967-.08-.14-.008-.217.06-.288.06-.06.14-.163.21-.244.07-.082.093-.14.14-.234.046-.093.024-.175-.012-.244-.035-.07-.318-.793-.435-1.087-.115-.288-.23-.248-.318-.248-.082 0-.175-.012-.268-.012-.092 0-.244.035-.372.163-.13.128-.488.477-.488 1.163 0 .686.498 1.35.57 1.443.07.093.977 1.49 2.37 2.087.33.143.588.228.79.3.33.105.63.09.868.055.264-.037.82-.333.937-.654.116-.32.116-.593.082-.652-.034-.06-.13-.093-.27-.163z"
        ></path>
      </svg>
      Contact Us
    </a>
  </div>

  <!-- page-wrapper Start -->
  <div class="page-wrapper compact-wrapper" id="pageWrapper">
    @include('layouts.simple.header')
    <div class="page-body-wrapper">
      @include('backend.layouts.navbar')

      <div class="page-body">
        @yield('main_content')

        <div id="loadingSpinner" class="spinner-overlay">
          <div class="spinner"></div>
        </div>
      </div>

      @include('layouts.simple.footer')
    </div>
  </div>

  @include('layouts.simple.script')
  @include('inc.alerts')
</body>
</html>
