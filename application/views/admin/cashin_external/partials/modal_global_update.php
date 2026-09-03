<!-- ── Global/Batch Mapping Update Modal ── -->
<div class="modal fade" id="globalUpdateModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 650px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header border-0 bg-light p-4">
                <div>
                    <h5 class="modal-title font-weight-bold text-dark"><i class="fas fa-layer-group text-primary mr-2"></i> Edit Mapping Configuration</h5>
                    <p class="text-muted small mb-0 mt-1">Bulk update payment channel routes and providers for merchants</p>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="globalUpdateForm" action="<?= base_url('external/cashin/update-global') ?>" method="POST">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <div class="modal-body p-4">
                    <!-- Scope Selection -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted d-block">UPDATE SCOPE</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="btn-dt-action-radio w-100 d-flex align-items-center justify-content-between p-3 border rounded-lg cursor-pointer">
                                    <div class="d-flex align-items-center">
                                        <input type="radio" name="update_type" value="group" checked class="mr-2">
                                        <div>
                                            <div class="font-weight-bold text-dark" style="font-size: 13px;">Entire Group</div>
                                            <div class="text-muted smaller">All merchants in group</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <div class="col-6">
                                <label class="btn-dt-action-radio w-100 d-flex align-items-center justify-content-between p-3 border rounded-lg cursor-pointer">
                                    <div class="d-flex align-items-center">
                                        <input type="radio" name="update_type" value="merchant" class="mr-2">
                                        <div>
                                            <div class="font-weight-bold text-dark" style="font-size: 13px;">Selected Merchants</div>
                                            <div class="text-muted smaller">Target specific merchants</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <style>
                        .select2-container--default .select2-selection--multiple {
                            max-height: 110px !important;
                            overflow-y: auto !important;
                            border-radius: 8px !important;
                        }
                        .select2-container--default .select2-selection--multiple .select2-selection__choice {
                            font-size: 11px;
                            padding: 2px 8px;
                            margin-top: 4px;
                            margin-right: 4px;
                            background-color: #eaecf4;
                            border: 1px solid #d1d3e2;
                            color: #4e73df;
                            font-weight: 600;
                        }
                    </style>

                    <div id="merchantSelectGroup" class="mb-3" style="display: none;">
                        <label class="form-label small fw-bold text-muted">Select Merchant(s)</label>
                        <select class="form-control select2" name="ref_merchantId[]" id="global_merchant" multiple="multiple" style="width: 100%;">
                            <?php foreach ($merchants as $m): ?>
                                <option value="<?= $m->id ?>"><?= $m->id ?> | <?= $m->c_name ?> | <?= $m->c_email ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge bg-light text-muted px-2 py-1 mr-2">1</span>
                                <span class="small fw-bold text-muted">CURRENT CONFIGURATION (FILTER)</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label smaller fw-bold text-muted mb-1">Channel Group</label>
                                <select class="form-control select2" name="current_group" id="global_current_group" required>
                                    <option value="">Select group</option>
                                    <?php foreach ($channel_groups as $chg): ?>
                                        <option value="<?= $chg->c_channelGroup ?>"><?= $chg->c_channelGroup ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label smaller fw-bold text-muted mb-1">External ID Default (Optional)</label>
                                <select class="form-control select2" name="current_externalId" id="global_current_external" disabled>
                                    <option value="">All External IDs</option>
                                    <?php foreach ($channel_external_id_defaults as $ecd): ?>
                                        <option value="<?= $ecd->c_externalIdDefault ?>"><?= $ecd->c_externalIdDefault ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label smaller fw-bold text-muted mb-1">Specific Channel ID (Optional)</label>
                                <select class="form-control select2" name="current_cashinChannelId" id="global_current_channel" disabled>
                                    <option value="">All Channel IDs</option>
                                </select>
                            </div>
                            <div class="mb-0">
                                <label class="form-label smaller fw-bold text-muted mb-1">Current Status (Optional)</label>
                                <select class="form-control select2" name="current_status" id="global_current_status">
                                    <option value="">All Statuses</option>
                                    <option value="Active">Active</option>
                                    <option value="Not Active">Not Active</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <hr class="my-4 border-dashed">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge bg-primary-soft text-primary px-2 py-1 mr-2">2</span>
                                <span class="small fw-bold text-primary">NEW CONFIGURATION (TARGET)</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label smaller fw-bold text-muted mb-1">New Channel Group</label>
                                <select class="form-control select2" name="new_group" id="global_new_group" required>
                                    <option value="">Select group</option>
                                    <?php foreach ($channel_groups as $chg): ?>
                                        <option value="<?= $chg->c_channelGroup ?>"><?= $chg->c_channelGroup ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label smaller fw-bold text-muted mb-1">New External ID Default (Optional)</label>
                                <select class="form-control select2" name="new_externalId" id="global_new_external" disabled>
                                    <option value="">Don't Update (Keep Original)</option>
                                    <?php foreach ($channel_external_id_defaults as $ecd): ?>
                                        <option value="<?= $ecd->c_externalIdDefault ?>"><?= $ecd->c_externalIdDefault ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label smaller fw-bold text-muted mb-1">New Specific Channel ID (Optional)</label>
                                <select class="form-control select2" name="new_cashinChannelId" id="global_new_channel" disabled>
                                    <option value="">Don't Update (Keep Original)</option>
                                </select>
                            </div>
                            <div class="mb-0">
                                <label class="form-label smaller fw-bold text-muted mb-1">New Status (Optional)</label>
                                <select class="form-control select2" name="new_status" id="global_new_status">
                                    <option value="">Don't Update (Keep Original)</option>
                                    <option value="Active">Active</option>
                                    <option value="Not Active">Not Active</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 justify-content-end px-4">
                    <button type="button" class="btn-dt-cancel" data-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn-dt-apply px-4">UPDATE ALL MERCHANTS</button>
                </div>
            </form>
        </div>
    </div>
</div>
