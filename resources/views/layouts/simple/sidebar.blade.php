        <!-- Page Sidebar Start-->
        <div class="sidebar-wrapper" data-layout="fill-svg">
          <div>
            <div class="logo-wrapper"><a href="#"><img class="img-fluid"
                  src="{{ asset('assets/old/images/logo/logo.png') }}" alt=""></a>
              <div class="toggle-sidebar">
                <svg class="sidebar-toggle">
                  <use href="{{ asset('assets/old/svg/icon-sprite.svg#toggle-icon') }}"></use>
                </svg>
              </div>
            </div>
            <div class="logo-icon-wrapper"><a href="#"><img class="img-fluid"
                  src="{{ asset('assets/old/images/logo/logo-icon.png') }}" alt=""></a></div>
            <nav class="sidebar-main">
              <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
              <div id="sidebar-menu">
                <ul class="sidebar-links" id="simple-bar">
                  <li class="back-btn"><a href="#"><img class="img-fluid"
                        src="{{ asset('assets/old/images/logo/logo-icon.png') }}" alt=""></a>
                    <div class="mobile-back text-end"><span>Back</span><i class="fa fa-angle-right ps-2"
                        aria-hidden="true"></i></div>
                  </li>
                  <li class="pin-title sidebar-main-title">
                    <div>
                      <h6>Pinned</h6>
                    </div>
                  </li>

                  @include('layouts.simple.menus.' . strtolower(auth()->user()->role))
                </ul>
              </div>
              <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
            </nav>
          </div>
        </div>
        <!-- Page Sidebar Ends-->
