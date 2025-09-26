@extends('backend.layouts.app_new')

@section('title', 'Dashboard')

@section('css')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
@endsection

@section('main')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6 p-0">
          <h3>Dashboard</h3>
        </div>
        <div class="col-sm-6 p-0">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">
                <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item active">Dashboard</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
  <!-- Container-fluid starts-->
  <div class="container-fluid project-dashboard">
    <div class="row">
      <!-- Statistics Cards -->
      <div class="col-xxl-12 col-xl-12 col-lg-12 box-col-12">
        <div class="row">
          <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6">
            <x-stat-card route="{{ route('dinetkan.users') }}" icon='<i class="fa-solid fa-users"></i>' label="Total Client"
              :amount="$totalUsers" />
          </div>

          <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6">
            <x-stat-card route="{{ route('dinetkan.users') }}" color="success" icon='<i class="fa-solid fa-users"></i>'
              label="New Client" :amount="$newUsers" />
          </div>

          <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6">
            <x-stat-card route="{{ route('dinetkan.users') }}" color="orange" icon='<i class="fa-solid fa-users"></i>'
              label="Active Client" :amount="$activeUsers" />
          </div>

          <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6">
            <x-stat-card route="{{ route('dinetkan.users') }}" color="danger" icon='<i class="fa-solid fa-users"></i>'
              label="Overdue Client" :amount="$overdueUsers" />
          </div>

          <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6">
            <x-stat-card route="{{ route('dinetkan.users') }}" color="warning" icon='<i class="fa-solid fa-users"></i>'
              label="Online Client" :amount="$recentOnlineUsers" />
          </div>
        </div>
      </div>

      <!-- Server Information Card -->
      <div class="d-flex flex-wrap gap-4">
  <div class="card flex-fill" style="min-width: 300px;">
    <div class="card-header">
      <h5 class="mb-0">Host Information</h5>
    </div>
    <div class="card-body">
      <table class="table">
        <tbody>
          <tr><td class="fw-bold">Server Address</td><td><span id="server-ip" class="hidden-ip">******</span><span id="actual-ip" class="d-none">{{ $larinfo['host']['ip'] }}</span></td></tr>
          <tr><td class="fw-bold">Location</td><td>{{ $larinfo['host']['city'] }} - {{ $larinfo['host']['country'] }}</td></tr>
          <tr><td class="fw-bold">Region</td><td>{{ $larinfo['host']['region'] ?: 'Not Available' }}</td></tr>
          <tr><td class="fw-bold">Timezone</td><td>{{ $larinfo['host']['timezone'] ?: 'Not Available' }}</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="card flex-fill" style="min-width: 300px;">
    <div class="card-header">
      <h5 class="mb-0">System Hardware</h5>
    </div>
    <div class="card-body">
      <table class="table">
        <tbody>
          <tr><td class="fw-bold">CPU</td><td>{{ $larinfo['server']['hardware']['cpu'] ?: 'Not Available' }}</td></tr>
          <tr><td class="fw-bold">CPU Cores</td><td>{{ $larinfo['server']['hardware']['cpu_count'] ?: 'Not Available' }}</td></tr>
          <tr><td class="fw-bold">RAM</td><td>{{ $larinfo['server']['hardware']['ram']['human_total'] ?: 'Not Available' }}</td></tr>
          <tr><td class="fw-bold">Free RAM</td><td>{{ $larinfo['server']['hardware']['ram']['human_free'] ?: 'Not Available' }}</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="card flex-fill" style="min-width: 300px;">
    <div class="card-header">
      <h5 class="mb-0">System Software</h5>
    </div>
    <div class="card-body">
      <table class="table">
        <tbody>
          <tr><td class="fw-bold">OS</td><td>{{ $larinfo['server']['software']['os'] }} {{ $larinfo['server']['software']['distro'] }}</td></tr>
          <tr><td class="fw-bold">Kernel Version</td><td>{{ $larinfo['server']['software']['kernel'] ?: 'Not Available' }}</td></tr>
          <tr><td class="fw-bold">Web Server</td><td>{{ $larinfo['server']['software']['webserver'] ?: 'Not Available' }}</td></tr>
          <tr><td class="fw-bold">PHP Version</td><td>{{ $larinfo['server']['software']['php'] ?: 'Not Available' }}</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="card flex-fill" style="min-width: 300px;">
    <div class="card-header">
      <h5 class="mb-0">Server Status</h5>
    </div>
    <div class="card-body">
      <table class="table">
        <tbody>
          <tr><td class="fw-bold">Database</td><td>{{ $larinfo['database']['driver'] }} (v{{ $larinfo['database']['version'] ?: 'Not Available' }})</td></tr>
          <tr><td class="fw-bold">Uptime</td><td>{{ $larinfo['server']['uptime']['uptime'] ?: 'Not Available' }}</td></tr>
          <tr><td class="fw-bold">Last Booted</td><td>{{ $larinfo['server']['uptime']['booted_at'] ?: 'Not Available' }}</td></tr>
          <tr><td class="fw-bold">Network Status</td><td id="network-status">Checking...</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>


      <!-- Recent Client Card -->
      <div class="col-xl-6 col-lg-6 col-md-6">
        <div class="card">
          <div class="card-header">
            <div class="header-top d-flex justify-content-between align-items-center">
              <h5 class="mb-0">Recent Client</h5>
            </div>
          </div>
          <div class="card-body team-members pt-0">
            <div class="table-responsive custom-scrollbar">
              <table class="display table admin-users-table" style="width:100%">
                <thead>
                  <tr>
                    <th>User Info</th>
                    <th>Email</th>
                    <th>Username</th>
                    <th>Status</th>
                    <th>Next Due</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($recentAdmins as $admin)
                    <tr>
                      <td>
                        <div class="d-flex">
                          <div class="flex-shrink-0">
                            @if ($admin->name)
                              <div class="user-avatar">
                                {{ strtoupper(substr($admin->name, 0, 1)) }}
                              </div>
                            @else
                              <img src="{{ asset('assets/images/dashboard-3/user/default.png') }}" alt="">
                            @endif
                          </div>
                          <div class="flex-grow-1 ms-2">
                            <a href="#">
                              <h6 class="mb-0">{{ $admin->name }}</h6>
                              <small>{{ $admin->shortname }}</small>
                            </a>
                          </div>
                        </div>
                      </td>
                      <td>{{ $admin->email }}</td>
                      <td>{{ $admin->username }}</td>
                      <td>
                        <div class="status-badge status-{{ strtolower($admin->status->label()) }}">
                          {{ $admin->status->label() }}
                        </div>
                      </td>
                      <td>{{ $admin->next_due ? date('d M Y', strtotime($admin->next_due)) : '-' }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Revenue Chart -->
      <div class="col-xl-6">
        <x-bar-chart title="Revenue" :legends="[
            [
                'label' => 'Revenue',
                'color' => 'bg-primary',
            ],
        ]" :route="route('dinetkan.charts.revenue')" :colors="['#5C61F2']" />
      </div>

      {{--  <div class="col-xl-6 col-lg-6 col-md-6">
        <div class="card">
          <div class="card-header">
            <div class="header-top d-flex justify-content-between align-items-center">
              <h5 class="mb-0">Renewed Client</h5>
            </div>
          </div>
          <div class="card-body team-members pt-0">
            <div class="table-responsive custom-scrollbar">
              <table class="display table team-members-table" style="width:100%">
                <thead>
                  <tr>
                    <th>User Info</th>
                    <th>Email</th>
                    <th>Username</th>
                    <th>Renewed Date</th>
                    <th>Next Due</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($recentRenewedAdmins as $invoice)
                    <tr>
                      <td>
                        <div class="d-flex">
                          <div class="flex-shrink-0">
                            @if ($invoice->admin->name)
                              <div class="user-avatar">
                                {{ strtoupper(substr($invoice->admin->name, 0, 1)) }}
                              </div>
                            @else
                              <img src="{{ asset('assets/images/dashboard-3/user/default.png') }}" alt="">
                            @endif
                          </div>
                          <div class="flex-grow-1 ms-2">
                            <a href="#">
                              <h6 class="mb-0">{{ $invoice->admin->name }}</h6>
                              <small>{{ $invoice->admin->shortname }}</small>
                            </a>
                          </div>
                        </div>
                      </td>
                      <td>{{ $invoice->admin->email }}</td>
                      <td>{{ $invoice->admin->username }}</td>
                      <td>{{ date('d M Y', strtotime($invoice->invoice_date)) }}</td>
                      <td>{{ date('d M Y', strtotime($invoice->due_date)) }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div> --}}

      <!-- Top Admin Card -->
      <div class="col-xl-6 col-lg-6 col-md-6">
        <div class="card">
          <div class="card-header">
            <div class="header-top d-flex justify-content-between align-items-center">
              <h5 class="mb-0">Top Admin</h5>
            </div>
          </div>
          <div class="card-body team-members pt-0">
            <div class="table-responsive custom-scrollbar">
              <table class="display table top-admins-table" style="width:100%">
                <thead>
                  <tr>
                    <th>User Info</th>
                    <th>Email</th>
                    <th>Username</th>
                    <th>Hotspot</th>
                    <th>PPPoE</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($topAdmins as $admin)
                    <tr>
                      <td>
                        <div class="d-flex">
                          <div class="flex-shrink-0">
                            @if ($admin->name)
                              <div class="user-avatar">
                                {{ strtoupper(substr($admin->name, 0, 1)) }}
                              </div>
                            @else
                              <img src="{{ asset('assets/images/dashboard-3/user/default.png') }}" alt="">
                            @endif
                          </div>
                          <div class="flex-grow-1 ms-2">
                            <a href="#">
                              <h6 class="mb-0">{{ $admin->name }}</h6>
                              <small>{{ $admin->shortname }}</small>
                            </a>
                          </div>
                        </div>
                      </td>
                      <td>{{ $admin->email }}</td>
                      <td>{{ $admin->username }}</td>
                      <td>{{ $admin->hotspot_users_count }}</td>
                      <td>{{ $admin->pppoe_users_count }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Admin Growth Chart -->
      <div class="col-xl-6">
        <x-line-chart title="Admin Growth" :route="route('dinetkan.charts.recent-admins')" :colors="['#5C61F2']" />
      </div>

      <!-- Daily Revenue Chart -->
      <div class="col-xl-6">
        <x-line-chart title="Daily Revenue" :route="route('dinetkan.charts.daily-revenue')" :colors="['#5C61F2']" />
      </div>
    </div>
  </div>
  <!-- Container-fluid Ends-->
@endsection

@section('scripts')

<script>
  document.getElementById('server-ip').addEventListener('click', function() {
    if (this.classList.contains('d-none')) {
      this.classList.remove('d-none');
      document.getElementById('actual-ip').classList.add('d-none');
    } else {
      this.classList.add('d-none');
      document.getElementById('actual-ip').classList.remove('d-none');
    }
  });
  async function checkPing() {
  const ipAddress = 'https://1.1.1.1';  // Direct IP with HTTPS protocol
  const start = performance.now();

  try {
    await fetch(ipAddress, {
      mode: 'no-cors',  // Prevent CORS blocking
      cache: 'no-store',  // Prevent cached responses
    });

    const end = performance.now();
    const ping = ((end - start) / 100).toFixed(2);  // Convert to seconds
    document.getElementById('network-status').textContent = `${ping} ms`;
  } catch (error) {
    const end = performance.now();
    const ping = ((end - start) / 100).toFixed(2);  // Still measure even on failure
    document.getElementById('network-status').textContent = `${ping} ms (Request Failed)`;
  }
}

checkPing();

</script>

  <!-- Apex Charts -->
  <script src="{{ asset('assets/js/chart/apex-chart/apex-chart.js') }}"></script>
  <script src="{{ asset('assets/js/chart/apex-chart/stock-prices.js') }}"></script>
  <script src="{{ asset('assets/js/chart/apex-chart/moment.min.js') }}"></script>

  <!-- DataTables -->
  <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/datatable.custom.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/datatable.custom1.js') }}"></script>

  <!-- Counter -->
  <script src="{{ asset('assets/js/counter/jquery.waypoints.min.js') }}"></script>
  <script src="{{ asset('assets/js/counter/jquery.counterup.min.js') }}"></script>
  <script src="{{ asset('assets/js/counter/counter-custom.js') }}"></script>

  <!-- Animation -->
  <script src="{{ asset('assets/js/animation/wow/wow.min.js') }}"></script>
  <script>
    new WOW().init();
  </script>
@endsection
