<!-- Begin Page Content -->
<div>

    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title text-dark fw-700">Merchant Supervisor</h4>
            <p class="dt-page-subtitle text-muted">Manage and organize merchant supervisors and their assigned merchants.</p>
        </div>
        <div class="d-flex" style="gap:10px;">
            <button type="button" class="btn-dt-action btn-dt-action-primary toggle-guide-btn" id="toggleGuideBtn" style="background-color: #6f42c1; border-color: #6f42c1; color: #fff;">
                <i class="fas fa-book-open mr-2"></i> Instructions Guide
            </button>
        </div>
    </div>

    <!-- ── Toggleable Page Instructional Drawer ── -->
    <div class="drawer-overlay" id="instructionOverlay"></div>
    <div class="drawer-right" id="instructionDrawer">
        <div class="drawer-header">
            <h6 class="drawer-title"><i class="fas fa-book mr-2"></i> Supervisor Management Overview</h6>
            <button type="button" class="drawer-close" id="closeDrawerBtn">&times;</button>
        </div>
        <div class="drawer-body">
            <p class="drawer-desc">This dashboard allows you to oversee and configure merchant supervisors. You can register new supervisors, manage their profile credentials, and dynamically assign or reassign merchants under their coordination.</p>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-search text-primary mr-2"></i> Live Search</div>
                <p class="drawer-card-text">Instantly filter supervisor records by name, username, or email using the search bar.</p>
            </div>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-filter text-primary mr-2"></i> Advanced Filters</div>
                <p class="drawer-card-text">Use the Filters dropdown to filter records by status (Active, Pending, Blocked, Freeze) or registration date range.</p>
            </div>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-plus-circle text-primary mr-2"></i> Add Supervisor</div>
                <p class="drawer-card-text">Create new supervisor credentials and immediately select the merchants they will supervise.</p>
            </div>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-user-edit text-primary mr-2"></i> Edit Supervisor</div>
                <p class="drawer-card-text">Use the Actions dropdown (⋮) on any table row to modify credentials, change status, or reassign merchants dynamically.</p>
            </div>
        </div>
    </div>

    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">

        <!-- ── Toolbar ── -->
        <form id="spv_search_form" method="post" action="<?= base_url('merchant/supervisor'); ?>">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <div class="dt-toolbar py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="dt-toolbar-left flex-grow-1" style="min-width: 280px;">
                    <div class="dt-search-wrapper">
                        <i class="fas fa-search dt-search-icon"></i>
                        <input type="text" id="dt-global-search" class="dt-search-input" placeholder="Search by name, username, and email..." value="<?= $this->session->userdata('search_spv'); ?>">
                    </div>
                </div>
                <div class="dt-toolbar-right d-flex align-items-center gap-2">
                    <!-- More Filters Trigger -->
                    <div class="dt-filter-group dt-more-filters-wrapper">
                        <button type="button" id="spvMoreFiltersBtn" class="dt-more-filters-btn <?= (!empty($this->session->userdata('search_spv_status')) || !empty($this->session->userdata('search_spv_date_from')) || !empty($this->session->userdata('search_spv_date_to'))) ? 'dt-more-filters-active' : ''; ?>">
                            <i class="fas fa-sliders-h mr-1 mr-2"></i> Filters
                            <?php 
                                $extra_active = 0;
                                if (!empty($this->session->userdata('search_spv_status'))) $extra_active++;
                                if (!empty($this->session->userdata('search_spv_date_from')) || !empty($this->session->userdata('search_spv_date_to'))) $extra_active++;
                                if ($extra_active > 0): 
                            ?>
                                <span class="dt-more-badge"><?= $extra_active; ?></span>
                            <?php endif; ?>
                            <i class="fas fa-chevron-down ml-1 dt-more-arrow"></i>
                        </button>

                        <!-- Dropdown Panel -->
                        <div class="dt-more-panel" id="spvMoreFiltersPanel">
                            <div class="dt-more-panel-header">
                                <span class="dt-more-panel-title"><i class="fas fa-filter mr-1 mr-2"></i> Advanced Filters</span>
                                <a href="<?= base_url('merchant/supervisor/reset'); ?>" class="dt-more-clear">Clear All</a>
                            </div>

                            <div class="dt-more-panel-body">
                                <!-- Registration Date Range -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-calendar-alt mr-1 mr-2"></i> Registration Date</label>
                                    <div class="dt-filter-chip">
                                        <input type="date" name="search_spv_date_from" class="dt-chip-input" value="<?= $this->session->userdata('search_spv_date_from'); ?>" title="Date From">
                                        <span class="text-muted mx-1" style="font-size:11px;">→</span>
                                        <input type="date" name="search_spv_date_to" class="dt-chip-input" value="<?= $this->session->userdata('search_spv_date_to'); ?>" title="Date To">
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-info-circle mr-1 mr-2"></i> Account Status</label>
                                    <select name="search_spv_status" class="dt-more-select">
                                        <option value="">All Account Statuses</option>
                                        <option value="Pending" <?= ($this->session->userdata('search_spv_status') == 'Pending') ? 'selected' : ''; ?>>Pending Approval</option>
                                        <option value="Active" <?= ($this->session->userdata('search_spv_status') == 'Active') ? 'selected' : ''; ?>>Active</option>
                                        <option value="Blocked" <?= ($this->session->userdata('search_spv_status') == 'Blocked') ? 'selected' : ''; ?>>Blocked</option>
                                        <option value="Freeze" <?= ($this->session->userdata('search_spv_status') == 'Freeze') ? 'selected' : ''; ?>>Frozen</option>
                                    </select>
                                </div>
                            </div>

                            <div class="dt-more-panel-footer">
                                <button type="submit" name="submit" class="btn-dt-apply btn-dt-action-primary shadow-sm">
                                    <i class="fas fa-check mr-1 mr-2"></i> APPLY FILTER
                                </button>
                                <button type="button" id="spvMoreFiltersClose" class="btn-dt-cancel btn-dt-secondary">
                                    CANCEL
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="dt-filter-group">
                        <div class="d-flex" style="gap:6px;">
                            <button type="button" class="btn-dt-action btn-dt-action-success" data-toggle="modal" data-target="#registerMerchantSpv">
                                <i class="fas fa-plus mr-1 mr-2"></i> Add Supervisor
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <script>
            $(document).ready(function() {
                <?php if ($this->session->flashdata('success')) : ?>
                    Swal.fire({
                        title: 'Success!',
                        text: '<?= $this->session->flashdata('success'); ?>',
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
            <table id="merchantSpvTable" class="table dt-table mb-0" style="width:100%">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">NO</th>
                        <th>NAME</th>
                        <th>USERNAME</th>
                        <th>EMAIL</th>
                        <th>STATUS</th>
                        <th>REGISTRATION DATE</th>
                        <?php if (!$this->rbac->has_permission($this->session->userdata('role'), 'no_action')) { ?>
                            <th class="text-center" style="width: 80px;">ACTION</th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody id="merchantSpvTableBody">
                    <!-- Data will be loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div><!-- /.dt-card -->
</div><!-- /.container-fluid -->

<!-- Modal: Register Merchant SPV -->
<div class="modal fade" id="registerMerchantSpv" tabindex="-1" role="dialog" aria-labelledby="registerMerchantSpvLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- Header Legacy Migrated -->
            <div class="modal-header modal-header-primary border-0 mh-premium">
                <div class="d-flex align-items-center">
                    <div class="mh-icon-badge">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="mh-title-wrap">
                        <h6 class="mh-title" id="registerMerchantSpvLabel">Register Merchant Supervisor</h6>
                        <small class="mh-subtitle">Create and register a new supervisor profile</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="<?= base_url('merchant/supervisor/register'); ?>">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <div class="modal-body p-0 bg-light text-dark">
                    <div class="d-flex g-0 w-100 flex-wrap flex-lg-nowrap">
                        <!-- Left Information Sidebar -->
                        <div class="col-lg-4 p-4 d-flex flex-column justify-content-between mb-0" style="background: var(--bg-body); border-right: 1px solid rgba(255,255,255,0.05);">
                            <div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm" style="width: 40px; height: 40px; min-width: 40px;">
                                        <i class="fas fa-info-circle fa-lg"></i>
                                    </div>
                                    <h6 class="fw-bold text-primary mb-0" style="font-size: 15px;">Supervisor Guide</h6>
                                </div>
                                <p class="text-muted small mb-4" style="font-size: 12px; line-height: 1.5; color: #a0a5b0 !important;">Follow the instructions below to register and configure a merchant supervisor profile accurately.</p>
                                
                                <div class="d-flex flex-column gap-3">
                                    <div class="bg-white-soft p-3 rounded-4 shadow-sm border-0 mb-3" style="background: rgba(255,255,255,0.04); border-radius: 8px;">
                                        <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12.5px;"><i class="fas fa-user text-primary mr-2"></i> 1. Account Profile</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.5; color: #a0a5b0 !important;">Enter the full name, unique username, and email address for this supervisor.</p>
                                    </div>
                                    <div class="bg-white-soft p-3 rounded-4 shadow-sm border-0 mb-3" style="background: rgba(255,255,255,0.04); border-radius: 8px;">
                                        <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12.5px;"><i class="fas fa-key text-primary mr-2"></i> 2. Password Policy</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.5; color: #a0a5b0 !important;">Provide a secure password. Ensure both password fields match to verify correctness.</p>
                                    </div>
                                    <div class="bg-white-soft p-3 rounded-4 shadow-sm border-0 mb-3" style="background: rgba(255,255,255,0.04); border-radius: 8px;">
                                        <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12.5px;"><i class="fas fa-link text-primary mr-2"></i> 3. Merchant Linkage</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.5; color: #a0a5b0 !important;">Select one or more merchants from the searchable list to link to this supervisor.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white-soft p-3 rounded-4 shadow-sm border-0 mt-3 d-flex align-items-center" style="background: rgba(255,255,255,0.02); border-radius: 8px;">
                                <i class="fas fa-lightbulb text-warning fa-2x mr-3"></i>
                                <span class="text-muted" style="font-size: 11px; line-height: 1.4; color: #a0a5b0 !important;">Need help? Contact the support team for user registration guidelines.</span>
                            </div>
                        </div>
                        
                        <!-- Right Form Area -->
                        <div class="col-lg-8 p-4 bg-light mb-0">
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="dt-more-label mb-2">SPV Name <span class="text-danger">*</span></label>
                                    <input type="text" class="dt-more-input" required name="c_name" placeholder="Full Name">
                                </div>
                                <div class="col-md-6">
                                    <label class="dt-more-label mb-2">Username <span class="text-danger">*</span></label>
                                    <input type="text" class="dt-more-input" required name="c_username" placeholder="username123">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="dt-more-label mb-2">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="dt-more-input" required name="c_email" placeholder="email@example.com">
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="dt-more-label mb-2">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="dt-more-input" required name="c_password" placeholder="••••••••">
                                </div>
                                <div class="col-md-6">
                                    <label class="dt-more-label mb-2">Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" class="dt-more-input" required name="c_confirmPassword" placeholder="••••••••">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="dt-more-label mb-2">Assigned Merchants</label>
                                <select class="form-control select2-merchant" id="c_merchant_spv" name="c_merchant_spv[]" multiple="multiple" style="width: 100%;">
                                </select>
                                <div class="form-text text-muted small mt-2">Search and select one or more merchants for this supervisor.</div>
                            </div>

                            <div class="mb-4">
                                <label class="dt-more-label mb-2 d-block">Account Status <span class="text-danger">*</span></label>
                                <div class="d-flex" style="gap:20px;">
                                    <?php 
                                        $statuses = ['Active', 'Pending', 'Blocked', 'Freeze'];
                                        foreach($statuses as $st):
                                    ?>
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" name="c_status" value="<?= $st ?>" id="status_<?= $st ?>" <?= $st == 'Active' ? 'checked' : '' ?>>
                                        <label class="custom-control-label" style="font-size:13px; font-weight:500;" for="status_<?= $st ?>"><?= $st ?></label>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 justify-content-end bg-white px-4">
                    <button type="button" class="btn-dt-cancel" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn-dt-apply px-4">
                        <i class="fas fa-save mr-2"></i> Register Supervisor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
        </div>
    </div>
</div>

<!-- Modal: Edit Merchant SPV -->
<div class="modal fade" id="editMerchantSpv" tabindex="-1" role="dialog" aria-labelledby="editMerchantSpvLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <!-- Header Legacy Migrated -->
            <div class="modal-header modal-header-primary border-0 mh-premium" style="background: linear-gradient(135deg, #f39c12 0%, #d35400 100%);">
                <div class="d-flex align-items-center">
                    <div class="mh-icon-badge" style="background: rgba(255,255,255,0.2);">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <div class="mh-title-wrap">
                        <h6 class="mh-title" id="editMerchantSpvLabel">Edit Merchant Supervisor</h6>
                        <small class="mh-subtitle">Modify supervisor profile and assign merchants</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editMerchantSpvForm" method="post" action="">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <div class="modal-body p-0 bg-light text-dark">
                    <div class="d-flex g-0 w-100 flex-wrap flex-lg-nowrap">
                        <!-- Left Information Sidebar -->
                        <div class="col-lg-4 p-4 d-flex flex-column justify-content-between mb-0" style="background: var(--bg-body); border-right: 1px solid rgba(255,255,255,0.05);">
                            <div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm" style="width: 40px; height: 40px; min-width: 40px; background-color: #d35400 !important;">
                                        <i class="fas fa-edit fa-lg"></i>
                                    </div>
                                    <h6 class="fw-bold text-warning mb-0" style="font-size: 15px; color: #f39c12 !important;">Update Guide</h6>
                                </div>
                                <p class="text-muted small mb-4" style="font-size: 12px; line-height: 1.5; color: #a0a5b0 !important;">Follow the instructions below to modify the supervisor profile and reassign merchants.</p>
                                
                                <div class="d-flex flex-column gap-3">
                                    <div class="bg-white-soft p-3 rounded-4 shadow-sm border-0 mb-3" style="background: rgba(255,255,255,0.04); border-radius: 8px;">
                                        <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12.5px;"><i class="fas fa-user-edit text-warning mr-2" style="color: #f39c12 !important;"></i> 1. Modify Profile</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.5; color: #a0a5b0 !important;">Edit name, username, and email credentials. Username and email must remain unique.</p>
                                    </div>
                                    <div class="bg-white-soft p-3 rounded-4 shadow-sm border-0 mb-3" style="background: rgba(255,255,255,0.04); border-radius: 8px;">
                                        <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12.5px;"><i class="fas fa-key text-warning mr-2" style="color: #f39c12 !important;"></i> 2. Update Password</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.5; color: #a0a5b0 !important;">Leave the password fields empty if you want to keep the current password.</p>
                                    </div>
                                    <div class="bg-white-soft p-3 rounded-4 shadow-sm border-0 mb-3" style="background: rgba(255,255,255,0.04); border-radius: 8px;">
                                        <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12.5px;"><i class="fas fa-adjust text-warning mr-2" style="color: #f39c12 !important;"></i> 3. Adjust Merchants</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.5; color: #a0a5b0 !important;">Add or remove merchants. Deselected merchants will be instantly unassigned.</p>
                                    </div>
                                    <div class="bg-white-soft p-3 rounded-4 shadow-sm border-0 mb-3" style="background: rgba(255,255,255,0.04); border-radius: 8px;">
                                        <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12.5px;"><i class="fas fa-info-circle text-warning mr-2" style="color: #f39c12 !important;"></i> 4. Account Status</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.5; color: #a0a5b0 !important;">Set status to Active, Pending, Blocked, or Freeze to control platform access.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white-soft p-3 rounded-4 shadow-sm border-0 mt-3 d-flex align-items-center" style="background: rgba(255,255,255,0.02); border-radius: 8px;">
                                <i class="fas fa-lightbulb text-warning fa-2x mr-3"></i>
                                <span class="text-muted" style="font-size: 11px; line-height: 1.4; color: #a0a5b0 !important;">Need help? Contact the support team for user registration guidelines.</span>
                            </div>
                        </div>
                        
                        <!-- Right Form Area -->
                        <div class="col-lg-8 p-4 bg-light mb-0">
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="dt-more-label mb-2">SPV Name <span class="text-danger">*</span></label>
                                    <input type="text" class="dt-more-input" required id="edit_c_name" name="c_name" placeholder="Full Name">
                                </div>
                                <div class="col-md-6">
                                    <label class="dt-more-label mb-2">Username <span class="text-danger">*</span></label>
                                    <input type="text" class="dt-more-input" required id="edit_c_username" name="c_username" placeholder="username123">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="dt-more-label mb-2">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="dt-more-input" required id="edit_c_email" name="c_email" placeholder="email@example.com">
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <label class="dt-more-label mb-2">Password <span class="text-muted font-weight-normal">(Leave blank to keep current)</span></label>
                                    <input type="password" class="dt-more-input" id="edit_c_password" name="c_password" placeholder="••••••••">
                                </div>
                                <div class="col-md-6">
                                    <label class="dt-more-label mb-2">Confirm Password</label>
                                    <input type="password" class="dt-more-input" id="edit_c_confirmPassword" name="c_confirmPassword" placeholder="••••••••">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="dt-more-label mb-2">Assigned Merchants</label>
                                <select class="form-control select2-merchant" id="edit_c_merchant_spv" name="c_merchant_spv[]" multiple="multiple" style="width: 100%;">
                                </select>
                                <div class="form-text text-muted small mt-2">Search and select one or more merchants for this supervisor.</div>
                            </div>

                            <div class="mb-4">
                                <label class="dt-more-label mb-2 d-block">Account Status <span class="text-danger">*</span></label>
                                <div class="d-flex" style="gap:20px;">
                                    <?php 
                                        $statuses = ['Active', 'Pending', 'Blocked', 'Freeze'];
                                        foreach($statuses as $st):
                                    ?>
                                    <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" name="c_status" value="<?= $st ?>" id="edit_status_<?= $st ?>">
                                        <label class="custom-control-label" style="font-size:13px; font-weight:500;" for="edit_status_<?= $st ?>"><?= $st ?></label>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 justify-content-end bg-white px-4">
                    <button type="button" class="btn-dt-cancel" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn-dt-apply px-4" style="background: linear-gradient(135deg, #f39c12 0%, #d35400 100%); border-color: #d35400;">
                        <i class="fas fa-save mr-2"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const table = initServerDataTable("#merchantSpvTable", "<?= base_url('merchant/supervisor') ?>", [
            { "data": "no", "className": "text-center" },
            { "data": "c_name", "className": "font-weight-bold text-dark text-nowrap" },
            { 
                "data": "c_username",
                "render": function(data) {
                    return `<span class="badge badge-light text-dark text-dark border px-2 py-1">${data}</span>`;
                }
            },
            { "data": "c_email", "className": "text-nowrap" },
            { 
                "data": "c_status",
                "render": function(data) {
                    let statusClass = 'secondary';
                    if (data === 'Active') statusClass = 'success';
                    else if (data === 'Pending') statusClass = 'warning';
                    else if (data === 'Blocked') statusClass = 'danger';
                    else if (data === 'Freeze') statusClass = 'info';
                    return `<span class="badge badge-${statusClass}-soft text-${statusClass} px-2 py-1">${data}</span>`;
                }
            },
            {
                "data": "c_created_date",
                "className": "text-center text-nowrap",
                "render": function(data) {
                    if (!data) return '-';
                    var d = new Date(data);
                    if (isNaN(d)) return data;
                    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    var day = ('0' + d.getDate()).slice(-2);
                    var month = months[d.getMonth()];
                    var year = d.getFullYear();
                    return '<span class="fw-bold text-dark">' + day + ' ' + month + ' ' + year + '</span>';
                }
            },
            { 
                "data": null,
                "className": "text-center",
                "orderable": false,
                render: function(data, type, row) {
                    return `
                        <div class="dropdown">
                            <button class="btn btn-sm rounded-circle p-2 border-0 bg-transparent" type="button" data-toggle="dropdown" data-boundary="viewport" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right shadow border-0 py-2">
                                <li>
                                    <a class="dropdown-item" href="<?= base_url('merchant/manage/list/') ?>${row.id}">
                                        <i class="fas fa-store text-primary mr-2"></i> View Merchants
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item edit-spv-btn" href="javascript:void(0)" data-id="${row.id}">
                                        <i class="fas fa-user-edit text-warning mr-2"></i> Edit Supervisor
                                    </a>
                                </li>
                                <li class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger delete-btn" href="javascript:void(0)" data-href="<?= base_url('merchant/supervisor/delete/') ?>${row.id}">
                                        <i class="fas fa-trash-alt mr-2"></i> Delete
                                    </a>
                                </li>
                            </ul>
                        </div>
                    `;
                }
            }
        ], {
        order: [[5, 'desc']], // Sort by Registration Date
        language: {
            "info": "Showing _START_ – _END_ of _TOTAL_ entries",
            "infoEmpty": "No entries to show",
            "zeroRecords": '<div class="text-center py-4 text-muted"><i class="fas fa-inbox fa-2x mb-2 d-block mr-2"></i> No supervisors found.</div>'
        }
    });

    // Global Search
    $('#dt-global-search').on('keyup', function() {
        table.search(this.value).draw();
    });

    // More Filters dropdown
    var $moreBtn   = $('#spvMoreFiltersBtn');
    var $morePanel = $('#spvMoreFiltersPanel');
    var $moreClose = $('#spvMoreFiltersClose');

    $moreBtn.on('click', function(e) {
        e.stopPropagation();
        var isOpen = $morePanel.hasClass('dt-panel-open');
        if (isOpen) {
            $morePanel.removeClass('dt-panel-open');
            $moreBtn.removeClass('dt-more-filters-open');
        } else {
            $morePanel.addClass('dt-panel-open');
            $moreBtn.addClass('dt-more-filters-open');
        }
    });

    $moreClose.on('click', function(e) {
        e.stopPropagation();
        $morePanel.removeClass('dt-panel-open');
        $moreBtn.removeClass('dt-more-filters-open');
    });

    $morePanel.on('click', function(e) {
        e.stopPropagation(); // Prevent closing when clicking inside
    });

    $(document).on('click', function() {
        $morePanel.removeClass('dt-panel-open');
        $moreBtn.removeClass('dt-more-filters-open');
    });

    // Select2 for ALL selects inside the More Filters panel
    $('#spvMoreFiltersPanel select').not('.select2-hidden-accessible').select2({
        width: '100%',
        dropdownAutoWidth: true,
        dropdownParent: $(document.body),
        minimumResultsForSearch: 0
    });

    // ── Select2 for Modal ──
    $('#registerMerchantSpv').on('shown.bs.modal', function () {
        const $select = $('#c_merchant_spv');
        
        // Destroy existing instance to force re-init with correct AJAX settings
        if ($select.hasClass("select2-hidden-accessible")) {
            $select.select2('destroy').empty();
        }

        $select.select2({
            dropdownParent: $('#registerMerchantSpv'),
            dropdownAutoWidth: true,
            placeholder: 'Search Merchants...',
            width: '100%',
            minimumInputLength: 1,
            ajax: {
                url: "<?= base_url('merchant/supervisor/search'); ?>",
                dataType: 'json',
                delay: 250,
                data: function (params) { return { q: params.term }; },
                processResults: function (data) {
                    return {
                        results: $.map(data, function(item) {
                            return { id: item.id, text: item.name };
                        })
                    };
                },
                cache: true,
                error: function (xhr, status, error) {
                    // Ignore aborted requests (common when typing fast/deleting)
                    if (xhr.status === 0) return;
                    console.error("Select2 AJAX Error:", status, error);
                }
            }
        });
    });

    // ── Edit Supervisor ──
    $(document).on('click', '.edit-spv-btn', function() {
        const id = $(this).data('id');
        
        // Show loading state using Swal
        Swal.fire({
            title: 'Loading...',
            text: 'Fetching supervisor details...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: "<?= base_url('merchant/supervisor/get/') ?>" + id,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                Swal.close();
                
                // Set form details
                $('#editMerchantSpvForm').attr('action', "<?= base_url('merchant/supervisor/update/') ?>" + id);
                $('#edit_c_name').val(data.c_name);
                $('#edit_c_username').val(data.c_username);
                $('#edit_c_email').val(data.c_email);
                $('#edit_c_password').val('');
                $('#edit_c_confirmPassword').val('');
                
                // Select status radio button
                $(`input[name="c_status"][value="${data.c_status}"]`).prop('checked', true);

                // Setup Select2 for Assigned Merchants in Edit Modal
                const $editSelect = $('#edit_c_merchant_spv');
                if ($editSelect.hasClass("select2-hidden-accessible")) {
                    $editSelect.select2('destroy').empty();
                }

                // Append currently assigned merchants as selected options
                if (data.assigned_merchants && data.assigned_merchants.length > 0) {
                    data.assigned_merchants.forEach(function(merchant) {
                        const newOption = new Option(merchant.c_name, merchant.id, true, true);
                        $editSelect.append(newOption);
                    });
                }
                
                $editSelect.select2({
                    dropdownParent: $('#editMerchantSpv'),
                    dropdownAutoWidth: true,
                    placeholder: 'Search Merchants...',
                    width: '100%',
                    minimumInputLength: 1,
                    ajax: {
                        url: "<?= base_url('merchant/supervisor/search'); ?>",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term,
                                supervisor_id: id
                            };
                        },
                        processResults: function (data) {
                            return {
                                results: $.map(data, function(item) {
                                    return { id: item.id, text: item.name };
                                })
                            };
                        },
                        cache: true,
                        error: function (xhr, status, error) {
                            if (xhr.status === 0) return;
                            console.error("Edit Select2 AJAX Error:", status, error);
                        }
                    }
                });

                // Open the modal
                $('#editMerchantSpv').modal('show');
            },
            error: function(xhr, status, error) {
                Swal.close();
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to retrieve supervisor details: ' + error,
                    icon: 'error',
                    customClass: {
                        popup: 'swal2-premium-popup',
                        confirmButton: 'swal2-premium-confirm'
                    },
                    buttonsStyling: false
                });
            }
        });
    });

    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        const url = $(this).data('href');
        Swal.fire({
            title: 'Are you sure?',
            text: "This supervisor and their access will be permanently deleted!",
            icon: 'warning',
            showCancelButton: true,
            customClass: {
                popup: 'swal2-premium-popup',
                confirmButton: 'swal2-premium-confirm',
                cancelButton: 'swal2-premium-cancel',
                actions: 'swal2-premium-actions'
            },
            buttonsStyling: false,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });

    // Instructions Guide Drawer Toggle
    $('#toggleGuideBtn').on('click', function() {
        $('#instructionDrawer, #instructionOverlay').addClass('open');
        $('body').css('overflow', 'hidden');
    });

    $('#closeDrawerBtn, #instructionOverlay').on('click', function() {
        $('#instructionDrawer, #instructionOverlay').removeClass('open');
        $('body').css('overflow', '');
    });
});
</script>



