@extends('backend.layouts.app')
@section('main')
@section('title', 'Payment Gateway')
<div class="container-lg">
    <!-- Page header -->
    <div class="row align-items-center mb-7">
        <div class="col-auto">
            <!-- Avatar -->
            <div class="avatar avatar-xl rounded text-primary">
                <span class="fs-2 material-symbols-outlined">
                    assured_workload
                    </span>
            </div>
        </div>
        <div class="col">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a class="text-body-secondary" href="#">Setting</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Payment Gateway</li>
                </ol>
            </nav>
            <!-- Heading -->
            <h1 class="fs-4 mb-0">Payment Gateway</h1>
        </div>
    </div>
    <div class="row mb-3 align-items-center">
        <label for="payment_gateway" class="col-md-2 col-form-label">Pilih Payment Gateway</label>
        <div class="col-md-4">
            <select class="form-select" id="payment_gateway" name="payment_gateway" required>
                <option value="" disabled selected>-- Pilih Payment Gateway --</option>
                <option value="midtrans" {{ $midtrans->status === 1 ? 'selected' : '' }}>Midtrans</option>
                <option value="duitku" {{ optional($duitku)->status === 1 ? 'selected' : '' }}>Duitku</option>
            </select>
        </div>
    </div>
<hr>    
    
    <div class="row">
        <div class="col-lg-6" id="pg_midtrans" style="display:none"> <!-- Card diperlebar -->
            <div class="card border rounded">
                {{-- <div class="card-header fw-bold bg-light">Midtrans Configuration</div> --}}
                <div class="card-header fw-bold bg-light d-flex justify-content-between align-items-center">
                    <span>Midtrans Configuration</span>
                    <button class="btn btn-sm btn-primary" type="submit" id="update">Save midtrans</button>
                </div>
                <div class="card-body">
                    <input type="hidden" id="midtrans_id" value="{{ $midtrans->id }}">
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-3 col-form-label text-muted" for="status">Status</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <span class="material-symbols-outlined">toggle_on</span>
                                </span>
                                <select class="form-select rounded-end" id="status">
                                    <option value="1" {{ $midtrans->status === 1 ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ $midtrans->status === 0 ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- ID Merchant -->
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-3 col-form-label text-muted" for="id_merchant">ID Merchant</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <span class="material-symbols-outlined">store</span>
                                </span>
                                <input class="form-control rounded-end" id="id_merchant" type="text"
                                    placeholder="G132706530" value="{{ $midtrans->id_merchant }}">
                            </div>
                        </div>
                    </div>
    
                    <!-- Client & Server Key -->
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-3 col-form-label text-muted" for="client_key">Client Key</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <span class="material-symbols-outlined">vpn_key</span>
                                </span>
                                <input class="form-control rounded-end" id="client_key" type="text"
                                    placeholder="Mid-client-eHid7cD1W-D12dgk" value="{{ $midtrans->client_key }}">
                            </div>
                        </div>
                    </div>
    
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-3 col-form-label text-muted" for="server_key">Server Key</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <span class="material-symbols-outlined">lock</span>
                                </span>
                                <input class="form-control rounded-end" id="server_key" type="text"
                                    placeholder="Mid-server-5iI_TXQPqwt0aoYBnltvNpjM"
                                    value="{{ $midtrans->server_key }}">
                            </div>
                        </div>
                    </div>
    
                    <!-- Biaya Admin & Status -->
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-3 col-form-label text-muted" for="admin_fee">Biaya Admin</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <span class="material-symbols-outlined">attach_money</span>
                                </span>
                                <input class="form-control rounded-end" id="admin_fee" type="number" placeholder="0"
                                    value="{{ $midtrans->admin_fee }}">
                            </div>
                        </div>
                    </div>

    
                    <!-- URL Notifikasi -->
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-3 col-form-label text-muted">URL Notifikasi</label>
                        <div class="col-md-9 d-flex align-items-center">
                            <code id="notification-url"
                                class="d-block bg-light p-2 rounded flex-grow-1">{{env('APP_URL')}}midtrans/notification</code>
                            <button class="btn btn-outline-primary btn-sm ms-2" onclick="copyToClipboard()">
                                <span class="material-symbols-outlined">content_copy</span>
                            </button>
                        </div>
                    </div>
    
                    <script>
                        function copyToClipboard() {
                            var copyText = document.getElementById("notification-url").textContent;
                            navigator.clipboard.writeText(copyText).then(function() {
                                alert("URL berhasil disalin!");
                            }).catch(function(err) {
                                console.error('Gagal menyalin: ', err);
                            });
                        }
                    </script>
    
                    {{-- <!-- Tutorial & Tombol Simpan -->
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            Untuk pendaftaran dan integrasi midtrans, silakan hubungi admin kami melalui nomor whatsapp berikut <a href="https://wa.me/6285155112192" target="_blank">085155112192</a>
                            
                        </div>
                        <div class="col-md-4 text-end">
                            <button class="btn btn-primary" type="submit" id="update">Save changes</button>
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
        <div class="col-lg-6" id="pg_duitku" style="display:none"> <!-- Card diperlebar -->
            <div class="card border rounded">
                <div class="card-header fw-bold bg-light d-flex justify-content-between align-items-center">
                    <span>Duitku Configuration</span>
                    <button class="btn btn-sm btn-secondary" type="submit" id="update-duitku">Save duitku</button>
                </div>
                <div class="card-body">
                    <input type="hidden" id="duitku_id">
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-3 col-form-label text-muted" for="status">Status</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <span class="material-symbols-outlined">toggle_on</span>
                                </span>
                                <select class="form-select rounded-end" id="status_duitku">
                                    <option value="1" {{optional($duitku)->status === 1 ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ optional($duitku)->status === 0 ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- ID Merchant -->
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-3 col-form-label text-muted" for="merchant_code">Merchant Code</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <span class="material-symbols-outlined">store</span>
                                </span>
                                <input class="form-control rounded-end" id="merchant_code" type="text"
                                    placeholder="" value="{{ optional($duitku)->id_merchant }}">
                            </div>
                        </div>
                    </div>
    
                    <!-- Client & Server Key -->
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-3 col-form-label text-muted" for="api_key">API Key</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <span class="material-symbols-outlined">vpn_key</span>
                                </span>
                                <input class="form-control rounded-end" id="api_key" type="text"
                                    placeholder="" value="{{ optional($duitku)->api_key}}">
                            </div>
                        </div>
                    </div>
    
                    <!-- Biaya Admin & Status -->
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-3 col-form-label text-muted" for="admin_fee_duitku">Biaya Admin</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <span class="material-symbols-outlined">attach_money</span>
                                </span>
                                <select class="form-select rounded-end" id="admin_fee_duitku">
                                    <option value="0" {{ optional($duitku)->admin_fee === 0 ? 'selected' : '' }}>Bebankan ke Merchant</option>
                                    <option value="1" {{optional($duitku)->admin_fee === 1 ? 'selected' : '' }}>Bebankan ke Pelanggan</option>
                                </select>
                            </div>
                        </div>
                    </div>
    
                    <!-- URL Notifikasi -->
                    <div class="row mb-3 align-items-center">
                        <label class="col-md-3 col-form-label text-muted">URL Notifikasi</label>
                        <div class="col-md-9 d-flex align-items-center">
                            <code id="notification-url-duitku"
                                class="d-block bg-light p-2 rounded flex-grow-1">{{env('APP_URL')}}duitku/notification</code>
                            <button class="btn btn-outline-primary btn-sm ms-2" onclick="copyToClipboardDuitku()">
                                <span class="material-symbols-outlined">content_copy</span>
                            </button>
                        </div>
                    </div>

                    <div class="row mb-3 align-items-center">
                        <label class="text-muted">Harap setting juga kepada siapa biaya admin dibebankan (Merchant/Pelanggan) di <b>Dashboard Duitku</b></label>
                    </div>
    
                    <script>
                        function copyToClipboardDuitku() {
                            var copyText = document.getElementById("notification-url-duitku").textContent;
                            navigator.clipboard.writeText(copyText).then(function() {
                                alert("URL berhasil disalin!");
                            }).catch(function(err) {
                                console.error('Gagal menyalin: ', err);
                            });
                        }
                    </script>
                    
                </div>
            </div>
        </div>
    </div>
    
</div>
@endsection
@push('scripts')
<script type="text/javascript">

    function toggleGatewaySection() {
        const selected = document.getElementById('payment_gateway').value;
        const midtransDiv = document.getElementById('pg_midtrans');
        const duitkuDiv = document.getElementById('pg_duitku');

        if (selected === 'midtrans') {
            midtransDiv.style.display = 'block';
            duitkuDiv.style.display = 'none';
        } else if (selected === 'duitku') {
            midtransDiv.style.display = 'none';
            duitkuDiv.style.display = 'block';
        } else {
            midtransDiv.style.display = 'none';
            duitkuDiv.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Trigger saat halaman dimuat
        toggleGatewaySection();

        // Trigger saat dropdown berubah
        document.getElementById('payment_gateway').addEventListener('change', toggleGatewaySection);
    });


    $('#update').click(function(e) {
        e.preventDefault();
        let midtran = $('#midtrans_id').val();

        // collect data by id
        var data = {
            'id_merchant': $('#id_merchant').val(),
            'client_key': $('#client_key').val(),
            'server_key': $('#server_key').val(),
            'admin_fee': $('#admin_fee').val(),
            'status': $('#status').val(),
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: `/midtrans/${midtran}`,
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
                        el.after($('<span class= "text-xs text-danger">' + value[0] +
                            '</span>'));
                    });
                }
            },

            error: function(err) {
                $("#message").html("Some Error Occurred!")
            }

        });

    });

    $('#update-duitku').click(function(e) {
        e.preventDefault();

        // collect data by id
        var data = {
            'merchant_code': $('#merchant_code').val(),
            'api_key': $('#api_key').val(),
            'admin_fee_duitku': $('#admin_fee_duitku').val(),
            'status_duitku': $('#status_duitku').val(),
        }
        console.log(data);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: `/duitku`,
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
                        el.after($('<span class= "text-xs text-danger">' + value[0] +
                            '</span>'));
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
