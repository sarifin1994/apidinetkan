    <!-- Modal Show -->
    <div class="modal fade" id="show" tabindex="-1" role="dialog" aria-labelledby="modal"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal">Script Radius</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="sbp-preview">
                        <div class="sbp-preview-text">
                            <h6>Panduan Penggunaan</h6>
                            <li>Copy seluruh isi script pada text area dibawah ini</li>
                            <li>Login mikrotik melalui <code>Winbox</code>, buka menu <code>New Terminal</code> kemudian
                                paste script yang sudah di copy sebelumnya, lanjut tekan tombol <code>Enter</code> di
                                keyboard</li>
                            <li>Buka menu <code>Radius</code>. Jika langkah diatas sudah berhasil, maka akan
                                tampil
                                <code>Radius Server</code> yang baru dibuat</li>
                            <li>Ceklis <code>Use Radius</code> di winbox pada menu <code>IP > Hotspot > Server Profiles > Tab RADIUS</code></li>
                            <li>Ceklis <code>Use Radius</code> di winbox pada menu <code>PPP > Secret > PPP Authentication&Accounting</code>   </li>
                        </div>
                        <hr />

                        <div class="sbp-preview-code">
                            <input type="hidden" id="radius_id">
                            <!-- Code sample-->
                            <small class="h6">Radius Server with API Authentication (ROS 6)</small>
                            <div class="position-absolute text-center w-100"></div>
                            <div style="right:20px" class="position-absolute">
                                <button type="button" class="btn btn-transparent-dark"
                                    onclick="copyl2tp()" onmouseout="outl2tp()">
                                    <span id="l2tp">Copy</span>
                                </button>
                            </div>
                            <textarea class="form-control pt-3" rows="7" readonly="" id="copyl2tp"></textarea>

                            <hr>

                            <small class="h6">Radius Server ROS 7</small>
                            <div class="position-absolute text-center w-100"></div>
                            <div style="right:20px" class="position-absolute">
                                <button type="button" class="btn btn-transparent-dark"
                                    onclick="copyl2tpros7()" onmouseout="outl2tpros7()">
                                    <span id="l2tpros7">Copy</span>
                                </button>
                            </div>
                            <textarea class="form-control pt-3" rows="7" readonly="" id="copyl2tpros7"></textarea>
                            {{-- <hr />
                            <small class="h6">Add New API User</small>
                            <div class="position-absolute text-center w-100"></div>
                            <div style="right:20px" class="position-absolute">
                                <button type="button" class="btn btn-transparent-dark"
                                    onclick="copysstp()" onmouseout="outsstp()">
                                    <span id="sstp">Copy</span>
                                </button>
                            </div>
                            <textarea class="form-control pt-3" rows="5" readonly="" id="copysstp"></textarea> --}}
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