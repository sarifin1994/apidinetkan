@extends('backend.layouts.app')

@section('title', 'Hotspot Reseller Management')

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
          <h3>Hotspot Reseller Management</h3>
        </div>
        <div class="col-sm-6 pe-0">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">
                <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item">Hotspot</li>
            <li class="breadcrumb-item active">Reseller</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Container-fluid starts-->
  <div class="container-fluid">
    <div class="row">
      <div class="col-xxl-4 box-col-4">
        <div class="card">
          <div class="card-body">
            <div class="bg-light-primary b-r-15">
              <div class="upcoming-box d-flex align-items-center justify-content-between px-4">
                <div>
                  <div class="upcoming-icon bg-primary">
                    <svg class="stroke-icon">
                      <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-router') }}"></use>
                    </svg>
                  </div>
                  <p>{{ $count }} Reseller</p>
                </div>

                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create">Add
                  Reseller</button>
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
            <div class="table-responsive custom-scrollbar" id="row_create">
              <table id="myTable" class="table-hover display nowrap table" width="100%">
                <thead>
                  <tr>
                    <th style="text-align:left!important">NO</th>
                    <th>NAMA RESELLER</th>
                    <th>AREA</th>
                    <th>NOMOR WA</th>
                    <th>SALDO (RP)</th>
                    <th>VCR NEW</th>
                    <th>VCR AKTIF</th>
                    <th>IZIN LOGIN</th>
                    <th>IZIN CETAK</th>
                    <th>STATUS</th>
                    <th>TGL JOIN</th>
                    <th>AKSI</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>

            @include('hotspot.reseller.modal.create')
            @include('hotspot.reseller.modal.edit')
            @include('hotspot.reseller.modal.deposit')
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Container-fluid Ends-->
@endsection

@section('scripts')
  <script src="{{ asset('assets/radiusqu/dist/js/moment.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/datatable.custom.js') }}"></script>

  <script type="text/javascript">
    const createModal = new bootstrap.Modal(document.getElementById('create'), {
      keyboard: false
    });
    const editModal = new bootstrap.Modal(document.getElementById('edit'), {
      keyboard: false
    });
    const depositModal = new bootstrap.Modal(document.getElementById('deposit'), {
      keyboard: false
    });

    let table = $('#myTable').DataTable({
      processing: true,
      serverSide: true,
      // scrollX: true,
      order: [
        [10, 'desc']
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
          data: 'name',
          name: 'name'
        },
        {
          data: 'kode_area',
          name: 'kode_area'
        },
        {
          data: 'wa',
          name: 'wa'
        },
        {
          data: 'balance',
          name: 'balance',
          render: $.fn.dataTable.render.number('.', ',', 0, ''),
        },
        {
          data: 'voucher_new',
          name: 'voucher_new',
          searchable: false,
          sortable: false,

        },
        {
          data: 'voucher_aktif',
          name: 'voucher_aktif',
          searchable: false,
          sortable: false,

        },
        {
          data: 'login',
          name: 'login',
          render: function(data) {
            if (data !== 0) {
              return '<a class="badge bg-success" href="/user">Ya</a>'
            } else {
              return '<a class="badge bg-danger" href="/user">Tidak</a>'
            }

          },
        },
        {
          data: 'cetak',
          name: 'cetak',
          render: function(data) {
            if (data !== 0) {
              return '<span class="badge bg-success">Ya</span>'
            } else {
              return '<span class="badge bg-danger">Tidak</span>'
            }

          },
        },
        {
          data: 'status',
          name: 'status',
          render: function(data) {
            if (data === 1) {
              return '<span class="badge bg-success">Aktif</span>'
            } else {
              return '<span class="badge bg-danger">Nonaktif</span>'
            }

          },
        },
        {
          data: 'created_at',
          name: 'created_at',
          className: 'd-none',
          render: function(data, type, row, meta) {
            return moment(data).local().format('DD/MM/YYYY HH:mm:ss');
          },
        },
        {
          data: 'action',
          name: 'action'
        }
      ]
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

    // action create
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
        'name': $('#name').val(),
        'wa': $('#wa').val(),
        'kode_area': $('#kode_area').val(),
        'nas': $('#nas').val(),
        'profile': $('#profile').val(),
        'cetak': $('#cetak').val(),
      }
      // console.log(data);

      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      // ajax proses
      $.ajax({
        url: baseUrl + `/`,
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
              $('input, textarea').val('');
              $('#kode_area').val('').trigger('change');
              createModal.hide();
            });
          } else {
            $.each(data.error, function(key, value) {
              var el = $(document).find('[name="' + key + '"]');
              el.after($('<div class="form-text text-danger">' + value[0] +
                '</div>'));
            });
          }
        },

        error: function(err) {
          $("#message").html("Some Error Occurred!")
        }

      });
    });

    $('#myTable').on('click', '#edit', function() {

      let id = $(this).data('id');

      //fetch detail post with ajax
      $.ajax({
        url: baseUrl + `/${id}`,
        type: "GET",
        cache: false,
        success: function(response) {


          //fill data to form
          $('#id').val(response.data.id),
            $('#name_edit').val(response.data.name),
            $('#kode_area_edit').val(response.data.kode_area).trigger('change'),
            $('#wa_edit').val(response.data.wa),
            $('#nas_edit').val(response.data.nas),
            $('#profile_edit').val(response.data.profile),
            $('#cetak_edit').val(response.data.cetak),

            //open modal
            editModal.show();
        }
      });
    });

    $('#update').click(function(e) {
      e.preventDefault();
      var error_ele = document.getElementsByClassName('form-text text-danger');
      if (error_ele.length > 0) {
        for (var i = error_ele.length - 1; i >= 0; i--) {
          error_ele[i].remove();
        }
      }
      let id = $('#id').val();

      // collect data by id
      var data = {
        'name_edit': $('#name_edit').val(),
        'wa_edit': $('#wa_edit').val(),
        'kode_area_edit': $('#kode_area_edit').val(),
        'nas_edit': $('#nas_edit').val(),
        'profile_edit': $('#profile_edit').val(),
        'cetak_edit': $('#cetak_edit').val(),
      }


      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      $.ajax({
        url: baseUrl + `/${id}`,
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
              editModal.hide();
            });
          } else {
            $.each(data.error, function(key, value) {
              var el = $(document).find('[name="' + key + '"]');
              el.after($('<div class="form-text text-danger">' + value[0] +
                '</div>'));
            });
          }
        },

        error: function(err) {
          $("#message").html("Some Error Occurred!")
        }

      });

    });

    $('#myTable').on('click', '#disable', function() {
      let id = $(this).data('id');
      Swal.fire({
        title: "Nonaktifkan Reseller",
        icon: 'warning',
        text: 'Apakah Anda yakin ingin menonaktifkan reseller ini?',
        showCancelButton: !0,
        confirmButtonText: "Ya, Nonaktifkan",
        cancelButtonText: "Batal",
        reverseButtons: !0,
      }).then(function(result) {
        if (result.isConfirmed) {
          var data = {
            'id': id
          };

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
            url: baseUrl + `/disable/${id}`,
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
                });
              } else {

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
    });

    $('#myTable').on('click', '#enable', function() {
      let id = $(this).data('id');
      Swal.fire({
        title: "Aktifkan Reseller",
        icon: 'warning',
        text: 'Apakah Anda yakin ingin mengaktifkan reseller ini?',
        showCancelButton: !0,
        confirmButtonText: "Ya, Aktifkan",
        cancelButtonText: "Batal",
        reverseButtons: !0,
      }).then(function(result) {
        if (result.isConfirmed) {
          var data = {
            'id': id
          };

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
            url: baseUrl + `/enable/${id}`,
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
                    table.ajax.reload();
                  });
              } else {

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
    });

    $('#myTable').on('click', '#deposit', function() {

      let id = $(this).data('id');

      //fetch detail post with ajax
      $.ajax({
        url: baseUrl + `/${id}`,
        type: "GET",
        cache: false,
        success: function(response) {
          //fill data to form
          $('#id_deposit').val(response.data.id),
            $('#group_id').val(response.data.group_id),
            depositModal.show();
        }
      });
    });

    $('#depo').click(function(e) {
      e.preventDefault();
      var error_ele = document.getElementsByClassName('form-text text-danger');
      if (error_ele.length > 0) {
        for (var i = error_ele.length - 1; i >= 0; i--) {
          error_ele[i].remove();
        }
      }
      let id = $('#id_deposit').val();

      // collect data by id
      var data = {
        'jml_deposit': $('#jml_deposit').val(),
        'group_id': $('#group_id').val(),
        'reseller_id': $('#id_deposit').val(),
      }


      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      $.ajax({
        url: baseUrl + `/deposit`,
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
              depositModal.hide();
            });
          } else {
            $.each(data.error, function(key, value) {
              var el = $(document).find('[name="' + key + '"]');
              el.after($('<div class="form-text text-danger">' + value[0] +
                '</div>'));
            });
          }
        },

        error: function(err) {
          $("#message").html("Some Error Occurred!")
        }

      });

    });



    $('#kode_area').select2({
      dropdownParent: $("#create .modal-content"),
      width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
      placeholder: $(this).data('placeholder'),
    });

    $('#profile').select2({
      dropdownParent: $("#create .modal-content"),
      width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
      placeholder: $(this).data('placeholder'),
    });


    $('#kode_area_edit').select2({
      dropdownParent: $("#edit .modal-content"),
      width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
      placeholder: $(this).data('placeholder'),
    });

    $('#profile_edit').select2({
      dropdownParent: $("#edit .modal-content"),
      width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
      placeholder: $(this).data('placeholder'),
    });
  </script>
@endsection
