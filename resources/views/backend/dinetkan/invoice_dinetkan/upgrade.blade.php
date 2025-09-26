@extends('backend.layouts.app_new')

@section('title', 'Service Detail')

@section('css')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/animate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/datatables.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/select2.css') }}">

@endsection

@section('main')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6 ps-0">
          <h3>Service Upgrade {{$mapping->service_id}}</h3>
        </div>
        <div class="col-sm-6 pe-0">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="">
                <!-- <svg class="stroke-icon">
                  <use href="{{ asset('assets/old/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg> -->
              </a>
            </li>
            <!-- <li class="breadcrumb-item active">Mitra Detail</li> -->
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- container-fluid starts-->
  <div class="container-fluid user-management-page">
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body">
            <div class="modal-body"> 
                <form id="adminForm" method="POST">
                    @csrf
                    <input type="hidden" id="adminMethod" name="_method" value="POST">
                    <input type="hidden" id="service_id" name="service_id" value="{{ $mapping->service_id }}">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="dinetkan_user_id" class="form-label">Mitra</label>
                            <select class="form-control" id="dinetkan_user_id" name="dinetkan_user_id" required>
                                @foreach ($resellers as $row)
                                    <option value="{{ $row->dinetkan_user_id }}" 
                                        {{ $mapping->dinetkan_user_id == $row->dinetkan_user_id ? 'selected' : ''}} disabled>
                                        {{ $row->first_name }}
                                        {{ $row->last_name }} -
                                        {{ isset($row->company) ? $row->company->name : '' }} - {{ $row->username }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label">Category Active</label>
                            <select class="form-control">
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{$mapping->category_id == $category->id ? 'selected' : ''}} disabled>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="license_dinetkan_id" class="form-label">Service Active</label>
                            <select class="form-control" >
                                @foreach ($licenses as $license)
                                    <option value="{{ $license->id }}" {{$mapping->license_id == $license->id ? 'selected' : ''}} disabled=>{{ $license->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="due_date">Harga</label>
                            <input class="form-control" name="due_date" id="due_date" value="{{ number_format(floatval($curr_lic->price), 0, ',', '.') }}" disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="price">Duedate</label>
                            <input class="form-control" name="price" id="price" value="{{ \Carbon\Carbon::parse($mapping->due_date)->format('d/m/Y')}}" disabled>
                        </div>
                        <hr>
                        <h6>Service Baru</h6>
                        <div class="col-md-12 mb-3">
                            <label for="jenis" class="form-label">Jenis</label>
                            <select class="form-select" id="jenis" name="jenis" required>
                                <option value="1" selected>Tanpa Tambah Duedate</option>
                                <option value="2">Tambah Duedate</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label">Category</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="license_dinetkan_id" class="form-label">Service</label>
                            <select class="form-select" id="license_dinetkan_id" name="license_dinetkan_id" required>
                                <option value="">Select Service</option>
                                @foreach ($licenses as $license)
                                <option value="{{ $license->id }}">{{ $license->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="ppn">PPN</label>
                            <input type="number" class="form-control" name="ppn" id="ppn" value="11" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="upgrade_date">Tanggal update</label>
                            <input type="date" class="form-control" name="upgrade_date" id="upgrade_date" required>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <!-- <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button> -->
                            <button type="button" class="btn btn-sm btn-success" id="submitBtn">Cek Harga</button>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="used_days">Total Hari Terpakai</label>
                            <input type="text" class="form-control" name="used_days" id="used_days" readonly required>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="remaining_days">Total Hari Sisa</label>
                            <input type="text" class="form-control" name="remaining_days" id="remaining_days"  readonly required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="total_prorate_str">Total Pembayaran Sebelum PPN</label>
                            <input type="hidden" class="form-control" name="total_prorate" id="total_prorate">
                            <input type="text" class="form-control" name="total_prorate_str" id="total_prorate_str"  readonly required>
                        </div>

                        <!-- Tombol Simpan -->
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-sm btn-secondary" id="btn_close">Close</button>
                            <button type="submit" class="btn btn-sm btn-primary">Upgrade</button>
                        </div>
                    </div>
                </form>
            </div>
          </div>
        </div>
      </div>
    </div> 
  </div>
  <!-- container-fluid Ends-->

@endsection

@push('scripts')
  <script src="{{ asset('assets/old/radiusqu/dist/js/moment.js') }}"></script>
  <script src="{{ asset('assets/old/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
  <!-- <script src="{{ asset('assets/old/js/datatable/datatables/datatable.custom.js') }}"></script> -->
  <script type="text/javascript">
    $(document).ready(() => {
        const baseurl = "{{ url('/') }}";
        
        $('#submitBtn').on('click', function(e){
            Swal.fire({
                icon: 'info',
                title: 'Processing',
                text: 'Processing data',
                showConfirmButton: false,
                // timer: 1500
            });
            $.ajax({
            url: '{{ route('dinetkan.invoice_dinetkan.order.check_price') }}',
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                Swal.close();
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: `${response.message}`,
                    showConfirmButton: false,
                    timer: 1500
                });
                
                $('#remaining_days').val(response.data.remaining_days);
                $('#used_days').val(response.data.used_days);
                $('#total_prorate').val(response.data.total_prorate);
                $('#total_prorate_str').val(number_js(response.data.total_prorate));
                
            },
            error: function(xhr) {
                Swal.close();
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
        })

        $('#jenis').on('change', function(){
            $('#remaining_days').val(0);
            $('#used_days').val(0);
            $('#total_prorate').val(0);
            $('#total_prorate_str').val(number_js(0));
        });

        const form = $('#adminForm');
        form.attr('action', '{{ route('dinetkan.invoice_dinetkan.order.create_upgrade') }}');
        // Form Submission
        form.on('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                icon: 'info',
                title: 'Processing',
                text: 'Processing data',
                showConfirmButton: false,
                // timer: 1500
            });

            $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                Swal.close();
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
                Swal.close();
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
                        text: item.name + ' ' + number_js(item.price)
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

        
        $('#btn_close').on('click', function(e){
            window.location = baseurl + '/dinetkan/invoice_dinetkan/order';
        });
    });

    function number_js(angka){
        let format = new Intl.NumberFormat('id-ID').format(angka);
        return format;
    }
    </script>
  
  @endpush
