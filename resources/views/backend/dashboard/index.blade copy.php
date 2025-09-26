@extends('backend.layouts.app')
@section('main')
@section('title', 'Dashboard')

<!-- Content -->
<div class="container-lg">
    <h2 class="fw-bold mb-2">Hello, {{ multi_auth()->username }}</h2>
    <p class="text-muted mb-8">Selamat datang di dashboard akun Anda</p>


    <div class="row mb-8">
        <!-- Card 1: Keuangan - Income Hari Ini -->
        <div class="col-12 col-md-6 col-xxl-3 mb-4 mb-md-0">
            <div class="card bg-body-tertiary border-transparent">
                <a href="/keuangan/transaksi" style="text-decoration: none; color: inherit;">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <!-- Heading -->
                                <h4 class="fs-sm fw-normal text-body-secondary mb-1">
                                    Keuangan<br>
                                    <small>Income Hari Ini</small>
                                </h4>
                                <!-- Text dengan loading effect -->
                                <div class="fs-md fw-semibold" id="incometoday"
                                    data-value="Rp{{ number_format($incometoday, 0, '.', '.') }}">
                                    <span class="material-symbols-outlined spinner">progress_activity</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <!-- Avatar -->
                                <div class="avatar avatar-lg bg-body text-primary">
                                    <i class="fs-4" data-duoicon="discount"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Card 2: Invoice - Total Unpaid -->
        <div class="col-12 col-md-6 col-xxl-3 mb-4 mb-md-0">
            <div class="card bg-body-tertiary border-transparent">
                <a href="/billing/unpaid" style="text-decoration: none; color: inherit;">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <!-- Heading -->
                                <h4 class="fs-sm fw-normal text-body-secondary mb-1">
                                    Invoice<br>
                                    <small>Total Unpaid</small>
                                </h4>
                                <!-- Text dengan loading effect -->
                                <div class="fs-md fw-semibold" id="totalunpaid" data-value="{{ $totalunpaid }}">
                                    <span class="material-symbols-outlined spinner">progress_activity</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <!-- Avatar -->
                                <div class="avatar avatar-lg bg-body text-primary">
                                    <i class="fs-4" data-duoicon="slideshow"></i>
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
                <a href="/hotspot/online" style="text-decoration: none; color: inherit;">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <!-- Heading -->
                                <h4 class="fs-sm fw-normal text-body-secondary mb-1">
                                    Hotspot<br>
                                    <small>User Onlline</small>
                                </h4>
                                <!-- Text dengan loading effect -->
                                <div class="fs-md fw-semibold" id="hotspotonline" data-value="{{ $hotspotonline }}">
                                    <span class="material-symbols-outlined spinner">progress_activity</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <!-- Avatar -->
                                <div class="avatar avatar-lg bg-body text-primary">
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
                <a href="/pppoe/online" style="text-decoration: none; color: inherit;">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <!-- Heading -->
                                <h4 class="fs-sm fw-normal text-body-secondary mb-1">
                                    PPPoE<br>
                                    <small>User Online</small>
                                </h4>
                                <!-- Text dengan loading effect -->
                                <div class="fs-md fw-semibold" id="pppoeonline" data-value="{{ $pppoeonline }}">
                                    <span class="material-symbols-outlined spinner">progress_activity</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <!-- Avatar -->
                                <div class="avatar avatar-lg bg-body text-primary">
                                    <i class="fs-4" data-duoicon="clock"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-xxl-8">
            <!-- Performance -->
            <div class="card mb-6">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="fs-6 mb-0">Ringkasan Pendapatan</h3>
                        </div>
                        <div class="col-auto my-n3 me-n3">
                            <a class="btn btn-link" href="/keuangan/transaksi">
                                Lihat Semua
                                <span class="material-symbols-outlined">arrow_right_alt</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area"><canvas id="chartIncome" width="100%" height="300"></canvas></div>
                </div>
            </div>

            <div class="card mb-6 mb-xxl-0">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="fs-6 mb-0">Daftar Mikrotik</h3>
                        </div>
                        <div class="col-auto my-n3 me-n3">
                            <a class="btn btn-link" href="/radius/mikrotik">
                                Lihat Semua
                                <span class="material-symbols-outlined">arrow_right_alt</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="pb-5 table-responsive">
                    <table id="myTable" class="table table-responsive table-flush table-hover display nowrap"
                        width="100%">
                        <thead>
                            {{-- <th>#</th> --}}
                            <th>Nama</th>
                            <th>IP Address</th>
                            <th>Timezone</th>
                            <th>Status</th>
                            <th>Total User</th>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-12 col-xxl-4">
            <!-- Goals -->
            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="fs-6 mb-0">Informasi Layanan</h3>
                </div>
                <div class="card-body py-3">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item px-0">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="avatar">
                                        <span class="material-symbols-outlined">
                                            hub
                                        </span>
                                    </div>
                                </div>
                                <div class="col ms-n2">
                                    <h6 class="fs-base fw-normal mb-0">Radius Engine</h6>
                                    <span class="fs-sm text-success"><span class="material-symbols-outlined">
                                            verified
                                        </span> Running</span>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item px-0">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="avatar">
                                        <span class="material-symbols-outlined">
                                            license
                                        </span>
                                    </div>
                                </div>
                                <div class="col ms-n2">
                                    <h6 class="fs-base fw-normal mb-0">Lisensi</h6>
                                    <span class="fs-sm text-body-secondary"><span class="fs-sm text">
                                            {{ \App\Models\Owner\License::where('id', multi_auth()->license_id)->first()->name }}
                                        </span>
                                </div>
                                <div class="col ms-n2 mt-2">
                                    <a class="btn btn-sm btn-link" href="/keuangan/transaksi">
                                        <span class="material-symbols-outlined">upgrade</span> Upgrade

                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item px-0">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="avatar">
                                        <span class="material-symbols-outlined">
                                            pending_actions
                                        </span>
                                    </div>
                                </div>
                                <div class="col ms-n2">
                                    <h6 class="fs-base fw-normal mb-0">Jatuh Tempo</h6>
                                    <span class="fs-sm text-body-secondary"><span class="fs-sm text">
                                            {{ \Carbon\Carbon::parse(multi_auth()->next_due)->format('d/m/Y') }}
                                        </span>
                                </div>
                                <div class="col ms-n2 mt-2">
                                    <a class="btn btn-sm btn-link" href="/keuangan/transaksi">
                                        <span class="material-symbols-outlined">shopping_cart_checkout</span> Bayar

                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item px-0">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="avatar">
                                        <span class="material-symbols-outlined">
                                            group
                                        </span>
                                    </div>
                                </div>
                                <div class="col ms-n2">
                                    <h6 class="fs-base fw-normal mb-0">PPPoE User</h6>
                                    <span
                                        class="fs-sm text-body-secondary">{{ \App\Models\Pppoe\PppoeUser::where('shortname', multi_auth()->shortname)->count() }}
                                        /
                                        <b>{{ \App\Models\Owner\License::where('id', multi_auth()->license_id)->first()->limit_pppoe }}</b></span>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item px-0">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="avatar">
                                        <div class="avatar">
                                            <span class="material-symbols-outlined">
                                                article_person
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col ms-n2">
                                    <h6 class="fs-base fw-normal mb-0">Hotspot User</h6>
                                    <span
                                        class="fs-sm text-body-secondary">{{ \App\Models\Hotspot\HotspotUser::where('shortname', multi_auth()->shortname)->count() }}
                                        /
                                        <b>{{ \App\Models\Owner\License::where('id', multi_auth()->license_id)->first()->limit_hs }}</b></span>
                                </div>
                                <div class="col-auto">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Activity -->
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="fs-6 mb-0">Last Activity</h3>
                        </div>
                        <div class="col-auto my-n3 me-n3">
                            <a class="btn btn-link" href="/log">
                                Lihat Semua
                                <span class="material-symbols-outlined">arrow_right_alt</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="activity">
                        @forelse($activity as $activity)
                            <li data-icon="chat_bubble">
                                <div>
                                    <h6 class="fs-base mb-1">
                                        {{ \App\Models\User::where('id', $activity->causer_id)->first()->username }}
                                        <span
                                            class="fs-sm fw-normal text-body-secondary ms-1">{{ $activity->created_at->diffForHumans() }}</span>
                                    </h6>
                                    <p class="mb-0"><small>{{ $activity->description }}</small></p>
                                </div>
                            </li>
                        @empty
                        @endforelse

                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js" crossorigin="anonymous"></script>
<script>
    let table = $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        searching: false,
        paginate: false,
        info: false,
        ajax: '{{ url()->current() }}',
        columns: [
            // {
            //     data: null,
            //     'sortable': false,
            //     render: function(data, type, row, meta) {
            //         return meta.row + meta.settings._iDisplayStart + 1;
            //     }
            // },
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
                render: function(data, type, row, meta) {
                    // Buat id unik untuk elemen pada baris ini
                    var id = 'ping-' + meta.row;
                    // Nilai sebenarnya berdasarkan kondisi data
                    var content = (data === 1) ?
                        '<span class="badge bg-success-subtle text-success">Online</span>' :
                        '<span class="badge bg-danger-subtle text-danger">Offline</span>';
                    // Simulasikan loading dengan mengganti konten setelah 1.5 detik
                    setTimeout(function() {
                        var el = document.getElementById(id);
                        if (el) {
                            el.innerHTML = content;
                        }
                    }, 1500);
                    // Kembalikan spinner sebagai placeholder
                    return '<span id="' + id +
                        '"><span class="material-symbols-outlined spinner">progress_activity</span></span>';
                }
            },
            {
                data: 'online',
                name: 'online',
                render: function(data, type, row, meta) {
                    var id = 'online-' + meta.row;
                    var content = '<span class="material-symbols-outlined">monitoring</span> ' + data +
                        ' online';
                    setTimeout(function() {
                        var el = document.getElementById(id);
                        if (el) {
                            el.innerHTML = content;
                        }
                    }, 1500);
                    return '<span id="' + id +
                        '"><span class="material-symbols-outlined spinner">progress_activity</span></span>';
                }

            },
        ]
    });
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

    var income = document.getElementById("chartIncome");
    var myLineChart = new Chart(income, {
        type: "line",
        data: {
            labels: [
                "Jan",
                "Feb",
                "Mar",
                "Apr",
                "May",
                "Jun",
                "Jul",
                "Aug",
                "Sep",
                "Oct",
                "Nov",
                "Dec"
            ],
            datasets: [{
                label: "Income",
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
            }]
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
                        unit: "date"
                    },
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 7
                    }
                }],
                yAxes: [{
                    ticks: {
                        maxTicksLimit: 6,
                        padding: 10,
                        // Include a dollar sign in the ticks
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
                display: false
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
                        var datasetLabel =
                            chart.datasets[tooltipItem.datasetIndex].label || "";
                        return datasetLabel + ": Rp " + number_format(tooltipItem.yLabel);
                    }
                }
            }
        }
    });

    document.addEventListener("DOMContentLoaded", function() {
        // Simulasi delay loading, misalnya 1.5 detik
        setTimeout(function() {
            document.getElementById('incometoday').innerHTML = document.getElementById('incometoday')
                .dataset.value;
            document.getElementById('totalunpaid').innerHTML = document.getElementById('totalunpaid')
                .dataset.value;
            document.getElementById('hotspotonline').innerHTML = document.getElementById(
                'hotspotonline').dataset.value;
            document.getElementById('pppoeonline').innerHTML = document.getElementById('pppoeonline')
                .dataset.value;
        }, 500);
    });
</script>
@endpush
