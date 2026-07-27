<!-- Begin Page Content -->
<div>

    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title"><?= $title; ?></h4>
            <p class="dt-page-subtitle">Manage administrator accounts, access levels, and role assignments.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            </div>
    </div>

    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">

        <!-- ── Toolbar ── -->
        <div class="dt-toolbar py-3 px-4">
            <div class="dt-search-wrapper flex-grow-1 mb-2 mb-md-0" style="min-width: 280px;">
                <i class="fas fa-search dt-search-icon"></i>
                <input type="text" id="adminGlobalSearch" class="dt-search-input" placeholder="Search by email, name, or role..." value="<?= $this->session->userdata('search_admin'); ?>">
            </div>

            <!-- RIGHT: Filters & Actions -->
            <div class="dt-toolbar-filters d-flex align-items-center gap-2">
                

                <!-- More Filters Trigger -->
                <div class="dt-filter-group dt-more-filters-wrapper">
                    <button type="button" id="adminMoreFiltersBtn" class="dt-more-filters-btn">
                        <i class="fas fa-sliders-h mr-1 mr-2"></i> Filters
                        <span class="dt-more-badge" id="adminFilterBadge" style="display: none;">0</span>
                        <i class="fas fa-chevron-down ml-1 dt-more-arrow"></i>
                    </button>

                    <!-- Dropdown Panel -->
                    <div class="dt-more-panel" id="adminMoreFiltersPanel">
                        <div class="dt-more-panel-header">
                            <span class="dt-more-panel-title"><i class="fas fa-filter mr-1 mr-2"></i> Advanced Filters</span>
                            <a href="javascript:void(0)" id="adminMoreClear" class="dt-more-clear">Clear All</a>
                        </div>

                        <div class="dt-more-panel-body">
                            <!-- Role -->
                            <div class="dt-more-field">
                                <label class="dt-more-label"><i class="fas fa-user-shield mr-1 mr-2"></i> Role</label>
                                <select id="filter_role" class="dt-more-select filter-select">
                                    <option value="">All Roles</option>
                                    <?php foreach ($roles as $r): ?>
                                        <option value="<?= $r->id ?>"><?= $r->role_name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Status -->
                            <div class="dt-more-field">
                                <label class="dt-more-label"><i class="fas fa-info-circle mr-1 mr-2"></i> Status</label>
                                <select id="filter_status" class="dt-more-select filter-select">
                                    <option value="">All Statuses</option>
                                    <option value="Active">Active</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Blocked">Blocked</option>
                                    <option value="Freeze">Freeze</option>
                                </select>
                            </div>
                        </div>

                        <div class="dt-more-panel-footer">
                            <button type="button" id="adminMoreApply" class="btn-dt-apply btn-dt-action-primary shadow-sm">
                                <i class="fas fa-check mr-1 mr-2"></i> APPLY FILTER
                            </button>
                            <button type="button" id="adminMoreFiltersClose" class="btn-dt-cancel btn-dt-secondary">
                                CANCEL
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Add New Admin Button -->
                <button type="button" class="btn-dt-apply btn-dt-action-primary shadow-sm add-admin-btn" data-toggle="modal" data-target=".manageUserModal">
                    <i class="fas fa-plus mr-2"></i> New Admin Account
                </button>
            </div>
        </div>

        <!-- ── Table ── -->
        <div class="table-responsive">
            <table class="table dt-table mb-0" id="adminTable" style="width:100%">
                <thead>
                    <tr>
                        <th style="width:50px;">NO</th>
                        <th>EMAIL</th>
                        <th>NAME</th>
                        <th>ROLE</th>
                        <th>LEVEL</th>
                        <th>STATUS</th>
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

<!-- ── Manage User Modal ── -->
<div class="modal fade manageUserModal" id="manageUserModal" tabindex="-1" role="dialog" aria-labelledby="manageUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <!-- Header Legacy Migrated -->
            <div class="modal-header modal-header-primary border-0 mh-premium">
                <div class="d-flex align-items-center">
                    <div class="mh-icon-badge">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="mh-title-wrap">
                        <h6 class="mh-title" id="manageUserModalLabel">MANAGE ADMIN ACCOUNT</h6>
                        <small class="mh-subtitle" id="manageUserModalSubtitle">Manage and process information details</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="manageUserForm" action="<?= base_url('access-control/accounts/create'); ?>" method="post">
                <div class="modal-body p-0 bg-light">
                    <div class="d-flex g-0 w-100 flex-column flex-lg-row">
                        <!-- Left Information Sidebar -->
                        <div class="col-lg-4 p-4 d-flex flex-column justify-content-between mb-0" style="background: var(--bg-body); border-right: 1px solid rgba(255,255,255,0.05);">
                            <div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm" style="width: 40px; height: 40px;">
                                        <i class="fas fa-users-cog fa-lg"></i>
                                    </div>
                                    <h6 class="fw-bold text-warning mb-0" style="font-size: 15px;">Account Guidelines</h6>
                                </div>
                                <p class="text-muted small mb-4" style="font-size: 12px; line-height: 1.5;">Manage administrative personnel logins, system clearances, and operational statuses.</p>
                                
                                <div class="d-flex flex-column gap-3">
                                    <div class="p-3 rounded-4 shadow-sm border-0 mb-3" style="background-color: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important; border-radius: 12px;">
                                        <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12px;"><i class="fas fa-shield-alt text-warning mr-2"></i> 1. Level Access</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.4;"><strong>Level 1</strong> grants comprehensive full master system rights. <strong>Level 2</strong> configures secondary supervisor control.</p>
                                    </div>
                                    <div class="p-3 rounded-4 shadow-sm border-0 mb-3" style="background-color: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important; border-radius: 12px;">
                                        <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12px;"><i class="fas fa-info-circle text-info mr-2"></i> 2. Account States</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.4;">Active permits instant backend logins. Blocked or Freeze states terminate login actions immediately.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Form Area -->
                        <div class="col-lg-8 p-4 bg-light mb-0 text-dark">
                            <!-- Email Address (Add Only) -->
                            <div class="mb-3" id="email_container">
                                <label class="dt-more-label mb-2">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="dt-more-input" id="c_email" name="c_email" placeholder="admin@example.com">
                            </div>

                            <!-- Full Name -->
                            <div class="mb-3">
                                <label class="dt-more-label mb-2">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="dt-more-input" id="c_name" name="c_name" placeholder="Admin full name" required>
                            </div>

                            <!-- Role -->
                            <div class="mb-3">
                                <label class="dt-more-label mb-2">Role <span class="text-danger">*</span></label>
                                <select class="dt-more-select" id="role_id" required name="role_id">
                                    <option value="" selected disabled>Select role</option>
                                    <?php foreach ($roles as $row): ?>
                                        <option value="<?= $row->id ?>"><?= $row->role_name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Level -->
                            <div class="mb-3">
                                <label class="dt-more-label mb-2">Level <span class="text-danger">*</span></label>
                                <select class="dt-more-select" id="c_level" required name="c_level">
                                    <option value="" selected disabled>Select level (Allowed values: 1 or 2)</option>
                                    <option value="1">Level 1 (Primary / Full Access)</option>
                                    <option value="2">Level 2 (Secondary / Restricted)</option>
                                </select>
                            </div>

                            <!-- Status -->
                            <div class="mb-3">
                                <label class="dt-more-label mb-2">Status <span class="text-danger">*</span></label>
                                <select class="dt-more-select" id="c_status" required name="c_status">
                                    <option value="" selected disabled>Select status</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Active">Active</option>
                                    <option value="Blocked">Blocked</option>
                                    <option value="Freeze">Freeze</option>
                                </select>
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label class="dt-more-label mb-2" for="c_password">New Password <span class="text-muted font-weight-normal small">(Optional)</span></label>
                                <input type="password" class="dt-more-input" id="c_password" name="c_password" placeholder="Enter password">
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-4">
                                <label class="dt-more-label mb-2" for="c_password_confirm">Confirm New Password <span class="text-muted font-weight-normal small">(Optional)</span></label>
                                <input type="password" class="dt-more-input" id="c_password_confirm" name="c_password_confirm" placeholder="Confirm password">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3 border-0 bg-white justify-content-end">
                    <button type="button" class="btn-dt-cancel mr-2" data-dismiss="modal">CLOSE</button>
                    <button type="submit" class="btn-dt-apply px-4">
                        <i class="fas fa-save mr-2"></i> SAVE CHANGES
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script type="text/javascript">
    
    $(document).ready(function () {
        </script>