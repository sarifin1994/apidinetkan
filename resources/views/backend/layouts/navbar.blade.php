    <!-- Sidenav -->

    <!-- Sidenav (lg) -->
    <aside class="aside" id="sidenav-lg">
        <nav class="navbar navbar-expand-xl navbar-vertical">
            <div class="container-lg">
                <!-- Brand -->
                <a class="navbar-brand fs-5 fw-bold px-xl-3 mb-xl-4" href="/">
                    {{-- <span class="material-symbols-outlined fs-4">
                        network_check
                    </span> Radiusqu --}}
                        <img src="{{ asset('assets/images/radiusqu.png') }}" alt="Radiusqu" height="40">
                </a>


                <!-- User -->
                <div class="ms-auto d-xl-none">
                    <div class="dropdown my-n2">
                        <a class="btn btn-link d-inline-flex align-items-center dropdown-toggle" href="#"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="avatar avatar-sm avatar-status avatar-status-success me-3">
                                <img class="avatar-img" src="{{ asset('assets/images/avatar.jpg') }}" alt="..." />
                            </span>
                            {{ multi_auth()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @if (multi_auth()->role === 'Admin')
                                <li><a class="dropdown-item" href="/account">Account</a></li>
                                <li>
                                    <hr class="dropdown-divider" />
                                </li>
                            @endif
                            <form id="logout-form2" action="{{ route('logout') }}" method="POST"
                                style="display: none;">
                                @csrf
                            </form>
                            <li><a class="dropdown-item" href="javascript:void(0)"
                                    onclick="event.preventDefault(); document.getElementById('logout-form2').submit();">Log
                                    out</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Toggler -->
                <button class="navbar-toggler ms-3" type="button" data-bs-toggle="collapse"
                    data-bs-target="#sidenavLargeCollapse" aria-controls="sidenavLargeCollapse" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Collapse -->
                <div class="collapse navbar-collapse" id="sidenavLargeCollapse">
                    <!-- Search -->
                    <div class="input-group d-xl-none my-4 my-xl-0">
                        <input class="form-control" disabled id="topnavSearchInputMobile" type="search"
                            placeholder="SERVER TIME {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}"
                            aria-label="Search" aria-describedby="navbarSearchMobile" />
                        <span class="input-group-text" id="navbarSearchMobile">
                            <span class="material-symbols-outlined"> schedule </span>
                        </span>
                    </div>

                    <!-- Nav -->
                    <nav class="navbar-nav nav-pills mb-7">
                        @if (in_array(multi_auth()->role, ['Owner', 'Admin', 'Teknisi', 'Kasir', 'Mitra']))
                            <div class="nav-item">
                                <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }} " href="/">
                                    <span class="material-symbols-outlined me-3">space_dashboard</span> Dashboard
                                </a>
                            </div>
                        @endif
                        
                        @if (in_array(multi_auth()->role, ['Admin']) && multi_auth()->is_dinetkan == 1)
                            <div class="nav-item">
                                <a class="nav-link {{ request()->is('admin/account*') ? 'active' : '' }}" href="#"
                                    data-bs-toggle="collapse" data-bs-target="#admin_account" role="button"
                                    aria-expanded="false" aria-controls="posts">
                                    <span class="material-symbols-outlined me-3">supervisor_account</span> Mitra 
                                </a>
                                <div class="collapse {{ request()->is('admin/account*') ? 'show' : '' }}" id="admin_account">
                                    <nav class="nav nav-pills">
                                        <a class="nav-link {{ request()->is('admin/account/info_dinetkan') ? 'active' : '' }}" href="{{ route('admin.account.info_dinetkan.index') }}">Info Akun</a>
                                        <a class="nav-link {{ request()->is('admin/account/invoice_dinetkan/order') ? 'active' : '' }}" href="{{ route('admin.account.invoice_dinetkan.order') }}">Service</a>
                                        <a class="nav-link {{ request()->is('admin/account/invoice_dinetkan') ? 'active' : '' }}" href="{{ route('admin.account.invoice_dinetkan.index') }}">Tagihan Pemakaian Bandwith</a>
                                        <a class="nav-link {{ request()->is('admin/account/mrtg') ? 'active' : '' }}" href="{{ route('admin.account.mrtg.index') }}">MRTG</a>
                                    </nav>
                                </div>
                            </div>
                        @endif

                        
                        @if (in_array(multi_auth()->role, ['Admin']) && multi_auth()->is_dinetkan == 1 && multi_auth()->status != 2)
                            <div class="nav-item">
                                <a class="nav-link" href="#"
                                    data-bs-toggle="collapse" data-bs-target="#admin_account_report" role="button"
                                    aria-expanded="false" aria-controls="posts">
                                    <span class="material-symbols-outlined me-3">assignment</span> Report Mitra
                                </a>
                                <div class="collapse" id="admin_account_report">
                                    <nav class="nav nav-pills">
                                        <a class="nav-link {{ request()->is('admin/account/billing/member_dinetkan/index') ? 'active' : '' }}" href="{{ route('admin.billing.member_dinetkan.index') }}">Pelanggan</a>
                                        <a class="nav-link {{ request()->is('admin/account/invoice_dinetkan/order') ? 'active' : '' }}" href="{{ route('admin.product_dinetkan.index') }}">Profile PPPOE</a>
                                        <a class="nav-link {{ request()->is('admin/account/invoice_dinetkan') ? 'active' : '' }}" href="{{ route('admin.billing.member_dinetkan.mapping_service') }}">Report</a>
                                        
                                    </nav>
                                </div>
                            </div>
                        @endif

                        @if(in_array(multi_auth()->role, ['Admin']) && multi_auth()->ext_role == 'dinetkan')
                            <div class="nav-item">
                                <a class="nav-link {{ request()->is('dinetkan*') ? 'active' : '' }}" href="#"
                                    data-bs-toggle="collapse" data-bs-target="#dinetkan" role="button"
                                    aria-expanded="false" aria-controls="posts">
                                    <span class="material-symbols-outlined me-3">supervisor_account</span> Mitra
                                </a>
                                <div class="collapse {{ request()->is('dinetkan*') ? 'show' : '' }}" id="dinetkan">
                                    <nav class="nav nav-pills">
                                        <a class="nav-link {{ request()->is('dinetkan/users_dinetkan') ? 'active' : '' }}" href="{{ route('dinetkan.users_dinetkan') }}">User</a>
                                        <a class="nav-link {{ request()->is('dinetkan/invoice_dinetkan/order') ? 'active' : '' }}" href="{{ route('dinetkan.invoice_dinetkan.order') }}">Order</a>
                                        <a class="nav-link {{ request()->is('dinetkan/invoice_dinetkan') ? 'active' : '' }}" href="{{ route('dinetkan.invoice_dinetkan.index') }}">Tagihan Pemakaian Bandwith</a>
                                        <a class="nav-link {{ request()->is('dinetkan/license_dinetkan') ? 'active' : '' }}" href="{{ route('dinetkan.license_dinetkan') }}">Service</a>
                                        <a class="nav-link {{ request()->is('dinetkan/master_pop') ? 'active' : '' }}" href="{{ route('dinetkan.master_pop') }}">Master POP</a>
                                        <a class="nav-link {{ request()->is('dinetkan/master_metro') ? 'active' : '' }}" href="{{ route('dinetkan.master_metro') }}">Master Metro</a>
                                        <a class="nav-link {{ request()->is('dinetkan/settings_dinetkan') ? 'active' : '' }}" href="{{ route('dinetkan.settings_dinetkan') }}">Settings</a>
                                        <a class="nav-link {{ request()->is('dinetkan/whatsapp') ? 'active' : '' }}" href="{{ route('dinetkan.whatsapp.index') }}">Whatsapp</a>
                                        <a class="nav-link {{ request()->is('dinetkan/users_dinetkan_service') ? 'active' : '' }}" href="{{ route('dinetkan.users_dinetkan_service')}}">Report</a>
                                    </nav>
                                </div>
                            </div>
                        @endif

                        @if (in_array(multi_auth()->role, ['Owner']))
                            <div class="nav-item">
                                <a class="nav-link {{ request()->is('license*') ? 'active' : '' }}" href="/license">
                                    <span class="material-symbols-outlined me-3">mark_chat_unread</span> License
                                </a>
                            </div>
                            <div class="nav-item">
                                <a class="nav-link {{ request()->is('user*') ? 'active' : '' }}" href="/user">
                                    <span class="material-symbols-outlined me-3">supervisor_account</span> Manage Users
                                </a>
                            </div>
                            <div class="nav-item">
                                <a class="nav-link {{ request()->is('withdraw*') ? 'active' : '' }}" href="/withdraw">
                                    <span class="material-symbols-outlined me-3">wallet</span> Withdraw
                                </a>
                            </div>
                            <div class="nav-item">
                                <a class="nav-link {{ request()->is('whatsapp*') ? 'active' : '' }}" href="/whatsapp">
                                    <span class="material-symbols-outlined me-3">mark_chat_unread</span> WhatsApp
                                </a>
                            </div>
                            <div class="nav-item">
                                <a class="nav-link {{ request()->is('setting*') ? 'active' : '' }}" href="#"
                                    data-bs-toggle="collapse" data-bs-target="#setting" role="button"
                                    aria-expanded="false" aria-controls="posts">
                                    <span class="material-symbols-outlined me-3">settings</span> Server Settings
                                </a>
                                <div class="collapse {{ request()->is('setting*') ? 'show' : '' }}" id="setting">
                                    <nav class="nav nav-pills">
                                        <a class="nav-link {{ request()->is('setting/vpn') ? 'active' : '' }}"
                                            href="/setting/vpn">VPN CHR</a>
                                        <a class="nav-link {{ request()->is('setting/wa') ? 'active' : '' }}"
                                            href="/setting/wa">WA Gateway</a>
                                        <a class="nav-link {{ request()->is('setting/payment_owner') ? 'active' : '' }}"
                                            href="/setting/payment_owner">Payment Gateway</a>
                                    </nav>
                                </div>
                            </div>
                            <div class="nav-item">
                                <a class="nav-link nav-link {{ request()->is('logs') ? 'active' : '' }}"
                                    href="/logs">
                                    <span class="material-symbols-outlined me-3">list_alt</span> Laravel Logs
                                </a>
                            </div>
                        @endif

                        @if (multi_auth()->role === 'Admin' || multi_auth()->role === 'Teknisi')
                            <div class="nav-item">
                                <a class="nav-link {{ request()->is('olt*') ? 'active' : '' }}" href="/olt">
                                    <span class="material-symbols-outlined me-3">nest_wifi_router</span>
                                    OLT
                                </a>
                            </div>

                            <div class="nav-item">
                                <a class="nav-link " href="#" data-bs-toggle="collapse"
                                    data-bs-target="#customers" role="button" aria-expanded="false"
                                    aria-controls="customers">
                                    <span class="material-symbols-outlined me-3">group</span> Hotspot
                                </a>
                                <div class="collapse {{ request()->is('hotspot*') ? 'show' : '' }}" id="customers">
                                    <nav class="nav nav-pills">
                                        <a class="nav-link {{ request()->is('hotspot/user') ? 'active' : '' }}"
                                            href="/hotspot/user">User</a>
                                        <a class="nav-link {{ request()->is('hotspot/online') ? 'active' : '' }}"
                                            href="/hotspot/online">Online</a>
                                        @if (multi_auth()->role === 'Admin')
                                            <a class="nav-link {{ request()->is('hotspot/profile') ? 'active' : '' }}"
                                                href="/hotspot/profile">Profile</a>
                                        @endif
                                        {{-- <a class="nav-link " href="./customers/customer-new.html">Resellers</a> --}}
                                    </nav>
                                </div>
                            </div>
                        @endif

                        @if (in_array(multi_auth()->role, ['Admin', 'Teknisi', 'Kasir', 'Mitra']))
                            <div class="nav-item">
                                <a class="nav-link {{ request()->is('pppoe*') ? 'active' : '' }}" href="#"
                                    data-bs-toggle="collapse" data-bs-target="#pppoe" role="button"
                                    aria-expanded="false" aria-controls="pppoe">
                                    <span class="material-symbols-outlined me-3">globe</span> PPPoE
                                </a>
                                <div class="collapse {{ request()->is('pppoe*') ? 'show' : '' }}" id="pppoe">
                                    <nav class="nav nav-pills">
                                        <a class="nav-link {{ request()->is('pppoe/user') ? 'active' : '' }}"
                                            href="/pppoe/user">User</a>
                                        @if (in_array(multi_auth()->role, ['Admin', 'Teknisi']))
                                            <a class="nav-link  {{ request()->is('pppoe/online') ? 'active' : '' }}"
                                                href="/pppoe/online">Online</a>
                                            <a class="nav-link  {{ request()->is('pppoe/offline') ? 'active' : '' }}"
                                                href="/pppoe/offline">Offline</a>
                                            <a class="nav-link {{ request()->is('pppoe/profile') ? 'active' : '' }}"
                                                href="/pppoe/profile">Profile</a>
                                        @endif
                                    </nav>
                                </div>
                            </div>
                        @endif

                        @if (in_array(multi_auth()->role, ['Admin', 'Kasir']) || (multi_auth()->role === 'Mitra' && multi_auth()->billing === 1))
                            <div class="nav-item">
                                <a class="nav-link {{ request()->is('invoice*') ? 'active' : '' }}" href="#"
                                    data-bs-toggle="collapse" data-bs-target="#account" role="button"
                                    aria-expanded="false" aria-controls="account">
                                    <span class="material-symbols-outlined me-3">payments</span> Invoice
                                </a>
                                <div class="collapse {{ request()->is('invoice*') ? 'show' : '' }}" id="account">
                                    <nav class="nav nav-pills">
                                        <a class="nav-link {{ request()->is('invoice/unpaid') ? 'active' : '' }}"
                                            href="/invoice/unpaid">Unpaid</a>
                                        <a class="nav-link {{ request()->is('invoice/paid') ? 'active' : '' }}"
                                            href="/invoice/paid">Paid</a>

                                    </nav>
                                </div>
                            </div>
                        @endif
                        @if (in_array(multi_auth()->role, ['Admin', 'Kasir', 'Mitra']))
                            <div class="nav-item">
                                <a class="nav-link {{ request()->is('keuangan*') ? 'active' : '' }}" href="#"
                                    data-bs-toggle="collapse" data-bs-target="#keuangan" role="button"
                                    aria-expanded="false" aria-controls="keuangan">
                                    <span class="material-symbols-outlined me-3">account_balance</span> Keuangan
                                </a>
                                <div class="collapse  {{ request()->is('keuangan*') ? 'show' : '' }} "
                                    id="keuangan">
                                    <nav class="nav nav-pills">
                                        @if (in_array(multi_auth()->role, ['Admin', 'Kasir']))
                                            <a class="nav-link {{ request()->is('keuangan/transaksi') ? 'active' : '' }}"
                                                href="/keuangan/transaksi">Transaksi</a>
                                            @if (\App\Models\Setting\Midtrans::where('shortname', multi_auth()->shortname)->first())
                                                @if(\App\Models\Setting\Midtrans::where('shortname', multi_auth()->shortname)->first()->status === 1)
                                                <a class="nav-link {{ request()->is('keuangan/midtrans') ? 'active' : '' }}"
                                                    href="/keuangan/midtrans">Midtrans</a>
                                                @endif
                                            @endif
                                            @if (\App\Models\Setting\Mduitku::where('shortname', multi_auth()->shortname)->first()?->status === 1)
                                                <a class="nav-link {{ request()->is('keuangan/duitku') ? 'active' : '' }}"
                                                    href="/keuangan/duitku">Duitku</a>
                                            @endif
                                        @endif
                                        <a class="nav-link {{ request()->is('keuangan/mitra') ? 'active' : '' }}"
                                            href="/keuangan/mitra">Mitra</a>
                                        <a class="nav-link {{ request()->is('keuangan/hotspot') ? 'active' : '' }}"
                                            href="/keuangan/hotspot">Hotspot</a>
                                        <a class="nav-link {{ request()->is('keuangan/kategori') ? 'active' : '' }}"
                                            href="/keuangan/kategori">Kategori</a>

                                    </nav>
                                </div>
                            </div>
                        @endif
                        {{-- <div class="nav-item">
                            <a class="nav-link " href="#" data-bs-toggle="collapse" data-bs-target="#posts"
                                role="button" aria-expanded="false" aria-controls="posts">
                                <span class="material-symbols-outlined me-3">confirmation_number</span> Ticket
                            </a>
                            <div class="collapse " id="posts">
                                <nav class="nav nav-pills">
                                    <a class="nav-link " href="./posts/categories.html">Pasang Baru</a>
                                    <a class="nav-link " href="./posts/posts.html">Gangguan</a>
                                </nav>
                            </div>
                        </div> --}}
                        @if (multi_auth()->role === 'Admin' || multi_auth()->role === 'Teknisi')
                            <div class="nav-item">
                                <a class="nav-link {{ request()->is('mapping*') ? 'active' : '' }}" href="#"
                                    data-bs-toggle="collapse" data-bs-target="#mapping" role="button"
                                    aria-expanded="false" aria-controls="mapping">
                                    <span class="material-symbols-outlined me-3">map</span> Mapping
                                </a>
                                <div class="collapse {{ request()->is('mapping*') ? 'show' : '' }} " id="mapping">
                                    <nav class="nav nav-pills">
                                        <a class="nav-link {{ request()->is('mapping/pop') ? 'active' : '' }}"
                                            href="/mapping/pop">POP</a>
                                        <a class="nav-link {{ request()->is('mapping/odp') ? 'active' : '' }}"
                                            href="/mapping/odp">ODP</a>
                                    </nav>
                                </div>
                            </div>
                        @endif
                        @if (multi_auth()->role === 'Admin')
                            <div class="nav-item">
                                <a class="nav-link {{ request()->is('partnership*') ? 'active' : '' }}"
                                    href="#" data-bs-toggle="collapse" data-bs-target="#partnership"
                                    role="button" aria-expanded="false" aria-controls="partnership">
                                    <span class="material-symbols-outlined me-3">guardian</span> Partnership
                                </a>
                                <div class="collapse {{ request()->is('partnership*') ? 'show' : '' }} "
                                    id="partnership">
                                    <nav class="nav nav-pills">
                                        <a class="nav-link {{ request()->is('partnership/mitra') ? 'active' : '' }}"
                                            href="/partnership/mitra">Mitra
                                            <small class="badge bg-primary-subtle text-primary ms-1"
                                                style="font-size:9px">PPPoE</small>
                                        </a>
                                        <a href="/partnership/reseller"
                                            class="nav-link {{ request()->is('partnership/reseller') ? 'active' : '' }}">Reseller
                                            <small class="badge bg-success-subtle text-success ms-1"
                                                style="font-size:9px">Hotspot</small>
                                        </a>
                                    </nav>
                                </div>
                            </div>
                        @endif
                        @if (multi_auth()->role === 'Admin' || multi_auth()->role === 'Teknisi')
                            <div class="nav-item">
                                <a class="nav-link {{ request()->is('tiket*') ? 'active' : '' }}"
                                    href="/tiket/gangguan">
                                    <span class="material-symbols-outlined me-3">confirmation_number</span>
                                    Tiket Gangguan
                                </a>
                            </div>
                        @endif
                    </nav>

                    @if (multi_auth()->role === 'Admin')
                        <!-- Heading -->
                        <h3 class="fs-base px-3 mb-4">Setting</h3>

                        <!-- Nav -->
                        <nav class="navbar-nav nav-pills mb-xl-7">


                            <div class="nav-item">
                                <a class="nav-link {{ request()->is('setting*') ? 'active' : '' }}" href="#"
                                    data-bs-toggle="collapse" data-bs-target="#setting" role="button"
                                    aria-expanded="false" aria-controls="posts">
                                    <span class="material-symbols-outlined me-3">settings</span> Setting
                                </a>
                                <div class="collapse {{ request()->is('setting*') ? 'show' : '' }}" id="setting">
                                    <nav class="nav nav-pills">
                                        <a class="nav-link {{ request()->is('setting/perusahaan') ? 'active' : '' }}"
                                            href="/setting/perusahaan">Perusahaan</a>
                                        <a class="nav-link {{ request()->is('setting/billing') ? 'active' : '' }}"
                                            href="/setting/billing">Billing & Notifikasi</a>
                                        <a class="nav-link {{ request()->is('setting/isolir') ? 'active' : '' }}"
                                            href="/setting/isolir">Mode Isolir</a>
                                        <a class="nav-link {{ request()->is('setting/role') ? 'active' : '' }}"
                                            href="/setting/role">Role Access</a>
                                        <a class="nav-link {{ request()->is('setting/payment') ? 'active' : '' }}"
                                            href="/setting/payment">Payment Gateway</a>
                                    </nav>
                                </div>
                            </div>

                            <div class="nav-item">
                                <a class="nav-link {{ request()->is('radius*') ? 'active' : '' }}" href="#"
                                    data-bs-toggle="collapse" data-bs-target="#mikrotik" role="button"
                                    aria-expanded="false" aria-controls="posts">
                                    <span class="material-symbols-outlined me-3">router</span> Radius
                                </a>
                                <div class="collapse {{ request()->is('radius*') ? 'show' : '' }} " id="mikrotik">
                                    <nav class="nav nav-pills">
                                        <a class="nav-link {{ request()->is('radius/vpn') ? 'active' : '' }} "
                                            href="/radius/vpn">VPN</a>
                                        <a class="nav-link {{ request()->is('radius/mikrotik') ? 'active' : '' }}"
                                            href="/radius/mikrotik">Mikrotik</a>
                                    </nav>
                                </div>
                            </div>

                            <div class="nav-item">
                                <a class="nav-link {{ request()->is('whatsapp*') ? 'active' : '' }}"
                                    href="/whatsapp">
                                    <span class="material-symbols-outlined me-3">mark_chat_unread</span> WhatsApp
                                </a>
                            </div>

                            <div class="nav-item">
                                <a class="nav-link {{ request()->is('smtp-setting*') ? 'active' : '' }}"
                                    href="/smtp-setting">
                                    <span class="material-symbols-outlined me-3"> contact_mail </span> Email Setting
                                </a>
                            </div>
                            <div class="nav-item">
                                <a class="nav-link {{ request()->is('user*') ? 'active' : '' }}" href="/user">
                                    <span class="material-symbols-outlined me-3">supervisor_account</span> Users
                                </a>
                            </div>
                            <div class="nav-item">
                                <a class="nav-link nav-link {{ request()->is('log') ? 'active' : '' }}"
                                    href="/log">
                                    <span class="material-symbols-outlined me-3">list_alt</span> Activity Log
                                </a>
                            </div>
                        </nav>
                    @endif

                    <!-- Divider -->
                    <hr class="my-4 d-xl-none" />

                    <!-- Nav -->
                    <nav class="navbar-nav nav-pills d-xl-none mb-7">
                        <div class="nav-item dropdown">
                            <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown"
                                data-bs-settings-switcher aria-expanded="false">
                                <span class="material-symbols-outlined"> settings </span> <span
                                    class="d-xl-none ms-3">Settings</span>
                            </a>
                            <div class="dropdown-menu">
                                <!-- Color mode -->
                                <h6 class="dropdown-header">Color mode</h6>
                                <a class="dropdown-item d-flex" data-bs-theme-value="light" href="#"
                                    role="button">
                                    <span class="material-symbols-outlined me-2"> light_mode </span> Light
                                </a>
                                <a class="dropdown-item d-flex" data-bs-theme-value="dark" href="#"
                                    role="button"> <span class="material-symbols-outlined me-2"> dark_mode </span>
                                    Dark </a>
                                <a class="dropdown-item d-flex" data-bs-theme-value="auto" href="#"
                                    role="button"> <span class="material-symbols-outlined me-2"> contrast </span>
                                    Auto </a>

                                <!-- Divider -->
                                <hr class="dropdown-divider" />

                                <!-- Navigation position -->
                                {{-- <h6 class="dropdown-header">Navigation position</h6>
                                <a class="dropdown-item d-flex" data-bs-navigation-position-value="sidenav"
                                    href="#" role="button">
                                    <span class="material-symbols-outlined me-2"> thumbnail_bar </span> Sidenav
                                </a>
                                <a class="dropdown-item d-flex" data-bs-navigation-position-value="topnav"
                                    href="#" role="button">
                                    <span class="material-symbols-outlined me-2"> toolbar </span> Topnav
                                </a> --}}
                            </div>
                        </div>
                        <div class="nav-item">
                            <a class="nav-link" href="#!">
                                <span class="material-symbols-outlined me-3">message</span> Contact us
                            </a>
                        </div>
                    </nav>

                    <!-- Card -->
                    {{-- <div class="card mt-auto">
                        <div class="card-body">
                            <!-- Heading -->
                            <h6>Need help?</h6>

                            <!-- Text -->
                            <p class="text-body-secondary mb-0">Feel free to reach out to us should you have any
                                questions or suggestions.</p>
                        </div>
                    </div> --}}
                </div>
            </div>
        </nav>
    </aside>

    <!-- Topnav (sm) -->
    <nav id="topnav-sm" class="navbar d-none d-xl-flex px-xl-6 sticky-top border-bottom bg-body"
        style="z-index: 1030;">
        <div class="container flex-column align-items-stretch">
            <div class="row gx-0 align-items-center">
                <div class="col">
                    {{-- <div class="sidenav-sizing">
                    <hr class="dropdown-divider">
                    <h6 class="dropdown-header">Sidenav sizing</h6>
                    <a class="dropdown-item d-flex" data-bs-sidenav-sizing-value="base" href="#" role="button" aria-pressed="false">
                      <span class="material-symbols-outlined me-2">density_large</span> Base
                    </a>
                    <a class="dropdown-item d-flex" data-bs-sidenav-sizing-value="md" href="#" role="button" aria-pressed="false">
                      <span class="material-symbols-outlined me-2">density_medium</span> Medium
                    </a>
                    <a class="dropdown-item d-flex active" data-bs-sidenav-sizing-value="sm" href="#" role="button" aria-pressed="true">
                      <span class="material-symbols-outlined me-2">density_small</span> Small
                    </a>
                  </div> --}}
                    <!-- Search -->
                    <div class="input-group" style="max-width: 340px">
                        <input class="form-control" disabled id="topnavSearchInput" type="search"
                            placeholder="SERVER TIME {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}"
                            aria-label="Search" aria-describedby="navbarSearch" />
                        {{-- <span class="input-group-text" id="navbarSearch"> --}}
                        {{-- <kbd class="badge bg-body-secondary text-body">⌘</kbd> --}}
                        {{-- <kbd class="badge bg-body-secondary text-body ms-1">></kbd> --}}
                        {{-- </span> --}}
                        <div class="d-flex align-items-center border-primary">
                            <div class="dropdown">
                                <button class="btn btn-link py-2 px-4 shadow-none" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="material-symbols-outlined fs-5" id="activeThemeIcon">contrast</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2" href="#"
                                            data-bs-theme-value="light">
                                            <span class="material-symbols-outlined">light_mode</span> Light
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2" href="#"
                                            data-bs-theme-value="dark">
                                            <span class="material-symbols-outlined">dark_mode</span> Dark
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center gap-2" href="#"
                                            data-bs-theme-value="auto">
                                            <span class="material-symbols-outlined">contrast</span> Auto
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-auto">
                    <!-- User -->
                    <div class="dropdown my-n2">
                        <a class="btn btn-link d-inline-flex align-items-center" href="#" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold ms-auto">{{ multi_auth()->name }}</span>
                                    <span class="text-muted small ms-auto">

                                        @if (multi_auth()->role === 'Mitra')
                                            {{ multi_auth()->id_mitra }}
                                        @else
                                            {{ multi_auth()->name }}
                                        @endif

                                        /
                                        {{ multi_auth()->role }}
                                    </span>
                                </div>
                                <span class="avatar avatar-sm avatar-status avatar-status-success ms-4">
                                    <img class="avatar-img" src="{{ asset('assets/images/avatar.jpg') }}"
                                        alt="Avatar" />
                                </span>
                            </div>


                            <span class="ms-2">

                                <span class="material-symbols-outlined">expand_more</span>
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @if (multi_auth()->role === 'Admin')
                                <li><a class="dropdown-item" href="/account">Account</a></li>
                                <li>
                                    <hr class="dropdown-divider" />
                                </li>
                            @endif
                            <li><a class="dropdown-item" href="/password">Password</a></li>
                            <li>
                                <hr class="dropdown-divider" />
                            </li>
                            <form id="logout-form1" action="{{ route('logout') }}" method="POST"
                                style="display: none;">
                                @csrf
                            </form>
                            <li><a class="dropdown-item" href="javascript:void(0)"
                                    onclick="event.preventDefault(); document.getElementById('logout-form1').submit();">Log
                                    out</a></li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </nav>


    <!-- JavaScript to handle theme switching -->
    <script>
        const themeIcon = document.getElementById('activeThemeIcon');

        const themeIcons = {
            auto: 'contrast',
            light: 'light_mode',
            dark: 'dark_mode',
        };

        function applyTheme(theme) {
            let effectiveTheme = theme;

            if (theme === 'auto') {
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                effectiveTheme = prefersDark ? 'dark' : 'light';
            }

            document.documentElement.setAttribute('data-bs-theme', effectiveTheme);
            localStorage.setItem('theme', theme);

            // Ganti ikon
            if (themeIcon) {
                themeIcon.textContent = themeIcons[theme];
            }

            // Hapus semua active class dari dropdown
            document.querySelectorAll('[data-bs-theme-value]').forEach(el => {
                el.classList.remove('active');
            });

            // Tambahkan active ke elemen yang sesuai
            const activeItem = document.querySelector(`[data-bs-theme-value="${theme}"]`);
            if (activeItem) {
                activeItem.classList.add('active');
            }
        }

        // Handle klik theme
        document.querySelectorAll('[data-bs-theme-value]').forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                const theme = item.getAttribute('data-bs-theme-value');
                applyTheme(theme);
            });
        });

        // On page load
        const savedTheme = localStorage.getItem('theme') || 'auto';
        applyTheme(savedTheme);

        // Dynamic update kalau user ganti system theme (saat auto aktif)
        if (savedTheme === 'auto') {
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
                applyTheme('auto');
            });
        }
    </script>
