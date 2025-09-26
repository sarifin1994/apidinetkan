@extends('backend.layouts.app_new')

@section('title', 'Account Info')

@section('main')
<div class="container-fluid">
  <div class="row">
    <!-- Account Information Card -->
    <div class="col-xl-12 mb-4">
      <div class="card rounded border-0 shadow-sm">
        <div class="card-header d-flex align-items-center text-white">
          <h5 class="d-flex align-items-center mb-0">
            <i class="ti ti-user-circle me-2"></i> Informasi Akun
          </h5>
        </div>
        <div class="card-body">

          <div class="mb-3">
  <a href="{{ route('admin.account.info.get_info_dinetkan') }}" class="btn btn-primary d-inline-flex align-items-center gap-1">
    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
      <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
      <path d="M6 4h-1a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h14a2 2 0 0 0 2 -2v-10l-4 -4h-2" />
      <circle cx="12" cy="14" r="2" />
      <path d="M14 4v4h-4v-4" />
    </svg>
    Update Data
  </a>
</div>

          <div class="row">
            <div class="mb-3 col-6">
              <label>User ID</label>
              <div class="input-group">
                <span class="input-group-text bg-light"><i class="ti ti-id"></i></span>
                <input type="text" class="form-control" value="{{$userdinetkan->dinetkan_user_id}}" readonly>
              </div>

              <label>Username</label>
              <div class="input-group">
                <span class="input-group-text bg-light"><i class="ti ti-user"></i></span>
                <input type="text" class="form-control" value="{{auth()->user()->username}}" readonly>
              </div>

              <label>First Name</label>
              <div class="input-group">
                <span class="input-group-text bg-light"><i class="ti ti-user-circle"></i></span>
                <input type="text" class="form-control" value="{{$userdinetkan->first_name}}" readonly>
              </div>

              <label>Last Name</label>
              <div class="input-group">
                <span class="input-group-text bg-light"><i class="ti ti-user-circle"></i></span>
                <input type="text" class="form-control" value="{{$userdinetkan->last_name}}" readonly>
              </div>

              <label>Whatsapp</label>
              <div class="input-group">
                <span class="input-group-text bg-light"><i class="ti ti-brand-whatsapp"></i></span>
                <input type="text" class="form-control" value="{{auth()->user()->whatsapp}}" readonly>
              </div>
            </div>

            <div class="mb-3 col-6">
              <label>Province</label>
              <div class="input-group">
                <span class="input-group-text bg-light"><i class="ti ti-map-pin"></i></span>
                <input type="text" class="form-control" value="{{$userdinetkan->province ? $userdinetkan->province->name : ''}}" readonly>
              </div>

              <label>City</label>
              <div class="input-group">
                <span class="input-group-text bg-light"><i class="ti ti-building"></i></span>
                <input type="text" class="form-control" value="{{$userdinetkan->regency ? $userdinetkan->regency->name : ''}}" readonly>
              </div>

              <label>District</label>
              <div class="input-group">
                <span class="input-group-text bg-light"><i class="ti ti-map-2"></i></span>
                <input type="text" class="form-control" value="{{$userdinetkan->district ? $userdinetkan->district->name : ''}}" readonly>
              </div>

              <label>Village</label>
              <div class="input-group">
                <span class="input-group-text bg-light"><i class="ti ti-home"></i></span>
                <input type="text" class="form-control" value="{{$userdinetkan->village ? $userdinetkan->village->name : ''}}" readonly>
              </div>

              <label>Address</label>
              <div class="input-group">
                <span class="input-group-text bg-light"><i class="ti ti-address-book"></i></span>
                <textarea class="form-control" rows="5" readonly>{{$userdinetkan->address}}</textarea>
              </div>
            </div>

            <div class="col-md-12 mb-3">
              <label for="address" class="form-label">Lokasi</label>
              <div class="input-group">
                <div id="map"></div>
              </div>
            </div>

            <div class="mb-3 col-6">
              <label for="latitude" class="form-label">Latitude</label>
              <div class="input-group">
                <span class="input-group-text bg-light"><i class="ti ti-arrows-diagonal"></i></span>
                <input type="text" class="form-control" id="latitude" name="latitude" :class="errors.latitude && 'is-invalid'" value="{{$userdinetkan->latitude}}" disabled="disabled">
                <template x-if="errors.latitude">
                  <span class="invalid-feedback">
                    <strong x-text="errors.latitude"></strong>
                  </span>
                </template>
              </div>
            </div>

            <div class="mb-3 col-6">
              <label for="longitude" class="form-label">Longitude</label>
              <div class="input-group">
                <span class="input-group-text bg-light"><i class="ti ti-arrows-diagonal-2"></i></span>
                <input type="text" class="form-control" id="longitude" name="longitude" :class="errors.longitude && 'is-invalid'" value="{{$userdinetkan->longitude}}" disabled="disabled">
                <template x-if="errors.longitude">
                  <span class="invalid-longitude">
                    <strong x-text="errors.longitude"></strong>
                  </span>
                </template>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Dokumen Card -->
    <div class="col-xl-12 mb-4">
      <div class="card rounded border-0 shadow-sm">
        <div class="card-header d-flex align-items-center text-white">
          <h5 class="d-flex align-items-center mb-0">
            <i class="ti ti-file-text me-2"></i> Informasi Dokumen
          </h5>
        </div>
        <div class="card-body">
          <div class="mb-3 col-12">
            <div class="table-responsive custom-scrollbar">
              <table id="myTable" class="table-hover display nowrap table" width="100%">
                <thead>
                  <th>Dokumen</th>
                  <th>Lihat File</th>
                </thead>
                @if($listDoc)
                  @foreach($listDoc as $row)
                    <tr>
                      <td>{{$row->docType->name}}</td>
                      <td>
                        <a target="_blank" class="btn btn-xs btn-light-primary" href="{{route('admin.account.info.show_file', $row->id)}}">
                          Lihat file
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
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/@tabler/icons@latest/iconfont/tabler-icons.min.js"></script>
<script>
  set_maps('{{$userdinetkan->latitude}}','{{$userdinetkan->longitude}}');
  function set_maps(defaultLat, defaultLng) {
    var map = null;
    if (map !== null) {
      map.remove();
    }
    var latInput = document.getElementById("latitude").value.trim();
    var lngInput = document.getElementById("longitude").value.trim();
    var lat = latInput ? parseFloat(latInput) : defaultLat;
    var lng = lngInput ? parseFloat(lngInput) : defaultLng;
    map = L.map('map').setView([lat, lng], 10);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);
    var marker = L.marker([lat, lng]).addTo(map);
    marker.on('dragend', function (event) {
      var position = marker.getLatLng();
      document.getElementById('latitude').value = position.lat;
      document.getElementById('longitude').value = position.lng;
    });
  }
</script>
@endpush
