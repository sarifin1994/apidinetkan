<div class="modal fade" id="memberModal" tabindex="-1" aria-labelledby="memberModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="memberModalLabel">Create/Edit Member</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="memberForm" method="POST">
        @csrf
        <input type="hidden" name="member_id" id="member_id">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="full_name" class="form-label">Full Name</label>
              <input type="text" class="form-control" id="full_name" name="full_name" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="id_member" class="form-label">Customer ID</label>
              <input type="text" class="form-control" id="id_member" name="id_member">
            </div>
            <div class="col-md-6 mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="email" name="email">
            </div>
            <div class="col-md-6 mb-3">
              <label for="wa" class="form-label">WhatsApp Number</label>
              <input type="text" class="form-control" id="wa" name="wa">
            </div>
            <div class="col-md-12 mb-3">
              <label for="address" class="form-label">Address</label>
              <textarea class="form-control" id="address" name="address" rows="3"></textarea>
            </div>
            <div class="col-md-6 mb-3">
              <label for="kode_area" class="form-label">Area Code</label>
              <select class="form-select" id="kode_area" name="kode_area">
                <!-- Populate with your area codes -->
                <option value="">Select Area</option>
                <!-- Add options dynamically -->
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label for="payment_type" class="form-label">Payment Type</label>
              <select class="form-select" id="payment_type" name="payment_type" required>
                <option value="Prabayar">Prepaid</option>
                <option value="Pascabayar">Postpaid</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label for="billing_period" class="form-label">Billing Period</label>
              <select class="form-select" id="billing_period" name="billing_period" required>
                <option value="Fixed Date">Fixed Date</option>
                <option value="Billing Cycle">Billing Cycle</option>
              </select>
            </div>
            <div class="col-md-6 mb-3">
              <label for="profile_id" class="form-label">Internet Package</label>
              <select class="form-select" id="profile_id" name="profile_id" required>
                <!-- Populate with your internet packages -->
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="saveMemberBtn">Save Member</button>
        </div>
      </form>
    </div>
  </div>
</div>
