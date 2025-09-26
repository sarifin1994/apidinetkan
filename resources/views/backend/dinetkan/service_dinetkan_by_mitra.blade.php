@extends('backend.layouts.app_new')

@section('title', 'Product')

@section('css')
  <!-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">

  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select2.css') }}"> -->
  <!-- DataTables CSS -->
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css"> -->

    <!-- Buttons Extension CSS -->
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css"> -->

@endsection

@section('main')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6 ps-0">
          <h3>Member Mitra</h3>
        </div>
        <div class="col-sm-6 pe-0">
          <ol class="breadcrumb">
            <li class="breadcrumb-item">
              <a href="/">
                <!-- <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg> -->
              </a>
            </li>
            <!-- <li class="breadcrumb-item active">Product</li> -->
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Container-fluid starts-->
  <div class="container-fluid user-management-page">
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive custom-scrollbar">
            <table id="kuponTable" class="table-hover display nowrap clickable table" width="100%">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Mitra</th>
                    <th>Nama Customer</th>
                    <th>Service</th>
                    <th>Harga</th>
                    <th>PPN %</th>
                    <th>Total PPN</th>
                    <th>BHP %</th>
                    <th>Total BHP</th>
                    <th>USO</th>
                    <th>Total USO</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($mapping as $row)
                <?php
                    $totalppn = ($row->product_ppn ? ($row->product_price * $row->product_ppn / 100) : 0);
                    $totalbhp = ($row->product_bhp ? ($row->product_price * $row->product_bhp / 100) : 0);
                    $totaluso = ($row->product_uso ? ($row->product_price * $row->product_uso / 100) : 0);
                ?>
                    <tr>
                    <td>{{ $row->id}}</td>
                    <td>{{ $row->fmitra}} {{ $row->lmitra }}</td>
                    <td>{{ $row->full_name}}</td>
                    <td>{{ $row->product_name}}</td>
                    <td>Rp {{ number_format($row->product_price, 0, '.', '.') }}</td>
                    <td>{{ $row->product_ppn}}</td>
                    <td>Rp {{ number_format($totalppn, 0, '.', '.') }}</td>
                    <td>{{ $row->product_bhp }}</td>
                    <td>Rp {{ number_format($totalbhp, 0, '.', '.') }}</td>
                    <td>{{ $row->product_uso }}</td>
                    <td>Rp {{ number_format($totaluso, 0, '.', '.') }}</td>
                    </tr>
                @empty
                @endforelse
                </tbody>
            </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Container-fluid Ends-->

@endsection

@push('scripts')
  <!-- <script src="{{ asset('assets/radiusqu/dist/js/moment.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/datatable.custom.js') }}"></script> -->

    <!-- jQuery -->
    <!-- <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script> -->

    <!-- DataTables JS -->
    <!-- <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script> -->

    <!-- Buttons Extension JS -->
    <!-- <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.pdfmake.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script> -->

    <!-- PDFMake (untuk export PDF) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
  
    <script>
    $(document).ready(function() {
        $('#kuponTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'excel',
                {
                    text: 'Export PDF',
                    action: function () {
                        const { jsPDF } = window.jspdf;
                        const doc = new jsPDF('p', 'pt', 'A4');
                        doc.text('Data Report Service', 40, 30);

                        doc.autoTable({
                            html: '#kuponTable',
                            startY: 50,
                            theme: 'grid',
                            styles: {
                                fontSize: 10,
                                cellPadding: 5,
                                valign: 'middle',
                                halign: 'center',
                            },
                            headStyles: {
                                fillColor: [240, 240, 240],
                                textColor: [0, 0, 0],
                            }
                        });

                        doc.save('Laporan_Kupon.pdf');
                    }
                }
            ]
        });


    //   $('#kuponTable').DataTable( {
    //     dom: 'Bfrtip',  // Aktifkan bagian untuk tombol
    //     buttons: [
    //         // 'copy', 'csv', 'excel', 'pdf', 'print'
    //         {
    //             extend: 'pdfHtml5',
    //             // exportOptions: {
    //             //     columns: [ 0, 1, 2, 5 ]
    //             // }
    //         },
            
    //     ]
    // } );
    });
    </script>
@endpush
