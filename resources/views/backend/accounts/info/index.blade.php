@extends('backend.layouts.app')

@section('title', 'Account Info')

@section('css')
@endsection

@section('main')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6 ps-0">
          <h3>Account Info</h3>
        </div>
        <div class="col-sm-6 pe-0">
          <ol class="breadcrumb">
            <li class="breadcrumb-item">
              <a href="../">
                <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg>
              </a>
            </li>
            <li class="breadcrumb-item">Account</li>
            <li class="breadcrumb-item active">Info</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Container-fluid starts -->
  <div class="container-fluid">
    <div class="row">
      <!-- Account Information Card -->
      <div class="col-xl-6 mb-4">
        <div class="card rounded border-0 shadow-sm">
          <div class="card-header d-flex align-items-center text-white">
            <h5 class="d-flex align-items-center mb-0">
              <i class="fas fa-user-circle me-2"></i> Informasi Akun
            </h5>
          </div>
          <div class="card-body">
            <table class="table-borderless mb-0 table">
              <tbody>
                <tr>
                  <td width="40%" class="fw-bold text-muted">Nama Lengkap</td>
                  <td>{{ auth()->user()->name }}</td>
                </tr>
                <tr>
                  <td class="fw-bold text-muted">Username</td>
                  <td>{{ auth()->user()->username }}</td>
                </tr>
                <tr>
                  <td class="fw-bold text-muted">Lisensi</td>
                  <td>Radiusqu {{ $license->name ?? 'Free' }}</td>
                </tr>
                <tr>
                  <td class="fw-bold text-muted">Jatuh Tempo</td>
                  <td>{{ \Carbon\Carbon::parse(auth()->user()->next_due)->format('d/m/Y') }}</td>
                </tr>
                <tr>
                  <td class="fw-bold text-muted">Status</td>
                  <td>
                    @if (auth()->user()->status === 3)
                      <span class="badge bg-danger">Expired</span>
                    @else
                      <span class="badge bg-success">Active</span>
                    @endif
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Usage Details Card -->
      <div class="col-xl-6 mb-4">
        <div class="card rounded border-0 shadow-sm">
          <div class="card-header d-flex align-items-center text-white">
            <h5 class="d-flex align-items-center mb-0">
              <i class="fas fa-chart-line me-2"></i> Rincian Penggunaan
            </h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <div class="d-flex justify-content-between">
                <span class="fw-bold text-muted">Staff Users</span>
                <span>{{ number_format($staffUsers, 0, '.', '.') }} /
                  {{ number_format($license->limit_user ?? 0, 0, '.', '.') }}</span>
              </div>
              <div class="progress bg-secondary mt-2" style="height: 15px;">
                <div class="progress-bar bg-primary" style="width: {{ $staffUsersPercentage }}%;">
                  {{ number_format($staffUsers, 0, '.', '.') }}
                </div>
              </div>
            </div>
            <div class="mb-3">
              <div class="d-flex justify-content-between">
                <span class="fw-bold text-muted">Mikrotik NAS</span>
                <span>{{ number_format($nas, 0, '.', '.') }} /
                  {{ number_format($license->limit_nas ?? 0, 0, '.', '.') }}</span>
              </div>
              <div class="progress bg-secondary mt-2" style="height: 15px;">
                <div class="progress-bar bg-primary" style="width: {{ $nasPercentage }}%;">
                  {{ number_format($nas, 0, '.', '.') }}
                </div>
              </div>
            </div>
            <div class="mb-3">
              <div class="d-flex justify-content-between">
                <span class="fw-bold text-muted">PPPoE User</span>
                <span>{{ number_format($user_pppoe, 0, '.', '.') }} /
                  {{ number_format($license->limit_pppoe ?? 0, 0, '.', '.') }}</span>
              </div>
              <div class="progress bg-secondary mt-2" style="height: 15px;">
                <div class="progress-bar bg-warning" style="width: {{ $userPppoePercentage }}%;">
                  {{ number_format($user_pppoe, 0, '.', '.') }}
                </div>
              </div>
            </div>
            <div class="mb-3">
              <div class="d-flex justify-content-between">
                <span class="fw-bold text-muted">Hotspot User</span>
                <span>{{ number_format($user_hs, 0, '.', '.') }} /
                  {{ number_format($license->limit_hs ?? 0, 0, '.', '.') }}</span>
              </div>
              <div class="progress bg-secondary mt-2" style="height: 15px;">
                <div class="progress-bar bg-danger" style="width: {{ $userHsPercentage }}%;">
                  {{ number_format($user_hs, 0, '.', '.') }}
                </div>
              </div>
            </div>
            <div class="mb-3">
              <div class="d-flex justify-content-between">
                <span class="fw-bold text-muted">VPN User</span>
                <span>{{ number_format($vpn, 0, '.', '.') }} /
                  {{ number_format($license->limit_vpn ?? 0, 0, '.', '.') }}</span>
              </div>
              <div class="progress bg-secondary mt-2" style="height: 15px;">
                <div class="progress-bar bg-info" style="width: {{ $vpnPercentage }}%;">
                  {{ number_format($vpn, 0, '.', '.') }}
                </div>
              </div>
            </div>
            <div class="mb-3">
              <div class="d-flex justify-content-between">
                <span class="fw-bold text-muted">VPN Remote User</span>
                <span>{{ number_format($vpnRemote, 0, '.', '.') }} /
                  {{ number_format($license->limit_vpn_remote ?? 0, 0, '.', '.') }}</span>
              </div>
              <div class="progress bg-secondary mt-2" style="height: 15px;">
                <div class="progress-bar bg-success" style="width: {{ $vpnRemotePercentage }}%;">
                  {{ number_format($vpnRemote, 0, '.', '.') }}
                </div>
              </div>
            </div>
            <div class="mb-3">
              <div class="d-flex justify-content-between">
                <span class="fw-bold text-muted">EPON OLT Limit</span>
                <span>{{ number_format($eponOlt, 0, '.', '.') }} /
                  {{ number_format($license->limit_epon ?? 0, 0, '.', '.') }}</span>
              </div>
              <div class="progress bg-secondary mt-2" style="height: 15px;">
                <div class="progress-bar bg-secondary" style="width: {{ $eponOltPercentage }}%;">
                  {{ number_format($eponOlt, 0, '.', '.') }}
                </div>
              </div>
            </div>
            <div class="mb-3">
              <div class="d-flex justify-content-between">
                <span class="fw-bold text-muted">GPON OLT Limit</span>
                <span>{{ number_format($gponOlt, 0, '.', '.') }} /
                  {{ number_format($license->limit_gpon ?? 0, 0, '.', '.') }}</span>
              </div>
              <div class="progress bg-secondary mt-2" style="height: 15px;">
                <div class="progress-bar bg-secondary" style="width: {{ $gponOltPercentage }}%;">
                  {{ number_format($gponOlt, 0, '.', '.') }}
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer text-end">
            <a class="btn btn-outline-primary" href="{{ route('admin.account.licensing.index') }}">
              Upgrade Lisensi <i class="fas fa-angle-right"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Container-fluid Ends-->
@endsection

@section('scripts')
@endsection
