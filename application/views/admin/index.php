<!-- Begin Page Content -->
<div>

    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title"><?= $title; ?></h4>
            <p class="dt-page-subtitle">Manage administrator accounts, access levels, and role assignments.</p>
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
            <h6 class="drawer-title"><i class="fas fa-book mr-2"></i> Admin Accounts Guide</h6>
            <button type="button" class="drawer-close" id="closeDrawerBtn">&times;</button>
        </div>
        <div class="drawer-body">
            <p class="drawer-desc">This page allows supervisors to manage administrative accounts, assign operational roles, and set clearance levels.</p>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-user-shield text-primary mr-2"></i> Admin Accounts</div>
                <p class="drawer-card-text">Audit back-office administrator credentials, role access mappings, and profile status updates.</p>
            </div>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-info-circle text-primary mr-2"></i> Active States</div>
                <p class="drawer-card-text">Account statuses (Active, Pending, Blocked, Freeze) control immediate login clearance. Blocked or frozen states terminate backend sessions instantly.</p>
            </div>
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
        var table = initServerDataTable('#adminTable', "<?= base_url('access-control/accounts') ?>", [
                { data: 'no', orderable: false, className: 'text-center' },
                { data: 'c_email', className: 'font-weight-bold text-primary small' },
                { data: 'c_name', className: 'font-weight-bold' },
                { data: 'role_name', className: 'text-dark' },
                { data: 'c_level', render: function(data) {
                    return '<span class="badge badge-light text-dark border px-2 py-1 text-uppercase small">' + data + '</span>';
                }},
                { data: 'c_status', render: function(data) {
                    var d = (data || '').toLowerCase();
                    var cls = 'secondary';
                    if (d === 'active') cls = 'success';
                    else if (d === 'blocked' || d === 'freeze') cls = 'danger';
                    else if (d === 'pending') cls = 'warning';
                    return '<span class="badge badge-' + cls + ' px-2 py-1">' + data + '</span>';
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
                                        <button type="button" class="dropdown-item edit-btn" 
                                            data-toggle="modal" data-target=".manageUserModal" 
                                            data-id="${row.id}" 
                                            data-email="${row.c_email}" 
                                            data-name="${row.c_name}" 
                                            data-role="${row.role_id}" 
                                            data-status="${row.c_status}" 
                                            data-level="${row.c_level}">
                                            <i class="fas fa-edit text-primary mr-2"></i> Manage Account
                                        </button>
                                    </li>
                                    <li>
                                        <button type="button" class="dropdown-item delete-btn text-danger" 
                                            data-id="${row.id}" data-name="${row.c_name}">
                                            <i class="fas fa-trash-alt text-danger mr-2"></i> Delete Account
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        `;
                    }
                }
            ], {
                ajax: {
                    url: "<?= base_url('access-control/accounts') ?>",
                    type: "POST",
                    data: function (d) {
                        var csrfName = $('meta[name="csrf-token-name"]').attr('content');
                        var csrfHash = $('meta[name="csrf-token-hash"]').attr('content');
                        if (csrfName && csrfHash) {
                            d[csrfName] = csrfHash;
                        }
                        if (d.search && d.search.value) {
                            d.search.value = d.search.value.trim();
                        }
                        d.role_id = $('#filter_role').val();
                        d.status = $('#filter_status').val();
                    }
                }
            });

        // Global Search with Debounce
        $('#adminGlobalSearch').on('input', debounce(function() {
            table.search(this.value.trim()).draw();
        }, 400));

        // Fill modal on edit button click
        $(document).on('click', '.edit-btn', function () {
            var $btn = $(this);
            $('#email_container').show();
            $('#c_email').val(($btn.attr('data-email') || '').trim()).attr('required', true);
            $('#c_name').val(($btn.attr('data-name') || '').trim());
            $('#c_level').val(($btn.attr('data-level') || '').trim());
            
            var roleId = ($btn.attr('data-role') || '').trim();
            $('#role_id').val(roleId).trigger('change');
            
            // Fallback: If direct match fails, look for value or text match
            if (!$('#role_id').val() && roleId) {
                $('#role_id option').each(function() {
                    if ($(this).val() == roleId || $(this).text().trim().toLowerCase() === roleId.toLowerCase()) {
                        $('#role_id').val($(this).val()).trigger('change');
                        return false;
                    }
                });
            }
            
            var status = ($btn.attr('data-status') || '').trim();
            $('#c_status').val(status).trigger('change');
            
            // Fallback: If direct match fails (e.g. case difference), look for insensitive match
            if (!$('#c_status').val() && status) {
                $('#c_status option').each(function() {
                    if ($(this).val().toLowerCase() === status.toLowerCase()) {
                        $('#c_status').val($(this).val()).trigger('change');
                        return false;
                    }
                });
            }

            $('#c_password').val('').removeAttr('required');
            $('#c_password_confirm').val('').removeAttr('required');
            $('label[for="c_password"]').html('New Password <span class="text-muted font-weight-normal small">(Optional)</span>');
            $('label[for="c_password_confirm"]').html('Confirm New Password <span class="text-muted font-weight-normal small">(Optional)</span>');
            $('#manageUserModalLabel').text('UPDATE ADMIN ACCOUNT');
            $('#manageUserModalSubtitle').text('Modify account details, access level, or reset password');
            const id = $btn.attr('data-id');
            $('#manageUserForm').attr('action', '<?= base_url('access-control/accounts/update') ?>/' + id);
        });

        // Fill modal on add button click
        $(document).on('click', '.add-admin-btn', function () {
            $('#email_container').show();
            $('#c_email').val('').attr('required', true);
            $('#c_name').val('');
            $('#c_level').val('');
            $('#role_id').val('').trigger('change');
            $('#c_status').val('').trigger('change');
            $('#c_password').val('').attr('required', true);
            $('#c_password_confirm').val('').attr('required', true);
            $('label[for="c_password"]').html('Password <span class="text-danger">*</span>');
            $('label[for="c_password_confirm"]').html('Confirm Password <span class="text-danger">*</span>');
            $('#manageUserModalLabel').text('ADD NEW ADMIN ACCOUNT');
            $('#manageUserModalSubtitle').text('Enter credentials and assign role permissions for the new administrator');
            $('#manageUserForm').attr('action', '<?= base_url('access-control/accounts/create') ?>');
        });

        // Delete admin button click
        $(document).on('click', '.delete-btn', function () {
            var id = $(this).attr('data-id');
            var name = $(this).attr('data-name');
            Swal.fire({
                title: 'Delete Admin Account?',
                text: "Are you sure you want to delete '" + name + "'? This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '<i class="fas fa-trash-alt mr-2"></i>Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= base_url('access-control/accounts/delete') ?>/' + id;
                }
            });
        });

        // ── More Filters dropdown ──
        var $moreBtn   = $('#adminMoreFiltersBtn');
        var $morePanel = $('#adminMoreFiltersPanel');
        var $moreClose = $('#adminMoreFiltersClose');
        var $moreApply = $('#adminMoreApply');
        var $moreClear = $('#adminMoreClear');

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

        $('#adminMoreFiltersPanel select').not('.select2-hidden-accessible').each(function () {
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
            const $badge = $('#adminFilterBadge');
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
            $('.filter-select').val('').trigger('change');
            updateFilterBadge();
            table.ajax.reload(null, false);
        });
    });
</script>