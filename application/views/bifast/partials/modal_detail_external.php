<!-- ── BI-FAST External Channel Log Modal ── -->
<div class="modal fade" id="detailBiFastChannelExternalModal" tabindex="-1" role="dialog" aria-labelledby="detailBiFastChannelExternalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 860px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <!-- Header -->
            <div class="modal-header border-0 bg-primary text-white p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px;">
                        <i class="fas fa-server fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title font-weight-bold mb-0 text-white" id="detailBiFastChannelExternalModalLabel">Detail Disbursement</h5>
                        <p class="text-white-50 small mb-0" id="detailBiFastSubtitle">View communication logs</p>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-4 bg-light">
                <!-- Transaction Info Banner -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: #fff;">
                    <div class="card-body p-3">
                        <div class="row align-items-center text-center text-md-left">
                            <div class="col-md-4 mb-2 mb-md-0 border-right">
                                <small class="text-muted text-uppercase font-weight-bold" style="font-size: 10px;">Provider</small>
                                <div class="font-weight-bold text-primary" id="cashoutExternalId" style="font-size: 14px;">—</div>
                            </div>
                            <div class="col-md-4 mb-2 mb-md-0 border-right">
                                <small class="text-muted text-uppercase font-weight-bold" style="font-size: 10px;">External ID 1</small>
                                <input type="text" id="TransactionIdExternal1" class="form-control-plaintext form-control-sm text-center text-md-left font-weight-bold text-dark p-0" readonly value="—">
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted text-uppercase font-weight-bold" style="font-size: 10px;">External ID 2</small>
                                <input type="text" id="TransactionIdExternal2" class="form-control-plaintext form-control-sm text-center text-md-left font-weight-bold text-dark p-0" readonly value="—">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Request Column -->
                    <div class="col-md-6 mb-3 mb-md-0">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; background: #fff;">
                            <div class="card-header bg-transparent border-0 pt-3 px-3 pb-0 d-flex justify-content-between align-items-center">
                                <span class="badge badge-primary px-2 py-1"><i class="fas fa-arrow-up mr-1"></i> REQUEST</span>
                                <button type="button" class="btn btn-sm btn-link text-primary p-0 btn-copy-json" data-target="RequestBodyCode">
                                    <i class="fas fa-copy"></i> Copy Body
                                </button>
                            </div>
                            <div class="card-body p-3">
                                <div class="mb-2">
                                    <small class="text-muted d-block" style="font-size: 10px;">Timestamp</small>
                                    <input type="text" id="RequestDatetime" class="form-control-plaintext form-control-sm font-weight-bold p-0 text-muted" readonly value="—">
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted d-block" style="font-size: 10px;">Header</small>
                                    <pre id="RequestHeaderCode" style="background: #1e1e2f; color: #a5d6a7; border-radius: 8px; padding: 10px; font-size: 11px; max-height: 80px; overflow-y: auto;">—</pre>
                                </div>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 10px;">Body</small>
                                    <pre id="RequestBodyCode" style="background: #1e1e2f; color: #90caf9; border-radius: 8px; padding: 10px; font-size: 11px; max-height: 140px; overflow-y: auto;">—</pre>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Response Column -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; background: #fff;">
                            <div class="card-header bg-transparent border-0 pt-3 px-3 pb-0 d-flex justify-content-between align-items-center">
                                <span class="badge badge-success px-2 py-1"><i class="fas fa-arrow-down mr-1"></i> RESPONSE</span>
                                <button type="button" class="btn btn-sm btn-link text-success p-0 btn-copy-json" data-target="ResponseBodyCode">
                                    <i class="fas fa-copy"></i> Copy Body
                                </button>
                            </div>
                            <div class="card-body p-3">
                                <div class="mb-2">
                                    <small class="text-muted d-block" style="font-size: 10px;">Timestamp</small>
                                    <input type="text" id="ResponseDatetime" class="form-control-plaintext form-control-sm font-weight-bold p-0 text-muted" readonly value="—">
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted d-block" style="font-size: 10px;">Header</small>
                                    <pre id="ResponseHeaderCode" style="background: #1e1e2f; color: #a5d6a7; border-radius: 8px; padding: 10px; font-size: 11px; max-height: 80px; overflow-y: auto;">—</pre>
                                </div>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 10px;">Body</small>
                                    <pre id="ResponseBodyCode" style="background: #1e1e2f; color: #ffe082; border-radius: 8px; padding: 10px; font-size: 11px; max-height: 140px; overflow-y: auto;">—</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer border-0 px-4 py-3 bg-white justify-content-end">
                <button type="button" class="btn-dt-cancel" data-dismiss="modal">
                    <i class="fas fa-times mr-1 mr-2"></i> Close
                </button>
            </div>
        </div>
    </div>
</div>
