@extends('backend.layouts.app')
@section('main')
@section('title', 'Tiket Gangguan')
<div class="container-lg">
    <div class="row align-items-center mb-7">
        <div class="col-auto">
            <div class="avatar avatar-xl rounded text-primary">
                <span class="fs-2 material-symbols-outlined">map</span>
            </div>
        </div>
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a class="text-body-secondary" href="#">Tiket</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Gangguan</li>
                </ol>
            </nav>
            <h1 class="fs-4 mb-0">Tiket Gangguan</h1>
        </div>
        <div class="col-12 col-sm-auto mt-4 mt-sm-0">
            @include('backend.tiket.gangguan.modal.create')
            @include('backend.tiket.gangguan.modal.setting')
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#create">
                <span class="material-symbols-outlined me-1">add</span> Tambah
            </button>
            <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#settings">
                <span class="material-symbols-outlined me-1">settings</span> Setting
            </button>
        </div>
    </div>

    <div class="row">
        <div class="card-body table-responsive">
            <table id="myTable" class="table table-hover display nowrap" width="100%">
                <thead>
                    <tr>
                        <th>No</th>
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
                <tbody></tbody>
            </table>
        </div>
    </div>

    <hr>

    <!-- Spinner Loading Grafik -->
    <div id="chart-loader" class="text-center my-4">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="mt-2">Memuat grafik tiket gangguan...</p>
    </div>

    <div class="row">
        <div class="container">
            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="fs-6 mb-0">Grafik Jumlah Tiket Gangguan</h3>
                </div>
                <div class="card-body chart-area">
                    <canvas id="chartTotalTiket"></canvas>
                </div>
            </div>
            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="fs-6 mb-0">Grafik Tiket Closed by Teknisi</h3>
                </div>
                <div class="card-body chart-area">
                    <canvas id="chartClosedTiketByTech"></canvas>
                </div>
            </div>
            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="fs-6 mb-0">Grafik Waktu Penyelesaian by Teknisi (Jam)</h3>
                </div>
                <div class="card-body chart-area">
                    <canvas id="chartResolutionTime"></canvas>
                </div>
            </div>
            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="fs-6 mb-0">Grafik Tiket Berdasarkan Jenis Gangguan</h3>
                </div>
                <div class="card-body chart-area">
                    <canvas id="chartJenisGangguan"></canvas>
                </div>
            </div>
            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="fs-6 mb-0">Grafik Tiket Berdasarkan Penyelesaian</h3>
                </div>
                <div class="card-body chart-area">
                    <canvas id="chartPenyelesaian"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
<script>
let grafikLoaded = false;

function dynamicColor(index, opacity = 0.5) {
    const r = (index * 50) % 255;
    const g = (index * 80) % 255;
    const b = (index * 110) % 255;
    return `rgba(${r}, ${g}, ${b}, ${opacity})`;
}

function loadGrafikTiket() {
    if (grafikLoaded) return;
    $('#chart-loader').show();

    fetch("{{ route('grafik.tiket') }}")
        .then(res => res.json())
        .then(data => {
            const labels = data.monthLabels;

            new Chart(chartTotalTiket.getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Tiket',
                        data: data.totalTicketsMonthly,
                        borderColor: dynamicColor(0, 1),
                        backgroundColor: dynamicColor(0, 0.2),
                        fill: true
                    }]
                }
            });

            const closed = data.allTechnicians.map((tech, i) => ({
                label: tech,
                data: data.closedTicketsByTech[tech],
                backgroundColor: dynamicColor(i)
            }));
            new Chart(chartClosedTiketByTech.getContext('2d'), {
                type: 'bar',
                data: { labels: labels, datasets: closed }
            });

            const avgTime = data.allTechnicians.map((tech, i) => ({
                label: tech,
                data: data.avgResolutionTimeByTech[tech],
                borderColor: dynamicColor(i, 1),
                backgroundColor: dynamicColor(i, 0.2),
                fill: false
            }));
            new Chart(chartResolutionTime.getContext('2d'), {
                type: 'line',
                data: { labels: labels, datasets: avgTime }
            });

            const jenis = data.allJenisGangguan.map((jenis, i) => ({
                label: jenis,
                data: data.gangguanByJenisData[jenis],
                borderColor: dynamicColor(i, 1),
                backgroundColor: dynamicColor(i, 0.2),
                fill: false
            }));
            new Chart(chartJenisGangguan.getContext('2d'), {
                type: 'bar',
                data: { labels: labels, datasets: jenis }
            });

            const peny = data.allPenyelesaian.map((peny, i) => ({
                label: peny,
                data: data.penyelesaianData[peny],
                borderColor: dynamicColor(i, 1),
                backgroundColor: dynamicColor(i, 0.2),
                fill: false
            }));
            new Chart(chartPenyelesaian.getContext('2d'), {
                type: 'bar',
                data: { labels: labels, datasets: peny }
            });

            $('#chart-loader').hide();
            grafikLoaded = true;
        })
        .catch(err => {
            console.error('Gagal ambil data grafik:', err);
            $('#chart-loader').html('<p class="text-danger">Gagal memuat grafik.</p>');
        });
}

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

table.on('draw', function () {
    loadGrafikTiket();
});

const id_pelanggan = new Choices(
        '#id_pelanggan', {
            allowHTML: true,
            removeItemButton: true,
            placeholderValue: '- Pilih Nama Pelanggan -',
        },
    )

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
                        url: `/tiket/gangguan/getSession/username?username=${encodeURIComponent(username)}`,
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
                        url: `/tiket/gangguan/getSession/username?username=${encodeURIComponent(username)}`,
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

</script>
@endpush
