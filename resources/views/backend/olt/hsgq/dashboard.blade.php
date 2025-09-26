@extends('backend.layouts.app')
@section('main')
@section('title', 'Dashboard OLT')


<div class="container-lg">
    <!-- Page header -->
    <div class="row align-items-center mb-7">
        <div class="col-auto">
            <!-- Avatar -->
            <div class="avatar avatar-xl rounded text-primary">
                <i class="fs-2" data-duoicon="airplay"></i>
            </div>
        </div>
        <div class="col">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a class="text-body-secondary" href="#">OLT</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
            </nav>

            <!-- Heading -->
            <h1 class="fs-4 mb-0">{{ $data['namaolt'] }}</h1>
        </div>
        <div class="col-12 col-sm-auto mt-4 mt-sm-0">
            <div class="col-auto mt-4">
                <button class="btn btn-success text-light me-2 mb-2" id="save" data-olt="{{ $data['namaolt'] }}">
                    <span class="material-symbols-outlined">
                        save
                    </span> Save
                </button>
                <button class="btn btn-danger text-light me-2 mb-2" id="reboot" data-olt="{{ $data['namaolt'] }}">
                    <span class="material-symbols-outlined">
                        restart_alt
                    </span> Reboot
                </button>
                <a class="btn btn-warning text-light me-2 mb-2  " href="/olt/hsgq/logout">
                    <span class="material-symbols-outlined">
                        Logout
                    </span> Logout
                </a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">

            <div class="card-body table-responsive">
                <div class="row mb-3">
                    <div class="col-lg-6">
                        <table style="font-size:14px" id="myTable" class="table display nowrap" width="100%">
                            <tr>
                                <td scope="col">OLT IDENTITY</td>
                                <td scope="col">: {{ $data['namaolt'] }}</td>
                            </tr>
                            <tr>
                                <td scope="col">PRODUCT MODEL</td>
                                <td scope="col">: {{ $data['device']['product_name'] }}</td>
                            </tr>
                            <tr>
                                <td scope="col">SYSTEM VERSION</td>
                                <td scope="col">: {{ $data['device']['sys_ver'] }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-lg-6">
                        <table style="font-size:14px" id="myTable" class="table display nowrap" width="100%">
                            <tr>
                                <td scope="col">DEVICE TYPE</td>
                                @if ($data['device']['device_type'] === 1)
                                    <td scope="col">: EPON</td>
                                @endif
                            </tr>
                            <tr>
                                <td scope="col">PON PORT</td>
                                <td scope="col">: {{ $data['device']['ponports'] }} PON</td>
                            </tr>
                            <tr>
                                <td scope="col">UPTIME</td>
                                <td scope="col">: {{ $data['time']['uptime']['0'] }} Days
                                    {{ $data['time']['uptime']['1'] }} Hours {{ $data['time']['uptime']['2'] }}
                                    Minute</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <table id="myTable" class="table table-hover table-bordered display nowrap" width="100%">
                    <thead>
                        <tr>
                            <th scope="col">PON</th>
                            <th scope="col">ONLINE</th>
                            <th scope="col">OFFLINE</th>
                            <th scope="col">TOTAL</th>
                            <th scope="col">STATUS</th>
                            <th scope="col">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['pon'] as $row)
                            @php
                                if ($row['status'] == '1') {
                                    $status = 'Running';
                                    $badge = 'success';
                                } else {
                                    $status = 'Offline';
                                    $badge = 'danger';
                                }
                                $total = $row['online'] + $row['offline'];
                            @endphp
                            <tr>
                                <td><span class=""> PON {{ $row['port_id'] }}</span>
                                </td>
                                <td><span class="badge bg-success-subtle text-success">Online</span> : <span
                                        style="font-size:12px">{{ $row['online'] }} ONU</span></td>
                                <td><span class="badge bg-danger-subtle text-danger">Offline</span> : <span
                                        style="font-size:12px">{{ $row['offline'] }} ONU</span></td>
                                <td><span class="badge bg-primary-subtle text-primary">Total</span> : <span
                                        style="font-size:12px">{{ $total }} ONU</span></td>
                                <td><span
                                        class="badge bg-{{ $badge }}-subtle text-{{ $badge }}">{{ $status }}</span>
                                </td>

                                @if ($total === 0)
                                    <td><a href="#!" class="btn btn-danger"><span
                                                class="material-symbols-outlined">
                                                visibility_off
                                            </span> Lihat</a>
                                    </td>
                                @else
                                    <td>
                                        <a href="/olt/hsgq/pon/{{ $row['port_id'] }}" class="btn btn-primary">
                                            <span class="material-symbols-outlined">
                                                visibility
                                            </span> Lihat</a>
                                    </td>
                                @endif
                            </tr>
                        @endforeach


                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">
    $('#reboot').on('click', function() {

        let olt = $(this).data('olt');

        Swal.fire({
            title: "Reboot",
            icon: 'warning',
            text: "Are you sure to Reboot OLT? ",
            showCancelButton: !0,
            reverseButtons: !0,
            confirmButtonText: "Yes, reboot!",
            cancelButtonText: "Cancel",
            confirmButtonColor: "#d33",
            // cancelButtonColor: "#d33",
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $(
                                'meta[name="csrf-token"]'
                            )
                            .attr('content')
                    }
                });

                $.ajax({
                    url: `/olt/hsgq/reboot/${olt}}`,
                    type: "POST",
                    cache: false,
                    dataType: "json",

                    // tampilkan pesan Success
                    success: function(data) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: `${data.message}`,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        setTimeout(
                            function() {
                                window.location.href = '/olt'
                            }, 1500);
                    },

                    error: function(err) {
                        $("#message").html(
                            "Some Error Occurred!"
                        )
                    }

                });
            }
        });
    });


    $('#save').on('click', function() {
        let olt = $(this).data('olt');
        Swal.fire({
            title: "Save",
            icon: 'warning',
            text: "Are you sure to Save Configuration?",
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: "Yes, save!",
            cancelButtonText: "Cancel",
            confirmButtonColor: "#d33",
        }).then(function(result) {
            if (result.isConfirmed) {
                // Tampilkan loading
                Swal.fire({
                    title: 'Saving...',
                    icon: 'info',
                    html: 'Please wait while saving configuration.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });

                // Setup CSRF
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // Kirim AJAX
                $.ajax({
                    url: `/olt/hsgq/save/${olt}}`,
                    type: "POST",
                    dataType: "json",
                    cache: false,

                    success: function(data) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    },

                    error: function(err) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal menyimpan konfigurasi.',
                        });
                    }
                });
            }
        });
    });

    // $('#save').on('click', function() {

    //     let olt = $(this).data('olt');

    //     Swal.fire({
    //         title: "Save",
    //         icon: 'warning',
    //         text: "Are you sure to Save Configuration? ",
    //         showCancelButton: !0,
    //         reverseButtons: !0,
    //         confirmButtonText: "Yes, save!",
    //         cancelButtonText: "Cancel",
    //         confirmButtonColor: "#d33",
    //         // cancelButtonColor: "#d33",
    //     }).then(function(result) {
    //         if (result.isConfirmed) {
    //             $.ajaxSetup({
    //                 headers: {
    //                     'X-CSRF-TOKEN': $(
    //                             'meta[name="csrf-token"]'
    //                         )
    //                         .attr('content')
    //                 }
    //             });

    //             $.ajax({
    //                 url: `/olt/hsgq/save/${olt}}`,
    //                 type: "POST",
    //                 cache: false,
    //                 dataType: "json",

    //                 // tampilkan pesan Success
    //                 success: function(data) {
    //                     Swal.fire({
    //                         icon: 'success',
    //                         title: 'Success',
    //                         text: `${data.message}`,
    //                         showConfirmButton: false,
    //                         timer: 1500
    //                     });
    //                     setTimeout(
    //                         function() {
    //                             location.reload()
    //                         }, 1500);
    //                 },

    //                 error: function(err) {
    //                     $("#message").html(
    //                         "Some Error Occurred!"
    //                     )
    //                 }

    //             });
    //         }
    //     });
    // });
</script>
@endpush
