@extends('backend.layouts.app_new')

@section('title', 'Service Management')

@section('css')
  <!-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/animate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/datatables.css') }}"> -->
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/old/css/vendors/select2.css') }}">
@endsection

@section('main')
  <!-- Container-fluid starts-->
  <div class="container-fluid">
    <div class="container-fluid">
      <div class="page-title">
        <div class="row">
          <div class="col-sm-6 ps-0">
            <h3>Service Management</h3>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <div class="card overview-details-box b-s-3-primary ">
          <div class="card-body">
              <div class="row">
                  <div class="col-md-12">
                      <div class="d-flex align-items-center gap-1">
                          <span class="bg-light-primary h-60 w-120 d-flex-center flex-column rounded-3">
                              <span class="f-w-500"> Service</span>
                              <span>{{ $licenseCount }}</span>
                          </span>
                          <div class="flex-grow-1">
                          <button type="button" class="btn btn-light-success h-60" data-bs-toggle="modal"
                          data-bs-target="#licenseCrudModal"><i class="fa-solid fa-plus"></i> Add Service</button>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12">
        <div class="card">
          <div class="card-body">
            <div class="table-responsive custom-scrollbar" id="row_create">
              {!! $dataTable->table() !!}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Container-fluid Ends-->

  <!-- License CRUD Modal -->
  <div class="modal fade" id="licenseCrudModal" tabindex="-1" aria-labelledby="licenseCrudModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="licenseCrudModalLabel">Create/Edit License</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="licenseCrudForm" method="POST">
          @csrf
          <input type="hidden" id="form_method" name="_method" value="POST">
          <input type="hidden" id="license_id" name="id">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
                <div class="mb-3">
                  <label for="category_id" class="form-label">Category</label>
                  <select class="form-select" id="category_id" name="category_id" required>
                    <option value="">Select Category</option>
                    @foreach ($categories as $category)
                      <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="mb-3">
                  <label for="name" class="form-label">Name</label>
                  <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3" x-data="{ price: '' }">
                  <label for="price" class="form-label">Price MRC</label>
                  <input type="text" class="form-control" id="price" name="price" x-model="price"
                    x-on:input="price = price.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')" required>
                </div>
                <div class="mb-3">
                  <label for="ppn" class="form-label">PPN MRC</label>
                  <input type="number" class="form-control" id="ppn" name="ppn">
                </div>
                <!-- <div class="mb-3" x-data="{ price_otc: '' }">
                  <label for="price_otc" class="form-label">Price OTC</label>
                  <input type="text" class="form-control" id="price_otc" name="price_otc" x-model="price_otc"
                    x-on:input="price_otc = price_otc.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')" required>
                </div> -->
                <!-- <div class="mb-3">
                  <label for="ppn_otc" class="form-label">PPN OTC</label>
                  <input type="number" class="form-control" id="ppn_otc" name="ppn_otc">
                </div> -->
                <div class="mb-3">
                  <label for="komisi_mitra" class="form-label">Komisi Mitra</label>
                  <input type="number" class="form-control" id="komisi_mitra" name="komisi_mitra">
                </div>
                <div class="mb-3">
                  <label for="capacity" class="form-label">Capacity</label>
                  <input type="text" class="form-control" id="capacity" name="capacity" required>
                </div>
                <div class="mb-3">
                  <label for="descriptions" class="form-label">Desriptions</label>
                  <!-- <input type="text" class="form-control" id="name" name="name" required> -->
                  <textarea class="form-control" id="descriptions" name="descriptions" required></textarea>
                </div>
                <!-- <div class="mb-3">
                  <label for="type" class="form-label">Type</label>
                  <select class="form-select" id="type" name="type" required>
                    <option value="">Select Type</option>
                    <option value="otc">OTC</option>
                    <option value="mrc">MRC</option>
                  </select>
                </div> -->
                <!-- <div class="mb-3">
                  <label for="limit_nas" class="form-label">Limit NAS</label>
                  <input type="number" class="form-control" id="limit_nas" name="limit_nas" required>
                </div>
                <div class="mb-3">
                  <label for="limit_pppoe" class="form-label">Limit PPPoE</label>
                  <input type="number" class="form-control" id="limit_pppoe" name="limit_pppoe" required>
                </div>
                <div class="mb-3">
                  <label for="limit_hs" class="form-label">Limit Hotspot</label>
                  <input type="number" class="form-control" id="limit_hs" name="limit_hs" required>
                </div>
                <div class="mb-3">
                  <label for="limit_user" class="form-label">Limit Users</label>
                  <input type="number" class="form-control" id="limit_user" name="limit_user" required>
                </div>
                <div class="mb-3">
                  <label for="limit_vpn" class="form-label">Limit VPN</label>
                  <input type="number" class="form-control" id="limit_vpn" name="limit_vpn" required>
                </div>
                <div class="mb-3">
                  <label for="limit_vpn_remote" class="form-label">Limit VPN Remote</label>
                  <input type="number" class="form-control" id="limit_vpn_remote" name="limit_vpn_remote" required>
                </div>
                <div class="mb-3">
                  <label for="olt_epon_limit" class="form-label">Limit OLT EPON</label>
                  <input type="number" class="form-control" id="olt_epon_limit" name="olt_epon_limit" required>
                  <small class="text-muted">Enter 0 for unlimited EPON OLT.</small>
                </div>
                <div class="mb-3">
                  <label for="olt_gpon_limit" class="form-label">Limit OLT GPON</label>
                  <input type="number" class="form-control" id="olt_gpon_limit" name="olt_gpon_limit" required>
                  <small class="text-muted">Enter 0 for unlimited GPON OLT.</small>
                </div>
                <div class="mb-3">
                  <label for="max_buy" class="form-label">Limit Maximum Buy</label>
                  <input type="number" class="form-control" id="max_buy" name="max_buy" required>
                  <small class="text-muted">Specify how many times an admin can purchase this license. Enter 0 for
                    unlimited purchases.</small>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="olt_epon" name="olt_epon">
                    <label class="form-check-label" for="olt_epon">Allow OLT EPON</label>
                    <small class="text-muted d-block">If checked, admin can create OLT EPON.</small>
                  </div>
                </div>
                <div class="mb-3">
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="olt_gpon" name="olt_gpon">
                    <label class="form-check-label" for="olt_gpon">Allow OLT GPON</label>
                    <small class="text-muted d-block">If checked, admin can create OLT GPON.</small>
                  </div>
                </div>
                <div class="mb-3">
                  <label for="olt_models" class="form-label">OLT Models</label>
                  <select class="form-select" id="olt_models" name="olt_models[]" multiple>
                    @foreach ($oltModels as $value => $label)
                      <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                  </select>
                  <small class="text-muted d-block">Select the OLT models that are allowed to be created with this
                    license.</small>
                </div> -->
                <!-- <div class="mb-3">
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="payment_gateway" name="payment_gateway">
                    <label class="form-check-label" for="payment_gateway">Allow Payment Gateway</label>
                    <small class="text-muted d-block">If checked, admin can use payment gateway for their payment
                      system.</small>
                  </div>
                </div> -->
                <!-- <div class="mb-3">
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="whatsapp" name="whatsapp">
                    <label class="form-check-label" for="whatsapp">Allow Whatsapp</label>
                    <small class="text-muted d-block">If checked, admin can use whatsapp for their payment
                      system.</small>
                  </div>
                </div> -->
                <!-- <div class="mb-3">
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="invoice_addon" name="invoice_addon">
                    <label class="form-check-label" for="invoice_addon">Allow Invoice Addon (Not Feature Yet)</label>
                    <small class="text-muted d-block">If checked, admin can use invoice addon for their payment
                      system.</small>
                  </div>
                </div> -->
                <!-- <div class="mb-3">
                  <label for="color" class="form-label">Service Color</label>
                  <div class="d-flex align-items-center">
                    <input type="color" class="form-control form-control-color me-2" id="color" name="color"
                      value="#000000" required>
                    <div id="colorPreview"
                      style="width: 30px; height: 30px; border: 1px solid #ccc; background-color: #000000;"></div>
                  </div>
                  <small class="text-muted">Choose a color for the Service badge.</small>
                </div> -->
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" id="submitBtn">Create</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <!-- <script src="{{ asset('assets/old/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
  <!-- <script src="{{ asset('assets/old/js/datatable/datatables/datatable.custom.js') }}"></script> --> -->
  {!! $dataTable->scripts() !!}

  <script>
    $(document).ready(function() {
      $('#olt_models').select2({
        allowClear: true,
        dropdownParent: $('#licenseCrudModal'),
        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
        placeholder: $(this).data('placeholder'),
      });

      // Function to reset form
      function resetForm() {
        $('#licenseCrudForm')[0].reset();
        $('#form_method').val('POST');
        $('#license_id').val('');
        $('#licenseCrudModalLabel').text('Create Service');
        $('#submitBtn').text('Create');
        $('#licenseCrudForm').attr('action', '{{ route('dinetkan.license_dinetkan.store') }}');
      }

      // Create License
      $('#licenseCrudForm').on('submit', function(e) {
        e.preventDefault();

        let price = $('#price').val().replace(/\./g, '');
        $('#price').val(price);

        // let price_otc = $('#price_otc').val().replace(/\./g, '');
        // $('#price_otc').val(price_otc);

        let komisi_mitra = $('#komisi_mitra').val().replace(/\./g, '');
        $('#komisi_mitra').val(komisi_mitra);

        // let oltEponValue = $('#olt_epon').is(':checked') ? 1 : 0;
        // $('#olt_epon').val(oltEponValue);

        // let oltGponValue = $('#olt_gpon').is(':checked') ? 1 : 0;
        // $('#olt_gpon').val(oltGponValue);

        let paymentGatewayValue = $('#payment_gateway').is(':checked') ? 1 : 0;
        $('#payment_gateway').val(paymentGatewayValue);

        let whatsappValue = $('#whatsapp').is(':checked') ? 1 : 0;
        $('#whatsapp').val(whatsappValue);

        let invoiceAddonValue = $('#invoice_addon').is(':checked') ? 1 : 0;
        $('#invoice_addon').val(invoiceAddonValue);

        const data = {
          name: $('#name').val(),
          price: $('#price').val(),
          // price_otc: $('#price_otc').val(),
          // limit_nas: $('#limit_nas').val(),
          // limit_pppoe: $('#limit_pppoe').val(),
          // limit_hs: $('#limit_hs').val(),
          // limit_user: $('#limit_user').val(),
          // limit_vpn: $('#limit_vpn').val(),
          // limit_vpn_remote: $('#limit_vpn_remote').val(),
          // olt_epon_limit: $('#olt_epon_limit').val(),
          // olt_gpon_limit: $('#olt_gpon_limit').val(),
          // max_buy: $('#max_buy').val(),
          // olt_epon: oltEponValue,
          // olt_gpon: oltGponValue,
          // olt_models: $('#olt_models').val(),
          payment_gateway: paymentGatewayValue,
          whatsapp: whatsappValue,
          invoice_addon: invoiceAddonValue,
          color: $('#color').val(),
          capacity: $('#capacity').val(),
          descriptions: $('#descriptions').val(),
          category_id: $('#category_id').val(),
          // type: $('#type').val(),
          ppn: $('#ppn').val(),
          // ppn_otc: $('#ppn_otc').val()
          komisi_mitra: $('#komisi_mitra').val()
        }
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
          url: $(this).attr('action'),
          method: $(this).find('#form_method').val(),
          data: data,
          success: function(response) {
            $('#licenseCrudModal').modal('hide');
            $('#license-table').DataTable().ajax.reload();
            // toastr.success(
            //   $('#form_method').val() === 'POST' ?
            //   "License created successfully!" :
            //   "License updated successfully!"
            // );
            Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: $('#form_method').val() === 'POST' ? "License created successfully!" : "License updated successfully!",
                    showConfirmButton: false,
                    timer: 1500
                });
            resetForm();
          },
          error: function(xhr) {
            alert(
              $('#form_method').val() === 'POST' ?
              'Error creating license: ' + xhr.responseJSON.message :
              'Error updating license: ' + xhr.responseJSON.message
            );
          }
        });
      });

      // Edit License - Populate Modal
      $(document).on('click', '.edit-icon.edit', function() {
        let licenseId = $(this).data('id');

        $.ajax({
          url: `/dinetkan/license_dinetkan/${licenseId}`,
          method: 'GET',
          success: function(response) {
            if(response.price != null){
              response.price = response.price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }
            // if(response.price_otc != null){
            //   response.price_otc = response.price_otc.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            // }
            if(response.komisi_mitra != null){
              response.komisi_mitra = response.komisi_mitra.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            $('#licenseCrudModalLabel').text('Edit Service');
            $('#submitBtn').text('Update');
            $('#form_method').val('PUT');
            $('#license_id').val(response.id);
            $('#name').val(response.name);
            $('#price').val(response.price);
            // $('#price_otc').val(response.price_otc);
            // $('#limit_nas').val(response.limit_nas);
            // $('#limit_pppoe').val(response.limit_pppoe);
            // $('#limit_hs').val(response.limit_hs);
            // $('#limit_user').val(response.limit_user);
            // $('#limit_vpn').val(response.limit_vpn);
            // $('#limit_vpn_remote').val(response.limit_vpn_remote);
            // $('#olt_epon_limit').val(response.olt_epon_limit);
            // $('#olt_gpon_limit').val(response.olt_gpon_limit);
            // $('#max_buy').val(response.max_buy);
            // $('#olt_epon').prop('checked', response.olt_epon);
            // $('#olt_gpon').prop('checked', response.olt_gpon);
            // $('#olt_models').val(response.olt_models || []).trigger('change');
            $('#payment_gateway').prop('checked', response.payment_gateway);
            $('#whatsapp').prop('checked', response.whatsapp);
            $('#invoice_addon').prop('checked', response.invoice_addon);
            $('input#color').val(response.color);
            $('#capacity').val(response.capacity);
            $('#descriptions').val(response.descriptions);
            $('#colorPreview').css('background-color', response.color);
            $('#category_id').val(response.category_id);
            // $('#type').val(response.type);
            $('#ppn').val(response.ppn);
            // $('#ppn_otc').val(response.ppn_otc);
            $('#komisi_mitra').val(response.komisi_mitra);
            $('#licenseCrudForm').attr('action', `/dinetkan/license_dinetkan/${response.id}`);
            $('#licenseCrudModal').modal('show');
          },
          error: function(xhr) {
            alert('Error fetching Service data: ' + xhr.responseJSON.message);
          }
        });
      });

      // Reset form when modal is closed
      $('#licenseCrudModal').on('hidden.bs.modal', function() {
        resetForm();
      });

      $('input#color').on('input change', function() {
        $('#colorPreview').css('background-color', $(this).val());
      });
    });
  </script>
@endpush
