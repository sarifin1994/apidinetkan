@extends('backend.layouts.app_new')
@section('main')
@section('title', 'Keuangan')
<!-- Content -->
<div class="container-fluid">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h2 class="page-title">Keuangn</h2>
        </div>
    </div>
        <div class="row g-3 mb-4">
            <!-- Saldo Cards -->
            @php
                $cards = [
                    ['title' => 'SALDO TERKINI', 'id' => 'totalSaldo', 'value' => multi_auth()->balance]
                ];
            @endphp

            @foreach ($cards as $card)
                <div class="col-12 col-md-6 col-xxl-3">
                    <div class="card">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="text-muted mb-1">{{ $card['title'] }}</h4>
                                <h3 id="{{ $card['id'] }}">Rp{{ number_format($card['value'], 0, '.', '.') }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    <!-- Tabel Transaksi -->
     
    <!-- <div class="row">
      <div class="col-12">
        <div class="card overview-details-box b-s-3-primary ">
          <div class="card-body">
            <div class="row">
              <div class="col-md-12">
                <div class="d-flex align-items-center gap-1">
                  <div class="flex-grow-1">
                    <button id="get_all" class="btn btn-light-primary" onclick="get_data('all')"> Semua </button>
                    <button id="get_harian" class="btn btn-light-primary" onclick="get_data('harian')"> Harian </button>
                    <button id="get_mingguan" class="btn btn-light-primary" onclick="get_data('mingguan')"> Mingguan </button>
                    <button id="get_bulanan" class="btn btn-light-primary" onclick="get_data('bulanan')"> Bulanan </button>
                    <button id="get_tahunan" class="btn btn-light-primary" onclick="get_data('tahunan')"> Tahunan </button>
                    <br>
                    <p>Klik button untuk ambil datanya sesuai dengan filternya</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div> -->

    <div class="card">
        <div class="card-body table-responsive">
            <table id="myTable" class="table table-responsive table-hover display nowrap" width="100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tgl/Waktu</th>
                        <th>Deskripsi</th>
                        <th>Nominal</th>
                        <th>Jenis</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- DataTables akan load otomatis -->
                </tbody>
            </table>
        </div>
    </div>
</div>


@endsection

@push('scripts')
<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function() {
        const delay = 1000; // Delay 1,5 detik
        const elements = document.querySelectorAll('[data-value]');
        elements.forEach(function(el) {
            setTimeout(function() {
                el.innerHTML = el.getAttribute('data-value');
            }, delay);
        });
    });

    // document.getElementById('getnominal').addEventListener('click', function() {
    //     document.getElementById('nominal_wd').value = totalSaldo;
    // });

    // get_data();
    // function get_data(type = 'all'){
    //     if(type == 'all'){
    //         const btnall = document.getElementById('get_all');
    //         btnall.classList.remove('btn-light-primary');
    //         btnall.classList.add('btn-primary');


    //         const btnharian = document.getElementById('get_harian');
    //         btnharian.classList.add('btn-light-primary');
    //         btnharian.classList.remove('btn-primary');

    //         const btnmingguan = document.getElementById('get_mingguan');
    //         btnmingguan.classList.add('btn-light-primary');
    //         btnmingguan.classList.remove('btn-primary');
            
    //         const btnbulanan = document.getElementById('get_bulanan');
    //         btnbulanan.classList.add('btn-light-primary');
    //         btnbulanan.classList.remove('btn-primary');
            
    //         const btntahunan = document.getElementById('get_tahunan');
    //         btntahunan.classList.add('btn-light-primary');
    //         btntahunan.classList.remove('btn-primary');
    //     }
    //     if(type == 'harian'){
    //         const btnharian = document.getElementById('get_harian');
    //         btnharian.classList.remove('btn-light-primary');
    //         btnharian.classList.add('btn-primary');

            
    //         const btnmingguan = document.getElementById('get_mingguan');
    //         btnmingguan.classList.add('btn-light-primary');
    //         btnmingguan.classList.remove('btn-primary');
            
    //         const btnbulanan = document.getElementById('get_bulanan');
    //         btnbulanan.classList.add('btn-light-primary');
    //         btnbulanan.classList.remove('btn-primary');
            
    //         const btntahunan = document.getElementById('get_tahunan');
    //         btntahunan.classList.add('btn-light-primary');
    //         btntahunan.classList.remove('btn-primary');
            
    //         const btnall = document.getElementById('get_all');
    //         btnall.classList.add('btn-light-primary');
    //         btnall.classList.remove('btn-primary');
    //     }
    //     if(type == 'mingguan'){            
    //         const btnmingguan = document.getElementById('get_mingguan');
    //         btnmingguan.classList.remove('btn-light-primary');
    //         btnmingguan.classList.add('btn-primary');


    //         const btnharian = document.getElementById('get_harian');
    //         btnharian.classList.add('btn-light-primary');
    //         btnharian.classList.remove('btn-primary');
            
    //         const btnbulanan = document.getElementById('get_bulanan');
    //         btnbulanan.classList.add('btn-light-primary');
    //         btnbulanan.classList.remove('btn-primary');
            
    //         const btntahunan = document.getElementById('get_tahunan');
    //         btntahunan.classList.add('btn-light-primary');
    //         btntahunan.classList.remove('btn-primary');
            
    //         const btnall = document.getElementById('get_all');
    //         btnall.classList.add('btn-light-primary');
    //         btnall.classList.remove('btn-primary');
    //     }
    //     if(type == 'bulanan'){       
    //         const btnbulanan = document.getElementById('get_bulanan');
    //         btnbulanan.classList.remove('btn-light-primary');
    //         btnbulanan.classList.add('btn-primary');
            
            
    //         const btnmingguan = document.getElementById('get_mingguan');
    //         btnmingguan.classList.add('btn-light-primary');
    //         btnmingguan.classList.remove('btn-primary');

    //         const btnharian = document.getElementById('get_harian');
    //         btnharian.classList.add('btn-light-primary');
    //         btnharian.classList.remove('btn-primary');
            
            
    //         const btntahunan = document.getElementById('get_tahunan');
    //         btntahunan.classList.add('btn-light-primary');
    //         btntahunan.classList.remove('btn-primary');
            
    //         const btnall = document.getElementById('get_all');
    //         btnall.classList.add('btn-light-primary');
    //         btnall.classList.remove('btn-primary');
    //     }
    //     if(type == 'tahunan'){       
    //         const btntahunan = document.getElementById('get_tahunan');
    //         btntahunan.classList.remove('btn-light-primary');
    //         btntahunan.classList.add('btn-primary');


    //         const btnbulanan = document.getElementById('get_bulanan');
    //         btnbulanan.classList.add('btn-light-primary');
    //         btnbulanan.classList.remove('btn-primary');
                        
    //         const btnmingguan = document.getElementById('get_mingguan');
    //         btnmingguan.classList.add('btn-light-primary');
    //         btnmingguan.classList.remove('btn-primary');

    //         const btnharian = document.getElementById('get_harian');
    //         btnharian.classList.add('btn-light-primary');
    //         btnharian.classList.remove('btn-primary');
            
    //         const btnall = document.getElementById('get_all');
    //         btnall.classList.add('btn-light-primary');
    //         btnall.classList.remove('btn-primary');
    //     }
    //     // Hancurkan DataTable jika sudah ada
    //     if ($.fn.DataTable.isDataTable('#myTable')) {
    //         $('#myTable').DataTable().clear().destroy();
    //     }
    //     $('#myTable').DataTable({
    //         processing: true,
    //         serverSide: true,
    //         scrollX: true,
    //         order: [
    //             1, 'desc'
    //         ],
    //         ajax: {
    //             url: '{{ url()->current() }}',
    //         },
    //         columns: [{
    //                 data: null,
    //                 'sortable': false,
    //                 render: function(data, type, row, meta) {
    //                     return meta.row + meta.settings._iDisplayStart + 1;
    //                 }
    //             },
    //             {
    //                 data: 'tx_date',
    //                 name: 'tx_date',
    //                 render: function(data, type, row, meta) {
    //                     return moment(data).local().format('DD MMM YYYY HH:mm:ss');
    //                 },
    //             },
    //             {
    //                 data: 'notes',
    //                 name: 'notes'
    //             },
    //             {
    //                 data: 'tx_amount',
    //                 name: 'tx_amount'
    //             },
    //             {
    //                 data: 'type',
    //                 name: 'type',
    //                 render: function(data, type, row) {
    //                     if (data === 'in') {
    //                         return "<span class='text-success'>Pemasukan</span>";
    //                     } else if (data === 'out') {
    //                         return "<span class='text-danger'>Pengeluaran</span>"
    //                     }
    //                 }
    //             },
    //             // {
    //             //     data: 'kategori',
    //             //     name: 'kategori'
    //             // },
    //             // {
    //             //     data: 'deskripsi',
    //             //     name: 'deskripsi'
    //             // },
    //             // {
    //             //     data: 'nominal',
    //             //     name: 'nominal',
    //             //     render: $.fn.dataTable.render.number('.', ',', 0, ''),
    //             // },
    //             // {
    //             //     data: 'metode',
    //             //     name: 'metode'
    //             // },
    //             // {
    //             //     data: 'created_by',
    //             //     name: 'created_by',
    //             // },
    //             // {
    //             //     data: 'action',
    //             //     name: 'action',
    //             // },
    //         ]
    //     });
    // }

    
    $('#myTable').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        order: [
            1, 'desc'
        ],
        ajax: {
            url: '{{ url()->current() }}',
        },
        columns: [{
                data: null,
                'sortable': false,
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            {
                data: 'tx_date',
                name: 'tx_date',
                render: function(data, type, row, meta) {
                    return moment(data).local().format('DD MMM YYYY HH:mm:ss');
                },
            },
            {
                data: 'notes',
                name: 'notes'
            },
            {
                data: 'tx_amount',
                name: 'tx_amount',
                render: $.fn.dataTable.render.number('.', ',', 0, ''),
            },
            {
                data: 'type',
                name: 'type',
                render: function(data, type, row) {
                    if (data === 'in') {
                        return "<span class='text-success'>Pemasukan</span>";
                    } else if (data === 'out') {
                        return "<span class='text-danger'>Pengeluaran</span>"
                    }
                }
            },
            // {
            //     data: 'kategori',
            //     name: 'kategori'
            // },
            // {
            //     data: 'deskripsi',
            //     name: 'deskripsi'
            // },
            // {
            //     data: 'nominal',
            //     name: 'nominal',
            //     render: $.fn.dataTable.render.number('.', ',', 0, ''),
            // },
            // {
            //     data: 'metode',
            //     name: 'metode'
            // },
            // {
            //     data: 'created_by',
            //     name: 'created_by',
            // },
            // {
            //     data: 'action',
            //     name: 'action',
            // },
        ]
    });   
</script>
@endpush
