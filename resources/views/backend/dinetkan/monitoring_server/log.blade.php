@extends('backend.layouts.app_new')

@section('title', 'Log Server')

@section('css')
<style>
    .progress-container {
        width: 100%;
        background-color: #f3f3f3;
        border-radius: 8px;
        box-shadow: inset 0 0 5px #ccc;
        margin: 20px auto;
        padding: 3px;
    }

    .progress-bar {
        height: 24px;
        width: 0%;
        background-color: #4caf50;
        border-radius: 5px;
        text-align: center;
        color: #fff;
        line-height: 24px;
        transition: width 0.5s ease;
    }
</style>
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">

  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select2.css') }}">
@endsection

@section('main')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6 ps-0">
          <h3>Log Server</h3>
        </div>
        <div class="col-sm-6 pe-0">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dinetkan.dashboard') }}">
                <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item active">Log Server</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Container-fluid starts-->
   
  

  <div class="container-fluid user-management-page">
    <div class="row">
      <div class="col-xxl-12 box-col-12">
        <div class="card">
          <div class="card-body">
            <div class="bg-light-primary b-r-15">
              <div class="upcoming-box d-flex align-items-center justify-content-between px-4">
                <form method="get" action="{{ route('dinetkan.monitoring.server.log.log') }}">
                  <input type="hidden" name="filter" value="today">
                  <button type="submit" class="btn btn-primary" id="openCreateModal">Today</button>
                </form>
                
                <form method="get" action="{{ route('dinetkan.monitoring.server.log.log') }}">
                  <input type="hidden" name="filter" value="week">
                  <button type="submit" class="btn btn-primary" id="openCreateModal">Last 1 Week</button>
                </form>
                
                <form method="get" action="{{ route('dinetkan.monitoring.server.log.log') }}">
                  <input type="hidden" name="filter" value="month">
                  <button type="submit" class="btn btn-primary" id="openCreateModal">Last 1 Month</button>
                </form>
                
                <form method="get" action="{{ route('dinetkan.monitoring.server.log.log') }}">
                  <input type="hidden" name="filter" value="3month">
                  <button type="submit" class="btn btn-primary" id="openCreateModal">Last 3 Month</button>
                </form>
                
                <form method="get" action="{{ route('dinetkan.monitoring.server.log.log') }}">
                  <input type="hidden" name="filter" value="6month">
                  <button type="submit" class="btn btn-primary" id="openCreateModal">Last 6 Month</button>
                </form>
                
                <form method="get" action="{{ route('dinetkan.monitoring.server.log.log') }}">
                  <input type="hidden" name="filter" value="1year">
                  <button type="submit" class="btn btn-primary" id="openCreateModal">Last 1 Year</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body">
            <div class="col-sm-6 ps-0">
              <h3>{{ $type }}</h3>
            </div>
            <div class="table-responsive custom-scrollbar">
            <table id="logTable" class="table-hover display nowrap clickable table" width="100%">
                <tbody>
                @forelse ($results as $row)
                    <tr>
                    <td style="width:25%">
                      {{ $row['server_name'] }}
                      <br>
                      {{ $row['server_address'] }}
                    </td>
                    <td>
                      <div class="progress-container">
                          <div class="progress-bar" id="progressBar" style="width:<?php echo $row['percentUp'] ?>%; background-color: #4caf50;"><?php echo $row['percentUp'] ?>%</div>
                      </div>
                    </td>
                    </tr>
                @empty
                @endforelse
                </tbody>
            </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Container-fluid Ends-->



@endsection

@section('scripts')
  <script src="{{ asset('assets/radiusqu/dist/js/moment.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/datatable.custom.js') }}"></script>
@endsection
