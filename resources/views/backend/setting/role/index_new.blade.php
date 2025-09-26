@extends('backend.layouts.app_new')
@section('main')
@section('title', 'Billing Setting')
<!-- Content -->
<div class="container-fluid">
    <!-- Page header -->
    <div class="row align-items-center mb-7">
        <!-- Breadcrumb and Title -->
        <div class="col-md-6 mb-3 mb-md-0">
            <h4 class="main-title">Role Access</h4>
            <ul class="app-line-breadcrumbs mb-0">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <span><i class="ti ti-settings   f-s-16"></i> Setting</span>
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="javascript:void(0)">Role</a>
                </li>
            </ul>
        </div>
    </div><br />
    <!-- Page content -->
    <div class="row">
        <!-- Role Teknisi -->
        <div class="col-lg-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white fw-semibold d-flex align-items-center">
                    <i class="ti ti-tools me-2"></i> Role Teknisi
                </div>
                <div class="card-body pt-3">
                    <div class="row align-items-center mb-3">
                        <div class="col-md-10">
                            <label for="teknisi_status_regist" class="form-label text-muted mb-0">
                                Tampilkan Status Registrasi Saat Create User PPPoE
                            </label>
                        </div>
                        <div class="col-md-2 text-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="teknisi_status_regist"
                                    value="0">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role Kasir -->
        <div class="col-lg-12 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white fw-semibold d-flex align-items-center">
                    <i class="ti ti-cash me-2"></i> Role Kasir
                </div>
                <div class="card-body pt-3">
                    <div class="row align-items-center mb-3">
                        <div class="col-md-10">
                            <label for="kasir_melihat_total_keuangan" class="form-label text-muted mb-0">
                                Tampilkan Total Keuangan di Menu Keuangan &gt; Transaksi
                            </label>
                        </div>
                        <div class="col-md-2 text-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="kasir_melihat_total_keuangan"
                                    value="0">
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
            'kasir_melihat_total_keuangan': $('#kasir_melihat_total_keuangan').val(),
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
