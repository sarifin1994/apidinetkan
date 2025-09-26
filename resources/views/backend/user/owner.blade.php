@extends('backend.layouts.app')
@section('main')
@section('title', 'Users')
<!-- Content -->
<div class="container-lg">
    <!-- Page header -->
    <div class="row align-items-center mb-7">
        <div class="col-auto">
            <!-- Avatar -->
            <div class="avatar avatar-xl rounded text-primary">
                <i class="fs-2" data-duoicon="user"></i>
            </div>
        </div>
        <div class="col">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    {{-- <li class="breadcrumb-item"><a class="text-body-secondary" href="#">User</a></li> --}}
                    <li class="breadcrumb-item active" aria-current="page">Users</li>
                </ol>
            </nav>

            <!-- Heading -->
            <h1 class="fs-4 mb-0">Users</h1>
        </div>
        <div class="col-12 col-sm-auto mt-4 mt-sm-0">
            <!-- Action -->
            @include('backend.user.modal.create')
            @include('backend.user.modal.edit')
            <button class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#create"> <span
                    class="material-symbols-outlined me-1">add</span> Create </button>
        </div>
    </div>

    <div class="row mb-8">

        <div class="col-12 col-md-6 col-xxl-3 mb-4 mb-md-0">
            <div class="card bg-body-tertiary border-transparent">
                <a href="/user" style="text-decoration: none; color: inherit;">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <!-- Heading -->
                                <h4 class="fs-sm fw-normal text-body-secondary mb-1">
                                    Est Omset Bulan Ini<br>
                                </h4>
                                <!-- Nilai dinamis dengan spinner -->
                                <div class="fs-lg fw-semibold" id="income" data-value="Rp {{ number_format($income, 0, ',', '.') }}">
                                    <span class="material-symbols-outlined spinner">progress_activity</span>
                                </div>
                                <div class="fs-sm fw-semibold text-danger" id="income_unpaid" data-value="Belum Bayar Rp {{ number_format($income_unpaid, 0, ',', '.') }}">
                                    <span class="material-symbols-outlined spinner">progress_activity</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <!-- Avatar -->
                                <div class="text-primary">
                                    <i class="fs-4" data-duoicon="credit-card"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xxl-3 mb-4 mb-md-0">
            <div class="card bg-body-tertiary border-transparent">
                <a href="/user" style="text-decoration: none; color: inherit;">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <!-- Heading -->
                                <h4 class="fs-sm fw-normal text-body-secondary mb-1">
                                    Total User<br>
                                </h4>
                                <!-- Nilai dinamis dengan spinner -->
                                <div class="fs-lg fw-semibold" id="totaluser" data-value="{{ $totaluser }}">
                                    <span class="material-symbols-outlined spinner">progress_activity</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <!-- Avatar -->
                                <div class="text-primary">
                                    <i class="fs-4" data-duoicon="credit-card"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Card 3: Hotspot - User Online -->
        <div class="col-12 col-md-6 col-xxl-3 mb-4 mb-md-0">
            <div class="card bg-body-tertiary border-transparent">
                <a href="/user" style="text-decoration: none; color: inherit;">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <!-- Heading -->
                                <h4 class="fs-sm fw-normal text-body-secondary mb-1">
                                    User Active<br>
                                </h4>
                                <!-- Nilai dinamis dengan spinner -->
                                <div class="fs-lg fw-semibold" id="useractive" data-value="{{ $useractive }}">
                                    <span class="material-symbols-outlined spinner">progress_activity</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <!-- Avatar -->
                                <div class="text-primary">
                                    <i class="fs-4" data-duoicon="credit-card"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Card 4: PPPoE - User Online -->
        <div class="col-12 col-md-6 col-xxl-3 mb-4 mb-md-0">
            <div class="card bg-body-tertiary border-transparent">
                <a href="/user" style="text-decoration: none; color: inherit;">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <!-- Heading -->
                                <h4 class="fs-sm fw-normal text-body-secondary mb-1">
                                    User Expired<br>
                                </h4>
                                <!-- Nilai dinamis dengan spinner -->
                                <div class="fs-lg fw-semibold" id="userexpired" data-value="{{ $userexpired }}">
                                    <span class="material-symbols-outlined spinner">progress_activity</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <!-- Avatar -->
                                <div class="text-primary">
                                    <i class="fs-4" data-duoicon="clock"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        
    </div>
    
    <!-- Page content -->
    <div class="row">
    
            <div class="card-body table-responsive">
                <table id="myTable" class="table table-responsive table-hover display nowrap" width="100%">
                    <thead>
                        <tr>
                            <!-- <th>#</th> -->
                            <td>ID</td>
                            <th>Username</th>
                            <th style="text-align:left">WhatsApp</th>
                            <th>Domain</th>
                            <th>Lisensi</th>
                            <th>Harga</th>
                            <th>Diskon</th>
                            <th>Order / Upgrade</th>
                            <th>Next Due</th>
                            <th>Status</th>
                            <th>Hotspot Online</th>
                            <th>PPPoE Online</th>
                            <th>NAS</th>
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
 document.addEventListener("DOMContentLoaded", function() {
        const delay = 1000; // Delay dalam milidetik (1.5 detik)
        // Pilih semua elemen yang memiliki atribut data-value
        const elements = document.querySelectorAll('[data-value]');
        elements.forEach(function(el) {
            setTimeout(function() {
                el.innerHTML = el.getAttribute('data-value');
            }, delay);
        });
    });
    let table = $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: '{{ url()->current() }}',
        columns: [
            // {
            //     data: null,
            //     'sortable': false,
            //     className: "text-center",
            //     render: function(data, type, row, meta) {
            //         return meta.row + meta.settings._iDisplayStart + 1;
            //     }
            // },
            {
                data: 'id',
                name: 'id'
            },           
            {
                data: 'username',
                name: 'username'
            },
            {
                data: 'whatsapp',
                name: 'whatsapp',
                createdCell: function(td) {
                    $(td).css('text-align', 'left'); // Memaksa teks ke kiri
                }
            },
            {
                data: 'domain',
                name: 'domain'
            },
            {
                data: 'license.name',
                name: 'license.name',
            },
            {
                data: 'license.price',
                name: 'license.price',
                render: $.fn.dataTable.render.number('.', ',', 0, ''),
            },
            {
                data: 'discount',
                name: 'discount',
                render: $.fn.dataTable.render.number('.', ',', 0, ''),
            },
            {
                data: 'order_name',
                name: 'order_name',
                searchable:false,
                sortable:false,
            },
            {
                data: 'next_due',
                name: 'next_due',
            },
            {
                data: 'status',
                name: 'status',
                render: function(data) {
                    if (data === 1) {
                        return '<span class="badge bg-success">Aktif</span>'
                    } else if (data === 3) {
                        return '<span class="badge bg-danger">Expired</span>'
                    } else if (data === 0) {
                        return '<span class="badge bg-danger">Nonaktif</span>'
                    } else if (data === 2) {
                        return '<span class="badge bg-warning">Pending</span>'
                    }

                },
            },
            {
                data: 'total_session_hotspot',
                name: 'total_session_hotspot',
                searchable:false,
                sortable:false,
                render: function(data, type, row, meta) {
                    var id = 'total_session_hotspot-' + meta.row;
                    // Tampilkan spinner sampai konten terupdate
                    return '<span id="' + id +
                        '"><span class="material-symbols-outlined spinner">progress_activity</span></span>';
                }

            },
            {
                data: 'total_session_pppoe',
                name: 'total_session_pppoe',
                searchable:false,
                sortable:false,
                render: function(data, type, row, meta) {
                    var id = 'total_session_pppoe-' + meta.row;
                    // Tampilkan spinner sampai konten terupdate
                    return '<span id="' + id +
                        '"><span class="material-symbols-outlined spinner">progress_activity</span></span>';
                }

            },

            {
                data: 'nas_count',
                name: 'nas_count',
            },
           
            {
                data: 'action',
                name: 'action',
                // visible:false
                
            }
        ],
        drawCallback: function(settings) {
            updateTotalSessionHotspot();
            updateTotalSessionPppoe();
        }
    });

     // Fungsi tambahan yang dipanggil setelah DataTable diload
     function updateTotalSessionHotspot() {
        table.rows().every(function(index, element) {
            var rowData = this.data();
            $.ajax({
                url: '/user/getTotalSessionHotspot', // Endpoint di Laravel
                type: "POST",
                data: {
                    shortname: rowData.username, // Mengirimkan id NAS
                    _token: "{{ csrf_token() }}" // Pastikan token CSRF tersedia
                },
                success: function(response) {
                    //   console.log(response);
                    // Asumsikan response mengembalikan { total_session: <jumlah> }
                    var updatedCount = response.total_session;
                    var content = '<span class="material-symbols-outlined">monitoring</span> ' +
                        updatedCount + ' online';
                    var el = document.getElementById('total_session_hotspot-' + index);
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

    function updateTotalSessionPppoe() {
        table.rows().every(function(index, element) {
            var rowData = this.data();
            $.ajax({
                url: '/user/getTotalSessionPppoe', // Endpoint di Laravel
                type: "POST",
                data: {
                    shortname: rowData.username, // Mengirimkan id NAS
                    _token: "{{ csrf_token() }}" // Pastikan token CSRF tersedia
                },
                success: function(response) {
                    //   console.log(response);
                    // Asumsikan response mengembalikan { total_session: <jumlah> }
                    var updatedCount = response.total_session;
                    var content = '<span class="material-symbols-outlined">monitoring</span> ' +
                        updatedCount + ' online';
                    var el = document.getElementById('total_session_pppoe-' + index);
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
            'role': $('#role').val(),
            'username': $('#username').val(),
            'password': $('#password').val(),
            'email': $('#email').val(),
            'whatsapp': $('#whatsapp').val(),
            'reseller_id': $('#reseller').val(),
        };

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Simpan referensi tombol dan teks aslinya
        var btn = $(this);
        var originalText = btn.html();

        // Ubah tampilan tombol: nonaktifkan dan tampilkan teks dengan spinner di sebelah kanan
        btn.prop('disabled', true).html(
            'Memproses... <span class="material-symbols-outlined spinner">progress_activity</span>');

        // Proses AJAX
        $.ajax({
            url: `/user`,
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
                        $('input, textarea').val('');
                        $('#create').modal('hide');
                    }, 1500);
                } else {
                    $.each(data.error, function(key, value) {
    var el = $(document).find('[name="' + key + '"]');
    // Tambahkan class is-invalid untuk membuat border merah (jika menggunakan Bootstrap)
    el.addClass('is-invalid');
    el.after($('<div class="form-text text-danger">' + value[0] + '</div>'));
});

                }
            },
            error: function(err) {
                btn.prop('disabled', false).html(originalText);
                $("#message").html("Some Error Occurred!");
            }
        });
    });


    $('#myTable').on('click', '#edit', function() {

        let user_id = $(this).data('id');

        //fetch detail post with ajax
        $.ajax({
            url: `/user/${user_id}`,
            type: "GET",
            cache: false,
            success: function(response) {

                //fill data to form
                $('#user_id').val(response.data.id),
                    $('#name_edit').val(response.data.name),
                    $('#email_edit').val(response.data.email),
                    $('#whatsapp_edit').val(response.data.whatsapp),
                    $('#username_edit').val(response.data.username),
                    $('#license_edit').val(response.data.license_id),
                    $('#next_due_edit').val(response.data.next_due),
                    $('#status_edit').val(response.data.status),
                    $('#discount_edit').val(response.data.discount),
                    $('#role_edit').val(response.data.role);

                //open modal
                $('#edit').modal('show');
            }
        });
    });

    $('#update').click(function(e) {
        e.preventDefault();

        // Hapus pesan error jika ada
        var error_ele = document.getElementsByClassName('form-text text-danger');
        if (error_ele.length > 0) {
            for (var i = error_ele.length - 1; i >= 0; i--) {
                error_ele[i].remove();
            }
        }

        let user_id = $('#user_id').val();

        // Kumpulkan data dari form
        var data = {
            'name': $('#name_edit').val(),
            'email': $('#email_edit').val(),
            'username': $('#username_edit').val(),
            'whatsapp': $('#whatsapp_edit').val(),
            'password': $('#password_edit').val(),
            'license_id': $('#license_edit').val(),
            'next_due': $('#next_due_edit').val(),
            'status' : $('#status_edit').val(),
            'discount' : $('#discount_edit').val(),
            'role': $('#role_edit').val(),
        };

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Simpan referensi tombol dan teks aslinya
        var btn = $(this);
        var originalText = btn.html();

        // Ubah tampilan tombol: nonaktifkan dan tampilkan teks dengan spinner di sebelah kanan
        btn.prop('disabled', true).html(
            'Memproses... <span class="material-symbols-outlined spinner">progress_activity</span>');

        $.ajax({
            url: `/user/${user_id}`,
            type: "PUT",
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
                        $('input, textarea').val('');
                        $('#edit').modal('hide');
                    }, 1500);
                } else {
                    $.each(data.error, function(key, value) {
    var el = $(document).find('[name="' + key + '"]');
    // Tambahkan class is-invalid untuk membuat border merah (jika menggunakan Bootstrap)
    el.addClass('is-invalid');
    el.after($('<div class="form-text text-danger">' + value[0] + '</div>'));
});

                }
            },
            error: function(err) {
                btn.prop('disabled', false).html(originalText);
                $("#message").html("Some Error Occurred!");
            }
        });
    });


    $('#myTable').on('click', '#disable', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: "Nonaktifkan User",
            icon: 'warning',
            text: 'Apakah Anda yakin ingin menonaktifkan user ini?',
            showCancelButton: !0,
            confirmButtonText: "Ya, Nonaktifkan",
            cancelButtonText: "Batal",
            reverseButtons: !0,
        }).then(function(result) {
            if (result.isConfirmed) {
                var data = {
                    'id': id
                };

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $(
                                'meta[name="csrf-token"]'
                            )
                            .attr('content')
                    }
                });

                // ajax proses
                $.ajax({
                    url: `/user/disable/${id}`,
                    type: "PUT",
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
                                table.ajax.reload()
                            });
                        } else {

                        }
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

    $('#myTable').on('click', '#enable', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: "Aktifkan Reseller",
            icon: 'warning',
            text: 'Apakah Anda yakin ingin mengaktifkan reseller ini?',
            showCancelButton: !0,
            confirmButtonText: "Ya, Aktifkan",
            cancelButtonText: "Batal",
            reverseButtons: !0,
        }).then(function(result) {
            if (result.isConfirmed) {
                var data = {
                    'id': id
                };

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $(
                                'meta[name="csrf-token"]'
                            )
                            .attr('content')
                    }
                });

                // ajax proses
                $.ajax({
                    url: `/user/enable/${id}`,
                    type: "PUT",
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
                                table.ajax.reload()
                            });
                        } else {

                        }
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
    $('#myTable').on('click', '#renew', function() {
        let id = $(this).data('id');
        let shortname = $(this).data('shortname');
        let next_due = $(this).data('next_due');
        $.ajax({
            url: `/user/${id}`,
            type: "GET",
            success: function(data) {
                Swal.fire({
                    title: "Renew Lisensi",
                    icon: 'warning',
                    text: "Perpanjang lisensi untuk user "+shortname+ "?",
                    showCancelButton: !0,
                    confirmButtonText: "Ya, Renew",
                    cancelButtonText: "Batal",
                    reverseButtons: !0,
                }).then(function(result) {
                    if (result.isConfirmed) {
                        var data = {
                            'shortname': shortname,
                            'next_due':next_due
                        }

                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $(
                                        'meta[name="csrf-token"]'
                                    )
                                    .attr('content')
                            }
                        });

                        // ajax proses
                        $.ajax({
                            url: `/user/renew/${id}`,
                            type: "PUT",
                            cache: false,
                            data: data,
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
            }
        });

    });

    $('#myTable').on('click', '#upgrade', function() {
        let id = $(this).data('id');
        let shortname = $(this).data('shortname');
        let next_due = $(this).data('next_due');
        let order = $(this).data('order');
        $.ajax({
            url: `/user/${id}`,
            type: "GET",
            success: function(data) {
                Swal.fire({
                    title: "Upgrade Lisensi",
                    icon: 'warning',
                    text: "Upgrade lisensi untuk user "+shortname+ "?",
                    showCancelButton: !0,
                    confirmButtonText: "Ya, Upgrade",
                    cancelButtonText: "Batal",
                    reverseButtons: !0,
                }).then(function(result) {
                    if (result.isConfirmed) {
                        var data = {
                            'shortname': shortname,
                            'next_due':next_due,
                            'order': order,
                        }

                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $(
                                        'meta[name="csrf-token"]'
                                    )
                                    .attr('content')
                            }
                        });

                        // ajax proses
                        $.ajax({
                            url: `/user/upgrade/${id}`,
                            type: "PUT",
                            cache: false,
                            data: data,
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
            }
        });

    });

    $('#myTable').on('click', '#delete', function() {
        let id = $(this).data('id');
        let shortname = $(this).data('shortname');
        $.ajax({
            url: `/user/${id}`,
            type: "GET",
            success: function(data) {
                Swal.fire({
                    title: "Hapus User",
                    icon: 'warning',
                    text: "Apakah anda yakin ingin menghapus user "+shortname+ "?",
                    showCancelButton: !0,
                    confirmButtonText: "Ya, Hapus",
                    cancelButtonText: "Batal",
                    reverseButtons: !0,
                }).then(function(result) {
                    if (result.isConfirmed) {
                        var data = {
                            'shortname': shortname,
                        }

                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': $(
                                        'meta[name="csrf-token"]'
                                    )
                                    .attr('content')
                            }
                        });

                        // ajax proses
                        $.ajax({
                            url: `/user/delete`,
                            type: "POST",
                            cache: false,
                            data: data,
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
            }
        });

    });
</script>
@endpush
