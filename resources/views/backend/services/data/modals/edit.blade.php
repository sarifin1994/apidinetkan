<!-- edit Modal Show -->
<div class="modal fade" id="edit" role="dialog" aria-labelledby="modal" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal">Edit Service</h5>
        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-lg-6">
            <input type="hidden" id="user_id">
            <input type="hidden" id="kode_area_id">
            <input type="hidden" id="kode_odp_id">
            <div class="form-group mb-3">
              <label for="username_edit" class="mb-1">Username <small class="text-danger">*</small></label>
              <input type="text" class="form-control" id="username_edit" name="username" placeholder="Username"
                autocomplete="off" required>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="form-group mb-3">
              <label for="password_edit" class="mb-1">Password <small class="text-danger">*</small></label>
              <input type="text" class="form-control" id="password_edit" name="password" placeholder="Password"
                autocomplete="off" required>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-6">
            <label for="profile_edit" class="mb-1">Assign Profile <small class="text-danger">*</small></label>
            <div class="form-group mb-3">
              <select class="form-select" id="profile_edit" name="profile" autocomplete="off"
                data-placeholder="Pilih Profile">
                @forelse ($profiles as $profile)
                  <option value="{{ $profile->id }}">{{ $profile->name }}</option>
                @empty
                @endforelse
              </select>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="form-group mb-3">
              <label for="member_id" class="mb-1">Member</label>
              <select class="form-select" id="member_id_edit" name="member_id" autocomplete="off"
                data-placeholder="Pilih Member">
                <option value="">Pilih Member</option>
              </select>
              <small class="text-muted">Gunakan form billing apabila ingin melakukan pemindahan layanan ke member lain
                dengan tagihan baru</small>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-6">
            <div class="form-group mb-3">
              <label for="ip_edit" class="mb-1">IP Address</label>
              <input type="text" class="form-control" id="ip_edit" name="ip" placeholder="IP Address"
                autocomplete="off" required>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="form-group mb-3">
              <input type="hidden" id="nas_secret">
              <label for="nas_edit" class="mb-1">Nas</label>
              <select class="form-select" id="nas_edit" name="nas" autocomplete="off" data-placeholder="Pilih Nas">
                <option value="" selected>all</option>
                @forelse ($nas as $nas)
                  <option value="{{ $nas->ip_router }}">{{ $nas->name }}</option>
                @empty
                @endforelse
              </select>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-6">
            <label for="kode_area_edit" class="mb-1">Kode Area</label>
            <div class="form-group mb-3" style="display:grid">
              <select class="form-control select2" id="kode_area_edit" name="kode_area" autocomplete="off"
                data-placeholder="Pilih Kode Area">
                @forelse ($areas as $area)
                  <option value="{{ $area->id }}">{{ $area->kode_area }}</option>
                @empty
                @endforelse
              </select>
            </div>
          </div>
          <div class="col-lg-6">
            <label for="kode_odp_edit" class="mb-1">Kode ODP</label>
            <div class="form-group mb-3" style="display:grid">
              <select class="form-control select2" id="kode_odp_edit" name="kode_odp" autocomplete="off"
                data-placeholder="Pilih Kode ODP">
              </select>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-6">
            <div class="form-group mb-3">
              <label for="lock_mac_edit" class="mb-1">Lock Mac</label>
              <select class="form-select" id="lock_mac_edit" name="lock_mac" autocomplete="off">
                <option value="0">Disabled</option>
                <option value="1">Enabled</option>
              </select>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="form-group mac mb-3" id="show_mac_edit">
              <input type="text" class="form-control" id="mac_edit" name="mac" value="{{ old('rx') }}"
                placeholder="Rate Limit Rx" autocomplete="off" required>
              <label for="mac_edit">MAC Address</label>
            </div>
          </div>
        </div>

        <span class="text-sm">Untuk mengubah data member, silakan edit di tab <a
            href="#data-member">Members</a></span>

        <div class="table-responsive collapse mt-3" id="collapseExample">
          <table id="sessionTable" class="table-hover display nowrap table" width="100%">
            <thead>
              <tr>
                <th>Start</th>
                <th>Stop</th>
                <th>IP</th>
                <th>MAC</th>
                <th>Rx</th>
                <th>Tx</th>
                <th>Uptime</th>
              </tr>
            </thead>
            <tbody>

            </tbody>
          </table>
        </div>

      </div>
      <div class="modal-footer">
        <button class="btn btn-warning" type="button" data-bs-dismiss="modal">
          Close
        </button>
        @if (auth()->user()->role === 'Admin' || auth()->user()->role === 'Helpdesk')
          <button class="btn btn-danger" type="submit" id="delete">
            Delete
          </button>
        @endif
        <div class="btn-group dropup">
          <button id="show_session" type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="collapse"
            data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
            Show Session
          </button>
        </div>
        @if (auth()->user()->role === 'Admin' || auth()->user()->role === 'Helpdesk')
          <div class="btn-group dropup">
            <a class="btn btn-info dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
              data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fa fa-edit"></i>&nbsp Status
            </a>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
              <li><button class="dropdown-item" id="enabled">Enabled</button></li>
              <li><button class="dropdown-item" id="disabled">Disabled</button></li>
              <li><button class="dropdown-item" id="suspend">Suspend</button></li>
            </ul>
          </div>
          <button class="btn btn-success" id="update" type="submit">
            Simpan
          </button>
        @endif
      </div>
    </div>
  </div>
</div>

@push('script-modal')
  <script>
    $.ajax({
      url: areaUrl + '/list',
      type: 'GET',
      success: function(data) {
        $('#kode_area_edit').select2({
          data: (() => {
            return data.map((item) => {
                return {
                  id: item.id,
                  text: item.kode_area
                }
              })
              .sort((a, b) => a.text.localeCompare(b.text));
          })(),
          allowClear: true,
          placeholder: $(this).data('placeholder'),
          dropdownParent: $("#edit .modal-content"),
        });
      }
    });

    $.ajax({
      url: memberUrl + '/list',
      type: 'GET',
      success: function(data) {
        $('#member_id_edit').select2({
          data: (() => {
            return data.map((item) => {
                return {
                  id: item.id,
                  text: item.full_name
                }
              })
              .sort((a, b) => a.text.localeCompare(b.text));
          })(),
          allowClear: true,
          placeholder: $(this).data('placeholder'),
          dropdownParent: $("#edit .modal-content"),
        });
      }
    });

    $('#show_session').click(function() {
      let username = $('#username_edit').val();
      $.ajax({
        url: baseUrl + `/${username}/session`,
        type: 'GET',
        data: {
          username: username
        },
        success: function(response) {
          console.log(response);
          $('#sessionTable').DataTable({
            data: response,
            // scrollX: true,
            pageLength: 2,
            lengthMenu: [
              [2, 5, 10, 20],
              [2, 5, 10, 20]
            ],
            destroy: true,
            order: [
              [0, 'desc']
            ],
            columns: [{
                data: 'start',
              },
              {
                data: 'stop',
                render: function(data) {
                  if (data === null) {
                    return ''
                  } else {
                    return data;
                  }

                },
              },
              {
                data: 'ip'
              },
              {
                data: 'mac'
              },
              {
                data: 'input',
                render: function bytesToSize(data) {
                  var sizes = ['Bytes', 'KB', 'MB', 'GB',
                    'TB'
                  ];
                  if (data == 0) return 'n/a';
                  var i = parseInt(Math.floor(Math.log(
                    data) / Math.log(1024)));
                  if (i == 0) return data + ' ' + sizes[i];
                  return (data / Math.pow(1024, i)).toFixed(
                    1) + ' ' + sizes[i];
                }
              },
              {
                data: 'output',
                render: function bytesToSize(data) {
                  var sizes = ['Bytes', 'KB', 'MB', 'GB',
                    'TB'
                  ];
                  if (data == 0) return 'n/a';
                  var i = parseInt(Math.floor(Math.log(
                    data) / Math.log(1024)));
                  if (i == 0) return data + ' ' + sizes[i];
                  return (data / Math.pow(1024, i)).toFixed(
                    1) + ' ' + sizes[i];
                }
              },
              {
                data: 'uptime',
                render: function convertSecondsToReadableString(
                  seconds) {
                  seconds = seconds || 0;
                  seconds = Number(seconds);
                  seconds = Math.abs(seconds);

                  const d = Math.floor(seconds / (3600 * 24));
                  const h = Math.floor(seconds % (3600 * 24) /
                    3600);
                  const m = Math.floor(seconds % 3600 / 60);
                  const s = Math.floor(seconds % 60);
                  const parts = [];

                  if (d > 0) {
                    parts.push(d + 'd');
                  }

                  if (h > 0) {
                    parts.push(h + 'h');
                  }

                  if (m > 0) {
                    parts.push(m + 'm');
                  }

                  // if (s > 0) {
                  //     parts.push(s + ' second' + (s > 1 ? 's' :
                  //         ''));
                  // }

                  return parts.join(' ');
                }

              }
            ]
          });

        }
      });
      $(this).toggleClass("active");
      if ($(this).hasClass("active")) {
        $(this).text("Hide Session");
      } else {
        $(this).text("Show Session");
      }
    });

    $('#delete').on('click', function() {
      let id = $('#user_id').val();
      Swal.fire({
        title: "Apakah anda yakin?",
        icon: 'warning',
        text: "Data yang sudah dihapus tidak dapat dikembalikan",
        showCancelButton: !0,
        reverseButtons: !0,
        confirmButtonText: "Yes, delete!",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#d33",
        // cancelButtonColor: "#d33",
      }).then(function(result) {
        if (result.isConfirmed) {
          $.ajax({
            url: baseUrl + `/${id}`,
            type: "POST",
            cache: false,
            data: {
              _method: "DELETE"
            },
            dataType: "json",

            // tampilkan pesan Success
            success: function(data) {
              Swal.fire({
                icon: 'success',
                title: 'Success',
                text: `${data.message}`,
                showConfirmButton: false,
                timer: 1500
              });
              setTimeout(
                function() {
                  table.ajax.reload()
                  $('#edit').modal('hide')
                });
            },

            error: function(err) {
              $("#message").html(
                "Some Error Occurred!"
              )
            }
          });
        }
      });
    });
  </script>
@endpush
