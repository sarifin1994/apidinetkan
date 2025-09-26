@extends('backend.layouts.app_new')
@section('main')
@section('title', 'PPPoE Online')
<!-- Content -->
<div class="container-fluid">
    <!-- Page header -->
     <div class="row align-items-center mb-7">
        <!-- Breadcrumb and Title -->
        <div class="col-md-6 mb-3 mb-md-0">
            <h4 class="main-title">User Online</h4>
            <ul class="app-line-breadcrumbs mb-0">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <span><i class="ti ti-router f-s-16"></i> PPPoE</span>
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="javascript:void(0)">Online</a>
                </li>
            </ul>
        </div>
         <div class="d-flex flex-wrap justify-content-md-end gap-2">
                <button type="button" class="btn btn-sm btn-primary" id="sync">
                    <i class="ti ti-refresh"></i> Sinkronkan
                </button>
         </div>
    </div><br/>

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
                        <th>Last Login</th>
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
                    return '<a href="javascript:void(0)" title="KICK USER" id="kick" data-id="' + row.username + '">' +
       '<i class="ti ti-logout text-danger"></i></a>';
                }
            },
            {
                data: 'session_id',
                name: 'session_id',
                render: function(data) {
                    return '<span class="badge bg-success">' + data + '</span>'
                },
            },
            {
                data: 'username',
                name: 'username'
            },
            {
                data: 'ip',
                name: 'ip',
                render: function(data, type, row) {
                    return '<a href="http://' + data + '" target="_blank">' + data + '</a>';
                }
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
                defaultContent: 'ALL',
                // searchable:false,
                // sortable:false,
            },
            {
                data: 'ppp.kode_area',
                name: 'ppp.kode_area',
                defaultContent: '',
            },
            {
                data: 'ppp.kode_odp',
                name: 'ppp.kode_odp',
                defaultContent: '',
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
                data: 'start',
                name: 'start'
            },
        ]
    });

    $('#myTable').on('click', '#kick', function() {
        let username = $(this).data('id');

        Swal.fire({
            title: 'Apakah anda yakin?',
            text: `"${username}" akan logout dari mikrotik`,
            icon: 'warning',
            showCancelButton: !0,
            reverseButtons: !0,
            confirmButtonText: "Ya, kick",
            cancelButtonText: "Batal",
            confirmButtonColor: "#d33",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: `/pppoe/user/kick/${username}`,
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
                                title: 'Berhasil!',
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
                                el.after($('<span class="text-sm text-danger">' +
                                    value[0] +
                                    '</span>'));
                            });
                        }
                    },

                    error: function(err) {
                        $("#message").html("Terjadi kesalahan saat menendang pengguna!")
                    }
                });
            }
        });
    });


    $('#sync').on('click', function() {
        Swal.fire({
            title: "Apakah anda yakin?",
            icon: 'warning',
            text: "Semua sesi online akan dihapus dan dikick dari mikrotik. Harap tunggu jangan diclose!",
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
                    url: `/pppoe/user/sync`,
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
