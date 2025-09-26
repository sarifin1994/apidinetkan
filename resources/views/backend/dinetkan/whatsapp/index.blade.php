@extends('backend.layouts.app_new')

@section('title', 'Whatsapp Setting')

@section('css')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/select2.css') }}">
@endsection

@section('main')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-sm-6 ps-0">
                    <h3>Whatsapp Setting</h3>
                </div>
                <div class="col-sm-6 pe-0">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="/">
                                <!-- <svg class="stroke-icon">
                                    <use href="{{ asset('assets/old/svg/icon-sprite.svg#stroke-home') }}"></use>
                                </svg> -->
                            </a>
                        </li>
                        <!-- <li class="breadcrumb-item active">Site Settings</li> -->
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- Container-fluid starts-->
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6">
                <div class="card w-100 position-relative overflow-hidden">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-semibold">Whatsapp Gateway</h5>
                        <p class="card-subtitle mb-4">Manage your Whatsapp Gateway settings here.</p>
                        <div class="row">
                            <div class="mb-3">
                                <label for="status">Status</label>
                                <input class="form-control" value="{{$wa->is_login == 1 ? 'Online' : 'Offline'}}" readonly>
                            </div>
                            @if($wa->is_login == 1)
                            <div class="mb-3">
                                <button class="btn btn-primary" onclick="logout_whatsapp()" type="button">Logout Whatsapp</button>
                                <!-- <p>Restart ini untuk proses disconnect dan connect ulang maka akan muncul QR lagi</p> -->
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-primary" onclick="group_whatsapp()" type="button">Get Whatsapp Group</button> 
                            </div>
                            @endif
                            @if($wa->is_login == 0)
                            <div class="mb-3">
                                <img id="qrcode">
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-primary" onclick="start_whatsapp()" type="button">Start Whatsapp</button>
                            </div>
                            @endif

                            @if($wa->is_login ==1)
                            
                            <div class="mb-3">
                                <form method="POST" action="{{route('dinetkan.whatsapp.update_group')}}">
                                    @csrf
                                    <label for="id_wag" class="form-label">Group Internal</label>
                                    <!-- <input type="text" class="form-control" id="id_wag" name="id_wag" required> -->
                                    <div class="form-group mb-3" style="display:grid">
                                        <select class="form-control" id="id_wag" name="id_wag" autocomplete="off" data-placeholder="Pilih WAGroup">
                                            <option value=""></option>
                                            @forelse ($wag as $row)
                                                <option value="{{ $row->group_id }}" {{$wa->group_id == $row->group_id ? 'selected' : ''}}>{{ $row->group_name }}</option>
                                            @empty
                                            @endforelse
                                        </select>

                                    </div>
                                    <button type="submit" class="btn btn-primary">Save Group Internal</button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                
                <div class="col-12 col-xxl-8">
                    <div class="card-body">
                        <div class="mt-3">
                            <h2 class="fs-5">Template Pesan</h2>
                            <span>Silakan sesuaikan template pesan WhatsApp sesuka hati dengan menggunakan parameter yang
                                tersedia</span>
                        </div>
                        <hr>

                        <!-- Button Group -->
                        <div class="d-flex flex-wrap">
                            <button class="btn btn-outline-primary m-1" id="account_active1"
                                data-id="{{ multi_auth()->shortname }}">
                                <span class="material-symbols-outlined">counter_1</span> Pemasangan Baru
                            </button>
                            <button class="btn btn-outline-info m-1" id="invoice_terbit1"
                                data-id="{{ multi_auth()->shortname }}">
                                <span class="material-symbols-outlined">counter_2</span> Invoice Terbit
                            </button>
                            <button class="btn btn-outline-secondary m-1" id="invoice_reminder1"
                                data-id="{{ multi_auth()->shortname }}">
                                <span class="material-symbols-outlined">counter_3</span> Invoice Reminder
                            </button>
                            <button class="btn btn-outline-success m-1" id="payment_paid1"
                                data-id="{{ multi_auth()->shortname }}">
                                <span class="material-symbols-outlined">counter_4</span> Invoice Dibayar
                            </button>
                            <button class="btn btn-outline-danger m-1" id="invoice_overdue1"
                                data-id="{{ multi_auth()->shortname }}">
                                <span class="material-symbols-outlined">counter_5</span> Invoice Overdue
                            </button>
                            <button class="btn btn-outline-danger m-1" id="payment_cancel1"
                                data-id="{{ multi_auth()->shortname }}">
                                <span class="material-symbols-outlined">counter_6</span> Invoice Dibatalkan
                            </button>
                            <button class="btn btn-outline-success m-1" id="user_aktif1"
                                data-id="{{ multi_auth()->shortname }}">
                                <span class="material-symbols-outlined">counter_7</span> User Diaktifkan
                            </button>
                            <button class="btn btn-outline-danger m-1" id="user_suspend1"
                                data-id="{{ multi_auth()->shortname }}">
                                <span class="material-symbols-outlined">counter_8</span> User Disuspend
                            </button>
                        </div>
                        <hr>
                        <!-- Additional row if needed -->
                        <div class="d-flex flex-wrap mb-5">
                            <!-- Additional content can go here -->
                            <button class="btn btn-outline-danger m-1" id="tiket_open_pelanggan1"
                                data-id="{{ multi_auth()->shortname }}">
                                <span class="material-symbols-outlined">counter_1</span> Tiket Gangguan Open (Pelanggan)
                            </button>
                            <button class="btn btn-outline-danger m-1" id="tiket_open_teknisi1"
                                data-id="{{ multi_auth()->shortname }}">
                                <span class="material-symbols-outlined">counter_3</span> Tiket Gangguan Open (Teknisi)
                            </button>
                            <button class="btn btn-outline-success m-1" id="tiket_close_pelanggan1"
                                data-id="{{ multi_auth()->shortname }}">
                                <span class="material-symbols-outlined">counter_2</span> Tiket Gangguan Closed (Pelanggan)
                            </button>
                            <button class="btn btn-outline-success m-1" id="tiket_close_teknisi1"
                                data-id="{{ multi_auth()->shortname }}">
                                <span class="material-symbols-outlined">counter_4</span> Tiket Gangguan Closed (Teknisi)
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
  </div>

    @include('backend.dinetkan.whatsapp.modal.show_invoice_terbit')
    @include('backend.dinetkan.whatsapp.modal.show_invoice_reminder')
    @include('backend.dinetkan.whatsapp.modal.show_invoice_paid')
    @include('backend.dinetkan.whatsapp.modal.show_account_active')
    @include('backend.dinetkan.whatsapp.modal.show_account_suspend')
    @include('backend.dinetkan.whatsapp.modal.show_invoice_overdue')
    @include('backend.dinetkan.whatsapp.modal.show_invoice_cancel')
    @include('backend.dinetkan.whatsapp.modal.show_tiket_open_pelanggan')
    @include('backend.dinetkan.whatsapp.modal.show_tiket_open_teknisi')
    @include('backend.dinetkan.whatsapp.modal.show_tiket_close_pelanggan')
    @include('backend.dinetkan.whatsapp.modal.show_tiket_close_teknisi')
    <!-- Container-fluid Ends-->
@endsection

@push('scripts')
<script>
    
    $('#id_wag').select2({
        allowClear: true,
        // dropdownParent: $("#couponModal .modal-content"),
    });
    function start_whatsapp(){
        $.ajax({
          url: `/dinetkan/whatsapp/start`,
          method: 'GET',
          success: function(data) {
            // toastr.success(data.message);
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: `${data.message}`,
                showConfirmButton: false,
                timer: 1500
            });
          },
          error: function(xhr) {
            alert('Error fetching admin data: ' + xhr.responseJSON.message);
          }
        });
    }
    function group_whatsapp(){
        $.ajax({
          url: `/dinetkan/whatsapp/get_group`,
          method: 'GET',
          success: function(data) {
            // toastr.success(data.message);
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: `${data.message}`,
                showConfirmButton: false,
                timer: 1500
            });
            location.reload();
          },
          error: function(xhr) {
            alert('Error fetching admin data: ' + xhr.responseJSON.message);
          }
        });
    }
    function restart_whatsapp(){
        $.ajax({
          url: `/dinetkan/whatsapp/restart`,
          method: 'GET',
          success: function(data) {
            // toastr.success(data.message);
            
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: `${data.message}`,
                showConfirmButton: false,
                timer: 1500
            });
            location.reload();
          },
          error: function(xhr) {
            alert('Error fetching admin data: ' + xhr.responseJSON.message);
          }
        });
    }

    
    function logout_whatsapp(){
        $.ajax({
          url: `/dinetkan/whatsapp/logout`,
          method: 'GET',
          success: function(data) {
            // toastr.success(data.message);
            
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: `${data.message}`,
                showConfirmButton: false,
                timer: 1500
            });
            location.reload();
          },
          error: function(xhr) {
            alert('Error fetching admin data: ' + xhr.responseJSON.message);
          }
        });
    }

    function update_qr(){
        <?php if($wa->is_login == 0) {?>
            $.ajax({
                url: `/dinetkan/whatsapp/get_qr`,
                method: 'GET',
                success: function(data) {
                    if(data.is_login == 0 || data.is_login == null){
                        if(data.qr_url != '' && data.qr_url != undefined){
                            document.getElementById("qrcode").src = data.qr_url;
                        }
                    } 
                    if(data.is_login == 1){ 
                        // toastr.success(data.message);
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: `${data.message}`,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        location.reload();
                    }
                },
                error: function(xhr) {
                    alert('Error fetching admin data: ' + xhr.responseJSON.message);
                }
                });
        <?php }?>
    }
    setInterval(update_qr,10000);

    $('#account_active1').on('click', function() {
        let id = $(this).data('id');
        $.ajax({
            url: `/dinetkan/whatsapp/template/${id}`,
            type: "GET",
            data: {
                id: id,
            },
            success: function(data) {
                $("#id").val(data.id);
                var account_active = data.account_active.replace(/<br>/gi, '\n');
                $('#account_active').val(account_active);


            }
        });
        $('#show_account_active').modal('show');
    });

    $('#invoice_terbit1').on('click', function() {
        let id = $(this).data('id');
        $.ajax({
            url: `/dinetkan/whatsapp/template/${id}`,
            type: "GET",
            data: {
                id: id,
            },
            success: function(data) {
                $("#id").val(data.id);
                var invoice_terbit = data.invoice_terbit.replace(/<br>/gi, '\n');
                $('#invoice_terbit').val(invoice_terbit);


            }
        });
        $('#show_invoice_terbit').modal('show');
    });

    $('#invoice_reminder1').on('click', function() {
        let id = $(this).data('id');
        $.ajax({
            url: `/dinetkan/whatsapp/template/${id}`,
            type: "GET",
            data: {
                id: id,
            },
            success: function(data) {
                $("#id").val(data.id);
                var invoice_reminder = data.invoice_reminder.replace(/<br>/gi, '\n');
                $('#invoice_reminder').val(invoice_reminder);
            }
        });
        $('#show_invoice_reminder').modal('show');
    });
    $('#invoice_overdue1').on('click', function() {
        let id = $(this).data('id');
        $.ajax({
            url: `/dinetkan/whatsapp/template/${id}`,
            type: "GET",
            data: {
                id: id,
            },
            success: function(data) {
                $("#id").val(data.id);
                var invoice_overdue = data.invoice_overdue.replace(/<br>/gi, '\n');
                $('#invoice_overdue').val(invoice_overdue);


            }
        });
        $('#show_invoice_overdue').modal('show');
    });
    $('#payment_paid1').on('click', function() {
        let id = $(this).data('id');
        $.ajax({
            url: `/dinetkan/whatsapp/template/${id}`,
            type: "GET",
            data: {
                id: id,
            },
            success: function(data) {
                $("#id").val(data.id);
                var payment_paid = data.payment_paid.replace(/<br>/gi, '\n');
                $('#payment_paid').val(payment_paid);
            }
        });
        $('#show_payment_paid').modal('show');
    });
    $('#payment_cancel1').on('click', function() {
        let id = $(this).data('id');
        $.ajax({
            url: `/dinetkan/whatsapp/template/${id}`,
            type: "GET",
            data: {
                id: id,
            },
            success: function(data) {
                $("#id").val(data.id);
                var payment_cancel = data.payment_cancel.replace(/<br>/gi, '\n');
                $('#payment_cancel').val(payment_cancel);


            }
        });
        $('#show_payment_cancel').modal('show');
    });

    $('#tiket_open_pelanggan1').on('click', function() {
        let id = $(this).data('id');
        $.ajax({
            url: `/dinetkan/whatsapp/template/${id}`,
            type: "GET",
            data: {
                id: id,
            },
            success: function(data) {
                $("#id").val(data.id);
                var tiket_open_pelanggan = data.tiket_open_pelanggan.replace(/<br>/gi, '\n');
                $('#tiket_open_pelanggan').val(tiket_open_pelanggan);


            }
        });
        $('#show_tiket_open_pelanggan').modal('show');
    });

    $('#tiket_open_teknisi1').on('click', function() {
        let id = $(this).data('id');
        $.ajax({
            url: `/dinetkan/whatsapp/template/${id}`,
            type: "GET",
            data: {
                id: id,
            },
            success: function(data) {
                $("#id").val(data.id);
                var tiket_open_teknisi = data.tiket_open_teknisi.replace(/<br>/gi, '\n');
                $('#tiket_open_teknisi').val(tiket_open_teknisi);


            }
        });
        $('#show_tiket_open_teknisi').modal('show');
    });

    $('#tiket_close_pelanggan1').on('click', function() {
        let id = $(this).data('id');
        $.ajax({
            url: `/dinetkan/whatsapp/template/${id}`,
            type: "GET",
            data: {
                id: id,
            },
            success: function(data) {
                $("#id").val(data.id);
                var tiket_close_pelanggan = data.tiket_close_pelanggan.replace(/<br>/gi, '\n');
                $('#tiket_close_pelanggan').val(tiket_close_pelanggan);


            }
        });
        $('#show_tiket_close_pelanggan').modal('show');
    });

    $('#tiket_close_teknisi1').on('click', function() {
        let id = $(this).data('id');
        $.ajax({
            url: `/dinetkan/whatsapp/template/${id}`,
            type: "GET",
            data: {
                id: id,
            },
            success: function(data) {
                $("#id").val(data.id);
                var tiket_close_teknisi = data.tiket_close_teknisi.replace(/<br>/gi, '\n');
                $('#tiket_close_teknisi').val(tiket_close_teknisi);


            }
        });
        $('#show_tiket_close_teknisi').modal('show');
    });
</script>
@endpush