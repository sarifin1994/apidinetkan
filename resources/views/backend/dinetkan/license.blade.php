@extends('backend.layouts.app_new')

@section('title', 'License Management')

@section('css')
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/animate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/datatables.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/select2.css') }}">
@endsection

@section('main')
  <div class="container-fluid">
    <div class="page-title">
      <div class="row">
        <div class="col-sm-6 ps-0">
          <h3>License Management</h3>
        </div>
        <div class="col-sm-6 pe-0">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dinetkan.dashboard') }}">
                <svg class="stroke-icon">
                  <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                </svg></a></li>
            <li class="breadcrumb-item">Radius</li>
            <li class="breadcrumb-item active">License Management</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
  <!-- Container-fluid starts-->
  <div class="container-fluid">
    <div class="row">
      <div class="col-xxl-4 box-col-4">
        <div class="card">
          <div class="card-body">
            <div class="bg-light-primary b-r-15">
              <div class="upcoming-box d-flex align-items-center justify-content-between px-4">
                <div>
                  <div class="upcoming-icon bg-primary">
                    <svg class="stroke-icon">
                      <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-form') }}"></use>
                    </svg>
                  </div>
                  <p>{{ $licenseCount }} License</p>
                </div>

                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                  data-bs-target="#licenseCrudModal">Add License</button>
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
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="name" class="form-label">Name</label>
                  <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3" x-data="{ price: '' }">
                  <label for="price" class="form-label">Price</label>
                  <input type="text" class="form-control" id="price" name="price" x-model="price"
                    x-on:input="price = price.replace(/[^0-9]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')" required>
                </div>
                <div class="mb-3">
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
                </div>
                <div class="mb-3">
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="payment_gateway" name="payment_gateway">
                    <label class="form-check-label" for="payment_gateway">Allow Payment Gateway</label>
                    <small class="text-muted d-block">If checked, admin can use payment gateway for their payment
                      system.</small>
                  </div>
                </div>
                <div class="mb-3">
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="whatsapp" name="whatsapp">
                    <label class="form-check-label" for="whatsapp">Allow Whatsapp</label>
                    <small class="text-muted d-block">If checked, admin can use whatsapp for their payment
                      system.</small>
                  </div>
                </div>
                <div class="mb-3">
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="invoice_addon" name="invoice_addon">
                    <label class="form-check-label" for="invoice_addon">Allow Invoice Addon (Not Feature Yet)</label>
                    <small class="text-muted d-block">If checked, admin can use invoice addon for their payment
                      system.</small>
                  </div>
                </div>
                <div class="mb-3">
                  <label for="color" class="form-label">License Color</label>
                  <div class="d-flex align-items-center">
                    <input type="color" class="form-control form-control-color me-2" id="color" name="color"
                      value="#000000" required>
                    <div id="colorPreview"
                      style="width: 30px; height: 30px; border: 1px solid #ccc; background-color: #000000;"></div>
                  </div>
                  <small class="text-muted">Choose a color for the license badge.</small>
                </div>
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

@section('scripts')
  <script src="{{ asset('assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('assets/js/datatable/datatables/datatable.custom.js') }}"></script>
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
        $('#licenseCrudModalLabel').text('Create License');
        $('#submitBtn').text('Create');
        $('#licenseCrudForm').attr('action', '{{ route('dinetkan.license.store') }}');
      }

      // Create License
      $('#licenseCrudForm').on('submit', function(e) {
        e.preventDefault();

        let price = $('#price').val().replace(/\./g, '');
        $('#price').val(price);

        let oltEponValue = $('#olt_epon').is(':checked') ? 1 : 0;
        $('#olt_epon').val(oltEponValue);

        let oltGponValue = $('#olt_gpon').is(':checked') ? 1 : 0;
        $('#olt_gpon').val(oltGponValue);

        let paymentGatewayValue = $('#payment_gateway').is(':checked') ? 1 : 0;
        $('#payment_gateway').val(paymentGatewayValue);

        let whatsappValue = $('#whatsapp').is(':checked') ? 1 : 0;
        $('#whatsapp').val(whatsappValue);

        let invoiceAddonValue = $('#invoice_addon').is(':checked') ? 1 : 0;
        $('#invoice_addon').val(invoiceAddonValue);

        const data = {
          name: $('#name').val(),
          price: $('#price').val(),
          limit_nas: $('#limit_nas').val(),
          limit_pppoe: $('#limit_pppoe').val(),
          limit_hs: $('#limit_hs').val(),
          limit_user: $('#limit_user').val(),
          limit_vpn: $('#limit_vpn').val(),
          limit_vpn_remote: $('#limit_vpn_remote').val(),
          olt_epon_limit: $('#olt_epon_limit').val(),
          olt_gpon_limit: $('#olt_gpon_limit').val(),
          max_buy: $('#max_buy').val(),
          olt_epon: oltEponValue,
          olt_gpon: oltGponValue,
          olt_models: $('#olt_models').val(),
          payment_gateway: paymentGatewayValue,
          whatsapp: whatsappValue,
          invoice_addon: invoiceAddonValue,
          color: $('#color').val(),
        }

        $.ajax({
          url: $(this).attr('action'),
          method: $(this).find('#form_method').val(),
          data: data,
          success: function(response) {
            $('#licenseCrudModal').modal('hide');
            $('#license-table').DataTable().ajax.reload();
            toastr.success(
              $('#form_method').val() === 'POST' ?
              "License created successfully!" :
              "License updated successfully!"
            );
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
          url: `/dinetkan/license/${licenseId}`,
          method: 'GET',
          success: function(response) {
            response.price = response.price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');

            $('#licenseCrudModalLabel').text('Edit License');
            $('#submitBtn').text('Update');
            $('#form_method').val('PUT');
            $('#license_id').val(response.id);
            $('#name').val(response.name);
            $('#price').val(response.price);
            $('#limit_nas').val(response.limit_nas);
            $('#limit_pppoe').val(response.limit_pppoe);
            $('#limit_hs').val(response.limit_hs);
            $('#limit_user').val(response.limit_user);
            $('#limit_vpn').val(response.limit_vpn);
            $('#limit_vpn_remote').val(response.limit_vpn_remote);
            $('#olt_epon_limit').val(response.olt_epon_limit);
            $('#olt_gpon_limit').val(response.olt_gpon_limit);
            $('#max_buy').val(response.max_buy);
            $('#olt_epon').prop('checked', response.olt_epon);
            $('#olt_gpon').prop('checked', response.olt_gpon);
            $('#olt_models').val(response.olt_models || []).trigger('change');
            $('#payment_gateway').prop('checked', response.payment_gateway);
            $('#whatsapp').prop('checked', response.whatsapp);
            $('#invoice_addon').prop('checked', response.invoice_addon);
            $('input#color').val(response.color);
            $('#colorPreview').css('background-color', response.color);
            $('#licenseCrudForm').attr('action', `/dinetkan/license/${response.id}`);
            $('#licenseCrudModal').modal('show');
          },
          error: function(xhr) {
            alert('Error fetching license data: ' + xhr.responseJSON.message);
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
@endsection
