@extends('backend.layouts.app')
@section('main')
@section('title', 'Perusahaan')
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
                    <li class="breadcrumb-item active" aria-current="page">Perusahaan</li>
                </ol>
            </nav>

            <!-- Heading -->
            <h1 class="fs-4 mb-0">Perusahaan</h1>
        </div>
    </div>



    <div class="row">
        <!-- Card Informasi Perusahaan -->
        <div class="col-lg-7 d-flex align-items-stretch mt-3">
            <div class="card border rounded w-100">
                <div class="card-header fw-semibold bg-light">
                    <span class="material-symbols-outlined align-middle">business</span>
                    Informasi Perusahaan
                </div>
                <div class="card-body">
                    <input type="hidden" name="company_id" id="company_id" value="{{ $company->id }}" />
    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="name">Nama Perusahaan</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <span class="material-symbols-outlined">business</span>
                                    </span>
                                    <input class="form-control" id="name" name="name" type="text" value="{{ $company->name }}" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="singkatan">Singkatan</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <span class="material-symbols-outlined">short_text</span>
                                    </span>
                                    <input class="form-control" id="singkatan" name="singkatan" type="text" value="{{ $company->singkatan }}" />
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="slogan">Slogan</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <span class="material-symbols-outlined">campaign</span>
                                    </span>
                                    <input class="form-control" id="slogan" name="slogan" type="text" value="{{ $company->slogan }}" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="wa">WhatsApp</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <span class="material-symbols-outlined">chat</span>
                                    </span>
                                    <input class="form-control" id="wa" name="wa" type="text" value="{{ $company->wa }}" />
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="email">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <span class="material-symbols-outlined">email</span>
                                    </span>
                                    <input class="form-control" id="email" type="email" value="{{ $company->email }}" />
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="website">Website</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <span class="material-symbols-outlined">public</span>
                                    </span>
                                    <input class="form-control" id="website" name="website" type="text" value="{{ $company->website }}" />
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="address">Alamat</label>
                                <textarea class="form-control" id="address" name="address" style="height:90px">{{ $company->address }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="note">Note Invoice</label>
                                <textarea class="form-control" id="note" name="note" style="height:90px">{{ $company->note }}</textarea>
                            </div>
                        </div>
                    </div>
        
                    <div class="row">
                        <div class="card-header fw-semibold bg-light mb-3 mt-3">
                            <span class="material-symbols-outlined align-middle">wallet</span>
                            Informasi Bank
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="bank">Nomor Rekening</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <span class="material-symbols-outlined">account_balance</span>
                                    </span>
                                    <input class="form-control" id="bank" name="bank" type="text" value="{{ $company->bank }}" />
                                </div>
                                <small>Isi dengan nama bank, cth: <span class="text-danger">BCA 148127199</span></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="holder">Atas Nama</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <span class="material-symbols-outlined">person</span>
                                    </span>
                                    <input class="form-control" id="holder" name="holder" type="text" value="{{ $company->holder }}" />
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <hr>
    
                    <div class="text-end">
                        <button id="update" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    
        <!-- Card Logo Perusahaan -->
        <div class="col-lg-5 d-flex align-items-stretch mt-3">
            <div class="card border rounded w-100">
                <div class="card-header fw-semibold bg-light">
                    <span class="material-symbols-outlined align-middle">image</span>
                    Logo Perusahaan
                </div>
                <div class="card-body text-center">
                    <form action="{{ route('company.upload', $company->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <img class="img-fluid rounded mb-3" style="width: 300px" src="/storage/logo/{{ $company->logo }}" alt="Logo Perusahaan" />
                        </div>
                        <input type="file" name="file_logo" class="form-control mb-3" required>
                        <small class="text-muted d-block mb-3">Format PNG, Maks 2 MB. Ukuran 1920 x 500 pixel</small>
                        
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
    
                        <button class="btn btn-warning" type="submit">
                            <span class="material-symbols-outlined">upload</span> Upload logo
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
