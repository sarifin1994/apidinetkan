<!-- Page Header Start-->
<div class="page-header">
  <div class="header-wrapper row m-0">
    <div class="header-logo-wrapper col-auto p-0">
      <div class="logo-wrapper"><a href="#"><img class="img-fluid for-light"
            src="{{ asset('assets/old/radiusqu/img/logo.png') }}" alt=""><img class="img-fluid for-dark"
            src="{{ asset('assets/old/radiusqu/img/logo.png') }}" alt=""></a></div>
      <div class="toggle-sidebar">
        <svg class="sidebar-toggle">
          <use href="{{ asset('assets/old/svg/icon-sprite.svg#stroke-animation') }}"></use>
        </svg>
      </div>
    </div>
    <div class="left-header col-xxl-5 col-xl-6 col-md-4 box-col-6 horizontal-wrapper col-auto p-0">
      <div class="left-menu-header">
        <div class="form-group w-100">
          <div class="Typeahead Typeahead--twitterUsers">
            <span class="server-time demo-input Typeahead-input w-100 py-0">
              Server time: <strong>{{ date('d M Y H:i:s') }}</strong>
              <script>
                var refreshIntervalId = setInterval(function() {
                  var now = new Date();
                  var formattedDate = now.toLocaleString('en-GB', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                  }).replace(',', '');
                  $('.server-time strong').text(formattedDate);
                }, 1000);
              </script>
            </span>
          </div>
        </div>
      </div>
    </div>
    <div class="nav-right col-xxl-7 col-xl-6 box-col-6 pull-right right-header col-auto ms-auto p-0">
      <ul class="nav-menus">
        <li>
          <div class="mode">
            <svg>
              <use href="{{ asset('assets/old/svg/icon-sprite.svg#fill-dark') }}"></use>
            </svg>
          </div>
        </li>
        <li class="profile-nav onhover-dropdown p-0">
          <div class="d-flex align-items-center profile-media"><img class="b-r-10 img-40"
              src="{{ asset('assets/old/images/dashboard/profile.png') }}" alt="">
            <div class="flex-grow-1"><span>{{ ucfirst(auth()?->user()?->name) }}</span>
              <p class="mb-0">{{ auth()?->user()->role }}</p>
            </div>
          </div>
          <ul class="profile-dropdown onhover-show-div">
            <li>
              <a href="/{{ strtolower(auth()?->user()->role) }}/account/profile">
                <i data-feather="user"></i><span>My Profile </span>
              </a>
            </li>
            @if (session()->has('origin_id'))
              <!-- <li>
                <a href="javascript:void(0)"
                  onclick="event.preventDefault(); document.getElementById('switch-account-form').submit();">
                  <i data-feather="log-out"></i><span>Switch Back</span>
                </a>
              </li>
              <form action="#" method="POST" class="d-none" id="switch-account-form">
                @csrf
              </form> -->
            @endif
            <li>
              <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                  data-feather="log-in"> </i><span>Log out</span></a>
            </li>
            <form action="{{ route('logout') }}" method="POST" class="d-none" id="logout-form">
              @csrf
            </form>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</div>
<!-- Page Header Ends-->
