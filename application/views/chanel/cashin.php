<!-- Begin Page Content -->
<div>

    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title"><?= $title; ?></h4>
            <p class="dt-page-subtitle">Manage and configure available cash-in payment channels and fee structures.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn-dt-action btn-dt-action-primary border-0 d-flex align-items-center shadow-sm" id="toggleGuideBtn" >
                <i class="fas fa-book-open mr-2"></i> <span class="d-none d-md-block">Instructions Guide</span>
            </button>
        </div>
    </div>

    <!-- ── Toggleable Page Instructional Drawer ── -->
    <div class="drawer-overlay" id="instructionOverlay"></div>
    <div class="drawer-right" id="instructionDrawer">
        <div class="drawer-header">
            <h6 class="drawer-title"><i class="fas fa-book mr-2"></i> Cash In Channels Guide</h6>
            <button type="button" class="drawer-close" id="closeDrawerBtn">&times;</button>
        </div>
        <div class="drawer-body">
            <p class="drawer-desc">This page allows administrators to manage and configure entry routes for customer deposits, including VA, E-Wallet, and QRIS.</p>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-arrow-alt-circle-down text-primary mr-2"></i> Deposit Setup</div>
                <p class="drawer-card-text">Configure cash-in channels such as Virtual Accounts, QRIS, and E-Wallets. Define min/max amount constraints per transaction.</p>
            </div>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-server text-primary mr-2"></i> External ID Default</div>
                <p class="drawer-card-text">Set the default external provider (routing/upstream provider key) for incoming transactions of each channel.</p>
            </div>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-percentage text-primary mr-2"></i> Fees & Costs</div>
                <p class="drawer-card-text">Configure either Fixed (flat rate) or Percentage-based fees applied to incoming transactions, along with settlement intervals.</p>
            </div>
        </div>
    </div>

    <!-- ── KPI Summary Cards ── -->
    

    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">

        <!-- ── Toolbar ── -->
        <!-- ── Toolbar ── -->
        <div class="dt-toolbar">
            <div class="dt-search-wrapper flex-grow-1 mb-2 mb-md-0" style="min-width: 280px;">
                <i class="fas fa-search dt-search-icon"></i>
                <input type="text" id="cashinGlobalSearch" class="dt-search-input" placeholder="Search by Channel, ID, or Category..." value="<?= $this->session->userdata('search_channel'); ?>">
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
                            <button type="button" id="cashinMoreApply" class="btn-dt-apply btn-dt-action-primary shadow-sm">
                                <i class="fas fa-check mr-1 mr-2"></i> APPLY FILTER
                            </button>
                            <button type="button" id="cashinMoreFiltersClose" class="btn-dt-cancel btn-dt-secondary">
                                CANCEL
                            </button>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn-dt-action btn-dt-action-success border-0 text-decoration-none d-flex align-items-center" data-toggle="modal" data-target=".bd-example-modal-lg" >
                    <i class="fas fa-plus"></i> <span class="d-none d-md-block">New Cash In Channel</span>
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
            <table class="table dt-table mb-0" id="cashinTable" style="width:100%">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>ID</th>
                        <th>CHANNEL GROUP</th>
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
    </div>
</div>

<!-- ── Modal Tambah Chanel ── -->
<div class="modal fade bd-example-modal-lg" id="addChanelModal" tabindex="-1" role="dialog" aria-labelledby="addChanelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <!-- Header Legacy Migrated -->
            <div class="modal-header modal-header-primary border-0 mh-premium">
                <div class="d-flex align-items-center">
                    <div class="mh-icon-badge">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="mh-title-wrap">
                        <h6 class="mh-title" id="addChanelModalLabel">New Cash In Channel</h6>
                        <small class="mh-subtitle">Manage and process information details</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="<?= base_url('channel/cashin/create'); ?>">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <div class="modal-body p-0 bg-light">
                    <div class="d-flex g-0 w-100 flex-column flex-lg-row">
                        <!-- Left Information Sidebar -->
                        <div class="col-lg-4 p-4 d-flex flex-column justify-content-between mb-0" style="background: var(--bg-body); border-right: 1px solid rgba(255,255,255,0.05);">
                            <div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm" style="width: 40px; height: 40px;">
                                        <i class="fas fa-arrow-alt-circle-down fa-lg"></i>
                                    </div>
                                    <h6 class="fw-bold text-success mb-0" style="font-size: 15px;">Cash In Guide</h6>
                                </div>
                                <p class="text-muted small mb-4" style="font-size: 12px; line-height: 1.5;">Establish entry channels for customer deposits, Va dynamic routes, or static qris.</p>
                                
                                <div class="d-flex flex-column gap-3">
                                    <div class="p-3 rounded-4 shadow-sm border-0 mb-3" style="background-color: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important; border-radius: 12px;">
                                        <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12px;"><i class="fas fa-money-bill-wave text-warning mr-2"></i> 1. Fees Setup</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.4;">Percentage fee represents dynamic slice, while Fixed represents flat fee rate.</p>
                                    </div>
                                    <div class="p-3 rounded-4 shadow-sm border-0 mb-3" style="background-color: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important; border-radius: 12px;">
                                        <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12px;"><i class="fas fa-calendar-alt text-info mr-2"></i> 2. Settlement</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.4;">Number of days wait time before balance converts to merchant clearable funds (e.g. 1 day).</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Form Area -->
                        <div class="col-lg-8 p-4 bg-light mb-0 text-dark">
                            <?php if(validation_errors()): ?>
                                <div class="alert alert-danger mb-4 shadow-sm border-0 small">
                                    <i class="fas fa-exclamation-triangle "></i> <?= validation_errors(); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="dt-more-label mb-2">Channel ID</label>
                                    <input type="text" class="dt-more-input" required name="id" placeholder="e.g. MANDIRI_VA">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="dt-more-label mb-2">Channel Group</label>
                                    <input type="text" class="dt-more-input" required name="chanelgroup" placeholder="e.g. VIRTUAL_ACCOUNT">
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="dt-more-label mb-2">Description</label>
                                <textarea class="dt-more-input" name="description" rows="2" placeholder="Briefly describe this channel..."></textarea>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="dt-more-label mb-2">External Default</label>
                                    <input type="text" class="dt-more-input" required name="externaldefault" placeholder="Provider reference">
                                </div>
                                <div class="col-md-6">
                                    <label class="dt-more-label mb-2">Fee Type</label>
                                    <select class="dt-more-select" required name="feetype">
                                        <option value="">Select fee type</option>
                                        <option value="Fixed">Fixed</option>
                                        <option value="Percetange">Percentage</option>
                                        <option value="Both">Both</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <label class="dt-more-label mb-2">Fee Value</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text border-right-0" style="border-radius:8px 0 0 8px; font-size:12px;">Rp</span>
                                        </div>
                                        <input type="text" class="input-rupiah dt-more-input h-auto" required name="fee" style="border-radius:0 8px 8px 0;">
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <label class="dt-more-label mb-2">Amount Min</label>
                                    <input type="text" class="input-rupiah dt-more-input" required name="amountmin" value="10000">
                                </div>
                                <div class="col-md-4">
                                    <label class="dt-more-label mb-2">Amount Max</label>
                                    <input type="text" class="input-rupiah dt-more-input" required name="amountmax" value="10000000">
                                </div>
                            </div>

                            <div class="mb-0">
                                <label class="dt-more-label mb-2">Settlement Interval (Days)</label>
                                <input type="number" class="dt-more-input input-rupiah" required name="settlementinterval" value="1">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3 border-0 bg-white justify-content-end">
                    <button type="button" class="btn-dt-cancel mr-2" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn-dt-apply px-4">
                        <i class="fas fa-save mr-2"></i> Save New Channel
                    </button>
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
                    <div class="mh-icon-badge">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="mh-title-wrap">
                        <h6 class="mh-title" id="editChanelModalLabel">Edit Cash In Channel</h6>
                        <small class="mh-subtitle">Modify and update existing information</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="<?= base_url('channel/cashin/update'); ?>">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <div class="modal-body p-0 bg-light">
                    <div class="d-flex g-0 w-100 flex-column flex-lg-row">
                        <!-- Left Information Sidebar -->
                        <div class="col-lg-4 p-4 d-flex flex-column justify-content-between mb-0" style="background: var(--bg-body); border-right: 1px solid rgba(255,255,255,0.05);">
                            <div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm" style="width: 40px; height: 40px;">
                                        <i class="fas fa-info-circle fa-lg"></i>
                                    </div>
                                    <h6 class="fw-bold text-primary mb-0" style="font-size: 15px;">Modification Guide</h6>
                                </div>
                                <p class="text-muted small mb-4" style="font-size: 12px; line-height: 1.5;">Update payment channels. Please double check values before committing changes.</p>
                                
                                <div class="d-flex flex-column gap-3">
                                    <div class="p-3 rounded-4 shadow-sm border-0 mb-3" style="background-color: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important; border-radius: 12px;">
                                        <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12px;"><i class="fas fa-exclamation-circle text-warning mr-2"></i> Channel ID</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.4;">Channel IDs are read-only to maintain relational integrity in operational ledgers.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Form Area -->
                        <div class="col-lg-8 p-4 bg-light mb-0 text-dark">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="dt-more-label mb-2">Channel ID</label>
                                    <input type="text" class="dt-more-input bg-light" readonly required name="id" id="edit_id">
                                    <input type="hidden" name="pk_id" id="edit_pk_id">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="dt-more-label mb-2">Channel Group</label>
                                    <input type="text" class="dt-more-input" required name="chanelgroup" id="edit_chanelgroup">
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="dt-more-label mb-2">Description</label>
                                <textarea class="dt-more-input" name="description" rows="2" id="edit_description"></textarea>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="dt-more-label mb-2">External Default</label>
                                    <input type="text" class="dt-more-input" required name="externaldefault" id="edit_externaldefault">
                                </div>
                                <div class="col-md-6">
                                    <label class="dt-more-label mb-2">Fee Type</label>
                                    <select class="dt-more-select" required name="feetype" id="edit_feetype">
                                        <option value="" disabled>Select fee type</option>
                                        <option value="Fixed">Fixed</option>
                                        <option value="Percetange">Percentage</option>
                                        <option value="Both">Both</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <label class="dt-more-label mb-2">Fee Value</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text border-right-0" style="border-radius:8px 0 0 8px; font-size:12px;">Rp</span>
                                        </div>
                                        <input type="text" class="input-rupiah dt-more-input h-auto" required name="fee" id="edit_fee" style="border-radius:0 8px 8px 0;">
                                    </div>
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
                                <input type="number" class="dt-more-input input-rupiah" required name="settlementinterval" id="edit_settlementinterval">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3 border-0 bg-white justify-content-end">
                    <button type="button" class="btn-dt-cancel mr-2" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn-dt-apply px-4">
                        <i class="fas fa-save mr-2"></i> Update Channel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Instructions Guide drawer handlers
        $('#toggleGuideBtn').on('click', function() {
            $('#instructionDrawer').addClass('open');
            $('#instructionOverlay').addClass('open');
            $('body').css('overflow', 'hidden'); // Lock background scroll
        });

        $('#closeDrawerBtn, #instructionOverlay').on('click', function() {
            $('#instructionDrawer').removeClass('open');
            $('#instructionOverlay').removeClass('open');
            $('body').css('overflow', ''); // Unlock scroll
        });

        // Standardize DataTables for premium look
        var table = initServerDataTable('#cashinTable', "<?= base_url('channel/cashin') ?>", [
                {data: 'no', orderable: false, className: 'text-center'},
                {data: 'ref_cashinChannelId', className: 'font-weight-bold text-primary dt-id-column'},
                {data: 'c_channelGroup', render: function(data){
                    return '<span class="badge badge-light text-dark border px-2 py-1 text-uppercase" style="font-size:10px; letter-spacing:0.5px; border-radius:4px;">'+data+'</span>';
                }},
                {data: 'c_description', className: 'small text-muted'},
                {data: 'c_externalIdDefault', className: 'text-dark'},
                {data: 'c_feeType', render: function(data){
                    var cls = (data.toLowerCase() === 'fixed') ? 'text-info' : 'text-primary';
                    return '<span class="'+cls+' font-weight-bold" style="font-size:11px;">'+data.toUpperCase()+'</span>';
                }},
                {data: 'c_fee', className: 'font-weight-bold text-dark text-nowrap', render: function(data, type, row) {
                    return 'Rp ' + number_format(data, 0, ',', '.');
                }},
                {data: 'c_feePercetange', className: 'font-weight-bold text-dark text-nowrap', render: function(data, type, row) {
                    return  number_format(data, 0, ',', '.') + '%';
                }},
                {
                    data: null, 
                    orderable: false, 
                    className: 'text-center',
                    render: function(data, type, row) {
                        return `
                            <div class="dropdown">
                                <button class="btn btn-sm rounded-circle p-2 border-0 bg-transparent" type="button" data-toggle="dropdown" data-boundary="viewport" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right shadow border-0 py-2">
                                    <li>
                                        <button type="button" class="dropdown-item edit-cashin" 
                                            data-toggle="modal" data-target="#editChanelModal" 
                                            data-id="${row.id}" 
                                            data-channelid="${row.ref_cashinChannelId}"
                                            data-group="${row.c_channelGroup}"
                                            data-desc="${row.c_description || ''}"
                                            data-ext="${row.c_externalIdDefault || ''}"
                                            data-feetype="${row.c_feeType || ''}"
                                            data-fee="${row.c_fee || 0}"
                                            data-min="${row.c_amountMin || 10000}"
                                            data-max="${row.c_amountMax || 10000000}"
                                            data-interval="${row.c_settlementInterval || 1}">
                                            <i class="fas fa-edit text-primary mr-2"></i> Edit Channel
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" class="dropdown-item delete-cashin text-danger" data-id="${row.id}">
                                            <i class="fas fa-trash-alt mr-2"></i> Delete Channel
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        `;
                    }
                }
            ], {
                ajax: {
                    url: "<?= base_url('channel/cashin') ?>",
                    type: "POST",
                    data: function (d) {
                        var csrfName = $('meta[name="csrf-token-name"]').attr('content');
                        var csrfHash = $('meta[name="csrf-token-hash"]').attr('content');
                        if (csrfName && csrfHash) {
                            d[csrfName] = csrfHash;
                        }
                        d.channel_group = $('#filter_channel_group').val();
                        d.external_id = $('#filter_external_id').val();
                        d.search_channel = $('#cashinGlobalSearch').val() || '';
                    }
                }
            });

        // Global Search mapping
        $('#cashinGlobalSearch').on('input', debounce(function() { table.search(this.value).draw(); }, 400));

        // Edit button mapping
        $(document).on('click', '.edit-cashin', function() {
            $('#edit_pk_id').val($(this).data('id'));
            $('#edit_id').val($(this).data('channelid'));
            $('#edit_chanelgroup').val($(this).data('group'));
            $('#edit_description').val($(this).data('desc'));
            $('#edit_externaldefault').val($(this).data('ext'));
            
            var ft = ($(this).data('feetype') || '').toLowerCase();
            if (ft === 'fixed') {
                $('#edit_feetype').val('Fixed');
            } else if (ft === 'percentage' || ft === 'percetange') {
                $('#edit_feetype').val('Percetange');
            } else if (ft === 'both') {
                $('#edit_feetype').val('Both');
            } else {
                $('#edit_feetype').val('');
            }
            $('#edit_feetype').trigger('change');
            $('#edit_fee').val(Math.floor(parseFloat($(this).data('fee'))));
            $('#edit_amountmin').val(Math.floor(parseFloat($(this).data('min'))));
            $('#edit_amountmax').val(Math.floor(parseFloat($(this).data('max'))));
            $('#edit_settlementinterval').val(Math.floor(parseFloat($(this).data('interval'))));
        });

        // Delete button mapping
        $(document).on('click', '.delete-cashin', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to delete cash-in channel " + id + ". This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                customClass: {
                    popup: 'swal2-premium-popup',
                    confirmButton: 'swal2-premium-confirm bg-danger border-danger',
                    cancelButton: 'swal2-premium-cancel'
                },
                buttonsStyling: false,
                confirmButtonText: '<i class="fas fa-trash-alt mr-2"></i> Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "<?= base_url('channel/cashin/delete/') ?>" + id;
                }
            });
        });

        // ── More Filters dropdown ──
        var $moreBtn   = $('#cashinMoreFiltersBtn');
        var $morePanel = $('#cashinMoreFiltersPanel');
        var $moreClose = $('#cashinMoreFiltersClose');
        var $moreApply = $('#cashinMoreApply');
        var $moreClear = $('#cashinMoreClear');

        $moreBtn.on('click', function(e) {
            e.stopPropagation();
            var isOpen = $morePanel.hasClass('dt-panel-open');
            $morePanel.toggleClass('dt-panel-open', !isOpen);
            $moreBtn.toggleClass('dt-open', !isOpen);
        });

        $moreClose.on('click', function() {
            $morePanel.removeClass('dt-panel-open');
            $moreBtn.removeClass('dt-open');
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('.dt-more-filters-wrapper').length) {
                $morePanel.removeClass('dt-panel-open');
                $moreBtn.removeClass('dt-open');
            }
        });

        $('#cashinMoreFiltersPanel select').not('.select2-hidden-accessible').each(function () {
            $(this).select2({
                width: '100%',
                dropdownAutoWidth: true,
                dropdownParent: $('body'),
                minimumResultsForSearch: 0
            });
        });

        function updateFilterBadge() {
            let count = 0;
            $('.filter-select').each(function() {
                if ($(this).val()) count++;
            });
            const $badge = $('#cashinFilterBadge');
            if (count > 0) {
                $badge.text(count).show();
                $moreBtn.addClass('dt-more-filters-active');
            } else {
                $badge.hide();
                $moreBtn.removeClass('dt-more-filters-active');
            }
        }

        $moreApply.on('click', function() {
            updateFilterBadge();
            table.ajax.reload(null, false);
            $morePanel.removeClass('dt-panel-open');
            $moreBtn.removeClass('dt-open');
        });

        $moreClear.on('click', function() {
            $('.filter-select').val('').trigger('change.select2');
            fetchMasterFilterOptions('');
            updateFilterBadge();
            table.ajax.reload(null, false);
        });

        // Cascading logic for Master Channel Filters
        $('#filter_channel_group').on('change', function() {
            const group = $(this).val();
            $('#filter_external_id').val('').trigger('change.select2');
            fetchMasterFilterOptions(group);
        });

        function fetchMasterFilterOptions(group) {
            var csrfName = $('meta[name="csrf-token-name"]').attr('content') || '<?= $this->security->get_csrf_token_name(); ?>';
            var csrfHash = $('meta[name="csrf-token-hash"]').attr('content') || '<?= $this->security->get_csrf_hash(); ?>';
            
            $('#filter_external_id').prop('disabled', true).html('<option value="">Loading...</option>').trigger('change.select2');

            $.ajax({
                url: "<?= base_url('channel/get-master-filter-options') ?>",
                type: "POST",
                data: { type: 'cashin', group: group, [csrfName]: csrfHash },
                dataType: "json",
                success: function(data) {
                    let providerOptions = '<option value="">All External IDs</option>';
                    const currentProvider = $('#filter_external_id').val();
                    data.providers.forEach(function(item) {
                        providerOptions += `<option value="${item}">${item}</option>`;
                    });
                    $('#filter_external_id').html(providerOptions).prop('disabled', false).trigger('change.select2');
                },
                error: function() {
                    $('#filter_external_id').prop('disabled', false).html('<option value="">All External IDs</option>').trigger('change.select2');
                }
            });
        }
    });
</script>



