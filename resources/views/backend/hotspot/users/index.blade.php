@extends('backend.layouts.app')

@section('title', 'Hotspot User Management')

@section('css')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select2.css') }}">
@endsection

@section('main')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6 ps-0">
          <h3>Hotspot User Management</h3>
        </div>
        <div class="col-sm-6 pe-0">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">
                <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item">Hotspot</li>
            <li class="breadcrumb-item active">User Management</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <div class="container-fluid user-management-page">
    <div class="row">

      @role(['admin', 'reseller'])
        <div class="col-xxl-4 box-col-4">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col-sm-12">
                  <div class="bg-light-primary b-r-15">
                    <div class="upcoming-box">
                      <div class="upcoming-icon bg-primary">
                        <svg class="stroke-icon">
                          <use href="{{ asset('assets/svg/icon-sprite.svg#user-visitor') }}"></use>
                        </svg>
                      </div>
                      <p>User</p>

                      <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                          aria-expanded="false">
                          <i class="fa fa-plus"></i>
                          {{ __('Create') }}
                        </button>
                        <ul class="dropdown-menu dropdown-block">
                          @role('admin')
                            <li><a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal"
                                data-bs-target="#create">User</a></li>
                          @endrole
                          <li><a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal"
                              data-bs-target="#generate">Voucher</a></li>
                        </ul>
                        @role('admin')
                          <button class="btn btn-warning dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa fa-edit"></i>
                            {{ __('Action') }}
                            <span class="row-count badge bg-dark text-light"></span>
                          </button>
                          <ul class="dropdown-menu dropdown-block">
                            <li><a class="dropdown-item" href="javascript:void(0)" id="enable">Enable</a></li>
                            <li><a class="dropdown-item" href="javascript:void(0)" id="disable">Disable</a></li>
                            <li>
                              <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="javascript:void(0)" id="reactivate">Re-Activate</a></li>
                          </ul>
                        @endrole
                        <button class="btn btn-info" id="btn-print" data-bs-toggle="modal" data-bs-target="#print" disabled>
                          <i class="fa fa-print"></i>
                          {{ __('Print') }}
                          <span class="row-count badge bg-dark text-light"></span>
                        </button>
                        @role('admin')
                          <button class="btn btn-danger" id="delete">
                            <i class="fa fa-trash"></i>
                            {{ __('Delete') }}
                            <span class="row-count badge bg-dark text-light"></span>
                          </button>
                        @endrole
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      @endrole

      <div class="col-xxl-2 col-lg-4 box-col-4">
        <div class="card user-management">
          <div class="card-body bg-primary">
            <div class="blog-card p-0">
              <div class="blog-card-content">
                <div class="blog-tags">
                  <div class="tags-icon">
                    <svg class="stroke-icon">
                      <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-task') }}"></use>
                    </svg>
                  </div>
                  <div class="tag-details">
                    <div class="d-flex align-items-center mb-3 gap-3">
                      <h3 class="total-num counter mb-0">
                        {{ $totalUsers }}</h2>
                        <p>Total Users</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xxl-6 col-lg-8 box-col-8e">
        <div class="card">
          <div class="card-header">
            <h4>Total Users by Status</h4>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-12">
                <div class="total-num counter">
                  <div class="d-flex by-role custom-scrollbar">
                    <div>
                      <div class="total-user bg-light-primary">
                        <h5>New</h5>
                        <span class="total-num counter">{{ $totalNew }}</span>
                      </div>
                    </div>
                    <div>
                      <div class="total-user bg-light-primary">
                        <h5>Active</h5>
                        <span class="total-num counter">{{ $totalActive }}</span>
                      </div>
                    </div>
                    <div>
                      <div class="total-user bg-light-primary">
                        <h5>Expired</h5>
                        <span class="total-num counter">{{ $totalExpired }}</span>
                      </div>
                    </div>
                    <div>
                      <div class="total-user bg-light-primary">
                        <h5>Disabled</h5>
                        <span class="total-num counter">{{ $totalDisabled }}</span>
                      </div>
                    </div>
                  </div>
                </div>
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
            <div class="row mb-3">
              <div class="col-md-3">
                <select class="form-select" id="filterCreated">
                  <option value="">Filter Created</option>
                  @forelse ($remarks as $remark)
                    <option value="{{ $remark->created_at }}">
                      {{ Carbon\Carbon::parse($remark->created_at)->format('d/m/Y H:i:s') }}
                    </option>
                  @empty
                  @endforelse
                </select>
              </div>
              <div class="col-md-3">
                <select class="form-select" id="filterNas">
                  <option value="">Filter NAS</option>
                  @forelse ($nas as $n)
                    <option value="{{ $n->ip_router }}">{{ $n->name }}</option>
                  @empty
                  @endforelse
                </select>
              </div>
              <div class="col-md-3">
                <select class="form-select" id="filterStatus">
                  <option value="">Filter Status</option>
                  <option value="1">New</option>
                  <option value="2">Active</option>
                  <option value="3">Expired</option>
                  <option value="0">Disabled</option>
                </select>
              </div>
              <div class="col-md-3">
                <select class="form-select" id="filterReseller">
                  <option value="">Filter Reseller</option>
                  @forelse ($resellers as $reseller)
                    <option value="{{ $reseller->id }}">{{ $reseller->name }}</option>
                  @empty
                  @endforelse
                </select>
              </div>
            </div>

            <div class="table-responsive custom-scrollbar">
              <table id="myTable" class="table-hover display nowrap table" width="100%">
                <thead>
                  <tr>
                    <th><input type="checkbox" class="form-check-input" id="head-cb"></th>
                    <th style="text-align:left!important">NO</th>
                    <th style="text-align:left!important">USERNAME</th>
                    <th style="text-align:left!important">PASSWORD</th>
                    <th>PROFILE</th>
                    <th>NAS</th>
                    <th>SERVER</th>
                    <th>CREATED</th>
                    <th>RESELLER</th>
                    <th>OWNER</th>
                    <th>STATUS</th>
                  </tr>
                </thead>
                <tbody>

                </tbody>
              </table>
            </div>

            @role('admin')
              @include('hotspot.users.modal.create')
            @endrole
            @include('hotspot.users.modal.generate')
            @include('hotspot.users.modal.edit')
            @include('hotspot.users.modal.print')
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script src="{{ asset('assets/radiusqu/dist/js/moment.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/datatable.custom.js') }}"></script>

  <script type="text/javascript">
    const resellerUrl = baseUrl.clone().replace('user', 'reseller');

    let table = $('#myTable').DataTable({
      processing: true,
      serverSide: true,
      autoWidth: true,
      // scrollX: true,
      ajax: {
        url: baseUrl + '/datatable',
        type: "POST",
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
      },
      lengthMenu: [10, 25, 50, 100, 200, 500, 1000, 2000],
      order: [
        [7, 'desc']
      ],
      columns: [{
          data: 'checkbox',
          'sortable': false,
          name: 'checkbox',
          // render: function(data, type, row, meta) {
          //     return '<input type="checkbox" class="form-check-input row-cb" value="' + row.id +
          //         '">';
          // },
        },
        {
          data: null,
          'sortable': false,
          render: function(data, type, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
          }
        },
        {
          data: 'username',
          name: 'username'
        },
        {
          data: 'value',
          name: 'value'
        },
        {
          data: 'profile_name',
          name: 'profile',
          render: function(data) {
            if (data === null) {
              return '<i class="text-danger">Unknown</i>'
            } else {
              return data
            }
          },
        },
        {
          data: 'nas_name',
          name: 'nas',
          render: function(data, type, row) {
            if (row.nas === null) {
              return 'all'
            } else if (row.nas_name === null) {
              return '<i class="text-danger">Unknown</i>'
            } else {
              return data
            }
          },
        },
        {
          data: 'server',
          name: 'server',
          render: function(data, type, row) {
            if (data === null) {
              return 'all'
            } else {
              return data
            }
          },
        },
        {
          data: 'created_at',
          name: 'created_at',
          render: function(data, type, row, meta) {
            return moment(data).local().format('DD/MM/YYYY HH:mm:ss');
          },
        },
        {
          data: 'reseller_name',
          name: 'reseller_id',
          render: function(data, type, row) {
            if (row.reseller_id === null) {
              return '-'
            } else if (row.id === null) {
              return '<i class="text-danger">Unknown</i>'
            } else {
              return data
            }
          },
        },
        {
          data: 'admin',
          name: 'admin',
        },
        {
          data: 'status',
          name: 'status',
          render: function(data) {
            if (data === 1) {
              return '<span class="text-primary">new</span>'
            } else if (data === 0) {
              return '<span class="text-warning">off</span>'
            } else if (data === 2) {
              return '<span class="text-success">active</span>'
            } else if (data === 3) {
              return '<span class="text-danger">expired</span>'
            } else {
              return data;
            }

          },
        },
      ],

    });

    var id_selected = [];
    table.on('preXhr.dt', function(e, settings, data) {
      data.remark = $('#filter_remark').val();
      data.status = $('#filter_status').val();
      data.nas = $('#filter_nas').val();
      data.reseller = $('#filter_reseller').val();
      data.idsel = id_selected;
    });


    $('#generate_voucher').click(function(e) {
      e.preventDefault();
      $('#generate_voucher').attr("disabled", true);
      $("#generate_voucher").html('Create&nbsp<i class="fa fa-refresh fa-spin" id="spinner"></i>');
      var error_ele = document.getElementsByClassName('alert text-sm');
      if (error_ele.length > 0) {
        for (var i = error_ele.length - 1; i >= 0; i--) {
          error_ele[i].remove();
        }
      }

      // collect data by id
      var data = {
        'jml_voucher': $('#jml_voucher').val(),
        'model': $('#model').val(),
        'character': $('#character').val(),
        'length': $('#length').val(),
        'prefix': $('#prefix').val(),
        'profile': $('#profile option:selected').text(),
        'profile_id': $('#profile option:selected').val(),
        'nas': $('#nas option:selected').val(),
        'hotspot_server': $('#hotspot_server').val(),
        'payment_status': $('#payment_status option:selected').val(),
        'price': $('#price').val(),
        'total': $('#total').val(),
        'reseller_id': $('#reseller option:selected').val(),
        'wa_reseller': $('#wa_reseller').val(),
      }

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      // ajax proses
      $.ajax({
        url: baseUrl + `/generate`,
        type: "POST",
        cache: false,
        data: data,
        dataType: "json",

        // tampilkan pesan Success
        success: function(data) {
          is_generate = true;
          if (data.error) {
            Swal.fire({
              icon: 'error',
              title: 'Gagal',
              text: `${data.error}`,
              showConfirmButton: false,
              timer: 5000
            });
          }
          if (data.success) {
            id_selected = (data.id).map(item => item.id);
            Swal.fire({
              icon: 'success',
              title: 'Success',
              text: `${data.message}`,
              showConfirmButton: false,
              timer: 1500
            });
            $('#generate_voucher').attr("disabled", false);
            $("#spinner").remove();
            setTimeout(function() {
              table.ajax.reload();
              $('#btn-print').prop('disabled', false);
              $('#delete').prop('disabled', false);
              $('input, textarea', ).val('');
              $('#profile').val('').trigger('change');
              $('#nas').val('').trigger('change');
              $('#reseller').val('').trigger('change');
              $('#totaluser').html(data.totaluser)
              $('#totalactive').html(data.totalactive)
              $('#totalsuspend').html(data.totalsuspend)
              $('#totaldisabled').html(data.totaldisabled)
              $('#generate').modal('hide');

            });
          } else {
            $.each(data.error, function(key, value) {
              var el = $(document).find('[name="' + key + '"]');
              el.after($('<span class= "alert text-sm text-danger">' + value[0] +
                '</div>'));
            });
            Swal.fire({
              icon: 'error',
              title: 'Failed',
              text: 'Failed to create user, please check your field',
              showConfirmButton: false,
              timer: 1500
            });

          }
        },

        error: function(err) {
          $("#message").html("Some Error Occurred!")
        }

      });
    });

    $("#generate").on("hidden.bs.modal", function() {
      $('#generate_voucher').attr("disabled", false);
      $("#spinner").remove();
    });

    $('#filter_remark').change(function() {
      table.ajax.reload(function(json) {
        $(".row-count").html(json.data.length);
      });
      $('#head-cb').prop('checked', true);
      $('#btn-print').prop('disabled', false);
      $('#delete').prop('disabled', false);
      $(".row-count").html($('.row-cb:checked').length);
      return false;
    });

    $('#filter_status').change(function() {
      table.ajax.reload(function(json) {
        $(".row-count").html(json.data.length);
      });
      $('#head-cb').prop('checked', true);
      $('#btn-print').prop('disabled', false);
      $('#delete').prop('disabled', false);
      return false;
    });

    $('#filter_nas').change(function() {
      table.ajax.reload()
      return false;
    });
    $('#filter_reseller').change(function() {
      table.ajax.reload()
      return false;
    });

    $('#myTable').on('length.dt', function(e, settings, len) {
      if ($('#filter_remark').val() || $('#filter_status').val()) {
        if (settings._iDisplayLength >= settings._iRecordsTotal) {
          $(".row-count").html(settings._iRecordsTotal);
        } else {
          $(".row-count").html(len);
        }
      } else {
        $('#head-cb').prop('checked', false);
        $(".row-count").html('');
        $('#btn-print').prop('disabled', true);
        $('#delete').prop('disabled', true);
      }
    });


    $('#myTable tbody').on('click', 'tr td:not(:first-child)', function() {

      let user_id = table.row(this).data().id;
      let username = table.row(this).data().username;

      $.ajax({
        url: baseUrl + `/${user_id}`,
        type: "GET",
        success: function(data) {
          $('#user_id').val(data.data.id),
            $('#username_edit').val(data.data.username),
            $('#password_edit').val(data.data.value),
            $('#profile_edit').val(data.data.rprofile.id);
          $('#nas_edit').val(data.data.nas);
          $('#hotspot_server_edit').val(data.data.server);
          if (data.data.status === 1) {
            $('#fill_status').html('<span class="text-primary">new</span>');
          } else if (data.data.status === 0) {
            $('#fill_status').html('<span class="text-warning">off</span>');
          } else if (data.data.status === 2) {
            $('#fill_status').html('<span class="text-success">active</span>');
          } else if (data.data.status === 3) {
            $('#fill_status').html('<span class="text-danger">expired</span>');
          }
          if (data.data.start_time !== null) {
            $('#fill_login').html(data.data.start_time);
          }
          if (data.data.end_time !== null) {
            $('#fill_expired').html(data.data.end_time);
          }

          if (jQuery.type(data.data.session.output) === 'undefined') {
            $('#fill_upload').html('-');
          } else {
            var upload = data.data.session.output
            var sizes = ['Bytes', 'KB', 'MB', 'GB',
              'TB'
            ];
            if (upload == 0) return 'n/a';
            var i = parseInt(Math.floor(Math.log(
              upload) / Math.log(1024)));
            // if (i == 0) return upload + ' ' + sizes[i];
            var upload_format = (upload / Math.pow(1024, i)).toFixed(
              1) + ' ' + sizes[i];
            $('#fill_upload').html(upload_format);
          }

          if (jQuery.type(data.data.session.input) === 'undefined') {
            $('#fill_download').html('-');
          } else {
            var download = data.data.session.input
            var sizes = ['Bytes', 'KB', 'MB', 'GB',
              'TB'
            ];
            // if (download == 0) return 'n/a';
            var i = parseInt(Math.floor(Math.log(
              download) / Math.log(1024)));
            // if (i == 0) return download + ' ' + sizes[i];
            var download_format = (download / Math.pow(1024, i)).toFixed(
              1) + ' ' + sizes[i];
            $('#fill_download').html(download_format);
          }

          if (jQuery.type(data.data.session.uptime) === 'undefined') {
            $('#fill_uptime').html('-');
          } else {
            var seconds = data.data.session.uptime
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

            var uptime = parts.join(' ');
            $('#fill_uptime').html(uptime)
          }




        }
      });

      $('#edit').modal('show');

    });

    $('#head-cb').on('click', function(e) {
      if ($(this).is(':checked', true)) {
        $(".row-cb").prop('checked', true);
        $(".row-count").html($('.row-cb:checked').length);
        $('#btn-print').prop('disabled', false);
        $('#delete').prop('disabled', false);
      } else {
        $(".row-cb").prop('checked', false);
        $(".row-count").html('');
        $('#btn-print').prop('disabled', true);
        $('#delete').prop('disabled', true);
      }
    });

    $('#myTable').on('click', '.row-cb', function() {
      if ($('.row-cb:checked').length == $('.row-cb').length) {
        $('#head-cb').prop('checked', true);
        $(".row-count").html($('.row-cb:checked').length);
        $('#btn-print').prop('disabled', false);
        $('#delete').prop('disabled', false);
      } else if ($('.row-cb:checked').length == 0) {
        $('#head-cb').prop('checked', false);
        $(".row-count").html('');
        $('#btn-print').prop('disabled', true);
        $('#delete').prop('disabled', true);
      } else {
        $('#head-cb').prop('checked', false);
        $(".row-count").html($('.row-cb:checked').length);
        $('#btn-print').prop('disabled', false);
        $('#delete').prop('disabled', false);
      }
    });

    $("#profile,#jml_voucher").on("keyup change", function() {
      var profile = $('#profile').val();
      if (profile) {
        $.ajax({
          url: baseUrl + `/getProfile/`,
          type: "GET",
          cache: false,
          data: {
            profile: profile,
            '_token': '{{ csrf_token() }}'
          },
          success: function(data) {
            if (data.data.reseller_price === '0') {
              var amount = data.data.price;
            } else {
              var amount = data.data.reseller_price;
            }
            var jml = $('#jml_voucher').val();
            var total = (jml * amount).toString();
            rp_amount = formatRupiah(amount, 2, ',', '.');
            $('#price').val(rp_amount);
            rp_total_amount = formatRupiah(total, 2, ',', '.');
            $('#total').val(rp_total_amount);
          }
        });
      }
    });

    $("#profile_c").on("keyup change", function() {
      var profile = $('#profile_c').val();
      if (profile) {
        $.ajax({
          url: baseUrl + `/getProfile`,
          type: "GET",
          cache: false,
          data: {
            profile: profile,
            '_token': '{{ csrf_token() }}'
          },
          success: function(data) {
            if (data.data.reseller_price === '0') {
              var amount = data.data.price;
            } else {
              var amount = data.data.reseller_price;
            }
            rp_total_amount = formatRupiah(amount, 2, ',', '.');
            $('#total_c').val(rp_total_amount);
          }
        });
      }
    });

    $("#reseller").on("change", function() {
      var reseller = $(this).val();
      $.ajax({
        url: resellerUrl + `/${reseller}`,
        type: "GET",
        cache: false,
        data: {
          reseller: reseller,
          '_token': '{{ csrf_token() }}'
        },
        success: function(data) {

          $('#wa_reseller').val(data.data.wa);
        }
      });
    });
    $("#reseller_c").on("change", function() {
      var reseller = $(this).val();
      $.ajax({
        url: resellerUrl + `/${reseller}`,
        type: "GET",
        cache: false,
        data: {
          reseller: reseller,
          '_token': '{{ csrf_token() }}'
        },
        success: function(data) {

          $('#wa_reseller_c').val(data.data.wa);
        }
      });
    });

    $('#store').click(function(e) {
      e.preventDefault();
      var error_ele = document.getElementsByClassName('form-text text-danger');
      if (error_ele.length > 0) {
        for (var i = error_ele.length - 1; i >= 0; i--) {
          error_ele[i].remove();
        }
      }

      // collect data by id
      var data = {
        'username': $('#username').val(),
        'password': $('#password').val(),
        'profile': $('#profile_c option:selected').text(),
        'nas': $('#nas_c option:selected').val(),
        'hotspot_server': $('#hotspot_server_c').val(),
        'payment_status': $('#payment_status_c option:selected').val(),
        'total': $('#total_c').val(),
        'reseller_id': $('#reseller_c option:selected').val(),
        'wa_reseller': $('#wa_reseller_c').val(),
      }


      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      // ajax proses
      $.ajax({
        url: baseUrl,
        type: "POST",
        cache: false,
        data: data,
        dataType: "json",

        // tampilkan pesan Success

        success: function(data) {
          if (data.error) {
            Swal.fire({
              icon: 'error',
              title: 'Failed',
              text: `${data.error}`,
              showConfirmButton: false,
              timer: 5000
            });
          }
          if (data.success) {
            Swal.fire({
              icon: 'success',
              title: 'Success',
              text: `${data.message}`,
              showConfirmButton: false,
              timer: 1500
            });
            setTimeout(function() {
              table.ajax.reload()
              $('input, textarea', ).val('');
              $('#profile').val('').trigger('change');
              $('#nas').val('').trigger('change');
              $('#reseller_c').val('').trigger('change');
              $('#totaluser').html(data.totaluser)
              $('#totalactive').html(data.totalactive)
              $('#totalsuspend').html(data.totalsuspend)
              $('#totaldisabled').html(data.totaldisabled)
              $('#create').modal('hide')
            });
          } else {
            $.each(data.error, function(key, value) {
              var el = $(document).find('[name="' + key + '"]');
              el.after($('<div class= "form-text text-danger">' + value[0] +
                '</div>'));
            });
            Swal.fire({
              icon: 'error',
              title: 'Failed',
              text: 'Failed to create user, please check your field',
              showConfirmButton: false,
              timer: 1500
            });

          }
        },

        error: function(err) {
          $("#message").html("Some Error Occurred!")
        }

      });
    });

    $('#update').click(function(e) {
      e.preventDefault();
      var error_ele = document.getElementsByClassName('alert text-sm');
      if (error_ele.length > 0) {
        for (var i = error_ele.length - 1; i >= 0; i--) {
          error_ele[i].remove();
        }
      }

      let user_id = $('#user_id').val();
      // collect data by id
      var data = {
        'user_id': $('#user_id').val(),
        'username': $('#username_edit').val(),
        'password': $('#password_edit').val(),
        'profile': $('#profile_edit option:selected').text(),
        'nas': $('#nas_edit').val(),
        'hotspot_server': $('#hotspot_server_edit').val(),
      }


      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      // ajax proses
      $.ajax({
        url: baseUrl + `/${user_id}`,
        type: "PUT",
        cache: false,
        data: data,
        dataType: "json",

        // tampilkan pesan Success

        success: function(data) {
          if (data.success) {
            Swal.fire({

              icon: 'success',
              title: 'Success',
              text: `${data.message}`,
              showConfirmButton: false,
              timer: 1500
            });
            setTimeout(function() {
              table.ajax.reload()
              $('#totaluser').html(data.totaluser)
              $('#totalactive').html(data.totalactive)
              $('#totalsuspend').html(data.totalsuspend)
              $('#totaldisabled').html(data.totaldisabled)
              $('#edit').modal('hide')
            });
          } else {
            $.each(data.error, function(key, value) {
              var el = $(document).find('[name="' + key + '"]');
              el.after($('<span class= "alert text-sm text-danger">' + value[0] +
                '</div>'));
            });
            Swal.fire({
              icon: 'error',
              title: 'Failed',
              text: 'Failed to create user, please check your field',
              showConfirmButton: false,
              timer: 1500
            });

          }
        },

        error: function(err) {
          $("#message").html("Some Error Occurred!")
        }

      });
    });

    let is_generate = false;
    $('#print_voucher').click(function(e) {
      e.preventDefault();
      $('#print_voucher').attr("disabled", true);
      $("#print_voucher").html('Print&nbsp<i class="fa fa-refresh fa-spin" id="spinner"></i>');
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      let checked = $('#myTable tbody .row-cb:checked')
      let ids = []

      if (is_generate === true) {
        ids = id_selected
      } else {
        $.each(checked, function(index, elm) {
          ids.push(elm.value)
        })
      }
      Swal.fire({
        icon: 'success',
        title: 'Success',
        text: `Voucher Berhasil Diprint! Harap Tunggu.`,
        showConfirmButton: false,
        timer: 5000
      });
      // ajax proses
      $.ajax({
        url: baseUrl + `/print`,
        type: "POST",
        cache: false,
        data: {
          ids: ids
        },
        dataType: "json",

        // tampilkan pesan Success

        success: function(data) {
          $('#print_voucher').attr("disabled", false);
          $("#spinner").remove();
          let win = window.open();
          win.document.write(data.data);
          win.document.close();
          setTimeout(() => {
            win.print();
            win.close();
          }, 500);
          $("#print").modal('hide');
        },
        error: function(err) {
          $("#message").html("Some Error Occurred!")
        }

      });

    });

    $('#enable').click(function(e) {
      e.preventDefault();
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      let checked = $('#myTable tbody .row-cb:checked')
      let ids = []

      if (is_generate === true) {
        ids = id_selected
      } else {
        $.each(checked, function(index, elm) {
          ids.push(elm.value)
        })
      }
      // ajax proses
      $.ajax({
        url: baseUrl + `/enable`,
        type: "POST",
        cache: false,
        data: {
          ids: ids
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
              $('#totaluser').html(data.totaluser)
              $('#totalactive').html(data.totalactive)
              $('#totalsuspend').html(data.totalsuspend)
              $('#totaldisabled').html(data.totaldisabled)
            });
          $('#head-cb').prop('checked', false)
          $(".row-count").html('');
        },

        error: function(err) {
          $("#message").html("Some Error Occurred!")
        }

      });
    });
    $('#disable').click(function(e) {
      e.preventDefault();
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      let checked = $('#myTable tbody .row-cb:checked')
      let ids = []

      if (is_generate === true) {
        ids = id_selected
      } else {
        $.each(checked, function(index, elm) {
          ids.push(elm.value)
        })
      }
      // ajax proses
      $.ajax({
        url: baseUrl + `/disable`,
        type: "POST",
        cache: false,
        data: {
          ids: ids
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
              $('#totaluser').html(data.totaluser)
              $('#totalactive').html(data.totalactive)
              $('#totalsuspend').html(data.totalsuspend)
              $('#totaldisabled').html(data.totaldisabled)
            });
          $('#head-cb').prop('checked', false)
          $(".row-count").html('');
        },

        error: function(err) {
          $("#message").html("Some Error Occurred!")
        }

      });
    });
    $('#reactivate').click(function(e) {
      e.preventDefault();
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      let checked = $('#myTable tbody .row-cb:checked')
      let ids = []

      if (is_generate === true) {
        ids = id_selected
      } else {
        $.each(checked, function(index, elm) {
          ids.push(elm.value)
        })
      }
      // ajax proses
      $.ajax({
        url: baseUrl + `/reactivate`,
        type: "POST",
        cache: false,
        data: {
          ids: ids
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
              $('#totaluser').html(data.totaluser)
              $('#totalactive').html(data.totalactive)
              $('#totalsuspend').html(data.totalsuspend)
              $('#totaldisabled').html(data.totaldisabled)
            });
          $('#head-cb').prop('checked', false)
          $(".row-count").html('');
        },

        error: function(err) {
          $("#message").html("Some Error Occurred!")
        }

      });
    });
    $('#delete').on('click', function() {
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
          $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $(
                  'meta[name="csrf-token"]'
                )
                .attr('content')
            }
          });

          let checked = $('#myTable tbody .row-cb:checked')
          let ids = []
          if (is_generate === true) {
            ids = id_selected
          } else {
            $.each(checked, function(index, elm) {
              ids.push(elm.value)
            })
          }
          $.ajax({
            url: baseUrl + `/delete`,
            type: "POST",
            cache: false,
            data: {
              _method: "DELETE",
              ids: ids
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
                  $('#totaluser').html(data.totaluser)
                  $('#totalactive').html(data.totalactive)
                  $('#totalsuspend').html(data.totalsuspend)
                  $('#totaldisabled').html(data.totaldisabled)
                });
              $('#head-cb').prop('checked', false)
              $(".row-count").html('');
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

    $('#show_session').click(function() {
      let username = $('#username_edit').val();
      $.ajax({
        url: baseUrl + `/getSession/${username}`,
        type: 'GET',
        data: {
          username: username
        },
        success: function(response) {
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
                  if (data == 0) return '0 Bytes';
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
                  if (data == 0) return '0 Bytes';
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

    $('#reseller').select2({
      dropdownParent: $("#generate"),
      width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
      placeholder: $(this).data('placeholder'),
    });

    $('#reseller_c').select2({
      dropdownParent: $("#create .modal-content"),
      width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
      placeholder: $(this).data('placeholder'),
    });

    /* Fungsi */
    function formatRupiah(angka, prefix) {
      var number_string = angka.replace(/[^,\d]/g, '').toString(),
        split = number_string.split(','),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

      if (ribuan) {
        separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
      }

      rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
      return prefix == undefined ? rupiah : (rupiah ? '' + rupiah : '');
    }
  </script>
@endsection
