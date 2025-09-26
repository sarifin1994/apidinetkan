@extends('backend.layouts.app_new')
@section('main')
@section('title', 'Mapping POP')
<!-- Content -->
<div class="container-lg">
    <!-- Page header -->
    <div class="row align-items-center mb-7">
        <!-- Breadcrumb and Title -->
        <div class="col-md-6">
            <h4 class="main-title">POP</h4>
            <ul class="app-line-breadcrumbs mb-0">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <span><i class="ti ti-map f-s-16"></i> Mapping</span>
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="javascript:void(0)">POP</a>
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
    @include('backend.mapping.area.modal.create')
    @include('backend.mapping.area.modal.edit')

    <!-- Page content -->
    <div class="row">
        <div class="card-body table-responsive">
            <table id="myTable" class="table table-responsive table-hover display nowrap" width="100%">
                <thead>
                    <tr>
                        <th style="text-align:left!important">No</th>
                        <th>POP</th>
                        <th>Deskripsi</th>
                        <th style="text-align:left!important">Jml ODP</th>
                        <th style="text-align:left!important">Jml Plgn</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
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
        scrollX: true,
        ajax: '{{ url()->current() }}',
        columns: [
            {
                data: null,
                sortable: false,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'kode_area', name: 'kode_area' },
            { data: 'deskripsi', name: 'deskripsi' },
            { data: 'jml_odp', name: 'jml_odp', className: "text-center", sortable: false, searchable: false },
            { data: 'jml_plgn', name: 'jml_plgn', className: "text-center", sortable: false, searchable: false },
            { data: 'action', name: 'action' }
        ]
    });

    $('#myTable').on('click', '#delete', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: "Apakah anda yakin?",
            icon: 'warning',
            text: "Data yang sudah dihapus tidak dapat dikembalikan",
            showCancelButton: true,
            reverseButtons: true,
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
                    url: `/mapping/pop/${id}`,
                    type: "POST",
                    cache: false,
                    data: { _method: "DELETE" },
                    dataType: "json",
                    success: function(data) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: `${data.message}`,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        setTimeout(() => table.ajax.reload(), 1500);
                    },
                    error: function() {
                        $("#message").html("Some Error Occurred!");
                    }
                });
            }
        });
    });

    $('#store').click(function(e) {
        e.preventDefault();

        $('.form-text.text-danger').remove();

        const data = {
            kode_area: $('#kode_area').val(),
            deskripsi: $('#deskripsi').val(),
        };

        const btn = $(this);
        const originalText = btn.html();
        btn.prop('disabled', true).html('Memproses... <i class="ti ti-loader-2 spinner"></i>');

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $.ajax({
            url: `/mapping/pop`,
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
                    setTimeout(() => {
                        table.ajax.reload();
                        $('input, textarea').val('');
                        $('#kode_area_id').val('').trigger('change');
                        $('#create').modal('hide');
                    }, 1500);
                } else {
                    $.each(data.error, function(key, value) {
                        $(`[name="${key}"]`).after(`<div class="form-text text-danger">${value[0]}</div>`);
                    });
                }
            },
            error: function() {
                btn.prop('disabled', false).html(originalText);
                $("#message").html("Some Error Occurred!");
            }
        });
    });

    $('#myTable').on('click', '#edit', function() {
        const pop_id = $(this).data('id');
        $.ajax({
            url: `/mapping/pop/${pop_id}`,
            type: "GET",
            cache: false,
            success: function(response) {
                $('#area_id').val(response.data.id);
                $('#kode_area_edit').val(response.data.kode_area);
                $('#deskripsi_edit').val(response.data.deskripsi);
                $('#edit').modal('show');
            }
        });
    });

    $('#update').click(function(e) {
        e.preventDefault();
        $('.form-text.text-danger').remove();

        const area_id = $('#area_id').val();
        const data = {
            kode_area: $('#kode_area_edit').val(),
            deskripsi: $('#deskripsi_edit').val(),
        };

        const btn = $(this);
        const originalText = btn.html();
        btn.prop('disabled', true).html('Memproses... <i class="ti ti-loader-2 spinner"></i>');

        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $.ajax({
            url: `/mapping/pop/${area_id}`,
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
                    setTimeout(() => {
                        table.ajax.reload();
                        $('#edit').modal('hide');
                    }, 1500);
                } else {
                    $.each(data.error, function(key, value) {
                        $(`[name="${key}"]`).after(`<div class="form-text text-danger">${value[0]}</div>`);
                    });
                }
            },
            error: function() {
                btn.prop('disabled', false).html(originalText);
                $("#message").html("Some Error Occurred!");
            }
        });
    });
</script>
@endpush
