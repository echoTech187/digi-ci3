<!-- Begin Page Content -->
<div>

    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title"><?= $title; ?></h4>
            <p class="dt-page-subtitle">Manage payout channels, external provider links, and transaction fee logic.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            </div>
    </div>

    <!-- ── KPI Summary Cards ── -->
    

    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">

        <!-- ── Toolbar ── -->
        <div class="dt-toolbar">
            <div class="dt-search-wrapper flex-grow-1 mb-2 mb-md-0" style="min-width: 280px;">
                <i class="fas fa-search dt-search-icon"></i>
                <input type="text" id="cashoutGlobalSearch" class="dt-search-input" placeholder="Search by Channel, Provider, or Fee..." value="<?= $this->session->userdata('search_channel_out'); ?>">
            </div>

            <!-- RIGHT: Filters & Actions -->
            <div class="dt-toolbar-filters d-flex align-items-center gap-2">
                <!-- More Filters Trigger -->
                <div class="dt-filter-group dt-more-filters-wrapper">
                    <button type="button" id="cashoutMoreFiltersBtn" class="dt-more-filters-btn">
                        <i class="fas fa-sliders-h mr-1 mr-2"></i> Filters
                        <span class="dt-more-badge" id="cashoutFilterBadge" style="display: none;">0</span>
                        <i class="fas fa-chevron-down ml-1 dt-more-arrow"></i>
                    </button>

                    <!-- Dropdown Panel -->
                    <div class="dt-more-panel" id="cashoutMoreFiltersPanel">
                        <div class="dt-more-panel-header">
                            <span class="dt-more-panel-title"><i class="fas fa-filter mr-1 mr-2"></i> Advanced Filters</span>
                            <a href="javascript:void(0)" id="cashoutMoreClear" class="dt-more-clear">Clear All</a>
                        </div>

                        <div class="dt-more-panel-body">
                            <!-- Channel Group -->
                            <div class="dt-more-field">
                                <label class="dt-more-label"><i class="fas fa-layer-group mr-1 mr-2"></i> Channel Group</label>
                                <select id="filter_channel_group" class="dt-more-select filter-select">
                                    <option value="">All Groups</option>
                                    <?php foreach ($channel_groups as $cg): ?>
                                        <option value="<?= $cg->c_channelGroup ?>"><?= $cg->c_channelGroup ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- External ID Default -->
                            <div class="dt-more-field">
                                <label class="dt-more-label"><i class="fas fa-server mr-1 mr-2"></i> External ID Default</label>
                                <select id="filter_external_id" class="dt-more-select filter-select">
                                    <option value="">All External IDs</option>
                                    <?php foreach ($channel_external_id_defaults as $prd): ?>
                                        <option value="<?= $prd->c_externalIdDefault ?>"><?= $prd->c_externalIdDefault ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="dt-more-panel-footer">
                            <button type="button" id="cashoutMoreApply" class="btn-dt-apply btn-dt-action-primary shadow-sm">
                                <i class="fas fa-check mr-1 mr-2"></i> APPLY FILTER
                            </button>
                            <button type="button" id="cashoutMoreFiltersClose" class="btn-dt-cancel btn-dt-secondary">
                                CANCEL
                            </button>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn-dt-action btn-dt-action-success border-0 text-decoration-none d-flex align-items-center" data-toggle="modal" data-target=".bd-example-modal-lg" >
                    <i class="fas fa-plus"></i> <span class="d-none d-md-block">New Cash Out Channel</span>
                </button>
            </div>
        </div>

        <!-- Alerts Standardized to Swal2 Premium -->
        <script>
            $(document).ready(function() {
                <?php 
                $successMsg = $this->session->flashdata('success') ?: $this->session->flashdata('message');
                if ($successMsg) : 
                ?>
                    Swal.fire({
                        title: 'Success!',
                        text: '<?= $successMsg; ?>',
                        icon: 'success',
                        customClass: {
                            popup: 'swal2-premium-popup',
                            confirmButton: 'swal2-premium-confirm'
                        },
                        buttonsStyling: false
                    });
                <?php endif; ?>

                <?php if ($this->session->flashdata('error')) : ?>
                    Swal.fire({
                        title: 'Error!',
                        html: '<?= trim(str_replace(["\r", "\n"], '', $this->session->flashdata('error'))); ?>',
                        icon: 'error',
                        customClass: {
                            popup: 'swal2-premium-popup',
                            confirmButton: 'swal2-premium-confirm'
                        },
                        buttonsStyling: false
                    });
                <?php endif; ?>
            });
        </script>

        <!-- ── Table ── -->
        <div class="table-responsive">
            <table class="table dt-table mb-0" id="cashoutTable" style="width:100%">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>ID</th>
                        <th>CHANEL GROUP</th>
                        <th>DESCRIPTION</th>
                        <th>EXTERNAL ID DEFAULT</th>
                        <th>FEE TYPE</th>
                        <th>FEE</th>
                        <th>FEE PERCENTAGE</th>
                        <th class="text-center">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Loaded via DataTables AJAX -->
                </tbody>
            </table>
        </div>

        <!-- ── Custom Footer ── -->
        <!-- Will be dynamically populated via DataTables dom/drawCallback -->
    </div>
</div>

<!-- ── Modal Tambah Chanel ── -->
<div class="modal fade bd-example-modal-lg" id="addCashoutModal" tabindex="-1" role="dialog" aria-labelledby="addCashoutLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header modal-header-primary border-0 mh-premium">
                <div class="d-flex align-items-center">
                    <div class="mh-icon-badge"><i class="fas fa-star"></i></div>
                    <div class="mh-title-wrap">
                        <h6 class="mh-title" id="addCashoutLabel">New Cash Out Channel</h6>
                        <small class="mh-subtitle">Manage and process information details</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;"><span aria-hidden="true">&times;</span></button>
            </div>
            <form method="post" action="<?= base_url('channel/cashout/create'); ?>">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <div class="modal-body p-0 bg-light">
                    <div class="d-flex g-0 w-100 flex-column flex-lg-row">
                        <div class="col-lg-4 p-4 d-flex flex-column mb-0" style="background: var(--bg-body); border-right: 1px solid rgba(255,255,255,0.05);">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width:40px;height:40px;flex-shrink:0;"><i class="fas fa-arrow-alt-circle-up fa-lg"></i></div>
                                <h6 class="fw-bold text-danger mb-0" style="font-size:15px;">Cash Out Guide</h6>
                            </div>
                            <p class="text-muted small mb-3" style="font-size:12px;line-height:1.5;">Configure outbound payout channels for external bank or provider endpoints.</p>
                            <div class="p-3 mb-3" style="border-radius:12px;">
                                <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size:12px;"><i class="fas fa-money-check-alt text-warning mr-2"></i> 1. Fee Type</h6>
                                <p class="text-muted mb-0" style="font-size:11px;line-height:1.4;">Fixed: flat deduction per transaction. percetange: dynamic slice of payout amount.</p>
                            </div>
                            <div class="p-3" style="border-radius:12px;">
                                <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size:12px;"><i class="fas fa-shield-alt text-info mr-2"></i> 2. Amount Limits</h6>
                                <p class="text-muted mb-0" style="font-size:11px;line-height:1.4;">Set min/max boundaries to protect against fraudulent payout requests.</p>
                            </div>
                        </div>
                        <div class="col-lg-8 p-4 bg-light mb-0 text-dark">
                            <?php if(validation_errors()): ?>
                                <div class="alert alert-danger mb-4 shadow-sm border-0 small"><i class="fas fa-exclamation-triangle"></i> <?= validation_errors(); ?></div>
                            <?php endif; ?>
                            <div class="row">
                                <div class="col-md-6 mb-3"><label class="dt-more-label mb-2">Channel ID</label><input type="text" class="dt-more-input" required name="id" placeholder="e.g. BANK_TRANSFER_MANDIRI"></div>
                                <div class="col-md-6 mb-3"><label class="dt-more-label mb-2">Channel Group</label><input type="text" class="dt-more-input" required name="chanelgroup" placeholder="e.g. BANK_TRANSFER"></div>
                            </div>
                            <div class="mb-3"><label class="dt-more-label mb-2">Description</label><textarea class="dt-more-input" name="description" rows="2" placeholder="Detail channel configuration..."></textarea></div>
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0"><label class="dt-more-label mb-2">External Default</label><input type="text" class="dt-more-input" required name="externaldefault" placeholder="External provider key"></div>
                                <div class="col-md-6"><label class="dt-more-label mb-2">Fee Type</label><select class="dt-more-select" required name="feetype"><option value="" selected disabled>Select fee type</option><option value="Fixed">Fixed</option><option value="Percetange">percetange</option><option value="Both">Both</option></select></div>
                            </div>
                            <div class="row mb-0">
                                <div class="col-md-4 mb-3 mb-md-0"><label class="dt-more-label mb-2">Fee Value</label><div class="input-group"><div class="input-group-prepend"><span class="input-group-text border-right-0" style="border-radius:8px 0 0 8px;font-size:12px;">Rp</span></div><input type="text" class="input-rupiah form-control" class="dt-more-input h-auto" required name="fee" style="border-radius:0 8px 8px 0;"></div></div>
                                <div class="col-md-4 mb-3 mb-md-0"><label class="dt-more-label mb-2">Amount Min</label><input type="text" class="input-rupiah form-control" class="dt-more-input" required name="amountmin" value="10000"></div>
                                <div class="col-md-4"><label class="dt-more-label mb-2">Amount Max</label><input type="text" class="input-rupiah form-control" class="dt-more-input" required name="amountmax" value="50000000"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3 border-0 bg-white justify-content-end">
                    <button type="button" class="btn-dt-cancel mr-2" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn-dt-apply px-4"><i class="fas fa-save mr-2"></i> Save New Cash Out</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── Modal Edit Chanel ── -->
<div class="modal fade bd-example-modal-lg" id="editChanelModal" tabindex="-1" role="dialog" aria-labelledby="editChanelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header modal-header-primary border-0 mh-premium">
                <div class="d-flex align-items-center">
                    <div class="mh-icon-badge"><i class="fas fa-edit"></i></div>
                    <div class="mh-title-wrap">
                        <h6 class="mh-title" id="editChanelModalLabel">Edit Cash Out Channel</h6>
                        <small class="mh-subtitle">Modify and update existing information</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;"><span aria-hidden="true">&times;</span></button>
            </div>
            <form method="post" action="<?= base_url('channel/cashout/update'); ?>">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <div class="modal-body p-0 bg-light">
                    <div class="d-flex g-0 w-100 flex-column flex-lg-row">
                        <div class="col-lg-4 p-4 d-flex flex-column mb-0" style="background: var(--bg-body); border-right: 1px solid rgba(255,255,255,0.05);">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width:40px;height:40px;flex-shrink:0;"><i class="fas fa-info-circle fa-lg"></i></div>
                                <h6 class="fw-bold text-primary mb-0" style="font-size:15px;">Modification Guide</h6>
                            </div>
                            <p class="text-muted small mb-3" style="font-size:12px;line-height:1.5;">Update payout channel configs. Verify all values carefully before saving.</p>
                            <div class="p-3" style="border-radius:12px;">
                                <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size:12px;"><i class="fas fa-lock text-warning mr-2"></i> Channel ID (Read-only)</h6>
                                <p class="text-muted mb-0" style="font-size:11px;line-height:1.4;">Channel IDs cannot be changed to maintain ledger data integrity.</p>
                            </div>
                        </div>
                        <div class="col-lg-8 p-4 bg-light mb-0 text-dark">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="dt-more-label mb-2">Channel ID</label>
                                    <input type="text" class="dt-more-input bg-light" readonly required name="id" id="edit_id">
                                    <input type="hidden" name="pk_id" id="edit_pk_id">
                                </div>
                                <div class="col-md-6 mb-3"><label class="dt-more-label mb-2">Channel Group</label><input type="text" class="dt-more-input" required name="chanelgroup" id="edit_chanelgroup"></div>
                            </div>
                            <div class="mb-3"><label class="dt-more-label mb-2">Description</label><textarea class="dt-more-input" name="description" rows="2" id="edit_description"></textarea></div>
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3 mb-md-0"><label class="dt-more-label mb-2">External Default</label><input type="text" class="dt-more-input" required name="externaldefault" id="edit_externaldefault"></div>
                                <div class="col-md-6"><label class="dt-more-label mb-2">Fee Type</label>
                                <select class="dt-more-select" required name="feetype" id="edit_feetype">
                                    <option value="" disabled>Select fee type</option>
                                    <option value="Fixed">Fixed</option>
                                    <option value="Percetange">Percentange</option>
                                    <option value="Both">Both</option></select>
                                </div>
                            </div>
                            <div class="row mb-0">
                                <div class="col-md-4 mb-3 mb-md-0"><label class="dt-more-label mb-2">Fee Value</label><div class="input-group"><div class="input-group-prepend"><span class="input-group-text border-right-0" style="border-radius:8px 0 0 8px;font-size:12px;">Rp</span></div><input type="text" class="input-rupiah form-control" class="dt-more-input h-auto" required name="fee" id="edit_fee" style="border-radius:0 8px 8px 0;"></div></div>
                                <div class="col-md-4 mb-3 mb-md-0"><label class="dt-more-label mb-2">Amount Min</label><input type="text" class="input-rupiah form-control" class="dt-more-input" required name="amountmin" id="edit_amountmin"></div>
                                <div class="col-md-4"><label class="dt-more-label mb-2">Amount Max</label><input type="text" class="input-rupiah form-control" class="dt-more-input" required name="amountmax" id="edit_amountmax"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3 border-0 bg-white justify-content-end">
                    <button type="button" class="btn-dt-cancel mr-2" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn-dt-apply px-4"><i class="fas fa-save mr-2"></i> Update Cash Out</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        </script>



