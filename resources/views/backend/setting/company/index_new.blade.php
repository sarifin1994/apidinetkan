@extends('backend.layouts.app_new')
@section('main')
@section('title', 'Perusahaan')
<!-- Content -->
<div class="container-fluid">
    <!-- Page header -->
    <div class="row align-items-center mb-7">
        <!-- Breadcrumb and Title -->
        <div class="col-md-6 mb-3 mb-md-0">
            <h4 class="main-title">Perusahaan</h4>
            <ul class="app-line-breadcrumbs mb-0">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <span><i class="ti ti-settings   f-s-16"></i> Setting</span>
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="javascript:void(0)">Perusahaan</a>
                </li>
            </ul>
        </div>
    </div><br />


    <div class="row">
        <!-- Informasi Perusahaan -->
        <div class="col-lg-7 d-flex align-items-stretch mt-3">
            <div class="card w-100 shadow-sm">
                <div class="card-header bg-primary fw-bold">
                    <i class="ti ti-building text-primary me-1"></i> Informasi Perusahaan
                </div>
                <div class="card-body">
                    <input type="hidden" name="company_id" id="company_id" value="{{ $company->id }}" />

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Perusahaan</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-building"></i></span>
                                <input type="text" class="form-control" id="name" name="name" value="{{ $company->name }}">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Singkatan</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-text-recognition"></i></span>
                                <input type="text" class="form-control" name="singkatan" id="singkatan"
                                    value="{{ $company->singkatan }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Slogan</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-megaphone"></i></span>
                                <input type="text" class="form-control" name="slogan" id="slogan"
                                    value="{{ $company->slogan }}">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">WhatsApp</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-brand-whatsapp"></i></span>
                                <input type="text" class="form-control" name="wa" id="wa" value="{{ $company->wa }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-mail"></i></span>
                                <input type="email" class="form-control" name="email" id="email" value="{{ $company->email }}">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Website</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="ti ti-world-www"></i></span>
                                <input type="text" class="form-control" name="website" id="website"
                                    value="{{ $company->website }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea class="form-control" name="address" id="address" rows="3">{{ $company->address }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Catatan Invoice</label>
                            <textarea class="form-control" name="note" rows="3">{{ $company->note }}</textarea>
                        </div>
                    </div>

                    <div class="border-top pt-3 mt-4">
                        <div class="card-header bg-primary fw-bold mb-3">
                            <i class="ti ti-wallet text-primary me-1"></i> Informasi Bank
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor Rekening</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-bank"></i></span>
                                    <input type="text" class="form-control" name="bank" id="bank"
                                        value="{{ $company->bank }}">
                                </div>
                                <small class="text-muted">Contoh: <span class="text-danger">BCA
                                        148127199</span></small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Atas Nama</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="ti ti-user"></i></span>
                                    <input type="text" class="form-control" name="holder" id="holder"
                                        value="{{ $company->holder }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button id="update" class="btn btn-primary">
                            <i class="ti ti-device-floppy me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logo Perusahaan -->
        <div class="col-lg-5 d-flex align-items-stretch mt-3">
            <div class="card w-100 shadow-sm">
                <div class="card-header bg-primary fw-bold">
                    <i class="ti ti-photo text-primary me-1"></i> Logo Perusahaan
                </div>
                <div class="card-body text-center">
                    <form action="{{ route('company.upload', $company->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <img src="{{ Storage::url('logo/'.$company->logo) }}" class="img-fluid rounded mb-3"
                            style="max-width: 300px;" alt="Logo Perusahaan">

                        <input type="file" name="file_logo" id="file_logo" class="form-control mb-3" required>
                        <small class="text-muted d-block mb-3">Format PNG, Maks 2 MB. Ukuran 1920 x 500 pixel</small>

                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <button type="submit" class="btn btn-warning">
                            <i class="ti ti-upload"></i> Upload Logo
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>



</div>

@endsection

@push('scripts')
<script type="text/javascript">
    $('#update').click(function(e) {
        e.preventDefault();
        var error_ele = document.getElementsByClassName('form-text text-danger');
        if (error_ele.length > 0) {
            for (var i = error_ele.length - 1; i >= 0; i--) {
                error_ele[i].remove();
            }
        }
        let perusahaan = $('#company_id').val();
        // collect data by id
        var data = {
            'name': $('#name').val(),
            'singkatan': $('#singkatan').val(),
            'slogan': $('#slogan').val(),
            'email': $('#email').val(),
            'wa': $('#wa').val(),
            'website': $('#website').val(),
            'address': $('#address').val(),
            'note': $('#note').val(),
            'bank': $('#bank').val(),
            'holder': $('#holder').val(),
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: `/setting/perusahaan/${perusahaan}`,
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
