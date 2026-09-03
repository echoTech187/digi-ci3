<!-- Details Modal -->
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="detailQrisDynamicChannelExternalModal" tabindex="-1">
    <div class="modal-dialog modal-lg border-0">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header modal-header-primary border-0 mh-premium">
                <div class="d-flex align-items-center">
                    <div class="mh-icon-badge">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div class="mh-title-wrap">
                        <h6 class="mh-title" id="detailQrisDynamicChannelExternalModalLabel">External Log Details</h6>
                        <small class="mh-subtitle">View comprehensive information details</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 bg-light">
                <!-- Guide Banner -->
                <div class="d-flex align-items-start pb-4" id="detail-guide-banner">
                    <div class="d-flex align-items-start p-3 w-100" style="background:rgba(78,115,223,0.06);border:1px solid rgba(78,115,223,0.12);border-radius:12px;">
                        <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center mr-3 flex-shrink-0" style="width:32px;height:32px;"><i class="fas fa-qrcode" style="font-size:13px;"></i></div>
                        <div>
                            <h6 class="fw-bold mb-1" style="font-size:12px;color:var(--text-dark);">QRIS Dynamic Detail</h6>
                            <p class="text-muted mb-0" style="font-size:11px;line-height:1.5;">Inspect QRIS dynamic payment details including QR generation data, merchant info, settlement routing, and external channel responses.</p>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="mb-3">
                        <div class="small text-uppercase font-weight-bold text-muted mb-1">Provider</div>
                        <div class="h6 font-weight-bold text-dark mb-0" id="cashinExternalId"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="small text-uppercase font-weight-bold text-muted mb-1">Ext Ref ID 1</div>
                            <div class="d-flex align-items-start">
                                <div class="h6 font-weight-bold text-dark mb-0 text-break mr-2" style="word-break: break-all;" id="TransactionIdExternal1"></div>
                                <button type="button" class="btn btn-sm btn-link text-primary p-0 flex-shrink-0 copy-ref-btn" data-target="#TransactionIdExternal1" title="Copy ID" style="line-height: 1;">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="small text-uppercase font-weight-bold text-muted mb-1">Ext Ref ID 2</div>
                            <div class="d-flex align-items-start">
                                <div class="h6 font-weight-bold text-dark mb-0 text-break mr-2" style="word-break: break-all;" id="TransactionIdExternal2"></div>
                                <button type="button" class="btn btn-sm btn-link text-primary p-0 flex-shrink-0 copy-ref-btn" data-target="#TransactionIdExternal2" title="Copy ID" style="line-height: 1;">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <ul class="nav nav-pills mb-3" id="detailTab" role="tablist" style="gap: 10px;">
                    <li class="nav-item">
                        <a class="nav-link active rounded-pill px-4 font-weight-bold" id="request-tab" data-toggle="pill" href="#request" style="font-size: 11px;">REQUEST</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded-pill px-4 font-weight-bold text-muted" id="response-tab" data-toggle="pill" href="#response" style="font-size: 11px;">RESPONSE</a>
                    </li>
                </ul>
                
                <div class="tab-content" id="detailTabContent">
                    <div class="tab-pane fade show active" id="request">
                        <div class="bg-white rounded-lg p-3 border">
                            <div class="mb-3">
                                <label class="small font-weight-bold text-primary mb-1">Request Datetime</label>
                                <div class="text-dark small" id="RequestDatetime"></div>
                            </div>
                            <div class="mb-3">
                                <label class="small font-weight-bold text-primary mb-1">Headers</label>
                                <pre class="p-3 rounded small border-0 mb-0" id="RequestHeader" style="max-height: 150px; overflow-y: auto;"></pre>
                            </div>
                            <div>
                                <label class="small font-weight-bold text-primary mb-1">Payload</label>
                                <pre class="p-3 rounded small border-0 mb-0" id="RequestBody" style="max-height: 250px; overflow-y: auto;"></pre>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="response">
                        <div class="bg-white rounded-lg p-3 border">
                            <div class="mb-3">
                                <label class="small font-weight-bold text-primary mb-1">Response Datetime</label>
                                <div class="text-dark small" id="ResponseDatetime"></div>
                            </div>
                            <div class="mb-3">
                                <label class="small font-weight-bold text-primary mb-1">Headers</label>
                                <pre class="p-3 rounded small border-0 mb-0" id="ResponseHeader" style="max-height: 150px; overflow-y: auto;"></pre>
                            </div>
                            <div>
                                <label class="small font-weight-bold text-primary mb-1">Payload</label>
                                <pre class="p-3 rounded small border-0 mb-0" id="ResponseBody" style="max-height: 250px; overflow-y: auto;"></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 p-3">
                <button type="button" class="btn btn-secondary rounded-pill px-4 text-uppercase font-weight-bold" data-dismiss="modal" style="font-size: 11px;">Close</button>
            </div>
        </div>
    </div>
</div>
