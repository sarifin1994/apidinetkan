@extends('backend.layouts.app')

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
      <div class="col-xxl-12 col-xl-12 col-lg-12 box-col-12">
        <div class="row">
          <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
            <a href="{{ route('teknisi.ticket.new.index') }}">
              <div class="card total-sales">
                <div class="card-body">
                  <div class="row">
                    <div class="col-xl-8 xl-12 col-md-8 col-sm-12 col box-col-12">
                      <div class="d-flex">
                        <span>
                          <i class="fa-solid fa-users"></i>
                        </span>
                        <div class="flex-shrink-0">
                          <h4>{{ $newClientOpenTickets }}</h4>
                          <h6>New Client Open Tickets</h6>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </a>
          </div>
          <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
            <a href="{{ route('teknisi.ticket.outage.index') }}">
              <div class="card total-sales">
                <div class="card-body">
                  <div class="row">
                    <div class="col-xl-8 xl-12 col-md-8 col-sm-12 col box-col-12">
                      <div class="d-flex up-sales">
                        <span>
                          <i class="fa-solid fa-users"></i>
                        </span>
                        <div class="flex-shrink-0">
                          <h4>{{ $outageOpenTickets }}</h4>
                          <h6>Outage Open Tickets</h6>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </a>
          </div>
          <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
            <a href="{{ route('teknisi.ticket.new.index') }}">
              <div class="card total-sales">
                <div class="card-body">
                  <div class="row">
                    <div class="col-xl-8 xl-12 col-md-8 col-sm-12 col box-col-12">
                      <div class="d-flex total-customer">
                        <span>
                          <i class="fa-solid fa-receipt"></i>
                        </span>
                        <div class="flex-shrink-0">
                          <h4>{{ $closedNewClientTickets }}</h4>
                          <h6>Closed New Client Tickets</h6>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </a>
          </div>
          <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
            <a href="{{ route('teknisi.ticket.outage.index') }}">
              <div class="card total-sales">
                <div class="card-body">
                  <div class="row">
                    <div class="col-xl-8 xl-12 col-md-8 col-sm-12 col box-col-12">
                      <div class="d-flex total-product">
                        <span>
                          <i class="fa-solid fa-receipt"></i>
                        </span>
                        <div class="flex-shrink-0">
                          <h4>{{ $closedOutageTickets }}</h4>
                          <h6>Closed Outage Tickets</h6>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </a>
          </div>
        </div>
      </div>

      <div class="col-xl-12">
        <div class="card">
          <div class="card-header pb-0">
            <div class="header-top">
              <h5>New Clients & Outage</h5>
            </div>
          </div>
          <div class="card-body p-0">
            <div id="Statistics" style="height: 100%"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Container-fluid Ends-->
@endsection

@section('scripts')
  <script src="{{ asset('assets/js/chart/apex-chart/apex-chart.js') }}"></script>
  <script src="{{ asset('assets/js/chart/apex-chart/moment.min.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/datatable.custom.js') }}"></script>
  <script src="{{ asset('assets/js/dashboard/teknisi.js') }}"></script>
@endsection
