@extends('backend.layouts.app_new')
@section('main')
@section('title', 'Password')
<div class="container-fluid">   
    <div class="row">
        <div class="col-lg-12" id="pg_midtrans"> <!-- Card diperlebar -->
            <div class="card border rounded">
                <div class="card-header fw-bold  d-flex justify-content-between align-items-center">
                    <span>Change Password</span>
                    <button class="btn btn-sm btn-primary" type="submit" id="update">Update</button>
                </div>
                <div class="card-body">
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-3 col-form-label text-muted" for="current_password">Password Sekarang</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-text ">
                                    <span class="material-symbols-outlined">password</span>
                                </span>
                                <input class="form-control rounded-end" id="current_password" name="current_password" type="password">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-3 col-form-label text-muted" for="password">Password</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-text ">
                                    <span class="material-symbols-outlined">password</span>
                                </span>
                                <input class="form-control rounded-end" id="password" name="password" type="password">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-3 col-form-label text-muted" for="confirm_password">Confirm Password</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-text ">
                                    <span class="material-symbols-outlined">password</span>
                                </span>
                                <input class="form-control rounded-end" id="confirm_password" name="confirm_password" type="password">
                            </div>
                        </div>
                    </div>

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

        // collect data by id
        var data = {
            'current_password': $('#current_password').val(),
            'password': $('#password').val(),
            'confirm_password': $('#confirm_password').val()
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: `/password`,
            type: "POST",
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
                    Swal.fire({
                        icon: 'error',
                        title: 'error',
                        text: `${data.message}`,
                        showConfirmButton: false,
                        timer: 1500
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