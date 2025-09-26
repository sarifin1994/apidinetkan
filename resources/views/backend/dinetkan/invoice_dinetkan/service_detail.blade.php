@extends('backend.layouts.app_new')

@section('title', 'Service Detail')

@section('css')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/animate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/datatables.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/select2.css') }}">

@endsection

@section('main')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6 ps-0">
          <h3>Service Detail</h3>
        </div>
        <div class="col-sm-6 pe-0">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="">
                <!-- <svg class="stroke-icon">
                  <use href="{{ asset('assets/old/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg> -->
              </a>
            </li>
            <!-- <li class="breadcrumb-item active">Mitra Detail</li> -->
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- container-fluid starts-->
  <div class="container-fluid user-management-page">
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body">
            <div class="modal-body"> 
              <form method="post" id="adminForm">  
                @csrf
                <input type="hidden" name="service_id" value="{{ $mapping->service_id }}">
                <div class="row">
                  <div class="mb-3 col-sm-12">
                    <h3>Service ID {{ $mapping->service_id }}</h3>
                  </div>
                  <div class="mb-3 col-12">
                    <label for="service" class="form-label">Service</label>
                    <input type="text" class="form-control" id="service" name="service" value="{{ $mapping->service->name }}" disbaled readonly>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" value="{{ $service_detail ? $service_detail->first_name : ''}}" required>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $service_detail ? $service_detail->last_name : ''}}" required>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ $service_detail ? $service_detail->email : ''}}">
                  </div>
                  <div class="mb-3 col-6">
                    <label for="whatsapp" class="form-label">WhatsApp</label>
                    <input type="text" class="form-control" id="whatsapp" name="whatsapp" value="{{ $service_detail ? $service_detail->whatsapp : ''}}" required>
                  </div>
                  <!-- <div class="mb-3 col-6">
                    <label for="company_name" class="form-label">Company Name</label>
                    <input type="text" class="form-control" id="company_name" name="company_name" >
                  </div> -->
                  <!-- <div class="mb-3 col-6">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                    <option value="4">NEW</option>
                    </select>
                  </div> -->
                  <div class="mb-3 col-6">
                    <label for="id_card" class="form-label">No. KTP / No. SIM </label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="id_card" name="id_card" class="form-control'" value="{{ $service_detail ? $service_detail->id_card : ''}}" required>
                    </div>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="npwp" class="form-label">NPWP</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="npwp" name="npwp" class="form-control" value="{{ $service_detail ? $service_detail->npwp : ''}}" required>
                    </div>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="provinsi" class="mb-1">Provinsi</label>
                    <div class="form-group mb-3" style="display:grid">
                    <select class="form-control" id="provinsi" name="province_id" autocomplete="off" data-placeholder="Pilih Provinsi">
                        <option value=""></option>
                        @forelse ($provinces as $pp)
                            <option value="{{ $pp->id }}" <?php if($service_detail->province_id == $pp->id){echo 'selected';}?> >{{ $pp->name }}</option>
                        @empty
                        @endforelse
                    </select>
                    </div>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="kabupaten" class="mb-1">Kota / Kabupaten</label>
                    <div class="form-group mb-3" style="display:grid">
                    <select class="form-control" id="kabupaten" name="regency_id" autocomplete="off" data-placeholder="Pilih Kota / Kabupaten">
                        <option value=""></option>
                        @forelse ($regencies as $pp)
                            <option value="{{ $pp->id }}" <?php if($service_detail->regency_id == $pp->id){echo 'selected';}?> >{{ $pp->name }}</option>
                        @empty
                        @endforelse
                    </select>

                    </div>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="kecamatan" class="mb-1">Kecamatan</label>
                    <div class="form-group mb-3" style="display:grid">
                    <select class="form-control" id="kecamatan" name="district_id" autocomplete="off" data-placeholder="Pilih Kecamatan">
                        <option value=""></option>
                        @forelse ($districts as $pp)
                            <option value="{{ $pp->id }}" <?php if($service_detail->district_id == $pp->id){echo 'selected';}?> >{{ $pp->name }}</option>
                        @empty
                        @endforelse
                    </select>

                    </div>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="desa" class="mb-1">Desa / Kelurahan </label>
                    <div class="form-group mb-3" style="display:grid">
                    <select class="form-control" id="desa" name="village_id" autocomplete="off" data-placeholder="Pilih Desa">
                        <option value=""></option>
                        @forelse ($villages as $pp)
                            <option value="{{ $pp->id }}" <?php if($service_detail->village_id == $pp->id){echo 'selected';}?> >{{ $pp->name }}</option>
                        @empty
                        @endforelse
                    </select>

                    </div>
                  </div>
                  <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <div class="input-group">
                        <textarea required class="form-control" id="address" name="address" rows="3" class="form-control">{{ $service_detail ? $service_detail->address : ''}}</textarea>
                    </div>
                  </div>
                  <div style="margin-top:100px"></div>
                  <div class="col-md-12 mb-3">
                      <label for="address" class="form-label">Lokasi</label>
                      <div class="input-group">
                          <div id="map"></div>
                      </div>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="latitude" class="form-label">Latitude</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="latitude" name="latitude" class="form-control" onchange="set_maps()" value="{{ $service_detail ? $service_detail->latitude : ''}}" required>
                    </div>
                  </div>
                  <div class="mb-3 col-6">
                    <label for="latitude" class="form-label">Longitude</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="longitude" name="longitude" class="form-control" onchange="set_maps()" value="{{ $service_detail ? $service_detail->longitude : ''}}" required>
                    </div>
                  </div>
                  <div class="mb-3 col-6">
                    <button type="submit" class="btn btn-light-primary">Save</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div> 
    
    <div style="margin-top:30px"></div>

    <div class="row">
      <!-- Account Information Card -->
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body">   
            <div class="mb-3 col-sm-12">
                <h3>Informasi Dokumen</h3>
              </div>     
            <!-- <form method="post" action="{{ route('dinetkan.users_dinetkan.update_doc_info_dinetkan') }}" enctype="multipart/form-data">
              @csrf
              
              <input type="hidden" name="service_id" value="{{ $mapping->service_id }}">
              <div class="row">
                <div class="mb-3 col-6">
                  <label for="doc_id" class="form-label">Dokumen</label>
                  <select class="form-select" id="doc_id" name="doc_id" required>
                    <option value="">Pilih Dokumen</option>
                    @if($docType)
                      @foreach($docType as $doc)
                      <option value="{{$doc->id}}">{{$doc->name}}</option>
                      @endforeach
                    @endif
                  </select>
                </div>
                <div class="mb-3 col-6">
                  <label for="doc" class="form-label">Pilih File</label>
                  <input type="file" accept="image/jpg,image/jpeg,image/png, application/pdf" name="doc" id="doc" class="form-control">
                </div>
                <div class="mb-3 col-6">
                  <button type="submit" class="btn btn-light-primary">Save</button>
                </div>
              </div>
            </form> -->

            <form id="uploadDocForm" enctype="multipart/form-data" method="POST">
              @csrf
              <input type="hidden" name="service_id" value="{{ $mapping->service_id }}">

              <div class="row">
                  <div class="mb-3 col-6">
                      <label for="doc_id" class="form-label">Dokumen</label>
                      <select class="form-select" id="doc_id" name="doc_id" required>
                          <option value="">Pilih Dokumen</option>
                          @if($docType)
                              @foreach($docType as $doc)
                                  <option value="{{$doc->id}}">{{$doc->name}}</option>
                              @endforeach
                          @endif
                      </select>
                  </div>
                  <div class="mb-3 col-6">
                      <label for="doc" class="form-label">Pilih File</label>
                      <input type="file" accept="image/jpg,image/jpeg,image/png,application/pdf" name="doc" id="doc" class="form-control">
                  </div>
                  <div class="mb-3 col-6">
                      <button type="submit" class="btn btn-light-primary">Save</button>
                  </div>
              </div>
            </form>
            <div class="mb-3 col-12"> 
              <div class="table-responsive custom-scrollbar">
                <table id="myTable" class="table-hover display nowrap table" width="100%">
                  <thead>
                    <th>Dokumen</th>
                    <th>Lihat File</th>
                    <th>Aksi</th>
                  </thead>
                  @if($listDoc)
                    @foreach($listDoc as $row)
                      <tr>
                        <td>{{$row->docType->name}}</td>
                        <td>
                          @if($row->file_ext == 'pdf')
                          <a href="{{route('dinetkan.users_dinetkan.show_file', $row->id)}}" target="_blank">Lihat file</a>
                          @endif
                          @if($row->file_ext != 'pdf')
                          <a href="{{route('dinetkan.users_dinetkan.show_file', $row->id)}}" target="_blank">Lihat file</a>
                          @endif
                        </td>
                        <td>
                          <a href="javascript:void(0)" class="delete-doc-icon delete btn btn-light-danger" data-id="{{ $row->id }}">
                          <i class="fa-solid fa-trash-can"></i>
                          </a>
                         </td>
                      </tr>
                    @endforeach
                  @endif
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div style="margin-top:30px"></div>
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body">
            <form id="adminGraphForm" method="POST">
              @csrf
              <div class="alert alert-light-danger">
                <label>Jika </label>
                <label style="font-weight:bold">Active Graphic</label>
                <label>MIKROTIK grafik akan ambil dari dari api mikrotik</label>
                <label>Walaupun data cacti sudah ada</label>
              </div>
              <div class="modal-body">
                <input type="hidden" name="service_id" value="{{ $mapping->service_id }}">
                <div class="row">
                  <div class="mb-3 col-sm-12">
                    <h3>Add Graph</h3>
                  </div>
                  <div class="mb-3 col-md-4">
                    <label for="trafic_mrtg" class="form-label">Active Graphic</label>
                    <select class="form-select" id="graph_type" name="graph_type" required>
                      <option value="">PILIH</option>
                      <option value="cacti" {{ $service_detail->graph_type == 'cacti' ? 'selected' : ''}}>Cacti</option>
                      <option value="mikrotik" {{ $service_detail->graph_type == 'mikrotik' ? 'selected' : ''}}>Mikrotik</option>
                      <option value="libre" {{ $service_detail->graph_type == 'libre' ? 'selected' : ''}}>LibreNMS</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="mb-3 col-6">
                <a type="button" class="btn btn-light-secondary" href="{{ route('dinetkan.users_dinetkan') }}">Back</a>
                <button type="submit" class="btn btn-light-primary">Save</button>
              </div>
            </form>
            <hr>
            <form id="adminCactiForm" method="POST">
              @csrf
              <div class="modal-body">
              
                <input type="hidden" name="dinetkan_user_id" value="{{ $mapping->dinetkan_user_id }}">
                <input type="hidden" name="service_id" value="{{ $mapping->service_id }}">
                <div class="row">
                  <div class="mb-3 col-md-4">
                    <label for="trafic_mrtg" class="form-label">Trafic MRTG</label>
                    <select class="form-select" id="trafic_mrtg_tree" name="trafic_mrtg_tree" required>
                      <option value="">Select Tree</option>
                      @if($tree)
                        @foreach($tree as $row)
                          <option value="{{$row['value']}}">{{$row['label']}}</option>
                        @endforeach
                      @endif
                    </select>
                  </div>
                  <div class="mb-3 col-md-4">
                    <label for="trafic_mrtg_tree_node" class="form-label">Tree Node</label>
                    <select class="form-select" id="trafic_mrtg_tree_node" name="trafic_mrtg_tree_node" required>
                      <option value="">Select Node</option>
                    </select>
                  </div>
                  <div class="mb-3 col-md-4">
                    <label for="page_graph" class="form-label">Page Graph</label>
                    <select class="form-select" id="page_graph" name="page_graph" required>
                      <option value="1">1</option>
                      <option value="2">2</option>
                      <option value="3">3</option>
                      <option value="4">4</option>
                      <option value="5">5</option>
                      <option value="6">6</option>
                      <option value="7">7</option>
                      <option value="8">8</option>
                      <option value="9">9</option>
                      <option value="10">10</option>
                      <option value="11">11</option>
                      <option value="12">12</option>
                      <option value="13">13</option>
                      <option value="14">14</option>
                      <option value="15">15</option>
                      <option value="16">16</option>
                      <option value="17">17</option>
                      <option value="18">18</option>
                      <option value="19">19</option>
                      <option value="20">20</option>
                    </select>
                  </div>
                  <div class="mb-3 col-md-4">
                    <label for="trafic_mrtg_graph" class="form-label">Graph</label>
                    <select class="form-select" id="trafic_mrtg_graph" name="trafic_mrtg_graph" required>
                      <option value="">Select Graph</option>
                    </select>
                  </div>
                  <div class="mb-3 col-md-4">
                    <label for="graph_name" class="form-label">Graph Name</label>
                    <input type="text" class="form-control" id="graph_name" name="graph_name" required placeholder="graph name" value="{{ $service_detail->graph_name }}">
                    <input type="hidden" id="selected_value" readonly>
                  </div>
                </div>
              </div>
              <div class="mb-3 col-6">
                <a type="button" class="btn btn-light-secondary" href="{{ route('dinetkan.users_dinetkan') }}">Back</a>
                <button type="submit" class="btn btn-light-primary">Save</button>
              </div>
            </form>

            <div class="table-responsive custom-scrollbar" id="row_create">
            <table id="myTable" class="table-hover display nowrap table" width="100%">
                <thead>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Action</th>
                </thead>
                @if($userdinetkanGraph)
                  @foreach($userdinetkanGraph as $row)
                    <tr>
                      <td>{{$row->graph_id}}</td>
                      <td>{{$row->graph_name}}</td>
                      <td>
                        <a href="javascript:void(0)" class="delete-icon delete btn btn-light-danger" data-id="{{ $row->id }}">
                        <i class="fa-solid fa-trash-can"></i>
                        </a>
                      </td>
                    </tr>
                  @endforeach
                @endif
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div style="margin-top:30px"></div>

    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body">
            <div class="modal-body">              
              <div class="row">
                <div class="mb-3 col-sm-12">
                  <h3>Data</h3>
                </div>
                <form method="post" id="adminForm2">  
                  @csrf
                  <input type="hidden" name="service_id" value="{{ $mapping->service_id }}">
                  <div class="row">
                    <div class="mb-3 col-md-6">
                      <label for="vlan" class="form-label">VLAN</label>
                      <input class="form-control" id="vlan_text" name="vlan_text" value="<?php if(isset($service_detail->vlan)){echo $service_detail->vlan;}?>">
                    </div>
                    <div class="mb-3 col-md-6">
                      <label for="pop" class="form-label">POP</label>
                      <select class="form-select" id="pop_id" name="pop_id" required>
                        <option value="">Select Tree</option>
                        @if($pop)
                          @foreach($pop as $row)
                            <option value="{{$row->id}}" <?php if(isset($service_detail->pop_id)){if($service_detail->pop_id == $row->id){echo 'selected';}}?> >{{$row->name}}</option>
                          @endforeach
                        @endif
                      </select>
                    </div>
                    <div class="mb-3 col-md-6">
                      <label for="metro_id" class="form-label">Metro</label>
                      <select class="form-select" id="metro_id" name="metro_id">
                        <option value="">Select Metro</option>
                        @if($metro)
                          @foreach($metro as $row)
                            <option value="{{$row->id}}" <?php if(isset($service_detail->metro_id)){if($service_detail->metro_id == $row->id){echo 'selected';}}?> >{{$row->name}}</option>
                          @endforeach
                        @endif
                      </select>
                    </div>
                    <div class="mb-3 col-md-6">
                      <label for="vendor" class="form-label">ID/CID/SO/SID Vendor</label>
                      <input type="text" class="form-control" id="vendor" name="vendor" value="<?php if(isset($service_detail->vendor)){ echo $service_detail->vendor;}?>">
                    </div>
                    <div class="mb-3 col-md-6">
                      <label for="ip_prefix" class="form-label">IP Prefix</label>
                      <input type="text" class="form-control" id="ip_prefix" name="ip_prefix" value="<?php if(isset($service_detail->vendor)){ echo $service_detail->ip_prefix;}?>">
                    </div>
                    <div class="mb-3 col-md-6">
                      <label for="id_wag" class="form-label">ID Whatsapp group</label>
                      <div class="form-group mb-3" style="display:grid">
                        <select class="form-control" id="id_wag" name="id_wag" autocomplete="off" data-placeholder="Pilih WAGroup">
                          <div class="row">
                          <option value=""></option>
                          @forelse ($wag as $row)
                              <option value="{{ $row->group_id }} " <?php if(isset($service_detail->group_id)){ if($row->group_id ==  $service_detail->group_id ){echo 'selected';}} ?>>{{ $row->group_name }}</option>
                          @empty
                          @endforelse
                          </div>
                        </select>
                      </div>
                    </div>
                    <div class="mb-3 col-md-6">
                      <label for="sn_modem" class="form-label">SN Modem</label>
                      <input type="text" class="form-control" id="sn_modem" name="sn_modem" value="<?php if(isset($service_detail->sn_modem)){ echo $service_detail->sn_modem;}?>">
                    </div>
                    <div class="mb-3 col-12">
                      <button type="submit" class="btn btn-light-primary">Save</button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    

    <div style="margin-top:30px"></div>

    <div class="row">
      <!-- Account Information Card -->
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body">   
            <div class="mb-3 col-sm-12">
                <h3>Api Mikrotik</h3>
              </div>     
            <form method="post" id="adminFormMikrotik">
              @csrf
              <input type="hidden" name="service_id" value="{{ $mapping->service_id }}">
              <div class="row">
                <div class="mb-3 col-6">
                  <label for="doc_id" class="form-label">Mikrotik</label>
                  <select class="form-select" id="id_mikrotik" name="id_mikrotik" required>
                    <option value="">Pilih Mikrotik</option>
                    @if($mikrotik)
                      @foreach($mikrotik as $row)
                      <option value="{{$row->id}}">{{$row->name}} - {{$row->ip}}</option>
                      @endforeach
                    @endif
                  </select>
                </div>
                  <div class="mb-3 col-6">
                    <label for="id_vlan" class="mb-1">VLAN</label>
                      <select class="form-control" id="id_vlan" name="id_vlan" autocomplete="off" data-placeholder="VLAN">
                      </select>
                  </div>
                <div class="mb-3 col-6">
                  <button type="submit" class="btn btn-light-primary">Save</button>
                </div>
              </div>
            </form>
            <div class="mb-3 col-12"> 
              <div class="table-responsive custom-scrollbar">
                <table id="myTable" class="table-hover display nowrap table" width="100%">
                  <thead>
                    <th>Mikrotik</th>
                    <th>Vlan</th>
                    <th>Status VLAN</th>
                    <th>Aksi</th>
                  </thead>
                  <tbody>
                    @if($mikrotik_detail)
                    <tr>
                      <td>{{$mikrotik_detail->name}} - {{$mikrotik_detail->ip}}</td>
                      <td>{{$service_detail->vlan_name}}</td>
                      <td><span id="status_vlan"></span></td>
                      <td>
                        <button class="btn btn-light-primary btn-sm" onclick="enabled_vlan('{{ $mapping->service_id }}')">Enabled</button>
                        <button class="btn btn-light-danger btn-sm" onclick="disabled_vlan('{{ $mapping->service_id }}')">disabled</button>
                      </td>
                    </tr>
                    @endif
                  </tbody>
                  
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>


    <div style="margin-top:30px"></div>

    <div class="row">
      <!-- Account Information Card -->
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body">   
            <div class="mb-3 col-sm-12">
                <h3>API LibreNMS</h3>
              </div>     
            <form method="post" id="adminFormLibre">
              @csrf
              <input type="hidden" name="service_id" value="{{ $mapping->service_id }}">
              <div class="row">
                <div class="mb-3 col-6">
                  <label for="hostname" class="form-label">Device</label>
                  <select class="form-select" id="hostname" name="hostname" required>
                    <option value="">Pilih Device</option>
                    @if($devices)
                      @foreach($devices as $row)
                      <option value="{{$row['hostname']}}">{{$row['hostname']}} ({{$row['sysName']}})</option>
                      @endforeach
                    @endif
                  </select>
                </div>
                <div class="mb-3 col-6">
                  <label for="ifName" class="form-label">IFNAME</label>
                  <select class="form-select" id="ifName" name="ifName" required>
                  </select>
                </div>
                <div class="mb-3 col-12">
                  <button type="submit" class="btn btn-light-primary">Save</button>
                </div>
              </div>
            </form>
            <div class="mb-3 col-12"> 
              <div class="table-responsive custom-scrollbar">
                <table id="myTable" class="table-hover display nowrap table" width="100%">
                  <thead>
                    <th>Device</th>
                    <th>IfName</th>
                    <th>Aksi</th>
                  </thead>
                  <tbody>
                    @if($servicelibre)
                      @foreach($servicelibre as $row)
                      <tr>
                        <td>{{$row->hostname}}</td>
                        <td>{{$row->ifName}}</td>
                        <!-- <td><a class="btn btn-danger btn-sm" href="{{ route('dinetkan.invoice_dinetkan.order.delete_ifname',$row->id)}}">Delete</a></td> -->
                         <td>
                          <a href="javascript:void(0)" class="delete-libre-icon delete btn btn-light-danger" data-id="{{ $row->id }}">
                          <i class="fa-solid fa-trash-can"></i>
                          </a>
                         </td>
                      </tr>
                      @endforeach
                    @endif
                  </tbody>
                  
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  
  <div class="modal fade" id="deleteModal" tabindex="-1" aria-label="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteModalLabel">Delete Cacti ??</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="deleteForm" method="POST">
          @csrf
          <input type="hidden" id="deleteMethod" name="_method" value="POST">
          <div class="modal-body">
            <input type="hidden" id="id" name="id">
            <div class="row">
              <div class="mb-3">
                <input type="text" class="form-control" id="modal_graph_id" readonly>
              </div>
              <div class="mb-3">
                <input type="text" class="form-control" id="modal_graph_name" readonly>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" id="modalSubmitBtn">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- container-fluid Ends-->

@endsection

@push('scripts')
  <script src="{{ asset('assets/old/radiusqu/dist/js/moment.js') }}"></script>
  <script src="{{ asset('assets/old/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
  <!-- <script src="{{ asset('assets/old/js/datatable/datatables/datatable.custom.js') }}"></script> -->
  <script>

      const form = $('#adminForm');
      // Form Submission
      form.on('submit', function(e) {
        Swal.fire({
            title: 'Please wait...',
            text: 'Processing Data ....',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        e.preventDefault();        

        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        $.ajax({
          url: '{{route('dinetkan.invoice_dinetkan.order.update_service_detail')}}',
          method: "POST",
          data: form.serialize(),
          success: function(response) {
            // modal.modal('hide');
            // toastr.success(response.message);
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: `${response.message}`,
                    showConfirmButton: false,
                    timer: 1500
                });
                location.reload();
          },
          error: function(xhr) {
            const errors = xhr.responseJSON.errors;
            let message = '';

            for (const key in errors) {
              message += errors[key] + '\n';
            }
            // toastr.error(message);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: `${message}`,
                        showConfirmButton: false,
                        timer: 1500
                    });
          }
        });
      });
      
      const form2 = $('#adminForm2');
      // Form Submission
      form2.on('submit', function(e) {
        Swal.fire({
            title: 'Please wait...',
            text: 'Processing Data ....',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        e.preventDefault();        

        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        $.ajax({
          url: '{{route('dinetkan.invoice_dinetkan.order.update_service_detail_2')}}',
          method: "POST",
          data: form2.serialize(),
          success: function(response) {
            // modal.modal('hide');
            // toastr.success(response.message);
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: `${response.message}`,
                    showConfirmButton: false,
                    timer: 1500
                });
                location.reload();
          },
          error: function(xhr) {
            const errors = xhr.responseJSON.errors;
            let message = '';

            for (const key in errors) {
              message += errors[key] + '\n';
            }
            // toastr.error(message);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: `${message}`,
                        showConfirmButton: false,
                        timer: 1500
                    });
          }
        });
      });

      const formMikrotik = $('#adminFormMikrotik');
      // Form Submission
      formMikrotik.on('submit', function(e) {
        Swal.fire({
            title: 'Please wait...',
            text: 'Processing Data ....',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        e.preventDefault();        

        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        $.ajax({
          url: '{{ route('dinetkan.invoice_dinetkan.order.mikrotik_update_service_detail') }}',
          method: "POST",
          data: formMikrotik.serialize(),
          success: function(response) {
            // modal.modal('hide');
            // toastr.success(response.message);
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: `${response.message}`,
                    showConfirmButton: false,
                    timer: 1500
                });
                location.reload();
          },
          error: function(xhr) {
            const errors = xhr.responseJSON.errors;
            let message = '';

            for (const key in errors) {
              message += errors[key] + '\n';
            }
            // toastr.error(message);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: `${message}`,
                        showConfirmButton: false,
                        timer: 1500
                    });
          }
        });
      });

    const baseurl = "{{ url('/') }}" //baseUrl.clone().pop().pop().pop().pop();
    $('#provinsi').select2();
    $.ajax({
    url: baseurl + '/dinetkan/settings/master/geo/provinces',
      type: "GET",
      success: function(data) {
        $('#provinsi').select2({
          data: (() => {
            return data.map((item) => {
                return {
                  id: item.id,
                  text: item.name
                }
              })
              .sort((a, b) => a.text.localeCompare(b.text));
          })(),
          allowClear: true,
          placeholder: $(this).data('placeholder'),
          // allowClear: true,
          // dropdownParent: $("#sync .modal-content"),
          width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
          // placeholder: $(this).data('placeholder'),
          // dropdownParent: $("#adminModal .modal-content"),
        });
      }
    });
    

    $('#kabupaten').select2();
    $('#provinsi').on('change', function() {
      $('#kabupaten').empty();
      var id_provinsi = $(this).val();
      console.log(id_provinsi);
      if (id_provinsi) {
        $.ajax({
          url: baseurl + '/dinetkan/settings/master/geo/regencies/' + id_provinsi,
          type: 'GET',
          dataType: "json",
          success: function(data) {
            $('#kabupaten').select2({
            data: (() => {
                return data.map((item) => {
                    return {
                    id: item.id,
                    text: item.name
                    }
                })
                .sort((a, b) => a.text.localeCompare(b.text));
            })(),
            allowClear: true,
            placeholder: $(this).data('placeholder'),
            // dropdownParent: $("#adminModal .modal-content"),
            });
          }
        });
      } else {
        // $('#kabupaten').empty();
      }
    });

    $('#kecamatan').select2();
    $('#kabupaten').on('change', function() {
      $('#kecamatan').empty();
    var id_kabupaten = $(this).val();
    if (id_kabupaten) {
        $.ajax({
        url: baseurl + '/dinetkan/settings/master/geo/districts/' + id_kabupaten,
        type: 'GET',
        dataType: "json",
        success: function(data) {
            $('#kecamatan').select2({
            data: (() => {
                return data.map((item) => {
                    return {
                    id: item.id,
                    text: item.name
                    }
                })
                .sort((a, b) => a.text.localeCompare(b.text));
            })(),
            allowClear: true,
            placeholder: $(this).data('placeholder'),
            // dropdownParent: $("#adminModal .modal-content"),
            });
        }
        });
    } else {
        // $('#kecamatan').empty();
    }
    });

    
    $('#desa').select2();
    $('#kecamatan').on('change', function() {
    $('#desa').empty();
    var id_kecamatan = $(this).val();
    if (id_kecamatan) {
        $.ajax({
        url: baseurl + '/dinetkan/settings/master/geo/villages/' + id_kecamatan,
        type: 'GET',
        dataType: "json",
        success: function(data) {
            $('#desa').select2({
            data: (() => {
                return data.map((item) => {
                    return {
                    id: item.id,
                    text: item.name
                    }
                })
                .sort((a, b) => a.text.localeCompare(b.text));
            })(),
            allowClear: true,
            placeholder: $(this).data('placeholder'),
            // dropdownParent: $("#adminModal .modal-content"),
            });
        }
        });
    } else {
        // $('#desa').empty();
    }
    });
    var lat = -6.200000;
    var lon = 106.816666;
    set_maps(lat,lon);
    var map = null;

  function set_maps(defaultLat = -6.200000, defaultLng = 106.816666) {
    if (map) {
      map.remove();
      map = null;
    }

    // FIX utama di sini
    if (L.DomUtil.get('map') != null) {
      L.DomUtil.get('map')._leaflet_id = null;
    }

    var latInput = document.getElementById("latitude").value.trim();
    var lngInput = document.getElementById("longitude").value.trim();

    var lat = latInput ? parseFloat(latInput) : defaultLat;
    var lng = lngInput ? parseFloat(lngInput) : defaultLng;

    map = L.map('map').setView([lat, lng], 10);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var marker = L.marker([lat, lng], { draggable: true }).addTo(map);

    marker.on('dragend', function (event) {
      var position = marker.getLatLng();
      document.getElementById('latitude').value = position.lat;
      document.getElementById('longitude').value = position.lng;
    });
  }

  </script>
  
  <script>  

    $('#trafic_mrtg_tree').on('change', function() {
    $('#trafic_mrtg_tree_node').empty();
    var id = $(this).val();
    if (id) {
        $.ajax({
        url:  baseurl + `/dinetkan/users_dinetkan/get_tree_node_mrtg/`+id,
        type: 'GET',
        dataType: "json",
        success: function(data) {
              if(data){
                let formattedData = data.map((item) => ({
                  id: item.value,
                  text: item.label
                })).sort((a, b) => a.text.localeCompare(b.text));

                // Tambahkan opsi default "Silahkan Pilih"
                formattedData.unshift({
                    id: '',
                    text: 'Silahkan Pilih',
                    disabled: false // Jangan disabled agar bisa dipilih
                });

                $('#trafic_mrtg_tree_node').select2({
                    data: formattedData,
                    allowClear: true,
                    dropdownParent: $("#adminCactiForm .modal-body"),
                });
            }else{
              $('#trafic_mrtg_tree_node').empty();
            }
          }
        });
    } else {
        $('#trafic_mrtg_tree_node').empty();
    }
    });

    $('#trafic_mrtg_tree_node').on('change', function() {
        let page_graph = document.getElementById("page_graph");
        let paging = page_graph.value;
        $('#trafic_mrtg_graph').empty();
        var id = $(this).val();
        if (id) {
            $.ajax({
                url: baseurl + `/dinetkan/users_dinetkan/get_graph_mrtg/` + id + `/` + paging,
                type: 'GET',
                dataType: "json",
                success: function(data) {
                    if (data) {
                        let formattedData = data.map((item) => ({
                            id: item.local_graph_id,
                            text: item.Title
                        })).sort((a, b) => a.text.localeCompare(b.text));

                        // Tambahkan opsi default "Silahkan Pilih"
                        formattedData.unshift({
                            id: '',
                            text: 'Silahkan Pilih',
                            disabled: false // Jangan disabled agar bisa dipilih
                        });

                        $('#trafic_mrtg_graph').select2({
                            data: formattedData,
                            allowClear: true,
                            dropdownParent: $("#adminCactiForm .modal-body"),
                        });
                    } else {
                        $('#trafic_mrtg_graph').empty();
                    }
                }
            });
        } else {
            $('#trafic_mrtg_graph').empty();
        }
    });

    $('#page_graph').on('change', function() {
        let trafic_mrtg_tree_node = document.getElementById("trafic_mrtg_tree_node");
        $('#trafic_mrtg_graph').empty();
        var paging = $(this).val();
        var id = trafic_mrtg_tree_node.value;
        if (id) {
            $.ajax({
                url: baseurl + `/dinetkan/users_dinetkan/get_graph_mrtg/` + id + `/` + paging,
                type: 'GET',
                dataType: "json",
                success: function(data) {
                    if (data) {
                        let formattedData = data.map((item) => ({
                            id: item.local_graph_id,
                            text: item.Title
                        })).sort((a, b) => a.text.localeCompare(b.text));

                        // Tambahkan opsi default "Silahkan Pilih"
                        formattedData.unshift({
                            id: '',
                            text: 'Silahkan Pilih',
                            disabled: false // Jangan disabled agar bisa dipilih
                        });

                        $('#trafic_mrtg_graph').select2({
                            data: formattedData,
                            allowClear: true,
                            dropdownParent: $("#adminCactiForm .modal-body"),
                        });
                    } else {
                        $('#trafic_mrtg_graph').empty();
                    }
                }
            });
        } else {
            $('#trafic_mrtg_graph').empty();
        }
    });
    
    $('#trafic_mrtg_graph').on('change', function() {
        var selectedOption = this.options[this.selectedIndex].text;
        document.getElementById("selected_value").value = selectedOption;
      });

      
    $('#id_mikrotik').on('change', function() {
      $('#id_vlan').empty();
      var id = $(this).val();
      if (id) {
        Swal.fire({
            title: 'Please wait...',
            text: 'Processing Data ....',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        $.ajax({
          url: baseurl + '/dinetkan/master_mikrotik/get_vlan/' + id,
          type: 'GET',
          dataType: "json",
          success: function(data) {
            swal.close();
            $('#id_vlan').select2({
            data: (() => {
                return data.map((item) => {
                    return {
                    id: item.id,
                    text: item.name
                    }
                })
                .sort((a, b) => a.text.localeCompare(b.text));
            })(),
            allowClear: true,
            placeholder: $(this).data('placeholder'),
            });
          },
          error: function(xhr) {
            // toastr.error(message);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: `${xhr.responseJSON.message}`,
                        showConfirmButton: false,
                        timer: 1500
                    });
          }
        });
      } else {
        swal.close();
      }
    });

    function enabled_vlan(service_id){
      Swal.fire({
          title: 'Please wait...',
          text: 'Processing Data ....',
          allowOutsideClick: false,
          showConfirmButton: false,
          didOpen: () => {
              Swal.showLoading();
          }
      });
      $.ajax({
        url: baseurl + '/dinetkan/master_mikrotik/enabled_vlan/' + service_id,
        type: 'GET',
        dataType: "json",
        success: function(data) {
          swal.close();
          get_vlan_single('{{ $mapping->service_id }}');
        },
        error: function(xhr) {
          swal.close();
        }
      });
    }

    function disabled_vlan(service_id){
      Swal.fire({
          title: 'Please wait...',
          text: 'Processing Data ....',
          allowOutsideClick: false,
          showConfirmButton: false,
          didOpen: () => {
              Swal.showLoading();
          }
      });
      $.ajax({
          url: baseurl + '/dinetkan/master_mikrotik/disabled_vlan/' + service_id,
          type: 'GET',
          dataType: "json",
          success: function(data) {
            swal.close();
            get_vlan_single('{{ $mapping->service_id }}');
          },
        error: function(xhr) {
          swal.close();
        }
        });
    }


    get_vlan_single('{{ $mapping->service_id }}');
    function get_vlan_single(service_id){
      let status_vlan = document.getElementById("status_vlan");
      if(status_vlan){
        document.getElementById("status_vlan").innerHTML  = '<button class="btn btn-light-danger btn-sm">VLAN Disabled</button>';
      }
      $.ajax({
          url: baseurl + '/dinetkan/master_mikrotik/get_vlan_single/' + service_id,
          type: 'GET',
          dataType: "json",
          success: function(data) {
            if(data.disabled == 'true'){
              document.getElementById("status_vlan").innerHTML  = '<button class="btn btn-light-danger btn-sm">VLAN Disabled</button>';
            }
            if(data.disabled == 'false'){
              document.getElementById("status_vlan").innerHTML  = '<button class="btn btn-light-primary btn-sm">VLAN Enabled</button>';
            }
          }
        });
    }
      
    const form3 = $('#adminCactiForm');
    // Form Submission
    form3.on('submit', function(e) {
      Swal.fire({
          title: 'Please wait...',
          text: 'Processing Data ....',
          allowOutsideClick: false,
          showConfirmButton: false,
          didOpen: () => {
              Swal.showLoading();
          }
      });
      e.preventDefault();        

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      $.ajax({
        url: '{{route('dinetkan.users_dinetkan.update_cacti_service')}}',
        method: "POST",
        data: form3.serialize(),
        success: function(response) {
          // modal.modal('hide');
          // toastr.success(response.message);
              Swal.fire({
                  icon: 'success',
                  title: 'Success',
                  text: `${response.message}`,
                  showConfirmButton: false,
                  timer: 1500
              });
              location.reload();
        },
        error: function(xhr) {
          const errors = xhr.responseJSON.errors;
          let message = '';

          for (const key in errors) {
            message += errors[key] + '\n';
          }
          // toastr.error(message);
                  Swal.fire({
                      icon: 'error',
                      title: 'Error',
                      text: `${message}`,
                      showConfirmButton: false,
                      timer: 1500
                  });
        }
      });
    });

    const form4 = $('#adminGraphForm');
    // Form Submission
    form4.on('submit', function(e) {
      Swal.fire({
          title: 'Please wait...',
          text: 'Processing Data ....',
          allowOutsideClick: false,
          showConfirmButton: false,
          didOpen: () => {
              Swal.showLoading();
          }
      });
      e.preventDefault();        

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      $.ajax({
        url: '{{route('dinetkan.invoice_dinetkan.order.update_active_graph')}}',
        method: "POST",
        data: form4.serialize(),
        success: function(response) {
          // modal.modal('hide');
          // toastr.success(response.message);
              Swal.fire({
                  icon: 'success',
                  title: 'Success',
                  text: `${response.message}`,
                  showConfirmButton: false,
                  timer: 1500
              });
              location.reload();
        },
        error: function(xhr) {
          const errors = xhr.responseJSON.errors;
          let message = '';

          for (const key in errors) {
            message += errors[key] + '\n';
          }
          // toastr.error(message);
                  Swal.fire({
                      icon: 'error',
                      title: 'Error',
                      text: `${message}`,
                      showConfirmButton: false,
                      timer: 1500
                  });
        }
      });
    });
      // $(document).ready(function() {
      //   // Buat fungsi supaya bisa dipanggil ulang
      //   function toggleFields() {
      //     var graph_type = $('#graph_type').val();
      //     console.log(graph_type);
      //     if(graph_type == 'mikrotik'){
      //       $('#trafic_mrtg_tree').prop('disabled', true).removeAttr('required');
      //       $('#trafic_mrtg_tree_node').prop('disabled', true).removeAttr('required');
      //       $('#page_graph').prop('disabled', true).removeAttr('required');
      //       $('#trafic_mrtg_graph').prop('disabled', true).removeAttr('required');
      //       $('#graph_name').prop('disabled', true).removeAttr('required');
      //     } else {
      //       $('#trafic_mrtg_tree').prop('disabled', false).attr('required', 'required');
      //       $('#trafic_mrtg_tree_node').prop('disabled', false).attr('required', 'required');
      //       $('#page_graph').prop('disabled', false).attr('required', 'required');
      //       $('#trafic_mrtg_graph').prop('disabled', false).attr('required', 'required');  
      //       $('#graph_name').prop('disabled', false).attr('required', 'required');   
            
      //     }
      //   }

      //   // Jalankan sekali saat load
      //   toggleFields();

      //   // Jalankan lagi saat berubah
      //   $('#graph_type').on('change', toggleFields);
      // });

      const modalDelete = $('#deleteModal');
      const formDelete = $('#deleteForm');
      const methodFieldDelete = $('#deleteMethod');
      const submitBtnDelete = $('#modalSubmitBtn');
      $(document).on('click', '.delete-icon.delete', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Please wait...',
            text: 'Processing Data ....',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
          url: `/dinetkan/users_dinetkan/single_cacti/${id}`,
          method: 'GET',
          success: function(data) {
            formDelete.attr('action', `/dinetkan/users_dinetkan/delete_cacti/${id}`);
            methodFieldDelete.val('DELETE');
            submitBtnDelete.text('Delete');
            swal.close();
            // Populate the form with data
            $('#id').val(data.id);
            $('#modal_graph_id').val(data.graph_id);
            $('#modal_graph_name').val(data.graph_name);
            modalDelete.modal('show');
          },
          error: function(xhr) {
            swal.close();
            alert('Error fetching admin data: ' + xhr.responseJSON.message);
          }
        });
      });

      $('#ifname').select2();
      $('#hostname').select2();
      const formLibre = $('#adminFormLibre');
      // Form Submission
      formLibre.on('submit', function(e) {
        Swal.fire({
            title: 'Please wait...',
            text: 'Processing Data ....',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        e.preventDefault();        

        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        $.ajax({
          url: '{{ route('dinetkan.invoice_dinetkan.order.libre_update_service_detail') }}',
          method: "POST",
          data: formLibre.serialize(),
          success: function(response) {
            // modal.modal('hide');
            // toastr.success(response.message);
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: `${response.message}`,
                    showConfirmButton: false,
                    timer: 1500
                });
                location.reload();
          },
          error: function(xhr) {
            const errors = xhr.responseJSON.errors;
            let message = '';

            for (const key in errors) {
              message += errors[key] + '\n';
            }
            // toastr.error(message);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: `${message}`,
                        showConfirmButton: false,
                        timer: 1500
                    });
          }
        });
      });
      
    $('#hostname').on('change', function() {
      $('#ifName').empty();
      var hostname = $(this).val();
      if (hostname) {
        $.ajax({
          url: baseurl + '/dinetkan/invoice_dinetkan/order/get_ifname/' + hostname,
          type: 'GET',
          dataType: "json",
          success: function(data) {
            $('#ifName').select2({
            data: (() => {
                return data.map((item) => {
                    return {
                    id: item.ifName,
                    text: item.ifName
                    }
                })
                .sort((a, b) => a.text.localeCompare(b.text));
            })(),
            allowClear: true,
            placeholder: $(this).data('placeholder'),
            // dropdownParent: $("#adminModal .modal-content"),
            });
          }
        });
      } else {
        // $('#kabupaten').empty();
      }
    });

    
    $(document).on('click', '.delete-libre-icon.delete', function() {
      const id = $(this).data('id');
      
      Swal.fire({
          title: 'Please wait...',
          text: 'Processing Data ....',
          allowOutsideClick: false,
          showConfirmButton: false,
          didOpen: () => {
              Swal.showLoading();
          }
      });

      $.ajax({
        url: `/dinetkan/invoice_dinetkan/order/delete_ifname/${id}`,
        method: 'GET',
        success: function(data) {
          swal.close();
          location.reload();
        },
        error: function(xhr) {
          swal.close();
          alert('Error fetching admin data: ' + xhr.responseJSON.message);
          location.reload();
        }
      });
    });

    
    $(document).on('click', '.delete-doc-icon.delete', function() {
      const id = $(this).data('id');
      
      Swal.fire({
          title: 'Please wait...',
          text: 'Processing Data ....',
          allowOutsideClick: false,
          showConfirmButton: false,
          didOpen: () => {
              Swal.showLoading();
          }
      });

      $.ajax({
        url: `/dinetkan/invoice_dinetkan/order/delete_service_doc/${id}`,
        method: 'GET',
        success: function(data) {
          swal.close();
          location.reload();
        },
        error: function(xhr) {
          swal.close();
          alert('Error fetching admin data: ' + xhr.responseJSON.message);
          location.reload();
        }
      });
    });
  </script>

<script>
$(document).ready(function(){
    $('#uploadDocForm').on('submit', function(e){
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: "{{ route('dinetkan.users_dinetkan.update_doc_info_dinetkan') }}",
            type: "POST",
            data: formData,
            contentType: false, // penting untuk upload file
            processData: false, // penting untuk upload file
            beforeSend: function(){
                Swal.fire({
                    title: 'Uploading...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response){
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.message
                });
                swal.close();
                location.reload();
                // misal reload table atau halaman
                // $('#materiTable').DataTable().ajax.reload();
            },
            error: function(xhr){
                let res = xhr.responseJSON;
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: res.message ?? 'Something went wrong'
                });
                location.reload();
            }
                // location.reload();
        });
    });
});
</script>
  @endpush
