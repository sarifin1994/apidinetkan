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
                    no_accounts
                </span>
            </div>
        </div>
        <div class="col">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a class="text-body-secondary" href="#">Setting</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Role</li>
                </ol>
            </nav>
            <!-- Heading -->
            <h1 class="fs-4 mb-0">Role Access</h1>
        </div>
    </div>
    <!-- Page content -->
    <div class="row">
        <!-- Bagian Isolir -->
        <div class="col-lg-8 mb-4"> <!-- Card diperlebar -->
            <div class="card border rounded">
                <div class="card-header fw-semibold bg-light">Role Teknisi</div>
                <div class="card-body">

                    <!-- Switch Mode Isolir -->
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-9 col-form-label text-muted" for="teknisi_status_regist">Tampilkan Status
                            Registrasi Saat Create User PPPoE</label>
                        <div class="col-md-1">
                            <div class="form-check form-switch">
                                <input class="form-check-input" role="switch" type="checkbox" value="0"
                                    id="teknisi_status_regist">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-8 mb-4"> <!-- Card diperlebar -->
            <div class="card border rounded">
                <div class="card-header fw-semibold bg-light">Role Kasir</div>
                <div class="card-body">
                    <!-- Switch Mode Isolir -->
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-9 col-form-label text-muted" for="kasir_melihat_total_keuangan">Tampilkan Total Keuangan di Menu Keuangan > Transaksi</label>
                        <div class="col-md-1">
                            <div class="form-check form-switch">
                                <input class="form-check-input" role="switch" type="checkbox" value="0" id="kasir_melihat_total_keuangan">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>



    </div>

    </section>
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
                if (response.data.teknisi_status_regist === 1) {
                    $('#teknisi_status_regist').attr('checked', true);
                    $('#teknisi_status_regist').val('1');
                } else {
                    $('#teknisi_status_regist').attr('checked', false);
                    $('#teknisi_status_regist').val('0');
                };
                if (response.data.kasir_melihat_total_keuangan === 1) {
                    $('#kasir_melihat_total_keuangan').attr('checked', true);
                    $('#kasir_melihat_total_keuangan').val('1');
                } else {
                    $('#kasir_melihat_total_keuangan').attr('checked', false);
                    $('#kasir_melihat_total_keuangan').val('0');
                };
            }
        });
    });

    $("#teknisi_status_regist").click(function() {
        if ($("#teknisi_status_regist").prop("checked")) {
            $("#teknisi_status_regist").val(1);
        } else {
            $("#teknisi_status_regist").val(0);
        }
    });
    $("#kasir_melihat_total_keuangan").click(function() {
        if ($("#kasir_melihat_total_keuangan").prop("checked")) {
            $("#kasir_melihat_total_keuangan").val(1);
        } else {
            $("#kasir_melihat_total_keuangan").val(0);
        }
    });


    $('#teknisi_status_regist,#kasir_melihat_total_keuangan').on('change', function() {
        // collect data by id
        var data = {
            'teknisi_status_regist': $('#teknisi_status_regist').val(),
            'kasir_melihat_total_keuangan' : $('#kasir_melihat_total_keuangan').val(),
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: `/setting/role`,
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
