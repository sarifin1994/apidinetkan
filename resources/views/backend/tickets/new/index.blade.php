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
                    <p>New Clients</p>

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
                        <h5>Pending</h5>
                        <span class="total-num counter">{{ $totalPending }}</span>
                      </div>
                    </div>
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
                  <select data-column="4" class="form-select" id="filter_status" name="filter_status">
                    <option value="">FILTER STATUS</option>
                    <option value="0">PENDING</option>
                    <option value="1">OPEN</option>
                    <option value="2">CLOSED</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="table-responsive custom-scrollbar">
              <table id="myTable" class="table-hover display nowrap table" width="100%">
                <thead>
                  <tr>
                    <th style="text-align:left!important">No</th>
                    <th>ID PSB</th>
                    <th>TGL REGISTRASI</th>
                    <th>NAMA LENGKAP</th>
                    <th>STATUS</th>
                    <th>TGL AKTIF</th>
                    <th>CLOSED BY</th>
                    <th>AKSI</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>

              @include('tickets.new.modal.create')
              @include('tickets.new.modal.create_secret')
              @include('tickets.new.modal.show_data')
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
    const serviceUrl = baseUrl.clone().pop().replace('ticket', 'services');
    const billingUrl = baseUrl.clone().pop().replace('ticket', 'billing');

    const createSecret = new bootstrap.Modal(document.getElementById('create_secret'), {
      keyboard: false
    });
    const showData = new bootstrap.Modal(document.getElementById('show_data'), {
      keyboard: false
    });

    let table = $('#myTable').DataTable({
      processing: true,
      serverSide: true,
      // scrollX: true,
      order: [
        [2, 'desc']
      ],
      ajax: '{{ url()->current() }}',
      columns: [{
          data: null,
          'sortable': false,
          render: function(data, type, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
          }
        },
        {
          data: 'id_psb',
          name: 'id_psb',
          render: function(data) {
            return '<span class="badge bg-primary">' + data + '</span>'
          },
        },

        {
          data: 'created_at',
          name: 'created_at',
          render: function(data, type, row, meta) {
            return moment(data).local().format('DD/MM/YYYY');
          },
        },
        {
          data: 'full_name',
          name: 'full_name',
          render: function(data, type, row) {
            return '<a href="javascript:void(0)" class="text-primary" style="text-decoration:none" id="show_data" data-ppp=' +
              row.rpppoe.id +
              ' data-username=' +
              row.rpppoe.username +
              ' data-id=' +
              row.rmember.id +
              '>' + data + '</a>'
          },

        },
        {
          data: 'status',
          name: 'status',
          render: function(data) {
            if (data === 2) {
              return '<span class="badge bg-success">CLOSED</span>'
            } else if (data === 0) {
              return '<span class="badge bg-warning">PENDING</span>'
            } else if (data === 1) {
              return '<span class="badge bg-danger">OPEN</span>'
            }

          },
        },
        {
          data: 'tgl_aktif',
          name: 'tgl_aktif',
          render: function(data, type, row, meta) {
            if (data !== null) {
              return moment(data).local().format('DD/MM/YYYY');
            } else {
              return ''
            }
          },
        },
        {
          data: 'closed_by',
          name: 'closed_by',
        },
        {
          data: 'action',
          name: 'action',
          width: '120px',
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
                  table.ajax.reload()
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

    $('#myTable').on('click', '#show_data', function() {
      let member_id = $(this).data('id');
      let ppp_id = $(this).data('ppp');
      let ppp_username = $(this).data('username');
      if (member_id) {
        $.ajax({
          url: baseUrl + `/${ppp_id}/service`,
          type: "GET",
          success: function(data) {
            $("#member_idc").val(data[0].id);
            $("#full_name").val(data[0].full_name);
            $("#wa").val(data[0].wa);
            $("#address").val(data[0].address);
            $("#kode_area option:selected").val(data[0].ppp.kode_area).text(data[0].ppp
              .kode_area);
            $("#kode_odp option:selected").val(data[0].ppp.kode_odp).text(data[0].ppp
              .kode_odp);

          }
        });

        $.ajax({
          url: billingUrl + `/member/getPpp/${ppp_id}`,
          type: "GET",
          data: {
            pppoe_id: ppp_id,
            pppoe_username: ppp_username,
          },
          success: function(data) {
            if (data.ppp.length === 0) {
              Swal.fire({
                icon: 'error',
                title: 'Maaf!',
                text: 'Ticket PSB masih berstatus PENDING. Silakan hubungi Admin / Helpdesk',
                showConfirmButton: true,
                // timer: 1500
              });
            } else {
              $("#username").html(data.ppp[0].username);
              $("#password").html(data.ppp[0].value);
              $("#profile_inet").html(data.ppp[0].profile);
              if (data.session !== null && data.session.status === 1 &&
                data.session.ip !== null && data.ppp[0].status === 1) {
                $('#status').html(
                  '<span class="text-success">online</span>');
                $('#fill_ip').html('<span>' + data.session.ip + '</span>');
              } else if (data.session !== null && data.session.status ===
                1 &&
                data.session.ip !== null && data.ppp[0].status === 2) {
                $('#status').html(
                  '<span class="text-warning">isolir</span>');
                $('#fill_ip').html('<span>' + data.session.ip + '</span>');
              } else if (data.session !== null && data.session.status ===
                2 && data.session.ip !== null) {
                $('#status').html(
                  '<span class="text-danger">offline</span>');
                $('#fill_ip').html('-');
              } else {
                $('#status').html(
                  '<span class="text-danger">offline</span>');
                $('#fill_ip').html('-');
              }
              var created = data.ppp[0].created_at;
              var created_at = moment(created).local().format('DD/MM/YYYY HH:mm:ss');
              $("#created").html(created_at);
              showData.show();
            }
          }
        });

      } else {

      }
    });

    $('#myTable').on('click', '#create_secret', function() {
      let psb = $(this).data('id');
      if (psb) {
        $.ajax({
          url: baseUrl + `/${psb}`,
          type: "GET",
          success: function(data) {
            $("#id").val(data.data.id);
            $("#id_psb").html(data.data.id_psb);
            $("#nama_psb").html(data.data.nama_lengkap);
            $("#profile_add option:selected").val(data.data.paket_id).text(data.data.paket);
            $('#full_name_show').val(data.data.rmember.full_name);
            $('#wa_show').val(data.data.rmember.wa);
            $('#address_show').val(data.data.rmember.address);

            var profile_id = data.data.paket_id;
            $.ajax({
              url: serviceUrl + `/data/${profile_id}/price`,
              type: "GET",
              cache: false,
              success: function(data) {
                var amount = data.price;
                rp_amount = formatRupiah(amount, 2, ',', '.');
                $('#amount').val(rp_amount);
              }
            });
          }
        });
      } else {

      }
      createSecret.show();
    });

    $('#store_secret').click(function(e) {
      e.preventDefault();
      $('#store_secret').attr("disabled", true).html(
        'Submit &nbsp<i class="fa fa-refresh fa-spin" id="spinner"></i>');
      $('.alert.text-sm').remove();

      let data = {
        'id': $('#id').val(),
        'username': $('#username_add').val(),
        'password': $('#password_add').val(),
        'profile': $('#profile_add option:selected').text(),
        'nas': $('#nas_add').val(),
        'kode_area_add': $('#kode_area_add').val(),
        'kode_odp_add': $('#kode_odp_add').val(),
        'lock_mac': $('#lock_mac').val(),
        'mac': $('#mac').val(),
        'profile_id': $('#profile_add option:selected').val(),
        'payment_type': $('#payment_type').val(),
        'payment_status': $('#payment_status').val(),
        'billing_period': $('#billing_period').val(),
        'reg_date': $('#reg_date').val(),
        'ppn': $('#ppn').val(),
        'discount': $('#discount').val(),
        'amount': $('#amount').val(),
        'payment_total': $('#payment_total').val(),
      };

      $.ajax({
        url: baseUrl + `/secret`,
        type: "POST",
        data: data,
        dataType: "json",
        success: function(res) {
          $('#store_secret').attr("disabled", false).html('Submit');
          if (res.success) {
            Swal.fire({
              icon: 'success',
              title: 'Success',
              text: `${res.message}`,
              showConfirmButton: false,
              timer: 1500
            });
            setTimeout(function() {
              location.reload();
            }, 1500);
          } else {
            $.each(res.error || {}, function(key, value) {
              var el = $(document).find('[name="' + key + '"]');
              el.after($('<span class="alert text-sm text-danger">' + value[0] + '</div>'));
            });
          }
        },
        error: function(err) {
          $("#message").html("Some Error Occurred!");
          $('#store_secret').attr("disabled", false).html('Submit');
        }
      });
    });

    $("#create_secret").on("hidden.bs.modal", function() {
      $('#store_secret').attr("disabled", false);
      $("#spinner").remove();
    });

    $('#myTable').on('click', '#confirm', function() {
      let psb = $(this).data('id');
      if (psb) {
        $.ajax({
          url: baseUrl + `/${psb}`,
          type: "GET",
          success: function(data) {
            var psb_id = data.data.id;
            var id_psb = data.data.id_psb;
            var full_name = data.data.rmember.full_name;
            var alamat = data.data.rmember.alamat;
            var pppoe_id = data.data.pppoe_id;
            var member_id = data.data.member_id;

            Swal.fire({
              title: "Konfirmasi Pemasangan",
              icon: 'warning',
              text: "" + id_psb + " a.n " +
                full_name + "",
              showCancelButton: !0,
              confirmButtonText: "Ya, Terpasang",
              cancelButtonText: "Close",
              reverseButtons: !0,
            }).then(function(result) {
              if (result.isConfirmed) {
                let id = psb_id;
                var data = {
                  'full_name': full_name,
                  'alamat': alamat,
                  'pppoe_id': pppoe_id,
                  'member_id': member_id
                };

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
                          table.ajax.reload()
                        });
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

    $('#profile_add').on('change', function() {
      let profile_id = $(this).val();
      if (profile_id) {
        $.ajax({
          url: serviceUrl + `/data/${profile_id}/price`,
          type: "GET",
          success: function(data) {
            let amount = data.price;
            setInvoiceAmounts(amount);
          }
        });
      } else {
        // If no profile selected, clear amounts
        $('#amount').val('');
        $('#payment_total').val('');
      }
    });

    $('#ppn,#discount').on('keyup change', function() {
      let amountStr = $('#amount').val().replace(/\./g, '');
      if (!amountStr) return;
      let amount = parseInt(amountStr);
      recalculateTotal(amount);
    });

    // multi select on modal create
    $('#kode_area_add').on('change', function() {
      let kode_area = $(this).val();
      if (kode_area) {
        $.ajax({
          url: serviceUrl + `/data/${kode_area}/kode-odp`,
          type: 'GET',
          data: {
            kode_area_id: kode_area,
          },
          dataType: "json",
          success: function(data) {
            if (data) {
              $('#kode_odp_add').empty().append('<option value="">Pilih Kode ODP</option>');
              $('#kode_odp_add').prop('disabled', false);
              $.each(data.odp, function(key, value) {
                $("#kode_odp_add").append('<option value="' + value.kode_odp + '">' + value.kode_odp +
                  '</option>');
              });
            } else {
              $('#kode_odp_add').empty().prop('disabled', true);
            }
          }
        });
      } else {
        $('#kode_odp_add').empty().prop('disabled', true);
      }
    });

    $('#lock_mac').change(function() {
      let lock_mac = $(this).val();
      if (lock_mac == '1') {
        $('#show_mac').show()
      } else {
        $('#show_mac').hide()
      }
    });

    $('#payment_type').change(function() {
      let payment_type = $(this).val();
      if (payment_type === 'Prabayar') {
        $('#show_payment_status').show();
        // Fixed Date only allowed for now, Billing Cycle disabled
        $('#billing_period').html(
          "<option value='Fixed Date'>Fixed Date</option><option value='Billing Cycle' disabled>Billing Cycle</option>"
        );
      } else {
        $('#show_payment_status').hide();
        $('#billing_period').html(
          "<option value='Fixed Date'>Fixed Date</option><option value='Billing Cycle'>Billing Cycle</option>");
      }
    });

    $('#kode_area_add').select2({
      allowClear: true,
      dropdownParent: $("#create_secret"),
      width: '100%',
      placeholder: 'Pilih Kode Area',
    });

    $('#kode_odp_add').select2({
      allowClear: true,
      dropdownParent: $("#create_secret"),
      width: '100%',
      placeholder: 'Pilih Kode ODP',
    });

    $('#create_secret').on('shown.bs.modal', function() {
      document.getElementById('reg_date').valueAsDate = new Date();
    });

    function kapital() {
      let x = document.getElementById("full_name_add");
      let y = document.getElementById("address_add");
      let z = document.getElementById("nama_lengkap");
      let xy = document.getElementById("alamat");
      x.value = x.value.toUpperCase();
      y.value = y.value.toUpperCase();
      z.value = z.value.toUpperCase();
      xy.value = xy.value.toUpperCase();
    }

    function nonkapital() {
      let x = document.getElementById("username_add");
      let y = document.getElementById("password_add");
      x.value = x.value.toLowerCase();
      y.value = y.value.toLowerCase();
    }

    /* Fungsi */
    function setInvoiceAmounts(amount) {
      let rp_amount = formatRupiah(amount.toString());
      $('#amount').val(rp_amount);
      $('#payment_total').val(rp_amount);
      recalculateTotal(amount);
    }

    function recalculateTotal(amount) {
      let ppn = parseInt($('#ppn').val()) || 0;
      let discount = parseInt($('#discount').val()) || 0;
      let amount_ppn = ppn ? amount * ppn / 100 : 0;
      let amount_discount = discount ? amount * discount / 100 : 0;
      let total = amount + amount_ppn - amount_discount;
      let rp_total = formatRupiah(total.toString());
      console.log(ppn, discount, amount, amount_ppn, amount_discount, total, rp_total);
      $('#payment_total').val(rp_total);
    }

    function formatRupiah(angka) {
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
      return rupiah;
    }
  </script>
@endsection
