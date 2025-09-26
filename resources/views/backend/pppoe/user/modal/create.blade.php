<!-- Modal Show -->
<div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="create_user_pppoe">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal">Tambah Pelanggan</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if (multi_auth()->role === 'Admin' ||
                            (multi_auth()->role === 'Teknisi' &&
                                optional(\App\Models\Setting\Role::where('shortname', multi_auth()->shortname)->first())->teknisi_status_regist === 1))
                        <span class="fw-bold">
                            <i class="ti ti-info-circle"></i> Status Registrasi &nbsp;&nbsp;
                        </span>
                        <div class="row mt-1 mb-5">
                            <div class="col-md-6">
                                <input id="active_now" required="" type="radio" name="reg_status" value="1"
                                    checked><label class="form-check-label" for="active_now"> &nbsp;AKTIF SEKARANG
                                    &nbsp;&nbsp;</label>
                                <input id="on_process" required="" type="radio" name="reg_status"
                                    value="0"><label class="form-check-label" for="on_process">&nbsp;
                                    <i class="ti ti-clock-hour-4"></i> PENDING
                            </div>
                        </div>
                    @endif
                    <span class="fw-bold">
                        <i class="ti ti-list-numbers"></i> Data Secret
                    </span>
                    <hr>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group mb-3">
                                <label for="type" class="mb-1">Tipe <small
                                        class="text-danger">*</small></label>
                                <select class="form-control" name="type" id="type" onchange="validateType()">
                                    <option value="pppoe" selected>PPPOE</option>
                                    <option value="dhcp">DHCP</option>
                                </select>
                            </div>
                        </div>
                    <!-- </div>
                    <div class="row"> -->
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="username" class="mb-1" id="username_label">Username <small
                                        class="text-danger">*</small></label>
                                <input type="text" class="form-control" id="username" name="username" placeholder=""
                                    autocomplete="off" required>
                            </div>
                        </div>
                        <div class="col-lg-6" id="password_wrapper">
                            <div class="form-group mb-3">
                                <label for="password" class="mb-1">Password <small
                                        class="text-danger">*</small></label>
                                <input type="text" class="form-control" id="password" name="password" placeholder=""
                                    autocomplete="off" required>
                            </div>
                        </div>
                    <!-- </div>
                    <div class="row"> -->
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="profile" class="mb-1">Profile <small
                                        class="text-danger">*</small></label>
                                <select class="form-select" id="profile" name="profile" autocomplete="off" required>
                                    <option value="">- Pilih Profile -</option>
                                    @forelse ($profiles as $profile)
                                        <option value="{{ $profile->id }}">{{ $profile->name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="nas" class="mb-1">Nas</label>
                                <select class="form-select" id="nas" name="nas" autocomplete="off">
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
                                <label for="ip_address" class="mb-1">IP Address</label>
                                <input type="text" class="form-control" id="ip_address" name="ip_address" placeholder=""
                                    autocomplete="off" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="lock_mac" class="mb-1">Lock Mac</label>
                                <select class="form-select" id="lock_mac" name="lock_mac" autocomplete="off">
                                    <option value="0">Disabled</option>
                                    <option value="1">Enabled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3 mac" id="show_mac" style="display:none">
                                <label for="mac" class="mb-1">MAC Address</label>
                                <input type="text" class="form-control" id="mac" name="mac"
                                    value="" placeholder="8b:fd:55:5a:0b:d4" autocomplete="off" required>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="fw-bold">
                            <i class="ti ti-user"></i> Data Pelanggan
                        </span>
                    </div>
                    <hr>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="full_name" class="mb-1">Nama Lengkap <small
                                        class="text-danger">*</small></label>
                                <input type="text" class="form-control" id="full_name" name="full_name"
                                    placeholder="" autocomplete="off" onkeyup="kapital()" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="id_pelanggan" class="mb-1">ID Pelanggan <small
                                        class="text-danger">*</small></label>
                                <!-- <input type="text" class="form-control" id="id_pelanggan" name="id_pelanggan"
                                    placeholder="" autocomplete="off" required> -->
                                <input type="text" class="form-control" placeholder="Akan terbentuk saat disimpan"
                                    autocomplete="off" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="ktp" class="mb-1">KTP</label>
                                <input type="text" class="form-control" id="ktp" name="ktp"
                                    placeholder="" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="npwp" class="mb-1">NPWP</label>
                                <input type="text" class="form-control" id="npwp" name="npwp"
                                    placeholder="" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="wa" class="mb-1">Nomor WhatsApp</label>
                                <input type="text" class="form-control" id="wa" name="wa"
                                    placeholder="" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="email" class="mb-1">Email</label>
                                <input type="text" class="form-control" id="email" name="email"
                                    placeholder="" autocomplete="off" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="mitra_id" class="mb-1">Mitra</label>
                                <select class="form-select" id="mitra_id" name="mitra_id" autocomplete="off"
                                    required>
                                    @if (multi_auth()->role === 'Mitra')
                                        <option value="{{ multi_auth()->id }}">{{ multi_auth()->name }} -
                                            {{ multi_auth()->id_mitra }}</option>
                                    @else
                                        <option value="">- Pilih Mitra -</option>
                                        @forelse ($mitras as $mitra)
                                            <option value="{{ $mitra->id }}">{{ $mitra->name }} -
                                                {{ $mitra->id_mitra }}</option>
                                        @empty
                                        @endforelse
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 col-lg-6">
                            <div class="form-group mb-3">
                                <label for="pks" class="mb-1">PKS</label>
                                <select class="form-select" id="pks" name="pks"
                                    autocomplete="off" data-placeholder="pks">
                                    <option value="no" selected>NO</option>
                                    <option value="yes">YES</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <label for="kode_area" class="mb-1">POP</label>
                            <div class="form-group mb-3" style="display:grid">
                                <select class="form-control" id="kode_area" name="kode_area" autocomplete="off"
                                    data-placeholder="Pilih POP">
                                    <option value=""></option>
                                    @forelse ($areas as $area)
                                        <option value="{{ $area->id }}">{{ $area->kode_area }}</option>
                                    @empty
                                    @endforelse
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
                        <div class="mb-3 col-lg-6">
                            <div class="form-group mb-3">
                                <label for="sn_modem" class="mb-1">SN Modem</label>
                                <input type="text" name="sn_modem" id="sn_modem" class="form-control"> 
                            </div>
                        </div>
                    </div>


                    <div class="mt-4">
                        <span class="fw-bold">
                            <i class="ti ti-map-pin"></i> Data Alamat
                        </span>
                    </div>
                    <hr>

                    <div class="row">
                        <div class="mb-3 col-lg-6">
                            <label for="provinsi" class="mb-1">Provinsi</label>
                            <div class="form-group mb-3" style="display:grid">
                                <select class="form-control" id="provinsi" name="province_id" autocomplete="off"
                                    data-placeholder="Pilih Provinsi">
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 col-lg-6">
                            <label for="kabupaten" class="mb-1">Kota / Kabupaten</label>
                            <div class="form-group mb-3" style="display:grid">
                                <select class="form-control" id="kabupaten" name="regency_id" autocomplete="off"
                                    data-placeholder="Pilih Kota / Kabupaten">
                                    <option value=""></option>
                                    @forelse ($regencies as $pp)
                                        <option value="{{ $pp->id }}">{{ $pp->name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 col-lg-6">
                            <label for="kecamatan" class="mb-1">Kecamatan</label>
                            <div class="form-group mb-3" style="display:grid">
                                <select class="form-control" id="kecamatan" name="district_id" autocomplete="off"
                                    data-placeholder="Pilih Kecamatan">
                                    <option value=""></option>
                                    @forelse ($districts as $pp)
                                        <option value="{{ $pp->id }}">{{ $pp->name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 col-lg-6">
                            <label for="desa" class="mb-1">Desa</label>
                            <div class="form-group mb-3" style="display:grid">
                                <select class="form-control" id="desa" name="village_id" autocomplete="off"
                                    data-placeholder="Pilih Desa">
                                    <option value=""></option>
                                    @forelse ($villages as $pp)
                                        <option value="{{ $pp->id }}">{{ $pp->name }}</option>
                                    @empty
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group mb-3">
                                <label for="address" class="mb-1">Alamat Lengkap <small></small></label>
                                <textarea name="address" id="address" style="height: 90px" onkeyup="kapital()" class="form-control"
                                    placeholder="" autocomplete="off" required></textarea>
                            </div>
                        </div>
                        <div class="col-lg-12 mb-3">
                            <label for="address" class="form-label">Lokasi</label>
                            <div class="input-group">
                                <div id="map"></div>
                            </div>
                        </div>
                        <div class="mb-3 col-lg-6">
                            <label for="latitude" class="form-label">Latitude</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="latitude" name="latitude"
                                    :class="errors.latitude && 'is-invalid'">
                                <template x-if="errors.latitude">
                                    <span class="invalid-feedback">
                                        <strong x-text="errors.latitude"></strong>
                                    </span>
                                </template>
                            </div>
                        </div>
                        <div class="mb-3 col-lg-6">
                            <label for="latitude" class="form-label">Longitude</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="longitude" name="longitude"
                                    :class="errors.longitude && 'is-invalid'">
                                <template x-if="errors.longitude">
                                    <span class="invalid-longitude">
                                        <strong x-text="errors.longitude"></strong>
                                    </span>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <span class="fw-bold">
                            <span class="material-symbols-outlined">
                                counter_4
                            </span> Data Pembayaran
                        </span>
                    </div>
                    <input class="form-check-input" name="option_billing" id="option_billing" value="1"
                        type="checkbox" checked>
                    <small class="text-sm">Tambahkan Data Pembayaran</small>
                    <hr>

                    <div id="show_billing">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="payment_type" class="mb-1">Tipe Pembayaran</label>
                                    <select class="form-select" id="payment_type" name="payment_type"
                                        autocomplete="off" data-placeholder="payment_type">
                                        <option value="Prabayar">Prabayar</option>
                                        <option value="Pascabayar">Pascabayar</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3" id="show_payment_status">
                                    <label for="payment_status" class="mb-1">Status Pembayaran</label>
                                    <select class="form-select" id="payment_status" name="payment_status"
                                        autocomplete="off" data-placeholder="payment_status">
                                        <option value="unpaid">Unpaid</option>
                                        <option value="paid">Paid</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="billing_period" class="mb-1">Siklus Tagihan</label>
                                    <select class="form-select" id="billing_period" name="billing_period"
                                        autocomplete="off" data-placeholder="billing_period">
                                        <option value="Fixed Date">Fixed Date</option>
                                        <option value="Renewable">Renewable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label for="reg_date" class="mb-1">Tanggal Aktif</label>
                                <div class="form-group mb-3">
                                    <input type="date" class="form-control" id="reg_date" name="reg_date"
                                        autocomplete="off" data-placeholder="reg_date">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="ppn" class="mb-1">PPN<small> %</small></label>
                                    <input type="number" class="form-control" id="ppn" name="ppn"
                                        placeholder="" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="discount" class="mb-1">Discount<small> (Masukan nominal tanpa
                                            titik)</small></label>
                                    <input type="number" class="form-control" id="discount" name="discount"
                                        placeholder="" autocomplete="off" required>
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
                                        <input type="text" class="form-control" id="desc" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>PPN</label>
                                        <input type="number" class="form-control" id="ppn_ad" value="0" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Tagihan Bulanan</label>
                                        <select class="form-control" id="monthly">
                                        <option value="Yes">Yes</option>
                                        <option value="No" selected>No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label>QTY</label>
                                        <input type="number" class="form-control" id="qty" value="1" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Harga</label>
                                        <input type="number" class="form-control" id="price" required>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mb-3">
                                    <button class="btn btn-success" onclick="setdata()" type="button">Add AdOns</button>
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
                                        <tbody id="invoiceTableBody">
                                            <!-- Baris item akan ditambahkan di sini -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="amount" class="mb-1">Harga Paket</label>
                                    <input type="text" disabled class="form-control" id="amount"
                                        name="amount" placeholder="" autocomplete="off">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-3">
                                    <label for="payment_total" class="mb-1">Total Pembayaran / Bulan</label>
                                    <input type="text" disabled class="form-control" id="payment_total"
                                        name="payment_total" placeholder="" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary text-white" id="store" type="submit">
                        Save changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
