<!-- Modal Show -->
<div class="modal fade" id="broadcast" role="dialog" aria-labelledby="modal" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal">Kirim Broadcast</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-6">
                        @if(multi_auth()->role === 'Admin')
                        <div class="form-group mb-3">
                            <label for="tipe" class="mb-1">Tipe Broadcast <small
                                    class="text-danger">*</small></label>
                            <select class="form-select" id="tipe" name="tipe" autocomplete="off">
                                <option value="">-- Pilih Tipe Broadcast --</option>
                                <option value="all">Semua Pelanggan AKTIF</option>
                                <option value="suspend">Semua Pelanggan SUSPEND</option>
                                <option value="byarea">Semua Pelanggan POP</option>
                                <option value="byodp">Semua Pelanggan ODP</option>
                            </select>
                        </div>
                        @endif
                        @if(multi_auth()->role === 'Owner')
                        <div class="form-group mb-3">
                            <label for="tipe" class="mb-1">Tipe Broadcast <small
                                    class="text-danger">*</small></label>
                            <select class="form-select" id="tipe" name="tipe" autocomplete="off">
                                <option value="">-- Pilih Tipe Broadcast --</option>
                                <option value="owner_all">Semua Pelanggan AKTIF</option>
                                <option value="owner_trial">Semua Pelanggan TRIAL</option>
                                <option value="owner_expired">Semua Pelanggan EXPIRED</option>
                            </select>
                        </div>
                        @endif
                    </div>

                </div>

                <hr />
                <div id="show_all" style="display:none">
                    {{-- <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="subject_all" class="mb-1">Subject</label>
                                <input type="text" class="form-control" id="subject_all" placeholder="">
                            </div>
                        </div>
                    </div> --}}
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group mb-3">
                                <label for="message_all" class="mb-1">Message</label>
                                <textarea name="message_all" id="message_all" style="height: 150px" class="form-control" placeholder=""
                                    autocomplete="off" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row px-2">
                        <table class="table table-striped">
                            <tbody style="font-size:15px">
                                {{-- <tr>
                                    <td style="width:30%">POP</td>
                                    <td>: <span id="fill_area"></span></td>
                                </tr> --}}
                                <tr>
                                    <td style="width:30%">Jumlah Pelanggan AKTIF</td>
                                    <td>: <span id="fill_jmlpelanggan_all"></span></td>
                                </tr>
                                {{-- <tr>
                                    <td>Status Internet</td>
                                    <td>: <span id="fill_internet"></span></td>
                                </tr>
                                <tr>
                                    <td>IP Address</td>
                                    <td>: <span id="fill_ip"></span></td>
                                </tr> --}}
                            </tbody>
                        </table>
                        <small class="text-sm text-danger">Harap gunakan dengan bijak! Kami tidak bertanggung jawab
                            apabila nomor whatsapp anda terblokir</small>
                    </div>


                </div>

                <div id="show_suspend" style="display:none">
                    {{-- <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="subject_all" class="mb-1">Subject</label>
                                <input type="text" class="form-control" id="subject_all" placeholder="">
                            </div>
                        </div>
                    </div> --}}
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group mb-3">
                                <label for="message_suspend" class="mb-1">Message</label>
                                <textarea name="message_suspend" id="message_suspend" style="height: 150px" class="form-control" placeholder=""
                                    autocomplete="off" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row px-2">
                        <table class="table table-striped">
                            <tbody style="font-size:15px">
                                {{-- <tr>
                                    <td style="width:30%">POP</td>
                                    <td>: <span id="fill_area"></span></td>
                                </tr> --}}
                                <tr>
                                    <td style="width:30%">Jumlah Pelanggan SUSPEND</td>
                                    <td>: <span id="fill_jmlpelanggan_suspend"></span></td>
                                </tr>
                                {{-- <tr>
                                    <td>Status Internet</td>
                                    <td>: <span id="fill_internet"></span></td>
                                </tr>
                                <tr>
                                    <td>IP Address</td>
                                    <td>: <span id="fill_ip"></span></td>
                                </tr> --}}
                            </tbody>
                        </table>
                        <small class="text-sm text-danger">Harap gunakan dengan bijak! Kami tidak bertanggung jawab
                            apabila nomor whatsapp anda terblokir</small>
                    </div>


                </div>

                <div id="show_byarea" style="display:none">
                    <div class="row">
                        <div class="col-lg-6">
                            <label for="kode_area" class="mb-1">Pop / Wilayah</label>
                            <div class="form-group mb-3">
                                <div class="form-group mb-3">
                                    <select class="form-select" id="kode_area">
                                        @forelse ($areas as $row)
                                        <option value="{{ $row->kode_area }}">{{ $row->kode_area }} - {{ $row->deskripsi }}</option>
                                        @empty
                                        @endforelse    
                                    </select>
                                </div>
                            </div>
                                
                        </div>
                        {{-- <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="subject_area" class="mb-1">Subject</label>
                                <input type="text" class="form-control" id="subject_area" placeholder="">
                            </div>
                        </div> --}}

                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group mb-3">
                                <label for="message_area" class="mb-1">Message</label>
                                <textarea name="message_area" id="message_area" style="height: 150px" class="form-control" placeholder=""
                                    autocomplete="off" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row px-2">
                        <table class="table table-striped">
                            <tbody style="font-size:15px">
                                <tr>
                                    <td style="width:30%">POP</td>
                                    <td>: <span id="fill_area"></span></td>
                                </tr>
                                <tr>
                                    <td>Jumlah Pelanggan</td>
                                    <td>: <span id="fill_jmlpelanggan_area"></span></td>
                                </tr>
                            </tbody>
                        </table>
                        <small class="text-sm text-danger">Harap gunakan dengan bijak! Kami tidak bertanggung jawab
                            apabila nomor whatsapp anda terblokir</small>
                    </div>
                </div>

                <div id="show_byodp" style="display:none">
                    <div class="row">
                        <div class="col-lg-6">
                            <label for="kode_odp" class="mb-1">Kode ODP</label>
                            <div class="form-group mb-3">
                                <select class="form-select" id="kode_odp">
                                    @forelse ($odps as $row)
                                    <option value="{{ $row->kode_odp }}">{{ $row->kode_odp }} - {{ $row->deskripsi }}</option>
                                    @empty
                                    @endforelse    
                                </select>
                            </div>
                           
                        </div>
                        {{-- <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="subject_area" class="mb-1">Subject</label>
                                <input type="text" class="form-control" id="subject_area" placeholder="">
                            </div>
                        </div> --}}

                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group mb-3">
                                <label for="message_odp" class="mb-1">Message</label>
                                <textarea name="message_odp" id="message_odp" style="height: 150px" class="form-control" placeholder=""
                                    autocomplete="off" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row px-2">
                        <table class="table table-striped">
                            <tbody style="font-size:15px">
                                <tr>
                                    <td style="width:30%">Kode ODP</td>
                                    <td>: <span id="fill_odp"></span></td>
                                </tr>
                                <tr>
                                    <td>Jumlah Pelanggan</td>
                                    <td>: <span id="fill_jmlpelanggan_odp"></span></td>
                                </tr>
                            </tbody>
                        </table>
                        <small class="text-sm text-danger">Harap gunakan dengan bijak! Kami tidak bertanggung jawab
                            apabila nomor whatsapp anda terblokir</small>
                    </div>
                </div>

                <div id="show_owner_all" style="display:none">
                    {{-- <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="subject_all" class="mb-1">Subject</label>
                                <input type="text" class="form-control" id="subject_all" placeholder="">
                            </div>
                        </div>
                    </div> --}}
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group mb-3">
                                <label for="message_all_owner" class="mb-1">Message</label>
                                <textarea name="message_all_owner" id="message_all_owner" style="height: 150px" class="form-control" placeholder=""
                                    autocomplete="off" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row px-2">
                        <table class="table table-striped">
                            <tbody style="font-size:15px">
                                {{-- <tr>
                                    <td style="width:30%">POP</td>
                                    <td>: <span id="fill_area"></span></td>
                                </tr> --}}
                                <tr>
                                    <td style="width:30%">Jumlah Pelanggan AKTIF</td>
                                    <td>: <span id="fill_jmlpelanggan_owner_all"></span></td>
                                </tr>
                                {{-- <tr>
                                    <td>Status Internet</td>
                                    <td>: <span id="fill_internet"></span></td>
                                </tr>
                                <tr>
                                    <td>IP Address</td>
                                    <td>: <span id="fill_ip"></span></td>
                                </tr> --}}
                            </tbody>
                        </table>
                        <small class="text-sm text-danger">Harap gunakan dengan bijak! Kami tidak bertanggung jawab
                            apabila nomor whatsapp anda terblokir</small>
                    </div>


                </div>

                <div id="show_owner_trial" style="display:none">
                    {{-- <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="subject_all" class="mb-1">Subject</label>
                                <input type="text" class="form-control" id="subject_all" placeholder="">
                            </div>
                        </div>
                    </div> --}}
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group mb-3">
                                <label for="message_trial_owner" class="mb-1">Message</label>
                                <textarea name="message_all_owner" id="message_trial_owner" style="height: 150px" class="form-control" placeholder=""
                                    autocomplete="off" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row px-2">
                        <table class="table table-striped">
                            <tbody style="font-size:15px">
                                {{-- <tr>
                                    <td style="width:30%">POP</td>
                                    <td>: <span id="fill_area"></span></td>
                                </tr> --}}
                                <tr>
                                    <td style="width:30%">Jumlah Pelanggan TRIAL</td>
                                    <td>: <span id="fill_jmlpelanggan_owner_trial"></span></td>
                                </tr>
                                {{-- <tr>
                                    <td>Status Internet</td>
                                    <td>: <span id="fill_internet"></span></td>
                                </tr>
                                <tr>
                                    <td>IP Address</td>
                                    <td>: <span id="fill_ip"></span></td>
                                </tr> --}}
                            </tbody>
                        </table>
                        <small class="text-sm text-danger">Harap gunakan dengan bijak! Kami tidak bertanggung jawab
                            apabila nomor whatsapp anda terblokir</small>
                    </div>


                </div>

                <div id="show_owner_expired" style="display:none">
                    {{-- <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group mb-3">
                                <label for="subject_all" class="mb-1">Subject</label>
                                <input type="text" class="form-control" id="subject_all" placeholder="">
                            </div>
                        </div>
                    </div> --}}
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group mb-3">
                                <label for="message_expired_owner" class="mb-1">Message</label>
                                <textarea name="message_expired_owner" id="message_expired_owner" style="height: 150px" class="form-control" placeholder=""
                                    autocomplete="off" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row px-2">
                        <table class="table table-striped">
                            <tbody style="font-size:15px">
                                {{-- <tr>
                                    <td style="width:30%">POP</td>
                                    <td>: <span id="fill_area"></span></td>
                                </tr> --}}
                                <tr>
                                    <td style="width:30%">Jumlah Pelanggan EXPIRED</td>
                                    <td>: <span id="fill_jmlpelanggan_owner_expired"></span></td>
                                </tr>
                                {{-- <tr>
                                    <td>Status Internet</td>
                                    <td>: <span id="fill_internet"></span></td>
                                </tr>
                                <tr>
                                    <td>IP Address</td>
                                    <td>: <span id="fill_ip"></span></td>
                                </tr> --}}
                            </tbody>
                        </table>
                        <small class="text-sm text-danger">Harap gunakan dengan bijak! Kami tidak bertanggung jawab
                            apabila nomor whatsapp anda terblokir</small>
                    </div>


                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary" id="sendBroadcast" type="submit">
                    Kirim broadcast
                </button>
            </div>
        </div>
    </div>
</div>
