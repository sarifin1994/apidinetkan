@extends('backend.layouts.app_new')
@section('main')
@section('title', 'Mikrotik')
<!-- Content -->
<div class="container-fluid">
    <!-- Page header -->
    <div class="row align-items-center mb-7">
        <!-- Breadcrumb and Title -->
        <div class="col-md-6 mb-3 mb-md-0">
            <h4 class="main-title">Mikrotik NAS</h4>
            <ul class="app-line-breadcrumbs mb-0">
                <li>
                    <a class="f-s-14 f-w-500" href="#">
                        <span><i class="ti ti-world f-s-16"></i> Radius</span>
                    </a>
                </li>
                <li class="active">
                    <a class="f-s-14 f-w-500" href="javascript:void(0)">Mikrotik</a>
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
    {{-- <div class="alert alert-primary p-6" role="alert">Jika Mikrotik Anda tidak memiliki IP Public, siakan create account VPN terlebih dahulu di menu Mikrotik > VPN --}}

    <!-- Page content -->
    <br/>
      @include('backend.radius.mikrotik.modal.create')
            @include('backend.radius.mikrotik.modal.show')
    <div class="row">
        <div class="card-body table-responsive">
            <table id="myTable" class="table table-responsive table-hover display nowrap" width="100%">
                <thead>
                    <tr>
                        <th style="text-align:left!important">No</th>
                        <th>Nama</th>
                        <th>IP Address</th>
                        <th>Port API</th>
                        <th>Timezone</th>
                        <th>Status</th>
                        <th>Total User</th>
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
        lengthMenu: [5, 10, 20, 50],
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
                name: 'name',
                'sortable': false,
            },
            {
                data: 'ip_router',
                name: 'ip_router'
            },
            {
                data: 'port_api',
                name: 'port_api'
            },
            {
                data: 'timezone',
                name: 'timezone',
                render: function(data) {
                    if (data === '0') {
                        return 'Asia/Jakarta';
                    } else if (data === '3600') {
                        return 'Asia/Makassar';
                    } else if (data === '7200') {
                        return 'Asia/Jayapura';
                    }
                },
            },
            {
                data: 'ping',
                name: 'ping',
            },
            {
                data: 'total_session',
                name: 'total_session',
                render: function(data, type, row, meta) {
                    var id = 'total_session-' + meta.row;
                    // Tampilkan spinner sampai konten terupdate
                    return '<span id="' + id +
                        '"><span class="material-symbols-outlined spinner">progress_activity</span></span>';
                }

            },
            {
                data: 'action',
                name: 'action'
            }
        ],
        drawCallback: function(settings) {
            $('.ping-check').each(function() {
                let rowId = $(this).data('id');
                let cell = $(this);

                $.ajax({
                    url: "{{ route('ping.check') }}",
                    type: "POST",
                    data: {
                        id: rowId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        let statusHtml = response.ping ?
                            '<span class="badge bg-success-subtle text-success">Online</span>' :
                            '<span class="badge bg-danger-subtle text-danger">Offline</span>';
                        // Ganti spinner dengan hasil ping
                        cell.replaceWith(statusHtml);
                    },
                    error: function() {
                        cell.text('Error');
                    }
                });
            });
            updateTotalSession();
        }
    });

    // Fungsi tambahan yang dipanggil setelah DataTable diload
    function updateTotalSession() {
        table.rows().every(function(index, element) {
            var rowData = this.data();
            $.ajax({
                url: '/radius/mikrotik/update/getTotalSession', // Endpoint di Laravel
                type: "POST",
                data: {
                    id: rowData.id, // Mengirimkan id NAS
                    _token: "{{ csrf_token() }}" // Pastikan token CSRF tersedia
                },
                success: function(response) {
                    //   console.log(response);
                    // Asumsikan response mengembalikan { total_session: <jumlah> }
                    var updatedCount = response.total_session;
                    var content = '<span class="material-symbols-outlined">monitoring</span> ' +
                        updatedCount + ' online';
                    var el = document.getElementById('total_session-' + index);
                    if (el) {
                        el.innerHTML = content;
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error updating total_session for row: ', rowData, error);
                }
            });
        });
    }

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
            'name': $('#name').val(),
            'ip_router': $('#ip_router').val(),
            'secret': $('#secret').val(),
            'port_api': $('#port_api').val(),
            'timezone': $('#timezone').val(),
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

        // Proses AJAX
        $.ajax({
            url: `/radius/mikrotik`,
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
                        // Tambahkan class is-invalid untuk membuat border merah (jika menggunakan Bootstrap)
                        el.addClass('is-invalid');
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
                    url: `/radius/mikrotik/${id}`,
                    type: "POST",
                    cache: false,
                    data: {
                        _method: "DELETE"
                    },
                    dataType: "json",

                    // tampilkan pesan Success
                    success: function(data) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: `${data.message}`,
                            showConfirmButton: false,
                            timer: 1000
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
    });

    $('#myTable').on('click', '#show', function() {

        let id = $(this).data('id');

        //fetch detail post with ajax
        $.ajax({
            url: `/radius/mikrotik/${id}`,
            type: "GET",
            cache: false,
            success: function(response) {
                var radius = '{{ env('IP_RADIUS_SERVER') }}';

                //fill data to form
                $('#copyl2tp').html('/user &#10;add name=' + response.data.user + ' password=' +
                        response.data.password +
                        ' group=write comment="added by radiusqu"&#10;/radius &#10;add address=' +
                        radius +
                        ' secret=' + response.data.secret +
                        ' service=ppp,hotspot timeout=3000ms comment="added by radiusqu"&#10;/radius incoming&#10;set accept=yes'
                    ),
                    $('#copyl2tpros7').html('/user &#10;add name=' + response.data.user +
                        ' password=' +
                        response.data.password +
                        ' group=write comment="added by radiusqu"&#10;/radius &#10;add address=' +
                        radius +
                        ' secret=' + response.data.secret +
                        ' service=ppp,hotspot timeout=3000ms comment="added by radiusqu"&#10;/radius incoming&#10;set accept=no'
                    ),
                    // $('#copysstp').html(
                    //     '/snmp community&#10;set [ find default=yes ] disabled=yes&#10;add addresses=149.28.146.56/32 name=starbill write-access=yes read-access=yes&#10;/snmp&#10;set enabled=yes'
                    // ),
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

    function copyl2tpros7() {
        var copyTextros7 = document.getElementById("copyl2tpros7");
        copyTextros7.select();
        copyTextros7.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyTextros7.value);

        var tooltip = document.getElementById("l2tpros7");
        tooltip.innerHTML = "Copied";
    }

    function outl2tp() {
        var tooltip = document.getElementById("l2tp");
        tooltip.innerHTML = "Copy";
    }

    function outl2tpros7() {
        var tooltip = document.getElementById("l2tpros7");
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

    const ip_router = new Choices(
        '#ip_router', {
            allowHTML: true,
            removeItemButton: true,
            placeholderValue: '- Pilih IP Router  -',
        },
    );
    $('#create').on('hide.bs.modal', function() {
        ip_router.removeActiveItems();
    });
</script>
@endpush
