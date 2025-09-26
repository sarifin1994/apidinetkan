 <!-- Menu Navigation starts -->
 <nav>
     <div class="app-logo">
         <a class="logo d-inline-block" href="{{ url('/') }}">
             <img src="{{ asset('assets/images/radiusqu.png') }}" alt="Radiusqu">
         </a>

         <span class="bg-light-primary toggle-semi-nav d-flex-center">
             <i class="ti ti-chevron-right"></i>
         </span>

         <div class="d-flex align-items-center nav-profile p-3">
             <span class="h-45 w-45 d-flex-center b-r-10 position-relative bg-danger m-auto">
                 <img alt="avatar" class="img-fluid b-r-10" src="{{ asset('assets_new/images/avatar/avatar.jpg') }}">
                 <span class="position-absolute top-0 end-0 p-1 bg-success border border-light rounded-circle"></span>
             </span>
             <div class="flex-grow-1 ps-2">
                 <h6 class="text-primary mb-0"> {{ multi_auth()->name }}</h6>
                 <p class="text-muted f-s-12 mb-0">{{ multi_auth()->role }}</p>
             </div>


             <div class="dropdown profile-menu-dropdown">
                 <a aria-expanded="false" data-bs-auto-close="true" data-bs-placement="top" data-bs-toggle="dropdown"
                     role="button">
                     <i class="ti ti-settings fs-5"></i>
                 </a>
                 <ul class="dropdown-menu">
                     @if (multi_auth()->role === 'Admin')
                         <li class="dropdown-item">
                             <a class="f-w-500" href="/account">
                                 <i class="ph-bold  ph-user pe-1 f-s-20"></i> Account
                             </a>
                         </li>
                     @endif
                     <li class="dropdown-item">
                         <a class="f-w-500" href="/password">
                             <i class="ph-duotone  ph-lock pe-1 f-s-20"></i> Password
                         </a>
                     </li>
                     <li class="dropdown-item">
                         <form id="logout-form2" action="{{ route('logout') }}" method="POST" style="display: none;">
                             @csrf
                         </form>
                         <a class="mb-0 text-danger" href="javascript:void(0)"
                             onclick="event.preventDefault(); document.getElementById('logout-form2').submit();">
                             <i class="ph-duotone  ph-sign-out pe-1 f-s-20"></i> Log Out
                         </a>
                     </li>
                 </ul>
             </div>

         </div>
     </div>
     <div class="app-nav" id="app-simple-bar">
         <ul class="main-nav p-0 mt-2">
             @if (in_array(multi_auth()->role, ['Owner', 'Admin', 'Teknisi', 'Kasir', 'Mitra']))
                 <li class="no-sub {{ request()->is('dashboard') ? 'active' : '' }}">
                     <a class="nav-link d-inline-flex align-items-center gap-2" href="/">
                         <i class="ti ti-home"></i>
                         <span>Dashboard</span>
                     </a>
                 </li>
             @endif
             @if (in_array(multi_auth()->role, ['Admin']) && multi_auth()->is_dinetkan == 1)
                <li class="no-sub {{ request()->is('admin/account/info_dinetkan') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->is('admin/account/info_dinetkan') ? 'active' : '' }} d-inline-flex align-items-center gap-2"
                        href="{{ route('admin.account.info_dinetkan.index') }}">
                        <i class="ti ti-user"></i>
                        <span>Info Akun</span>
                    </a>
                </li>
                <li class="no-sub {{ request()->is('admin/account/invoice_dinetkan/order') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->is('admin/account/invoice_dinetkan/order') ? 'active' : '' }} d-inline-flex align-items-center gap-2"
                        href="{{ route('admin.account.invoice_dinetkan.order') }}">
                        <i class="ti ti-server-cog"></i>
                        <span>Service</span>
                    </a>
                </li>
                <li class="no-sub {{ request()->is('admin/account/invoice_dinetkan') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->is('admin/account/invoice_dinetkan') ? 'active' : '' }} d-inline-flex align-items-center gap-2"
                        href="{{ route('admin.account.invoice_dinetkan.index') }}">
                        <i class="ti ti-receipt-2"></i>
                        <span>Tagihan Pemakaian Service</span>
                    </a>
                </li>
                <li class="no-sub {{ request()->is('admin/account/mrtg') ? 'active' : '' }}">
                    <a class="nav-link {{ request()->is('admin/account/mrtg') ? 'active' : '' }} d-inline-flex align-items-center gap-2"
                        href="{{ route('admin.account.mrtg.index') }}">
                        <i class="ti ti-chart-histogram"></i>
                        <span>MRTG</span>
                        </a>
                </li>
             @endif
             @if (in_array(multi_auth()->role, ['Admin']) && multi_auth()->is_dinetkan == 1 && multi_auth()->status != 2)
                @php
                    $isActive = request()->is('admin/billing/member_dinetkan/index') ||
                                request()->is('admin/product_dinetkan') ||
                                request()->is('admin/billing/member_dinetkan/mapping_service');
                @endphp
             @endif
            
            
         </ul>
     </div>

     <div class="menu-navs">
         <span class="menu-previous"><i class="ti ti-chevron-left"></i></span>
         <span class="menu-next"><i class="ti ti-chevron-right"></i></span>
     </div>

 </nav>
 <!-- Menu Navigation ends -->
