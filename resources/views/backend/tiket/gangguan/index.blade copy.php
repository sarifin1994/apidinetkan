@extends('backend.layouts.app')
@section('main')
@section('title', 'Tiket Gangguan')
<!-- Content -->
<div class="container-lg">
    <!-- Page header -->
    <div class="row align-items-center mb-7">
        <div class="col-auto">
            <!-- Avatar -->
            <div class="avatar avatar-xl rounded text-primary">
                <span class="fs-2 material-symbols-outlined">
                    map
                </span>
            </div>
        </div>
        <div class="col">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a class="text-body-secondary" href="#">Tiket</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Gangguan</li>
                </ol>
            </nav>

            <!-- Heading -->
            <h1 class="fs-4 mb-0">Tiket Gangguan</h1>
        </div>
        <div class="col-12 col-sm-auto mt-4 mt-sm-0">
            <!-- Action -->
            @include('backend.tiket.gangguan.modal.create')
            @include('backend.tiket.gangguan.modal.setting')

            <button class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#create"> <span
                    class="material-symbols-outlined me-1">add</span> Tambah </button>
            <button class="btn btn-secondary " data-bs-toggle="modal" data-bs-target="#settings"> <span
                    class="material-symbols-outlined me-1">settings</span> Setting </button>

        </div>
    </div>

    <!-- Page content -->
    <div class="row">
        <div class="card-body table-responsive">
            <table id="myTable" class="table table-responsive table-hover display nowrap" width="100%">
                <thead>
                    <tr>
                        <th style="text-align:left!important">No</th>
                        <th>No Tiket</th>
                        <th>Waktu Dibuat</th>
                        <th>Pelanggan</th>
                        <th>Jenis Gangguan</th>
                        <th>Prioritas</th>
                        <th>Status</th>
                        <th>Waktu Close</th>
                        <th>Penyelesaian</th>
                        <th>Teknisi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="container">
            <div class="card mb-6">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="fs-6 mb-0">Grafik Jumlah Tiket Gangguan</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="chartTotalTiket"></canvas>
                    </div>
                </div>
            </div>
            <div class="card mb-6">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="fs-6 mb-0">Grafik Tiket Closed by Teknisi</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="chartClosedTiketByTech"></canvas>
                    </div>
                </div>
            </div>
            <div class="card mb-6">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="fs-6 mb-0">Grafik Waktu Penyelesaian by Teknisi (Jam)</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="chartResolutionTime"></canvas>
                    </div>
                </div>
            </div>
            <div class="card mb-6">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="fs-6 mb-0">Grafik Tiket Berdasarkan Jenis Gangguan</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="chartJenisGangguan"></canvas>
                    </div>
                </div>
            </div>
            <div class="card mb-6">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="fs-6 mb-0">Grafik Tiket Berdasarkan Penyelesaian</h3>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="chartPenyelesaian"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js" crossorigin="anonymous"></script>
<script>
    // Set new default font family and font color to mimic Bootstrap's default styling
    (Chart.defaults.global.defaultFontFamily = "Inter"),
    '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = "#858796";

    // Fungsi sederhana untuk menghasilkan warna berdasarkan index
    function dynamicColor(index, opacity = 0.5) {
        const r = (index * 50) % 255;
        const g = (index * 80) % 255;
        const b = (index * 110) % 255;
        return `rgba(${r}, ${g}, ${b}, ${opacity})`;
    }

    // Data dari controller
    const monthLabels = @json($monthLabels);

    // --- Grafik 1: Total Tiket Perbulan (Line Chart) ---
    const totalTicketsMonthly = @json($totalTicketsMonthly);
    const dataTotalTiket = monthLabels.map((label, i) => {
        // Gunakan index pada monthKeys yang sesuai dengan urutan bulan
        // Karena data total tiket disimpan dengan key "YYYY-MM", kita perlu mapping berdasarkan index
        // Misal, jika bulan ke-i adalah "2025-0{i+1}" sesuai key, gunakan totalTicketsMonthly[...]
        // Namun, jika data tidak ada, gunakan 0.
        const key = (new Date().getFullYear()) + '-' + String(i + 1).padStart(2, '0');
        return totalTicketsMonthly[key] || 0;
    });

    new Chart(document.getElementById('chartTotalTiket').getContext('2d'), {
        type: 'line',
        data: {
            labels: monthLabels,
            datasets: [{
                label: 'Total Tiket',
                data: dataTotalTiket,
                borderColor: dynamicColor(1, 1),
                backgroundColor: dynamicColor(1, 0.2),
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                },
                x: {
                    grid: {
                        display: true
                    }
                }
            }
        }
    });

    // --- Grafik 2: Total Tiket Closed Per Teknisi (Grouped Bar Chart) ---
    const allTechnicians = @json($allTechnicians);
    const closedTicketsByTech = @json($closedTicketsByTech);
    let datasetsClosed = [];
    allTechnicians.forEach(function(tech, index) {
        datasetsClosed.push({
            label: tech,
            data: monthLabels.map((label, i) => {
                const key = (new Date().getFullYear()) + '-' + String(i + 1).padStart(2, '0');
                return closedTicketsByTech[tech][monthLabels.indexOf(label)] || 0;
            }),
            backgroundColor: dynamicColor(index, 0.5)
        });
    });
    new Chart(document.getElementById('chartClosedTiketByTech').getContext('2d'), {
        type: 'bar',
        data: {
            labels: monthLabels,
            datasets: datasetsClosed
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    stacked: false
                },
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // --- Grafik 3: Waktu Penyelesaian Tiket Per Teknisi (Jam) (Line Chart) ---
    const avgResolutionTimeByTech = @json($avgResolutionTimeByTech);
    let datasetsResolution = [];
    allTechnicians.forEach(function(tech, index) {
        datasetsResolution.push({
            label: tech,
            data: monthLabels.map((label, i) => {
                const key = (new Date().getFullYear()) + '-' + String(i + 1).padStart(2, '0');
                return avgResolutionTimeByTech[tech][monthLabels.indexOf(label)] || 0;
            }),
            borderColor: dynamicColor(index, 1),
            backgroundColor: dynamicColor(index, 0.2),
            fill: false,
            tension: 0.3
        });
    });
    new Chart(document.getElementById('chartResolutionTime').getContext('2d'), {
        type: 'line',
        data: {
            labels: monthLabels,
            datasets: datasetsResolution
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // --- Grafik 4: Jenis Gangguan Perbulan (Line Chart) ---
    const allJenisGangguan = @json($allJenisGangguan);
    const gangguanByJenisData = @json($gangguanByJenisData);
    let datasetsJenisGangguan = [];
    allJenisGangguan.forEach(function(jenis, index) {
        datasetsJenisGangguan.push({
            label: jenis,
            data: monthLabels.map((label, i) => {
                const key = (new Date().getFullYear()) + '-' + String(i + 1).padStart(2, '0');
                return gangguanByJenisData[jenis][monthLabels.indexOf(label)] || 0;
            }),
            borderColor: dynamicColor(index, 1),
            backgroundColor: dynamicColor(index, 0.2),
            fill: false,
            tension: 0.3
        });
    });
    new Chart(document.getElementById('chartJenisGangguan').getContext('2d'), {
        type: 'bar',
        data: {
            labels: monthLabels,
            datasets: datasetsJenisGangguan
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // --- Grafik 5: Penyelesaian Gangguan Perbulan (Line Chart) ---
    const allPenyelesaian = @json($allPenyelesaian);
    const penyelesaianData = @json($penyelesaianData);
    let datasetsPenyelesaian = [];
    allPenyelesaian.forEach(function(peny, index) {
        datasetsPenyelesaian.push({
            label: peny,
            data: monthLabels.map((label, i) => {
                const key = (new Date().getFullYear()) + '-' + String(i + 1).padStart(2, '0');
                return penyelesaianData[peny][monthLabels.indexOf(label)] || 0;
            }),
            borderColor: dynamicColor(index + 5, 1),
            backgroundColor: dynamicColor(index + 5, 0.2),
            fill: false,
            tension: 0.3
        });
    });
    new Chart(document.getElementById('chartPenyelesaian').getContext('2d'), {
        type: 'bar',
        data: {
            labels: monthLabels,
            datasets: datasetsPenyelesaian
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<script type="text/javascript">
    let table = $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        order: [
            [2, 'desc']
        ],
        ajax: '{{ url()->current() }}',
        columns: [{
                data: null,
                'sortable': false,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: 'nomor_tiket',
                name: 'nomor_tiket',
                render: function(data, type, row) {
                    return '<span class="badge bg-danger-subtle text-danger">' + data + '</span>';
                }
            },

            {
                data: 'created_at',
                name: 'created_at',
                render: function(data, type, row) {
                    if (data) {
                        return moment(data).format('DD/MM/YYYY HH:mm');
                    }
                    return '';
                }
            },

            {
                data: 'nama_pelanggan',
                name: 'nama_pelanggan'
            },
            {
                data: 'jenis_gangguan',
                name: 'jenis_gangguan'
            },
            {
                data: 'prioritas',
                name: 'prioritas',
                render: function(data, type, row) {
                    var kelas = '';
                    if (data.toLowerCase() === 'rendah') {
                        kelas = 'badge bg-success-subtle text-success';
                    } else if (data.toLowerCase() === 'normal') {
                        kelas = 'badge bg-primary-subtle text-primary';
                    } else if (data.toLowerCase() === 'tinggi') {
                        kelas = 'badge bg-danger-subtle text-danger';
                    }
                    return '<span class="' + kelas + '">' + data + '</span>';
                }
            },
            {
                data: 'status',
                name: 'status',
                render: function(data, type, row) {
                    if (data.toLowerCase() === 'open') {
                        return '<span class="badge bg-danger-subtle text-danger">open</span>';
                    } else if (data.toLowerCase() === 'closed') {
                        return '<span class="badge bg-success-subtle text-success">closed</span>';
                    }
                    return data;
                }
            },

            {
                data: 'closed_at',
                name: 'closed_at',
                render: function(data, type, row) {
                    if (data) {
                        return moment(data).format('DD/MM/YYYY HH:mm');
                    }
                    return '';
                }
            },
            {
                data: 'penyelesaian',
                name: 'penyelesaian'
            },
            {
                data: 'teknisi',
                name: 'teknisi',
            },
            {
                data: 'action',
                name: 'action'
            }
        ]
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
                    url: `/tiket/gangguan/${id}`,
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
    });

    $('#myTable').on('click', '#close', function() {

        var id = $(this).data('id');
        var id_pelanggan = $(this).data('id_pelanggan');
        var nomor_tiket = $(this).data('nomor_tiket');

        $.ajax({
            url: `/pppoe/user/${id_pelanggan}`,
            type: "GET",
            cache: false,
            data: {
                id_pelanggan: id_pelanggan,
                '_token': '{{ csrf_token() }}'
            },
            success: function(data) {
                var full_name = data.data.full_name;
                let username = data.data.username;
                if (username) {
                    $.ajax({
                        url: `/tiket/gangguan/getSession/${username}`,
                        type: 'GET',
                        data: {
                            username: username
                        },
                        success: function(response) {
                            var status_internet;
                            var ip;

                            if (response.session !== null && response.status === 1 &&
                                response.ip !== null && response.status === 1) {
                                status_internet = 'ONLINE';
                                ip = response.ip;
                            } else if (response.session !== null && response.status ===
                                1 &&
                                response.ip !== null && response.ppp.status === 2) {
                                status_internet = 'ISOLIR';
                                ip = response.ip;
                            } else if (response.session !== null && response.status ===
                                2 && response.ip !== null && response.ppp.status === 2
                            ) {
                                status_internet = 'SUSPEND';
                                ip = response.ip;
                            } else if (response.session !== null && response.status ===
                                2 && response.ip !== null) {
                                status_internet = 'OFFLINE';
                                ip = response.ip;
                            } else {
                                status_internet = 'UNKNOWN';
                            }

                            Swal.fire({
                                title: "Close " + nomor_tiket,
                                icon: 'warning',
                                input: "select",
                                inputOptions: {
                                    'Resplice Kabel': 'Resplice Kabel',
                                    'Set Ulang ONT': 'Set Ulang ONT',
                                    'Ganti ONT': 'Ganti ONT',
                                    'Ganti Adaptor': 'Ganti Adaptor',
                                    'Ganti Kabel': 'Ganti Kabel',
                                    'Ganti Patchord': 'Ganti Patchord',
                                    'Ganti Splitter': 'Ganti Splitter',
                                    'Ganti Adapter': 'Ganti Adapter',
                                    'Ganti HTB': 'Ganti HTB',
                                    'Lainnya': 'Lainnya',
                                },
                                inputPlaceholder: '- Pilih Langkah Penyelesaian -',

                                text: full_name + " - Status Internet : " +
                                    status_internet,
                                showCancelButton: true,
                                reverseButtons: true,
                                confirmButtonText: "Ya, Close",
                                cancelButtonText: "Batal",
                                customClass: {
                                    input: 'form-select w-auto bg-none width-auto p-3 mx-10',
                                },
                                inputValidator: function(value) {
                                    return new Promise(function(resolve,
                                        reject) {
                                        if (value !== '') {
                                            resolve();
                                        } else {
                                            resolve(
                                                'Silakan pilih langkah penyelesaian'
                                            );
                                        }
                                    });
                                }
                            }).then(function(result) {
                                if (result.isConfirmed) {
                                    // Menampilkan SweetAlert Loading
                                    var data = {
                                        'id': id,
                                        'pelanggan_id': id_pelanggan,
                                        'status_internet': status_internet,
                                        'ip': ip,
                                        'penyelesaian': result.value,
                                    };
                                    // console.log(data);

                                    const swalLoading = Swal.fire({
                                        title: 'Proses...',
                                        text: 'Sedang memproses, harap tunggu...',
                                        icon: 'info',
                                        allowOutsideClick: false,
                                        showConfirmButton: false,
                                        willOpen: () => {
                                            Swal
                                                .showLoading(); // Menampilkan animasi loading
                                        }
                                    });

                                    // AJAX untuk menutup tiket
                                    $.ajaxSetup({
                                        headers: {
                                            'X-CSRF-TOKEN': $(
                                                'meta[name="csrf-token"]'
                                            ).attr('content')
                                        }
                                    });

                                    $.ajax({
                                        url: `/tiket/gangguan/close/${id}`,
                                        type: "PUT",
                                        cache: false,
                                        data: data,
                                        dataType: "json",

                                        success: function(data) {
                                            swalLoading
                                                .close(); // Menutup SweetAlert Loading
                                            if (data.success ===
                                                false) {
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'Gagal',
                                                    text: data
                                                        .message,
                                                    showConfirmButton: true
                                                });
                                            } else {
                                                Swal.fire({
                                                    icon: 'success',
                                                    title: 'Success',
                                                    text: `${data.message}`,
                                                    showConfirmButton: false,
                                                    timer: 1500
                                                });
                                                setTimeout(
                                                    function() {
                                                        table
                                                            .ajax
                                                            .reload(); // Reload data tabel
                                                    }, 1500);
                                            }
                                        },

                                        error: function(err) {
                                            swalLoading
                                                .close(); // Menutup SweetAlert Loading
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Error',
                                                text: 'Terjadi kesalahan saat menutup tiket.',
                                                showConfirmButton: true
                                            });
                                        }
                                    });
                                }
                            });
                        }
                    });
                }
            }
        });
    });


    // Aksi Create
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
            'pelanggan_id': $('#id_pelanggan').val(),
            'jenis_gangguan': $('#jenis_gangguan').val(),
            'teknisi': $('#teknisi').val(),
            'prioritas': $('#prioritas').val(),
            'note': $('#note').val(),
            'status_internet': $('#fill_internet').text(),
            'ip': $('#fill_ip').text(),
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
            url: `/tiket/gangguan`,
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
                        $('#create').modal('hide');
                        table.ajax.reload();
                        $('input, textarea').val('');
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

    $("#group_select").one("click", function() {
        // Tampilkan loading di dalam select
        $("#group_select").html('<option>Loading...</option>');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: '/tiket/gangguan/getGroup', // Sesuaikan endpoint API Anda
            method: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    // Kosongkan elemen select dan tambahkan opsi default
                    $("#group_select").empty();
                    $("#group_select").append('<option value="">-- Pilih Group --</option>');

                    // Iterasi setiap grup dan tambahkan sebagai option
                    $.each(response.data, function(index, group) {
                        $("#group_select").append(
                            '<option value="' + group.id + '">' + group.subject +
                            '</option>'
                        );
                    });

                    // (Opsional) Tampilkan data grup di div#group_data
                    let html = '<ul>';
                    $.each(response.data, function(index, group) {
                        html += '<li>ID: ' + group.id + ' | Group: ' + group.subject +
                            '</li>';
                    });
                    html += '</ul>';
                    $("#group_data").html(html);
                } else {
                    console.error("Gagal memuat data grup:", response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching groups:", error);
            }
        });
    });

    // Aksi Save
    $('#save').click(function(e) {
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
            'group_id': $('#group_select').val(),
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
            url: `/tiket/gangguan/saveGroup`,
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
                        $('#settings').modal('hide');
                        table.ajax.reload();
                        $('input, textarea').val('');
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


    const id_pelanggan = new Choices(
        '#id_pelanggan', {
            allowHTML: true,
            removeItemButton: true,
            placeholderValue: '- Pilih Nama Pelanggan -',
        },
    )

    $("#id_pelanggan").on("change", function() {
        var id_pelanggan = $(this).val();
        $.ajax({
            url: `/pppoe/user/${id_pelanggan}`,
            type: "GET",
            cache: false,
            data: {
                id_pelanggan: id_pelanggan,
                '_token': '{{ csrf_token() }}'
            },
            success: function(data) {
                let username = data.data.username;
                if (username) {
                    $.ajax({
                        url: `/tiket/gangguan/getSession/${username}`,
                        type: 'GET',
                        data: {
                            username: username
                        },
                        success: function(response) {
                            if (response.session !== null && response.status === 1 &&
                                response.ip !== null && response.status === 1) {
                                $('#fill_internet').html(
                                    '<span class="badge bg-success-subtle text-success">ONLINE</span>'
                                );
                                $('#fill_ip').html('<span>' + response.ip + '</span>');
                            } else if (response.session !== null && response.status ===
                                1 &&
                                response.ip !== null && response.ppp.status === 2) {
                                $('#fill_internet').html(
                                    '<span class="badge bg-danger-subtle text-danger">ISOLIR</span>'
                                );
                                $('#fill_ip').html('<span>' + response.ip + '</span>');
                            } else if (response.session !== null && response.status ===
                                2 && response.ip !== null && response.ppp.status === 2
                            ) {
                                $('#fill_internet').html(
                                    '<span class="badge bg-danger-subtle text-danger">SUSPEND</span>'
                                );
                                $('#fill_ip').html('-');
                            } else if (response.session !== null && response.status ===
                                2 && response.ip !== null) {
                                $('#fill_internet').html(
                                    '<span class="badge bg-danger-subtle text-danger">OFFLINE</span>'
                                );
                                $('#fill_ip').html('-');
                            } else {
                                $('#fill_internet').html(
                                    '<span class="badge bg-danger-subtle text-danger">UNKNOWN</span>'
                                );
                                $('#fill_ip').html('-');
                            }
                        }
                    });

                }
            }
        });
    });

    $("input[name='tipe_gangguan']").on("change", function () {
        if ($(this).val() === "individu") {
            $("#individu").show();
            $("#massal").hide();
        } else {
            $("#individu").hide();
            $("#massal").show();
        }
    });
    var multipleCancelButton = new Choices(
        '#kode_area', {
            allowHTML: true,
            placeholderValue: '- Pilih POP -',
            removeItemButton: true,
        }
    );
</script>
@endpush
