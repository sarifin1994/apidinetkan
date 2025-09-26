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
          <h3>Service</h3>
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
              <table id="myTable" class="table table-responsive table-hover display nowrap" width="100%">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Mitra</th>
                    <th>Bulan</th>
                    <th>Tahun</th>
                    <th>Total Member</th>
                    <th>Total Harga</th>
                    <th>Total PPN</th>
                    <th>Total BHP</th>
                    <th>Total USO</th>
                    <th>Status</th>
                    <th>Paid Date</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($mapping as $row)
                    <tr>
                    <td>{{ $row->id}}</td>
                    <td><a class="btn btn-light-primary btn-sm" href="{{ route('dinetkan.users_dinetkan_service_by_mitra', $row->dinetkan_user_id) }}">{{ $row->nama_mitra}}</a></td>
                    <td>{{ $row->month}}</td>
                    <td>{{ $row->year}}</td>
                    <td>{{ $row->total_member}}</td>
                    <td>Rp {{ number_format($row->total_price, 0, '.', '.') }}</td>
                    <td>Rp {{ number_format($row->total_ppn, 0, '.', '.') }}</td>
                    <td>Rp {{ number_format($row->total_bhp, 0, '.', '.') }}</td>
                    <td>Rp {{ number_format($row->total_uso, 0, '.', '.') }}</td>
                    <td>{{ $row->status }}</td>
                    <td>{{ $row->paid_date }}</td>
                    
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
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script> -->
    <!-- <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.pdfmake.min.js"></script> -->
    <!-- <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script> -->

    <!-- PDFMake (untuk export PDF) -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script> -->

    @if(env('APP_ENV') == 'development')
    <script src="https://app-sandbox.duitku.com/lib/js/duitku.js"></script>
    @endif

    @if(env('APP_ENV') == 'production')
    <script src="https://app-prod.duitku.com/lib/js/duitku.js"></script>
    @endif
    <!-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> -->
  
    <script>
    $(document).ready(function() {
      let table = $('#myTable').DataTable({});
      const baseurl = "{{ url('/') }}";
      $('#myTable').on('click', '#pay', function() {
          let id = $(this).data('id');
          $.ajax({
              url: baseurl + `/admin/billing/member_dinetkan/mapping_service_by_id/${id}`,
              type: "GET",
              data: {
                  id: id
              },
              success: function(data) {
                var setoran_id = data.id;  
                var dinetkan_user_id = data.dinetkan_user_id;
                var total_price = data.total_price;
                var total_ppn = data.total_ppn;
                var total_bhp = data.total_bhp;
                var total_uso = data.total_uso;
                var total_member = data.total_member;
                var status = data.status;
                var notes = data.notes;
                var paid_via = data.paid_via;
                var month = data.month;
                var year = data.year;

                  // Tampilkan swal input untuk metode pembayaran
                  Swal.fire({
                      title: "Konfirmasi Pembayaran",
                      icon: 'warning',
                      input: "select",
                      inputOptions: {
                          'Cash': 'Cash',
                          'Transfer': 'Transfer',
                      },
                      inputPlaceholder: 'Metode Pembayaran',
                      text: "Setoran Periode " + month + " " + year,
                      showCancelButton: true,
                      confirmButtonText: "Ya, Sudah Bayar",
                      cancelButtonText: "Batal",
                      reverseButtons: true,
                      customClass: {
                          input: 'form-select w-auto p-3 mx-10',
                      },
                      inputValidator: function(value) {
                          return new Promise(function(resolve) {
                              if (value !== '') {
                                  resolve();
                              } else {
                                  resolve('Harap pilih metode pembayaran');
                              }
                          });
                      }
                  }).then(function(result) {
                      if (result.isConfirmed) {
                          // Tampilkan swal loading sederhana tanpa timer
                          Swal.fire({
                              title: "Pembayaran Storan",
                              icon: "info",
                              text: "Setoran sedang dibayar. Harap tunggu...",
                              showConfirmButton: false,
                              allowOutsideClick: false,
                              didOpen: () => {
                                  Swal.showLoading();
                              }
                          });
                          if(result.value == 'Transfer'){
                            console.log("ini transfer");
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            });
                            $.ajax({
                                type: "POST",
                                data: {
                                    'setoran_id': setoran_id,
                                },
                                url: baseurl + '/duitku/create_setoran',
                                dataType: "json",
                                cache: false,
                                success: function(result) {
                                    checkout.process(result.reference, {
                                        successEvent: function(result) {
                                            location.reload();
                                        },
                                        pendingEvent: function(result) {
                                            location.reload();
                                        },
                                        errorEvent: function(result) {
                                            // Add Your Action
                                            console.log('error');
                                            console.log(result);
                                            alert('Payment Error');
                                        },
                                        closeEvent: function(result) {
                                            location.reload();
                                        }
                                    });
                                }
                            });
                            return;
                          }
                          if(result.value == 'Cash'){
                            var dataku = {
                                'setoran_id': setoran_id,
                                'paid_via': result.value,
                            };
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                        'content')
                                }
                            });

                            $.ajax({
                              url: baseurl + `/admin/billing/member_dinetkan/update_mapping_service/`,
                                type: "PUT",
                                cache: false,
                                data: dataku,
                                dataType: "json",
                                success: function(data) {
                                    if (data.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success',
                                            text: data.message,
                                            showConfirmButton: false,
                                            timer: 1500
                                        });
                                        setTimeout(function() {
                                            table.ajax.reload();
                                        }, 1500);
                                    }
                                },
                                error: function(err) {
                                    $("#message").html("Some Error Occurred!");
                                }
                            });
                          }
                      }
                  });
              }
          });
      });
        // $('#kuponTable').DataTable({
        //     dom: 'Bfrtip',
        //     buttons: [
        //         'excel',
        //         {
        //             text: 'Export PDF',
        //             action: function () {
        //                 const { jsPDF } = window.jspdf;
        //                 const doc = new jsPDF('p', 'pt', 'A4');
        //                 doc.text('Data Report Service', 40, 30);

        //                 doc.autoTable({
        //                     html: '#kuponTable',
        //                     startY: 50,
        //                     theme: 'grid',
        //                     styles: {
        //                         fontSize: 10,
        //                         cellPadding: 5,
        //                         valign: 'middle',
        //                         halign: 'center',
        //                     },
        //                     headStyles: {
        //                         fillColor: [240, 240, 240],
        //                         textColor: [0, 0, 0],
        //                     }
        //                 });

        //                 doc.save('Laporan_Kupon.pdf');
        //             }
        //         }
        //     ]
        // });


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
