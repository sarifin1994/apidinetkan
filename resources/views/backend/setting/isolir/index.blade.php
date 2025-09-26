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
                    <li class="breadcrumb-item active" aria-current="page">Isolir</li>
                </ol>
            </nav>
            <!-- Heading -->
            <h1 class="fs-4 mb-0">Mode Isolir</h1>
        </div>
    </div>
    <!-- Page content -->
    <div class="row">
        <!-- Bagian Isolir -->
        <div class="col-lg-8"> <!-- Card diperlebar -->
            <div class="card border rounded">
                <div class="card-header fw-semibold bg-light">Pengaturan Isolir</div>
                <div class="card-body">
                    <input type="hidden" id="isolir_id">
    
                    <!-- Switch Mode Isolir -->
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-3 col-form-label text-muted" for="isolir">Mode Isolir</label>
                        <div class="col-md-9">
                            <div class="form-check form-switch">
                                <input class="form-check-input" role="switch" type="checkbox" value="0" id="isolir">
                                <label class="form-check-label text-muted" for="isolir">Aktifkan Mode Isolir</label>
                            </div>
                        </div>
                    </div>
                    <hr>

                     <!-- Script Blokir Akses Internet -->
                     <div class="row mb-3 align-items-center">
                        <label class="col-md-3 col-form-label text-muted">Script Blokir Internet (Wajib)</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <textarea class="form-control" rows="3" readonly id="copysstp"></textarea>
                                <button class="btn btn-outline-primary" onclick="copyToClipboard('copysstp', 'sstp')">
                                    <span class="material-symbols-outlined">content_copy</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <hr>
    
                    <!-- Script Aktifkan Web Proxy -->
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-3 col-form-label text-muted">Script Aktifkan Web Proxy (Opsional)</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <textarea class="form-control" rows="3" readonly id="copyl2tp"></textarea>
                                <button class="btn btn-outline-primary" onclick="copyToClipboard('copyl2tp', 'l2tp')">
                                    <span class="material-symbols-outlined">content_copy</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <hr>
    
                    <!-- Script Redirect ke Halaman Isolir -->
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-3 col-form-label text-muted">Script Redirect ke Halaman Isolir (Opsional)</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <textarea class="form-control" rows="3" readonly id="copypptp"></textarea>
                                <button class="btn btn-outline-primary" onclick="copyToClipboard('copypptp', 'pptp')">
                                    <span class="material-symbols-outlined">content_copy</span>
                                </button>
                            </div>
                        </div>
                    </div>
                   
    
                   
    
                </div>
            </div>
        </div>
    
        <!-- Bagian Informasi Billing -->
        <div class="col-lg-4">
            <div class="card border rounded">
                <div class="card-header bg-light fw-semibold">
                    <span class="material-symbols-outlined align-middle">contact_support</span>
                    Informasi Billing Setting
                </div>
                <div class="card-body">
                    <ol class="text-muted mb-0">
                        <li>IP Address Isolir akan di-generate secara random dengan Network 
                            <small class="text-danger">172.30.0.0/16</small>
                        </li>
                        <li>Tidak perlu menambahkan IP Pool</li>
                        <li>Jika ingin mengubah halaman isolir, edit menu Access di Web Proxy dan ubah dst-address 
                            <small class="text-danger">!{{ env('IP_WEB_ISOLIR') }}</small> dengan IP isolir Anda
                        </li>
                        <li>Setelah mengaktifkan mode isolir, user PPPoE akan otomatis terhubung ke IP isolir dan dialihkan ke halaman isolir</li>
                        <li>Jika ada pertanyaan mengenai setting mode isolir, silakan hubungi kami</li>
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
                    },1500);
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