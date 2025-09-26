<!-- Modal Show -->
<div class="modal fade" id="generate" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">Generate Voucher</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <span class="fw-bold">
                    <span class="material-symbols-outlined">
                        counter_1
                        </span> Data Voucher</span>
                    <hr>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <input type="hidden" id="wa_reseller">
                            <label for="jml_voucher" class="mb-1">Jumlah Voucher <small
                                class="text-danger">*</small></label>
                            <input type="text" class="form-control" id="jml_voucher" name="jml_voucher"
                                placeholder="" autocomplete="off" required>
                        </div>
                    </div>                    
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="remark" class="mb-1">Remark</label>
                            <input type="text" class="form-control" id="remark" name="remark"
                                placeholder="" autocomplete="off" required>
                        </div>
                    </div>
                    
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="model" class="mb-1">Voucher Model</label>
                            <select class="form-select" id="model" name="model" autocomplete="off"
                                data-placeholder="Voucher Model" required>
                                <option value="1">Username = Password</option>
                                <option value="2">Username + Password</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="character" class="mb-1">Voucher Character</label>
                            <select class="form-select" id="character" name="character" autocomplete="off"
                                data-placeholder="Voucher Character" required>
                                <option value="1">0123456789</option>
                                <option value="2">abcdefghijkl</option>
                                <option value="3">ABCDEFGHIJKL</option>
                                <option value="4">ABCDEFG01234</option>
                                <option value="5" selected>abcABC01234</option>
                            </select>
                        </div>

                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="length" class="mb-1">Voucher Length</label>
                            <select class="form-select" id="length" name="length" autocomplete="off"
                                data-placeholder="Voucher Length" required>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6" selected>6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="prefix" class="mb-1">Voucher Prefix</label>
                            <input type="text" class="form-control" id="prefix" name="prefix"
                                placeholder="" autocomplete="off" required>
                        </div>
                    </div>

                </div>

                <span class="fw-bold">
                    <span class="material-symbols-outlined">
                        counter_2
                        </span> Data Profile</span>
                    <hr>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="profile" class="mb-1">Profile <small
                                class="text-danger">*</small></label>
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
                            <label for="nas" class="mb-1">Nas</label>
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
                    {{-- <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="hotspot_server" class="mb-1">Hotspot Server</label>
                            <input type="text" class="form-control" id="hotspot_server" name="hotspot_server"
                                placeholder="" autocomplete="off" required>
                        </div>
                    </div> --}}
                    @if(multi_auth()->role !== 'Reseller')
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="payment_status" class="mb-1">Status Pembayaran</label>
                            <select class="form-select" id="payment_status" name="payment_status" autocomplete="off"
                                data-placeholder="Pilih Payment Status">
                                <option value="2">Paid</option>
                                <option value="1">Unpaid</option>
                            </select>
                        </div>
                    </div>
                    @else
                    <div class="col-lg-6 d-none">
                        <div class="form-group mb-3">
                            <label for="payment_status" class="mb-1">Status Pembayaran</label>
                            <select class="form-select" id="payment_status" name="payment_status" autocomplete="off"
                                data-placeholder="Pilih Payment Status">
                                <option value="2">Paid</option>
                            </select>
                        </div>
                    </div>
                    @endif

                    <div class="col-lg-6">
                        <label for="reseller" class="mb-1">Reseller</label>
                        <div class="form-group mb-3" style="display:grid">
                            <select class="form-select" id="reseller" name="reseller" autocomplete="off"
                                data-placeholder="Pilih Reseller">
                                <option value="">Pilih Reseller</option>
                                @forelse ($resellers as $reseller)
                                    <option value="{{ $reseller->id }}">{{ $reseller->name }} - {{ $reseller->id_reseller }}</option>
                                @empty
                                @endforelse
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="price" class="mb-1">Price</label>
                            <input type="text" class="form-control" id="price" name="price"
                                placeholder="" autocomplete="off" disabled>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group mb-3">
                            <label for="total" class="mb-1">Total</label>
                            <input type="text" class="form-control" id="total" name="total"
                                placeholder="" autocomplete="off" disabled>
                        </div>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary text-white" id="generate_voucher" type="submit">
                        Generate voucher
                    </button>
            </div>
        </div>
    </div>
</div>
