@extends('backend.layouts.app')

@section('title', 'Revenue Report Management')

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
          <h3>Revenue Management</h3>
        </div>
        <div class="col-sm-6 pe-0">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">
                <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item">Reports</li>
            <li class="breadcrumb-item active">Revenue Management</li>
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
                    <p>Revenue</p>

                    <div class="d-flex justify-content-center gap-2">
                      <button class="btn btn-primary" type="button" data-bs-toggle="modal"
                        data-bs-target="#create_income">
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
                        <p>Total Records</p>
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
            <h4>Total Records by Method</h4>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-12">
                <div class="total-num counter">
                  <div class="d-flex by-role custom-scrollbar">
                    <div>
                      <div class="total-user bg-light-primary">
                        <h5>Cash</h5>
                        <span class="total-num counter">{{ $totalCash }}</span>
                      </div>
                    </div>
                    <div>
                      <div class="total-user bg-light-primary">
                        <h5>Transfer</h5>
                        <span class="total-num counter">{{ $totalTransfer }}</span>
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
              <table id="myTable" class="table-hover display nowrap table" width="100%">
                <thead>
                  <tr>
                    <th style="text-align:left!important">NO</th>
                    <th>TANGGAL</th>
                    <th>KATEGORI</th>
                    <th>DESKRIPSI</th>
                    <th style="text-align:left!important">TOTAL</th>
                    <th>METODE</th>
                    <th>ADMIN</th>
                    <th>AKSI</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>

              @include('reports.revenue.modal.create')
              @include('reports.revenue.modal.edit')
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
        {
          data: 'tanggal',
          name: 'tanggal',
          render: function(data, type, row, meta) {
            return moment(data).local().format('DD/MM/YYYY HH:mm:ss');
          },
        },
        {
          data: 'item',
          name: 'item',
        },
        {
          data: 'deskripsi',
          name: 'deskripsi',
        },
        {
          data: 'price',
          name: 'price',
          render: $.fn.dataTable.render.number('.', ',', 0, ''),
        },
        {
          data: 'payment_method',
          name: 'payment_method',
          render: function(data) {
            if (data === 1) {
              return '<span>Cash</span>'
            } else {
              return '<span>Transfer</span>'
            }

          },
        },

        {
          data: 'admin',
          name: 'admin',
        },
        {
          data: 'action',
          name: 'action',
        },
      ]
    });

    // action create
    $('#store_income').click(function(e) {
      e.preventDefault();
      var error_ele = document.getElementsByClassName('alert text-sm');
      if (error_ele.length > 0) {
        for (var i = error_ele.length - 1; i >= 0; i--) {
          error_ele[i].remove();
        }
      }
      let transaction_id = $('#transaction_id').val();

      // collect data by id
      var data = {
        'tanggal': $('#tanggal_ci').val(),
        'type': $('#type_ci').val(),
        'category': $('#category_ci').val(),
        'item': $('#category_ci').val(),
        'deskripsi': $('#deskripsi_ci').val(),
        'price': $('#price_ci').val(),
        'payment_method': $('#payment_method_ci').val(),
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
              $('#create_income').modal('hide');
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

    // action create
    $('#update').click(function(e) {
      e.preventDefault();
      var error_ele = document.getElementsByClassName('alert text-sm');
      if (error_ele.length > 0) {
        for (var i = error_ele.length - 1; i >= 0; i--) {
          error_ele[i].remove();
        }
      }
      let transaction_id = $('#transaction_id').val();

      // collect data by id
      var data = {
        'tanggal': $('#tanggal').val(),
        'type': $('#type_ci').val(),
        'category': $('#category').val(),
        'item': $('#category').val(),
        'deskripsi': $('#deskripsi').val(),
        'price': $('#price').val(),
        'payment_method': $('#payment_method').val(),
      }


      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      // ajax proses
      $.ajax({
        url: baseUrl.replace('revenue', 'transaction') + `/${transaction_id}`,
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
              $('#edit').modal('hide');
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


    $('#myTable').on('click', '#btn-edit-income', function() {
      let transaction = $(this).data('id');
      if (transaction) {
        $.ajax({
          url: baseUrl.replace('revenue', 'transaction') + `/${transaction}`,
          type: "GET",
          success: function(data) {
            $("#transaction_id").val(data.data.id);
            $("#tanggal").val(data.data.tanggal);
            if (data.data.type === 1 || data.data.type === 2 || data.data.type === 3) {
              $("#type option:selected").val(data.data.type).text('Pemasukan');
            } else {
              $("#type option:selected").val(data.data.type).text('Pengeluaran');
            }
            $("#category").val(data.data.category);
            $("#deskripsi").val(data.data.deskripsi);
            var total = data.data.price;
            rp_total = formatRupiah(total, 2, ',', '.');
            $('#price').val(rp_total);
            if (data.data.payment_method === 1) {
              $("#payment_method").html(
                "<option value='1'>Cash</option><option value='2'>Transfer</option>"
              );
            } else {
              $("#payment_method").html(
                "<option value='2'>Transfer</option><option value='1'>Cash</option>"
              );
            }

          }
        });
      } else {

      }
      $('#edit').modal('show');
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
                timer: 1500,
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

    $('#create_income').on('shown.bs.modal', function(e) {
      document.getElementById('tanggal_ci').valueAsDate = new Date();
    })
    // document.getElementById('tanggal_ci').valueAsDate = new Date();

    /* Tanpa Rupiah */
    var price = document.getElementById('price');
    price.addEventListener('keyup', function(e) {
      price.value = formatRupiah(this.value);
    });

    /* Tanpa Rupiah */
    var price_ci = document.getElementById('price_ci');
    price_ci.addEventListener('keyup', function(e) {
      price_ci.value = formatRupiah(this.value);
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
