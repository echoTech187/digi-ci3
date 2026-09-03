<!-- Begin Page Content -->
<div>
    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title">
                <?= $title; ?>
                <span class="badge badge-primary" style="font-size:13px; font-weight:600; border-radius:8px; padding:5px 12px;">
                    <?= $role['role_name']; ?>
                </span>
            </h4>
            <p class="dt-page-subtitle">Manage menu structures and toggle access rights for this role.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn-dt-action btn-dt-action-primary border-0 d-flex align-items-center shadow-sm" id="toggleGuideBtn">
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
            <p class="drawer-desc">Manage deep hierarchical menu configurations and toggle granular access rights for the selected role.</p>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-sitemap text-primary mr-2"></i> Parent & Submenus</div>
                <p class="drawer-card-text">Distinguish main parent module tabs from nested sub-menus.</p>
            </div>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-toggle-on text-primary mr-2"></i> Access Granted</div>
                <p class="drawer-card-text">Toggle switches in the table column to instantly register or revoke access to paths.</p>
            </div>
        </div>
    </div>

    <!-- ── Access Configuration Card ── -->
    <div class="card border-0 shadow-sm dt-card">
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

        <div class="table-responsive">
            <table class="table dt-table mb-0" id="roleAccessTable" style="width:100%">
                <thead>
                    <tr>
                        <th style="width:50px">#</th>
                        <th>Menu</th>
                        <th>URL</th>
                        <th style="width:120px" class="text-center">Access</th>
                        <th style="width:100px" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; foreach ($menu as $m) : ?>
                        <tr>
                            <td class="text-muted"><?= $i++; ?></td>
                            <td>
                                <?php if ($m['parent_id'] > 0) : ?>
                                    <span class="text-muted mr-2" style="font-size: 13px;">ㄴ</span>
                                    <i class="<?= $m['icon']; ?> text-muted mr-2" style="font-size: 12px;"></i>
                                    <span class="text-secondary"><?= $m['title']; ?></span>
                                <?php else : ?>
                                    <i class="<?= $m['icon']; ?> text-primary mr-2"></i>
                                    <span class="font-weight-bold text-dark"><?= $m['title']; ?></span>
                                    <?php if (!empty($m['group_modules'])) : ?>
                                        <span class="badge badge-light border ml-2" style="font-size:10px;"><?= $m['group_modules']; ?></span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td><code class="text-primary"><?= $m['url']; ?></code></td>
                            <td class="text-center">
                                <label class="switch mb-0">
                                    <input type="checkbox" class="rbac-checkbox" data-role="<?= $role['id']; ?>" data-menu="<?= $m['id']; ?>" <?= check_access($role['id'], $m['id']); ?>>
                                    <span class="slider round"></span>
                                </label>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn-dt-action-icon btn-dt-action-edit edit-menu-btn" data-id="<?= $m['id']; ?>" title="Edit Menu"><i class="fas fa-pen fa-xs"></i></button>
                                <button type="button" class="btn-dt-action-icon btn-dt-action-delete delete-menu-btn" data-id="<?= $m['id']; ?>" data-title="<?= $m['title']; ?>" title="Delete Menu"><i class="fas fa-trash fa-xs"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ── Include Modals (Partial) ── -->
<?php $this->load->view('menu/partials/modal_role_access'); ?>

<!-- ── Include JavaScript Assets ── -->
<script src="<?= base_url('assets/js/role_access.js'); ?>"></script>
