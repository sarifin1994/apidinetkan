@extends('backend.layouts.app')

@section('title', 'Service Data')

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
          <h3>Service Data</h3>
        </div>
        <div class="col-sm-6 pe-0">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">
                <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item">Service</li>
            <li class="breadcrumb-item active">Data</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <div class="container-fluid user-management-page">
    <div class="row">
      <div class="col-xxl-6 col-lg-8 box-col-8e">
        <div class="card">
          <div class="card-header">
            <h4>Total Services by Status</h4>
          </div>
          <div id="serviceStats" class="card-body">
            <div class="row">
              <div class="col-12">
                <div class="total-num counter">
                  <div class="d-flex by-role custom-scrollbar">
                    <div>
                      <div class="total-user bg-light-primary">
                        <h5>Active</h5>
                        <span class="total-num counter">{{ $totalActive }}</span>
                      </div>
                    </div>
                    <div>
                      <div>
                        <div class="total-user bg-light-primary">
                          <h5>New</h5>
                          <span class="total-num counter">{{ $totalNew }}</span>
                        </div>
                      </div>
                    </div>
                    <div>
                      <div class="total-user bg-light-primary">
                        <h5>Suspended</h5>
                        <span class="total-num counter">{{ $totalSuspend }}</span>
                      </div>
                    </div>
                    <div>
                      <div class="total-user bg-light-primary">
                        <h5>Inactive</h5>
                        <span class="total-num counter">{{ $totalInactive }}</span>
                      </div>
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
            <h4>Total Members by Status</h4>
          </div>
          <div id="memberStats" class="card-body">
            <div class="row">
              <div class="col-12">
                <div class="total-num counter">
                  <div class="d-flex by-role custom-scrollbar">
                    <div>
                      <div class="total-user bg-light-primary">
                        <h5>Active</h5>
                        <span class="total-num counter">{{ $totalActiveMembers }}</span>
                      </div>
                    </div>
                    <div>
                      <div>
                        <div class="total-user bg-light-primary">
                          <h5>New</h5>
                          <span class="total-num counter">{{ $totalNewMembers }}</span>
                        </div>
                      </div>
                    </div>
                    <div>
                      <div class="total-user bg-light-primary">
                        <h5>Inactive</h5>
                        <span class="total-num counter">{{ $totalInactiveMembers }}</span>
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

    <div class="row mb-4">
      <div class="col-12 mb-4 rounded p-3">
        <ul class="nav nav-pills justify-content-center" id="data-tab" role="tablist">
          <li class="nav-item" role="presentation"><a class="nav-link active" id="data-pppoe-tab" data-bs-toggle="tab"
              href="#data-pppoe" role="tab" aria-controls="data-pppoe" aria-selected="true">
              <i class="icofont icofont-ui-user"></i>
              Services
            </a>
          </li>
          @role(['admin', 'helpdesk'])
            <li class="nav-item" role="presentation"><a class="nav-link" id="data-member-tab" data-bs-toggle="tab"
                href="#data-member" role="tab" aria-controls="data-member" aria-selected="false">
                <i class="icofont icofont-man-in-glasses"></i>
                Members
              </a>
            </li>
          @endrole
        </ul>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="tab-content" id="data-tabContent">
          <div class="tab-pane fade active show" id="data-pppoe" role="tabpanel" aria-labelledby="data-pppoe-tab">
            @role('admin')
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-body">
                      <div class="bg-light-primary b-r-15">
                        <div class="upcoming-box">
                          <div class="d-flex gap-2 px-4">
                            <button class="btn btn-primary dropdown-toggle d-flex align-items-center gap-2" type="button"
                              id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                              <i class="fa fa-cog"></i>
                              {{ __('Action') }}
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                              <li>
                                <a class="dropdown-item" href="#" id="deleteSelectedData">
                                  <i class="fa fa-trash"></i>
                                  {{ __('Delete Selected') }}
                                </a>
                              </li>
                            </ul>

                            <button class="btn btn-primary" type="button" data-bs-toggle="modal"
                              data-bs-target="#create">
                              <i class="fa fa-plus"></i>
                              {{ __('Create') }}
                            </button>

                            <button class="btn btn-success" type="button" data-bs-toggle="modal"
                              data-bs-target="#import-service">
                              <i class="fa fa-upload"></i>
                              {{ __('Import') }}
                            </button>

                            <a href="{{ route('admin.services.export') }}" class="btn btn-warning">
                              <i class="fa fa-download"></i>
                              {{ __('Export') }}
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            @endrole

            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-body">
                    <div class="table-responsive custom-scrollbar">
                      <table id="pppoeTable" class="table-hover display nowrap clickable table" width="100%">
                        <thead>
                          <tr>
                            <th>
                              <input class="checkbox_animated" id="checkAll" type="checkbox">
                            </th>
                            @role('admin')
                              <th>BILL</th>
                            @endrole
                            <th>INET</th>
                            <th>SERVICE</th>
                            <th>NAME</th>
                            <th>PROFILE</th>
                            <th>TYPE</th>
                            @role('admin')
                              <th>BILLING</th>
                              <th>CYCLE</th>
                            @endrole
                            <th>ACTIVE</th>
                            @role('admin')
                              <th>DUE</th>
                            @endrole
                            <th>NAS</th>
                            <th>AREA</th>
                            <th>ODP</th>
                            @role('admin')
                              <th>INVOICE</th>
                            @endrole
                            <th>REGISTERED</th>
                          </tr>
                        </thead>
                        <tbody>
                        </tbody>
                      </table>
                    </div>

                    @role('admin')
                      @include('services.data.modals.create')
                      @include('services.data.modals.edit')
                      @include('services.data.modals.session')
                      @include('services.data.modals.invoices')
                      @include('services.data.modals.payment')
                      @include('services.data.modals.import')
                    @endrole
                    @include('services.data.modals.pppoe')
                  </div>
                </div>
              </div>
            </div>
          </div>

          @role(['admin', 'helpdesk'])
            <div class="tab-pane fade" id="data-member" role="tabpanel" aria-labelledby="data-member-tab">
              @include('services.members.table')
            </div>
          @endrole
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script src="{{ asset('assets/radiusqu/dist/js/moment.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/datatable.custom.js') }}"></script>

  <script type="text/javascript">
    const areaUrl = baseUrl.clone().pop().pop() + '/settings/master/area';
    const odpUrl = baseUrl.clone().pop().pop() + '/settings/master/odp';

    let table = $('#pppoeTable').DataTable({
      processing: true,
      serverSide: true,
      stateSave: true,
      lengthMenu: [10, 25, 50, 100, 200, 500, 1000, 2000],
      ajax: '{{ url()->current() }}',
      columns: [{
          // Removed `sortable: false`
          data: 'id',
          name: 'id',
          searchable: false,
          render: function(data, type, row, meta) {
            return `<input class="checkbox_animated" type="checkbox" name="checkedIds[]" value="${data}">`;
          }
        },
        @role('admin')
          {
            // Removed `sortable: false`
            data: 'status',
            name: 'status',
            className: 'text-center',
            searchable: false,
            render: function(data) {
              if (data === 1) {
                return '<i class="fas fa-circle-check text-success"></i>';
              } else {
                return '<i class="fas fa-circle-xmark text-danger"></i>';
              }
            }
          },
        @endrole {
          // Removed `sortable: false`
          data: 'session_internet',
          name: 'session_internet',
          className: 'text-center',
          searchable: false,
          render: function(data, type, row) {
            if (row.session.session_id !== null && row.session.status === 1 && row.session.ip !== null && row
              .status === 1) {
              return '<i class="fas fa-circle text-success"></i>';
            } else if (row.session.session_id !== null && row.session.status === 2 && row.session.ip !== null) {
              return '<i class="fas fa-circle text-danger"></i>';
            } else if (row.session.session_id !== null && row.session.status === 1 && row.session.ip !== null &&
              row.status === 2) {
              return '<i class="fas fa-circle text-warning"></i>';
            } else {
              return '<i class="fas fa-circle text-danger"></i>';
            }
          },
        },
        {
          data: 'member.id_service',
          name: 'member.id_service',
          // No longer includes `sortable: false`.
          render: function(data, type, row) {
            if (!data) {
              return `<span class="btn btn-sm btn-danger">UNKNOWN</span>`;
            }
            return `<button class="btn btn-sm btn-primary show-pppoe"
                  data-bs-toggle="modal" data-bs-target="#showPppoe"
                  data-id="${row.id}">
                  <i class="fas fa-info-circle"></i> ${data}
                </button>`;
          },
        },
        {
          data: 'member.data.full_name',
          name: 'member.data.full_name',
          render: function(data) {
            return data ? data : '-';
          },
        },
        {
          data: 'profile_name',
          name: 'profile',
          render: function(data) {
            return data ? data : '<i class="text-danger">UNKNOWN</i>';
          },
        },
        {
          data: 'type',
          name: 'type',
          className: "text-uppercase",
        },
        @role('admin')
          {
            data: 'member.payment_type',
            name: 'member.payment_type',
            className: "text-uppercase",
            render: function(data, type, row) {
              if (!data) {
                return `<button class="btn btn-sm btn-danger show-payment"
                      data-bs-toggle="modal" data-bs-target="#showPayment"
                      data-id="${row.id}" data-new="true">
                      <i class="fas fa-wallet"></i> Create
                    </button>`;
              } else {
                if (data === 'Prabayar') {
                  return `<button class="btn btn-sm btn-info show-payment"
                        data-bs-toggle="modal" data-bs-target="#showPayment"
                        data-id="${row.id}">
                        <i class="fas fa-wallet"></i> ${data}
                      </button>`;
                } else {
                  return `<button class="btn btn-sm btn-warning show-payment"
                        data-bs-toggle="modal" data-bs-target="#showPayment"
                        data-id="${row.id}">
                        <i class="fas fa-wallet"></i> ${data}
                      </button>`;
                }
              }
            },
          }, {
            data: 'member.billing_period',
            name: 'member.billing_period',
            className: "text-uppercase",
            render: function(data) {
              return data ? data : '<i class="text-danger">Unknown</i>';
            },
          },
        @endrole {
          data: 'member.reg_date',
          name: 'member.reg_date',
          render: function(data, type, row, meta) {
            return moment(data).local().format('DD/MM/YYYY');
          },
        },
        @role('admin')
          {
            data: 'member.next_due',
            name: 'member.next_due',
            render: function(data) {
              return moment(data).local().format('DD/MM/YYYY');
            },
          },
        @endrole {
          data: 'nas_name',
          name: 'nas',
          render: function(data, type, row) {
            if (row.nas === null) {
              return 'all';
            } else if (row.nas_name === null) {
              return '<i class="text-danger">Unknown</i>';
            } else {
              return data;
            }
          },
        },
        {
          data: 'area_name',
          name: 'kode_area',
          render: function(data) {
            return data === null ? '-' : data;
          },
        },
        {
          data: 'odp_name',
          name: 'kode_odp',
          render: function(data) {
            return data === null ? '-' : data;
          },
        },
        @role('admin')
          {
            data: 'id',
            name: 'id',
            // Removed `sortable: false, searchable: false` so it�s now sortable & searchable
            render: function(data, type, row) {
              return `
            <button class="btn btn-sm btn-success show-invoice"
                    data-bs-toggle="modal" data-bs-target="#showInvoices"
                    data-id="${data}" title="See Invoice History">
              <i class="fas fa-file-invoice"></i> Invoice
            </button>`;
            },
          },
        @endrole {
          data: 'created_at',
          name: 'created_at',
          render: function(data) {
            return moment(data).local().format('DD/MM/YYYY');
          },
        },
      ]
    });


    $('#pppoeTable tbody').on('click', 'tr td:not(:nth-child(1))', function(event) {
      if ($(event.target).is('button')) return;

      let row_user_id = table.row(this).data().id;
      let row_username = table.row(this).data().username;
      let row_kode_area_id = table.row(this).data().rarea.id;
      let row_kode_odp = table.row(this).data().kode_odp;

      $.ajax({
        url: baseUrl + `/${row_user_id}`,
        type: "GET",
        success: function(data) {
          $('#user_id').val(data.data.id);
          $('#kode_area_id').val(row_kode_area_id);
          $('#kode_odp_id').val(row_kode_odp);
          $('#username_edit').val(data.data.username);
          $('#password_edit').val(data.data.value);
          $('#profile_edit').val(data.data.rprofile.id);
          $('#nas_edit').val(data.data.nas);
          $('#kode_area_edit').val(data.data.rarea.id).trigger('change');
          $('#kode_odp_edit').val(row_kode_odp).trigger('change');

          $('#nas_secret').val(data.data.radius.secret);
          if (data.data.status === 0) {
            $('#disabled').addClass('disabled');
            $('#enabled').attr('data-id', data.data.id);
            $('#suspend').attr('data-id', data.data.id);
          } else if (data.data.status === 1) {
            $('#enabled').addClass('disabled');
            $('#disabled').attr('data-id', data.data.id);
            $('#suspend').attr('data-id', data.data.id);
          } else if (data.data.status === 2) {
            $('#suspend').addClass('disabled');
            $('#enabled').attr('data-id', data.data.id);
            $('#disabled').attr('data-id', data.data.id);
          };

          if (data.data.lock_mac === 1) {
            $('#show_mac_edit').show();
          } else {
            $('#show_mac_edit').hide();
          };

          $('#lock_mac_edit').change(function() {
            let lock_mac_edit = $(this).val(); //Get selected option value
            if (lock_mac_edit == '1') {
              $('#show_mac_edit').show()
            } else {
              $('#show_mac_edit').hide()
            }
          });

          $('#lock_mac_edit').val(data.data.lock_mac);
          $('#mac_edit').val(data.data.mac);
          $('#member_id_edit').append(new Option(data.data.member.data.full_name, data.data.member.member_id,
            true, true)).trigger('change');
        }
      });

      $('#edit').modal('show');
    });

    $('#kode_area').on('change', function() {
      var kode_area = $(this).val();
      if (kode_area) {
        $.ajax({
          url: baseUrl + `/${kode_area}/kode-odp`,
          type: 'GET',
          data: {
            kode_area_id: kode_area,
            '_token': '{{ csrf_token() }}'
          },
          dataType: "json",
          success: function(data) {
            if (data) {
              $('kode_odp').empty();
              $('#kode_odp').attr('disabled', false);
              $('#kode_odp').html(
                '<option value="">Pilih Kode ODP</option>');
              $.each(data.odp, function(key, value) {
                $("#kode_odp").append('<option value="' + value
                  .kode_odp + '">' + value.kode_odp +
                  '</option>');
              });

            } else {
              $('kode_odp').empty();
            }
          }
        });
      } else {
        $('kode_odp').empty();
      }
    });

    $('#kode_area_edit').on('change', function() {
      var kode_area = $(this).val();
      const kode_odp = $('#kode_odp_id').val();

      $('#kode_odp_edit').html(
        '<option value="">Pilih Kode ODP</option>'
      );

      if (kode_area) {
        $.ajax({
          url: baseUrl + `/${kode_area}/kode-odp`,
          type: 'GET',
          data: {
            kode_area_id: kode_area,
            '_token': '{{ csrf_token() }}'
          },
          dataType: "json",
          success: function(data) {
            if (data) {
              $('#kode_odp_edit').attr('disabled', false);

              $.each(data.odp, function(key,
                value) {
                $("#kode_odp_edit")
                  .append(
                    '<option value="' +
                    value
                    .kode_odp +
                    '">' + value
                    .kode_odp +
                    '</option>'
                  );
              });

              $('#kode_odp_edit').val(kode_odp).trigger('change');
            } else {
              $('kode_odp_edit').empty();
            }
          }
        });
      } else {
        $('kode_odp_edit').empty();
      }
    });

    $("#profile").on("change", function() {
      var profile_id = $(this).val();

      if (!profile_id) {
        return;
      }

      $.ajax({
        url: baseUrl + `/${profile_id}/price`,
        type: "GET",
        cache: false,
        success: function(data) {
          var amount = data.price;
          rp_amount = formatRupiah(amount, 2, ',', '.');
          $('#amount').val(rp_amount);
          rp_total_amount = formatRupiah(amount, 2, ',', '.');
          $('#payment_total').val(rp_total_amount);
        }
      });
    });

    $("#ppn,#discount").on("keyup change", function() {
      var ppn = $('#ppn').val();
      var discount = $('#discount').val();
      var amount_ppn = amount * ppn / 100;
      var amount_discount = amount * discount / 100;
      if (discount === null) {
        var total_with_ppn = parseInt(amount) + parseInt(amount_ppn);
        var total_plus_ppn = total_with_ppn.toString();
        rp_total_plus_ppn = formatRupiah(total_plus_ppn, 2, ',', '.');
        $('#payment_total').val(rp_total_plus_ppn);
      } else {
        var total_with_ppn_discount = parseInt(amount) + parseInt(
          amount_ppn) - parseInt(amount_discount);
        var total_plus_ppn_discount = total_with_ppn_discount.toString();
        rp_total_plus_ppn_discount = formatRupiah(total_plus_ppn_discount,
          2, ',', '.');
        $('#payment_total').val(rp_total_plus_ppn_discount);
      }
    });

    $('#payment_type').change(function() {
      let payment_type = $(this).val();
      if (payment_type == 'Prabayar') {
        $('#show_payment_status').show()
        $('#billing_period').html(
          "<option value='Fixed Date'>Fixed Date</option><option value='Billing Cycle' disabled>Billing Cycle</option>"
        );
      } else {
        $('#show_payment_status').hide()
        $('#billing_period option:disabled').removeAttr('disabled');
      }
    });

    $('#edit').on('hidden.bs.modal', function() {
      $('#enabled').removeClass('disabled');
      $('#disabled').removeClass('disabled');
      $('#suspend').removeClass('disabled');
    });

    $("#enabled").click(function() {
      let id = $('#user_id').val();
      var data = {
        'username': $('#username_edit').val(),
        'nas': $('#nas_edit option:selected').val(),
        'secret': $('#nas_secret').val(),
      }

      $.ajax({
        url: baseUrl + `/enable/${id}`,
        type: "PUT",
        cache: false,
        data: data,
        dataType: "json",

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
              $('#edit').modal('hide')
            });
          } else {
            $.each(data.error, function(key, value) {
              var el = $(document).find('[name="' + key + '"]');
              el.after($('<span class= "text-sm text-danger">' +
                value[0] +
                '</div>'));
            });
          }
        },

        error: function(err) {
          $("#message").html("Some Error Occurred!")
        }

      });

    });

    $("#disabled").click(function() {
      let id = $('#user_id').val();
      var data = {
        'username': $('#username_edit').val(),
        'nas': $('#nas_edit option:selected').val(),
        'secret': $('#nas_secret').val(),
      }
      $.ajax({
        url: baseUrl + `/disable/${id}`,
        type: "PUT",
        cache: false,
        data: data,
        dataType: "json",

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
              $('#edit').modal('hide')
            });
          } else {
            $.each(data.error, function(key, value) {
              var el = $(document).find('[name="' + key + '"]');
              el.after($('<span class= "text-sm text-danger">' +
                value[0] +
                '</div>'));
            });
          }
        },

        error: function(err) {
          $("#message").html("Some Error Occurred!")
        }

      });

    });

    $("#suspend").click(function() {
      let id = $('#user_id').val();
      var data = {
        'username': $('#username_edit').val(),
        'nas': $('#nas_edit option:selected').val(),
        'secret': $('#nas_secret').val(),
      }
      $.ajax({
        url: baseUrl + `/suspend/${id}`,
        type: "PUT",
        cache: false,
        data: data,
        dataType: "json",

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
              $('#edit').modal('hide')
            });
          } else {
            $.each(data.error, function(key, value) {
              var el = $(document).find('[name="' + key + '"]');
              el.after($('<span class= "text-sm text-danger">' +
                value[0] +
                '</div>'));
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

      var data = {
        'username': $('#username_edit').val(),
        'password': $('#password_edit').val(),
        'profile': $('#profile_edit option:selected').text(),
        'profile_id': $('#profile_edit option:selected').val(),
        'member_id': $('#member_id_edit').val(),
        'ip_address': $('#ip_address_edit').val(),
        'nas': $('#nas_edit').val(),
        'kode_area': $('#kode_area_edit option:selected').text(),
        'kode_odp': $('#kode_odp_edit').val(),
        'lock_mac': $('#lock_mac_edit').val(),
        'mac': $('#mac_edit').val(),
      }

      $.ajax({
        url: baseUrl + `/${user_id}`,
        type: "PUT",
        cache: false,
        data: data,
        dataType: "json",

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
              $('#edit').modal('hide');
            });
          } else {
            $.each(data.error, function(key, value) {
              var el = $(document).find('[name="' + key + '"]');
              el.after($('<span class= "alert text-sm text-danger">' +
                value[0] +
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
          Swal.fire({
            icon: 'error',
            title: 'Failed',
            text: 'Data Gagal Disimpan!',
            showConfirmButton: false,
            timer: 1500
          });

        }

      });
    });

    $('#create').on('shown.bs.modal', function() {
      document.getElementById('reg_date').valueAsDate = new Date();
    });

    $('#checkAll').on('click', function() {
      $(this).closest('table').find('input:checkbox').prop('checked', this.checked);
    });

    $('#pppoeTable tbody').on('click', 'input:checkbox', function(e) {
      e.stopPropagation();

      if (!this.checked) {
        $('#checkAll').prop('checked', false);
      }
    });

    $('#deleteSelectedData').on('click', function() {
      const checkedMemberIds = $('input[name="checkedIds[]"]:checked').map(function() {
        return $(this).val();
      }).get();

      if (checkedMemberIds.length === 0) {
        toastr.warning('Please select at least one member to delete.');
        return;
      }

      Swal.fire({
        title: 'Are you sure?',
        text: 'You won\'t be able to revert this!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: baseUrl,
            method: 'DELETE',
            data: {
              ids: checkedMemberIds
            },
            success: function(response) {
              table.ajax.reload();
              toastr.success(response.message);
            },
            error: function(xhr) {
              const errors = xhr.responseJSON.errors;
              let message = '';

              for (const key in errors) {
                message += errors[key] + '\n';
              }

              toastr.error(message);
            }
          });
        }
      });
    });

    $(document).on('ajaxSuccess', function(event, request, settings) {
      const type = settings.type.toLowerCase();

      if (type !== 'post' && type !== 'put' && type !== 'delete') {
        return;
      }

      refreshStats();
    });

    $(window).on('form:global:success', function(event) {
      const method = event.detail.method.toLowerCase();

      if (method !== 'post' && method !== 'put' && method !== 'delete') {
        return;
      }

      refreshStats();
    });

    /* Fungsi */
    function refreshStats() {
      // refresh total services counter
      $.ajax({
        url: baseUrl.clone().pop() + '/data/stats',
        method: 'GET',
        success: function(response) {
          const totalActive = response.total_active;
          const totalNew = response.total_new;
          const totalSuspend = response.total_suspend;
          const totalInactive = response.total_inactive;

          // Change the number of total services
          $('#serviceStats span.total-num').each(function(index, element) {
            const total = [totalActive, totalNew, totalSuspend, totalInactive][index];
            $(element).text(total);
          });
        }
      });

      // refresh total members counter
      $.ajax({
        url: baseUrl.clone().pop() + '/member/stats',
        method: 'GET',
        success: function(response) {
          const totalActiveMembers = response.total_active;
          const totalNewMembers = response.total_new;
          const totalInactiveMembers = response.total_inactive;

          // Change the number of total members
          $('#memberStats span.total-num').each(function(index, element) {
            const total = [totalActiveMembers, totalNewMembers, totalInactiveMembers][index];
            $(element).text(total);
          });
        }
      });
    }

    function formatRupiah(angka, prefix) {
      angka = angka.toString();
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
@endpush
