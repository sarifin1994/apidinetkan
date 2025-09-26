@extends('backend.layouts.app')

@section('title', 'Transaction Report Management')

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
          <h3>Transaction Management</h3>
        </div>
        <div class="col-sm-6 pe-0">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">
                <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item">Reports</li>
            <li class="breadcrumb-item active">Transaction Management</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <div class="container-fluid user-management-page">
    <div class="row project-dashboard">
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
                    <h3 class="total-num counter mb-0">{{ $total }}</h3>
                    <p>Total Transaction</p>

                    <div class="d-flex justify-content-center gap-2">
                      <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="fa fa-plus"></i>
                        {{ __('Create') }}
                      </button>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal"
                            data-bs-target="#create_income">Income</a></li>
                        <li><a class="dropdown-item" href="javascript:void(0)" data-bs-toggle="modal"
                            data-bs-target="#create_expense">Expense</a></li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-xxl-8 box-col-8">
        <div class="row">
          <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
            <div class="card total-sales">
              <div class="card-body">
                <div class="row">
                  <div class="col-xl-8 xl-12 col-md-8 col-sm-12 col box-col-12">
                    <div class="d-flex">
                      <span>
                        <i class="fa-solid fa-wallet"></i>
                      </span>
                      <div class="flex-shrink-0">
                        <h4 class="text-blue">{{ number_format($totalBalance, 0, '.', '.') }}</h4>
                        <h6>Balance</h6>
                        <small>This Month : {{ number_format($balanceMonth, 0, '.', '.') }}</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
            <div class="card total-sales">
              <div class="card-body">
                <div class="row">
                  <div class="col-xl-8 xl-12 col-md-8 col-sm-12 col box-col-12">
                    <div class="d-flex total-customer">
                      <span>
                        <svg>
                          <use href="{{ asset('assets/svg/icon-sprite.svg#Sales') }}"></use>
                        </svg>
                      </span>
                      <div class="flex-shrink-0">
                        <h4 class="text-success">{{ number_format($totalIncome, 0, '.', '.') }}</h4>
                        <h6>Revenue</h6>
                        <small>This Month : {{ number_format($incomeMonth, 0, '.', '.') }}</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
            <div class="card total-sales">
              <div class="card-body">
                <div class="row">
                  <div class="col-xl-8 xl-12 col-md-8 col-sm-12 col box-col-12">
                    <div class="d-flex up-sales">
                      <span>
                        <svg>
                          <use href="{{ asset('assets/svg/icon-sprite.svg#Product') }}"></use>
                        </svg>
                      </span>
                      <div class="flex-shrink-0">
                        <h4 class="text-orange">{{ number_format($totalExpense, 0, '.', '.') }}</h4>
                        <h6>Expense</h6>
                        <small>This Month : {{ number_format($expenseMonth, 0, '.', '.') }}</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
            <div class="card total-sales">
              <div class="card-body">
                <div class="row">
                  <div class="col-xl-8 xl-12 col-md-8 col-sm-12 col box-col-12">
                    <div class="d-flex total-product">
                      <span>
                        <i class="fa-solid fa-cubes"></i>
                      </span>
                      <div class="flex-shrink-0">
                        <h4>{{ number_format($totalTransactions, 0, ',', '') }}</h4>
                        <h6>Transaction</h6>
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
                  <input type="date" data-column="1" class="form-control daterange" id="mindate" name="mindate">
                </div>
              </div>
              <div class="col-lg-3">
                <div class="form-group mb-3">
                  <label for="maxdate" class="mb-1">SAMPAI TANGGAL</label>
                  <input type="date" data-column="1" class="form-control daterange" id="maxdate" name="maxdate">
                </div>
              </div>
              <div class="col-lg-3">
                <div class="form-group mb-3">
                  <label for="filter_type" class="mb-1">TIPE TRANSAKSI</label>
                  <select data-column="5" class="form-select" id="filter_type" name="filter_type">
                    <option value="">Filter Tipe</option>
                    <option value="1">Pemasukan</option>
                    <option value="2">Pengeluaran</option>
                  </select>
                </div>
              </div>
              <div class="col-lg-3">
                <div class="form-group mb-3">
                  <label for="filter_item" class="mb-1">KATEGORI</label>
                  <select data-column="2" class="form-select" id="filter_item" name="filter_item">
                    <option value="">Filter Kategori</option>

                    @foreach ($revenueCategories as $category)
                      <option value="{{ $category->value }}">{{ $category->label() }}</option>
                    @endforeach

                    @foreach ($expenseCategories as $category)
                      <option value="{{ $category->value }}">{{ $category->label() }}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>

            <div class="table-responsive custom-scrollbar">
              <table id="myTable" class="table-hover display nowrap table" width="100%">
                <thead>
                  <tr>
                    <th style="text-align:left!important">NO</th>
                    <th>TANGGAL</th>
                    <th>KATEGORI</th>
                    <th>DESKRIPSI</th>
                    <th style="text-align:left!important">TOTAL</th>
                    <th>TIPE</th>
                    <th>METODE</th>
                    <th>ADMIN</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>

              @include('reports.revenue.modal.create', ['categories' => $revenueCategories])
              @include('reports.expense.modal.create', ['categories' => $expenseCategories])
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
      dom: "lBfrtip",
      lengthMenu: [10, 25, 50, 100, 200, 500, 1000, 10000],
      layout: {
        topStart: ['buttons', 'pageLength']
      },
      buttons: [{
        text: '<i class="fas fa-file-export"></i>&nbspEXPORT',
        className: 'btn btn-warning text-light me-2 mb-2',
        extend: 'excel',
        filename: 'transaction-report',
        title: '',
        exportOptions: {
          columns: [1, 2, 3, 4, 5, 6, 7],
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
          data: 'tanggal',
          name: 'tanggal',
          render: function(data, type, row, meta) {
            return moment(data).local().format('DD/MM/YYYY HH:mm:ss');
          },
        },
        {
          data: 'category',
          name: 'category',
          // render: function(data) {
          //     if (data === 'Invoice') {
          //         return '<span class="badge bg-primary">' + data + '</span>'
          //     } else if (data === 'Hotspot') {
          //         return '<span class="badge bg-secondary">' + data + '</span>'
          //     } else if (data === 'Operasional') {
          //         return '<span class="badge bg-warning">' + data + '</span>'
          //     }else if (data === 'Belanja') {
          //         return '<span class="badge bg-cyan">' + data + '</span>'
          //     }
          // },
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
          data: 'type',
          name: 'type',
          render: function(data) {
            if (data === 1) {
              return '<span class="text-success">Income</span>'
            } else {
              return '<span class="text-danger">Expense</span>'
            }

          },
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
        // {
        //     data: 'action',
        //     name: 'action',
        //     width: '90px',
        // },
      ]
    });

    // table.buttons().container().addClass("d-inline").appendTo('#export');

    /* custom button event print */
    $(document).on('click', '#export', function() {
      $(".buttons-print")[0].click(); //trigger the click event
    });

    table.on('preXhr.dt', function(e, settings, data) {
      data.start_date = $('#mindate').val();
      data.end_date = $('#maxdate').val();
    });

    $('#maxdate').change(function() {
      table.ajax.reload();
      return false;
    });

    $('#filter_item').change(function() {
      table.column($(this).data('column'))
        .search($(this).val().toString())
        .draw();
    });

    $('#filter_type').change(function() {
      var any_string = $(this).val().toString();
      table.column($(this).data('column'))
        .search(any_string, true, false)
        .draw();
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
        'item': $('#item_ci').val(),
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
        url: baseUrl.replace('transaction', 'income'),
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

    $('#store_expense').click(function(e) {
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
        'tanggal': $('#tanggal_ce').val(),
        'type': $('#type_ce').val(),
        'item': $('#item_ce').val(),
        'deskripsi': $('#deskripsi_ce').val(),
        'price': $('#price_ce').val(),
        'payment_method': $('#payment_method_ce').val(),
      }


      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      // ajax proses
      $.ajax({
        url: baseUrl.replace('transaction', 'expense'),
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
              $('#create_expense').modal('hide');
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

    $('#create_income').on('shown.bs.modal', function(e) {
      document.getElementById('tanggal_ci').valueAsDate = new Date();
    })
    $('#create_expense').on('shown.bs.modal', function(e) {
      document.getElementById('tanggal_ce').valueAsDate = new Date();
    })

    /* Tanpa Rupiah */
    var price_ce = document.getElementById('price_ce');
    price_ce.addEventListener('keyup', function(e) {
      price_ce.value = formatRupiah(this.value);
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
