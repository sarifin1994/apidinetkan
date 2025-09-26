@extends('backend.layouts.app')
@section('main')
@section('title', 'Billing Setting')
<!-- Content -->
<div class="container-lg">
    <!-- Page header -->
    <div class="row align-items-center mb-7">
        <div class="col-auto">
            <!-- Avatar -->
            <div class="avatar avatar-xl rounded text-primary">
                <span class="fs-2 material-symbols-outlined">
                    credit_card_gear
                    </span>
            </div>
        </div>
        <div class="col">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a class="text-body-secondary" href="#">Setting</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Billing</li>
                </ol>
            </nav>
            <!-- Heading -->
            <h1 class="fs-4 mb-0">Billing Setting</h1>
        </div>
    </div>

    <div class="row">
        <!-- Card Pengaturan Billing -->
        <div class="col-lg-8">
            <div class="card border rounded">
                <div class="card-header fw-semibold bg-light">
                    
                    Pengaturan Billing
                </div>
                <div class="card-body">
                    <input type="hidden" id="billing_id">
                    
                    <!-- Tanggal Jatuh Tempo -->
                    <div class="form-group row">
                        <label for="due_bc" class="col-sm-10">Tanggal jatuh tempo untuk tipe pembayaran <span class="text-primary">Pascabayar - Billing Cycle</span></label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control border-dark-subtle bg-light" id="due_bc" name="due_bc" placeholder="20">
                        </div>
                    </div>
                    <hr />
    
                    <!-- Generate Invoice -->
                    <div class="form-group row">
                        <label for="inv_fd" class="col-sm-10">Berapa hari generate invoice untuk siklus tagihan <span class="text-primary">Fixed Date & Renewable</span><br><span class="text-primary">Isi 7 paling cepat</span></label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control border-dark-subtle bg-light" id="inv_fd" name="inv_fd" placeholder="1">
                        </div>
                    </div>
                    <hr />
    
    
                    {{-- <!-- Waktu Suspend -->
                    <div class="form-group row">
                        <label for="suspend_time" class="col-sm-9">Waktu pelanggan disuspend / isolir oleh sistem<br><span class="text-primary">Isi format berikut 00:00:00</span></label>
                        <div class="col-sm-3">
                            <input type="text" class="form-control border-dark-subtle bg-light" id="suspend_time" name="suspend_time" placeholder="06:00:00">
                        </div>
                    </div>
                    <hr /> --}}
    
                    <!-- Notifikasi Reminder -->
                    <div class="form-group row">
                        <label for="notif_ir" class="col-sm-10">Berapa hari notifikasi reminder dikirim sebelum jatuh tempo<span class="text-primary"><br>Isi 0 jika tidak pernah</span></label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control border-dark-subtle bg-light" id="notif_ir" name="notif_ir" placeholder="0">
                        </div>
                    </div>
                    <hr />

                      <!-- Toleransi Suspend -->
                      <div class="form-group row">
                        <label for="suspend_date" class="col-sm-10">Toleransi hari sebelum user disuspend oleh sistem<br><span class="text-primary">isi 0 jika tidak pernah</span></label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control border-dark-subtle bg-light" id="suspend_date" name="suspend_date" placeholder="1">
                        </div>
                    </div>
                    <hr />
    
                    <!-- Checkbox Notifikasi -->
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="notif_it">
                        <label class="form-check-label" for="notif_it">Kirim notifikasi saat terbit invoice<br><small><i>Saat invoice dibuat manual / digenerate oleh sistem</i></small></label>
                    </div>
                    <hr />
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="notif_ps">
                        <label class="form-check-label" for="notif_ps">Kirim notifikasi status pembayaran<br><small><i>Saat invoice dibayar / dibatalkan</i></small></label>
                    </div>
                    <hr />
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="notif_sm">
                        <label class="form-check-label" for="notif_sm">Kirim notifikasi status pelanggan<br><small><i>Saat pelanggan baru aktif / disuspend oleh sistem</i></small></label>
                    </div>
                    <hr />
    
                    <!-- Tombol Simpan -->
                    <div class="text-end">
                        <button class="btn btn-primary" id="update" type="submit">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    
        <!-- Card Informasi Billing -->
        <div class="col-lg-4">
            <div class="card border rounded">
                <div class="card-header fw-semibold bg-light">
                    <span class="material-symbols-outlined align-middle">contact_support</span>
                    Informasi Billing
                </div>
                <div class="card-body">
                    <ol class="text-muted">
                        <li><i>PASCABAYAR</i> adalah tipe tagihan pakai dulu baru bayar dengan 2 opsi siklus: <i>BILLING CYCLE</i> dan <i>FIXED DATE</i>.</li>
                        <li><i>PRABAYAR</i> adalah tipe tagihan bayar dulu baru pakai, hanya tersedia opsi <i>FIXED DATE</i>.</li>
                        <li><i>BILLING CYCLE</i> memiliki jatuh tempo tetap setiap bulan (prorate), invoice dibuat otomatis setiap tanggal 1.</li>
                        <li><i>FIXED DATE</i> menggunakan jatuh tempo berdasarkan tanggal registrasi, invoice dibuat minimal 7 hari sebelumnya.</li>
                        <li>Pastikan nomor WhatsApp di menu <b><a href="/whatsapp">WhatsApp</a></b> dalam status <span class="text-success">CONNECTED</span> agar notifikasi billing berjalan lancar.</li>
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
    el.after($('<div class="form-text text-danger">' + value[0] + '</div>'));
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
