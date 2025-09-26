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
                 <li class="{{ request()->is('admin/account*') ? 'active' : '' }}">
                     <a class="nav-link d-inline-flex align-items-center gap-2"
                         aria-expanded="{{ request()->is('admin/account*') ? 'true' : 'false' }}"
                         data-bs-toggle="collapse" href="#dashboard">
                         <i class="ti ti-user"></i>
                         mitra
                     </a>
                     <ul class="collapse {{ request()->is('admin/account*') ? 'show' : '' }}" id="dashboard">
                         <li><a class="nav-link {{ request()->is('admin/account/info_dinetkan') ? 'active' : '' }}"
                                 href="{{ route('admin.account.info_dinetkan.index') }}">Info Akun</a></li>
                         <li><a class="nav-link {{ request()->is('admin/account/invoice_dinetkan/order') ? 'active' : '' }}"
                                 href="{{ route('admin.account.invoice_dinetkan.order') }}">Service</a></li>
                         <li><a class="nav-link {{ request()->is('admin/account/invoice_dinetkan') ? 'active' : '' }}"
                                 href="{{ route('admin.account.invoice_dinetkan.index') }}">Tagihan Pemakaian
                                 Bandwith</a></li>
                         <li><a class="nav-link {{ request()->is('admin/account/mrtg') ? 'active' : '' }}"
                                 href="{{ route('admin.account.mrtg.index') }}">MRTG</a></li>
                     </ul>
                 </li>
             @endif
             @if (in_array(multi_auth()->role, ['Admin']) && multi_auth()->is_dinetkan == 1 && multi_auth()->status != 2)
                @php
                    $isActive = request()->is('admin/billing/member_dinetkan/index') ||
                                request()->is('dmin/product_dinetkan') ||
                                request()->is('admin/billing/member_dinetkan/mapping_service');
                @endphp

                <li class="{{ $isActive ? 'active' : '' }}">
                    <a class="nav-link d-inline-flex align-items-center gap-2"
                    aria-expanded="{{ $isActive ? 'true' : 'false' }}"
                    data-bs-toggle="collapse" href="#admin_account_report">
                        <i class="ti ti-report"></i>
                        Report Mitra
                    </a>
                    <ul class="collapse {{ $isActive ? 'show' : '' }}"
                        id="admin_account_report">
                        <li>
                            <a class="nav-link {{ request()->is('admin/account/invoice_dinetkan/order') ? 'active' : '' }}" 
                                href="{{ route('admin.product_dinetkan.index') }}">
                                Profile PPPOE
                            </a>
                        </li>
                        <li>
                            <a class="nav-link {{ request()->is('admin/account/billing/member_dinetkan/index') ? 'active' : '' }}" 
                                href="{{ route('admin.billing.member_dinetkan.index') }}">
                                Pelanggan
                            </a>
                        </li>
                        <li>
                        <a class="nav-link {{ request()->is('admin/account/invoice_dinetkan') ? 'active' : '' }}" 
                            href="{{ route('admin.billing.member_dinetkan.mapping_service') }}">
                            Report
                        </a>
                        </li>
                    </ul>
                </li>

             @endif
             @if (in_array(multi_auth()->role, ['Admin']) && multi_auth()->ext_role == 'dinetkan')
                 <li class="{{ request()->is('dinetkan*') ? 'active' : '' }}">
                     <a class="nav-link d-inline-flex align-items-center gap-2"
                         aria-expanded="{{ request()->is('dinetkan*') ? 'true' : 'false' }}" data-bs-toggle="collapse"
                         href="#dinetkan">
                         <i class="ti ti-users"></i>
                         Mitra
                     </a>
                     <ul class="collapse {{ request()->is('dinetkan*') ? 'show' : '' }}" id="dinetkan">
                         <li><a class="nav-link {{ request()->is('dinetkan/users_dinetkan') ? 'active' : '' }}"
                                 href="{{ route('dinetkan.users_dinetkan') }}">User</a></li>
                         <li><a class="nav-link {{ request()->is('dinetkan/invoice_dinetkan/order') ? 'active' : '' }}"
                                 href="{{ route('dinetkan.invoice_dinetkan.order') }}">Order</a></li>
                         <li><a class="nav-link {{ request()->is('dinetkan/invoice_dinetkan') ? 'active' : '' }}"
                                 href="{{ route('dinetkan.invoice_dinetkan.index') }}">Tagihan Pemakaian Bandwith</a>
                         </li>
                         <li><a class="nav-link {{ request()->is('dinetkan/license_dinetkan') ? 'active' : '' }}"
                                 href="{{ route('dinetkan.license_dinetkan') }}">Service</a></li>
                         <li><a class="nav-link {{ request()->is('dinetkan/master_pop') ? 'active' : '' }}"
                                 href="{{ route('dinetkan.master_pop') }}">Master POP</a></li>
                         <li><a class="nav-link {{ request()->is('dinetkan/master_metro') ? 'active' : '' }}"
                                 href="{{ route('dinetkan.master_metro') }}">Master Metro</a></li>
                         <li><a class="nav-link {{ request()->is('dinetkan/master_mikrotik') ? 'active' : '' }}"
                                 href="{{ route('dinetkan.master_mikrotik') }}">Master Mikrotik</a></li>
                         <li><a class="nav-link {{ request()->is('dinetkan/settings_dinetkan') ? 'active' : '' }}"
                                 href="{{ route('dinetkan.settings_dinetkan') }}">Settings</a></li>
                         <li><a class="nav-link {{ request()->is('dinetkan/whatsapp') ? 'active' : '' }}"
                                 href="{{ route('dinetkan.whatsapp.index') }}">Whatsapp</a></li>
                         <li><a class="nav-link {{ request()->is('dinetkan/keuangan') ? 'active' : '' }}"
                                 href="{{ route('dinetkan.keuangan.index') }}">Keuangan</a></li>
                         <li><a class="nav-link {{ request()->is('dinetkan/users_dinetkan_service') ? 'active' : '' }}"
                                 href="{{ route('dinetkan.users_dinetkan_service') }}">Report Service Mitra</a></li>
                         <li><a class="nav-link {{ request()->is('dinetkan/admin') ? 'active' : '' }}"
                                 href="/dinetkan/admin">Anggota Admin</a></li>
                     </ul>
                 </li>
             @endif
             @if (in_array(multi_auth()->role, ['Owner']))
                 <li class="no-sub {{ request()->is('license*') ? 'active' : '' }}">
                     <a class="nav-link d-inline-flex align-items-center gap-2" href="/license">
                         <i class="ti ti-license"></i>
                         License
                     </a>
                 </li>
                 <li class="no-sub {{ request()->is('user*') ? 'active' : '' }}">
                     <a class="nav-link d-inline-flex align-items-center gap-2" href="/user">
                         <i class="ti ti-users"></i>
                         Manage Users
                     </a>
                 </li>
                 <li class="no-sub {{ request()->is('withdraw*') ? 'active' : '' }}">
                     <a class="nav-link d-inline-flex align-items-center gap-2" href="/withdraw">
                         <i class="ti ti-wallet"></i>
                         Withdraw
                     </a>
                 </li>
                 <li class="no-sub {{ request()->is('whatsapp*') ? 'active' : '' }}">
                     <a class="nav-link d-inline-flex align-items-center gap-2" href="/whatsapp">
                         <i class="ti ti-brand-whatsapp"></i>
                         Whatsapp
                     </a>
                 </li>
                 <li class="{{ request()->is('setting*') ? 'active' : '' }}">
                     <a class="nav-link d-inline-flex align-items-center gap-2"
                         aria-expanded="{{ request()->is('setting*') ? 'true' : 'false' }}" data-bs-toggle="collapse"
                         href="#server_setting">
                         <i class="ti ti-server"></i>
                         Setting Server
                     </a>
                     <ul class="collapse {{ request()->is('setting*') ? 'show' : '' }}" id="server_setting">
                         <li><a class="nav-link {{ request()->is('setting/vpn') ? 'active' : '' }}"
                                 href="/setting/vpn">VPN CHR</a></li>
                         <li><a class="nav-link {{ request()->is('setting/wa') ? 'active' : '' }}"
                                 href="/setting/wa">WA Gateway</a></li>
                         <li><a class="nav-link {{ request()->is('setting/payment_owner') ? 'active' : '' }}"
                                 href="/setting/payment_owner">Payment Gateway</a></li>
                         <li><a class="nav-link {{ request()->is('setting/payment_owner/duitku_log') ? 'active' : '' }}"
                                 href="/setting/payment_owner/duitku_log">Duitku Log</a></li>
                     </ul>
                 </li>
                 <li class="no-sub {{ request()->is('logs*') ? 'active' : '' }}">
                     <a class="nav-link d-inline-flex align-items-center gap-2" href="/logs">
                         <i class="ti ti-list"></i>
                         Laravel Logs
                     </a>
                 </li>
             @endif
             @if (multi_auth()->role === 'Admin' || multi_auth()->role === 'Teknisi')
                 <li class="no-sub {{ request()->is('olt*') ? 'active' : '' }}">
                     <a class="nav-link d-inline-flex align-items-center gap-2" href="/olt">
                         <i class="ti ti-router"></i>
                         OLT
                     </a>
                 </li>
                 <li class="{{ request()->is('hotspot/') ? 'active' : '' }}">
                     <a class="nav-link d-inline-flex align-items-center gap-2"
                         aria-expanded="{{ request()->is('hotspot/') ? 'true' : 'false' }}" data-bs-toggle="collapse"
                         href="#nav_hotspot">
                         <i class="ti ti-wifi"></i>
                         Hotspot
                     </a>
                     <ul class="collapse {{ request()->is('hotspot/') ? 'show' : '' }}" id="nav_hotspot">
                         <li>
                             <a class="nav-link {{ request()->is('hotspot_user') ? 'active' : '' }}"
                                 href="/hotspot_user">User</a>
                         </li>
                         <li>
                             <a class="nav-link {{ request()->is('hotspot_online') ? 'active' : '' }}"
                                 href="/hotspot_online">Online</a>
                         </li>
                         @if (multi_auth()->role === 'Admin')
                             <li>
                                 <a class="nav-link {{ request()->is('hotspot_profile') ? 'active' : '' }}"
                                     href="/hotspot_profile">Profile</a>
                             </li>
                         @endif
                     </ul>
                 </li>
             @endif
             @if (in_array(multi_auth()->role, ['Mitra']))
                 <!-- <li class="no-sub {{ request()->is('widrawal*') ? 'active' : '' }}">
                     <a class="nav-link d-inline-flex align-items-center gap-2" href="/widrawal">
                        <i class="fa-solid fa-money-bill-transfer"></i>
                         Widrawal
                     </a>
                 </li> -->
                 <li class="{{ request()->is('widrawal*') ? 'active' : '' }}">
                     <a class="nav-link d-inline-flex align-items-center gap-2"
                         aria-expanded="{{ request()->is('widrawal*') ? 'true' : 'false' }}" data-bs-toggle="collapse"
                         href="#nav_widrawal">
                         <i class="fa-solid fa-money-bill-transfer"></i>
                         Widrawal
                     </a>
                     <ul class="collapse {{ request()->is('widrawal*') ? 'show' : '' }}" id="nav_widrawal">
                         <li>
                             <a class="nav-link {{ request()->is('widrawal') ? 'active' : '' }}"
                                 href="/widrawal">Bank Account</a>
                         </li>
                         <li>
                             <a class="nav-link {{ request()->is('widrawal/history') ? 'active' : '' }}"
                                 href="/widrawal/history">Balance History</a>
                         </li>
                     </ul>
                 </li>
                 <li class="{{ request()->is('kemitraan*') ? 'active' : '' }}">
                     <a class="nav-link d-inline-flex align-items-center gap-2"
                         aria-expanded="{{ request()->is('kemitraan*') ? 'true' : 'false' }}" data-bs-toggle="collapse"
                         href="#nav_kemitraan">
                         <i class="fa-solid fa-handshake"></i>
                         Kemitraan
                     </a>
                     <ul class="collapse {{ request()->is('kemitraan*') ? 'show' : '' }}" id="nav_kemitraan">
                         <li>
                             <a class="nav-link {{ request()->is('kemitraan/users') ? 'active' : '' }}"
                                 href="/kemitraan/users">User</a>
                         </li>
                         <li>
                             <a class="nav-link {{ request()->is('kemitraan/keuangan_dinetkan') ? 'active' : '' }}"
                                 href="/kemitraan/keuangan_dinetkan">Keuangan</a>
                         </li>
                         <li>
                             <a class="nav-link {{ request()->is('kemitraan/invoice') ? 'active' : '' }}"
                                 href="/kemitraan/invoice">Invoice</a>
                         </li>
                     </ul>
                 </li>
             @endif
             @if (in_array(multi_auth()->role, ['Admin', 'Teknisi', 'Kasir', 'Mitra']))
                 <li class="{{ request()->is('pppoe/') ? 'active' : '' }}">
                     <a class="nav-link d-inline-flex align-items-center gap-2"
                         aria-expanded="{{ request()->is('pppoe/') ? 'true' : 'false' }}" data-bs-toggle="collapse"
                         href="#nav_pppoe">
                         <i class="ti ti-world"></i>
                         PPPoE
                     </a>
                     <ul class="collapse {{ request()->is('pppoe/') ? 'show' : '' }}" id="nav_pppoe">
                         <li>
                             <a class="nav-link {{ request()->is('pppoe_user') ? 'active' : '' }}"
                                 href="/pppoe_user">User</a>
                         </li>
                         @if (in_array(multi_auth()->role, ['Admin', 'Teknisi']))
                             <li>
                                 <a class="nav-link {{ request()->is('pppoe_online') ? 'active' : '' }}"
                                     href="/pppoe_online">Online</a>
                             </li>
                             <li>
                                 <a class="nav-link {{ request()->is('pppoe_offline') ? 'active' : '' }}"
                                     href="/pppoe_offline">Offline</a>
                             </li>
                             <li>
                                 <a class="nav-link {{ request()->is('pppoe_profile') ? 'active' : '' }}"
                                     href="/pppoe_profile">Profile</a>
                             </li>
                         @endif
                     </ul>
                 </li>
             @endif
             @if (in_array(multi_auth()->role, ['Admin', 'Kasir']) || (multi_auth()->role === 'Mitra' && multi_auth()->billing === 1))
                 <li class="{{ request()->is('invoice*') ? 'active' : '' }}">
                     <a class="nav-link d-inline-flex align-items-center gap-2"
                         aria-expanded="{{ request()->is('invoice*') ? 'true' : 'false' }}" data-bs-toggle="collapse"
                         href="#invoice">
                         <i class="ti ti-file-invoice"></i>
                         Invoice
                     </a>
                     <ul class="collapse {{ request()->is('invoice*') ? 'show' : '' }}" id="invoice">
                         <li> <a class="nav-link {{ request()->is('invoice/unpaid') ? 'active' : '' }}"
                                 href="/invoice/unpaid">Unpaid</a></li>
                         <li> <a class="nav-link {{ request()->is('invoice/paid') ? 'active' : '' }}"
                                 href="/invoice/paid">Paid</a></li>
                     </ul>
                 </li>
             @endif
             @if (in_array(multi_auth()->role, ['Admin', 'Kasir', 'Mitra']))
                 <li class="{{ request()->is('keuangan*') ? 'active' : '' }}">
                     <a class="nav-link d-inline-flex align-items-center gap-2"
                         aria-expanded="{{ request()->is('keuangan*') ? 'true' : 'false' }}"
                         data-bs-toggle="collapse" href="#keuangan">
                         <i class="ti ti-moneybag"></i>
                         Keuangan
                     </a>
                     <ul class="collapse {{ request()->is('keuangan*') ? 'show' : '' }}" id="keuangan">
                         @if (in_array(multi_auth()->role, ['Admin', 'Kasir']))
                             <li><a class="nav-link {{ request()->is('keuangan/transaksi') ? 'active' : '' }}"
                                     href="/keuangan/transaksi">Transaksi</a></li>
                             @if (\App\Models\Setting\Midtrans::where('shortname', multi_auth()->shortname)->first())
                                 @if (\App\Models\Setting\Midtrans::where('shortname', multi_auth()->shortname)->first()->status === 1)
                                     <li> <a class="nav-link {{ request()->is('keuangan/midtrans') ? 'active' : '' }}"
                                             href="/keuangan/midtrans">Midtrans</a></li>
                                 @endif
                             @endif
                             @if (\App\Models\Setting\Mduitku::where('shortname', multi_auth()->shortname)->first()?->status === 1)
                                 <li><a class="nav-link {{ request()->is('keuangan/duitku') ? 'active' : '' }}"
                                         href="/keuangan/duitku">Duitku</a></li>
                             @endif
                         @endif
                         <li><a class="nav-link {{ request()->is('keuangan/mitra') ? 'active' : '' }}"
                                 href="/keuangan/mitra">Mitra</a></li>
                         <li><a class="nav-link {{ request()->is('keuangan/hotspot') ? 'active' : '' }}"
                                 href="/keuangan/hotspot">Hotspot</a></li>
                         <li> <a class="nav-link {{ request()->is('keuangan/kategori') ? 'active' : '' }}"
                                 href="/keuangan/kategori">Kategori</a></li>
                     </ul>
                 </li>
             @endif
             @if (multi_auth()->role === 'Admin' || multi_auth()->role === 'Teknisi')
                 <li class="{{ request()->is('mapping*') ? 'active' : '' }}">
                     <a class="nav-link d-inline-flex align-items-center gap-2"
                         aria-expanded="{{ request()->is('mapping*') ? 'true' : 'false' }}" data-bs-toggle="collapse"
                         href="#mapping">
                         <i class="ti ti-sitemap"></i>
                         Mapping
                     </a>
                     <ul class="collapse {{ request()->is('mapping*') ? 'show' : '' }}" id="mapping">
                         <li><a class="nav-link {{ request()->is('mapping/pop') ? 'active' : '' }}"
                                 href="/mapping/pop">POP</a></li>
                         <li> <a class="nav-link {{ request()->is('mapping/odp') ? 'active' : '' }}"
                                 href="/mapping/odp">ODP</a></li>
                     </ul>
                 </li>
             @endif
             @if (multi_auth()->role === 'Admin')
                 <li class="{{ request()->is('partnership*') ? 'active' : '' }}">
                     <a class="nav-link d-inline-flex align-items-center gap-2"
                         aria-expanded="{{ request()->is('partnership*') ? 'true' : 'false' }}"
                         data-bs-toggle="collapse" href="#partnership">
                         <i class="ti ti-affiliate"></i>
                         Patnership
                     </a>
                     <ul class="collapse {{ request()->is('partnership*') ? 'show' : '' }}" id="partnership">
                         <li> <a class="nav-link {{ request()->is('partnership/mitra') ? 'active' : '' }}"
                                 href="/partnership/mitra">Mitra
                                 <span
                                     class="badge text-outline-primary badge-notification ms-2 py-0 px-1 small">PPPoE</span>
                             </a></li>
                         <li> <a href="/partnership/reseller"
                                 class="nav-link {{ request()->is('partnership/reseller') ? 'active' : '' }}">Reseller
                                 <span
                                     class="badge text-outline-success badge-notification ms-2 py-0 px-1 small">Hotspot</span>

                             </a></li>
                     </ul>
                 </li>
             @endif
             @if (multi_auth()->role === 'Admin' || multi_auth()->role === 'Teknisi')
                 <li class="no-sub {{ request()->is('tiket*') ? 'active' : '' }}">
                     <a class="nav-link d-inline-flex align-items-center gap-2" href="/tiket/gangguan">
                         <i class="ti ti-ticket"></i>
                         Tiket Gangguan
                     </a>
                 </li>
             @endif
             @if (multi_auth()->role === 'Admin')
                 <li class="menu-title">
                     <span>Setting</span>
                 </li>
                 <li class="{{ request()->is('setting*') ? 'active' : '' }} another-level">
                     <a class="nav-link d-inline-flex align-items-center gap-2"
                         aria-expanded="{{ request()->is('setting*') ? 'true' : 'false' }}" data-bs-toggle="collapse"
                         href="#setting_admin">
                         <i class="ti ti-server-cog"></i>
                         Setting
                     </a>
                     <ul class="collapse {{ request()->is('setting*') ? 'show' : '' }}" id="setting_admin">
                         <li>
                             <a class="nav-link {{ request()->is('setting/perusahaan') ? 'active' : '' }}"
                                 href="/setting/perusahaan">Perusahaan</a>
                         </li>
                         <li>
                             <a class="nav-link {{ request()->is('setting/billing') ? 'active' : '' }}"
                                 href="/setting/billing">Billing & Notifikasi</a>
                         </li>
                         <li>
                             <a class="nav-link {{ request()->is('setting/isolir') ? 'active' : '' }}"
                                 href="/setting/isolir">Mode Isolir</a>
                         </li>
                         <li>
                             <a class="nav-link {{ request()->is('setting/role') ? 'active' : '' }}"
                                 href="/setting/role">Role Access</a>
                         </li>
                         <li>
                             <a class="nav-link {{ request()->is('setting/payment') ? 'active' : '' }}"
                                 href="/setting/payment">Payment Gateway</a>
                         </li>
                     </ul>
                 </li>
                 <li class="{{ request()->is('radius*') ? 'active' : '' }} another-level">
                     <a class="nav-link d-inline-flex align-items-center gap-2"
                         aria-expanded="{{ request()->is('radius*') ? 'true' : 'false' }}" data-bs-toggle="collapse"
                         href="#radius">
                         <i class="ti ti-router"></i>
                         Radius
                     </a>
                     <ul class="collapse {{ request()->is('radius*') ? 'show' : '' }}" id="radius">
                         <li><a class="nav-link {{ request()->is('radius/vpn') ? 'active' : '' }} "
                                 href="/radius/vpn">VPN</a></li>
                         <li><a class="nav-link {{ request()->is('radius/mikrotik') ? 'active' : '' }}"
                                 href="/radius/mikrotik">Mikrotik</a></li>
                     </ul>
                 </li>
                 <li class="no-sub {{ request()->is('whatsapp*') ? 'active' : '' }}">
                     <a class="nav-link d-inline-flex align-items-center gap-2" href="/whatsapp">
                         <i class="ti ti-brand-whatsapp"></i>
                         Whatsapp
                     </a>
                 </li>
                 <li class="no-sub {{ request()->is('smtp-setting*') ? 'active' : '' }}">
                     <a class="nav-link d-inline-flex align-items-center gap-2" href="/smtp-setting">
                         <i class="ti ti-brand-gmail"></i>
                         Email Setting
                     </a>
                 </li>
                 <li class="no-sub {{ request()->is('user*') ? 'active' : '' }}">
                     <a class="nav-link d-inline-flex align-items-center gap-2" href="/user">
                         <i class="ti ti-user"></i>
                         Users
                     </a>
                 </li>
                 <li class="no-sub {{ request()->is('log*') ? 'active' : '' }}">
                     <a class="nav-link d-inline-flex align-items-center gap-2" href="/log">
                         <i class="ti ti-history"></i>
                         Activity Log
                     </a>
                 </li>
             @endif
         </ul>
     </div>

     <div class="menu-navs">
         <span class="menu-previous"><i class="ti ti-chevron-left"></i></span>
         <span class="menu-next"><i class="ti ti-chevron-right"></i></span>
     </div>

 </nav>
 <!-- Menu Navigation ends -->
