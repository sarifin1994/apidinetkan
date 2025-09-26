@extends('backend.layouts.app')
@section('main')
@section('title', 'Mailer')
<!-- Content -->
<div class="container-lg">
    <!-- Page header -->
    <div class="row align-items-center mb-7">
        <div class="col-auto">
            <!-- Avatar -->
            <div class="avatar avatar-xl rounded text-primary">
                <span class="fs-2 material-symbols-outlined">
                    meeting_room
                    </span>
            </div>
        </div>
        <div class="col">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a class="text-body-secondary" href="#">Setting</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Mailer</li>
                </ol>
            </nav>

            <!-- Heading -->
            <h1 class="fs-4 mb-0">Mailer</h1>
        </div>
    </div>



    <div class="row">
        <!-- Card Informasi Perusahaan -->
        <div class="col-md-12 d-flex align-items-stretch mt-3">
            <div class="card border rounded w-100">
                <div class="card-header fw-semibold bg-light">
                    <span class="material-symbols-outlined align-middle">business</span>
                    Mailer
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-6 mb-3">
                            <label for="host" class="mb-1">SMTP Server <small class="text-danger">*</small></label>
                            <input class="form-control" name="host" id="host" value="{{ old('host', $setting->host ?? '') }}" required>
                        </div>
                        <div class="form-group col-md-6 mb-3">
                            <label for="port" class="mb-1">Port <small class="text-danger">*</small></label>
                            <input class="form-control" name="port" id="port" type="number" value="{{ old('port', $setting->port ?? 587) }}" required>
                        </div>
                        <div class="form-group col-md-6 mb-3">
                            <label for="encryption" class="mb-1">Encryption <small class="text-danger">*</small></label>
                            <select class="form-control" name="encryption" id="encryption">
                                <option value="tls" {{ ($setting->encryption ?? '') == 'tls' ? 'selected' : '' }}>TLS</option>
                                <option value="ssl" {{ ($setting->encryption ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6 mb-3">
                            <label for="sender_name" class="mb-1">Sender Name <small class="text-danger">*</small></label>
                            <input class="form-control" name="sender_name" id="sender_name" value="{{ old('sender_name', $setting->sender_name ?? '') }}" required>
                        </div>
                        <div class="form-group col-md-6 mb-3">
                            <label for="username" class="mb-1">Username <small class="text-danger">*</small></label>
                            <input class="form-control" name="username" id="username" value="{{ old('username', $setting->username ?? '') }}" required>
                        </div>
                        <div class="form-group col-md-6 mb-3">
                            <label for="password" class="mb-1">Password <small class="text-danger">*</small></label>
                            <input class="form-control" name="password" id="password" type="password" value="{{ old('password', $setting->password ?? '') }}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-12" style="margin-top:10px">
                            <button class="btn btn-primary" id="store" type="submit">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 d-flex align-items-stretch mt-3">
            <div class="card border rounded w-100">
                <div class="card-header fw-semibold bg-light">
                    <span class="material-symbols-outlined align-middle">business</span>
                    Test Kirim Email
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="destination" class="mb-1">Email tujuan <small class="text-danger">*</small></label>
                        <input class="form-control" name="destination" id="destination" required>
                    </div>
                    <div class="form-group mb-3">
                        <button class="btn btn-primary" id="btn_test" type="submit">Simpan</button>
                    </div>
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
    btn.prop('disabled', true).html('Memproses... <span class="material-symbols-outlined spinner">progress_activity</span>');

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
                    el.after($('<div class="form-text text-danger">' + value[0] + '</div>'));
                });
            }
        },
        error: function(err) {
            btn.prop('disabled', false).html(originalText);
            // $("#message").html("Some Error Occurred!");
            $.each(data.error, function(key, value) {
                    var el = $(document).find('[name="' + key + '"]');
                    el.after($('<div class="form-text text-danger">' + value[0] + '</div>'));
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
    btn.prop('disabled', true).html('Memproses... <span class="material-symbols-outlined spinner">progress_activity</span>');

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
                    el.after($('<div class="form-text text-danger">' + value[0] + '</div>'));
                });
            }
        },
        error: function(err) {
            btn.prop('disabled', false).html(originalText);
            // $("#message").html("Some Error Occurred!");
            $.each(data.error, function(key, value) {
                    var el = $(document).find('[name="' + key + '"]');
                    el.after($('<div class="form-text text-danger">' + value[0] + '</div>'));
                });
        }
    });
});
</script>
@endpush