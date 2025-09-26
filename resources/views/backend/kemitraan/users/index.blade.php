@extends('backend.layouts.app_new')
@section('main')
@section('title', 'kemitraan User')
<!-- Content -->
<div class="container-fluid">
    <!-- Page header -->
    <div class="row align-items-center mb-7">
        <!-- Breadcrumb and Title -->
        <div class="col-md-6 mb-3 mb-md-0">
            <h4 class="main-title">kemitraan User</h4>
            <ul class="app-line-breadcrumbs mb-0">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <span><i class="ti ti-router f-s-16"></i> kemitraan</span>
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="javascript:void(0)">User</a>
                </li>
            </ul>
        </div>

        <!-- Buttons -->
        @if (in_array(multi_auth()->role, ['Admin', 'Teknisi']) || (multi_auth()->role === 'Mitra' && multi_auth()->user === 1))
            <div class="d-flex flex-wrap justify-content-md-end gap-2">
                @if (multi_auth()->role === 'Admin')
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-warning text-white dropdown-toggle"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-edit"></i> Action
                            <span class="row-count badge bg-dark text-white ms-1"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" id="enableMassal">Aktifkan</a></li>
                            <li><a class="dropdown-item" id="disableMassal">Suspend</a></li>
                            <li><a class="dropdown-item" id="registMassal">Proses Registrasi</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" id="reactideleteMassalate">Hapus</a></li>
                        </ul>
                    </div>

                    <button class="btn btn-sm btn-success text-white" type="button" id="downloadExcel"
                        data-bs-toggle="modal" data-bs-target="#export" onclick="downloadExcel()">
                        <i class="ti ti-download me-1"></i> Export
                        <span class="row-count badge bg-dark text-white"></span>
                    </button>

                    <button class="btn btn-sm btn-warning text-white" data-bs-toggle="modal" data-bs-target="#import">
                        <i class="ti ti-file-import me-1"></i> Import
                    </button>
                @endif
            </div>
        @endif
    </div>

    <br />


    <!-- Page content -->
    {{-- @include('backend.kemitraan.user.modal.create') --}}
        {{--@include('backend.kemitraan.user.modal.edit') --}}
            {{--@include('backend.kemitraan.user.modal.import') --}}
                {{--@include('backend.kemitraan.user.modal.show_session') --}}
    <div class="row mb-4">
        @if (session('error'))
            <div class="alert alert-light-border-danger d-flex align-items-center justify-content-between"
                role="alert">
                <p class="mb-0">
                    <i class="ti ti-x f-s-18 me-2"></i>{{ session('error') }}
                </p>
                <i class="ti ti-x" data-bs-dismiss="alert"></i>
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-light-border-primary d-flex align-items-center justify-content-between"
                role="alert">
                <p class="mb-0">
                    <i class="ti ti-check f-s-18 me-2"></i>{{ session('success') }}
                </p>
                <i class="ti ti-x" data-bs-dismiss="alert"></i>
            </div>
        @endif
        <div class="row">
            <!-- Card 1: User Total -->
            <div class="col-12 col-md-6 col-xxl-3 mb-4">
                <div class="card text-center h-100">
                    <span class="bg-primary h-50 w-50 d-flex-center rounded-circle m-auto eshop-icon-box">
                        <i class="ti ti-users f-s-24"></i>
                    </span>
                    <div class="card-body eshop-cards">
                        <span class="ripple-effect"></span>
                        <h3 class="text-primary mb-0" id="totaluser">{{ $totaluser }}</h3>
                        <p class="mg-b-35 f-w-600 text-dark-800 txt-ellipsis-1">USER TOTAL</p>
                    </div>
                </div>
            </div>

            <!-- Card 2: User New -->
            <div class="col-12 col-md-6 col-xxl-3 mb-4">
                <div class="card text-center h-100">
                    <span class="bg-warning h-50 w-50 d-flex-center rounded-circle m-auto eshop-icon-box">
                        <i class="ti ti-users f-s-24"></i>
                    </span>
                    <div class="card-body eshop-cards">
                        <span class="ripple-effect"></span>
                        <h3 class="text-warning mb-0" id="totaldisabled">0</h3>
                        <p class="mg-b-35 f-w-600 text-dark-800 txt-ellipsis-1">USER PENDING</p>
                    </div>
                </div>
            </div>

            <!-- Card 3: User Active -->
            <div class="col-12 col-md-6 col-xxl-3 mb-4">
                <div class="card text-center h-100">
                    <span class="bg-success h-50 w-50 d-flex-center rounded-circle m-auto eshop-icon-box">
                        <i class="ti ti-users f-s-24"></i>
                    </span>
                    <div class="card-body eshop-cards">
                        <span class="ripple-effect"></span>
                        <h3 class="text-success mb-0" id="totalactive">0</h3>
                        <p class="mg-b-35 f-w-600 text-dark-800 txt-ellipsis-1">USER ACTIVE</p>
                    </div>
                </div>
            </div>

            <!-- Card 4: User Expired -->
            <div class="col-12 col-md-6 col-xxl-3 mb-4">
                <div class="card text-center h-100">
                    <span class="bg-danger h-50 w-50 d-flex-center rounded-circle m-auto eshop-icon-box">
                        <i class="ti ti-users f-s-24"></i>
                    </span>
                    <div class="card-body eshop-cards">
                        <span class="ripple-effect"></span>
                        <h3 class="text-danger mb-0" id="totalsuspend">0</h3>
                        <p class="mg-b-35 f-w-600 text-dark-800 txt-ellipsis-1">USER SUSPEND</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>

    <!-- Buttons -->
    <div class="row">
      <div class="col-12">
        <div class="card overview-details-box b-s-3-primary ">
          <div class="card-body">
            <div class="row">
              <div class="col-md-12">
                <div class="d-flex align-items-center gap-1">
                  <div class="flex-grow-1">
                    <a href="{{ route('kemitraan.users.create') }}" type="button" class="btn btn-light-success h-60" ><i class="fa-solid fa-plus"></i> Add Users</a> 
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    

    <div class="row">
        <div class="card-body table-responsive">
            <table id="myTable" class="table table-responsive table-hover display nowrap" width="100%">
                <thead>
                    <tr>
                        <th>ID Pelanggan</th>
                        <th>Nama Pelanggan</th>
                        <th>Tanggal Buat</th>
                        <!-- <th>Service</th>
                        <th>Due Data</th>
                        <th>Status</th> -->
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script type="text/javascript">
    const baseurl = "{{ url('/') }}" //baseUrl.clone().pop().pop().pop().pop();
    let table = $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        order: [
            [0, 'desc']
        ],
        lengthMenu: [10, 100, 500, 1000, 2000],
        ajax: '{{ url()->current() }}',
        columns: [
            {
                data: 'id_pelanggan',
                name: 'id_pelanggan',
            },
            {
                data: 'full_name',
                name: 'full_name',
            },
            {
                data: 'created_at',
                name: 'created_at',
            }
        ]
    });

</script>
@endpush
