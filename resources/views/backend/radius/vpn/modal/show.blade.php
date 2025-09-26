    <!-- Modal Show -->
    <div class="modal fade" id="show" tabindex="-1" role="dialog" aria-labelledby="modal"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal">Script VPN</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="sbp-preview">
                        <div class="sbp-preview-text">
                            <h6>Panduan Penggunaan</h6>
                            <li>Pilih salah satu mode yang akan digunakan</li>
                            <li>Copy seluruh isi script pada kolom mode yang dipilih</li>
                            <li>Login mikrotik melalui <code>Winbox</code>, buka menu <code>New Terminal</code> kemudian
                                paste script yang sudah di copy sebelumnya, lanjut tekan tombol <code>Enter</code> di
                                keyboard</li>
                            <li>Buka menu <code>PPP > Interface</code>. Jika langkah diatas sudah berhasil, maka akan
                                tampil
                                interface vpn baru sesuai mode yang dipilih</li>
                            <li>Lihat status interface VPN, jika belum terhubung <code>connected</code> silahkan coba
                                menggunakan mode yang lain</li>
                            <li>Jika sudah terhubung <code>connected</code> cirinya ada icon huruf <code>R</code> di
                                samping
                                interface VPN</code></li>
                        </div>
                        <hr />

                        <div class="sbp-preview-code">
                            <!-- Code sample-->
                            <input type="hidden" id="vpn_id">
                            <small class="h6">L2TP Client</small>
                            <div class="position-absolute text-center w-100"></div>
                            <div style="right:20px" class="position-absolute">
                                <button type="button" class="btn btn-transparent-dark"
                                    onclick="copyl2tp()" onmouseout="outl2tp()">
                                    <span id="l2tp">Copy</span>
                                </button>
                            </div>
                            <textarea readonly="" class="form-control pt-3" rows="5" id="copyl2tp"></textarea>
                            <hr />
                            <small class="h6">SSTP Client</small>
                            <div class="position-absolute text-center w-100"></div>
                            <div style="right:20px" class="position-absolute">
                                <button type="button" class="btn btn-transparent-dark"
                                    onclick="copysstp()" onmouseout="outsstp()">
                                    <span id="sstp">Copy</span>
                                </button>
                            </div>
                            <textarea class="form-control pt-3" rows="5" readonly="" id="copysstp"></textarea>
                            <hr />
                            <small class="h6">PPTP Client</small>
                            <div class="position-absolute text-center w-100"></div>
                            <div style="right:20px" class="position-absolute">
                                <button type="button" class="btn btn-transparent-dark"
                                    onclick="copypptp()" onmouseout="outpptp()">
                                    <span id="pptp">Copy</span>
                                </button>
                            </div>
                            <textarea class="form-control pt-3" rows="5" readonly="" id="copypptp"></textarea>
                            <hr />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="button" data-bs-dismiss="modal">
                        OK! Script Sudah Dipaste Di Mikrotik
                    </button>
                </div>
            </div>
        </div>
    </div>