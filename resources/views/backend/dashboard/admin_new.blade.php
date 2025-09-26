@extends('backend.layouts.app_new')
@section('main')
@section('title', 'Dashboard')
<!-- Body main section starts -->
    <div class="container-fluid">
        <!-- Cards Row -->
        <div class="row g-3 row-cols-1 row-cols-sm-2 row-cols-lg-4">
            <div class="col">
                <a href="/keuangan/transaksi" class="text-decoration-none text-reset">
                    <div class="card text-center h-100">
                        <span class="bg-primary h-50 w-50 d-flex-center rounded-circle m-auto eshop-icon-box">
                            <i class="ph ph-currency-circle-dollar f-s-24"></i>
                        </span>
                        <div class="card-body eshop-cards">
                            <span class="ripple-effect"></span>
                            <h3 class="text-primary mb-0" id="incometoday"></h3>
                            <p class="mg-b-35 f-w-600 text-dark-800 txt-ellipsis-1">Income Hari Ini</p>
                            <span class="badge bg-light-primary">Keuangan</span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col">
                <a href="/invoice/unpaid" class="text-decoration-none text-reset">
                    <div class="card text-center h-100">
                        <span class="bg-danger h-50 w-50 d-flex-center rounded-circle m-auto eshop-icon-box">
                            <i class="ph ph-x-circle f-s-24"></i>
                        </span>
                        <div class="card-body eshop-cards">
                            <span class="ripple-effect"></span>
                            <h3 class="text-danger mb-0" id="totalunpaid"></h3>
                            <p class="mg-b-35 f-w-600 text-dark-800 txt-ellipsis-1">Total Unpaid</p>
                            <span class="badge bg-light-danger">Invoice</span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col">
                <a href="/hotspot_online" class="text-decoration-none text-reset">
                    <div class="card text-center h-100">
                        <span class="bg-secondary h-50 w-50 d-flex-center rounded-circle m-auto eshop-icon-box">
                            <i class="ph ph-wifi-high f-s-24"></i>
                        </span>
                        <div class="card-body eshop-cards">
                            <span class="ripple-effect"></span>
                            <h3 class="text-warning mb-0 txt-ellipsis-1" id="hotspotonline"></h3>
                            <p class="mg-b-35 f-w-600 text-dark-800 txt-ellipsis-1">User Online</p>
                            <span class="badge bg-light-dark">Hotspot</span>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col">
                <a href="/pppoe_online" class="text-decoration-none text-reset">
                    <div class="card text-center h-100">
                        <span class="bg-success h-50 w-50 d-flex-center rounded-circle m-auto eshop-icon-box">
                            <i class="ph-duotone ph-user-circle-plus f-s-24"></i>
                        </span>
                        <div class="card-body eshop-cards">
                            <span class="ripple-effect"></span>
                            <h3 class="text-success mb-0" id="pppoeonline"></h3>
                            <p class="mg-b-35 f-w-600 text-dark-800 txt-ellipsis-1">User Online</p>
                            <span class="badge bg-light-success">PPPoE</span>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Ringkasan Keuangan & Informasi Layanan -->
        <div class="row g-3">
            <div class="col-12 col-xxl-8">
                <div class="card project-data-container h-100">
                    <div class="card-body">
                        <div
                            class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3">
                            <h6 class="mb-0">Ringkasan Keuangan</h6>
                            <a href="/keuangan/transaksi"
                                class="btn btn-outline-primary btn-sm d-flex align-items-center">
                                Lihat Semua <i class="ti ti-arrow-narrow-right ms-1"></i>
                            </a>
                        </div>
                        <div class="chart-container position-relative" style="height: 300px; width: 100%;">
                            <canvas id="chartIncomeExpense"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xxl-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="mb-3">Informasi Layanan</h5>
                        <ul class="list-box top-brand-list mb-0">
                            <li class="d-flex align-items-center">
                                <div
                                    class="b-1-light bg-primary-200 p-1 h-40 w-40 d-flex-center b-r-12 flex-shrink-0 overflow-hidden box-list-img">
                                    <img alt="Radius Engine" class="img-fluid"
                                        src="https://cdn-icons-png.flaticon.com/512/4270/4270036.png">
                                </div>
                                <div class="flex-grow-1 mg-s-45">
                                    <h6 class="mb-0 f-w-500 text-dark-800 txt-ellipsis-1">Radius Engine</h6>
                                    <p class="text-success-800 mb-0">Running</p>
                                </div>
                                <div class="text-end">
                                    <i class="ti ti-shield-check text-success" style="font-size: 1.25rem;"></i>
                                </div>
                            </li>
                            <li class="d-flex align-items-center">
                                <div
                                    class="b-1-light bg-primary-200 p-1 h-40 w-40 d-flex-center b-r-12 flex-shrink-0 overflow-hidden box-list-img">
                                    <img alt="Lisensi" class="img-fluid"
                                        src="https://cdn-icons-png.flaticon.com/128/1728/1728431.png">
                                </div>
                                <div class="flex-grow-1 mg-s-45">
                                    <h6 class="mb-0 f-w-500 text-dark-800 txt-ellipsis-1">Lisensi</h6>
                                    <p class="text-secondary-800 mb-0">
                                        {{ \App\Models\Owner\License::find(multi_auth()->license_id)->name }}
                                    </p>
                                </div>
                                <div class="text-end">
                                    <a href="/order/license" class="badge bg-light-info">
                                        <i class="ti ti-arrow-up-right" style="margin-right: 4px;"></i> Upgrade
                                    </a>
                                </div>
                            </li>
                            <li class="d-flex align-items-center">
                                <div
                                    class="b-1-light bg-primary-200 p-1 h-40 w-40 d-flex-center b-r-12 flex-shrink-0 overflow-hidden box-list-img">
                                    <img alt="Jatuh Tempo" class="img-fluid"
                                        src="https://cdn-icons-png.flaticon.com/128/5448/5448287.png">
                                </div>
                                <div class="flex-grow-1 mg-s-45">
                                    <h6 class="mb-0 f-w-500 text-dark-800 txt-ellipsis-1">Jatuh Tempo</h6>
                                    <p class="text-secondary-800 mb-0">
                                        {{ \Carbon\Carbon::parse(multi_auth()->next_due)->format('d/m/Y') }}
                                    </p>
                                </div>
                                <div class="text-end">
                                    <a href="/account" class="badge bg-light-primary">
                                        <i class="ti ti-credit-card" style="margin-right: 4px;"></i>Bayar
                                    </a>
                                </div>
                            </li>
                            <li class="d-flex align-items-center">
                                <div
                                    class="b-1-light bg-primary-200 p-1 h-40 w-40 d-flex-center b-r-12 flex-shrink-0 overflow-hidden box-list-img">
                                    <img alt="PPPoE" class="img-fluid"
                                        src="https://cdn-icons-png.flaticon.com/128/1256/1256650.png">
                                </div>
                                <div class="flex-grow-1 mg-s-45">
                                    <h6 class="mb-0 f-w-500 text-dark-800 txt-ellipsis-1">PPPoE User</h6>
                                    <p class="text-secondary-800 mb-0">
                                        {{ \App\Models\Pppoe\PppoeUser::where('shortname', multi_auth()->shortname)->count() }}
                                        /
                                        <b>{{ \App\Models\Owner\License::find(multi_auth()->license_id)->limit_pppoe }}</b>
                                    </p>
                                </div>
                            </li>
                            <li class="d-flex align-items-center">
                                <div
                                    class="b-1-light bg-primary-200 p-1 h-40 w-40 d-flex-center b-r-12 flex-shrink-0 overflow-hidden box-list-img">
                                    <img alt="Hotspot" class="img-fluid"
                                        src="https://cdn-icons-png.flaticon.com/128/4041/4041269.png">
                                </div>
                                <div class="flex-grow-1 mg-s-45">
                                    <h6 class="mb-0 f-w-500 text-dark-800 txt-ellipsis-1">Hotspot User</h6>
                                    <p class="text-secondary-800 mb-0">
                                        {{ \App\Models\Hotspot\HotspotUser::where('shortname', multi_auth()->shortname)->count() }}
                                        /
                                        <b>{{ \App\Models\Owner\License::find(multi_auth()->license_id)->limit_hs }}</b>
                                    </p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mikrotik Table -->
        <div class="row g-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <h5 class="mb-0">Daftar Mikrotik</h5>
                        <a href="/radius/mikrotik" class="btn btn-outline-primary btn-sm d-flex align-items-center">
                            Lihat Semua <i class="ti ti-arrow-narrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body pt-2 px-3 pb-3" style="min-height: 200px;">
                        <div style="width: 100%; overflow-x: auto;">
                            <table class="table table-striped table-bordered mb-0" id="example"
                                style="width: 100%; table-layout: auto; min-width: 600px;">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>IP Address</th>
                                        <th>Status</th>
                                        <th>Total User</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Data belum tersedia</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Last Activity -->
        <div class="row g-3">
            <div class="col-lg-12">
                <div class="card equal-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Last Activity</h5>
                            <a href="/log" class="btn btn-outline-primary btn-sm d-flex align-items-center">
                                Lihat Semua <i class="ti ti-arrow-narrow-right ms-1"></i>
                            </a>
                        </div>
                        <ul class="app-timeline-box">
                            @forelse($activity->take(5) as $act)
                                <li class="timeline-section">
                                    <div class="timeline-icon">
                                        <span class="text-light-primary h-35 w-35 d-flex-center b-r-50">
                                            <i class="ti ti-circle-check f-s-20"></i>
                                        </span>
                                    </div>
                                    <div class="timeline-content bg-light-primary b-1-primary">
                                        <div class="d-flex justify-content-between align-items-center timeline-flex">
                                            <h6 class="mt-2 text-primary">
                                                {{ $act->causer_type::find($act->causer_id)->username ?? $act->causer_type::find($act->causer_id)->name }}
                                            </h6>
                                            <p class="text-dark">{{ $act->created_at->diffForHumans() }}</p>
                                        </div>
                                        <p class="mt-2 text-dark">{{ $act->description }}</p>
                                    </div>
                                </li>
                            @empty
                                <li class="text-muted text-center">Tidak ada aktivitas.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- Body main section ends -->
@endsection
@push('scripts')
<script>
    function updateDashboard() {
        $.ajax({
            url: "{{ route('dashboard.data') }}",
            method: "GET",
            dataType: "json",
            success: function(data) {
                // Update Income Today
                $('#incometoday').html('Rp' + data.incometoday.toLocaleString('id-ID'));

                // Update Total Unpaid
                $('#totalunpaid').html(data.totalunpaid);

                // Update Hotspot Online
                $('#hotspotonline').html(data.hotspotonline);

                // Update PPPoE Online
                $('#pppoeonline').html(data.pppoeonline);

                // Anda juga dapat mengupdate grafik dan elemen lainnya menggunakan data.dataIncome, data.dataExpense, dll.
                // Misalnya, menggunakan Chart.js:
                if (window.myChartIncomeExpense) {
                    myChartIncomeExpense.data.labels = data.dataIncome.labels;
                    myChartIncomeExpense.data.datasets[0].data = data.dataIncome.data;
                    myChartIncomeExpense.data.datasets[1].data = data.dataExpense.data;
                    myChartIncomeExpense.update();
                }

                // Update elemen lainnya seperti activity log jika diperlukan
                // ...
            },
            error: function(xhr, status, error) {
                console.error('Gagal memuat data dashboard: ' + error);
            }
        });
    }

    // Panggil fungsi updateDashboard segera saat halaman dimuat
    updateDashboard();

    // Optional: Set interval untuk refresh data secara otomatis setiap 30 detik atau sesuai kebutuhan
    setInterval(updateDashboard, 30000); // 30000 ms = 30 detik

    let table = $('#example').DataTable({
        processing: true,
        serverSide: true,
        // lengthMenu: [4],
        ajax: '{{ url()->current() }}',
        columns: [{
                data: null,
                'sortable': false,
                render: function(data, type, row, meta) {
                    return '<div style="text-align: left;">' + (meta.row + meta.settings
                        ._iDisplayStart + 1) + '</div>';
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
            // {
            //     data: 'timezone',
            //     name: 'timezone',
            //     render: function(data) {
            //         if (data === '0') {
            //             return 'Asia/Jakarta';
            //         } else if (data === '3600') {
            //             return 'Asia/Makassar';
            //         } else if (data === '7200') {
            //             return 'Asia/Jayapura';
            //         }
            //     },
            // },
            {
                data: 'ping',
                name: 'ping',
            },
            {
                data: 'total_session',
                name: 'total_session',
                render: function(data, type, row, meta) {
                    var id = 'total_session-' + meta.row;
                    var content = '<span class="material-symbols-outlined">monitoring</span> ' + data +
                        ' online';
                    // Tampilkan spinner sampai konten terupdate
                    return '<span id="' + id +
                        '"><span class="material-symbols-outlined spinner">progress_activity</span></span>';
                }

            },
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
    // // Fungsi tambahan yang dipanggil setelah DataTable diload
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


    // Set new default font family and font color to mimic Bootstrap's default styling
    (Chart.defaults.global.defaultFontFamily = "Inter"),
    '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = "#858796";

    function number_format(number, decimals, dec_point, thousands_sep) {
        // *     example: number_format(1234.56, 2, ',', ' ');
        // *     return: '1 234,56'
        number = (number + "").replace(",", "").replace(" ", "");
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = typeof thousands_sep === "undefined" ? "." : thousands_sep,
            dec = typeof dec_point === "undefined" ? "." : dec_point,
            s = "",
            toFixedFix = function(n, prec) {
                var k = Math.pow(10, prec);
                return "" + Math.round(n * k) / k;
            };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : "" + Math.round(n)).split(".");
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || "").length < prec) {
            s[1] = s[1] || "";
            s[1] += new Array(prec - s[1].length + 1).join("0");
        }
        return s.join(dec);
    }

    var ctx = document.getElementById("chartIncomeExpense");
    var myLineChart = new Chart(ctx, {
        type: "line",
        data: {
            labels: [
                "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
            ],
            datasets: [{
                    label: "Pendapatan",
                    lineTension: 0.3,
                    backgroundColor: "rgba(0, 97, 242, 0.05)",
                    borderColor: "rgba(0, 97, 242, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(0, 97, 242, 1)",
                    pointBorderColor: "rgba(0, 97, 242, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(0, 97, 242, 1)",
                    pointHoverBorderColor: "rgba(0, 97, 242, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: @json($dataIncome['data']),
                },
                {
                    label: "Pengeluaran",
                    lineTension: 0.3,
                    backgroundColor: "rgba(220, 53, 69, 0.05)",
                    borderColor: "rgba(220, 53, 69, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(220, 53, 69, 1)",
                    pointBorderColor: "rgba(220, 53, 69, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(220, 53, 69, 1)",
                    pointHoverBorderColor: "rgba(220, 53, 69, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: @json($dataExpense['data']),
                }
            ]
        },
        options: {
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 0
                }
            },
            scales: {
                xAxes: [{
                    time: {
                        unit: "month"
                    },
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 12
                    }
                }],
                yAxes: [{
                    ticks: {
                        maxTicksLimit: 6,
                        padding: 10,
                        callback: function(value, index, values) {
                            return "Rp " + number_format(value);
                        }
                    },
                    gridLines: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                }]
            },
            legend: {
                display: true
            },
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                titleMarginBottom: 10,
                titleFontColor: "#6e707e",
                titleFontSize: 14,
                borderColor: "#dddfeb",
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                intersect: false,
                mode: "index",
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem, chart) {
                        var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || "";
                        return datasetLabel + ": Rp " + number_format(tooltipItem.yLabel);
                    }
                }
            }
        }
    });


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
</script>
@endpush
