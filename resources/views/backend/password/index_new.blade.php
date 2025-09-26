@extends('backend.layouts.app_new')
@section('title', 'Ganti Password')
@section('main')

<div class="container-fluid">   
  <div class="row">
    <div class="col-lg-12">
      <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h3 class="card-title mb-0">Ganti Password</h3>
          <button class="btn btn-sm btn-primary" id="update">
            <i class="ti ti-refresh"></i> Update
          </button>
        </div>

        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">Password Saat Ini</label>
            <div class="input-group input-group-flat">
              <span class="input-group-text"><i class="ti ti-lock"></i></span>
              <input type="password" class="form-control" id="current_password" name="current_password" placeholder="••••••••">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Password Baru</label>
            <div class="input-group input-group-flat">
              <span class="input-group-text"><i class="ti ti-lock"></i></span>
              <input type="password" class="form-control" id="password" name="password" placeholder="••••••••">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Konfirmasi Password</label>
            <div class="input-group input-group-flat">
              <span class="input-group-text"><i class="ti ti-lock"></i></span>
              <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="••••••••">
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

    const data = {
      'current_password': $('#current_password').val(),
      'password': $('#password').val(),
      'confirm_password': $('#confirm_password').val()
    };

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $.ajax({
      url: `/password`,
      type: "POST",
      data: data,
      dataType: "json",
      success: function(response) {
        const { success, message } = response;
        Swal.fire({
          icon: success ? 'success' : 'error',
          title: success ? 'Berhasil' : 'Gagal',
          text: message,
          showConfirmButton: false,
          timer: 1600
        });
        if (success) {
          setTimeout(() => location.reload(), 1600);
        }
      },
      error: function(err) {
        Swal.fire({
          icon: 'error',
          title: 'Oops!',
          text: 'Terjadi kesalahan saat memproses.',
          showConfirmButton: false,
          timer: 1600
        });
      }
    });
  });
</script>
@endpush
