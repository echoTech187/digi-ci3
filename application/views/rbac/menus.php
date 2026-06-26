<div>
    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title">Merchant Menu Management</h4>
            <p class="dt-page-subtitle">Organize and configure sidebar navigation items and their access permissions for the Merchant Portal</p>
        </div>
        <div class="dt-page-actions">
            <button class="btn-dt-action btn-dt-action-success" data-toggle="modal" data-target="#menuModal">
                <i class="fas fa-plus"></i> <span class="d-none d-md-block">Add Menu Item</span>
            </button>
        </div>
    </div>

    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">
        <!-- Toolbar -->
        <div class="dt-toolbar">
            <!-- LEFT: Search Wrapper -->
            <div class="dt-search-wrapper">
                <i class="fas fa-search dt-search-icon"></i>
                <input type="text" id="menuSearch" class="dt-search-input" placeholder="Search menu title or url...">
            </div>

            <!-- RIGHT: Legend -->
            <div class="dt-toolbar-filters">
                <span class="badge badge-pill badge-primary-soft text-primary px-3 py-2 mr-2">
                    <i class="fas fa-sitemap mr-1"></i> Hierarchical View
                </span>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table dt-table mb-0" id="menuTable" style="width:100%">
                <thead>
                    <tr>
                        <th width="50" class="text-center">SORT</th>
                        <th>MENU STRUCTURE</th>
                        <th>TYPE</th>
                        <th>PERMISSION</th>
                        <th>STATUS</th>
                        <th width="120" class="text-center">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $currentGroup = null;
                    foreach ($menus as $m): 
                        $isSub = ($m['parent_id'] !== NULL && $m['parent_id'] != 0);
                        
                        // Detect Group Change for Headings (Only for Main Menus)
                        $mGroup = isset($m['c_group']) ? ($m['c_group'] ?: 'General') : 'General';
                        if (!$isSub && strtolower($mGroup) !== strtolower($currentGroup ?? '')) {
                            $currentGroup = $mGroup;
                            echo '<tr class="bg-primary-soft"><td colspan="6" class="py-2 px-4"><span class="badge badge-primary px-3 py-1 text-uppercase" style="font-size:10px; letter-spacing:1px;"><i class="fas fa-layer-group mr-2"></i> Group: ' . $currentGroup . '</span></td></tr>';
                        }

                        $typeBadge = !$isSub 
                            ? '<span class="badge badge-primary-soft text-primary px-2 py-1" style="font-size:10px;">MAIN MENU</span>'
                            : '<span class="badge badge-light text-dark border px-2 py-1" style="font-size:10px;">SUB MENU</span>';
                        
                        $indent = $isSub 
                            ? '<span class="text-muted ml-4 mr-2" style="font-size:18px;">ㄴ</span>' 
                            : '<i class="' . ($m['c_icon'] ?: 'fas fa-circle') . ' text-primary mr-2" style="width: 20px; text-align: center;"></i>';
                    ?>
                    <tr class="<?= $isSub ? 'bg-light-subtle' : '' ?>">
                        <td class="text-center font-weight-bold text-muted small"><?= $m['c_sortOrder']; ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <?= $indent; ?>
                                <div class="d-flex flex-column">
                                    <span class="<?= $isSub ? '' : 'font-weight-bold text-dark' ?>" style="font-size: 14px;">
                                        <?= $m['c_label']; ?>
                                    </span>
                                    <code class="small text-muted" style="font-size: 10px; letter-spacing: -0.2px;">
                                        <?= $m['c_url'] ?: '/'; ?>
                                    </code>
                                </div>
                            </div>
                        </td>
                        <td><?= $typeBadge; ?></td>
                        <td>
                            <?php if ($m['ref_permissionId']): ?>
                                <div class="d-flex flex-column">
                                    <span class="badge badge-secondary-soft text-secondary px-2 py-1 mb-1" style="font-size:9px; width: fit-content;">
                                        <i class="fas fa-key mr-1"></i> <?= $m['permission_code'] ?>
                                    </span>
                                    <small class="text-muted" style="font-size: 9px;"><?= $m['c_group'] ?></small>
                                </div>
                            <?php else: ?>
                                <span class="text-muted small italic" style="font-size: 10px;">Public / No Restriction</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= $m['c_isActive'] 
                                ? '<span class="badge badge-pill badge-success-soft text-success px-3 py-1">Active</span>' 
                                : '<span class="badge badge-pill badge-danger-soft text-danger px-3 py-1">Hidden</span>'; ?>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-light text-primary btn-edit-menu shadow-none px-3" 
                                    data-json='<?= json_encode($m); ?>'>
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ── Menu Modal ── -->
<div class="modal fade" id="menuModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="<?= base_url('merchant/access-control/menus/save'); ?>" method="POST" class="w-100">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <div class="modal-content border-0 shadow-lg rounded-xxl overflow-hidden">
                <div class="modal-header modal-header-primary border-0 mh-premium">
                    <div class="mh-icon-badge">
                        <img src="<?= base_url('assets/img/profile/default.jpg'); ?>" class="mh-avatar-img">
                    </div>
                    <div class="mh-title-wrap">
                        <h5 class="mh-title" id="menuModalLabel">Menu Item Details</h5>
                        <span class="mh-subtitle">Configure label, routing, and access requirements</span>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="menu_id" id="menu_id">
                    
                    <div class="form-group mb-4">
                        <label class="text-uppercase small font-weight-bold text-muted mb-2">Display Title</label>
                        <input type="text" name="c_label" id="m_title" class="dt-more-input font-weight-bold" placeholder="e.g. Transaction History" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label class="text-uppercase small font-weight-bold text-muted mb-2">URL Path</label>
                                <input type="text" name="c_url" id="m_url" class="dt-more-input" placeholder="admin/history">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label class="text-uppercase small font-weight-bold text-muted mb-2">Icon Class</label>
                                <input type="text" name="c_icon" id="m_icon" class="dt-more-input" placeholder="fas fa-list">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label class="text-uppercase small font-weight-bold text-muted mb-2">Group Name (Sidebar Header)</label>
                                <input type="text" name="c_group" id="m_group" class="dt-more-input" placeholder="e.g. CORE SYSTEM">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label class="text-uppercase small font-weight-bold text-muted mb-2">Parent Menu</label>
                                <select name="parent_id" id="m_parent_id" class="dt-more-select">
                                    <option value="">None (Top Level)</option>
                                    <?php foreach ($main_menus as $mm): ?>
                                    <option value="<?= $mm['id']; ?>"><?= $mm['c_label']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-4">
                                <label class="text-uppercase small font-weight-bold text-muted mb-2">Sort Order</label>
                                <input type="number" name="c_sortOrder" id="m_sortOrder" class="dt-more-input" value="0">
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="text-uppercase small font-weight-bold text-muted mb-2">Required Permission</label>
                        <div class="row">
                            <div class="col-md-6">
                                <select name="ref_permissionId" id="m_perm" class="dt-more-select">
                                    <option value="">None (Visible to all authorized users)</option>
                                    <?php foreach ($permissions_grouped as $group => $perms): ?>
                                        <optgroup label="<?= strtoupper($group); ?>">
                                            <?php foreach ($perms as $p): ?>
                                            <option value="<?= $p['id']; ?>"><?= $p['c_code']; ?> - <?= $p['c_name']; ?></option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="new_permission_code" id="m_new_perm" class="dt-more-input" placeholder="OR Create New Code (e.g. view_xyz)">
                                <small class="text-primary italic" style="font-size: 9px;">*System will auto-create if filled</small>
                            </div>
                        </div>
                    </div>

                    <div class="custom-control custom-switch dt-switch">
                        <input type="checkbox" name="c_isActive" value="1" class="custom-control-input" id="m_isActive" checked>
                        <label class="custom-control-label font-weight-bold text-dark" for="m_isActive">Active in Sidebar</label>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light px-4 font-weight-bold" data-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm font-weight-bold">SAVE MENU ITEM</button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
.rounded-xxl { border-radius: 28px !important; }
.bg-light-subtle { background-color: var(--bg-body); }
.italic { font-style: italic; }
.dt-switch .custom-control-input:checked ~ .custom-control-label::before {
    background-color: var(--success);
    border-color: var(--success);
}
.dt-divider { height: 1px; background: var(--border-color); }
</style>

<script>
$(document).ready(function() {
    // Live search filter (since it's a static hierarchical table)
    $('#menuSearch').on('keyup', function() {
        var val = $(this).val().toLowerCase();
        $('#menuTable tbody tr').each(function() {
            var text = $(this).text().toLowerCase();
            $(this).toggle(text.indexOf(val) > -1);
        });
    });

    // Handle Edit Action
    $(document).on('click', '.btn-edit-menu', function() {
        const m = $(this).data('json');
        
        $('#menu_id').val(m.id);
        $('#m_title').val(m.c_label);
        $('#m_url').val(m.c_url);
        $('#m_icon').val(m.c_icon);
        $('#m_parent_id').val(m.parent_id).trigger('change');
        $('#m_sortOrder').val(m.c_sortOrder);
        $('#m_group').val(m.c_group);
        $('#m_perm').val(m.ref_permissionId).trigger('change');
        $('#m_new_perm').val(''); // Clear on edit
        $('#m_isActive').prop('checked', m.c_isActive == 1);

        $('#menuModal').modal('show');
    });

    // Reset modal on close
    $('#menuModal').on('hidden.bs.modal', function () {
        $('#menu_id').val('');
        $('#m_title').val('');
        $('#m_url').val('');
        $('#m_icon').val('');
        $('#m_parent_id').val('').trigger('change');
        $('#m_sortOrder').val('0');
        $('#m_group').val('');
        $('#m_perm').val('').trigger('change');
        $('#m_new_perm').val(''); // Clear on close
        $('#m_isActive').prop('checked', true);
    });

    // Success flash notification (Handled globally in footer)
});
</script>
