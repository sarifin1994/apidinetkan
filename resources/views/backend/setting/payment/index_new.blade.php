@extends('backend.layouts.app_new')
@section('main')
@section('title', 'Payment Gateway')
<div class="container-fluid">
    <!-- Page header -->
    <div class="row align-items-center mb-7">
        <!-- Breadcrumb and Title -->
        <div class="col-md-6 mb-3 mb-md-0">
            <h4 class="main-title">Payment Gateway</h4>
            <ul class="app-line-breadcrumbs mb-0">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <span><i class="ti ti-settings   f-s-16"></i> Setting</span>
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="javascript:void(0)">Payment Gateway</a>
                </li>
            </ul>
        </div>
    </div><br />
    <div class="row mb-12 align-items-center">
        <label for="payment_gateway" class="col-md-2 col-form-label fw-semibold text-muted">
            <i class="ti ti-building-bank text-primary me-1"></i> Payment Gateway
        </label>
        <div class="col-md-4">
            <div class="input-group shadow-sm">
                <span class="input-group-text bg-light border-end-0">
                    <i class="ti ti-credit-card text-secondary"></i>
                </span>
                <select class="form-select border-start-0" id="payment_gateway" name="payment_gateway" required>
                    <option value="" disabled selected>-- Pilih Payment Gateway --</option>
                    <!-- <option value="midtrans" {{ $midtrans->status === 1 ? 'selected' : '' }}>Midtrans</option> -->
                    <option value="duitku" {{ optional($duitku)->status === 1 ? 'selected' : '' }}>Duitku</option>
                </select>
            </div>
        </div>
    </div>

    <hr class="my-4">

    <!-- Midtrans Card -->
    <div class="col-lg-12" id="pg_midtrans" style="display: none;">
        <div class="card shadow-sm border-light rounded-3">
            <div class="card-header bg-primary text-white fw-bold d-flex justify-content-between align-items-center">
                <span><i class="ti ti-settings me-2"></i> Midtrans Configuration</span>
                <button class="btn btn-sm btn-light text-primary fw-semibold" type="submit" id="update">
                    <i class="ti ti-device-floppy me-1"></i> Save
                </button>
            </div>
            <div class="card-body pb-4">
                <input type="hidden" id="midtrans_id" value="{{ $midtrans->id }}">

                <!-- Status -->
                <div class="mb-3 row align-items-center">
                    <label class="col-md-3 col-form-label text-muted">Status</label>
                    <div class="col-md-9">
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white">
                                <i class="ti ti-toggle-right text-primary"></i>
                            </span>
                            <select class="form-select border-start-0" id="status">
                                <option value="1" {{ $midtrans->status === 1 ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ $midtrans->status === 0 ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- ID Merchant -->
                <div class="mb-3 row align-items-center">
                    <label class="col-md-3 col-form-label text-muted">ID Merchant</label>
                    <div class="col-md-9">
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white">
                                <i class="ti ti-building-store text-primary"></i>
                            </span>
                            <input type="text" id="id_merchant" class="form-control border-start-0"
                                placeholder="G132706530" value="{{ $midtrans->id_merchant }}">
                        </div>
                    </div>
                </div>

                <!-- Client Key -->
                <div class="mb-3 row align-items-center">
                    <label class="col-md-3 col-form-label text-muted">Client Key</label>
                    <div class="col-md-9">
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white">
                                <i class="ti ti-key text-primary"></i>
                            </span>
                            <input type="text" id="client_key" class="form-control border-start-0"
                                placeholder="Mid-client-xxxxx" value="{{ $midtrans->client_key }}">
                        </div>
                    </div>
                </div>

                <!-- Server Key -->
                <div class="mb-3 row align-items-center">
                    <label class="col-md-3 col-form-label text-muted">Server Key</label>
                    <div class="col-md-9">
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white">
                                <i class="ti ti-lock text-primary"></i>
                            </span>
                            <input type="text" id="server_key" class="form-control border-start-0"
                                placeholder="Mid-server-xxxxx" value="{{ $midtrans->server_key }}">
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="mb-3 row align-items-center">
                    <label class="col-md-3 col-form-label text-muted">Status</label>
                    <div class="col-md-9">
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white">
                                <i class="ti ti-toggle-right text-primary"></i>
                            </span>
                            <select class="form-select border-start-0" id="status">
                                <option value="1" {{ $midtrans->status === 1 ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ $midtrans->status === 0 ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Biaya Admin -->
                <div class="mb-3 row align-items-center">
                    <label class="col-md-3 col-form-label text-muted">Biaya Admin</label>
                    <div class="col-md-9">
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white">
                                <i class="ti ti-currency-dollar text-primary"></i>
                            </span>
                            <input type="number" id="admin_fee" class="form-control border-start-0" placeholder="0"
                                value="{{ $midtrans->admin_fee }}">
                        </div>
                    </div>
                </div>

                <!-- URL Notifikasi -->
                <div class="mb-3 row align-items-center">
                    <label class="col-md-3 col-form-label text-muted">URL Notifikasi</label>
                    <div class="col-md-9">
                        <div class="input-group shadow-sm">
                            <span class="input-group-text bg-white">
                                <i class="ti ti-link text-primary"></i>
                            </span>
                            <input type="text" readonly id="notification-url"
                                class="form-control border-start-0 bg-light"
                                value="{{ env('APP_URL') }}midtrans/notification">
                            <button class="btn btn-outline-primary" type="button" onclick="copyToClipboard()">
                                <i class="ti ti-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <script>
                    function copyToClipboard() {
                        const copyText = document.getElementById("notification-url");
                        copyText.select();
                        copyText.setSelectionRange(0, 99999); // mobile support
                        navigator.clipboard.writeText(copyText.value)
                            .then(() => alert("URL berhasil disalin!"))
                            .catch(err => console.error('Gagal menyalin: ', err));
                    }
                </script>
            </div>
        </div>
    </div>
    <div class="col-lg-12" id="pg_duitku" style="display:none"> <!-- Card diperlebar -->
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

                <hr>
                <h5>Disbursement</h5>

                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-muted" for="user_id">Merchant Code</label>
                    <div class="col-md-9">
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fa-solid fa-user-tag"></i>
                            </span>
                            <input class="form-control rounded-end" id="user_id" type="number"
                                placeholder="1234" value="{{ optional($duitku)->user_id }}">
                        </div>
                    </div>
                </div>
                <!-- Client & Server Key -->
                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-muted" for="secret_key">Secret Key</label>
                    <div class="col-md-9">
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                            <i class="fa-solid fa-user-shield"></i>
                            </span>
                            <input class="form-control rounded-end" id="secret_key" type="text"
                                placeholder="12e3r4tyujkyj6y54t3r2e" value="{{ optional($duitku)->secret_key}}">
                        </div>
                    </div>
                </div>
                <!-- Client & Server Key -->
                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-muted" for="fee_disburs">Fee</label>
                    <div class="col-md-9">
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                            <i class="fa-solid fa-user-shield"></i>
                            </span>
                            <input class="form-control rounded-end" id="fee_disburs" type="number"
                                placeholder="2500" value="{{ optional($duitku)->fee_disburs}}">
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-muted" for="email_disburs">Email Disburst</label>
                    <div class="col-md-9">
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                            <i class="fa-solid fa-at"></i>
                            </span>
                            <input class="form-control rounded-end" id="email_disburs" type="text"
                                placeholder="abc@abc.com" value="{{ optional($duitku)->email_disburs}}">
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-muted" for="minimal_disburs">Minimal Disburst</label>
                    <div class="col-md-9">
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                            <i class="fa-solid fa-sack-dollar"></i>
                            </span>
                            <input class="form-control rounded-end" id="minimal_disburs" type="number"
                                min="20000" placeholder="20000" value="{{ optional($duitku)->minimal_disburs}}">
                        </div>
                    </div>
                </div>

                <div class="row mb-3 align-items-center">
                    <label class="col-md-3 col-form-label text-muted" for="status_widrawal">Status Widrawal</label>
                    <div class="col-md-9">
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <span class="material-symbols-outlined">toggle_on</span>
                            </span>
                            <select class="form-select rounded-end" id="status_widrawal">
                                <option value="1" {{optional($duitku)->status_widrawal === 1 ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ optional($duitku)->status_widrawal === 0 ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>
                    </div>
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

    document.addEventListener('DOMContentLoaded', function() {
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
            
            'secret_key': $('#secret_key').val(),
            'user_id': $('#user_id').val(),
            'status_widrawal': $('#status_widrawal').val(),
            'fee_disburs': $('#fee_disburs').val(),
            'email_disburs': $('#email_disburs').val(),
            'minimal_disburs': $('#minimal_disburs').val()
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
