<!-- ── Add Channel Modal ── -->
<div class="modal fade" id="addChanelModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 600px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header border-0 bg-primary text-white p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px;">
                        <i class="fas fa-plus fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title font-weight-bold text-white mb-0">Add Cash-In Channel</h5>
                        <p class="text-white-50 small mb-0">Create new incoming payment method route</p>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('channel/cashin/add'); ?>" method="post">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <div class="modal-body p-4 bg-light">
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="dt-more-label mb-2">Channel ID</label>
                            <input type="text" class="dt-more-input" required name="id" placeholder="e.g. bca_va">
                        </div>
                        <div class="col-md-6">
                            <label class="dt-more-label mb-2">Channel Group</label>
                            <input type="text" class="dt-more-input" required name="chanelgroup" placeholder="e.g. va">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="dt-more-label mb-2">Description</label>
                        <textarea class="dt-more-input" name="description" rows="2" placeholder="e.g. BCA Virtual Account"></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="dt-more-label mb-2">External Default</label>
                            <input type="text" class="dt-more-input" required name="externaldefault" placeholder="e.g. ifp">
                        </div>
                        <div class="col-md-6">
                            <label class="dt-more-label mb-2">Fee Type</label>
                            <select class="dt-more-select" required name="feetype">
                                <option value="" disabled selected>Select fee type</option>
                                <option value="Fixed">Fixed</option>
                                <option value="Percetange">Percentage</option>
                                <option value="Both">Both</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label class="dt-more-label mb-2">Fee Value</label>
                            <input type="text" class="input-rupiah dt-more-input" required name="fee" placeholder="0">
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label class="dt-more-label mb-2">Amount Min</label>
                            <input type="text" class="input-rupiah dt-more-input" required name="amountmin" placeholder="10000">
                        </div>
                        <div class="col-md-4">
                            <label class="dt-more-label mb-2">Amount Max</label>
                            <input type="text" class="input-rupiah dt-more-input" required name="amountmax" placeholder="50000000">
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="dt-more-label mb-2">Settlement Interval (Days)</label>
                        <input type="number" class="dt-more-input" required name="settlementinterval" value="1">
                    </div>
                </div>
                <div class="modal-footer px-4 py-3 border-0 bg-white justify-content-end">
                    <button type="button" class="btn-dt-cancel mr-2" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn-dt-apply px-4"><i class="fas fa-plus mr-2"></i> Add Channel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── Edit Channel Modal ── -->
<div class="modal fade" id="editChanelModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 600px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header border-0 bg-primary text-white p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px;">
                        <i class="fas fa-edit fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title font-weight-bold text-white mb-0">Edit Cash-In Channel</h5>
                        <p class="text-white-50 small mb-0">Update channel parameters and limits</p>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('channel/cashin/edit'); ?>" method="post">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <input type="hidden" name="pk_id" id="edit_pk_id">
                <div class="modal-body p-4 bg-light">
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="dt-more-label mb-2">Channel ID</label>
                            <input type="text" class="dt-more-input" required name="id" id="edit_id" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="dt-more-label mb-2">Channel Group</label>
                            <input type="text" class="dt-more-input" required name="chanelgroup" id="edit_chanelgroup">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="dt-more-label mb-2">Description</label>
                        <textarea class="dt-more-input" name="description" rows="2" id="edit_description"></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="dt-more-label mb-2">External Default</label>
                            <input type="text" class="dt-more-input" required name="externaldefault" id="edit_externaldefault">
                        </div>
                        <div class="col-md-6">
                            <label class="dt-more-label mb-2">Fee Type</label>
                            <select class="dt-more-select" required name="feetype" id="edit_feetype">
                                <option value="Fixed">Fixed</option>
                                <option value="Percetange">Percentage</option>
                                <option value="Both">Both</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label class="dt-more-label mb-2">Fee Value</label>
                            <input type="text" class="input-rupiah dt-more-input" required name="fee" id="edit_fee">
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label class="dt-more-label mb-2">Amount Min</label>
                            <input type="text" class="input-rupiah dt-more-input" required name="amountmin" id="edit_amountmin">
                        </div>
                        <div class="col-md-4">
                            <label class="dt-more-label mb-2">Amount Max</label>
                            <input type="text" class="input-rupiah dt-more-input" required name="amountmax" id="edit_amountmax">
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="dt-more-label mb-2">Settlement Interval (Days)</label>
                        <input type="number" class="dt-more-input" required name="settlementinterval" id="edit_settlementinterval">
                    </div>
                </div>
                <div class="modal-footer px-4 py-3 border-0 bg-white justify-content-end">
                    <button type="button" class="btn-dt-cancel mr-2" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn-dt-apply px-4"><i class="fas fa-save mr-2"></i> Update Channel</button>
                </div>
            </form>
        </div>
    </div>
</div>
