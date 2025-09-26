<!-- payment Modal Show -->
<div class="modal fade" id="showPayment" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modal">DATA PEMBAYARAN | <span class="text-sm" id="payment_full_name_data"></h5>
        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form x-load x-data="form" id="paymentForm">
        <div class="modal-body payment-data">
          <div class="row">
            <input type="hidden" id="pppoe_id_data" name="pppoe_id">
            <div class="col-lg-6">
              <div class="form-group mb-3">
                <label for="payment_type_data" class="mb-1">Tipe Pembayaran</label>
                <select class="form-select" id="payment_type_data" name="payment_type" autocomplete="off">
                  <option value="Prabayar">Prabayar</option>
                  <option value="Pascabayar">Pascabayar</option>
                </select>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="form-group mb-3">
                <label for="billing_period_data" class="mb-1">Siklus Tagihan</label>
                <select class="form-select" id="billing_period_data" name="billing_period" autocomplete="off">
                  <option value="Fixed Date">Fixed Date</option>
                  <option value="Billing Cycle">Billing Cycle</option>
                </select>

              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-6">
              <div class="form-group mb-3">
                <label for="payment_type" class="mb-1">Tanggal Aktif</label>
                <input type="date" class="form-control" id="reg_date_data" name="reg_date">
              </div>
            </div>
            <div class="col-lg-6">
              <div class="form-group mb-3">
                <label for="payment_type" class="mb-1">Tanggal Jatuh Tempo</label>
                <input type="date" class="form-control" id="next_due_data" name="next_due">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-6">
              <div class="form-group mb-3">
                <label for="profile" class="mb-1">Internet Profile</label>
                <select class="form-select" id="profile_data" name="profile" autocomplete="off">
                  @forelse ($profiles as $profile)
                    <option value="{{ $profile->id }}">{{ $profile->name }}</option>
                  @empty
                  @endforelse
                </select>

              </div>
            </div>
            <div class="col-lg-6">
              <div class="form-group mb-3">
                <label for="amount" class="mb-1">Amount</label>
                <input type="text" class="form-control" id="amount_data" name="amount" disabled>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-6">
              <div class="form-group mb-3">
                <label for="ppn" class="mb-1">PPN<small> %</small></label>
                <input type="number" class="form-control" id="ppn_data" name="ppn" placeholder=""
                  autocomplete="off" required>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="form-group mb-3">
                <label for="discount" class="mb-1">Discount<small> %</small></label>
                <input type="number" class="form-control" id="discount_data" name="discount" placeholder=""
                  autocomplete="off" required>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-6">
              <div class="form-group mb-3">
                <label for="payment_total" class="mb-1">Payment Total</label>
                <input type="text" disabled class="form-control" id="payment_total_data" name="payment_total"
                  placeholder="payment_total" autocomplete="off">
              </div>
            </div>
            <div class="col-lg-6">
              <div class="form-group mb-3">
                <label for="member_id" class="mb-1">Member</label>
                <select class="form-select" id="member_id_data" name="member_id" autocomplete="off"
                  data-placeholder="Pilih Member">
                  <option value="">Pilih Member</option>
                </select>
              </div>
            </div>
          </div>

          <span class="text-sm">Pemindahan member akan mengubah data member pada invoice yang terkait dan status
            pembayaran tagihan baru akan berstatus belum lunas.</span>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">
            Batal
          </button>
          @if (auth()->user()->role === 'Admin' || auth()->user()->role === 'Helpdesk' || auth()->user()->role === 'Kasir')
            <button class="btn btn-primary" id="editPayment" type="submit">
              Simpan
            </button>
          @endif
        </div>
      </form>
    </div>
  </div>
</div>

@push('script-modal')
  <script>
    let isNew = false;

    function calculateTotal() {
      let amount = parseInt($('#amount_data').val().replace(/\./g, '')) || 0;
      let ppn = parseFloat($('#ppn_data').val()) || 0;
      let discount = parseFloat($('#discount_data').val()) || 0;

      if (amount <= 0) {
        $('#payment_total_data').val('0');
        return;
      }

      // Calculate PPN and Discount
      let amount_ppn = (ppn > 0) ? (amount * ppn / 100) : 0;
      let amount_discount = (discount > 0) ? (amount * discount / 100) : 0;

      // Calculate total
      let total = amount + amount_ppn - amount_discount;
      $('#payment_total_data').val(formatRupiah(total.toString(), 2, '.', ','));
    }

    // Event listener for PPN and Discount inputs
    $("#ppn_data, #discount_data").on("keyup change", calculateTotal);

    $('#pppoeTable').on('click', '.show-payment', function(event) {
      let pppoeId = $(this).data('id');
      isNew = $(this).data('new');

      if (!pppoeId) return;

      if (isNew) {
        $('#paymentForm').trigger('reset');
        $("#pppoe_id_data").val(pppoeId);
        $('#member_id_data').val(null).change();
        $('#profile_data').trigger('change');
        $('#payment_type_data, #billing_period_data, #reg_date_data, #next_due_data, #profile_data').removeAttr(
          'disabled');
        return;
      }

      // Fetch payment data
      $.ajax({
        url: baseUrl + `/${pppoeId}/payment`,
        type: "GET",
        success: function(data) {
          $('#payment_full_name_data').html(data.member.full_name);

          $("#pppoe_id_data").val(data.pppoe_id);
          $("#payment_type_data").val(data.payment_type).change();
          $("#billing_period_data").val(data.billing_period).change();
          $("#reg_date_data").val(data.reg_date);
          $("#next_due_data").val(data.next_due);
          $("#profile_data").val(data.profile.id).change();
          $("#member_id_data").val(data.member.id).change();

          let amount = data.profile.price;
          $('#amount_data').val(formatRupiah(amount.toString(), 2, '.', ','));
          $('#ppn_data').val(data.ppn || 0);
          $('#discount_data').val(data.discount || 0);

          calculateTotal();
        }
      });
    });

    $('#reg_date_data').on('change', function() {
      let reg_date = $(this).val();
      let next_due = moment(reg_date).add(1, 'months').format('YYYY-MM-DD');
      $('#next_due_data').val(next_due);
    });

    $("#profile_data").on("change", function() {
      let profile_id = $(this).val();
      if (!profile_id) return;

      $.ajax({
        url: baseUrl + `/${profile_id}/price`,
        type: "GET",
        cache: false,
        success: function(data) {
          let amount = data.price;
          $('#amount_data').val(formatRupiah(amount.toString(), 2, '.', ','));
          calculateTotal();
        }
      });
    });

    $('#editPayment').click(function(e) {
      e.preventDefault();
      $('.alert.text-sm').remove();

      let pppoe_id = $('#pppoe_id_data').val();

      let data = {
        'member_id': $('#member_id_data').val(),
        'next_due': $('#next_due_data').val(),
        'billing_period': $('#billing_period_data').val(),
        'payment_type': $('#payment_type_data').val(),
        'reg_date': $('#reg_date_data').val(),
        'profile_id': $('#profile_data').val(),
        'amount': $('#amount_data').val().replace(/\./g, ''),
        'payment_total': $('#payment_total_data').val().replace(/\./g, ''),
        'ppn': $('#ppn_data').val(),
        'discount': $('#discount_data').val(),
      };

      $.ajax({
        url: baseUrl + `/${pppoe_id}/payment`,
        type: isNew ? 'POST' : 'PUT',
        cache: false,
        data: data,
        dataType: "json",
        success: function(response) {
          if (response.success) {
            Swal.fire({
              icon: 'success',
              title: 'Success',
              text: `${response.message}`,
              showConfirmButton: false,
              timer: 1500
            });
            setTimeout(() => {
              table.ajax.reload();
              show_payment.hide();
            }, 1500);
          } else {
            $.each(response.error, (key, value) => {
              let el = $(document).find(`[name="${key}"]`);
              el.after(`<span class="alert text-sm text-danger">${value[0]}</span>`);
            });
          }
        },
        error: function() {
          $("#message").html("Some Error Occurred!");
        }
      });
    });

    // Initialize Member Dropdown
    $.ajax({
      url: memberUrl + '/list',
      type: "GET",
      success: function(data) {
        $('#member_id_data').select2({
          data: data.map(item => ({
            id: item.id,
            text: item.full_name
          })).sort((a, b) => a.text.localeCompare(b.text)),
          allowClear: true,
          placeholder: $(this).data('placeholder'),
          dropdownParent: $("#showPayment .modal-content"),
        });
      }
    });
  </script>
@endpush
