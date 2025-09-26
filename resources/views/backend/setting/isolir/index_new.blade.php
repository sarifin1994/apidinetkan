@extends('backend.layouts.app_new')
@section('main')
@section('title', 'Billing Setting')
<!-- Content -->
<div class="container-fluid">
    <!-- Page header -->
    <div class="row align-items-center mb-7">
        <!-- Breadcrumb and Title -->
        <div class="col-md-6 mb-3 mb-md-0">
            <h4 class="main-title">Mode Isolir</h4>
            <ul class="app-line-breadcrumbs mb-0">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <span><i class="ti ti-settings   f-s-16"></i> Setting</span>
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="javascript:void(0)">Isolir</a>
                </li>
            </ul>
        </div>
    </div><br />
    <!-- Page content -->
    <div class="row">
        <!-- Pengaturan Isolir -->
        <div class="col-lg-8 mt-3">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white fw-semibold">
                    <i class="ti ti-shield-lock me-1"></i> Pengaturan Isolir
                </div>
                <div class="card-body">
                    <input type="hidden" id="isolir_id">

                    <!-- Switch Mode Isolir -->
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-3 col-form-label text-muted" for="isolir">Mode Isolir</label>
                        <div class="col-md-9">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" value="0" id="isolir">
                                <label class="form-check-label text-muted" for="isolir">Aktifkan Mode Isolir</label>
                            </div>
                        </div>
                    </div>

                    <!-- Script Blokir Internet -->
                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label text-muted">Script Blokir Internet (Wajib)</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <textarea class="form-control" rows="3" readonly id="copysstp"></textarea>
                                <button class="btn btn-outline-primary" onclick="copyToClipboard('copysstp', 'sstp')">
                                    <i class="ti ti-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Script Web Proxy -->
                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label text-muted">Script Aktifkan Web Proxy (Opsional)</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <textarea class="form-control" rows="3" readonly id="copyl2tp"></textarea>
                                <button class="btn btn-outline-primary" onclick="copyToClipboard('copyl2tp', 'l2tp')">
                                    <i class="ti ti-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Script Redirect Halaman Isolir -->
                    <div class="row mb-3">
                        <label class="col-md-3 col-form-label text-muted">Script Redirect ke Halaman Isolir
                            (Opsional)</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <textarea class="form-control" rows="3" readonly id="copypptp"></textarea>
                                <button class="btn btn-outline-primary" onclick="copyToClipboard('copypptp', 'pptp')">
                                    <i class="ti ti-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informasi Billing Setting -->
        <div class="col-lg-4 mt-3">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white fw-semibold">
                    <i class="ti ti-info-circle me-1"></i> Informasi Billing Setting
                </div>
                <div class="card-body">
                    <ol class="text-muted mb-0">
                        <li>IP Address Isolir akan di-generate random dengan Network <span
                                class="text-danger">172.30.0.0/16</span></li>
                        <li>Tidak perlu menambahkan IP Pool</li>
                        <li>Edit menu Access di Web Proxy untuk mengubah halaman isolir, ganti dst-address <span
                                class="text-danger">!{{ env('IP_WEB_ISOLIR') }}</span></li>
                        <li>User PPPoE akan otomatis terhubung ke IP isolir setelah mode diaktifkan</li>
                        <li>Hubungi admin jika butuh bantuan setting isolir</li>
                    </ol>
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
                $('#isolir_id').val(response.data.id),
                    $('#copyl2tp').html(
                        '/ip proxy&#10;set enabled=yes parent-proxy=0.0.0.0 port="8080"&#10;/ip proxy access&#10;add action=deny redirect-to="isolir.radiusqu.com" src-address="172.30.0.0/16"'
                    );
                $('#copypptp').html(
                    '/ip firewall nat&#10;add action=redirect chain=dstnat comment="redirect isolir - by radiusqu" disabled=no &#92&#10;dst-address="!{{ env('IP_WEB_ISOLIR') }}" dst-port="80,443" protocol=tcp &#92&#10;src-address="172.30.0.0/16" to-ports="8080"'
                );
                $('#copysstp').html(
                    '/ip firewall filter&#10;add action=drop chain=forward comment="drop isolir - by radiusqu" &#92&#10;dst-address="!{{ env('IP_WEB_ISOLIR') }}" protocol=tcp src-address="172.30.0.0/16" &#10;add action=drop chain=forward comment="drop isolir - by radiusqu" &#92&#10;dst-address="!{{ env('IP_WEB_ISOLIR') }}" protocol=udp dst-port=!53,5353 src-address="172.30.0.0/16"'
                );
                if (response.data.isolir === 1) {
                    $('#isolir').attr('checked', true);
                    $('#isolir').val('1');
                } else {
                    $('#isolir').attr('checked', false);
                    $('#isolir').val('0');
                };
            }
        });
    });

    $("#isolir").click(function() {
        if ($("#isolir").prop("checked")) {
            $("#isolir").val(1);
        } else {
            $("#isolir").val(0);
        }
    });

    $('#isolir').on('change', function() {
        let setting = $('#isolir_id').val();

        // collect data by id
        var data = {
            'isolir': $('#isolir').val(),
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: `/setting/isolir/${setting}`,
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

    function copyToClipboard(elementId, buttonId) {
        var text = document.getElementById(elementId).textContent;
        navigator.clipboard.writeText(text).then(() => {
            alert("Script berhasil disalin!");
        }).catch(err => {
            console.error('Gagal menyalin: ', err);
        });
    }
</script>
@endpush
