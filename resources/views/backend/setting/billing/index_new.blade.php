@extends('backend.layouts.app_new')
@section('main')
@section('title', 'Billing Setting')
<!-- Content -->
<div class="container-fluid">
    <!-- Page header -->
    <div class="row align-items-center mb-7">
        <!-- Breadcrumb and Title -->
        <div class="col-md-6 mb-3 mb-md-0">
            <h4 class="main-title">Billing Setting</h4>
            <ul class="app-line-breadcrumbs mb-0">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <span><i class="ti ti-settings   f-s-16"></i> Setting</span>
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="javascript:void(0)">Billing</a>
                </li>
            </ul>
        </div>
    </div><br />

    <div class="row">
        <!-- Pengaturan Billing -->
        <div class="col-lg-8 mt-3">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white fw-semibold">
                    <i class="ti ti-file-invoice me-1"></i> Pengaturan Billing
                </div>
                <div class="card-body">
                    <input type="hidden" id="billing_id" />

                    <!-- Tanggal Jatuh Tempo -->
                    <div class="row align-items-center mb-3">
                        <label class="col-md-10 col-form-label">
                            Tanggal jatuh tempo untuk tipe pembayaran
                            <span class="text-primary">Pascabayar - Billing Cycle</span>
                        </label>
                        <div class="col-md-2">
                            <input type="text" class="form-control" id="due_bc" name="due_bc" placeholder="20">
                        </div>
                    </div>

                    <!-- Generate Invoice -->
                    <div class="row align-items-center mb-3">
                        <label class="col-md-10 col-form-label">
                            Berapa hari generate invoice untuk siklus tagihan
                            <span class="text-primary">Fixed Date & Renewable</span><br>
                            <small class="text-muted">Isi 7 paling cepat</small>
                        </label>
                        <div class="col-md-2">
                            <input type="text" class="form-control" id="inv_fd" name="inv_fd" placeholder="1">
                        </div>
                    </div>

                    <!-- Reminder -->
                    <div class="row align-items-center mb-3">
                        <label class="col-md-10 col-form-label">
                            Berapa hari notifikasi reminder dikirim sebelum jatuh tempo<br>
                            <small class="text-muted">Isi 0 jika tidak pernah</small>
                        </label>
                        <div class="col-md-2">
                            <input type="text" class="form-control" id="notif_ir" name="notif_ir" placeholder="0">
                        </div>
                    </div>

                    <!-- Suspend -->
                    <div class="row align-items-center mb-3">
                        <label class="col-md-10 col-form-label">
                            Toleransi hari sebelum user disuspend oleh sistem<br>
                            <small class="text-muted">Isi 0 jika tidak pernah</small>
                        </label>
                        <div class="col-md-2">
                            <input type="text" class="form-control" id="suspend_date" name="suspend_date"
                                placeholder="1">
                        </div>
                    </div>

                    <hr>

                    <!-- Checkbox Section -->
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="notif_it">
                        <label class="form-check-label" for="notif_it">
                            Kirim notifikasi saat terbit invoice<br>
                            <small><i>Saat invoice dibuat manual / digenerate oleh sistem</i></small>
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="notif_ps">
                        <label class="form-check-label" for="notif_ps">
                            Kirim notifikasi status pembayaran<br>
                            <small><i>Saat invoice dibayar / dibatalkan</i></small>
                        </label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="notif_sm">
                        <label class="form-check-label" for="notif_sm">
                            Kirim notifikasi status pelanggan<br>
                            <small><i>Saat pelanggan baru aktif / disuspend oleh sistem</i></small>
                        </label>
                    </div>

                    <div class="text-end">
                        <button class="btn btn-primary" id="update" type="submit">
                            <i class="ti ti-device-floppy me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi Billing -->
        <div class="col-lg-4 mt-3">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white fw-semibold">
                    <i class="ti ti-info-circle me-1"></i> Informasi Billing
                </div>
                <div class="card-body">
                    <ol class="text-muted ps-3">
                        <li><strong>PASCABAYAR</strong> = pakai dulu baru bayar, tersedia <i>Billing Cycle</i> &
                            <i>Fixed Date</i>.</li>
                        <li><strong>PRABAYAR</strong> = bayar dulu baru pakai, hanya <i>Fixed Date</i>.</li>
                        <li><i>Billing Cycle</i>: tempo tetap tiap bulan, invoice otomatis tiap tanggal 1.</li>
                        <li><i>Fixed Date</i>: tempo berdasar registrasi, invoice dibuat minimal 7 hari sebelum tempo.
                        </li>
                        <li>Pastikan nomor WhatsApp di <b><a href="/whatsapp">menu WhatsApp</a></b> status <span
                                class="text-success">CONNECTED</span> agar notifikasi berjalan lancar.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>


</div>
@endsection
@push('scripts')
<script type="text/javascript">
    $(document).ready(function() {
        //fetch detail post with ajax
        $.ajax({
            url: '{{ url()->current() }}',
            type: "GET",
            cache: false,
            success: function(response) {
                $('#billing_id').val(response.data.id);
                $('#due_bc').val(response.data.due_bc);
                $('#inv_fd').val(response.data.inv_fd);
                $('#suspend_date').val(response.data.suspend_date);
                $('#suspend_time').val(response.data.suspend_time);
                $('#notif_ir').val(response.data.notif_ir);
                if (response.data.notif_it === 1) {
                    $('#notif_it').attr('checked', true);
                    $('#notif_it').val('1');
                } else {
                    $('#notif_it').attr('checked', false);
                    $('#notif_it').val('0');
                };
                if (response.data.notif_ps === 1) {
                    $('#notif_ps').attr('checked', true);
                    $('#notif_ps').val('1');
                } else {
                    $('#notif_ps').attr('checked', false);
                    $('#notif_ps').val('0');
                };
                if (response.data.notif_sm === 1) {
                    $('#notif_sm').attr('checked', true);
                    $('#notif_sm').val('1');
                } else {
                    $('#notif_sm').attr('checked', false);
                    $('#notif_sm').val('0');
                };
            }
        });
    });

    $("#notif_it").click(function() {
        if ($("#notif_it").prop("checked")) {
            $("#notif_it").val(1);
        } else {
            $("#notif_it").val(0);
        }
    });

    $("#notif_ps").click(function() {
        if ($("#notif_ps").prop("checked")) {
            $("#notif_ps").val(1);
        } else {
            $("#notif_ps").val(0);
        }
    });

    $("#notif_sm").click(function() {
        if ($("#notif_sm").prop("checked")) {
            $("#notif_sm").val(1);
        } else {
            $("#notif_sm").val(0);
        }
    });

    $('#update').click(function() {
        let billing = $('#billing_id').val();

        // collect data by id
        var data = {
            'due_bc': $('#due_bc').val(),
            'inv_fd': $('#inv_fd').val(),
            'suspend_date': $('#suspend_date').val(),
            'suspend_time': $('#suspend_time').val(),
            'notif_ir': $('#notif_ir').val(),
            'notif_it': $('#notif_it').val(),
            'notif_ps': $('#notif_ps').val(),
            'notif_sm': $('#notif_sm').val(),
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: `/setting/billing/${billing}`,
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
                        location.reload()
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
                $("#message").html("Some Error Occurred!")
            }

        });

    });
</script>
@endpush
