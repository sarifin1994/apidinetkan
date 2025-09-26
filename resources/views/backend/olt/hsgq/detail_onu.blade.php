@extends('backend.layouts.app')
@section('main')
@section('title', 'Detail ONU')
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
            <li class="breadcrumb-item active" aria-current="page">ONU</li>
          </ol>
        </nav>

        <!-- Heading -->
        <h1 class="fs-4 mb-0">{{ $data['base']->onu_name }}</h1>
      </div>
      <div class="col-12 col-sm-auto mt-4 mt-sm-0">
        <div class="col-auto">
            <a href="/olt/hsgq/pon/{{ $data['base']->port_id }}" class="btn btn-primary text-light me-2">
                <span class="material-symbols-outlined">
                    reply
                </span> KEMBALI
            </a>
        </div>
          </div>
    </div>

        <div class="row">
            <div class="col-lg-7">
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="h5 col-auto">ONU INFORMATION</div>
                    </div>
                    @include('backend.olt.modal.rename')
                    <div class="card-body">
                        <table style="font-size:14px" id="myTable" class="table table-bordered display nowrap" width="100%">
                            <tr>
                                <td scope="col">Name</td>
                                <td scope="col">{{ $data['base']->onu_name }}</td>
                            </tr>
                            <tr>
                                <td scope="col">Mac Address</td>
                                <td scope="col">{{ $data['base']->macaddr }}</td>
                            </tr>
                            <tr>
                                <td scope="col">Distance</td>
                                <td scope="col">{{ $data['base']->distance }} Meter</td>
                            </tr>
                            <tr>
                                <td scope="col">Vendor</td>
                                <td scope="col">{{ $data['base']->vendor }}</td>
                            </tr>
                            <tr>
                                <td scope="col">Model</td>
                                <td scope="col">{{ $data['base']->sn_model }}</td>
                            </tr>
                            {{-- <tr>
                                <td scope="col">Status</td>
                                @if ($data['user'] === null)
                                    <td scope="col" class="text-warning">Belum Sinkron</td>
                                @else
                                    <td scope="col" class="text-success">Sudah Sinkron</td>
                                @endif

                            </tr> --}}
                        </table>
                        <div class="col-auto">
                            <button class="btn btn-primary text-light me-2 mb-2" type="button" data-bs-toggle="modal"
                                data-bs-target="#rename">
                                <span class="material-symbols-outlined">
                                    edit_document
                                    </span> Rename
                            </button>
                            <button class="btn btn-secondary text-light me-2 mb-2" id="reboot_onu"
                                data-port="{{ $data['base']->port_id }}" data-onu="{{ $data['base']->onu_id }}">
                                <span class="material-symbols-outlined">
                                    restart_alt
                                    </span> Reboot
                            </button>
                            <button class="btn btn-danger text-light me-2 mb-2" id="delete_onu"
                                data-port="{{ $data['base']->port_id }}" data-onu="{{ $data['base']->onu_id }}"
                                data-mac="{{ $data['base']->macaddr }}">
                                <span class="material-symbols-outlined">
                                    delete
                                    </span> Delete
                            </button>
                        </div>
                        <hr>
                        {{-- <div class="col-auto mt-3">
                            @if ($data['user'] !== null)
                                <div class="text-left mb-3">
                                    <b>MEMBER INFORMATION</b>
                                </div>
                                <table style="font-size:14px" id="myTable" class="table table-bordered"
                                    width="100%">
                                    <tr>
                                        <td scope="col" width="45%">Full Name</td>
                                        <td scope="col">{{ $data['user']['member']->full_name }}</td>
                                    </tr>
                                    <tr>
                                        <td scope="col">POP</td>
                                        <td scope="col">{{ $data['user']['ppp']->kode_area }}</td>
                                    </tr>
                                    <tr>
                                        <td scope="col">Kode ODP</td>
                                        <td scope="col">{{ $data['user']['ppp']->kode_odp }}</td>
                                    </tr>
                                    <tr>
                                        <td scope="col">Alamat</td>
                                        <td scope="col">{{ $data['user']['member']->address }}</td>
                                    </tr>
                                    @if ($data['session'] !== null)
                                        <tr>
                                            <td scope="col">Status Internet</td>
                                            @if ($data['session']->status === 1)
                                                <td scope="col" class="text-success">Online</td>
                                            @else
                                                <td scope="col" class="text-danger">Offline</td>
                                            @endif
                                        </tr>
                                        <tr>
                                            <td scope="col">IP Address</td>
                                            <td scope="col">{{ $data['session']->ip }}</td>
                                        </tr>
                                    @endif
                                </table>
                            @endif

                        </div> --}}

                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <div class="h5 col-auto">ONU OPTICAL DIAGNOSE</div>
                        <div class="col-auto">
                            {{-- <button class="btn btn-primary text-light" type="button" data-bs-toggle="modal"
                                data-bs-target="#create">
                                <i class="fas fa-circle-plus"></i>&nbspCreate
                            </button> --}}
                        </div>
                    </div>
                    <div class="card-body">
                        <table style="font-size:14px" id="myTable" class="table table-bordered" width="100%">
                            <tr>
                                <td scope="col">Temperature</td>
                                <td scope="col">{{ $data['optic']->work_temprature }}</td>
                            </tr>
                            <tr>
                                <td scope="col">Voltage</td>
                                <td scope="col">{{ $data['optic']->work_voltage }}</td>
                            </tr>
                            <tr>
                                <td scope="col">Transmit Power</td>
                                <td scope="col">{{ $data['optic']->transmit_power }}</td>
                            </tr>
                            <tr>
                                <td scope="col">Receive Power</td>
                                <td scope="col">{{ $data['optic']->receive_power }}</td>
                            </tr>
                            <tr>
                                <td scope="col">Status</td>
                                @if ($data['base']->status === 'Online')
                                    <td scope="col"><span class="text-success">Online</span></td>
                                @elseif($data['base']->status === 'Offline')
                                    <td scope="col"><span class="text-danger">Offline</span></td>
                                @elseif($data['base']->status === 'Initial')
                                    <td scope="col"><span class="text-warning">Initial</span></td>
                                @endif
                            </tr>
                        </table>
                        {{-- <ol>
                            <li>Syncronize berfungsi untuk menautkan onu ke data member</li>
                            <li>Apabila sudah melakukan syncronize maka tidak perlu melakukan rename onu</li>
                            <li>Apabila tidak mempunyai data member, silakan lakukan rename onu secara manual</li>
                            <li>Delete onu apabila sudah tidak digunakan</li>
                        </ol> --}}


                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
@push('scripts')
<script type="text/javascript">
    $('#reboot_onu').on('click', function() {

        let port = $(this).data('port');
        let onu = $(this).data('onu');

        Swal.fire({
            title: "Reboot",
            icon: 'warning',
            text: "Are you sure to Reboot ONU? ",
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
                    url: `/olt/hsgq/reboot/pon/${port}/onu/${onu}`,
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
                            timer: 3000
                        });
                        setTimeout(
                            function() {
                                location
                                    .reload();
                            }, 3000);
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

    $('#delete_onu').on('click', function() {

        let port = $(this).data('port');
        let onu = $(this).data('onu');
        let mac = $(this).data('mac');

        Swal.fire({
            title: "Delete",
            icon: 'warning',
            text: "Are you sure to Delete ONU? ",
            showCancelButton: !0,
            reverseButtons: !0,
            confirmButtonText: "Yes, delete!",
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
                    url: `/olt/hsgq/delete/pon/${port}/onu/${onu}`,
                    type: "POST",
                    cache: false,
                    data:{
                        mac:mac,
                    },
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
                                window.location.href=`/olt/hsgq/pon/${port}`
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

    $("#member_id").on("change", function() {
        var member_id = $(this).val();
        $.ajax({
            url: `/ticket/ggn/getArea/${member_id}`,
            type: "GET",
            cache: false,
            data: {
                member_id: member_id,
                '_token': '{{ csrf_token() }}'
            },
            success: function(data) {
                $('#fill_name').html(data[0].full_name);
                $('#fill_alamat').html(data[0].address);
                if (data[0].kode_area === null) {
                    $('#fill_area').html('<i>NULL</i>');
                } else {
                    $('#fill_area').html(data[0].kode_area);
                }
                $('#fill_odp').html(data[0].ppp.kode_odp);
                $('#fill_pppoe_id').val(data[0].ppp.id);

                let username = data[0].ppp.username;
                if (username) {
                    $.ajax({
                        url: `/ticket/ggn/getSession/${username}`,
                        type: 'GET',
                        data: {
                            pppoe_username: username
                        },
                        success: function(response) {
                            $('#fill_mac_wan').html(response.mac)
                        }
                    });

                } else {

                }
            }
        });
    });

    $('#sync_onu').click(function(e) {
        e.preventDefault();
        var error_ele = document.getElementsByClassName('alert text-sm');
        if (error_ele.length > 0) {
            for (var i = error_ele.length - 1; i >= 0; i--) {
                error_ele[i].remove();
            }
        }

        // collect data by id
        var data = {
            'member_id': $('#member_id option:selected').val(),
            'member_name': $('#member_id option:selected').text(),
            'pppoe_id': $('#fill_pppoe_id').val(),
            'port_id': $('#port_id').val(),
            'onu_id': $('#onu_id').val(),
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // ajax proses
        $.ajax({
            url: `/olt/onu/sync`,
            type: "POST",
            cache: false,
            data: data,
            dataType: "json",

            // tampilkan pesan Success

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
                        var el = $(document).find('[for="' + key + '"]');
                        el.after($('<span class= "alert text-sm text-danger">' +
                            value[0] +
                            '</div>'));
                    });
                }
            },

            error: function(err) {
                $("#message").html("Some Error Occurred!")
            }

        });
    });

    $('#rename_onu').click(function(e) {
        e.preventDefault();
        var error_ele = document.getElementsByClassName('alert text-sm');
        if (error_ele.length > 0) {
            for (var i = error_ele.length - 1; i >= 0; i--) {
                error_ele[i].remove();
            }
        }

        // collect data by id
        var data = {
            'onu_name': $('#onu_name').val(),
            'port_id': $('#port_id_rename').val(),
            'onu_id': $('#onu_id_rename').val(),
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // ajax proses
        $.ajax({
            url: `/olt/hsgq/onu/rename`,
            type: "POST",
            cache: false,
            data: data,
            dataType: "json",

            // tampilkan pesan Success

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
                        var el = $(document).find('[for="' + key + '"]');
                        el.after($('<span class= "alert text-sm text-danger">' +
                            value[0] +
                            '</div>'));
                    });
                }
            },

            error: function(err) {
                $("#message").html("Some Error Occurred!")
            }

        });
    });

    $('#member_id').select2({
        allowClear: true,
        dropdownParent: $("#sync .modal-content"),
        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
        placeholder: $(this).data('placeholder'),
    });

</script>
@endpush
