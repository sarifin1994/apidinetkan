<!-- Modal Show -->
<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form>
                <div class="modal-header">
                    <h5 class="modal-title" id="modal">Edit Pelanggan</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="user_id">
                    <input type="hidden" id="kode_area_id">
                    <input type="hidden" id="kode_odp_id">
                    <input type="hidden" id="profile_id">
                  <span class="fw-bold d-flex align-items-center gap-1">
  <i class="ti ti-number-1"></i>
  Data Secret
</span>

                    <hr>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group mb-3">
                                <label for="type_edit" class="mb-1">Tipe <small
                                        class="text-danger">*</small></label>
                                <select class="form-control" name="type_edit" id="type_edit" onchange="validateTypeEdit()">
                                    <option value="pppoe">PPPOE</option>
                                    <option value="dhcp">DHCP</option>
                                </select>
                            </div>
                        </div>
                    <!-- </div>
                    <div class="row"> -->
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="username_edit" class="mb-1" id="username_label_edit">Username <small
                                        class="text-danger">*</small></label>
                                <input type="text" class="form-control" id="username_edit" name="username_edit"
                                    placeholder="" autocomplete="off" @if (multi_auth()->role !== 'Admin') disabled @endif
                                    required>
                            </div>
                        </div>
                        <div class="col-lg-6" id="password_wrapper_edit">
                            <div class="form-group mb-3">
                                <label for="password_edit" class="mb-1">Password <small
                                        class="text-danger">*</small></label>
                                <input type="text" class="form-control" id="password_edit" name="password_edit"
                                    placeholder="" autocomplete="off" @if (multi_auth()->role !== 'Admin') disabled @endif
                                    required>
                            </div>
                        </div>
                    <!-- </div>
                    <div class="row"> -->
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="profile_edit" class="mb-1">Profile <small
                                        class="text-danger">*</small></label>
                                <select class="form-select" id="profile_edit" name="profile_edit" autocomplete="off"
                                    @if (multi_auth()->role !== 'Admin') disabled @endif required>
                                    @forelse ($profiles as $profile)
                                        <option value="{{ $profile->id }}">{{ $profile->name }}</option>
                                    @empty
                                    @endforelse
                        
                                    @forelse ($licensedinetkan as $lic)
                                        <option value="{{ $lic->id_dinetkan }}">{{ $lic->name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="nas_edit" class="mb-1">Nas</label>
                                <select class="form-select" id="nas_edit" name="nas_edit" autocomplete="off"
                                    @if (multi_auth()->role !== 'Admin') disabled @endif required>
                                    <option value="">all</option>
                                    @forelse ($nas as $nas)
                                        <option value="{{ $nas->ip_router }}">{{ $nas->name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                    <!-- </div>
                    <div class="row"> -->
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="ip_address_edit" class="mb-1">IP Address</label>
                                <input type="text" class="form-control" id="ip_address_edit" name="ip_address_edit" placeholder=""
                                    autocomplete="off" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="lock_mac_edit" class="mb-1">Lock Mac</label>
                                <select class="form-select" id="lock_mac_edit" name="lock_mac_edit" autocomplete="off"
                                    @if (multi_auth()->role !== 'Admin') disabled @endif>
                                    <option value="0">Disabled</option>
                                    <option value="1">Enabled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3 mac" id="show_mac_edit">
                                <label for="mac_edit" class="mb-1">MAC Address</label>
                                <input type="text" class="form-control" id="mac_edit" name="mac" value=""
                                    placeholder="8b:fd:55:5a:0b:d4" autocomplete="off"
                                    @if (multi_auth()->role !== 'Admin') disabled @endif required>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                       <span class="fw-bold d-flex align-items-center gap-1">
                        <i class="ti ti-number-2"></i>
                        Data Pelanggan
                        </span>


                    </div>
                    <hr>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="full_name_edit" class="mb-1">Nama Lengkap <small
                                        class="text-danger">*</small></label>
                                <input type="text" class="form-control" id="full_name_edit" name="full_name_edit"
                                    placeholder="" autocomplete="off" onkeyup="kapital()" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="id_pelanggan_edit" class="mb-1">ID Pelanggan <small
                                        class="text-danger">*</small></label>
                                <input type="number" class="form-control" id="id_pelanggan_edit"
                                    name="id_pelanggan_edit" placeholder="" autocomplete="off" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="ktp_edit" class="mb-1">KTP</label>
                                <input type="text" class="form-control" id="ktp_edit" name="ktp_edit"
                                    placeholder="" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="npwp_edit" class="mb-1">NPWP</label>
                                <input type="text" class="form-control" id="npwp_edit" name="npwp_edit"
                                    placeholder="" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="wa_edit" class="mb-1">Nomor WhatsApp</label>
                                <input type="text" class="form-control" id="wa_edit" name="wa_edit"
                                    placeholder="" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="email_edit" class="mb-1">Email</label>
                                <input type="text" class="form-control" id="email_edit" name="email_edit"
                                    placeholder="" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="mitra_id_edit" class="mb-1">Mitra</label>
                                <select class="form-select" id="mitra_id_edit" name="mitra_id_edit"
                                    autocomplete="off" @if (multi_auth()->role !== 'Admin') disabled @endif required>
                                    <option value="">- Pilih Mitra -</option>
                                    @forelse ($mitras as $mitra)
                                        <option value="{{ $mitra->id }}">{{ $mitra->name }} -
                                            {{ $mitra->id_mitra }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 col-lg-6">
                            <div class="form-group mb-3">
                                <label for="pks_edit" class="mb-1">PKS</label>
                                <select class="form-select" id="pks_edit" name="pks_edit"
                                    autocomplete="off" data-placeholder="pks">
                                    <option value="no" selected>NO</option>
                                    <option value="yes">YES</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <label for="kode_area_edit" class="mb-1">POP</label>
                            <div class="form-group mb-3" style="display:grid">
                                <select class="form-control" id="kode_area_edit" name="kode_area_edit"
                                    autocomplete="off" data-placeholder="Pilih POP">
                                    <option value=""></option>
                                    @forelse ($areas as $area)
                                        <option value="{{ $area->id }}">{{ $area->kode_area }}</option>
                                    @empty
                                    @endforelse
                                </select>

                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label for="kode_odp_edit" class="mb-1">Kode ODP</label>
                            <div class="form-group mb-3" style="display:grid">
                                <select class="form-control" id="kode_odp_edit" name="kode_odp_edit"
                                    autocomplete="off" data-placeholder="Pilih Kode ODP">
                                    <option value=""></option>
                                    @forelse ($odps as $area)
                                        <option value="{{ $area->kode_odp }}">{{ $area->kode_odp }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-lg-6">
                            <div class="form-group mb-3">
                                <label for="sn_modem_edit" class="mb-1">SN Modem</label>
                                <input type="text" name="sn_modem_edit" id="sn_modem_edit" class="form-control"> 
                            </div>
                        </div>
                    </div>

                    

                    
                    <div class="mt-4">
<span class="fw-bold d-flex align-items-center gap-1">
  <i class="ti ti-number-3"></i>
  Data Alamat
</span>


                    </div>
                    <hr>

                    <div class="row">
                        <div class="mb-3 col-lg-6">
                            <label for="provinsi_edit" class="mb-1">Provinsi</label>
                            <div class="form-group mb-3" style="display:grid">
                                <select class="form-control" id="provinsi_edit" name="province_id" autocomplete="off" data-placeholder="Pilih Provinsi">
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 col-lg-6">
                            <label for="kabupaten_edit" class="mb-1">Kota / Kabupaten</label>
                            <div class="form-group mb-3" style="display:grid">
                                <select class="form-control" id="kabupaten_edit" name="regency_id" autocomplete="off" data-placeholder="Pilih Kota / Kabupaten">
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 col-lg-6">
                            <label for="kecamatan_edit" class="mb-1">Kecamatan</label>
                            <div class="form-group mb-3" style="display:grid">
                                <select class="form-control" id="kecamatan_edit" name="district_id" autocomplete="off" data-placeholder="Pilih Kecamatan">
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 col-lg-6">
                            <label for="desa_edit" class="mb-1">Desa</label>
                            <div class="form-group mb-3" style="display:grid">
                                <select class="form-control" id="desa_edit" name="village_id" autocomplete="off" data-placeholder="Pilih Desa">
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group mb-3">
                                    <label for="address_edit" class="mb-1">Alamat Lengkap <small></small></label>
                                    <textarea name="address_edit" id="address_edit" style="height: 90px" onkeyup="kapital()" class="form-control"
                                        placeholder="" autocomplete="off" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 mb-3">
                            <label for="address" class="form-label">Lokasi</label>
                            <div class="input-group">
                                <div id="map_edit"></div>
                            </div>
                        </div>
                        <div class="mb-3 col-lg-6">
                            <label for="latitude_edit" class="form-label">Latitude</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="latitude_edit" name="latitude" :class="errors.latitude && 'is-invalid'">
                                <template x-if="errors.latitude">
                                    <span class="invalid-feedback">
                                        <strong x-text="errors.latitude"></strong>
                                    </span>
                                </template>
                            </div>
                        </div>
                        <div class="mb-3 col-lg-6">
                            <label for="latitude_edit" class="form-label">Longitude</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="longitude_edit" name="longitude" :class="errors.longitude && 'is-invalid'">
                                <template x-if="errors.longitude">
                                    <span class="invalid-longitude">
                                        <strong x-text="errors.longitude"></strong>
                                    </span>
                                </template>
                            </div>
                        </div>
                    </div>
                    @if (multi_auth()->role === 'Admin')
                        <div class="mt-4">
                           <span class="fw-bold d-flex align-items-center gap-1">
                            <i class="ti ti-number-4"></i>
                            Data Pembayaran
                            </span>


                        </div>
                        <input class="form-check-input" name="option_billing_edit" id="option_billing_edit" type="checkbox">
                    <small class="text-sm">Tambahkan Data Pembayaran</small>
                    <hr>
                    <div id="show_billing_edit">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="payment_type_edit" class="mb-1">Tipe Pembayaran</label>
                                    <select class="form-select" id="payment_type_edit" name="payment_type_edit"
                                        autocomplete="off" data-placeholder="payment_type_edit" @if (multi_auth()->role !== 'Admin') disabled @endif>
                                        <option value="">- Pilih Tipe Pembayaran -</option>
                                        <option value="Prabayar">Prabayar</option>
                                        <option value="Pascabayar">Pascabayar</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="billing_period_edit" class="mb-1">Siklus Tagihan</label>
                                    <select class="form-select" id="billing_period_edit" name="billing_period_edit"
                                    autocomplete="off" data-placeholder="billing_period_edit">
                                    <option value="Fixed Date">Fixed Date</option>
                                    <option value="Renewable">Renewable</option>
                                    <option value="Billing Cycle">Billing Cycle</option>
                                </select>

                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6" id="show_reg_date_edit">
                                <div class="form-group mb-3">
                                    <label for="reg_date_edit" class="mb-1">Tanggal Aktif</label>
                                    <input type="date" class="form-control" id="reg_date_edit"
                                        name="reg_date_edit" disabled>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="next_due_edit" class="mb-1">Tanggal Jatuh Tempo</label>
                                    <input type="date" class="form-control" id="next_due_edit"
                                        name="next_due_edit" @if (multi_auth()->role !== 'Admin') disabled @endif>
                                    {{-- <small id="helper" class="form-text text-primary">Next Invoice <input type="date" class="form-control" id="next_invoice" name="next_invoice" disabled></small> --}}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="ppn_edit" class="mb-1">PPN<small> %</small></label>
                                    <input type="number" class="form-control" id="ppn_edit" name="ppn_edit"
                                        placeholder="" autocomplete="off" required
                                        @if (multi_auth()->role !== 'Admin') disabled @endif>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="discount_edit" class="mb-1">Discount<small> (Masukan nominal tanpa titik)</small></label>
                                    <input type="number" class="form-control" id="discount_edit"
                                        name="discount_edit" placeholder="" autocomplete="off" required
                                        @if (multi_auth()->role !== 'Admin') disabled @endif>
                                </div>
                            </div>
                        </div>
                        

                        <div class="mt-4">
                            <span class="fw-bold">
                                <span class="material-symbols-outlined">
                                    counter_5
                                </span> Data Ad on
                            </span>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <div class="row">
                                    <div class="mb-3">
                                        <label>Deskripsi</label>
                                        <input type="text" class="form-control" id="desc_edit" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>PPN</label>
                                        <input type="number" class="form-control" id="ppn_ad_edit" value="0" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Tagihan Bulanan</label>
                                        <select class="form-control" id="monthly_edit">
                                        <option value="Yes">Yes</option>
                                        <option value="No" selected>No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>QTY</label>
                                        <input type="number" class="form-control" id="qty_edit" value="1" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Harga</label>
                                        <input type="number" class="form-control" id="price_edit" required>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mb-3">
                                    <button class="btn btn-success" onclick="setdataedit()" type="button">Add AdOns</button>
                                </div>
                                <div class="table-responsive">
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
                                        <tbody id="invoiceTableBodyEdit">
                                            <!-- Baris item akan ditambahkan di sini -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="amount_edit" class="mb-1">Harga Paket</label>
                                    <input type="text" disabled class="form-control" id="amount_edit"
                                        name="amount_edit" placeholder="" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="payment_total_edit" class="mb-1">Total Pembayaran / Bulan</label>
                                    <input type="text" disabled class="form-control" id="payment_total_edit"
                                        name="payment_total_edit" placeholder="" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
                    @if(multi_auth()->role === 'Admin')
                    <div class="btn-group dropup">
                    <a class="btn btn-danger me-2 dropdown-toggle text-white" href="#" role="button" id="dropdownMenuLink"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="fw-bold d-flex align-items-center gap-1">
  <i class="ti ti-square-edit"></i>
  Action
</span>

                    </a>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <li><a class="dropdown-item" id="enable">Aktifkan</a></li>
                        <li><a class="dropdown-item" id="disable">Suspend</a></li>
                        <li><a class="dropdown-item" id="regist">Proses Registrasi</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" id="delete">Hapus</a></li>
                    </ul>
                </div>
                @endif
                    <button class="btn btn-primary text-white" id="update" type="submit">
                        Save changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
