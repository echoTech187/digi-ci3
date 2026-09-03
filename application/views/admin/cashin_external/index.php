<div>
    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title">Cashin External Merchant</h4>
            <p class="dt-page-subtitle">Centralized configuration for all merchant cashin channels</p>
        </div>
        <div class="d-flex" style="gap:10px;">
            <button type="button" class="btn-dt-action btn-dt-action-success" data-toggle="modal" data-target="#globalUpdateModal">
                <i class="fas fa-globe mr-1 mr-2"></i> Edit Mapping
            </button>
            <a href="<?= base_url('external/cashin/create'); ?>" class="btn-dt-action btn-dt-action-success border-0 text-decoration-none d-flex align-items-center">
                <i class="fas fa-plus mr-1 mr-2"></i> Add Mapping
            </a>
        </div>
    </div>

    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">
        <!-- ── Toolbar ── -->
        <div class="dt-toolbar">
            <div class="dt-search-wrapper flex-grow-1 mb-2 mb-md-0" style="min-width: 280px;">
                <i class="fas fa-search dt-search-icon"></i>
                <input type="text" id="dt-search" class="dt-search-input" placeholder="Search merchant or channel..." value="<?= htmlspecialchars($this->session->userdata('search_external_cashin')); ?>">
            </div>

            <!-- RIGHT: Filters & Actions -->
            <div class="dt-toolbar-filters d-flex align-items-center gap-2">
                <!-- More Filters Trigger -->
                <div class="dt-filter-group dt-more-filters-wrapper">
                    <button type="button" id="cashinMoreFiltersBtn" class="dt-more-filters-btn">
                        <i class="fas fa-sliders-h mr-1 mr-2"></i> Filters
                        <span class="dt-more-badge" id="cashinFilterBadge" style="display: none;">0</span>
                        <i class="fas fa-chevron-down ml-1 dt-more-arrow"></i>
                    </button>

                    <!-- Dropdown Panel -->
                    <div class="dt-more-panel" id="cashinMoreFiltersPanel">
                        <div class="dt-more-panel-header">
                            <span class="dt-more-panel-title"><i class="fas fa-filter mr-1 mr-2"></i> Advanced Filters</span>
                            <a href="javascript:void(0)" id="cashinMoreClear" class="dt-more-clear">Clear All</a>
                        </div>

                        <div class="dt-more-panel-body">
                            <!-- Merchant -->
                            <div class="dt-more-field">
                                <label class="dt-more-label"><i class="fas fa-store mr-1 mr-2"></i> Merchant</label>
                                <select id="filter_merchant" class="dt-more-select filter-select">
                                    <option value="">All Merchants</option>
                                    <?php foreach ($merchants as $m): ?>
                                        <option value="<?= $m->id ?>"><?= $m->c_name ?> (ID: <?= $m->id ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

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

                            <!-- Provider -->
                            <div class="dt-more-field">
                                <label class="dt-more-label"><i class="fas fa-server mr-1 mr-2"></i> External ID Default</label>
                                <select id="filter_provider" class="dt-more-select filter-select">
                                    <option value="">All External IDs</option>
                                    <?php foreach ($channel_external_id_defaults as $prd): ?>
                                        <option value="<?= $prd->c_externalIdDefault ?>"><?= $prd->c_externalIdDefault ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <!-- Channel ID -->
                            <div class="dt-more-field">
                                <label class="dt-more-label"><i class="fas fa-hashtag mr-1 mr-2"></i> Channel ID</label>
                                <select id="filter_channel_id" class="dt-more-select filter-select">
                                    <option value="">All Channel IDs</option>
                                    <?php foreach ($channel_ids as $cid): ?>
                                        <option value="<?= $cid->id ?>"><?= $cid->id ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <!-- Status -->
                            <div class="dt-more-field">
                                <label class="dt-more-label"><i class="fas fa-info-circle mr-1 mr-2"></i> Status</label>
                                <select id="filter_status" class="dt-more-select filter-select">
                                    <option value="">All Statuses</option>
                                    <option value="Active">Active</option>
                                    <option value="Not Active">Not Active</option>
                                </select>
                            </div>
                        </div>

                        <div class="dt-more-panel-footer">
                            <button type="button" class="btn-dt-action btn-dt-action-primary w-100" id="cashinMoreApply">Apply Filters</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Container -->
        <div class="table-responsive">
            <table class="table dt-table mb-0 align-middle" id="cashinTable" style="width: 100%;">
                <thead>
                    <tr>
                        <th class="ps-4">NO</th>
                        <th>MERCHANT</th>
                        <th>CHANNEL INFO</th>
                        <th>FEES CONFIG</th>
                        <th class="text-center">SETTLEMENT</th>
                        <th class="text-right">LIMITS (MIN / MAX)</th>
                        <th class="text-center">STATUS</th>
                        <th class="text-center pe-4" style="width: 80px;">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ── Include Modals (Partial) ── -->
<?php $this->load->view('admin/cashin_external/partials/modal_global_update'); ?>

<!-- ── Include JavaScript Assets ── -->
<script src="<?= base_url('assets/js/external_mapping_helper.js'); ?>"></script>
<script src="<?= base_url('assets/js/cashin_external.js'); ?>"></script>
