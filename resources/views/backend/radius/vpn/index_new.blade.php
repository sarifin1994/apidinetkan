@extends('backend.layouts.app_new')
@section('main')
@section('title', 'VPN')
<!-- Content -->
<div class="container-fluid">
    <!-- Page header -->
    <div class="row align-items-center mb-7">
        <!-- Breadcrumb and Title -->
        <div class="col-md-6 mb-3 mb-md-0">
            <h4 class="main-title">VPN</h4>
            <ul class="app-line-breadcrumbs mb-0">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <span><i class="ti ti-world f-s-16"></i> Radius</span>
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="javascript:void(0)">VPN</a>
                </li>
            </ul>
        </div>

        <!-- Buttons -->
        <div class="d-flex flex-wrap justify-content-md-end gap-2">
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#create">
                <i class="ti ti-plus"></i> Tambah
            </button>
        </div>
    </div>
    {{-- <div class="alert alert-primary p-6" role="alert">VPN digunakan untuk menghubungkan Mikrotik Anda dengan Radius
        Server kami melalui jaringan internet
        <hr>Silakan create account VPN terlebih dahulu agar Radius Server
        kami dapat merespon request yang dikirimkan oleh Mikrotik Anda
    </div> --}}
    <!-- Page content -->
    @include('backend.radius.vpn.modal.create')
    @include('backend.radius.vpn.modal.show')
    <br/>
    <div class="row">
        <div class="card-body table-responsive">
            <table id="myTable" class="table table-responsive table-hover display nowrap" width="100%">
                <thead>
                    <tr>
                        <th style="text-align:left!important">No</th>
                        <th>Nama</th>
                        <th>User</th>
                        <th>Password</th>
                        <th>IP Address</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script type="text/javascript">
    let table = $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: '{{ url()->current() }}',
        columns: [{
                data: null,
                'sortable': false,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'user',
                name: 'user'
            },
            {
                data: 'password',
                name: 'password',
                sortable: false,
                searchable: false,
            },
            {
                data: 'ip_address',
                name: 'ip_address',
                sortable: false,
                searchable: false,
            },
            {
                data: 'action',
                name: 'action'
            }
        ]
    });

    // action create
    $('#store').click(function(e) {
        e.preventDefault();

        // Hapus pesan error jika ada
        var error_ele = document.getElementsByClassName('form-text text-danger');
        if (error_ele.length > 0) {
            for (var i = error_ele.length - 1; i >= 0; i--) {
                error_ele[i].remove();
            }
        }

        // Kumpulkan data dari form
        var data = {
            'vpn_server': $('#vpn_server option:selected').val(),
            'name': $('#name').val(),
            'user': $('#user').val(),
            'password': $('#password').val(),
        };

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Simpan referensi tombol dan teks aslinya
        var btn = $(this);
        var originalText = btn.html();

        // Ubah tampilan tombol: nonaktifkan tombol dan tampilkan teks dengan spinner di sebelah kanan
        btn.prop('disabled', true).html(
            'Memproses... <span class="material-symbols-outlined spinner">progress_activity</span>');

        $.ajax({
            url: `/radius/vpn`,
            type: "POST",
            cache: false,
            data: data,
            dataType: "json",
            success: function(data) {
                // Kembalikan tombol ke kondisi semula
                btn.prop('disabled', false).html(originalText);
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: `${data.message}`,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    setTimeout(function() {
                        table.ajax.reload();
                        $('input').val('');
                        $('#create').modal('hide');
                    }, 1500);
                } else {
                    $.each(data.error, function(key, value) {
                        var el = $(document).find('[name="' + key + '"]');
                        el.after($('<div class="form-text text-danger">' + value[0] +
                            '</div>'));
                    });
                }
            },
            error: function(err) {
                btn.prop('disabled', false).html(originalText);
                $("#message").html("Some Error Occurred!");
            }
        });
    });


    $('#myTable').on('click', '#delete', function() {

        let id = $(this).data('id');
        $.ajax({
            url: `/radius/vpn/${id}`,
            type: "GET",
            cache: false,
            success: function(response) {
                var vpn_user = response.data.user;
                Swal.fire({
                    title: "Apakah anda yakin?",
                    icon: 'warning',
                    text: "Data yang sudah dihapus tidak dapat dikembalikan",
                    showCancelButton: !0,
                    reverseButtons: !0,
                    confirmButtonText: "Ya, Hapus!",
                    cancelButtonText: "Batal",
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
                            url: `/radius/vpn/${id}`,
                            type: "POST",
                            cache: false,
                            data: {
                                _method: "DELETE",
                                'vpn_user': vpn_user,
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
                                        table.ajax.reload()
                                    });
                            },

                            error: function(err) {
                                $("#message").html(
                                    "Some Error Occurred!"
                                )
                            }

                        });
                    }
                });
            }
        });

    });

    $('#myTable').on('click', '#show', function() {

        let vpn_id = $(this).data('id');

        //fetch detail post with ajax
        $.ajax({
            url: `/radius/vpn/${vpn_id}`,
            type: "GET",
            cache: false,
            success: function(response) {
                var radius = '{{ env('IP_RADIUS_SERVER') }}';

                //fill data to form
                $('#copyl2tp').html('/interface l2tp-client&#10;add connect-to=' + response.data
                        .vpn_server +
                        ' name=RADIUSQU_L2TP user=' + response.data.user +
                        ' password=' + response.data.password +
                        ' disabled=no comment="added by radiusqu"&#10;/ip route&#10;add dst-address=' +
                        radius +
                        ' gateway=RADIUSQU_L2TP distance=1 disabled=no comment="added by radiusqu"&#10;'
                    ),
                    $('#copysstp').html('/interface sstp-client&#10;add connect-to=' + response.data
                        .vpn_server +
                        ' name=RADIUSQU_SSTP user=' + response.data.user +
                        ' password=' + response.data.password +
                        ' disabled=no comment="added by radiusqu"&#10;/ip route&#10;add dst-address=' +
                        radius +
                        ' gateway=RADIUSQU_SSTP distance=2 disabled=no comment="added by radiusqu"&#10;'
                    ),
                    $('#copypptp').html('/interface pptp-client&#10;add connect-to=' + response.data
                        .vpn_server +
                        ' name=RADIUSQU_PPTP user=' + response.data.user +
                        ' password=' + response.data.password +
                        ' disabled=no comment="added by radiusqu"&#10;/ip route&#10;add dst-address=' +
                        radius +
                        ' gateway=RADIUSQU_PPTP distance=3 disabled=no comment="added by radiusqu"&#10;'
                    ),

                    //open modal
                    $('#show').modal('show');
            }
        });
    });

    function copyl2tp() {
        var copyText = document.getElementById("copyl2tp");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);

        var tooltip = document.getElementById("l2tp");
        tooltip.innerHTML = "Copied";
    }

    function outl2tp() {
        var tooltip = document.getElementById("l2tp");
        tooltip.innerHTML = "Copy";
    }

    function copysstp() {
        var copyText = document.getElementById("copysstp");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);

        var tooltip = document.getElementById("sstp");
        tooltip.innerHTML = "Copied";
    }

    function outsstp() {
        var tooltip = document.getElementById("sstp");
        tooltip.innerHTML = "Copy";
    }

    function copypptp() {
        var copyText = document.getElementById("copypptp");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);

        var tooltip = document.getElementById("pptp");
        tooltip.innerHTML = "Copied";
    }

    function outpptp() {
        var tooltip = document.getElementById("pptp");
        tooltip.innerHTML = "Copy";
    }
</script>
@endpush
