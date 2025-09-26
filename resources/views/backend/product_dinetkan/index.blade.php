@extends('backend.layouts.app_new')

@section('title', 'Profile PPPOE')

@section('css')
  <!-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/animate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/datatables.css') }}">

  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/animate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/datatables.css') }}"> -->
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/select2.css') }}">
@endsection

@section('main')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6 ps-0">
          <h3>Profile PPPOE</h3>
        </div>
        <div class="col-sm-6 pe-0">
          <ol class="breadcrumb">
            <li class="breadcrumb-item">
              <a href="/">
                <!-- <svg class="stroke-icon">
                  <use href="{{ asset('assets/old/svg/icon-sprite.svg#stroke-home') }}"></use>
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
      <div class="col-xxl-4 box-col-4">
        <div class="card">
          <div class="card-body">
            <!-- <div class="bg-light-primary b-r-15">
              <div class="upcoming-box d-flex align-items-center justify-content-between px-4">
                <div>
                  <div class="upcoming-icon bg-primary">
                  </div>
                </div>
                <button type="button" class="btn btn-primary" id="openCreateModal">Add Product</button>
              </div>
            </div> -->
            <button type="button" class="btn btn-light-primary" style="width:100%" id="openCreateModal">Add Profile</button>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body">
        <div class="table-responsive custom-scrollbar">
          <table id="kuponTable" class="table-hover display nowrap clickable table" width="100%">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Kapasitas</th>
                <th>Harga</th>
                <!-- <th>PPN</th>
                <th>BHP</th>
                <th>USO</th> -->
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($product as $row)
                <tr>
                  <td>{{ $row->id}}</td>
                  <td>{{ $row->product_name}}</td>
                  <td>{{ $row->kapasitas}}</td>
                  <td>Rp. {{ number_format($row->price, 0, '.', '.') }}</td>
                  <!-- <td>{{ $row->ppn}}</td>
                  <td>{{ $row->bhp}}</td>
                  <td>{{ $row->uso}}</td> -->
                  <td>
                    <div class="action-div">
                        <a href="javascript:void(0)" class="edit-icon edit btn btn-light-primary" data-id="{{ $row->id}}">
                            edit
                        </a>
                        <!-- <a href="javascript:void(0)" class="edit-icon delete badge badge-danger" data-id="{{ $row->id}}">
                            <i class="fas fa-trash-alt"></i>
                        </a> -->
                    </div>
                  </td>
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

  <!-- Unified Modal -->
  <div class="modal fade" id="couponModal" tabindex="-1" aria-labelledby="couponModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="couponModalLabel">Manage Coupon</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="couponForm" method="POST">
          @csrf
          <input type="hidden" id="couponMethod" name="_method" value="POST">
          <div class="modal-body">
            <input type="hidden" id="id" name="id">
            <div class="mb-3">
              <label for="product_name" class="form-label">Name</label>
              <input type="text" class="form-control" id="product_name" name="product_name" required>
            </div> 
            <div class="mb-3">
              <label for="kapasitas" class="form-label">Kapasitas</label>
              <input type="text" class="form-control" id="kapasitas" name="kapasitas" required>
            </div> 
            <div class="mb-3" x-data="{ price: '' }">
                <label for="price" class="form-label">Price</label>
                <input type="text" class="form-control" id="price" name="price" x-model="price"
                x-on:input="price = price.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')" required>
            </div>
            <div class="mb-3">
              <!-- <label for="ppn" class="form-label">PPN %</label> -->
              <input type="hidden" class="form-control" id="ppn" name="ppn" value="{{$ppn}}" readonly>
            </div>
            <div class="mb-3">
              <!-- <label for="bhp" class="form-label">BHP %</label> -->
              <input type="hidden" class="form-control" id="bhp" name="bhp" value="{{$bhp}}" readonly>
            </div>
            <div class="mb-3">
              <!-- <label for="uso" class="form-label">USO %</label> -->
              <input type="hidden" class="form-control" id="uso" name="uso" value="{{$uso}}" readonly>
            </div> 
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" id="submitBtn">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>


@endsection

@push('scripts')
  <!-- <script src="{{ asset('assets/old/radiusqu/dist/js/moment.js') }}"></script>
  <script src="{{ asset('assets/old/js/datatable/datatables/jquery.dataTables.min.js') }}"></script> -->
  <!-- <script src="{{ asset('assets/old/js/datatable/datatables/datatable.custom.js') }}"></script> -->

  <script>
    $(document).ready(function() {
      $('#kuponTable').DataTable({
          columnDefs: [
              { targets: '_all', className: 'dt-center align-center' }
          ]
      });

      const modal = $('#couponModal');
      const form = $('#couponForm');
      const methodField = $('#couponMethod');
      const submitBtn = $('#submitBtn');

      // Open Create Modal
      $('#openCreateModal').on('click', function() {
        form[0].reset();
        modal.find('.modal-title').text('Create New Profile');
        form.attr('action', '{{ route('admin.product_dinetkan.store') }}');
        methodField.val('POST');
        submitBtn.text('Create');
        modal.modal('show');
      });

      // Edit Button Click Handler
      $(document).on('click', '.edit-icon.edit', function() {
        const userId = $(this).data('id');

        $.ajax({
          url: `/admin/product_dinetkan/single/${userId}`,
          method: 'GET',
          success: function(data) {
            modal.find('.modal-title').text('Update Product');
            form.attr('action', `/admin/product_dinetkan/update/${userId}`);
            methodField.val('put');
            submitBtn.text('Update');
            if(data.price != null){
                data.price = data.price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            // Populate the form with data
            $('#id').val(data.id);
            $('#product_name').val(data.product_name);
            $('#price').val(data.price);
            $('#ppn').val(data.ppn);
            $('#bhp').val(data.bhp);
            $('#uso').val(data.uso);
            $('#kapasitas').val(data.kapasitas);

            modal.modal('show');
          },
          error: function(xhr) {
            alert('Error fetching coupon data: ' + xhr.responseJSON.message);
          }
        });
      });

      $(document).on('click', '.edit-icon.delete', function() {
        const userId = $(this).data('id');

        $.ajax({
          url: `/dinetkan/master_pop/get_single/${userId}`,
          method: 'GET',
          success: function(data) {
            modal.find('.modal-title').text('Delete POP');
            form.attr('action', `/dinetkan/master_pop/${userId}`);
            methodField.val('POST');
            submitBtn.text('Delete');

            // Populate the form with data
            $('#id').val(data.id);
            $('#name').val(data.name);
            $('#pic_name').val(data.pic_name);
            $('#pic_whatsapp').val(data.pic_whatsapp);
            $('#ip').val(data.ip);

            $('#name').prop('readonly', true);
            $('#pic_name').prop('readonly', true);
            $('#pic_whatsapp').prop('readonly', true);
            $('#ip').prop('readonly', true);

            modal.modal('show');
          },
          error: function(xhr) {
            alert('Error fetching coupon data: ' + xhr.responseJSON.message);
          }
        });
      });
      
    });

  function cek_radio(val){
    
    const type_nominal = document.getElementById("type_nominal");
    const type_percent = document.getElementById("type_percent");
    
    const nominal = document.getElementById("nominal");
    const percent = document.getElementById("percent");
    if(val == 'percent'){
      nominal.style.display = 'none';
      percent.style.display = 'block';
    }
    if(val == 'nominal'){
      nominal.style.display = 'block';
      percent.style.display = 'none';
    }
  }
  </script>
@endpush
