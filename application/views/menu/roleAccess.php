<!-- Begin Page Content -->
<div>

    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title">
                <?= $title; ?>
                <span class="badge badge-primary " style="font-size:13px; font-weight:600; border-radius:8px; padding:5px 12px;">
                    <?= $role['role_name']; ?>
                </span>
            </h4>
            <p class="dt-page-subtitle">Manage menu structures and toggle access rights for this role.</p>
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
            <h6 class="drawer-title"><i class="fas fa-book mr-2"></i> Menu Permissions Guide</h6>
            <button type="button" class="drawer-close" id="closeDrawerBtn">&times;</button>
        </div>
        <div class="drawer-body">
            <p class="drawer-desc">This matrix page allows administrators to manage deep hierarchical menu configurations and toggle granular access rights for the selected role.</p>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-sitemap text-primary mr-2"></i> Parent & Submenus</div>
                <p class="drawer-card-text">Distinguish main parent module tabs from nested sub-menus (indicated by 'ㄴ'). Modifying parent nodes shifts child navigation paths.</p>
            </div>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-toggle-on text-primary mr-2"></i> Access Granted</div>
                <p class="drawer-card-text">Toggle switches in the table column to instantly register or revoke access to paths for users holding this role.</p>
            </div>
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

    <!-- ── Access Configuration Card ── -->
    <div class="card border-0 shadow-sm dt-card">
        <!-- Toolbar -->
        <div class="dt-toolbar py-3 px-4">
            <div class="dt-toolbar-left">
                <div class="dt-search-wrapper">
                    <i class="fas fa-search dt-search-icon"></i>
                    <input type="text" id="menuSearch" class="dt-search-input" placeholder="Search menu...">
                </div>
            </div>
            <div class="dt-toolbar-right">
                <button type="button" class="btn-dt-action btn-dt-action-success" data-toggle="modal" data-target="#addMenuModal" style="border-radius:10px; font-weight:600; font-size:14px;">
                    <i class="fas fa-plus"></i> <span class="d-none d-md-block">Add Menu</span>
                </button>
                
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table dt-table mb-0" id="roleAccessTable" style="width:100%">
                <thead>
                    <tr>
                        <th style="width:50px;">NO</th>
                        <th>MENU NAME</th>
                        <th>TYPE</th>
                        <th>GROUP MODULE</th>
                        <th class="text-center" style="width:140px;">ACCESS GRANTED</th>
                        <th class="text-center">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>
                    <?php foreach ($menu as $m):
                        $isSub = ($m['parent_id'] != 0);
                        $type = (!$isSub)
                            ? "<span class='badge badge-primary px-2 py-1' style='font-size:10px;'>Main Menu</span>"
                            : "<span class='badge badge-light text-dark border px-2 py-1' style='font-size:10px;'>Sub Menu</span>";
                        $indent = $isSub ? "<span class='text-muted  ml-4' style='font-size:18px;'>ㄴ</span>" : "<i class='".$m['icon']."  text-primary'></i>";

                        $checked = in_array($m['id'], $role_access_ids);
                    ?>
                    <tr class="<?= $isSub ? 'bg-light-subtle' : '' ?>">
                        <td class="text-center text-muted small"><?= $i; ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <?= $indent; ?>
                                <div class="d-flex flex-column" style="gap: 0px;">
                                    <span class="<?= $isSub ? '' : 'font-weight-bold text-dark' ?>" style="font-size: 15px; margin-bottom: -2px;">
                                        <?= $m['title']; ?>
                                    </span>
                                    <span class="small text-muted" style="font-size:10px; font-family: monospace; letter-spacing: -0.2px;">
                                        <?= base_url($m['url']); ?>
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td><?= $type; ?></td>
                        <td>
                            <?php if (!empty($m['group_modules'])): ?>
                                <span class="badge badge-secondary px-2 py-1" style="font-size:10px;"><?= $m['group_modules']; ?></span>
                            <?php else: ?>
                                <span class="text-muted small">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <!-- Premium Toggle Switch -->
                            <label class="dt-toggle-switch mb-0">
                                <input type="checkbox"
                                       class="rbac-checkbox"
                                       <?= $checked ? 'checked' : ''; ?>
                                       data-role="<?= $role['id']; ?>"
                                       data-menu="<?= $m['id']; ?>">
                                <span class="dt-toggle-slider"></span>
                            </label>
                        </td>
                        <td class="text-center">
                            <div class="dropdown">
                                <button class="btn btn-sm rounded-circle shadow-none p-2" type="button" data-toggle="dropdown" data-boundary="viewport" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v text-muted"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right border-0 shadow-lg p-2" style="border-radius: 12px; min-width: 150px;">
                                    <li>
                                        <button class="dropdown-item rounded-2 py-2 edit-menu-btn" data-id="<?= $m['id']; ?>">
                                            <i class="fas fa-edit text-primary mr-2"></i> Edit Menu
                                        </button>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <button class="dropdown-item rounded-2 py-2 text-danger delete-menu-btn" data-id="<?= $m['id']; ?>" data-title="<?= $m['title']; ?>">
                                            <i class="fas fa-trash mr-2"></i> Delete Menu
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <?php $i++;
                    endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ── Modals ── -->

<!-- Add Menu Modal -->
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="addMenuModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <!-- Header Legacy Migrated -->
            <div class="modal-header modal-header-primary border-0 mh-premium">
                <div class="d-flex align-items-center">
                    <div class="mh-icon-badge">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="mh-title-wrap">
                        <h6 class="mh-title" id="addMenuModalLabel">New Menu</h6>
                        <small class="mh-subtitle">Manage and process information details</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addMenuForm">
                <div class="modal-body p-0 bg-light">
                    <div class="d-flex g-0 w-100 flex-column flex-lg-row">
                        <!-- Left Information Sidebar -->
                        <div class="col-lg-4 p-4 d-flex flex-column justify-content-between mb-0" style="background: var(--bg-body); border-right: 1px solid rgba(255,255,255,0.05);">
                            <div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm" style="width: 40px; height: 40px;">
                                        <i class="fas fa-info-circle fa-lg"></i>
                                    </div>
                                    <h6 class="fw-bold text-primary mb-0" style="font-size: 15px;">Configuration Guide</h6>
                                </div>
                                <p class="text-muted small mb-4" style="font-size: 12px; line-height: 1.5;">Define core menu parameters to build system navigation structures safely.</p>
                                
                                <div class="d-flex flex-column gap-3">
                                    <div class="p-3 rounded-4 shadow-sm border-0 mb-3" style="background-color: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important; border-radius: 12px;">
                                        <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12px;"><i class="fas fa-heading text-warning mr-2"></i> 1. Menu Title</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.4;">Enter a descriptive name for the navigation item as seen in the sidebar.</p>
                                    </div>
                                    <div class="p-3 rounded-4 shadow-sm border-0 mb-3" style="background-color: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important; border-radius: 12px;">
                                        <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12px;"><i class="fas fa-link text-info mr-2"></i> 2. Target Routing</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.4;">Provide the relative URL path mapping the menu to its respective controller action.</p>
                                    </div>
                                    <div class="p-3 rounded-4 shadow-sm border-0 mb-3" style="background-color: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important; border-radius: 12px;">
                                        <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12px;"><i class="fas fa-sitemap text-success mr-2"></i> 3. Hierarchy Nesting</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.4;">Select a parent menu if this behaves as a sub-menu navigation tier.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Form Area -->
                        <div class="col-lg-8 p-4 bg-light mb-0">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label class="dt-more-label mb-2">Menu Title</label>
                                        <input type="text" class="dt-more-input" name="title" placeholder="e.g. Dashboard" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label class="dt-more-label mb-2">Group Module</label>
                                        <div class="d-flex align-items-stretch w-100 dt-input-group">
                                            <select class="dt-more-select group-module-select flex-grow-1 m-0 rounded-right-none" name="group_modules" style="border-top-right-radius: 0 !important; border-bottom-right-radius: 0 !important; border-right: 0 !important;">
                                                <option value="">-- Choose Group Module --</option>
                                                <?php foreach($group_modules as $gm): ?>
                                                    <option value="<?= $gm['group_modules'] ?>"><?= $gm['group_modules'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button class="btn btn-primary add-group-btn m-0" type="button" style="border-top-left-radius: 0 !important; border-bottom-left-radius: 0 !important; font-size: 13px; font-weight: 600; padding: 0 15px; white-space: nowrap;"><i class="fas fa-plus mr-2"></i> Add</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="dt-more-label mb-2">URL</label>
                                        <input type="text" class="dt-more-input" name="url" placeholder="e.g. dashboard" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="dt-more-label mb-2">Icon</label>
                                        <input type="text" class="dt-more-input" name="icon" placeholder="e.g. fas fa-tachometer-alt" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group mb-3">
                                        <label class="dt-more-label mb-2">Parent Menu</label>
                                        <select class="dt-more-select" name="parent_id">
                                            <option value="0">None (Main Menu)</option>
                                            <?php foreach($main_menus as $mm): ?>
                                                <option value="<?= $mm['id'] ?>"><?= $mm['title'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="dt-more-label mb-2">Order</label>
                                        <input type="number" class="dt-more-input input-rupiah" name="menu_order" value="0" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3 border-0 bg-white justify-content-end">
                    <button type="button" class="btn-dt-cancel mr-2" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn-dt-apply px-4">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Menu Modal -->
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="editMenuModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <!-- Header Legacy Migrated -->
            <div class="modal-header modal-header-primary border-0 mh-premium">
                <div class="d-flex align-items-center">
                    <div class="mh-icon-badge">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="mh-title-wrap">
                        <h6 class="mh-title" id="editMenuModalLabel">Edit Menu</h6>
                        <small class="mh-subtitle">Modify and update existing information</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editMenuForm">
                <input type="hidden" name="id" id="edit_id">
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
                                <p class="text-muted small mb-4" style="font-size: 12px; line-height: 1.5;">Update existing structural elements. Changes persist across all roles using this menu.</p>
                                
                                <div class="d-flex flex-column gap-3">
                                    <div class="p-3 rounded-4 shadow-sm border-0 mb-3" style="background-color: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important; border-radius: 12px;">
                                        <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12px;"><i class="fas fa-sync text-warning mr-2"></i> 1. Live Sync</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.4;">Renaming menu titles changes the labels rendered in the user interfaces dynamically.</p>
                                    </div>
                                    <div class="p-3 rounded-4 shadow-sm border-0 mb-3" style="background-color: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important; border-radius: 12px;">
                                        <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size: 12px;"><i class="fas fa-folder-open text-info mr-2"></i> 2. Hierarchy Shifts</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.4;">Altering parent menus re-nests sub-navigation paths instantly.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Form Area -->
                        <div class="col-lg-8 p-4 bg-light mb-0">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label class="dt-more-label mb-2">Menu Title</label>
                                        <input type="text" class="dt-more-input" name="title" id="edit_title" required>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label class="dt-more-label mb-2">Group Module</label>
                                        <div class="d-flex align-items-stretch w-100 dt-input-group">
                                            <select class="dt-more-select group-module-select flex-grow-1 m-0" name="group_modules" id="edit_group_modules" style="border-top-right-radius: 0 !important; border-bottom-right-radius: 0 !important; border-right: 0 !important;">
                                                <option value="">-- Choose Group Module --</option>
                                                <?php foreach($group_modules as $gm): ?>
                                                    <option value="<?= $gm['group_modules'] ?>"><?= $gm['group_modules'] ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button class="btn btn-primary add-group-btn m-0" type="button" style="border-top-left-radius: 0 !important; border-bottom-left-radius: 0 !important; font-size: 13px; font-weight: 600; padding: 0 15px; white-space: nowrap;"><i class="fas fa-plus mr-2"></i> Add</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="dt-more-label mb-2">URL</label>
                                        <input type="text" class="dt-more-input" name="url" id="edit_url" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="dt-more-label mb-2">Icon</label>
                                        <input type="text" class="dt-more-input" name="icon" id="edit_icon" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group mb-3">
                                        <label class="dt-more-label mb-2">Parent Menu</label>
                                        <select class="dt-more-select" name="parent_id" id="edit_parent_id">
                                            <option value="0">None (Main Menu)</option>
                                            <?php foreach($main_menus as $mm): ?>
                                                <option value="<?= $mm['id'] ?>"><?= $mm['title'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="dt-more-label mb-2">Order</label>
                                        <input type="number" class="dt-more-input input-rupiah" name="menu_order" id="edit_menu_order" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3 border-0 bg-white justify-content-end">
                    <button type="button" class="btn-dt-cancel mr-2" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn-dt-apply px-4">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Add Group Module Modal -->
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="addGroupModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content border-0 shadow-lg">
            <!-- Header Legacy Migrated -->
<div class="modal-header modal-header-primary border-0 mh-premium">
    <div class="d-flex align-items-center">
        <div class="mh-icon-badge">
            <i class="fas fa-star"></i>
        </div>
        <div class="mh-title-wrap">
            <h6 class="mh-title" id="addGroupModalLabel">New Group Module</h6>
            <small class="mh-subtitle" >Manage and process information details</small>
        </div>
    </div>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
            <div class="modal-body p-4">
                <div class="form-group mb-0">
                    <label class="dt-more-label mb-2">Group Name</label>
                    <input type="text" class="form-control dt-more-input" id="new_group_modal_input" placeholder="e.g. Transaction">
                </div>
            </div>
            <div class="modal-footer px-0 pb-0 border-0 pt-0 mx-4 mb-4">
                <button type="button" class="btn-dt-cancel py-2 px-3 m-0" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn-dt-apply py-2 px-3 " id="saveNewGroupBtn">Add</button>
            </div>
        </div>
    </div>
</div>

<!-- ── AJAX Logic ── -->
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

    const CSRF_NAME = "<?php echo $this->security->get_csrf_token_name(); ?>";
    const CSRF_HASH = "<?php echo $this->security->get_csrf_hash(); ?>";

    // Live search filter
    $('#menuSearch').on('keyup', function() {
        var val = $(this).val().toLowerCase();
        $('#roleAccessTable tbody tr').each(function() {
            var text = $(this).find('td:nth-child(2)').text().toLowerCase();
            $(this).toggle(text.indexOf(val) > -1);
        });
    });

    // Toggle access AJAX
    $('.rbac-checkbox').on('change', function() {
        var $input  = $(this);
        var $row    = $input.closest('tr');
        var menuId  = $input.data('menu');
        var roleId  = $input.data('role');

        $row.addClass('saving');

        $.ajax({
            url: "<?= base_url('access-control/roles/change-access'); ?>",
            type: 'POST',
            data: {
                menuId: menuId,
                roleId: roleId,
                [CSRF_NAME]: CSRF_HASH
            },
            success: function() {
                $row.removeClass('saving').addClass('saved');
                setTimeout(function() { $row.removeClass('saved'); }, 500);
            },
            error: function() {
                $input.prop('checked', !$input.prop('checked'));
                $row.removeClass('saving');
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to update access.',
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

    // Add Menu AJAX
    $('#addMenuForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: "<?= base_url('menu/save/ajax'); ?>",
            type: 'POST',
            data: $(this).serialize() + "&" + CSRF_NAME + "=" + CSRF_HASH,
            dataType: 'json',
            success: function(res) {
                if(res.status == 'success') location.reload();
            }
        });
    });

    // Edit Menu - Load Data
    $('.edit-menu-btn').on('click', function() {
        var id = $(this).data('id');
        $.ajax({
            url: "<?= base_url('menu/get/'); ?>" + id,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#edit_id').val(data.id);
                $('#edit_title').val(data.title);
                
                var groupMod = data.group_modules ? data.group_modules.toString().trim() : "";
                if(groupMod && $("#edit_group_modules option[value='" + groupMod + "']").length === 0) {
                     $("#edit_group_modules").append(new Option(groupMod, groupMod));
                }
                $('#edit_group_modules').val(groupMod).trigger('change');
                
                $('#edit_url').val(data.url);
                $('#edit_icon').val(data.icon);
                $('#edit_parent_id').val(data.parent_id);
                $('#edit_menu_order').val(data.menu_order);
                $('#editMenuModal').modal('show');
            }
        });
    });

    // Update Menu AJAX
    $('#editMenuForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: "<?= base_url('menu/update-menu/ajax'); ?>",
            type: 'POST',
            data: $(this).serialize() + "&" + CSRF_NAME + "=" + CSRF_HASH,
            dataType: 'json',
            success: function(res) {
                if(res.status == 'success') location.reload();
            }
        });
    });

    // Delete Menu AJAX
    $('.delete-menu-btn').on('click', function() {
        var id = $(this).data('id');
        var title = $(this).data('title');
        Swal.fire({
            title: 'Delete Menu',
            text: 'Are you sure you want to delete "' + title + '"? This will also delete any sub-menus and revoke access from all roles.',
            icon: 'warning',
            showCancelButton: true,
            customClass: {
                popup: 'swal2-premium-popup',
                confirmButton: 'swal2-premium-confirm',
                cancelButton: 'swal2-premium-cancel'
            },
            buttonsStyling: false,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= base_url('menu/delete/ajax'); ?>",
                    type: 'POST',
                    data: { id: id, [CSRF_NAME]: CSRF_HASH },
                    dataType: 'json',
                    success: function(res) {
                        if(res.status == 'success') {
                            location.reload();
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: res.message,
                                icon: 'error',
                                customClass: {
                                    popup: 'swal2-premium-popup',
                                    confirmButton: 'swal2-premium-confirm'
                                },
                                buttonsStyling: false
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to delete menu due to a server error.',
                            icon: 'error',
                            customClass: {
                                popup: 'swal2-premium-popup',
                                confirmButton: 'swal2-premium-confirm'
                            },
                            buttonsStyling: false
                        });
                    }
                });
            }
        });
    });

    // Add New Group Module dynamically via Modal
    var activeGroupSelect = null;
    
    $('.add-group-btn').on('click', function() {
        activeGroupSelect = $(this).closest('.form-group').find('.group-module-select');
        $('#new_group_modal_input').val('');
        $('#addGroupModal').modal('show');
    });

    $('#saveNewGroupBtn').on('click', function() {
        var newGroup = $('#new_group_modal_input').val().trim();
        
        if(newGroup !== "") {
            var exists = false;
            var existingValue = "";
            
            // Check uniqueness (case-insensitive) on the active select
            if(activeGroupSelect) {
                activeGroupSelect.find('option').each(function() {
                    if ($(this).val().toLowerCase() === newGroup.toLowerCase()) {
                        exists = true;
                        existingValue = $(this).val();
                        return false; // break loop
                    }
                });
            }

            if (exists) {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Group Module "' + existingValue + '" sudah ada!',
                    icon: 'warning',
                    customClass: {
                        popup: 'swal2-premium-popup',
                        confirmButton: 'swal2-premium-confirm'
                    },
                    buttonsStyling: false
                });
                if(activeGroupSelect) {
                    activeGroupSelect.val(existingValue); // select the existing one
                }
                $('#addGroupModal').modal('hide');
            } else {
                // Add to all group module select fields so they are visually synced
                $('.group-module-select').each(function() {
                    $(this).append(new Option(newGroup, newGroup));
                });
                // Auto Select
                if(activeGroupSelect) {
                    activeGroupSelect.val(newGroup);
                }
                $('#addGroupModal').modal('hide');
            }
        }
    });

    // Handle enter key in the input modal
    $('#new_group_modal_input').on('keypress', function(e) {
        if(e.which == 13) {
            e.preventDefault();
            $('#saveNewGroupBtn').click();
        }
    });

});
</script>



