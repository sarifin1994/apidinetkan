@extends('backend.layouts.app')
@section('main')
@section('title', 'PPPoE Offline')
<!-- Content -->
<div class="container-lg">
    <!-- Page header -->
    <div class="row align-items-center mb-7">
        <div class="col-auto">
            <!-- Avatar -->
            <div class="avatar avatar-xl rounded text-primary">
                <span class="fs-2 material-symbols-outlined">
                    wifi_off
                </span>
            </div>
        </div>
        <div class="col">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a class="text-body-secondary" href="#">PPPoE</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Offline</li>
                </ol>
            </nav>

            <!-- Heading -->
            <h1 class="fs-4 mb-0">User Offline</h1>
        </div>
        {{-- <div class="col-12 col-sm-auto mt-4 mt-sm-0" >
            <!-- Action -->
            <button class="btn btn-primary me-2 mb-2" id="sync"> <span
                    class="material-symbols-outlined me-1">sync</span> Sinkronkan</button>
        </div> --}}

        {{-- <div class="col-12 col-sm-auto mt-4 mt-sm-0" >
            <!-- Action -->
            <button class="btn btn-primary me-2 mb-2" data-bs-toggle="modal" data-bs-target="#create"> <span
                    class="material-symbols-outlined me-1">add</span> Create</button>
            <div class="btn-group">
                <button type="button" class="btn btn-danger me-2 dropdown-toggle"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="material-symbols-outlined">
                        edit_square
                    </span> Action<span class="row-count badge bg-dark text-light ms-1"></span>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" id="enable">Aktifkan</a></li>
                    <li><a class="dropdown-item" id="disable">Nonaktifkan</a></li>
                    <li><a class="dropdown-item" id="regist">Proses Registrasi</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" id="delete">Hapus</a></li>
                </ul>
            </div>
            <button class="btn btn-success me-2 mb-2" data-bs-toggle="modal" data-bs-target="#export"> <span
                    class="material-symbols-outlined me-1">file_export</span> Export </button>
            <button class="btn btn-warning me-2 mb-2" data-bs-toggle="modal" data-bs-target="#import"> <span
                    class="material-symbols-outlined me-1">file_save</span> Import </button>
        </div> --}}
    </div>

    <!-- Page content -->
    <div class="row">
        <div class="card-body table-responsive">
            <table id="myTable" class="table table-responsive table-hover display nowrap" width="100%">
                <thead>
                    <tr>
                        <th style="text-align:left!important">#</th>
                        <th>Session</th>
                        <th>Username</th>
                        <th>IP Address</th>
                        <th>MAC</th>
                        <th>Full Name</th>
                        <th>NAS</th>
                        <th>POP</th>
                        <th>ODP</th>
                        <th>Upload</th>
                        <th>Download</th>
                        <th>Uptime</th>
                        <th>Last Logout</th>
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
        order: [
            [12, 'desc']
        ],
        columns: [{
                data: null,
                'sortable': false,
                render: function(data, type, row, meta) {
                    return '<a href="javascript:void(0)" title="DELETE SESSION" id="delete" data-id=' +
                        row
                        .username +
                        '><span class="material-symbols-outlined text-danger">delete_sweep</span></a>';
                }
            },
            {
                data: 'session_id',
                name: 'session_id',
                render: function(data) {
                    return '<span class="badge bg-danger">' + data + '</span>'
                },
            },
            {
                data: 'username',
                name: 'username'
            },
            {
                data: 'ip',
                name: 'ip'
            },
            {
                data: 'mac',
                name: 'mac',
                // render: function(data) {
                //     return '<span class="badge bg-primary">' + data + '</span>'
                // },
            },
            {
                data: 'ppp.full_name',
                name: 'ppp.full_name',
                defaultContent: ''
            },
            {
                data: 'mnas.name',
                name: 'mnas.name',
                defaultContent: 'ALL'
            },
            {
                data: 'ppp.kode_area',
                name: 'ppp.kode_area',
                defaultContent: ''
            },
            {
                data: 'ppp.kode_odp',
                name: 'ppp.kode_odp',
                defaultContent: ''
            },
            {
                data: 'input',
                render: function bytesToSize(data) {
                    var sizes = ['Bytes', 'KB', 'MB', 'GB',
                        'TB'
                    ];
                    if (data == 0) return 'n/a';
                    var i = parseInt(Math.floor(Math.log(
                        data) / Math.log(1024)));
                    if (i == 0) return data + ' ' + sizes[i];
                    return (data / Math.pow(1024, i)).toFixed(
                        1) + ' ' + sizes[i];
                }
            },
            {
                data: 'output',
                render: function bytesToSize(data) {
                    var sizes = ['Bytes', 'KB', 'MB', 'GB',
                        'TB'
                    ];
                    if (data == 0) return 'n/a';
                    var i = parseInt(Math.floor(Math.log(
                        data) / Math.log(1024)));
                    if (i == 0) return data + ' ' + sizes[i];
                    return (data / Math.pow(1024, i)).toFixed(
                        1) + ' ' + sizes[i];
                }
            },
            {
                data: 'uptime',
                render: function convertSecondsToReadableString(
                    seconds) {
                    seconds = seconds || 0;
                    seconds = Number(seconds);
                    seconds = Math.abs(seconds);

                    const d = Math.floor(seconds / (3600 * 24));
                    const h = Math.floor(seconds % (3600 * 24) /
                        3600);
                    const m = Math.floor(seconds % 3600 / 60);
                    const s = Math.floor(seconds % 60);
                    const parts = [];

                    if (d > 0) {
                        parts.push(d + 'd');
                    }

                    if (h > 0) {
                        parts.push(h + 'h');
                    }

                    if (m > 0) {
                        parts.push(m + 'm');
                    }

                    // if (s > 0) {
                    //     parts.push(s + ' second' + (s > 1 ? 's' :
                    //         ''));
                    // }

                    return parts.join(' ');
                }
            },
            {
                data: 'stop',
                name: 'stop'
            },
        ]
    });

    $('#myTable').on('click', '#delete', function() {
        let username = $(this).data('id');

        Swal.fire({
            title: 'Apakah anda yakin?',
            text: `"${username}" akan dihapus dari session offline`,
            icon: 'warning',
            showCancelButton: true,
            showCancelButton: !0,
            reverseButtons: !0,
            confirmButtonText: "Ya, hapus",
            cancelButtonText: "Cancel",
            confirmButtonColor: "#d33",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: `/pppoe/user/offline/delete`,
                    type: "POST",
                    cache: false,
                    data: {
                        username: username
                    },
                    dataType: "json",

                    success: function(data) {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: `${data.message}`,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            setTimeout(function() {
                                table.ajax.reload()
                            }, 1500);
                        } else {
                            $.each(data.error, function(key, value) {
                                var el = $(document).find('[name="' + key + '"]');
                                el.after($('<span class= "text-sm text-danger">' +
                                    value[0] +
                                    '</span>'));
                            });
                        }
                    },

                    error: function(err) {
                        $("#message").html("Some Error Occurred!")
                    }
                });
            }
        });
    });


    $('#sync').on('click', function() {
        Swal.fire({
            title: "Apakah anda yakin?",
            icon: 'warning',
            text: "Semua sesi offline akan dihapus dan dikick dari mikrotik. Harap tunggu jangan diclose!",
            showCancelButton: true,
            reverseButtons: true,
            confirmButtonText: "Ya, Sinkronkan",
            cancelButtonText: "Batal",
            confirmButtonColor: "#d33",
            showLoaderOnConfirm: true,
            preConfirm: () => {
                // Setup CSRF token untuk AJAX
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // Jalankan AJAX dan kembalikan promise-nya
                return $.ajax({
                    url: `/pppoe/user/sync/offline`,
                    type: "POST",
                    cache: false,
                    dataType: "json"
                }).then(response => {
                    if (!response.success) {
                        return Promise.reject(response.error || 'Unknown error');
                    }
                    return response;
                }).catch(error => {
                    // Tampilkan pesan error yang lebih informatif
                    let errMsg = error.responseText || error.statusText || JSON.stringify(
                        error) || "Unknown error";
                    Swal.showValidationMessage(`Request failed: ${errMsg}`);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: result.value.message,
                    showConfirmButton: false,
                    timer: 1500
                });
                setTimeout(function() {
                    table.ajax.reload();
                }, 1500);
                $('#head-cb').prop('checked', false);
                $(".row-count").html('');
            }
        });
    });
</script>
@endpush
