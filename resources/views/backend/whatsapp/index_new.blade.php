@extends('backend.layouts.app_new')
@section(section: 'main')
@section('title', 'Whatsapp')
<!-- Content -->
<div class="container-fluid">
    <!-- Page header -->
    <div class="row align-items-center mb-7">
        <!-- Breadcrumb and Title -->
        <div class="col-md-6 mb-3 mb-md-0">
            <h4 class="main-title">Whatsapp</h4>
            <ul class="app-line-breadcrumbs mb-0">
                <li>
                    <a class="active" href="#">
                        <span><i class="ti ti-brand-whatsapp f-s-16"></i> Whatsapp</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="d-flex flex-wrap justify-content-md-end gap-2">
            <!-- Tambah Button -->

        </div>
    </div><br />
    <!-- Page content -->
    @include('backend.whatsapp.modal.scan')
    @include('backend.whatsapp.modal.change')
    @if (multi_auth()->role === 'Admin')
        @include('backend.whatsapp.modal.show_invoice_terbit')
        @include('backend.whatsapp.modal.show_invoice_reminder')
        @include('backend.whatsapp.modal.show_invoice_paid')
        @include('backend.whatsapp.modal.show_account_active')
        @include('backend.whatsapp.modal.show_account_suspend')
        @include('backend.whatsapp.modal.show_invoice_overdue')
        @include('backend.whatsapp.modal.show_invoice_cancel')
        @include('backend.whatsapp.modal.show_tiket_open_pelanggan')
        @include('backend.whatsapp.modal.show_tiket_open_teknisi')
        @include('backend.whatsapp.modal.show_tiket_close_pelanggan')
        @include('backend.whatsapp.modal.show_tiket_close_teknisi')
    @endif
    @include('backend.whatsapp.modal.broadcast')
    @include('backend.whatsapp.modal.show_message')
    <!-- Tabler UI version (Modern & Professional) -->
   <div class="row">
    <div class="col-12 col-xxl-4">
        <div class="position-sticky mb-8" style="top: 40px">
            <!-- Card -->
            <div class="card mb-3 shadow-sm border-0">
                <!-- Badge Header -->
                <div class="text-center mt-n2">
                    <span class="badge bg-primary-subtle text-primary fs-6">
                        <i class="ti ti-info-circle me-1"></i> Device Information
                    </span>
                </div>
            
                <!-- Body -->
                <div class="card-body text-center">
                    <ul class="list-group list-group-flush mb-0">
                        @if ($whatsapp->mpwa_server_server == 'mpwa')
                            <li class="list-group-item d-flex justify-content-between px-0 bg-transparent border-0">
                                <span class="text-muted">Nomor Pengirim</span>
                                <span>{{ $result['info'][0]['body'] ?? 'NULL' }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0 bg-transparent border-0">
                                <span class="text-muted">Status Device</span>
                                @if ($result['info'][0]['status'] === 'Disconnect' ?? 'NULL')
                                    <span class="badge bg-danger-subtle text-danger">Disconnect</span>
                                @else
                                    <span class="badge bg-success-subtle text-success">Connected</span>
                                @endif
                            </li>
                        @endif
            
                        @if ($whatsapp->mpwa_server_server == 'radiusqu')
                            <li class="list-group-item d-flex justify-content-between px-0 bg-transparent border-0">
                                <span class="text-muted">Status Device</span>
                                @if ($whatsapp->is_login == 0)
                                    <span class="badge bg-danger-subtle text-danger">Disconnect</span>
                                @else
                                    <span class="badge bg-success-subtle text-success">Connected</span>
                                @endif
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
            

            <!-- Buttons -->
            <div class="row gx-3">
                <div class="col">
                    <button class="btn btn-outline-primary w-100" data-bs-target="#scan" data-bs-toggle="modal">
                        <i class="ti ti-scan me-1"></i> Scan
                    </button>
                </div>
                <div class="col">
                    <button class="btn btn-outline-secondary w-100" data-bs-target="#change" data-bs-toggle="modal">
                        <i class="ti ti-edit-circle me-1"></i> Ganti Nomor
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if (multi_auth()->role === 'Admin')
        <div class="col-12 col-xxl-8">
            <div class="card-body">
                <div class="mt-3">
                    <h2 class="fs-5">Template Pesan</h2>
                    <span>Silakan sesuaikan template pesan WhatsApp sesuka hati dengan menggunakan parameter yang tersedia</span>
                </div>
                <hr>

                <!-- Button Group -->
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <button class="btn btn-outline-primary" id="account_active1" data-id="{{ multi_auth()->shortname }}">
                        <i class="ti ti-numbers-1"></i> Pemasangan Baru
                    </button>
                    <button class="btn btn-outline-info" id="invoice_terbit1" data-id="{{ multi_auth()->shortname }}">
                        <i class="ti ti-numbers-2"></i> Invoice Terbit
                    </button>
                    <button class="btn btn-outline-secondary" id="invoice_reminder1" data-id="{{ multi_auth()->shortname }}">
                        <i class="ti ti-numbers-3"></i> Invoice Reminder
                    </button>
                    <button class="btn btn-outline-success" id="payment_paid1" data-id="{{ multi_auth()->shortname }}">
                        <i class="ti ti-numbers-4"></i> Invoice Dibayar
                    </button>
                    <button class="btn btn-outline-danger" id="invoice_overdue1" data-id="{{ multi_auth()->shortname }}">
                        <i class="ti ti-numbers-5"></i> Invoice Overdue
                    </button>
                    <button class="btn btn-outline-danger" id="payment_cancel1" data-id="{{ multi_auth()->shortname }}">
                        <i class="ti ti-numbers-6"></i> Invoice Dibatalkan
                    </button>
                    <button class="btn btn-outline-success" id="user_aktif1" data-id="{{ multi_auth()->shortname }}">
                        <i class="ti ti-numbers-7"></i> User Diaktifkan
                    </button>
                    <button class="btn btn-outline-danger" id="user_suspend1" data-id="{{ multi_auth()->shortname }}">
                        <i class="ti ti-numbers-8"></i> User Disuspend
                    </button>
                </div>

                <hr>

                <!-- Tiket Gangguan -->
                <div class="d-flex flex-wrap gap-2 mb-5">
                    <button class="btn btn-outline-danger" id="tiket_open_pelanggan1" data-id="{{ multi_auth()->shortname }}">
                        <i class="ti ti-alert-circle"></i> Tiket Gangguan Open (Pelanggan)
                    </button>
                    <button class="btn btn-outline-danger" id="tiket_open_teknisi1" data-id="{{ multi_auth()->shortname }}">
                        <i class="ti ti-alert-triangle"></i> Tiket Gangguan Open (Teknisi)
                    </button>
                    <button class="btn btn-outline-success" id="tiket_close_pelanggan1" data-id="{{ multi_auth()->shortname }}">
                        <i class="ti ti-circle-check"></i> Tiket Gangguan Closed (Pelanggan)
                    </button>
                    <button class="btn btn-outline-success" id="tiket_close_teknisi1" data-id="{{ multi_auth()->shortname }}">
                        <i class="ti ti-check"></i> Tiket Gangguan Closed (Teknisi)
                    </button>
                </div>
            </div>
        </div>
    @endif

    <hr>
    <div class="row p-3">
        <div class="col-lg-6">
            <h2 class="fs-5">Riwayat Pesan</h2>
            <span>Silakan track riwayat pesan dibawah ini untuk melihat status pesan</span>
        </div>
        <div class="col-lg-6 mt-3 modal-footer">
            <button class="btn btn-primary text-white me-2 mb-2 float-end" type="button" data-bs-toggle="modal" data-bs-target="#broadcast">
                <i class="ti ti-broadcast"></i> Kirim Broadcast
            </button>
            <button class="btn btn-warning text-white me-2 mb-2 float-end" type="button" id="resend" disabled>
                <i class="ti ti-refresh"></i> Resend
                <span class="row-count badge bg-dark text-white fs-7"></span>
            </button>
            <button class="btn btn-danger text-white me-2 mb-2 float-end" id="delete" type="button" disabled>
                <i class="ti ti-trash"></i> Delete
                <span class="row-count badge bg-dark text-white fs-7"></span>
            </button>
        </div>
    </div>

    <div class="card-body table-responsive">
        <table id="myTable" class="table table-hover display nowrap" width="100%">
            <thead>
                <tr>
                    <th><input type="checkbox" class="form-check-input" id="head-cb"></th>
                    <th>ID</th>
                    <th>Tgl/Waktu</th>
                    <th>Penerima</th>
                    <th>Pesan</th>
                    <th>Status</th>
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
        order: [
            1, 'desc'
        ],
        scrollX: true,
        ajax: '{{ url()->current() }}',
        columns: [{
                data: 'checkbox',
                'sortable': false,
                name: 'checkbox',
            },
            {
                data: 'id',
                name: 'id',
            },
            {
                data: 'created_at',
                name: 'created_at',
                render: function(data, type, row, meta) {
                    return moment(data).local().format('DD MMM YYYY HH:mm:ss');
                },
            },
            {
                data: 'number',
                name: 'number',
            },
            {
                data: 'message',
                name: 'message',
                render: function(data, type, row) {
                    var shortText = data.length > 45 ? data.substr(0, 45) + '...' : data;

                    // Escape kutip dan ubah \n jadi <br> saat nanti di modal
                    var safeMessage = data
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#39;');

                    return '<a href="javascript:void(0)" id="show_message" data-message="' +
                        safeMessage + '">' + shortText + '</a>';
                }

            }, {
                data: 'status',
                name: 'status',
                render: function(data, type, row) {
                    if (data === 'success') {
                        return "<span class='badge bg-success-subtle text-success'>Success</span>";
                    } else {
                        return "<span class='badge bg-danger-subtle text-danger'>Failed</span>"
                    }
                }
            },
        ]
    });

    $('#myTable').on('click', '#show_message', function() {
        var fullMessage = $(this).data('message');

        // Ganti \n atau \\n dengan <br>
        var formattedMessage = fullMessage.replace(/\\n|(\r\n|\n|\r)/g, '<br>');

        $('#messageModal .modal-body').html(formattedMessage);
        $('#messageModal').modal('show');
    });

    var id_selected = [];
    table.on('preXhr.dt', function(e, settings, data) {
        data.idsel = id_selected;
    });
    $('#head-cb').on('click', function(e) {
        if ($(this).is(':checked', true)) {
            $(".row-cb").prop('checked', true);
            $(".row-count").html($('.row-cb:checked').length);
            $('#delete').prop('disabled', false);
            $('#resend').prop('disabled', false);
        } else {
            $(".row-cb").prop('checked', false);
            $(".row-count").html('');
            $('#delete').prop('disabled', true);
            $('#resend').prop('disabled', true);

        }
    });

    $('#myTable').on('click', '.row-cb', function() {
        if ($('.row-cb:checked').length == $('.row-cb').length) {
            $('#head-cb').prop('checked', true);
            $(".row-count").html($('.row-cb:checked').length);
            $('#delete').prop('disabled', false);
            $('#resend').prop('disabled', false);
        } else if ($('.row-cb:checked').length == 0) {
            $('#head-cb').prop('checked', false);
            $(".row-count").html('');
            $('#delete').prop('disabled', true);
            $('#resend').prop('disabled', true);
        } else {
            $('#head-cb').prop('checked', false);
            $(".row-count").html($('.row-cb:checked').length);
            $('#delete').prop('disabled', false);
            $('#resend').prop('disabled', false);
        }
    });

    $('#resend').on('click', function() {
        Swal.fire({
            title: "Apakah anda yakin?",
            icon: 'warning',
            text: "Pesan yang dipilih akan dikirim ulang",
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: "Ya, Kirim",
            cancelButtonText: "Batal",
            confirmButtonColor: "#d33",
        }).then(function(result) {
            if (result.isConfirmed) {
                // Tampilkan sweet alert loading tanpa timer
                Swal.fire({
                    title: "Mengirim ulang",
                    icon: "info",
                    html: "Mengirim ulang pesan. Harap tunggu dan jangan diclose",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                let checked = $('#myTable tbody .row-cb:checked');
                let ids = [];
                $.each(checked, function(index, elm) {
                    ids.push(elm.value);
                });
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: `/whatsapp/message/resend`,
                    type: "POST",
                    cache: false,
                    data: {
                        ids: ids
                    },
                    dataType: "json",
                    success: function(data) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        setTimeout(function() {
                            table.ajax.reload();
                        }, 1500);
                        $('#head-cb').prop('checked', false);
                        $(".row-count").html('');
                    },
                    error: function(err) {
                        $("#message").html("Some Error Occurred!");
                        Swal.close();
                    }
                });
            }
        });
    });

    $('#delete').on('click', function() {
        Swal.fire({
            title: "Apakah anda yakin?",
            icon: 'warning',
            text: "Pesan yang dipilih akan dihapus",
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: "Ya, Hapus",
            cancelButtonText: "Batal",
            confirmButtonColor: "#d33",
        }).then(function(result) {
            if (result.isConfirmed) {
                // Tampilkan sweet alert loading tanpa timer
                Swal.fire({
                    title: "Menghapus pesan",
                    icon: "info",
                    html: "Menghapus pesan. Harap tunggu dan jangan diclose",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                let checked = $('#myTable tbody .row-cb:checked');
                let ids = [];
                $.each(checked, function(index, elm) {
                    ids.push(elm.value);
                });
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: `/whatsapp/message/delete`,
                    type: "POST",
                    cache: false,
                    data: {
                        ids: ids
                    },
                    dataType: "json",
                    success: function(data) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        setTimeout(function() {
                            table.ajax.reload();
                        }, 1500);
                        $('#head-cb').prop('checked', false);
                        $(".row-count").html('');
                    },
                    error: function(err) {
                        $("#message").html("Some Error Occurred!");
                        Swal.close();
                    }
                });
            }
        });
    });


    // Action update (misalnya update no_wa)
    $('#update').click(function(e) {
        e.preventDefault();

        // Hapus pesan error jika ada
        var error_ele = document.getElementsByClassName('form-text text-danger');
        if (error_ele.length > 0) {
            for (var i = error_ele.length - 1; i >= 0; i--) {
                error_ele[i].remove();
            }
        }

        // Simpan referensi tombol dan teks aslinya
        var btn = $(this);
        var originalText = btn.html();

        // Ubah tampilan tombol: nonaktifkan dan tampilkan teks dengan spinner di sebelah kanan
        btn.prop('disabled', true).html(
            'Memproses... <span class="material-symbols-outlined spinner">progress_activity</span>');

        let whatsapp = $('#mpwa_id').val();
        var data = {
            'no_wa': $('#no_wa_edit').val(),
        };

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: `/whatsapp/${whatsapp}`,
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
                        location.reload();
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


    // Action rescan (Generate QR)
    $('#action-rescan').click(function(e) {
        e.preventDefault();

        // Hapus pesan error jika ada
        var error_ele = document.getElementsByClassName('form-text text-danger');
        if (error_ele.length > 0) {
            for (var i = error_ele.length - 1; i >= 0; i--) {
                error_ele[i].remove();
            }
        }

        // Simpan referensi tombol dan teks aslinya
        var btn = $(this);
        var originalText = btn.html();

        // Ubah tampilan tombol: nonaktifkan dan tampilkan teks dengan spinner di sebelah kanan
        btn.prop('disabled', true).html(
            '<span class="material-symbols-outlined">qr_code_2</span> Generate QR <span class="material-symbols-outlined spinner">progress_activity</span>'
        );

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: `/whatsapp/device/scan/`,
            type: "POST",
            cache: false,
            dataType: "json",
            success: function(data) {
                if (data.success === true) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: `${data.message}`,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    setTimeout(function() {
                        if (data.data) {
                            var imgURL = data.data;
                            var $img = $("<img />").attr("src", imgURL);
                            $("#show_qr").html($img);
                        }
                        // Kembalikan tombol ke kondisi semula
                        btn.prop('disabled', false).html(originalText);
                    }, 1500);
                } else if (data.success === false) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Maaf Gagal..',
                        text: `${data.message}`,
                        showConfirmButton: true,
                    });
                    btn.prop('disabled', false).html(originalText);
                } else {
                    btn.prop('disabled', false).html(originalText);
                }
            },
            error: function(err) {
                $("#message").html("Some Error Occurred!");
                btn.prop('disabled', false).html(originalText);
            }
        });
    });

    $('#account_active1').on('click', function() {
        let id = $(this).data('id');
        $.ajax({
            url: `/whatsapp/template/${id}`,
            type: "GET",
            data: {
                id: id,
            },
            success: function(data) {
                $("#id").val(data.id);
                var account_active = data.account_active.replace(/<br>/gi, '\n');
                $('#account_active').val(account_active);


            }
        });
        $('#show_account_active').modal('show');
    });

    $('#invoice_terbit1').on('click', function() {
        let id = $(this).data('id');
        $.ajax({
            url: `/whatsapp/template/${id}`,
            type: "GET",
            data: {
                id: id,
            },
            success: function(data) {
                $("#id").val(data.id);
                var invoice_terbit = data.invoice_terbit.replace(/<br>/gi, '\n');
                $('#invoice_terbit').val(invoice_terbit);


            }
        });
        $('#show_invoice_terbit').modal('show');
    });

    $('#invoice_reminder1').on('click', function() {
        let id = $(this).data('id');
        $.ajax({
            url: `/whatsapp/template/${id}`,
            type: "GET",
            data: {
                id: id,
            },
            success: function(data) {
                $("#id").val(data.id);
                var invoice_reminder = data.invoice_reminder.replace(/<br>/gi, '\n');
                $('#invoice_reminder').val(invoice_reminder);
            }
        });
        $('#show_invoice_reminder').modal('show');
    });
    $('#invoice_overdue1').on('click', function() {
        let id = $(this).data('id');
        $.ajax({
            url: `/whatsapp/template/${id}`,
            type: "GET",
            data: {
                id: id,
            },
            success: function(data) {
                $("#id").val(data.id);
                var invoice_overdue = data.invoice_overdue.replace(/<br>/gi, '\n');
                $('#invoice_overdue').val(invoice_overdue);


            }
        });
        $('#show_invoice_overdue').modal('show');
    });
    $('#payment_paid1').on('click', function() {
        let id = $(this).data('id');
        $.ajax({
            url: `/whatsapp/template/${id}`,
            type: "GET",
            data: {
                id: id,
            },
            success: function(data) {
                $("#id").val(data.id);
                var payment_paid = data.payment_paid.replace(/<br>/gi, '\n');
                $('#payment_paid').val(payment_paid);
            }
        });
        $('#show_payment_paid').modal('show');
    });
    $('#payment_cancel1').on('click', function() {
        let id = $(this).data('id');
        $.ajax({
            url: `/whatsapp/template/${id}`,
            type: "GET",
            data: {
                id: id,
            },
            success: function(data) {
                $("#id").val(data.id);
                var payment_cancel = data.payment_cancel.replace(/<br>/gi, '\n');
                $('#payment_cancel').val(payment_cancel);


            }
        });
        $('#show_payment_cancel').modal('show');
    });

    $('#tiket_open_pelanggan1').on('click', function() {
        let id = $(this).data('id');
        $.ajax({
            url: `/whatsapp/template/${id}`,
            type: "GET",
            data: {
                id: id,
            },
            success: function(data) {
                $("#id").val(data.id);
                var tiket_open_pelanggan = data.tiket_open_pelanggan.replace(/<br>/gi, '\n');
                $('#tiket_open_pelanggan').val(tiket_open_pelanggan);


            }
        });
        $('#show_tiket_open_pelanggan').modal('show');
    });

    $('#tiket_open_teknisi1').on('click', function() {
        let id = $(this).data('id');
        $.ajax({
            url: `/whatsapp/template/${id}`,
            type: "GET",
            data: {
                id: id,
            },
            success: function(data) {
                $("#id").val(data.id);
                var tiket_open_teknisi = data.tiket_open_teknisi.replace(/<br>/gi, '\n');
                $('#tiket_open_teknisi').val(tiket_open_teknisi);


            }
        });
        $('#show_tiket_open_teknisi').modal('show');
    });

    $('#tiket_close_pelanggan1').on('click', function() {
        let id = $(this).data('id');
        $.ajax({
            url: `/whatsapp/template/${id}`,
            type: "GET",
            data: {
                id: id,
            },
            success: function(data) {
                $("#id").val(data.id);
                var tiket_close_pelanggan = data.tiket_close_pelanggan.replace(/<br>/gi, '\n');
                $('#tiket_close_pelanggan').val(tiket_close_pelanggan);


            }
        });
        $('#show_tiket_close_pelanggan').modal('show');
    });

    $('#tiket_close_teknisi1').on('click', function() {
        let id = $(this).data('id');
        $.ajax({
            url: `/whatsapp/template/${id}`,
            type: "GET",
            data: {
                id: id,
            },
            success: function(data) {
                $("#id").val(data.id);
                var tiket_close_teknisi = data.tiket_close_teknisi.replace(/<br>/gi, '\n');
                $('#tiket_close_teknisi').val(tiket_close_teknisi);


            }
        });
        $('#show_tiket_close_teknisi').modal('show');
    });

    // Update Account Active
    $('#updateAccountActive').click(function() {
        let id = $('#id').val();
        var data = {
            'account_active': $('#account_active').val(),
        };

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var btn = $(this);
        var originalText = btn.html();
        btn.prop('disabled', true).html(
            'Memproses... <span class="material-symbols-outlined spinner">progress_activity</span>');

        $.ajax({
            url: `/whatsapp/template/active/${id}`,
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
                        location.reload();
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

    // Update Invoice Terbit
    $('#updateInvoiceTerbit').click(function() {
        let id = $('#id').val();
        var data = {
            'invoice_terbit': $('#invoice_terbit').val(),
        };

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var btn = $(this);
        var originalText = btn.html();
        btn.prop('disabled', true).html(
            'Memproses... <span class="material-symbols-outlined spinner">progress_activity</span>');

        $.ajax({
            url: `/whatsapp/template/terbit/${id}`,
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
                        location.reload();
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

    // Update Invoice Reminder
    $('#updateInvoiceReminder').click(function() {
        let id = $('#id').val();
        var data = {
            'invoice_reminder': $('#invoice_reminder').val(),
        };

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var btn = $(this);
        var originalText = btn.html();
        btn.prop('disabled', true).html(
            'Memproses... <span class="material-symbols-outlined spinner">progress_activity</span>');

        $.ajax({
            url: `/whatsapp/template/reminder/${id}`,
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
                        location.reload();
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

    // Update Invoice Overdue
    $('#updateInvoiceOverdue').click(function() {
        let id = $('#id').val();
        var data = {
            'invoice_overdue': $('#invoice_overdue').val(),
        };

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var btn = $(this);
        var originalText = btn.html();
        btn.prop('disabled', true).html(
            'Memproses... <span class="material-symbols-outlined spinner">progress_activity</span>');

        $.ajax({
            url: `/whatsapp/template/overdue/${id}`,
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
                        location.reload();
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

    // Update Payment Paid
    $('#updatePaymentPaid').click(function() {
        let id = $('#id').val();
        var data = {
            'payment_paid': $('#payment_paid').val(),
        };

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var btn = $(this);
        var originalText = btn.html();
        btn.prop('disabled', true).html(
            'Memproses... <span class="material-symbols-outlined spinner">progress_activity</span>');

        $.ajax({
            url: `/whatsapp/template/paid/${id}`,
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
                        location.reload();
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

    // Update Payment Cancel
    $('#updatePaymentCancel').click(function() {
        let id = $('#id').val();
        var data = {
            'payment_cancel': $('#payment_cancel').val(),
        };

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var btn = $(this);
        var originalText = btn.html();
        btn.prop('disabled', true).html(
            'Memproses... <span class="material-symbols-outlined spinner">progress_activity</span>');

        $.ajax({
            url: `/whatsapp/template/cancel/${id}`,
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
                        location.reload();
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

    $('#updateOpenPelanggan').click(function() {
        let id = $('#id').val();
        var data = {
            'tiket_open_pelanggan': $('#tiket_open_pelanggan').val(),
        };

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var btn = $(this);
        var originalText = btn.html();
        btn.prop('disabled', true).html(
            'Memproses... <span class="material-symbols-outlined spinner">progress_activity</span>');

        $.ajax({
            url: `/whatsapp/template/tiketOpenPelanggan/${id}`,
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
                        location.reload();
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

    $('#updateOpenTeknisi').click(function() {
        let id = $('#id').val();
        var data = {
            'tiket_open_teknisi': $('#tiket_open_teknisi').val(),
        };

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var btn = $(this);
        var originalText = btn.html();
        btn.prop('disabled', true).html(
            'Memproses... <span class="material-symbols-outlined spinner">progress_activity</span>');

        $.ajax({
            url: `/whatsapp/template/tiketOpenTeknisi/${id}`,
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
                        location.reload();
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

    $('#updateClosedPelanggan').click(function() {
        let id = $('#id').val();
        var data = {
            'tiket_close_pelanggan': $('#tiket_close_pelanggan').val(),
        };

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var btn = $(this);
        var originalText = btn.html();
        btn.prop('disabled', true).html(
            'Memproses... <span class="material-symbols-outlined spinner">progress_activity</span>');

        $.ajax({
            url: `/whatsapp/template/tiketClosePelanggan/${id}`,
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
                        location.reload();
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

    $('#updateClosedTeknisi').click(function() {
        let id = $('#id').val();
        var data = {
            'tiket_close_teknisi': $('#tiket_close_teknisi').val(),
        };

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var btn = $(this);
        var originalText = btn.html();
        btn.prop('disabled', true).html(
            'Memproses... <span class="material-symbols-outlined spinner">progress_activity</span>');

        $.ajax({
            url: `/whatsapp/template/tiketCloseTeknisi/${id}`,
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
                        location.reload();
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


    $('#tipe').change(function() {
        let tipe = $(this).val();
        if (tipe === 'all') {
            $('#show_suspend').hide();
            $('#show_byarea').hide();
            $('#show_byodp').hide();
            $('#show_all').show();
            $.ajax({
                url: `/whatsapp/broadcast/getAllUserActive`,
                type: "GET",
                cache: false,
                success: function(data) {
                    $('#fill_jmlpelanggan_all').html(data.countuser);
                    let wa = [];
                    $.each(data.data, function(index, row) {
                        if (row.wa !== null) {
                            wa.push(row.wa);
                        }
                    });
                    $('#sendBroadcast').click(function(e) {
                        Swal.fire({
                            title: "Apakah anda yakin?",
                            icon: 'warning',
                            text: "Pesan akan dikirim ke semua pelanggan berdasarkan tipe broadcast yang dipilih",
                            showCancelButton: true,
                            reverseButtons: true,
                            confirmButtonText: "Ya, Kirim!",
                            cancelButtonText: "Batal",
                            confirmButtonColor: "#d33",
                        }).then(function(result) {
                            if (result.isConfirmed) {
                                // Tampilkan SweetAlert loading tanpa timer
                                Swal.fire({
                                    title: "Mengirim pesan",
                                    icon: "info",
                                    html: "Mengirim pesan broadcast. Harap tunggu dan jangan di close...",
                                    allowOutsideClick: false,
                                    showConfirmButton: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });
                                // Nonaktifkan tombol dan tampilkan spinner di sebelah kanan teks
                                var btn = $('#sendBroadcast');
                                var originalText = btn.html();
                                btn.prop('disabled', true).html(
                                    'Kirim Broadcast <span class="material-symbols-outlined spinner">sync_arrow_up</span>'
                                );
                                var sendData = {
                                    'tipe': $('#tipe').val(),
                                    'message': $('#message_all').val(),
                                    'wa': wa
                                };
                                $.ajaxSetup({
                                    headers: {
                                        'X-CSRF-TOKEN': $(
                                                'meta[name="csrf-token"]')
                                            .attr('content')
                                    }
                                });
                                $.ajax({
                                    url: `/whatsapp/broadcast/send`,
                                    type: "POST",
                                    cache: false,
                                    data: sendData,
                                    dataType: "json",
                                    success: function(data) {
                                        if (data.success) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Success',
                                                text: `${data.message}`,
                                                showConfirmButton: true,
                                            });
                                            setTimeout(function() {
                                                location
                                                    .reload();
                                            }, 2000);
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Failed',
                                                text: `Something wen't wrong, please retry`,
                                                showConfirmButton: false,
                                                timer: 1500
                                            });
                                            btn.prop('disabled', false)
                                                .html(originalText);
                                        }
                                    },
                                    error: function(err) {
                                        $("#message").html(
                                            "Some Error Occurred!");
                                        btn.prop('disabled', false)
                                            .html(originalText);
                                    }
                                });
                            }
                        });
                    });
                }
            });
        } else if (tipe === 'suspend') {
            $('#show_all').hide();
            $('#show_byarea').hide();
            $('#show_byodp').hide();
            $('#show_suspend').show();
            $.ajax({
                url: `/whatsapp/broadcast/getAllUserSuspend`,
                type: "GET",
                cache: false,
                success: function(data) {
                    $('#fill_jmlpelanggan_suspend').html(data.countuser);
                    let wa = [];
                    $.each(data.data, function(index, row) {
                        if (row.wa !== null) {
                            wa.push(row.wa);
                        }
                    });
                    $('#sendBroadcast').click(function(e) {
                        Swal.fire({
                            title: "Apakah anda yakin?",
                            icon: 'warning',
                            text: "Pesan akan dikirim ke semua pelanggan berdasarkan tipe broadcast yang dipilih",
                            showCancelButton: true,
                            reverseButtons: true,
                            confirmButtonText: "Ya, Kirim!",
                            cancelButtonText: "Batal",
                            confirmButtonColor: "#d33",
                        }).then(function(result) {
                            if (result.isConfirmed) {
                                // Tampilkan SweetAlert loading tanpa timer
                                Swal.fire({
                                    title: "Mengirim pesan",
                                    icon: "info",
                                    html: "Mengirim pesan broadcast. Harap tunggu dan jangan di close...",
                                    allowOutsideClick: false,
                                    showConfirmButton: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });
                                var btn = $('#sendBroadcast');
                                var originalText = btn.html();
                                btn.prop('disabled', true).html(
                                    'Kirim Broadcast <span class="material-symbols-outlined spinner">sync_arrow_up</span>'
                                );
                                var sendData = {
                                    'tipe': $('#tipe').val(),
                                    'message': $('#message_suspend').val(),
                                    'wa': wa
                                };
                                $.ajaxSetup({
                                    headers: {
                                        'X-CSRF-TOKEN': $(
                                                'meta[name="csrf-token"]')
                                            .attr('content')
                                    }
                                });
                                $.ajax({
                                    url: `/whatsapp/broadcast/send`,
                                    type: "POST",
                                    cache: false,
                                    data: sendData,
                                    dataType: "json",
                                    success: function(data) {
                                        if (data.success) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Success',
                                                text: `${data.message}`,
                                                showConfirmButton: true,
                                            });
                                            setTimeout(function() {
                                                location
                                                    .reload();
                                            }, 2000);
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Failed',
                                                text: `Something wen't wrong, please retry`,
                                                showConfirmButton: false,
                                                timer: 1500
                                            });
                                            btn.prop('disabled', false)
                                                .html(originalText);
                                        }
                                    },
                                    error: function(err) {
                                        $("#message").html(
                                            "Some Error Occurred!");
                                        btn.prop('disabled', false)
                                            .html(originalText);
                                    }
                                });
                            }
                        });
                    });
                }
            });
        } else if (tipe === 'byarea') {
            $('#show_suspend').hide();
            $('#show_all').hide();
            $('#show_byodp').hide();
            $('#show_byarea').show();
        } else if (tipe === 'byodp') {
            $('#show_byarea').hide();
            $('#show_suspend').hide();
            $('#show_all').hide();
            $('#show_byodp').show();
        } else if (tipe === 'owner_all') {
            $('#show_owner_all').show();
            $('#show_owner_trial').hide();
            $('#show_owner_expired').hide();
            $.ajax({
                url: `/whatsapp/broadcast/getAllUserActive_owner`,
                type: "GET",
                cache: false,
                success: function(data) {
                    $('#fill_jmlpelanggan_owner_all').html(data.countuser);
                    let wa = [];
                    $.each(data.data, function(index, row) {
                        if (row.whatsapp !== null) {
                            wa.push(row.whatsapp);
                        }
                    });
                    $('#sendBroadcast').click(function(e) {
                        Swal.fire({
                            title: "Apakah anda yakin?",
                            icon: 'warning',
                            text: "Pesan akan dikirim ke semua pelanggan berdasarkan tipe broadcast yang dipilih",
                            showCancelButton: true,
                            reverseButtons: true,
                            confirmButtonText: "Ya, Kirim!",
                            cancelButtonText: "Batal",
                            confirmButtonColor: "#d33",
                        }).then(function(result) {
                            if (result.isConfirmed) {
                                // Tampilkan SweetAlert loading tanpa timer
                                Swal.fire({
                                    title: "Mengirim pesan",
                                    icon: "info",
                                    html: "Mengirim pesan broadcast. Harap tunggu dan jangan di close...",
                                    allowOutsideClick: false,
                                    showConfirmButton: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });
                                // Nonaktifkan tombol dan tampilkan spinner di sebelah kanan teks
                                var btn = $('#sendBroadcast');
                                var originalText = btn.html();
                                btn.prop('disabled', true).html(
                                    'Kirim Broadcast <span class="material-symbols-outlined spinner">sync_arrow_up</span>'
                                );
                                var sendData = {
                                    'tipe': $('#tipe').val(),
                                    'message': $('#message_all_owner').val(),
                                    'wa': wa
                                };
                                $.ajaxSetup({
                                    headers: {
                                        'X-CSRF-TOKEN': $(
                                                'meta[name="csrf-token"]')
                                            .attr('content')
                                    }
                                });
                                $.ajax({
                                    url: `/whatsapp/broadcast/send`,
                                    type: "POST",
                                    cache: false,
                                    data: sendData,
                                    dataType: "json",
                                    success: function(data) {
                                        if (data.success) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Success',
                                                text: `${data.message}`,
                                                showConfirmButton: true,
                                            });
                                            setTimeout(function() {
                                                location
                                                    .reload();
                                            }, 2000);
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Failed',
                                                text: `Something wen't wrong, please retry`,
                                                showConfirmButton: false,
                                                timer: 1500
                                            });
                                            btn.prop('disabled', false)
                                                .html(originalText);
                                        }
                                    },
                                    error: function(err) {
                                        $("#message").html(
                                            "Some Error Occurred!");
                                        btn.prop('disabled', false)
                                            .html(originalText);
                                    }
                                });
                            }
                        });
                    });
                }
            });
        } else if (tipe === 'owner_trial') {
            $('#show_owner_trial').show();
            $('#show_owner_all').hide();
            $('#show_owner_expired').hide();
            $.ajax({
                url: `/whatsapp/broadcast/getAllUserTrial_owner`,
                type: "GET",
                cache: false,
                success: function(data) {
                    $('#fill_jmlpelanggan_owner_trial').html(data.countuser);
                    let wa = [];
                    $.each(data.data, function(index, row) {
                        if (row.whatsapp !== null) {
                            wa.push(row.whatsapp);
                        }
                    });
                    $('#sendBroadcast').click(function(e) {
                        Swal.fire({
                            title: "Apakah anda yakin?",
                            icon: 'warning',
                            text: "Pesan akan dikirim ke semua pelanggan berdasarkan tipe broadcast yang dipilih",
                            showCancelButton: true,
                            reverseButtons: true,
                            confirmButtonText: "Ya, Kirim!",
                            cancelButtonText: "Batal",
                            confirmButtonColor: "#d33",
                        }).then(function(result) {
                            if (result.isConfirmed) {
                                // Tampilkan SweetAlert loading tanpa timer
                                Swal.fire({
                                    title: "Mengirim pesan",
                                    icon: "info",
                                    html: "Mengirim pesan broadcast. Harap tunggu dan jangan di close...",
                                    allowOutsideClick: false,
                                    showConfirmButton: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });
                                // Nonaktifkan tombol dan tampilkan spinner di sebelah kanan teks
                                var btn = $('#sendBroadcast');
                                var originalText = btn.html();
                                btn.prop('disabled', true).html(
                                    'Kirim Broadcast <span class="material-symbols-outlined spinner">sync_arrow_up</span>'
                                );
                                var sendData = {
                                    'tipe': $('#tipe').val(),
                                    'message': $('#message_trial_owner').val(),
                                    'wa': wa
                                };
                                $.ajaxSetup({
                                    headers: {
                                        'X-CSRF-TOKEN': $(
                                                'meta[name="csrf-token"]')
                                            .attr('content')
                                    }
                                });
                                $.ajax({
                                    url: `/whatsapp/broadcast/send`,
                                    type: "POST",
                                    cache: false,
                                    data: sendData,
                                    dataType: "json",
                                    success: function(data) {
                                        if (data.success) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Success',
                                                text: `${data.message}`,
                                                showConfirmButton: true,
                                            });
                                            setTimeout(function() {
                                                location
                                                    .reload();
                                            }, 2000);
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Failed',
                                                text: `Something wen't wrong, please retry`,
                                                showConfirmButton: false,
                                                timer: 1500
                                            });
                                            btn.prop('disabled', false)
                                                .html(originalText);
                                        }
                                    },
                                    error: function(err) {
                                        $("#message").html(
                                            "Some Error Occurred!");
                                        btn.prop('disabled', false)
                                            .html(originalText);
                                    }
                                });
                            }
                        });
                    });
                }
            });
        } else if (tipe === 'owner_expired') {
            $('#show_owner_expired').show();
            $('#show_owner_all').hide();
            $('#show_owner_trial').hide();
            $.ajax({
                url: `/whatsapp/broadcast/getAllUserExpired_owner`,
                type: "GET",
                cache: false,
                success: function(data) {
                    $('#fill_jmlpelanggan_owner_expired').html(data.countuser);
                    let wa = [];
                    $.each(data.data, function(index, row) {
                        if (row.whatsapp !== null) {
                            wa.push(row.whatsapp);
                        }
                    });
                    $('#sendBroadcast').click(function(e) {
                        Swal.fire({
                            title: "Apakah anda yakin?",
                            icon: 'warning',
                            text: "Pesan akan dikirim ke semua pelanggan berdasarkan tipe broadcast yang dipilih",
                            showCancelButton: true,
                            reverseButtons: true,
                            confirmButtonText: "Ya, Kirim!",
                            cancelButtonText: "Batal",
                            confirmButtonColor: "#d33",
                        }).then(function(result) {
                            if (result.isConfirmed) {
                                // Tampilkan SweetAlert loading tanpa timer
                                Swal.fire({
                                    title: "Mengirim pesan",
                                    icon: "info",
                                    html: "Mengirim pesan broadcast. Harap tunggu dan jangan di close...",
                                    allowOutsideClick: false,
                                    showConfirmButton: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });
                                // Nonaktifkan tombol dan tampilkan spinner di sebelah kanan teks
                                var btn = $('#sendBroadcast');
                                var originalText = btn.html();
                                btn.prop('disabled', true).html(
                                    'Kirim Broadcast <span class="material-symbols-outlined spinner">sync_arrow_up</span>'
                                );
                                var sendData = {
                                    'tipe': $('#tipe').val(),
                                    'message': $('#message_expired_owner')
                                        .val(),
                                    'wa': wa
                                };
                                $.ajaxSetup({
                                    headers: {
                                        'X-CSRF-TOKEN': $(
                                                'meta[name="csrf-token"]')
                                            .attr('content')
                                    }
                                });
                                $.ajax({
                                    url: `/whatsapp/broadcast/send`,
                                    type: "POST",
                                    cache: false,
                                    data: sendData,
                                    dataType: "json",
                                    success: function(data) {
                                        if (data.success) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Success',
                                                text: `${data.message}`,
                                                showConfirmButton: true,
                                            });
                                            setTimeout(function() {
                                                location
                                                    .reload();
                                            }, 2000);
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Failed',
                                                text: `Something wen't wrong, please retry`,
                                                showConfirmButton: false,
                                                timer: 1500
                                            });
                                            btn.prop('disabled', false)
                                                .html(originalText);
                                        }
                                    },
                                    error: function(err) {
                                        $("#message").html(
                                            "Some Error Occurred!");
                                        btn.prop('disabled', false)
                                            .html(originalText);
                                    }
                                });
                            }
                        });
                    });
                }
            });
        } else {
            $('#show_byodp').hide();
            $('#show_all').hide();
            $('#show_suspend').hide();
            $('#show_byarea').hide();
        }
    });

    $('#kode_area').change(function() {
        let kode_area = $(this).val();
        $.ajax({
            url: `/whatsapp/broadcast/getAllUserArea`,
            type: "GET",
            cache: false,
            data: {
                kode_area: kode_area,
                '_token': '{{ csrf_token() }}'
            },
            success: function(data) {
                $('#fill_area').html($('#kode_area').val());
                $('#fill_jmlpelanggan_area').html(data.countuser);
                let wa = [];
                $.each(data.data, function(index, row) {
                    if (row.wa !== null) {
                        wa.push(row.wa);
                    }
                });

                // Bind click untuk broadcast berdasarkan area
                $('#sendBroadcast').off('click').on('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: "Apakah anda yakin?",
                        icon: 'warning',
                        text: "Pesan akan dikirim ke semua pelanggan berdasarkan tipe broadcast yang dipilih",
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonText: "Ya, Kirim!",
                        cancelButtonText: "Batal",
                        confirmButtonColor: "#d33",
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            // Tampilkan SweetAlert loading tanpa timer
                            Swal.fire({
                                title: "Mengirim pesan",
                                icon: "info",
                                html: "Mengirim pesan broadcast. Harap tunggu dan jangan di close...",
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            var btn = $('#sendBroadcast');
                            var originalText = btn.html();
                            btn.prop('disabled', true).html(
                                'Kirim Broadcast <span class="material-symbols-outlined spinner">sync_arrow_up</span>'
                            );
                            var sendData = {
                                'tipe': $('#tipe').val(),
                                'message': $('#message_area').val(),
                                'wa': wa
                            };
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $(
                                        'meta[name="csrf-token"]').attr(
                                        'content')
                                }
                            });
                            $.ajax({
                                url: `/whatsapp/broadcast/send`,
                                type: "POST",
                                cache: false,
                                data: sendData,
                                dataType: "json",
                                success: function(data) {
                                    if (data.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success',
                                            text: `${data.message}`,
                                            showConfirmButton: true,
                                        });
                                        setTimeout(function() {
                                            location.reload();
                                        }, 2000);
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Failed',
                                            text: `Something wen't wrong, please retry`,
                                            showConfirmButton: false,
                                            timer: 1500
                                        });
                                        btn.prop('disabled', false)
                                            .html(originalText);
                                    }
                                },
                                error: function(err) {
                                    $("#message").html(
                                        "Some Error Occurred!");
                                    btn.prop('disabled', false).html(
                                        originalText);
                                }
                            });
                        }
                    });
                });
            }
        });
    });

    $('#kode_odp').change(function() {
        let kode_odp = $(this).val();
        $.ajax({
            url: `/whatsapp/broadcast/getAllUserOdp`,
            type: "GET",
            cache: false,
            data: {
                kode_odp: kode_odp,
                '_token': '{{ csrf_token() }}'
            },
            success: function(data) {
                $('#fill_odp').html($('#kode_odp').val());
                $('#fill_jmlpelanggan_odp').html(data.countuser);
                let wa = [];
                $.each(data.data, function(index, row) {
                    if (row.wa !== null) {
                        wa.push(row.wa);
                    }
                });

                // Bind click untuk broadcast berdasarkan ODP
                $('#sendBroadcast').off('click').on('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: "Apakah anda yakin?",
                        icon: 'warning',
                        text: "Pesan akan dikirim ke semua pelanggan berdasarkan tipe broadcast yang dipilih",
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonText: "Ya, Kirim!",
                        cancelButtonText: "Batal",
                        confirmButtonColor: "#d33",
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            // Tampilkan SweetAlert loading tanpa timer
                            Swal.fire({
                                title: "Mengirim pesan",
                                icon: "info",
                                html: "Mengirim pesan broadcast. Harap tunggu dan jangan di close...",
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            var btn = $('#sendBroadcast');
                            var originalText = btn.html();
                            btn.prop('disabled', true).html(
                                'Kirim Broadcast <span class="material-symbols-outlined spinner">sync_arrow_up</span>'
                            );
                            var sendData = {
                                'tipe': $('#tipe').val(),
                                'message': $('#message_odp').val(),
                                'wa': wa
                            };
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $(
                                        'meta[name="csrf-token"]').attr(
                                        'content')
                                }
                            });
                            $.ajax({
                                url: `/whatsapp/broadcast/send`,
                                type: "POST",
                                cache: false,
                                data: sendData,
                                dataType: "json",
                                success: function(data) {
                                    if (data.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success',
                                            text: `${data.message}`,
                                            showConfirmButton: true,
                                        });
                                        setTimeout(function() {
                                            location.reload();
                                        }, 2000);
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Failed',
                                            text: `Something wen't wrong, please retry`,
                                            showConfirmButton: false,
                                            timer: 1500
                                        });
                                        btn.prop('disabled', false)
                                            .html(originalText);
                                    }
                                },
                                error: function(err) {
                                    $("#message").html(
                                        "Some Error Occurred!");
                                    btn.prop('disabled', false).html(
                                        originalText);
                                }
                            });
                        }
                    });
                });
            }
        });
    });



    // $("#broadcast").on("hidden.bs.modal", function() {
    //     $('#sendBroadcast').attr("disabled", false);
    //     $("#spinner").remove();
    // });

    // $('#kode_area').select2({
    //     dropdownParent: $("#broadcast"),
    //     width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
    //     placeholder: $(this).data('placeholder'),
    // });
    // $('#kode_odp').select2({
    //     dropdownParent: $("#broadcast"),
    //     width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
    //     placeholder: $(this).data('placeholder'),
    // });

    new Choices(
        '#kode_area', {
            allowHTML: true,
            removeItemButton: true,
            placeholderValue: 'Pilih POP',
        }
    );
    new Choices(
        '#kode_odp', {
            allowHTML: true,
            removeItemButton: true,
            placeholderValue: 'Pilih Kode ODP',
        }
    );
</script>
@endpush
