@extends('backend.layouts.app_new')
@section('main')
@section('title', 'Mailer')
<!-- Content -->
<div class="container-fluid">
    <!-- Page header -->
    <div class="row align-items-center mb-7">
        <!-- Breadcrumb and Title -->
        <div class="col-md-6 mb-3 mb-md-0">
            <h4 class="main-title">Mailer</h4>
            <ul class="app-line-breadcrumbs mb-0">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <span><i class="ti ti-settings   f-s-16"></i> Setting</span>
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="javascript:void(0)">Mailer</a>
                </li>
            </ul>
        </div>
    </div><br />



    <div class="row">
        <!-- Konfigurasi Mailer -->
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header d-flex align-items-center">
                    <i class="ti ti-mail me-2 text-primary"></i>
                    <h3 class="card-title mb-0 fs-5">Konfigurasi Mailer</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="host" class="form-label">SMTP Server <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="host" name="host" class="form-control"
                                value="{{ old('host', $setting->host ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="port" class="form-label">Port <span class="text-danger">*</span></label>
                            <input type="number" id="port" name="port" class="form-control"
                                value="{{ old('port', $setting->port ?? 587) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="encryption" class="form-label">Encryption <span
                                    class="text-danger">*</span></label>
                            <select name="encryption" id="encryption" class="form-select">
                                <option value="tls" {{ ($setting->encryption ?? '') == 'tls' ? 'selected' : '' }}>TLS
                                </option>
                                <option value="ssl" {{ ($setting->encryption ?? '') == 'ssl' ? 'selected' : '' }}>SSL
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="sender_name" class="form-label">Sender Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="sender_name" name="sender_name" class="form-control"
                                value="{{ old('sender_name', $setting->sender_name ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" id="username" name="username" class="form-control"
                                value="{{ old('username', $setting->username ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" id="password" name="password" class="form-control"
                                value="{{ old('password', $setting->password ?? '') }}" required>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" id="store" class="btn btn-primary">
                            <i class="ti ti-device-floppy me-1"></i> Simpan Konfigurasi
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Kirim Email -->
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex align-items-center">
                    <i class="ti ti-send me-2 text-success"></i>
                    <h3 class="card-title mb-0 fs-5">Test Kirim Email</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="destination" class="form-label">Email Tujuan <span
                                class="text-danger">*</span></label>
                        <input type="email" id="destination" name="destination" class="form-control" required>
                    </div>
                    <button type="submit" id="btn_test" class="btn btn-success">
                        <i class="ti ti-mail-forward me-1"></i> Kirim Test Email
                    </button>
                </div>
            </div>
        </div>
    </div>



</div>

@endsection

@push('scripts')
<script type="text/javascript">
    $('#store').click(function(e) {
        e.preventDefault();

        var data = {
            'host': $('#host').val(),
            'port': $('#port').val(),
            'encryption': $('#encryption').val(),
            'username': $('#username').val(),
            'sender_name': $('#sender_name').val(),
            'password': $('#password').val(),
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
            'Memproses... <i class="ti ti-loader-2 ti-spin ms-1"></i>');

        // Proses AJAX
        $.ajax({
            url: `{{ route('smtp.update') }}`,
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
                } else {
                    $.each(data.error, function(key, value) {
                        var el = $(document).find('[name="' + key + '"]');
                        el.after($('<div class="form-text text-danger">' + value[0] +
                            '</div>'));
                    });
                }
            },
            error: function(err) {
                btn.prop('disabled', false).html(originalText);
                // $("#message").html("Some Error Occurred!");
                $.each(data.error, function(key, value) {
                    var el = $(document).find('[name="' + key + '"]');
                    el.after($('<div class="form-text text-danger">' + value[0] +
                    '</div>'));
                });
            }
        });
    });


    $('#btn_test').click(function(e) {
        e.preventDefault();

        var data = {
            'destination': $('#destination').val()
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
            'Memproses... <i class="ti ti-loader-2 ti-spin ms-1"></i>');

        // Proses AJAX
        $.ajax({
            url: `{{ route('smtp.test') }}`,
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
                } else {
                    $.each(data.error, function(key, value) {
                        var el = $(document).find('[name="' + key + '"]');
                        el.after($('<div class="form-text text-danger">' + value[0] +
                            '</div>'));
                    });
                }
            },
            error: function(err) {
                btn.prop('disabled', false).html(originalText);
                // $("#message").html("Some Error Occurred!");
                $.each(data.error, function(key, value) {
                    var el = $(document).find('[name="' + key + '"]');
                    el.after($('<div class="form-text text-danger">' + value[0] +
                    '</div>'));
                });
            }
        });
    });
</script>
@endpush
