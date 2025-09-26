@extends('backend.layouts.app')

@section('title', 'Unpaid Management')

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
          <h3>Unpaid Management</h3>
        </div>
        <div class="col-sm-6 pe-0">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">
                <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item">Billing</li>
            <li class="breadcrumb-item active">Unpaid Management</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <div class="container-fluid user-management-page">
    @if (auth()->user()->role === 'Admin' || auth()->user()->role === 'Kasir')
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
                      <p>Invoice</p>

                      <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-primary" type="button" data-bs-toggle="modal"
                          data-bs-target="#create_invoice">
                          <i class="fa fa-plus"></i>
                          {{ __('Manual Invoice') }}
                        </button>
                        <span id="export"></span>
                        <button class="btn btn-success" id="export">
                          <i class="fa fa-print"></i>
                          {{ __('Export') }}
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endif

    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive custom-scrollbar">
              <table id="myTable" class="table-hover display nowrap table" width="100%">
                <thead>
                  <tr>
                    <th style="text-align:left!important">NO</th>
                    <th style="text-align:left!important">NO INVOICE</th>
                    <th>NAMA LENGKAP</th>
                    <th>WHATSAPP</th>
                    <th>AREA</th>
                    <th>ALAMAT</th>
                    <th>TGL INVOICE</th>
                    <th>JTH TEMPO</th>
                    <th>ITEM</th>
                    <th>PERIODE LANGGANAN</th>
                    <th>TOTAL</th>
                    <th>AKSI</th>
                    <th>STATUS</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @include('billing.unpaid.modal.show_invoice')
  @include('billing.unpaid.modal.create_invoice')
@endsection

@section('scripts')
  <script src="{{ asset('assets/radiusqu/dist/js/moment.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/datatable.custom.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatable-extension/dataTables.buttons.min.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatable-extension/buttons.html5.min.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatable-extension/buttons.print.min.js') }}"></script>

  <script type="text/javascript">
    const showInvoice = new bootstrap.Modal(document.getElementById('show_invoice'), {
      keyboard: false
    });

    let table = $('#myTable').DataTable({
      processing: true,
      serverSide: true,
      // scrollX: true,
      lengthMenu: [10, 25, 50, 100, 200, 500, 1000, 2000],
      ajax: '{{ url()->current() }}',
      dom: "lBfrtip",
      // buttons: ["copy", "csv", "excel", "pdf", "print"],
      // layout: {
      //     topStart: ['buttons', 'pageLength']
      // },

      buttons: [{
        text: '<i class="fas fa-file-export"></i>&nbspEXPORT',
        extend: 'excel',
        filename: 'unpaid-invoices',
        title: '',
        exportOptions: {
          columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 10],
          format: {
            body: function(data, row, column, node) {
              data = $('<p>' + data + '</p>').text();
              return $.isNumeric(data.replace('.', '')) ? data.replace('.', '') : data;
            }
          }
        },
      }],
      columns: [{
          data: null,
          'sortable': false,
          render: function(data, type, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
          }
        },
        {
          data: 'no_invoice',
          name: 'no_invoice',
          render: function(data, type, row) {
            return '<a href="javascript:void(0)" class="badge b-ln-height badge-primary show_invoice" style="text-decoration:none"  data-id=' +
              row.id +
              '>' + data + '</a>'
          },
        },
        {
          data: 'member.full_name',
          name: 'member.full_name',
        },
        {
          data: 'member.wa',
          name: 'member.wa',
          visible: false,
        },
        {
          data: 'member.kode_area',
          name: 'member.kode_area'
        },
        {
          data: 'member.address',
          name: 'member.address',
          visible: false,
        },
        {
          data: 'invoice_date',
          name: 'invoice_date',
          render: function(data, type, row, meta) {
            return moment(data).local().format('DD/MM/YYYY');
          },
        },
        {
          data: 'due_date',
          name: 'due_date',
          render: function(data, type, row, meta) {
            return moment(data).local().format('DD/MM/YYYY');
          },
        },
        {
          data: 'item',
          name: 'item',
          visible: false,
        },
        {
          data: 'subscribe',
          name: 'subscribe',
        },
        {
          data: 'price',
          name: 'price',
          render: $.fn.dataTable.render.number('.', ',', 0, ''),
        },
        {
          data: 'action',
          name: 'action',
          width: '90px',
        },
        {
          data: 'created_at',
          name: 'created_at',
          visible: false,
        },
      ]
    });
    $(
      ".buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel"
    ).addClass("btn btn-warning text-light me-2 mb-2");
    table.buttons().container().addClass("d-inline").appendTo('#export');

    /* custom button event print */
    $(document).on('click', '#export', function() {
      $(".buttons-print")[0].click();
    });

    // action create
    $('#editInvoice').click(function(e) {
      e.preventDefault();
      var error_ele = document.getElementsByClassName('alert text-sm');
      if (error_ele.length > 0) {
        for (var i = error_ele.length - 1; i >= 0; i--) {
          error_ele[i].remove();
        }
      }
      let invoice_id = $('#invoice_id').val();

      // collect data by id
      var data = {
        'item': $('#item').val(),
        'ppn': $('#ppn').val(),
        'discount': $('#discount').val(),
        'amount': $('#amount').val(),
      }


      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      // ajax proses
      $.ajax({
        url: baseUrl + `/updateInvoice/${invoice_id}`,
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
              showInvoice.hide();
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

    // dapatkan value kode area dari member_id
    $('#member_id').on('change', function() {
      let member_id = $(this).val();

      if (member_id) {
        $.ajax({
          url: `${baseUrl}/${member_id}/services`,
          type: 'GET',
          success: function(services) {
            $('#pppoe_member_id').empty().append('<option value=""></option>');
            services.forEach(svc => {
              let text = svc.pppoe ? svc.pppoe.username + ' (' + svc.profile.name + ')' : 'Service #' + svc
                .id;
              $('#pppoe_member_id').append('<option value="' + svc.id + '">' + text + '</option>');
            });
          }
        });
      } else {
        $('#pppoe_member_id').empty();
      }
    });

    $('#pppoe_member_id').on('change', function() {
      let pppoe_member_id = $(this).val();
      if (pppoe_member_id) {
        $.ajax({
          url: `${baseUrl}/${pppoe_member_id}/detail`,
          type: 'GET',
          success: function(data) {
            // data has PppoeMember details
            // fill fields
            $('#fill_area').val(data.kode_area);
            $('#fill_username').val(data.pppoe.username);

            // Payment logic
            let payment_type = data.payment_type;
            let billing_period = data.billing_period;
            $('#fill_payment_type').empty().append('<option>' + payment_type + '</option>');
            $('#fill_billing_period').empty().append('<option>' + billing_period + '</option>');

            // Retrieve price
            let price = data.profile.price;
            let ppn = data.ppn;
            let discount = data.discount;

            // Determine invoice_date, due_date, subscribe as per your business logic.
            // The following is simplified. You can replicate logic from your original code.
            // Assume we set today's date as invoice_date and next_due from service
            let today = moment().format('DD/MM/YYYY');
            let next_due = moment(new Date(data.next_due)).format('DD/MM/YYYY');
            let next_double_due = moment(new Date(data.next_due)).add(1, 'month').format('DD/MM/YYYY');
            $('#fill_invoice_date').val(new Date().toISOString().split('T')[0]);
            $('#fill_due_date').val(data.next_due);
            // subscribe can be prefilled based on your rules
            $('#fill_subscribe').val(`${next_due} - ${next_double_due}`);
            $('#fill_validity').empty().append('<option>1 Month</option>');

            // Set defaults
            $('#fill_item').val('Internet: ' + data.pppoe.username + ' | ' + data.profile.name);
            $('#fill_amount').val(formatRupiah(price.toString()));
            $('#fill_ppn').val(ppn);
            $('#fill_discount').val(discount);

            // Trigger recalculation
            recalculateTotal();
          }
        });
      }
    });

    // Recalculate total on ppn/discount change
    $('#fill_ppn,#fill_discount,#fill_amount').on('keyup change', function() {
      recalculateTotal();
    });

    function recalculateTotal() {
      let amount = $('#fill_amount').val().replace(/\./g, '');
      if (!amount) amount = 0;
      amount = parseInt(amount);
      let ppn = parseInt($('#fill_ppn').val()) || 0;
      let discount = parseInt($('#fill_discount').val()) || 0;

      let amount_ppn = amount * ppn / 100;
      let amount_discount = amount * discount / 100;
      let total = amount + amount_ppn - amount_discount;
      $('#fill_payment_total').val(formatRupiah(total.toString()));
    }

    $('#generate_invoice').click(function(e) {
      e.preventDefault();
      $('.alert.text-sm').remove();

      let data = {
        member_id: $('#fill_member_id').val(),
        id_member: $('#fill_id_member').val(),
        today: $('#fill_invoice_date').val(),
        payment_type: $('#fill_payment_type').val(),
        billing_period: $('#fill_billing_period').val(),
        next_due: $('#fill_due_date').val(),
        amount: $('#fill_amount').val(),
        ppn: $('#fill_ppn').val(),
        discount: $('#fill_discount').val(),
        username: $('#fill_username').val(),
        profile: $('#fill_profile').val(),
        subscribe: $('#fill_subscribe').val(),
        item: $('#fill_item').val(),
        full_name: $('#fill_full_name').val(),
        password: $('#fill_password').val(),
        wa: $('#fill_wa').val(),
        pppoe_member_id: $('#pppoe_member_id').val(),
      };

      $.ajax({
        url: baseUrl + '/generate',
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function(res) {
          if (res.success) {
            Swal.fire({
              icon: 'success',
              title: 'Success',
              text: res.message,
              showConfirmButton: false,
              timer: 1500
            });
            setTimeout(function() {
              location.reload();
            }, 1500);
          } else {
            $.each(res.error, function(key, value) {
              var el = $(document).find('[name="' + key + '"]');
              el.after('<span class="alert text-sm text-danger">' + value[0] + '</span>');
            });
          }
        },
        error: function(err) {
          console.error(err);
          $("#message").html("Some Error Occurred!");
        }
      });
    });


    $('#myTable').on('click', '.show_invoice', function() {
      let invoice_id = $(this).data('id');
      if (invoice_id) {
        $.ajax({
          url: baseUrl + `/getInvoice/${invoice_id}`,
          type: "GET",
          data: {
            invoice_id: invoice_id,
          },
          success: function(data) {
            $("#invoice_id").val(data.data.id);
            $("#full_name").val(data.data.member.full_name);
            $("#no_invoice").val(data.data.no_invoice);
            $("#invoice_date").val(data.data.invoice_date);
            $("#due_date").val(data.data.due_date);
            $("#subscribe").val(data.data.subscribe);
            $("#payment_type option:selected").text(data.data.payment_type).val(data
              .data
              .payment_type);
            $("#validity option:selected").text('1 Month');
            $("#billing_period option:selected").text(data.data.billing_period).val(data
              .data
              .billing_period);
            $("#item").val(data.data.item);

            var amount = data.data.price;

            rp_amount = formatRupiah(amount, 2, ',', '.');
            $('#amount').val(rp_amount);
            rp_total_amount = formatRupiah(amount, 2, ',', '.');
            $('#ppn').val(data.data.ppn);
            $('#discount').val(data.data.discount);
            $('#payment_total').val(rp_total_amount);

            $('#show_invoice').on('shown.bs.modal', function() {
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
                var total_plus_ppn_discount = total_with_ppn_discount
                  .toString();
                rp_total_plus_ppn_discount = formatRupiah(
                  total_plus_ppn_discount,
                  2, ',', '.');
                $('#payment_total').val(rp_total_plus_ppn_discount);
              }
            })


            $("#ppn,#discount").on("keyup change", function() {
              var jml = document.getElementById('amount').value;
              var amount = jml.replace(/\./g, "");
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
                var total_plus_ppn_discount = total_with_ppn_discount
                  .toString();
                rp_total_plus_ppn_discount = formatRupiah(
                  total_plus_ppn_discount,
                  2, ',', '.');
                $('#payment_total').val(rp_total_plus_ppn_discount);
              }
            });
          }
        });
      } else {

      }
      showInvoice.show();
    });

    $('#myTable').on('click', '#pay', function() {
      let invoice_id = $(this).data('id');
      let ppp_id = $(this).data('ppp');
      if (invoice_id) {
        $.ajax({
          url: baseUrl + `/getInvoice/${invoice_id}`,
          type: "GET",
          data: {
            invoice_id: invoice_id,
            ppp_id: ppp_id,
          },
          success: function(data) {
            var id = data.data.id;
            var full_name = data.data.member.full_name;
            var no_invoice = data.data.no_invoice;
            var invoice_date = data.data.invoice_date;
            var amount = data.data.price;
            var ppn = data.data.ppn;
            var discount = data.data.discount;
            var amount_ppn = amount * ppn / 100;
            var amount_discount = amount * discount / 100;
            var total_with_ppn_discount = parseInt(amount) + parseInt(
              amount_ppn) - parseInt(amount_discount);
            var total_plus_ppn_discount = total_with_ppn_discount
              .toString();

            var payment_type = data.data.payment_type;
            var billing_period = data.data.billing_period;
            var payment_url = data.data.payment_url;

            var subscribe = data.data.subscribe;
            var periode = data.data.period;
            var due_date = data.data.due_date;

            var member_id = data.data.member_id;
            var id_member = data.data.member.id_member;
            var nas = data.ppp.nas;
            var no_wa = data.data.member.wa;

            // collect data pppoe
            var ppp_id = data.ppp.id;
            var ppp_user = data.ppp.username;
            var ppp_pass = data.ppp.value;
            var ppp_profile = data.ppp.profile;
            var ppp_status = data.ppp.status;

            Swal.fire({
              title: "Konfirmasi Pembayaran",
              icon: 'warning',
              input: "select",
              inputOptions: {
                '1': 'Cash',
                '2': 'Transfer',
              },
              inputPlaceholder: 'Metode Pembayaran',
              text: "INV #" + no_invoice + " a.n " + full_name + "",
              showCancelButton: !0,
              confirmButtonText: "Ya, sudah bayar",
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
                    resolve('Harap pilih metode pembayaran');
                  }
                });
              }
            }).then(function(result) {
              if (result.isConfirmed) {
                let invoice_id = id;
                var dataku = {
                  'member_id': member_id,
                  'id_member': id_member,
                  'no_wa': no_wa,
                  'subscribe': subscribe,
                  'periode': periode,
                  'full_name': full_name,
                  'no_invoice': no_invoice,
                  'invoice_date': invoice_date,
                  'amount': amount,
                  'ppn': ppn,
                  'discount': discount,
                  'payment_total': total_plus_ppn_discount,
                  'payment_method': result.value,
                  'payment_type': payment_type,
                  'payment_url': payment_url,
                  'billing_period': billing_period,
                  'due_date': due_date,
                  'ppp_id': ppp_id,
                  'ppp_status': ppp_status,
                  'pppoe_user': ppp_user,
                  'pppoe_pass': ppp_pass,
                  'pppoe_profile': ppp_profile,
                  'nas': nas,
                }

                $.ajaxSetup({
                  headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                      .attr('content')
                  }
                });

                // ajax proses
                $.ajax({
                  url: baseUrl + `/payInvoice/${invoice_id}`,
                  type: "PUT",
                  cache: false,
                  data: dataku,
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
                      });
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
                        url: baseUrl + `/payInvoiceWA/${invoice_id}`,
                        type: "POST",
                        cache: false,
                        data: dataku,
                        dataType: "json",
                      });

                    } else {
                      $.each(data.error, function(key,
                        value) {
                        var el = $(document).find(
                          '[name="' + key +
                          '"]');
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
              }
            });
          }
        });
      } else {}

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
                timer: 1500
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

    function kapital() {
      let x = document.getElementById("full_name");
      let y = document.getElementById("address");
      x.value = x.value.toUpperCase();
      y.value = y.value.toUpperCase();
    }

    /* Tanpa Rupiah */
    var price = document.getElementById('amount');
    var total = document.getElementById('payment_total');
    var fill_price = document.getElementById('fill_amount');
    var fill_total = document.getElementById('fill_payment_total');
    price.addEventListener('keyup', function(e) {
      price.value = formatRupiah(this.value);
      total.value = formatRupiah(this.value);
    });
    fill_price.addEventListener('keyup', function(e) {
      fill_price.value = formatRupiah(this.value);
      fill_total.value = formatRupiah(this.value);
    });


    /* Fungsi */
    function formatRupiah(angka) {
      let number_string = angka.replace(/[^,\d]/g, '').toString(),
        split = number_string.split(','),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

      if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
      }

      rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
      return rupiah;
    }

    $('#member_id').select2({
      allowClear: true,
      dropdownParent: $("#create_invoice"),
      width: '100%',
      placeholder: $(this).data('placeholder'),
    });

    $('#pppoe_member_id').select2({
      allowClear: true,
      width: '100%',
      placeholder: 'Pilih Layanan',
    });
  </script>
@endsection
