@extends('backend.layouts.app_new')
@section('title', 'Order')

@section('css')
  <!-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/animate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/datatables.css') }}"> -->
  <!-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/select2.css') }}"> -->
@endsection

@section('main')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6 ps-0">
          <h3>Edit Order</h3>
        </div>
      </div>
    </div>
  </div>

  <div class="container-fluid user-management-page">
    <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-body">
          <form id="adminForm" method="POST">
            @csrf
            <input type="hidden" id="adminMethod" name="_method" value="POST">
            <input type="hidden" id="id_mapping" name="id_mapping" value="{{$mapping->id}}">
            <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="dinetkan_user_id" class="form-label">Mitra</label>
                  <div class="form-group mb-3" style="display:grid">
                    <select class="form-control" id="dinetkan_user_id" name="dinetkan_user_id" style="width:100%" required>
                      @foreach ($resellers as $row)
                        <option value="{{ $row->dinetkan_user_id }}" {{ $row->dinetkan_user_id == $mapping->dinetkan_user_id ? 'selected' : '' }} >{{ $row->first_name }} {{ $row->last_name }} - {{ isset($row->company) ? $row->company->name : '' }} - {{ $row->username }} </option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="id_mitra" class="form-label">Partnership</label>
                  <select class="form-control" id="id_mitra" name="id_mitra" style="width:100%" required>
                  <option value="">-- Partnership --</option>
                    @foreach ($mitras as $row)
                      <option value="{{ $row->id_mitra }}" {{ $row->id_mitra == $mapping->id_mitra ? 'selected' : '' }}>{{ $row->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="category_id" class="form-label">Category</label>
                  <div class="form-group mb-3" style="display:grid">
                    <select class="form-control" id="category_id" name="category_id" style="width:100%" required>
                      <option value="">-- Pilih Kategori --</option>
                      @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ $category->id == $mapping->category_id ? 'selected' : '' }}>{{ $category->name }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="license_dinetkan_id" class="form-label">Service</label>
                  <select class="form-control" id="license_dinetkan_id" name="license_dinetkan_id" required>
                    <option value="">Select Service</option>
                    @foreach ($licenses as $license)
                      <option value="{{ $license->id }}" {{ $license->id == $mapping->license_id ? 'selected' : '' }}>{{ $license->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-4 mb-3">
                  <label for="statuses" class="form-label">Statuses</label>
                  <select class="form-control" id="statuses" name="statuses" required>
                    @foreach ($statuses as $key=>$val)
                      <option value="{{ $key }}" <?php echo $key == $progress ? 'selected' : '' ;?>>{{ $val }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-12 mb-3">
                  <label for="notes" class="form-label">Notes</label>
                  <textarea name="notes" id="notes" class="form-control" rows="5" required>{{$mapping->notes}}</textarea>
                </div>
                <div style="margin-top:30px"></div>
                <div class="col-md-12" id="div_pembayaran">
                  <div class="d-flex justify-content-center">
                    <h6>Detail Pembayaran</h6>
                  </div>
                  <div class="row">
                    <div class="col-md-4 col-sm-12">
                      <label for="active_date" class="form-label">
                        Active Date
                        <span class="material-symbols-outlined" data-bs-toggle="tooltip" data-bs-placement="top" title="Tanggal pelanggan daftar atau pertama kali terhubung,
                                form ini hanya sebagai data, tapi bisa juga dijadikan
                                patokan Prorate/Prorate.">
                        help
                        </span>
                      </label>
                      <input type="date" class="form-control" id="active_date" name="active_date">
                    </div>

                    <div class="col-md-4 col-sm-12">
                      <label for="remainder_day" class="form-label">
                        Reminder Pembayran 
                        <span class="material-symbols-outlined" data-bs-toggle="tooltip" data-bs-placement="top" title="Ingatkan pelanggan tagihan pembayaran N hari sebelum tanggal ISOLIR, 
                                  jika kosong maka nilai default adalah 7. Contoh, tanggal ISOLIR : 2025-01-10 12:00, 
                                  maka pada tanggal 2025-01-03 12:00 sudah masuk masa penagihan, data akan berada pada Billing->Unpaid">
                        help
                        </span>
                      </label>
                      <input type="number" class="form-control" id="remainder_day" name="remainder_day" value="5">
                    </div>

                    <div class="col-md-4 col-sm-12">
                      <label for="payment_date" class="form-label">
                        Tgl Pembayaran / Tgl ISOLIR
                        <span class="material-symbols-outlined" data-bs-toggle="tooltip" data-bs-placement="top" title="Tanggal pelanggan pertama kali masuk masa ISOLIR, 
                                      jika kamu mau jadikan pelanggan Bayar Dulu Baru Pakai(PRABAYAR), maka buat tanggal ISOLIR dekat dengan tanggal hari ini, 
                                      jika kamu mau jadikan pelanggan Pakai Dulu Baru Pakai(PASCABAYAR) maka buat tanggal ISOLIR berjarak -+1 Bulan dari hari ini">
                        help
                        </span>
                      </label>
                      <input type="date" class="form-control" id="payment_date" name="payment_date">
                    </div>
                    <div class="col-lg-4 col-sm-12">
                      <label class="form-label" for="payment_siklus">Siklus Pembayaran 
                        <span class="material-symbols-outlined" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Siklus Tetap(Tgl Pembayaran) : Tanggal pembayaran setiap bulannya akan sama dengan tanggal ISOLIR yang dipilih di form ini. 
                                      Siklus Profile : Tanggal pembayaran akan menyesuaikan dengan tanggal terakhir perpanjang, setiap perpanjang akan otomatis +30 Hari, 
                                      artinya bisa saja tanggal pembayaran bulan berikutnya berubah(Tidak cocok untuk Kurangi tagihan setiap keterlambatan). 
                                      Siklus Bulanan : Tanggal pebayaran akan dipaksa sesuai tanggal yang diseting setiap bulannya">
                        help
                        </span>
                      </label>
                      <select class="form-control" id="payment_siklus" required="" name="payment_siklus">
                          <option value="1" selected>Siklus Tetap (Tgl Pembayaran)</option>
                          <option value="3">Siklus Profile</option>
                          <option value="2">Siklus Bulanan. Setiap Tanggal {{$settings->siklus_pembayaran}}</option>
                      </select>
                    </div>

                    <div class="col-lg-4 col-sm-12">
                      <label class="form-label" for="payment_method">METHOD 
                        <span class="material-symbols-outlined" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Method ini hanya sebagai STATUS DATA, 
                                tidak mempengaruhi tagihan/ total pembayaran. 
                                PRABAYAR/PASCABAYAR hanya dipengaruhi oleh Tgl Pembayaran / Tgl ISOLIR">
                          help
                        </span>
                      </label>
                      <select class="form-control" id="payment_method" required="" name="payment_method">
                      <option value="prabayar">PRABAYAR/PREPAID</option>
                        <option value="pascabayar">PASCABAYAR/POSTPAID</option>
                      </select>
                    </div>

                    <div class="col-lg-4 col-sm-12">
                      <label class="form-label" for="prorata">Prorate/Prorata 
                        <span class="material-symbols-outlined" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Jika YES, prorata hanya akan dihitung pertama sekali saja. 
                                    Perhitungan Prorate/Prorata dihitung dari berapa jarak tanggal antara Tanggal Aktif dan Tgl Pembayaran / Tgl ISOLIR. 
                                    Kotak merah adalah hitungan hari yang tidak terpakai, kotak hijau adalah hitungan hari yang terpakai">
                        help
                        </span>
                      </label>
                      <select class="form-control" id="prorata" required="" name="prorata">
                          <option value="off">NO</option>
                          <option value="on">YES</option>
                      </select>
                    </div>
                  </div>
                </div>
                <!-- <div class="col-md-6" id="div_next_due">
                  <label for="next_due" class="form-label">Next Due</label>
                  <input type="date" class="form-control" id="next_due" name="next_due">
                </div>
                <div class="col-md-6" id="div_is_otc">
                  <label for="is_otc" class="form-label">is OTC ?</label>
                    <select class="form-control" id="is_otc" name="is_otc">
                      <option value="">Select OTC</option>
                      <option value="1">YES</option>
                      <option value="0">NO</option>
                    </select>
                </div> -->

                <div style="margin-top:30px"></div>
                <div id="div_adons">
                  <div class="row">
                    <div class="d-flex justify-content-end mb-3">
                      <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addItemModal" type="button">Ad Ons</button>
                    </div>

                    <table class="table table-bordered text-center align-middle">
                      <thead class="table-light">
                        <tr>
                          <th>Deskripsi</th>
                          <th>PPN</th>
                          <th>Tagihan Bulanan</th>
                          <th>QTY</th>
                          <th>Harga</th>
                          <th>Total PPN</th>
                          <th>Total</th>
                          <th>Aksi</th>
                        </tr>
                      </thead>
                      <tbody id="invoiceTableBody">
                        <!-- Baris item akan ditambahkan di sini -->
                      </tbody>
                    </table>
                  </div>
                </div>

                <div class="col-md-12" id="div_total">
                  <div class="row">
                    <div class="col-md-6"></div>
                    <div class="row col-md-6">
                        <div class="col-md-6"><span style="font-weight:bold">Paket</span></div>
                        <div class="col-md-6" style="text-align: right;">
                          <span style="font-weight:bold" id="real_paket"></span>
                        </div>
                        <div class="col-md-6"><span style="font-weight:bold">Total Paket</span></div>
                        <div class="col-md-6" style="text-align: right;">
                          <span style="font-weight:bold" id="total_paket"></span>
                        </div>
                      
                        <div class="col-md-6"><span style="font-weight:bold">PPN Paket</span></div>
                        <div class="col-md-6" style="text-align: right;">
                          <span style="font-weight:bold" id="ppn_paket"></span>
                        </div>
                      
                        <div class="col-md-6"><span style="font-weight:bold">Total Adons</span></div>
                        <div class="col-md-6" style="text-align: right;">
                          <span style="font-weight:bold" id="total_adons"></span>
                        </div>
                      
                        <div class="col-md-6"><span style="font-weight:bold">Total</span></div>
                        <div class="col-md-6" style="text-align: right;">
                          <span style="font-weight:bold" id="grand_total"></span>
                        </div>
                        <hr>
                        <div class="col-md-6"><span style="font-weight:bold">Total Tagihan Bulanan</span></div>
                        <div class="col-md-6" style="text-align: right;">
                          <span style="font-weight:bold" id="grand_total_monthly"></span>
                        </div>
                        <p>Total Pembayaran ini ada total pembyaran setiap bulan</p>
                      </div>
                    </div>
                  </div>
                </div>
            </div>
            <div style="margin-top:30px"></div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary" id="submitBtn">Save</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    </div>
  </div>

  

  <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="addItemForm">
          <div class="modal-header">
            <h5 class="modal-title" id="addItemModalLabel">Tambah Item</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="mb-3">
                <label>Deskripsi</label>
                <input type="text" class="form-control" id="desc" required>
              </div>
              <div class="col-md-6">
                <label>PPN</label>
                <input type="number" class="form-control" id="ppn" value="0" required>
              </div>
              <div class="col-md-6">
                <label>Tagihan Bulanan</label>
                <select class="form-control" id="monthly">
                  <option value="Yes">Yes</option>
                  <option value="No" selected>No</option>
                </select>
              </div>
              <div class="col-md-6">
                <label>QTY</label>
                <input type="number" class="form-control" id="qty" value="1" required>
              </div>
              <div class="col-md-6">
                <label>Harga</label>
                <input type="number" class="form-control" id="price" required>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="setdata()">Save</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </form>
      </div>
    </div>
  </div>

@endsection

@push('scripts')
  <!-- <script src="{{ asset('assets/old/js/datatable/datatables/datatable.custom.js') }}"></script> -->
  
  <script>
    
  const baseurl = "{{ url('/') }}"
  function show_loading(){
    Swal.fire({
        icon: 'info',
        title: 'Processing',
        text: 'Processing data',
        showConfirmButton: false,
        // timer: 1500
    });
  }
    $(document).ready(function() {
      const form = $('#adminForm');
      // Form Submission
      form.on('submit', function(e) {
      Swal.fire({
          icon: 'info',
          title: 'Processing',
          text: 'Processing data',
          showConfirmButton: false,
          // timer: 1500
      });
        e.preventDefault();

        $.ajax({
          url: baseurl + '/dinetkan/invoice_dinetkan/create_new',
          method: 'POST',
          data: form.serialize(),
          success: function(response) {
            swal.close();
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: `${response.message}`,
                    showConfirmButton: false,
                    timer: 1500
                });
                window.location = baseurl + '/dinetkan/invoice_dinetkan/order';
          },
          error: function(xhr) {
            swal.close();
            const errors = xhr.responseJSON.errors;
            let message = '';

            for (const key in errors) {
              message += errors[key] + '\n';
            }

            // toastr.error(message);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: `${response.message}`,
                    showConfirmButton: false,
                    timer: 1500
                });
          }
        });
      });
    })

      function setdata(){
        const desc = document.getElementById("desc").value;
        const ppn = document.getElementById("ppn").value;
        const monthly = document.getElementById("monthly").value;
        const qty = parseInt(document.getElementById("qty").value);
        const price = parseInt(document.getElementById("price").value);
        let totalppn = 0; // (ppn * qty * price / 100);
        if(ppn > 0){
          totalppn = (ppn * qty * price / 100);
        }
        const total = (qty * price) + totalppn;

        const row = `
          <tr>
            <td><input type="text" class="form-control form-control-sm text-center" value="${desc}" name="desc[]"></td>
            <td><input type="number" class="form-control form-control-sm text-center ppn" value="${ppn}" name="ppn[]" onchange="updateTotal(this)"></td>
            <td>
              <select class="form-control form-control-sm text-center" name="monthly[]" onchange="updateTotal(this)">
                <option value="Yes" ${monthly === 'Yes' ? 'selected' : ''}>Yes</option>
                <option value="No" ${monthly === 'No' ? 'selected' : ''}>No</option>
              </select>
            </td>
            <td><input type="number" class="form-control form-control-sm text-center qty" value="${qty}" name="qty[]" onchange="updateTotal(this)"></td>
            <td><input type="number" class="form-control form-control-sm text-center price" value="${price}" name="price[]" onchange="updateTotal(this)"></td>
            <td class="totalppn text-end">${totalppn}</td>
            <td class="total text-end">${total}</td>
            <td><button class="btn btn-sm btn-danger" onclick="removeRow(this)">🗑️</button></td>
          </tr>
        `;

        document.getElementById("invoiceTableBody").insertAdjacentHTML("beforeend", row);
        // Reset the form on modal hide
        const modalElement = document.getElementById('addItemModal'); // the modal container
        modalElement.addEventListener('hidden.bs.modal', function () {
          addItemForm.reset();
        });
        bootstrap.Modal.getInstance(document.getElementById("addItemModal")).hide();
        count_grand_total();
      }
    

    function updateTotal(el) {
      const row = el.closest('tr');
      const qty = parseInt(row.querySelector('.qty').value) || 0;
      const price = parseInt(row.querySelector('.price').value) || 0;
      const ppn = parseInt(row.querySelector('.ppn').value) || 0;
      let totalppn = 0;
      if(ppn > 0){
        totalppn = ppn * qty * price / 100;
      }
      row.querySelector('.totalppn').innerText = totalppn ;
      row.querySelector('.total').innerText = (qty * price) + totalppn ;

      count_grand_total();
    }

    function removeRow(btn) {
      btn.closest('tr').remove();
      count_grand_total();
    }
  </script>
  <script>
    $(document).ready(function() {
        // Ambil elemen input
      const dateInput = document.getElementById('active_date');
      // Format tanggal hari ini dalam bentuk YYYY-MM-DD
      const today = new Date().toISOString().split('T')[0];
      // Set nilai default ke hari ini
      dateInput.value = today;

      // Ambil elemen input
      
      const todayx = new Date();
      const nextMonth = new Date(todayx);

      // Tambahkan 1 bulan
      nextMonth.setMonth(todayx.getMonth() + 1);

      // Format ke YYYY-MM-DD
      const formatted = nextMonth.toISOString().split('T')[0];
      const dateInputPayment = document.getElementById('payment_date');
      // Set nilai default ke hari ini
      dateInputPayment.value = formatted;


      $('#div_pembayaran').hide();
      $('#div_adons').hide();
      $('#div_total').hide();

      
      $('#statuses').select2({
        allowClear: true,
      });
      $('#license_dinetkan_id').select2({
        allowClear: true,
      });
      $('#id_mitra').select2({
        allowClear: true,
      });
      $('#dinetkan_user_id').select2({
        allowClear: true,
      });
      $('#category_id').select2({
          placeholder: '-- Pilih Kategori --',
          allowClear: true,
      });
    });


    const paymentInput = document.getElementById('payment_date');
    paymentInput.addEventListener('change', function () {
      const todayx = new Date();
      const nextM = new Date(todayx);
      nextM.setDate({{$settings->siklus_pembayaran}});
      nextM.setMonth(todayx.getMonth() + 1);
      const tdate = nextM.toISOString().split('T')[0];
      if (this.value !== tdate) {
        // set to 
        // <option value="1">Siklus Tetap (Tgl Pembayaran)</option>
        document.getElementById('payment_siklus').value = 1;
      }
    });

    $('#payment_siklus').on('change', function() {
      var siklus = $(this).val();
      console.log(siklus);
      // <option value="1">Siklus Tetap (Tgl Pembayaran)</option>
      // <option value="3">Siklus Profile</option>
      // <option value="2">Siklus Bulanan. Setiap Tanggal 25</option>
      if(siklus == 1){

      }
      if(siklus == 2){
        const todayx = new Date();
        const nextMonth = new Date(todayx);

        nextMonth.setDate({{$settings->siklus_pembayaran}});
        // if({{$settings->siklus_pembayaran}} > todayx.getDate()  ){
        //   nextMonth.setMonth(todayx.getMonth() + 1);
        // }
        const formatted = nextMonth.toISOString().split('T')[0];
        const dateInputPayment = document.getElementById('payment_date');
        dateInputPayment.value = formatted;
      }
      if(siklus == 3){
        
      }
    });

    $('#category_id').on('change', function() {
    $('#license_dinetkan_id').empty();
    var category_id = $(this).val();
    if (category_id) {
        $.ajax({
        url:  baseurl + `/dinetkan/license_dinetkan/by_category/`+category_id+`/all`,
        type: 'GET',
        dataType: "json",
        success: function(data) {
              if(data){
                let formattedData = data.map((item) => ({
                id: item.id,
                text: item.name
                })).sort((a, b) => a.text.localeCompare(b.text));

                // Tambahkan opsi default "Silahkan Pilih"
                formattedData.unshift({
                    id: '',
                    text: 'Silahkan Pilih',
                    disabled: false // Jangan disabled agar bisa dipilih
                });

                $('#license_dinetkan_id').select2({
                    data: formattedData,
                    allowClear: true,
                    // dropdownParent: $("#adminModal .modal-content"),
                });
            }else{
              $('#license_dinetkan_id').empty();
            }
          }
        });
    } else {
        $('#license_dinetkan_id').empty();
    }
    });

    $('#license_dinetkan_id').on('change', function() {
      var license_dinetkan_id = $(this).val();
      if (license_dinetkan_id) {
        get_license();
      }
    });
    $('#active_date').on('change', function() {
        const value = $(this).val();
        console.log("tanggal aktif => " + value);
        get_license();
    });
    $('#payment_date').on('change', function() {
        const value = $(this).val();
        console.log("tanggal bayar => " + value);
        get_license();
    });
    $('#payment_method').on('change', function() {
        const value = $(this).val();
        get_license();
    });
    $('#prorata').on('change', function() {
        const value = $(this).val();
        get_license();
    });

    function get_license(){
      const active_date = document.getElementById("active_date").value;
      const payment_date = document.getElementById("payment_date").value;
      const payment_method = document.getElementById("payment_method").value;
      const prorata = document.getElementById("prorata").value;
      
      var license_dinetkan_id = $('#license_dinetkan_id').val();
      $.ajax({
          url:  baseurl + `/dinetkan/license_dinetkan/by_license/`+license_dinetkan_id,
          type: 'GET',
          data: {
            'active_date':active_date,
            'payment_date':payment_date,
            'payment_method':payment_method,
            'prorata': prorata,
            'dinetkan_user_id': $('#dinetkan_user_id').val()
          },
          dataType: "json",
          success: function(data) {
            if(data.success == true){
              $('#real_paket').text(data.harga_asli);
              $('#total_paket').text(data.harga_prorate);
              $('#ppn_paket').text(data.ppn);
              // $('#total_adons').text(data.price);
              // $('#grand_total').text(data.price);
              count_grand_total();
            }
            if(data.success == false){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: `${data.message}`,
                    showConfirmButton: false,
                    timer: 2500
                });
            }
          }
          });
      
    }
    function count_grand_total(){
      let total_paket = parseInt(document.getElementById("total_paket").textContent || 0);
      let real_paket = parseInt(document.getElementById("real_paket").textContent || 0);
      
      let ppn_paket = parseInt(document.getElementById("ppn_paket").textContent || 0);
      let totalAll = 0;
      let totalPPNAll = 0;
      let grand_total_monthly = 0;
      let _total_monthly = 0;

      document.querySelectorAll('#invoiceTableBody tr').forEach(row => {
        const total = parseInt(row.querySelector('.total')?.textContent || 0);
        // const totalPPN = parseInt(row.querySelector('.totalppn')?.textContent || 0);
        totalAll += total;
        // totalPPNAll += totalPPN;
        
        // Ambil value dari select dalam baris ini
        const monthlyValue = row.querySelector('select[name="monthly[]"]')?.value;
        if(monthlyValue == 'Yes'){
          _total_monthly = _total_monthly + total;
        }

      });

      let _ppn = 0;
      let _ppn_real = 0;
      if(ppn_paket > 0){
        _ppn = total_paket * ppn_paket / 100;
        _ppn_real = real_paket * ppn_paket / 100;
      }
      const _grand_total = total_paket + _ppn + totalAll;
      grand_total_monthly = _total_monthly + real_paket + _ppn_real;
      $('#total_adons').text(totalAll.toLocaleString('id-ID'));
      $('#grand_total').text(parseInt(_grand_total).toLocaleString('id-ID'));
      $('#grand_total_monthly').text(grand_total_monthly.toLocaleString('id-ID'));
    }

    
    $('#statuses').on('change', function() {
      $('#next_due').val("");
      var id = $(this).val();
      if(id == 1){
        $('#div_pembayaran').show();
        $('#div_adons').show();
        $('#div_total').show();
        get_license();
      } else{
        $('#div_pembayaran').hide();
        $('#div_adons').hide();
        $('#div_total').hide();
      }
    });
  </script>
@endpush
