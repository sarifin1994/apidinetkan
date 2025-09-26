@extends('backend.layouts.app')

@section('title', 'Billing Member Management')

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
          <h3>Billing Member Management</h3>
        </div>
        <div class="col-sm-6 pe-0">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">
                <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item">Billing</li>
            <li class="breadcrumb-item active">Member Management</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <div class="container-fluid user-management-page">
    <div class="row">
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
                        {{ $total }}</h2>
                        <p>Total Member</p>
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
            <h4>Total Member by Status</h4>
          </div>
          <div class="card-body">
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
                      <div class="total-user bg-light-primary">
                        <h5>Suspended</h5>
                        <span class="total-num counter">{{ $totalSuspend }}</span>
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
            <div class="table-responsive custom-scrollbar">
              <table id="myTable" class="table-hover display nowrap clickable table" width="100%">
                <thead>
                  <tr>
                    <th style="text-align:left!important">NO</th>
                    <th style="text-align:left!important">ID PELANGGAN</th>
                    <th>NAMA LENGKAP</th>
                    <th>AREA</th>
                    <th>TGL AKTIF</th>
                    <th>JTH TEMPO</th>
                    <th>TIPE</th>
                    <th>SIKLUS</th>
                    <th>NEXT INV</th>
                    <th>INVOICE</th>
                    <th>STATUS</th>
                    <th>CREATED</th>
                    <th>AKSI</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>

              @include('billing.members.modal.show_ppp')
              @include('billing.members.modal.show_payment')
              @include('billing.members.modal.show_invoice')
              @include('billing.members.modal.show_contact')
              @include('billing.members.modal.create')
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
    const show_ppp = new bootstrap.Modal(document.getElementById('show_ppp'), {
      keyboard: false
    });
    const show_payment = new bootstrap.Modal(document.getElementById('show_payment'), {
      keyboard: false
    });
    const show_invoice = new bootstrap.Modal(document.getElementById('show_invoice'), {
      keyboard: false
    });
    const show_contact = new bootstrap.Modal(document.getElementById('show_contact'), {
      keyboard: false
    });

    let table = $('#myTable').DataTable({
      processing: true,
      serverSide: true,
      // scrollX: true,
      order: [
        [12, 'desc']
      ],
      lengthMenu: [10, 25, 50, 100, 200, 500, 1000, 2000],
      ajax: '{{ url()->current() }}',
      columns: [{
          data: null,
          'sortable': false,
          render: function(data, type, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
          }
        },
        {
          data: 'id_member',
          name: 'id_member',
          render: function(data, type, row) {
            return `<a href="javascript:void(0)" class="btn btn-sm btn-primary" id="show_ppp"
              data-username="${row.ppp.username}"
              data-id="${row.ppp.id}"
              data-name="${row.full_name}">
              <i class="fas fa-info-circle"></i> ${data}
            </a>`;
          },
        },
        {
          data: 'full_name',
          name: 'full_name'
        },
        {
          data: 'kode_area',
          name: 'kode_area',
        },
        {
          data: 'reg_date',
          name: 'reg_date',
          render: function(data, type, row, meta) {
            return moment(data).local().format('DD/MM/YYYY');
          },
        },
        {
          data: 'next_due',
          name: 'next_due',
          render: function(data, type, row, meta) {
            return moment(data).local().format('DD/MM/YYYY');
          },
        },

        {
          data: 'payment_type',
          name: 'payment_type',
          className: "text-uppercase",
          render: function(data, type, row) {
            return `<a href="javascript:void(0)" class="btn btn-sm btn-info" id="show_payment"
              data-id="${row.id}">
              <i class="fas fa-wallet"></i> ${data}
            </a>`;
          },
        },
        {
          data: 'billing_period',
          name: 'billing_period',
          className: "text-uppercase",
        },
        {
          data: 'next_invoice',
          name: 'next_invoice',
          render: function(data, type, row, meta) {
            return moment(data).local().format('DD/MM/YYYY');
          },
        },
        {
          data: 'invoice',
          name: 'invoice',
          sortable: false,
          searchable: false,
          render: function(data, type, row) {
            return `
                <a href="javascript:void(0)"
                    class="btn btn-sm btn-success"
                    id="show_invoice"
                    data-id="${row.id}"
                    title="Lihat Invoice">
                    <i class="fas fa-file-invoice"></i> Invoice
                </a>`;
          },
        },
        {
          data: 'ppp_status',
          name: 'ppp_status',
          sortable: false,
          searchable: false,
          render: function(data) {
            if (data === 1) {
              return '<span class="badge badge-xs bg-success text-light">active</span>'
            } else if (data === 0) {
              return '<span class="badge badge-xs bg-danger text-light">off</span>'
            } else if (data === 2) {
              return '<span class="badge badge-xs bg-warning text-light">suspend</span>'
            } else {
              return data;
            }
          },
        },
        {
          data: 'created_at',
          name: 'created_at',
          visible: false,
        },
        {
          data: 'contact',
          name: 'contact',
          sortable: false,
          searchable: false,
          render: function(data, type, row) {
            return `
                <a href="javascript:void(0)"
                    class="btn btn-sm btn-secondary"
                    id="show_contact"
                    data-id="${row.id}"
                    title="Edit Member">
                    <i class="fas fa-user-edit"></i> Edit
                </a>`;
          },
        },
      ]
    });

    $('#myTable').on('click', '#show_payment', function() {
      let member_id = $(this).data('id');
      if (member_id) {
        $.ajax({
          url: baseUrl + `/getPayment/${member_id}`,
          type: "GET",
          data: {
            member_id: member_id,
          },
          success: function(data) {
            $("#member_id").val(data[0].id);
            $("#payment_full_name").html(data[0].full_name);
            $("#payment_full_name_edit").val(data[0].full_name);
            $("#payment_type option:selected").text(data[0].payment_type).val(data[0]
              .payment_type);
            $("#billing_period option:selected").text(data[0].billing_period).val(data[0]
              .billing_period);
            $("#reg_date").val(data[0].reg_date);
            $("#next_due").val(data[0].next_due);
            $("#profile option:selected").text(data[0].profile.name).val(data[0].profile
              .name);

            var amount = data[0].profile.price;
            rp_amount = formatRupiah(amount, 2, ',', '.');
            $('#amount').val(rp_amount);
            rp_total_amount = formatRupiah(amount, 2, ',', '.');
            $('#ppn').val(data[0].ppn);
            $('#discount').val(data[0].discount);
            // $('#payment_total').val(rp_total_amount);

            var ppn = $('#ppn').val();
            var discount = $('#discount').val();
            var amount_ppn = amount * ppn / 100;
            var amount_discount = amount * discount / 100;

            if (discount === null) {
              var total_with_ppn = parseInt(amount) + parseInt(amount_ppn);
              var total_plus_ppn = total_with_ppn.toString();
              rp_total_plus_ppn = formatRupiah(total_plus_ppn, 2, ',', '.');
              $('#payment_total').val(rp_total_plus_ppn);
            } else if (ppn === null) {
              var total_with_discount = parseInt(amount) - parseInt(
                amount_discount);
              var total_plus_discount = total_with_discount.toString();
              rp_total_plus_discount = formatRupiah(total_plus_discount, 2,
                ',', '.');
              $('#payment_total').val(rp_total_plus_discount);
            } else {
              var total_with_ppn_discount = parseInt(amount) + parseInt(
                amount_ppn) - parseInt(amount_discount);
              var total_plus_ppn_discount = total_with_ppn_discount
                .toString();
              rp_total_plus_ppn_discount = formatRupiah(
                total_plus_ppn_discount,
                2, ',', '.');
              $('#payment_total').val(rp_total_plus_ppn_discount);
            }
          }
        });
      } else {

      }

      show_payment.show();
    });


    $("#ppn,#discount").on("keyup change", function() {
      var amount = $('#amount').val();
      var ppn = $('#ppn').val();
      var discount = $('#discount').val();

      amount = parseInt(amount.replace(/\./g, '')) || 0;
      ppn = parseInt(ppn) || 0;
      discount = parseInt(discount) || 0;

      if (!amount) {
        return;
      }

      var amount_ppn = ppn ? amount * (ppn / 100) : 0;
      var amount_discount = discount ? amount * (discount / 100) : 0;

      var total_with_ppn_discount = parseInt(amount) + parseInt(amount_ppn) - parseInt(amount_discount);
      var total_plus_ppn_discount = total_with_ppn_discount.toString();
      rp_total_plus_ppn_discount = formatRupiah(total_plus_ppn_discount, 2, ',', '.');
      $('#payment_total').val(rp_total_plus_ppn_discount);
    });

    // action create
    $('#editPayment').click(function(e) {
      e.preventDefault();
      var error_ele = document.getElementsByClassName('alert text-sm');
      if (error_ele.length > 0) {
        for (var i = error_ele.length - 1; i >= 0; i--) {
          error_ele[i].remove();
        }
      }
      let member_id = $('#member_id').val();

      // collect data by id
      var data = {
        'next_due': $('#next_due').val(),
        'ppn': $('#ppn').val(),
        'discount': $('#discount').val(),
        'billing_period': $('#billing_period').val(),
        'full_name': $('#payment_full_name_edit').val(),
      }


      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      // ajax proses
      $.ajax({
        url: baseUrl + `/updatePayment/${member_id}`,
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
              show_payment.hide();
            });
          } else {
            $.each(data.error, function(key, value) {
              var el = $(document).find('[name="' + key + '"]');
              el.after($('<span class= "alert text-sm text-danger">' + value[0] +
                '</div>'));
            });
          }
        },

        error: function(err) {
          $("#message").html("Some Error Occurred!")
        }

      });
    });

    $('#editContact').click(function(e) {
      e.preventDefault();
      var error_ele = document.getElementsByClassName('alert text-sm');
      if (error_ele.length > 0) {
        for (var i = error_ele.length - 1; i >= 0; i--) {
          error_ele[i].remove();
        }
      }
      let member_id = $('#member_idc').val();

      // collect data by id
      var data = {
        'pppoe_id': $('#pppoe_idc').val(),
        'id_member': $('#id_member').val(),
        'full_name': $('#full_name').val(),
        'email': $('#email').val(),
        'wa': $('#wa').val(),
        'address': $('#address').val(),
      }


      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      // ajax proses
      $.ajax({
        url: baseUrl + `/updateContact/${member_id}`,
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
              show_contact.hide();
            });
          } else {
            $.each(data.error, function(key, value) {
              var el = $(document).find('[name="' + key + '"]');
              el.after($('<span class= "alert text-sm text-danger">' + value[0] +
                '</div>'));
            });
          }
        },

        error: function(err) {
          $("#message").html("Some Error Occurred!")
        }

      });
    });

    $('#myTable').on('click', '#show_ppp', function() {
      let ppp_id = $(this).data('id');
      let ppp_username = $(this).data('username');
      var full_name = $(this).data('name');
      $("#ppp_full_name").html(full_name);
      if (ppp_id) {
        $.ajax({
          url: baseUrl + `/getPpp/${ppp_id}`,
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
                '<span class="badge bg-success text-light">online</span>');
              $('#fill_ip').html('<span>' + data.session.ip + '</span>');
            } else if (data.session !== null && data.session.status ===
              1 &&
              data.session.ip !== null && data.ppp[0].status === 2) {
              $('#status').html(
                '<span class="badge bg-warning text-light">isolir</span>');
              $('#fill_ip').html('<span>' + data.session.ip + '</span>');
            } else if (data.session !== null && data.session.status ===
              2 && data.session.ip !== null) {
              $('#status').html(
                '<span class="badge bg-danger text-light">offline</span>');
              $('#fill_ip').html('-');
            } else {
              $('#status').html(
                '<span class="badge bg-danger text-light">offline</span>');
              $('#fill_ip').html('-');
            }
            var created = data.ppp[0].created_at;
            var created_at = moment(created).local().format('DD/MM/YYYY HH:mm:ss');
            $("#created").html(created_at);
          }
        });
      } else {

      }
      show_ppp.show();
    });

    $('#myTable').on('click', '#show_invoice', function() {
      let member_id = $(this).data('id');
      if (member_id) {
        $.ajax({
          url: baseUrl + `/getListInvoice/${member_id}`,
          type: 'GET',
          data: {
            member_id: member_id
          },
          success: function(response) {
            $("#inv_full_name").html(response[0].member.full_name);
            $('#invoiceTable').DataTable({
              data: response,
              // scrollX: true,
              pageLength: 6,
              lengthMenu: [
                [6, 12, 24],
                [6, 12, 24],
              ],
              destroy: true,
              columns: [{
                  data: null,
                  'sortable': false,
                  render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart +
                      1;
                  }
                }, {
                  data: 'invoice_date',
                  render: function(data, type, row, meta) {
                    return moment(data).local().format(
                      'DD/MM/YYYY');
                  },
                },
                {
                  data: 'no_invoice',
                },
                {
                  data: 'payment_type',
                  className: 'text-uppercase'
                },
                {
                  data: 'period',
                  render: function(data, type, row, meta) {
                    return moment(data).local().format(
                      'DD/MM/YYYY');
                  },
                },
                {
                  data: 'price',
                  render: $.fn.dataTable.render.number('.', ',', 0, ''),
                },
                {
                  data: 'status',
                  render: function(data) {
                    if (data === 0) {
                      return '<span class="badge badge-sm bg-danger">UNPAID</span>'
                    } else {
                      return '<span class="badge bg-success">PAID</span>'
                    }
                  },
                },
                {
                  data: 'paid_date',
                  render: function(data, type, row, meta) {
                    if (data === null) {
                      return ''
                    } else {
                      return moment(data).local().format(
                        'DD/MM/YYYY');
                    }
                  },
                },
              ]
            });

            show_invoice.show();
          },
        });
      } else {};
    });

    $('#myTable').on('click', '#show_contact', function() {
      let member_id = $(this).data('id');
      if (member_id) {
        $.ajax({
          url: baseUrl + `/getContact/${member_id}`,
          type: "GET",
          data: {
            member_id: member_id,
          },
          success: function(data) {

            $("#member_idc").val(data[0].id);
            $("#full_name").val(data[0].full_name);
            $("#id_member").val(data[0].id_member);
            $("#email").val(data[0].email);
            $("#wa").val(data[0].wa);
            $("#address").val(data[0].address);
            $("#pppoe_idc").val(data[0].ppp.id).text(data[0].ppp
              .id);
            // $("#kode_area option:selected").val(data[0].ppp.kode_area).text(data[0].ppp
            //     .kode_area);
            // $("#kode_odp option:selected").val(data[0].ppp.kode_odp).text(data[0].ppp
            //     .kode_odp);

          }
        });
      } else {

      }
      show_contact.show();
    });

    $('#lock_mac').change(function() {
      let lock_mac = $(this).val(); //Get selected option value
      if (lock_mac == '1') {
        $('#show_mac').show()
      } else {
        $('#show_mac').hide()
      }
    });

    $("#option_member").click(function() {
      if ($("#option_member").prop("checked")) {
        $("#option_member").val(1);
        $("#show_member").show();
      } else {
        $("#option_member").val(0);
        $("#show_member").hide();
      }
    });




    $('#kode_area').select2({
      theme: "bootstrap-5",
      allowClear: true,
      dropdownParent: $("#create .modal-content"),
      width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
      placeholder: $(this).data('placeholder'),
    });
    $('#kode_area_edit').select2({
      theme: "bootstrap-5",
      allowClear: true,
      dropdownParent: $("#edit .modal-content"),
      width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
      placeholder: $(this).data('placeholder'),
    });
    $('#kode_odp').select2({
      theme: "bootstrap-5",
      allowClear: true,
      dropdownParent: $("#create .modal-content"),
      width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
      placeholder: $(this).data('placeholder'),
    });
    $('#kode_odp_edit').select2({
      theme: "bootstrap-5",
      allowClear: true,
      dropdownParent: $("#edit .modal-content"),
      width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
      placeholder: $(this).data('placeholder'),
    });

    function kapital() {
      let x = document.getElementById("full_name");
      let y = document.getElementById("address");
      x.value = x.value.toUpperCase();
      y.value = y.value.toUpperCase();
    }

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
