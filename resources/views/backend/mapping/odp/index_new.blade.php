@extends('backend.layouts.app_new')
@section('main')
@section('title', 'Mapping ODP')
<!-- Content -->
<div class="container-fluid">
    <div class="row align-items-center mb-7">
        <!-- Breadcrumb and Title -->
        <div class="col-md-6">
            <h4 class="main-title">ODP</h4>
            <ul class="app-line-breadcrumbs mb-0">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <span>
                            <i class="ti ti-map f-s-16"></i> Mapping
                        </span>
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="javascript:void(0)">ODP</a>
                </li>
            </ul>
        </div>

        <!-- Button -->
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create">
                <i class="ti ti-plus me-1"></i>
                Create
            </button>
        </div>
    </div><br />
    
    @include('backend.mapping.odp.modal.create')
    @include('backend.mapping.odp.modal.edit')

    <!-- Page content -->
    <div class="row">
        <div class="card-body table-responsive">
            <table id="myTable" class="table table-responsive table-hover display nowrap" width="100%">
                <thead>
                    <tr>
                        <th style="text-align:left!important">No</th>
                        <th>POP</th>
                        <th>ODP</th>
                        <th>Deskripsi</th>
                        <th style="text-align:left!important">Jml Port</th>
                        <th style="text-align:left!important">Jml Plgn</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    let table = $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ url()->current() }}',
        columns: [
            {
                data: null,
                'sortable': false,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: 'area.kode_area',
                name: 'area.kode_area'
            },
            {
                data: 'kode_odp',
                name: 'kode_odp'
            },
            {
                data: 'deskripsi',
                name: 'deskripsi'
            },
            {
                data: 'port_odp',
                name: 'port_odp',
                className: "text-center",
            },
            {
                data: 'jml_plgn',
                name: 'jml_plgn',
                className: "text-center",
                sortable: false,
                searchable: false,
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
            confirmButtonText: "Ya, Hapus!",
            cancelButtonText: "Batal",
            confirmButtonColor: "#d33",
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: `/mapping/odp/${id}`,
                    type: "POST",
                    cache: false,
                    data: {
                        _method: "DELETE"
                    },
                    dataType: "json",
                    success: function(data) {
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
                    },
                    error: function(err) {
                        $("#message").html("Some Error Occurred!")
                    }
                });
            }
        });
    });

    // Aksi Create
    $('#store').click(function(e) {
        e.preventDefault();

        var error_ele = document.getElementsByClassName('form-text text-danger');
        if (error_ele.length > 0) {
            for (var i = error_ele.length - 1; i >= 0; i--) {
                error_ele[i].remove();
            }
        }

        var data = {
            'kode_area_id': $('#kode_area_id').val(),
            'kode_odp': $('#kode_odp').val(),
            'deskripsi': $('#deskripsi').val(),
            'port_odp': $('#port_odp').val(),
            'latitude': $('#latitude').val(),
            'longitude': $('#longitude').val(),
        };

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var btn = $(this);
        var originalText = btn.html();
        btn.prop('disabled', true).html(
            'Memproses... <i class="ti ti-loader ti-spin"></i>');

        $.ajax({
            url: `/mapping/odp`,
            type: "POST",
            cache: false,
            data: data,
            dataType: "json",
            success: function(data) {
                btn.prop('disabled', false).html(originalText);
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: `${data.message}`,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    setTimeout(function() {
                        $('#create').modal('hide');
                        table.ajax.reload();
                        $('input, textarea').val('');
                    }, 1500);
                } else {
                    $.each(data.error, function(key, value) {
                        var el = $(document).find('[name="' + key + '"]');
                        el.after($('<div class="form-text text-danger">' + value[0] + '</div>'));
                    });
                }
            },
            error: function(err) {
                btn.prop('disabled', false).html(originalText);
                $("#message").html("Some Error Occurred!");
            }
        });
    });

    // Aksi Update
    $('#update').click(function(e) {
        e.preventDefault();

        var error_ele = document.getElementsByClassName('alert text-sm');
        if (error_ele.length > 0) {
            for (var i = error_ele.length - 1; i >= 0; i--) {
                error_ele[i].remove();
            }
        }

        let odp_id = $('#odp_id').val();

        var data = {
            'kode_area_id': $('#kda_id').val(),
            'kode_odp': $('#kode_odp_edit').val(),
            'deskripsi': $('#deskripsi_edit').val(),
            'port_odp': $('#port_odp_edit').val(),
            'latitude': $('#latitude_edit').val(),
            'longitude': $('#longitude_edit').val(),
        };

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var btn = $(this);
        var originalText = btn.html();
        btn.prop('disabled', true).html(
            'Memproses... <i class="ti ti-loader ti-spin"></i>');

        $.ajax({
            url: `/mapping/odp/${odp_id}`,
            type: "PUT",
            cache: false,
            data: data,
            dataType: "json",
            success: function(data) {
                btn.prop('disabled', false).html(originalText);
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: `${data.message}`,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    setTimeout(function() {
                        table.ajax.reload();
                        $('#edit').modal('hide');
                    }, 1500);
                } else {
                    $.each(data.error, function(key, value) {
                        var el = $(document).find('[name="' + key + '"]');
                        el.after($('<span class="alert text-sm text-danger">' + value[0] + '</span>'));
                    });
                }
            },
            error: function(err) {
                btn.prop('disabled', false).html(originalText);
                $("#message").html("Some Error Occurred!");
            }
        });
    });

    // action edit
    $('#myTable').on('click', '#edit', function() {
        let odp_id = $(this).data('id');

        $.ajax({
            url: `/mapping/odp/${odp_id}`,
            type: "GET",
            cache: false,
            success: function(response) {
                $('#odp_id').val(response.data.id),
                $('#kda_id').val(response.data.kode_area_id),
                $('#kode_area_id_edit').val(response.data.area.kode_area),
                $('#kode_odp_edit').val(response.data.kode_odp),
                $('#deskripsi_edit').val(response.data.deskripsi),
                $('#port_odp_edit').val(response.data.port_odp),
                $('#latitude_edit').val(response.data.latitude),
                $('#longitude_edit').val(response.data.longitude),
                $('#edit').modal('show');
            }
        });
    });
</script>
@endpush
