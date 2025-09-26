@extends('backend.layouts.app')

@section('title', 'Billing Paid Management')

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
          <h3>Billing Paid Management</h3>
        </div>
        <div class="col-sm-6 pe-0">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">
                <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item">Billing</li>
            <li class="breadcrumb-item active">Paid Management</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <div class="container-fluid user-management-page">
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
                    <th>AREA</th>
                    <th>TGL INVOICE</th>
                    <th>TGL JATUH TEMPO</th>
                    <th>TGL BAYAR</th>
                    <th>TOTAL</th>
                    <th>AKSI</th>
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

  @include('billing.paid.modal.show_invoice')
@endsection

@section('scripts')
  <script src="{{ asset('assets/radiusqu/dist/js/moment.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/datatable.custom.js') }}"></script>

  <script type="text/javascript">
    const showInvoice = new bootstrap.Modal(document.getElementById('show_invoice'), {
      keyboard: false
    });

    const memberBillingUrl = baseUrl.clone().replace('paid', 'member');

    let table = $('#myTable').DataTable({
      processing: true,
      serverSide: true,
      // scrollX: true,
      order: [
        [9, 'desc']
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
          data: 'no_invoice',
          name: 'no_invoice',
          render: function(data, type, row) {
            return '<a href="javascript:void(0)" class="text-primary" style="text-decoration:none" id="show_invoice" data-id=' +
              row.id +
              '>' + data + '</a>'
          },
        },
        {
          data: 'member.full_name',
          name: 'member.full_name',
        },
        {
          data: 'member.kode_area',
          name: 'member.kode_area'
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
          data: 'paid_date',
          name: 'paid_date',
          render: function(data, type, row, meta) {
            return moment(data).local().format('DD/MM/YYYY');
          },
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

    $('#myTable').on('click', '#show_invoice', function() {
      let invoice_id = $(this).data('id');
      if (invoice_id) {
        $.ajax({
          url: memberBillingUrl + `/getInvoice/${invoice_id}`,
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

    $('#myTable').on('click', '#undopay', function() {
      let invoice_id = $(this).data('id');
      let ppp_id = $(this).data('ppp');
      if (invoice_id) {
        $.ajax({
          url: memberBillingUrl + `/getInvoice/${invoice_id}`,
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
            var next_due = data.data.member.next_due;


            // collect data pppoe
            var ppp_id = data.ppp.id;
            var ppp_user = data.ppp.username;
            var ppp_pass = data.ppp.value;
            var ppp_profile = data.ppp.profile;
            var ppp_status = data.ppp.status;

            Swal.fire({
              title: "Batalkan Pembayaran",
              icon: 'warning',
              text: "INV #" + no_invoice + " a.n " + full_name + "",
              showCancelButton: !0,
              confirmButtonText: "Ya, batalkan",
              cancelButtonText: "Tutup",
              reverseButtons: !0,
            }).then(function(result) {
              if (result.isConfirmed) {
                let invoice_id = id;
                var dataku = {
                  'member_id': member_id,
                  'id_member': id_member,
                  'no_wa': no_wa,
                  'subscribe': subscribe,
                  'full_name': full_name,
                  'no_invoice': no_invoice,
                  'periode': periode,
                  'invoice_date': invoice_date,
                  'amount': amount,
                  'ppn': ppn,
                  'discount': discount,
                  'payment_total': total_plus_ppn_discount,
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
                  'next_due': next_due,
                }


                $.ajaxSetup({
                  headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                      .attr('content')
                  }
                });

                // ajax proses
                $.ajax({
                  url: baseUrl + `/undopayInvoice/${invoice_id}`,
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
                        url: baseUrl + `/undopayInvoiceWA/${invoice_id}`,
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
