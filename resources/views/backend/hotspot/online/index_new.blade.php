@extends('backend.layouts.app_new')
@section('main')
@section('title', 'Hotspot Online')
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
                        <span><i class="ti ti-wifi f-s-16"></i> Hotspot</span>
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="javascript:void(0)">Online</a>
                </li>
            </ul>
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
                        <th>NAS</th>
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
            order : [[9,'desc']],
            columns: [{
                    data: null,
                    'sortable': false,
                    render: function(data, type, row, meta) {
                    return '<a href="javascript:void(0)" title="KICK USER" id="kick" data-id=' + row
                        .username +
                        '><span class="material-symbols-outlined text-danger">logout</span></a>';
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
                    name: 'ip'
                },
                {
                    data: 'mac',
                    name: 'mac',
                    render: function(data) {
                        return '<span class="badge bg-primary">' + data + '</span>'
                    },
                },
                {
                    data: 'mnas.name',
                    name: 'mnas.name',
                    // sortable:false,
                    // searchable:false,
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
                        title: 'Success',
                        text: `${data.message}`,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    setTimeout(function() {
                        table.ajax.reload()
                    });
                } else {
                    $.each(data.error, function(key, value) {
                        var el = $(document).find('[name="' + key + '"]');
                        el.after($('<span class= "text-sm text-danger">' +
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

    </script>
@endpush
