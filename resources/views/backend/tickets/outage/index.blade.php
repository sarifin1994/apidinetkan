@extends('backend.layouts.app')

@section('title', 'New Client Ticket Management')

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
          <h3>New Client Management</h3>
        </div>
        <div class="col-sm-6 pe-0">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">
                <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item">Tickets</li>
            <li class="breadcrumb-item active">New Client Management</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <div class="container-fluid user-management-page">
    <div class="row">
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
                    <p>Outage</p>

                    <div class="d-flex justify-content-center gap-2">
                      <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#create">
                        <i class="fa fa-plus"></i>
                        {{ __('Create') }}
                      </button>
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
            <h4>Total Ticket by Status</h4>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-12">
                <div class="total-num counter">
                  <div class="d-flex by-role custom-scrollbar">
                    <div>
                      <div class="total-user bg-light-primary">
                        <h5>Open</h5>
                        <span class="total-num counter">{{ $totalOpen }}</span>
                      </div>
                    </div>
                    <div>
                      <div class="total-user bg-light-primary">
                        <h5>Closed</h5>
                        <span class="total-num counter">{{ $totalClosed }}</span>
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
              <div class="col-lg-3">
                <div class="form-group mb-3">
                  <label for="mindate" class="mb-1">DARI TANGGAL</label>
                  <input type="date" data-column="2" class="form-control daterange" id="mindate" name="mindate">
                </div>
              </div>
              <div class="col-lg-3">
                <div class="form-group mb-3">
                  <label for="maxdate" class="mb-1">SAMPAI TANGGAL</label>
                  <input type="date" data-column="2" class="form-control daterange" id="maxdate" name="maxdate">
                </div>
              </div>
              <div class="col-lg-3">
                <div class="form-group mb-3">
                  <label for="filter_status" class="mb-1">STATUS TICKET</label>
                  <select data-column="5" class="form-select" id="filter_status" name="filter_status">
                    <option value="">FILTER STATUS</option>
                    <option value="0">OPEN</option>
                    <option value="1">CLOSED</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="table-responsive custom-scrollbar">
              <table id="myTable" class="table-hover display nowrap table" width="100%">
                <thead>
                  <tr>
                    <th style="text-align:left!important">NO</th>
                    {{-- <th>ID GGN</th> --}}
                    <th>TGL OPEN</th>
                    {{-- <th style="text-align:left!important">Jam</th> --}}
                    <th>NAMA LENGKAP</th>
                    <th>KODE AREA</th>
                    <th>JENIS GANGGUAN</th>
                    <th>STATUS</th>
                    <th style="text-align:left!important">TGL CLOSED</th>
                    {{-- <th style="text-align:left!important">Jam</th> --}}
                    <th>CLOSED BY</th>
                    <th>AKSI</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>

              @include('tickets.outage.modal.create')
              @include('tickets.outage.modal.show_data')
            </div>
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
    const pppoeUrl = baseUrl.clone().pop().replace('ticket', 'pppoe');
    const billingUrl = baseUrl.clone().pop().replace('ticket', 'billing');

    const showData = new bootstrap.Modal(document.getElementById('show_data'), {
      keyboard: false
    });

    let table = $('#myTable').DataTable({
      processing: true,
      serverSide: true,
      // scrollX: true,
      order: [
        [1, 'desc']
      ],
      ajax: '{{ url()->current() }}',
      columns: [{
          data: null,
          'sortable': false,
          render: function(data, type, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
          }
        },
        // {
        //     data: 'id_ggn',
        //     name: 'id_ggn',
        //     render: function(data, type, row) {
        //         if (row.tipe === 1) {
        //             return '<span class="badge bg-primary">' + data + '</span>'
        //         } else {
        //             return '<span class="badge bg-secondary">' + data + '</span>'
        //         }
        //     },
        // },

        {
          data: 'tgl_open',
          name: 'tgl_open',
          render: function(data, type, row, meta) {
            return moment(data).local().format('DD/MM/YYYY HH:mm');
          },
        },

        // {
        //     data: 'tgl_open',
        //     name: 'tgl_open',
        //     render: function(data, type, row, meta) {
        //         return moment(data).local().format('HH:mm');
        //     },
        // },

        {
          data: 'nama_lengkap',
          name: 'nama_lengkap',
          render: function(data, type, row) {
            if (data === 'MASSAL') {
              return '<span class="text-danger">' + data + '</span>'
            } else {
              return '<a href="javascript:void(0)" class="text-primary" style="text-decoration:none" id="show_data" data-ppp=' +
                row.rpppoe.id +
                ' data-username=' +
                row.rpppoe.username +
                ' data-id=' +
                row.rmember.id +
                '>' + data + '</a>'
            }
          },

        },
        {
          data: 'kode_area',
          name: 'kode_area',
          render: function(data) {
            if (data === 'NULL') {
              return '<i>NULL</i>'
            } else {
              return data
            }

          },
        },
        {
          data: 'jenis',
          name: 'jenis',
        },
        {
          data: 'status',
          name: 'status',
          render: function(data) {
            if (data === 0) {
              return '<span class="badge bg-danger">OPEN</span>'
            } else if (data === 1) {
              return '<span class="badge bg-success">CLOSED</span>'
            }

          },
        },
        {
          data: 'tgl_closed',
          name: 'tgl_closed',
          render: function(data, type, row, meta) {
            if (data !== null) {
              return moment(data).local().format('DD/MM/YYYY HH:mm');
            } else {
              return ''
            }
          },
        },
        // {
        //     data: 'tgl_closed',
        //     name: 'tgl_closed',
        //     render: function(data, type, row, meta) {
        //         if (data === null) {
        //             return ''
        //         } else {
        //             return moment(data).local().format('HH:mm');
        //         }
        //     },
        // },
        {
          data: 'closed_by',
          name: 'closed_by',
        },
        {
          data: 'action',
          name: 'action',
          width: '100px',
        },
      ]
    });

    table.on('preXhr.dt', function(e, settings, data) {
      data.start_date = $('#mindate').val();
      data.end_date = $('#maxdate').val();
    });

    $('#maxdate').change(function() {
      table.ajax.reload();
      return false;
    });

    $('#filter_status').change(function() {
      table.column($(this).data('column'))
        .search($(this).val())
        .draw();
    });

    $('#myTable').on('click', '#delete', function() {

      let id = $(this).data('id');

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
                timer: 1000
              });
              setTimeout(
                function() {
                  location
                    .reload();
                }, 1000);
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

    $('#myTable').on('click', '#show_data', function() {
      let member_id = $(this).data('id');
      let ppp_id = $(this).data('ppp');
      let ppp_username = $(this).data('username');
      if (member_id) {
        $.ajax({
          url: billingUrl + `/member/getPpp/${ppp_id}`,
          type: "GET",
          data: {
            pppoe_id: ppp_id,
            pppoe_username: ppp_username,
          },
          success: function(data) {
            $("#username").html(data.ppp[0].username);
            $("#password").html(data.ppp[0].value);
            $("#profile_inet").html(data.ppp[0].profile);
            if (data.session !== null && data.session.status === 1 &&
              data.session.ip !== null && data.ppp[0].status === 1) {
              $('#status').html(
                '<span class="text-success">online</span>');
              $('#fill_ip_show').html('<span>' + data.session.ip + '</span>');
            } else if (data.session !== null && data.session.status ===
              1 &&
              data.session.ip !== null && data.ppp[0].status === 2) {
              $('#status').html(
                '<span class="text-warning">isolir</span>');
              $('#fill_ip_show').html('<span>' + data.session.ip + '</span>');
            } else if (data.session !== null && data.session.status ===
              2 && data.session.ip !== null) {
              $('#status').html(
                '<span class="text-danger">offline</span>');
              $('#fill_ip_show').html('-');
            } else {
              $('#status').html(
                '<span class="text-danger">offline</span>');
              $('#fill_ip_show').html('-');
            }
            var created = data.ppp[0].created_at;
            var created_at = moment(created).local().format('DD/MM/YYYY HH:mm:ss');
            $("#created").html(created_at);
          }
        });
        $.ajax({
          url: billingUrl + `/member/getContact/${member_id}`,
          type: "GET",
          data: {
            member_id: member_id,
          },
          success: function(data) {

            $("#member_idc").val(data[0].id);
            $("#full_name").val(data[0].full_name);
            $("#wa").val(data[0].wa);
            $("#address").val(data[0].address);
            $("#kode_area_show option:selected").val(data[0].ppp.kode_area).text(data[0].ppp
              .kode_area);
            $("#kode_odp option:selected").val(data[0].ppp.kode_odp).text(data[0].ppp
              .kode_odp);

          }
        });
      } else {

      }
      showData.show();
    });

    $('#myTable').on('click', '#confirm', function() {
      let ggn = $(this).data('id');
      if (ggn) {
        $.ajax({
          url: baseUrl + `/${ggn}`,
          type: "GET",
          success: function(data) {
            var ggn_id = data.data.id;
            var id_ggn = data.data.id_ggn;
            var tipe = data.data.tipe;
            var member = data.data.member_id
            var kode_area = data.data.kode_area;
            var jenis = data.data.jenis;
            var nama = data.data.nama_lengkap
            if (data.data.tipe === 1) {
              var area = ''
            } else {
              var area = data.data.kode_area
            }

            Swal.fire({
              title: "Konfirmasi Gangguan",
              icon: 'warning',
              input: "select",
              inputOptions: {
                'Resplice Kabel': 'Resplice Kabel',
                'Set Ulang ONT': 'Set Ulang ONT',
                'Ganti ONT': 'Ganti ONT',
                'Ganti Kabel': 'Ganti Kabel',
                'Ganti Patchord': 'Ganti Patchord',
                'Ganti Splitter': 'Ganti Splitter',
                'Ganti Adapter': 'Ganti Adapter',
                'Ganti HTB': 'Ganti HTB',
                'Lainnya': 'Lainnya',
              },
              inputPlaceholder: '- Pilih Langkah Penyelesaian -',

              text: "" + id_ggn + " " +
                nama + " " + area + "",
              showCancelButton: !0,
              confirmButtonText: "Ya, Close Ticket",
              cancelButtonText: "Batal",
              reverseButtons: !0,
              customClass: {
                input: 'form-select w-auto bg-none width-auto p-3 mx-10',
              },
              inputValidator: function(value) {
                return new Promise(function(resolve, reject) {
                  if (value !== '') {
                    resolve();
                  } else {
                    resolve(
                      'Silakan pilih langkah penyelesaian'
                    );
                  }
                });
              }
            }).then(function(result) {
              if (result.isConfirmed) {
                let id = ggn_id;
                var data = {
                  'tipe': tipe,
                  'nama': nama,
                  'jenis': jenis,
                  'kode_area': kode_area,
                  'penyelesaian': result.value,
                }

                $.ajaxSetup({
                  headers: {
                    'X-CSRF-TOKEN': $(
                        'meta[name="csrf-token"]'
                      )
                      .attr('content')
                  }
                });

                // ajax proses
                $.ajax({
                  url: baseUrl + `/confirm/${id}`,
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
                      setTimeout(
                        function() {
                          location
                            .reload();
                        }, 1500);
                    } else {
                      $.each(data.error,
                        function(
                          key,
                          value) {
                          var el =
                            $(
                              document)
                            .find(
                              '[name="' +
                              key +
                              '"]'
                            );
                          el.after(
                            $('<span class= "alert text-sm text-danger">' +
                              value[
                                0
                              ] +
                              '</span>'
                            )
                          );
                        });
                    }
                  },

                  error: function(err) {
                    $("#message").html(
                      "Some Error Occurred!"
                    )
                  }

                });
              }
            });
          }
        });
      } else {

      }

    });

    $("#create").on("hidden.bs.modal", function() {
      $('#store').attr("disabled", false);
      $("#spinner").remove();
    });

    // action create
    $('#store').click(function(e) {
      e.preventDefault();
      $('#store').attr("disabled", true);
      $("#store").html('Submit &nbsp<i class="fa fa-refresh fa-spin" id="spinner"></i>');
      var error_ele = document.getElementsByClassName('alert text-sm');
      if (error_ele.length > 0) {
        for (var i = error_ele.length - 1; i >= 0; i--) {
          error_ele[i].remove();
        }
      }

      // collect data by id
      var data = {
        'tipe': $('#tipe').val(),
        'member_id': $('#member_id option:selected').val(),
        'pppoe_id': $('#ppp_id').val(),
        'nama_lengkap': $('#member_id option:selected').text(),
        'kode_area': $('#kode_area option:selected').text(),
        'area': $('#fill_area').text(),
        'jenis': $('#jenis option:selected').text(),
        'jenis_massal': $('#jenis_massal option:selected').val(),
        'note': $('#note').val(),
        'note_massal': $('#note_massal').val(),
        'wa': $('#fill_wa').val(),
        'alamat': $('#fill_alamat').text(),
        'internet': $('#fill_internet').text(),
        'ip': $('#fill_ip').text(),
        'odp': $('#fill_odp').val(),
      }

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      // ajax proses
      $.ajax({
        url: baseUrl + ``,
        type: "POST",
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
            $('#store').attr("disabled", false);
            $("#spinner").remove();
            setTimeout(function() {
              location.reload()
            }, 1500);
          } else {
            $.each(data.error, function(key, value) {
              var el = $(document).find('[for="' + key + '"]');
              el.after($('<span class= "alert text-sm text-danger">' +
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

    // dapatkan value kode area dari member_id
    $("#member_id").on("change", function() {
      var member_id = $(this).val();
      $.ajax({
        url: baseUrl + `/getArea/${member_id}`,
        type: "GET",
        cache: false,
        data: {
          member_id: member_id,
          '_token': '{{ csrf_token() }}'
        },
        success: function(data) {
          $('#fill_name').html(data[0].full_name);
          if (data[0].kode_area === null) {
            $('#fill_area').html('<i>NULL</i>');
          } else {
            $('#fill_area').html(data[0].kode_area);
          }
          $('#fill_alamat').html(data[0].address);
          $('#ppp_id').val(data[0].ppp.id);
          $('#fill_wa').val(data[0].wa);
          $('#fill_odp').val(data[0].ppp.kode_odp);

          let username = data[0].ppp.username;
          if (username) {
            $.ajax({
              url: baseUrl + `/getSession/${username}`,
              type: 'GET',
              data: {
                pppoe_username: username
              },
              success: function(response) {
                if (response.session !== null && response.status === 1 &&
                  response.ip !== null && response.ppp.status === 1) {
                  $('#fill_internet').html(
                    '<span class="text-success">online</span>');
                  $('#fill_ip').html('<span>' + response.ip + '</span>');
                } else if (response.session !== null && response.status ===
                  1 &&
                  response.ip !== null && response.ppp.status === 2) {
                  $('#fill_internet').html(
                    '<span class="text-warning">isolir</span>');
                  $('#fill_ip').html('<span>' + response.ip + '</span>');
                } else if (response.session !== null && response.status ===
                  2 && response.ip !== null) {
                  $('#fill_internet').html(
                    '<span class="text-danger">offline</span>');
                  $('#fill_ip').html('-');
                } else {
                  $('#fill_internet').html(
                    '<span class="text-danger">offline</span>');
                  $('#fill_ip').html('-');
                }
              }
            });

          } else {

          }
        }
      });
    });

    $(document).ready(function() {
      $('#kode_area_add').on('change', function() {
        var kode_area = $(this).val();
        if (kode_area) {
          $.ajax({
            url: pppoeUrl + `/user/getKodeOdp/${kode_area}`,
            type: 'GET',
            data: {
              kode_area_id: kode_area,
              '_token': '{{ csrf_token() }}'
            },
            dataType: "json",
            success: function(data) {
              if (data) {
                $('kode_odp_add').empty();
                $('#kode_odp_add').attr('disabled', false);
                $('#kode_odp_add').html(
                  '<option value="">Pilih Kode ODP</option>');
                $.each(data.odp, function(key, value) {
                  $("#kode_odp_add").append('<option value="' + value
                    .kode_odp + '">' + value.kode_odp +
                    '</option>');
                });

              } else {
                $('kode_odp_add').empty();
              }
            }
          });
        } else {
          $('kode_odp_add').empty();
        }
      });
    });

    $('#tipe').change(function() {
      let tipe = $(this).val(); //Get selected option value
      if (tipe == '1') {
        $('#show_individual').show()
        $('#show_massal').hide()
      } else {
        $('#show_individual').hide()
        $('#show_massal').show()
      }
    });

    $('#kode_area').select2({
      dropdownParent: $("#create .modal-content"),
      width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
      placeholder: $(this).data('placeholder'),
    });

    $('#member_id').select2({
      dropdownParent: $("#create .modal-content"),
      width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
      placeholder: $(this).data('placeholder'),
    });

    $('#jenis').select2({
      dropdownParent: $("#create .modal-content"),
      width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
      placeholder: $(this).data('placeholder'),
    });

    $('#jenis_massal').select2({
      dropdownParent: $("#create .modal-content"),
      width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
      placeholder: $(this).data('placeholder'),
    });
  </script>
@endsection
