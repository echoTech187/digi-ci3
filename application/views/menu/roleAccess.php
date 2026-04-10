<!-- Begin Page Content -->
<div class="container-fluid pb-4">

    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title">
                <?= $title; ?>
                <span class="badge badge-primary ml-2" style="font-size:13px; font-weight:600; border-radius:8px; padding:5px 12px;">
                    <?= $role['role_name']; ?>
                </span>
            </h4>
            <p class="dt-page-subtitle">Manage menu structures and toggle access rights for this role.</p>
        </div>
    </div>

    <!-- ── Alert ── -->
    <?php if ($this->session->flashdata('message')): ?>
        <div class="alert alert-success border-0 shadow-sm animate__animated animate__fadeIn mb-4">
            <i class="fas fa-check-circle mr-2"></i> <?= $this->session->flashdata('message'); ?>
        </div>
    <?php endif; ?>

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
                <button type="button" class="btn-dt-chip-action btn-dt-action-primary" data-toggle="modal" data-target="#addMenuModal" style="border-radius:10px; font-weight:600; font-size:14px;">
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
                        $indent = $isSub ? "<span class='text-muted mr-2 ml-4' style='font-size:18px;'>ㄴ</span>" : "<i class='".$m['icon']." mr-2 text-primary'></i>";

                        $ci = get_instance();
                        $ci->db->where('role_id', $role['id']);
                        $ci->db->where('menu_id', $m['id']);
                        $result = $ci->db->get('user_access_menu');
                        $checked = ($result->num_rows() > 0);
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
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-icon-only text-primary edit-menu-btn" data-id="<?= $m['id']; ?>" title="Edit Menu">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-icon-only text-danger delete-menu-btn" data-id="<?= $m['id']; ?>" data-title="<?= $m['title']; ?>" title="Delete Menu">
                                    <i class="fas fa-trash"></i>
                                </button>
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
<div class="modal fade" id="addMenuModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header modal-header-primary border-0 py-3">
                <h5 class="modal-title font-weight-bold text-white">
                    <i class="fas fa-plus-circle mr-2"></i>New Menu
                </h5>
                <button type="button" class="close text-white outline-none" data-dismiss="modal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addMenuForm">
                <div class="modal-body p-4">
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
                                    <button class="btn btn-primary add-group-btn m-0" type="button" style="border-top-left-radius: 0 !important; border-bottom-left-radius: 0 !important; font-size: 13px; font-weight: 600; padding: 0 15px; white-space: nowrap;"><i class="fas fa-plus"></i> Add</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="dt-more-label mb-2">URL</label>
                                <input type="text" class="dt-more-input" name="url" placeholder="e.g. admin/dashboard" required>
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
                                <input type="number" class="dt-more-input" name="menu_order" value="0" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-0 pb-0 border-0 pt-3 mx-4 mb-4">
                    <button type="button" class="btn-dt-cancel" data-dismiss="modal" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn-dt-apply px-4 ml-2">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Menu Modal -->
<div class="modal fade" id="editMenuModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header modal-header-primary border-0 py-3">
                <h5 class="modal-title font-weight-bold text-white">
                    <i class="fas fa-edit mr-2"></i>Edit Menu
                </h5>
                <button type="button" class="close text-white outline-none" data-dismiss="modal" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editMenuForm">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body p-4">
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
                                    <button class="btn btn-primary add-group-btn m-0" type="button" style="border-top-left-radius: 0 !important; border-bottom-left-radius: 0 !important; font-size: 13px; font-weight: 600; padding: 0 15px; white-space: nowrap;"><i class="fas fa-plus"></i> Add</button>
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
                                <input type="number" class="dt-more-input" name="menu_order" id="edit_menu_order" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-0 pb-0 border-0 pt-3 mx-4 mb-4">
                    <button type="button" class="btn-dt-cancel" data-dismiss="modal" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn-dt-apply px-4 ml-2">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Add Group Module Modal -->
<div class="modal fade" id="addGroupModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary border-0 py-3">
                <h6 class="modal-title font-weight-bold text-white">
                    <i class="fas fa-plus-circle mr-2"></i>New Group Module
                </h6>
                <button type="button" class="close text-white outline-none" data-dismiss="modal" aria-label="Close">
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
                <button type="button" class="btn-dt-apply py-2 px-3 ml-2" id="saveNewGroupBtn">Add</button>
            </div>
        </div>
    </div>
</div>

<!-- ── AJAX Logic ── -->
<script>
$(document).ready(function() {
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
            url: "<?= base_url('menu/changeAccess'); ?>",
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
                alert('Failed to update access.');
            }
        });
    });

    // Add Menu AJAX
    $('#addMenuForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: "<?= base_url('menu/saveMenuAjax'); ?>",
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
            url: "<?= base_url('menu/getMenuById/'); ?>" + id,
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
            url: "<?= base_url('menu/updateMenuAjax'); ?>",
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
        if(confirm('Are you sure you want to delete "' + title + '"? This will also delete any sub-menus and revoke access from all roles.')) {
            $.ajax({
                url: "<?= base_url('menu/deleteMenuAjax'); ?>",
                type: 'POST',
                data: { id: id, [CSRF_NAME]: CSRF_HASH },
                dataType: 'json',
                success: function(res) {
                    if(res.status == 'success') location.reload();
                }
            });
        }
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
                alert('Group Module "' + existingValue + '" sudah ada!');
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
