<!-- Modal Show -->
<div class="modal fade" id="create" role="dialog" aria-labelledby="modal" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <form id="createForm" action="{{ url()->current() }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="modal">TAMBAH TICKET PSB</h5>
          <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-6">
              <div class="form-group mb-3">
                <label for="nama_lengkap" class="mb-1">Nama Lengkap <small class="text-danger">*</small></label>
                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" onkeyup="kapital()"
                  required>
                <small id="helper" class="form-text text-muted">Isi nama lengkap sesuai KTP/SIM</small>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="form-group mb-3">
                <label for="no_wa" class="mb-1">Nomor WhatsApp <small class="text-danger">*</small></label>
                <input type="number" class="form-control" id="no_wa" name="no_wa" required>
                <small id="helper" class="form-text text-muted">Isi format berikut 081234567890</small>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-6">
              <div class="form-group mb-3">
                <label for="alamat" class="mb-1">Alamat Lengkap <small class="text-danger">*</small></label>
                <textarea name="alamat" id="alamat" style="height: 90px" onkeyup="kapital()" class="form-control" required></textarea>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="form-group mb-3">
                <label for="paket" class="mb-1">Paket Internet <small class="text-danger">*</small></label>
                <select class="form-select" id="paket" name="paket" required>
                  <option value="">Pilih Paket Internet</option>
                  @forelse ($profiles as $profile)
                    <option value="{{ $profile->id }}|{{ $profile->name }}">{{ $profile->name }} (Rp
                      {{ number_format((int) $profile->price, 0, '', '.') }})</option>
                  @empty
                    <option value="" disabled>Tidak ada paket tersedia</option>
                  @endforelse
                </select>
              </div>
            </div>
          </div>
          <span>SAAT DISUBMIT DATA TICKET PSB AKAN MASUK KE GRUP TELEGRAM PSB<br>HARAP SETTING TERLEBIH DAHULU GROUP
            CHAT ID DI MENU <a href="/telegram" class="text-primary">TELEGRAM</a></span>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">
            Batal
          </button>
          <button class="btn btn-primary" id="store" type="button">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Failure -->
<div class="modal fade" id="failureModal" tabindex="-1" aria-labelledby="failureModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-danger" id="failureModalLabel">Submission Failed</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Please fill out all required fields before submitting the form.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
  document.getElementById('store').addEventListener('click', function() {
    this.disabled = true; // Disable the button to prevent multiple clicks
    validateAndSubmitForm();
  });

  function validateAndSubmitForm() {
    const form = document.getElementById('createForm');
    let isValid = true;

    // Reset the invalid class for all inputs
    form.querySelectorAll('[required]').forEach((input) => {
      if (!input.value.trim()) {
        isValid = false;
        input.classList.add('is-invalid'); // Add visual cue for missing input
      } else {
        input.classList.remove('is-invalid');
      }
    });

    if (isValid) {
      // Collect form data for AJAX request
      const formData = new FormData(form);

      // Make AJAX request
      fetch(form.action, {
          method: form.method,
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Close the modal if the response indicates success
            const modal = bootstrap.Modal.getInstance(document.getElementById('create'));
            modal.hide();

            // Optionally, refresh the page or update UI after the modal is closed
            setTimeout(() => {
              location.reload(); // Reload the page to see updated data
            }, 500);
          } else {
            // Re-enable the button if the submission fails
            document.getElementById('store').disabled = false;
          }
        })
        .catch(error => {
          console.error('Error:', error);
          // Re-enable the button if there's an error
          document.getElementById('store').disabled = false;
        });
    } else {
      // Show failure modal if form validation fails
      new bootstrap.Modal(document.getElementById('failureModal')).show();

      // Re-enable the button if validation fails
      document.getElementById('store').disabled = false;
    }
  }
</script>

<style>
  .is-invalid {
    border-color: #dc3545;
  }
</style>
