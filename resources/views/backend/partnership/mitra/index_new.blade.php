@extends('backend.layouts.app_new')
@section(section: 'main')
@section('title', 'Mitra')
<!-- Content -->
<div class="container-fluid">
    <!-- Page header -->
    <div class="row align-items-center mb-7">
        <!-- Breadcrumb and Title -->
        <div class="col-md-6 mb-3 mb-md-0">
            <h4 class="main-title">Mitra</h4>
            <ul class="app-line-breadcrumbs mb-0">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <span><i class="ti ti-heart-handshake f-s-16"></i> Patnership</span>
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="javascript:void(0)">Mitra</a>
                </li>
            </ul>
        </div>

        <!-- Buttons -->
        <div class="d-flex flex-wrap justify-content-md-end gap-2">
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#create">
                <i class="ti ti-plus"></i> Tambah
            </button>
        </div>
    </div>
    <br />
    @include('backend.partnership.mitra.modal.create')
    @include('backend.partnership.mitra.modal.edit')
    @include('backend.partnership.mitra.modal.editkomisi')

    <!-- Page content -->
    <div class="row">
        <div class="card-body table-responsive">
            <table id="myTable" class="table table-responsive table-hover display nowrap" width="100%">
                <thead>
                    <tr>
                        <th style="text-align:left!important">No</th>
                        <th>ID Mitra</th>
                        <th>Nama Mitra</th>
                        <th>Nomor WA</th>
                        <th>Jml Plgn</th>
                        <th>Status</th>
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
        scrollX: true,
        ajax: '{{ url()->current() }}',
        columns: [{
                data: null,
                'sortable': false,
                className: 'text-center',
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: 'id_mitra',
                name: 'id_mitra',
            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'nomor_wa',
                name: 'nomor_wa',
            },
            {
                data: 'jml_plgn',
                name: 'jml_plgn',
                className: 'text-center',
                sortable: false,
                searchable: false,
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
                data: 'action',
                name: 'action'
            }
        ]
    });

    var multipleCancelButton = new Choices(
        '#profile', {
            allowHTML: true,
            removeItemButton: true,
        }
    );

    $('#create').on('shown.bs.modal', function() {
        var number = 'M' + Math.random().toString().substring(2, 8);
        $('#id_mitra').val(number);
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
                    url: `/partnership/mitra/${id}`,
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

    // action create
    // Aksi Create (Store)
    $('#store').click(function(e) {
        e.preventDefault();

        // Hapus pesan error jika ada
        var error_ele = document.getElementsByClassName('form-text text-danger');
        if (error_ele.length > 0) {
            for (var i = error_ele.length - 1; i >= 0; i--) {
                error_ele[i].remove();
            }
        }

        // Kumpulkan data dari form
        var data = {
            'nama_mitra': $('#nama_mitra').val(),
            'nomor_wa': $('#nomor_wa').val(),
            'profile': $('#profile').val(),
            'id_mitra': $('#id_mitra').val(),
            'password': $('#pass_mitra').val(),
            'login': $('#login').val(),
            'user': $('#user').val(),
            'billing': $('#billing').val(),
        };

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Simpan referensi tombol dan teks aslinya
        var btn = $(this);
        var originalText = btn.html();
        // Ubah tampilan tombol: nonaktifkan dan tampilkan teks dengan spinner di sebelah kanan
        btn.prop('disabled', true).html(
            'Memproses... <i class="ti ti-loader ti-spin"></i>');

        // Proses AJAX
        $.ajax({
            url: `/partnership/mitra`,
            type: "POST",
            cache: false,
            data: data,
            dataType: "json",
            success: function(data) {
                // Kembalikan tombol ke kondisi semula
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
                        $('input, textarea').val('');
                        $('#create').modal('hide');
                    }, 1500);
                } else {
                    $.each(data.error, function(key, value) {
                        var el = $(document).find('[name="' + key + '"]');
                        // Tambahkan class is-invalid untuk membuat border merah (jika menggunakan Bootstrap)
                        el.addClass('is-invalid');
                        el.after($('<div class="form-text text-danger">' + value[0] +
                            '</div>'));
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

        // Hapus pesan error jika ada
        var error_ele = document.getElementsByClassName('form-text text-danger');
        if (error_ele.length > 0) {
            for (var i = error_ele.length - 1; i >= 0; i--) {
                error_ele[i].remove();
            }
        }
        let mitra_id = $('#mitra_id').val();

        // Kumpulkan data dari form
        var data = {
            'nama_mitra': $('#nama_mitra_edit').val(),
            'nomor_wa': $('#nomor_wa_edit').val(),
            'profile': $('#profile_edit').val(),
            'id_mitra': $('#id_mitra_edit').val(),
            'password': $('#pass_mitra_edit').val(),
            'login': $('#login_edit').val(),
            'user': $('#user_edit').val(),
            'billing': $('#billing_edit').val(),
        };

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Simpan referensi tombol dan teks aslinya
        var btn = $(this);
        var originalText = btn.html();
        // Ubah tampilan tombol: nonaktifkan dan tampilkan teks dengan spinner di sebelah kanan
        btn.prop('disabled', true).html(
'Memproses... <i class="ti ti-loader-2 ti-spin"></i>');

        // Proses AJAX
        $.ajax({
            url: `/partnership/mitra/${mitra_id}`,
            type: "PUT",
            cache: false,
            data: data,
            dataType: "json",
            success: function(data) {
                // Kembalikan tombol ke kondisi semula
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
                        // Tambahkan class is-invalid untuk membuat border merah (jika menggunakan Bootstrap)
                        el.addClass('is-invalid');
                        el.after($('<div class="form-text text-danger">' + value[0] +
                            '</div>'));
                    });

                }
            },
            error: function(err) {
                btn.prop('disabled', false).html(originalText);
                $("#message").html("Some Error Occurred!");
            }
        });
    });


    var multipleCancelButton = new Choices(
        '#profile_edit', {
            allowHTML: true,
            removeItemButton: true,
        }
    );
    $('#myTable').on('click', '#edit', function() {

        let mitra_id = $(this).data('id');

        //fetch detail post with ajax
        $.ajax({
            url: `/partnership/mitra/${mitra_id}`,
            type: "GET",
            cache: false,
            success: function(response) {
                // console.log(response.data.profile)

                //fill data to form
                $('#mitra_id').val(response.data.id),
                    $('#nama_mitra_edit').val(response.data.name),
                    $('#nomor_wa_edit').val(response.data.nomor_wa),
                    $('#id_mitra_edit').val(response.data.id_mitra),
                    $('#login_edit').val(response.data.login),
                    $('#user_edit').val(response.data.user),
                    $('#billing_edit').val(response.data.billing);
                // $('#profile_edit').val();
                if (typeof response.data.profile === 'string') {
                    try {
                        response.data.profile = JSON.parse(response.data.profile);
                    } catch (e) {
                        console.error("Gagal parsing string ke array:", e);
                    }
                };
                if (response.data.profile !== null && response.data.profile !== '' && response.data
                    .profile !== undefined) {
                       multipleCancelButton.removeActiveItems();
                        multipleCancelButton.setChoiceByValue(response.data.profile);
                }
             
                //open modal
                $('#edit').modal('show');
            }
        });
    });



    $('#myTable').on('click', '#enable', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: "Aktifkan Mitra",
            icon: 'warning',
            text: 'Apakah Anda yakin ingin mengaktifkan mitra ini?',
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
                    url: `/partnership/mitra/enable/${id}`,
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

    $('#myTable').on('click', '#disable', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: "Nonaktifkan Mitra",
            icon: 'warning',
            text: 'Apakah Anda yakin ingin menonaktifkan mitra ini?',
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
                    url: `/partnership/mitra/disable/${id}`,
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
    
    $('#myTable').on('click', '#edit_komisi', function() {

        let mitra_id = $(this).data('id');

        //fetch detail post with ajax
        $.ajax({
            url: `/partnership/mitra/${mitra_id}`,
            type: "GET",
            cache: false,
            success: function(response) {
                $.ajax({
                    url: `/partnership/mitra/get_invoice_paid/${mitra_id}`,
                    type: "GET",
                    cache: false,
                    success: function(responsex) {
                        $('#id_invoice').select2({
                        data: (() => {
                            return responsex.map((item) => {
                                return {
                                id: item.id,
                                text: item.no_invoice
                                }
                            })
                            .sort((a, b) => a.text.localeCompare(b.text));
                        })(),
                        allowClear: true,
                        placeholder: $(this).data('placeholder'),
                        dropdownParent: $("#edit_komisi .modal-content"),
                        });
                    }
                });
                // console.log(response.data)

                //fill data to form
                $('#mitra_id').val(response.data.id),
                $('#nama_mitra_edit_komisi').val(response.data.name), 
                $('#balance_mitra_edit_komisi').val(formatRupiah(response.data.balance)),            
                //open modal
                $('#edit_komisi').modal('show');
            }
        });
    });

    $('#nominal_mitra_edit_komisi').on('input', function() {
        let val = unformatRupiah($(this).val());
        $(this).val(formatRupiah(val));
    });

    function formatRupiah(angka) {
        return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function unformatRupiah(rupiah) {
        return parseFloat(rupiah.replace(/[^\d]/g, '')) || 0;
    }    

    // Aksi Update
    $('#update_komisi').click(function(e) {
        e.preventDefault();
        // Hapus pesan error jika ada
        var error_ele = document.getElementsByClassName('form-text text-danger');
        if (error_ele.length > 0) {
            for (var i = error_ele.length - 1; i >= 0; i--) {
                error_ele[i].remove();
            }
        }
        let mitra_id = $('#mitra_id').val();

        // Kumpulkan data dari form
        var data = {
            'mitra_id': $('#mitra_id').val(),
            'jenis': $('#jenis').val(),
            'id_invoice': $('#id_invoice').val(),
            'nominal_mitra_edit_komisi': unformatRupiah($('#nominal_mitra_edit_komisi').val()),
            'notes': $('#notes').val()
        };

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Simpan referensi tombol dan teks aslinya
        var btn = $(this);
        var originalText = btn.html();
        // Ubah tampilan tombol: nonaktifkan dan tampilkan teks dengan spinner di sebelah kanan
        btn.prop('disabled', true).html('Memproses... <i class="ti ti-loader-2 ti-spin"></i>');

        // Proses AJAX
        $.ajax({
            url: `/partnership/mitra/edit_saldo`,
            type: "POST",
            cache: false,
            data: data,
            dataType: "json",
            success: function(data) {
                // Kembalikan tombol ke kondisi semula
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
                        $('#edit_komisi').modal('hide');
                        $('#mitra_id').val('');
                        $('#jenis').val('');
                        $('#id_invoice').val('');
                        $('#nominal_mitra_edit_komisi').val(0);
                        $('#notes').val('');
                    }, 1500);
                } else {
                    $.each(data.error, function(key, value) {
                        var el = $(document).find('[name="' + key + '"]');
                        // Tambahkan class is-invalid untuk membuat border merah (jika menggunakan Bootstrap)
                        el.addClass('is-invalid');
                        el.after($('<div class="form-text text-danger">' + value[0] +
                            '</div>'));
                    });

                }
            },
            error: function(err) {
                btn.prop('disabled', false).html(originalText);
                $("#message").html("Some Error Occurred!");
            }
        });
    });
</script>
@endpush
