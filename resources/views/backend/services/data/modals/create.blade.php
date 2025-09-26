<!-- create Modal Show -->
<div class="modal fade" id="create" role="dialog" aria-labelledby="modal" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal">Create Service</h5>
        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <span class="h6">Data Secret</span>
        <hr />
        <div class="form-check radio radio-primary ps-0">
          <ul class="radio-wrapper">
            <li>
              <input class="form-check-input" id="type-dhcp" type="radio" name="type" value="dhcp">
              <label class="form-check-label" for="type-dhcp">
                <i class="fa fa-circle"></i>
                <span>DHCP</span>
              </label>
            </li>
            <li>
              <input class="form-check-input" id="type-pppoe" type="radio" name="type" value="pppoe">
              <label class="form-check-label" for="type-pppoe">
                <i class="fa fa-circle"></i>
                <span>PPPoE</span>
              </label>
            </li>
          </ul>
        </div>

        <div id="createDhcp" class="row" style="display:none">
          <div class="col-lg-6">
            <div class="form-group mb-3">
              <label for="mac_address" class="mb-1">MAC Address DHCP</label>
              <input type="text" class="form-control" id="mac_address" name="mac_address"
                placeholder="8b:fd:55:5a:0b:d4" autocomplete="off">
            </div>
          </div>
        </div>

        <div id="createPppoe" class="row" style="display:none">
          <div class="col-lg-6">
            <div class="form-group mb-3">
              <label for="username" class="mb-1">Username</label>
              <input type="text" class="form-control" id="username" name="username" placeholder="nama@perusahaan"
                autocomplete="off">
            </div>
          </div>
          <div class="col-lg-6">
            <div class="form-group mb-3">
              <label for="password" class="mb-1">Password</label>
              <input type="text" class="form-control" id="password" name="password" placeholder="01122024"
                autocomplete="off">
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-6">
            <div class="form-group mb-3">
              <label for="profile" class="mb-1">Assign Profile <small class="text-danger">*</small></label>
              <select class="form-select" id="profile" name="profile" autocomplete="off"
                data-placeholder="Pilih Profile" required>
                <option value="">Pilih Profile</option>
                @forelse ($profiles as $profile)
                  <option value="{{ $profile->id }}">{{ $profile->name }}</option>
                @empty
                @endforelse
              </select>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="form-group mb-3">
              <label for="member_id" class="mb-1">Member</label>
              <select class="form-select" id="member_id" name="member_id" autocomplete="off"
                data-placeholder="Pilih Member">
                <option value="">Pilih Member</option>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-6">
            <div class="form-group mb-3">
              <label for="ip_address" class="mb-1">IP Address</label>
              <input type="text" class="form-control" id="ip_address" name="ip_address" placeholder="103.15.95.8"
                autocomplete="off">
              <small class="text-muted">Kosongkan jika IP Address dinamis</small>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="form-group mb-3">
              <label for="nas" class="mb-1">NAS</label>
              <select class="form-select" id="nas" name="nas" autocomplete="off"
                data-placeholder="Pilih Nas">
                <option value="">all</option>
                @forelse ($nas as $nas)
                  <option value="{{ $nas->ip_router }}">{{ $nas->name }}</option>
                @empty
                @endforelse
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-6">
            <label for="kode_area" class="mb-1">Kode Area</label>
            <div class="form-group mb-3" style="display:grid">
              <select class="form-control" id="kode_area" name="kode_area" autocomplete="off"
                data-placeholder="Pilih Kode Area">
                <div class="row">
                  <option value=""></option>
                  @forelse ($areas as $area)
                    <option value="{{ $area->id }}">{{ $area->kode_area }}</option>
                  @empty
                  @endforelse
                </div>
              </select>

            </div>
          </div>
          <div class="col-lg-6">
            <label for="kode_odp" class="mb-1">Kode ODP</label>
            <div class="form-group mb-3" style="display:grid">
              <select class="form-control" id="kode_odp" name="kode_odp" autocomplete="off"
                data-placeholder="Pilih Kode ODP">
              </select>
            </div>
          </div>

        </div>
        <div class="row">
          <div class="col-lg-6">
            <div class="form-group mb-3">
              <label for="lock_mac" class="mb-1">Lock Mac</label>
              <select class="form-select" id="lock_mac" name="lock_mac" autocomplete="off">
                <option value="0">Disabled</option>
                <option value="1">Enabled</option>
              </select>
            </div>
          </div>
          <div class="col-lg-6" style="display:none">
            <div class="form-group mac mb-3" id="lockMac">
              <label for="lock_mac_address" class="mb-1">MAC Address</label>
              <input type="text" class="form-control" id="lock_mac_address" name="lock_mac_address"
                value="" placeholder="8b:fd:55:5a:0b:d4" autocomplete="off" required>
            </div>
          </div>
        </div>

        <div class="h6 col-auto mt-2">
          <label class="d-flex align-items-center gap-2">
            Data Pembayaran
            <input id="include_payment" class="checkbox_animated" type="checkbox" name="include_payment"
              value="0">
          </label>
        </div>
        <hr>

        <div id="show_payment" style="display:none">
          <div class="row">
            <div class="col-lg-6">
              <div class="form-group mb-3">
                <label for="payment_type" class="mb-1">Tipe Pembayaran</label>
                <select class="form-select" id="payment_type" name="payment_type" autocomplete="off"
                  data-placeholder="payment_type">
                  <option value="Prabayar">Prabayar</option>
                  <option value="Pascabayar">Pascabayar</option>
                </select>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="form-group mb-3" id="show_payment_status">
                <label for="payment_status" class="mb-1">Status Pembayaran</label>
                <select class="form-select" id="payment_status" name="payment_status" autocomplete="off"
                  data-placeholder="payment_status">
                  <option value="paid">Paid</option>
                  <option value="unpaid">Unpaid</option>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-6">
              <div class="form-group mb-3">
                <label for="billing_period" class="mb-1">Siklus Tagihan</label>
                <select class="form-select" id="billing_period" name="billing_period" autocomplete="off"
                  data-placeholder="billing_period">
                  <option value="Fixed Date">Fixed Date</option>
                  <option value="Billing Cycle" disabled>Billing Cycle</option>
                </select>
              </div>
            </div>
            <div class="col-lg-6">
              <label for="reg_date" class="mb-1">Tanggal Aktif</label>
              <div class="form-group mb-3">
                <input type="date" class="form-control" id="reg_date" name="reg_date" autocomplete="off"
                  data-placeholder="reg_date">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-6">
              <div class="form-group mb-3">
                <label for="ppn" class="mb-1">PPN<small> %</small></label>
                <input type="number" class="form-control" id="ppn" name="ppn" placeholder=""
                  autocomplete="off">
              </div>
            </div>
            <div class="col-lg-6">
              <div class="form-group mb-3">
                <label for="discount" class="mb-1">Discount<small> %</small></label>
                <input type="number" class="form-control" id="discount" name="discount" placeholder=""
                  autocomplete="off">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-6">
              <div class="form-group mb-3">
                <label for="amount" class="mb-1">Amount</label>
                <input type="text" disabled class="form-control" id="amount" name="amount" placeholder=""
                  autocomplete="off">
              </div>
            </div>
            <div class="col-lg-6">
              <div class="form-group mb-3">
                <label for="payment_total" class="mb-1">Payment Total</label>
                <input type="text" disabled class="form-control" id="payment_total" name="payment_total"
                  placeholder="" autocomplete="off">
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">
          Batal
        </button>
        <button class="btn btn-success" id="store" type="submit">
          Simpan
        </button>
      </div>
    </div>
  </div>
</div>

@push('script-modal')
  <script>
    $.ajax({
      url: areaUrl + '/list',
      type: "GET",
      success: function(data) {
        $('#kode_area').select2({
          data: (() => {
            return data.map((item) => {
                return {
                  id: item.id,
                  text: item.kode_area
                }
              })
              .sort((a, b) => a.text.localeCompare(b.text));
          })(),
          allowClear: true,
          placeholder: $(this).data('placeholder'),
          dropdownParent: $("#create .modal-content"),
        });
      }
    });

    $.ajax({
      url: odpUrl + '/list',
      type: "GET",
      success: function(data) {
        $('#kode_odp').select2({
          data: (() => {
            return data.map((item) => {
                return {
                  id: item.id,
                  text: item.kode_odp
                }
              })
              .sort((a, b) => a.text.localeCompare(b.text));
          })(),
          allowClear: true,
          placeholder: $(this).data('placeholder'),
          dropdownParent: $("#create .modal-content"),
        });
      }
    });

    $.ajax({
      url: memberUrl + '/list',
      type: "GET",
      success: function(data) {
        $('#member_id').select2({
          data: (() => {
            return data.map((item) => {
                return {
                  id: item.id,
                  text: item.full_name
                }
              })
              .sort((a, b) => a.text.localeCompare(b.text));
          })(),
          allowClear: true,
          placeholder: $(this).data('placeholder'),
          dropdownParent: $("#create .modal-content"),
        });
      }
    });

    $('#type-dhcp').change(function() {
      if ($(this).is(':checked')) {
        $('#createDhcp').show();
        $('#createPppoe').hide();
      }
    });

    $('#type-pppoe').change(function() {
      if ($(this).is(':checked')) {
        $('#createPppoe').show();
        $('#createDhcp').hide();
      }
    });
    $('#type-pppoe').prop('checked', true).trigger('change');

    $('#lock_mac').change(function() {
      let lock_mac = $(this).val();
      if (lock_mac == '1') {
        $('#lockMac').parent().show()
      } else {
        $('#lockMac').parent().hide()
      }
    });

    $('#include_payment').change(function(e) {
      if ($(this).is(':checked')) {
        $('#show_payment').slideDown();
        $('#ppn').attr('required', 'required');
        $('#discount').attr('required', 'required');
      } else {
        $('#show_payment').slideUp();
        $('#ppn').removeAttr('required');
        $('#discount').removeAttr('required');
      }
    });

    $('#store').click(function(e) {
      e.preventDefault();
      var error_ele = document.getElementsByClassName('form-text text-danger');
      if (error_ele.length > 0) {
        for (var i = error_ele.length - 1; i >= 0; i--) {
          error_ele[i].remove();
        }
      }

      var data = {
        'type': $('input[name=type]:checked').val(),
        'mac_address': $('#mac_address').val(),
        'username': $('#username').val(),
        'password': $('#password').val(),
        'profile': $('#profile option:selected').text(),
        'ip_address': $('#ip_address').val(),
        'nas': $('#nas').val(),
        'kode_area': $('#kode_area option:selected').text(),
        'kode_odp': $('#kode_odp').val(),
        'lock_mac': $('#lock_mac').val(),
        'mac': $('#lock_mac_address').val(),
        'profile_id': $('#profile option:selected').val(),
        'member_id': $('#member_id').val(),
        'include_payment': $('#include_payment').is(':checked') ? true : false,
        'payment_type': $('#payment_type').val(),
        'payment_status': $('#payment_status').val(),
        'billing_period': $('#billing_period').val(),
        'reg_date': $('#reg_date').val(),
        'ppn': $('#ppn').val(),
        'discount': $('#discount').val(),
        'amount': $('#amount').val(),
        'payment_total': $('#payment_total').val(),
      }

      $.ajax({
        url: baseUrl,
        type: "POST",
        cache: false,
        data: data,
        dataType: "json",

        success: function(data) {
          if (data.error) {
            Swal.fire({
              icon: 'error',
              title: 'Failed',
              text: `${data.error}`,
              showConfirmButton: false,
              timer: 5000
            });
          }
          if (data.success) {
            Swal.fire({
              icon: 'success',
              title: 'Success',
              text: `${data.message}`,
              showConfirmButton: false,
              timer: 1500
            });
            setTimeout(function() {
              table.ajax.reload()
              $('#type-dhcp').prop('checked', false);
              $('#type-pppoe').prop('checked', true);
              $('#mac_address').val('');
              $('#username').val('');
              $('#password').val('');
              $('#ppn').val('');
              $('#discount').val('');
              $('#amount').val('');
              $('#payment_total').val('');
              $('textarea', ).val('');
              $('#profile').val('').trigger('change');
              $('#member_id').val('').trigger('change');
              $('#include_payment').prop('checked', false);
              $('#ip_address').val('');
              $('#nas').val('').trigger('change');
              $('#kode_area').val('').trigger('change');
              $('#kode_odp').val('').trigger('change');
              $('#create').modal('hide');
            });
          } else {
            $.each(data.error, function(key, value) {
              var el = $(document).find('[name="' + key + '"]');
              el.after($('<div class="form-text text-danger">' + value[0] +
                '</div>'));
            });
            Swal.fire({
              icon: 'error',
              title: 'Failed',
              text: 'Failed to create user, please check your field',
              showConfirmButton: false,
              timer: 1500
            });

          }
        },

        error: function(err) {
          $("#message").html("Some Error Occurred!")
          Swal.fire({
            icon: 'error',
            title: 'Failed',
            text: 'Failed to create user, please change active date!',
            showConfirmButton: false,
            timer: 1500
          });

        }

      });
    });
  </script>
@endpush
