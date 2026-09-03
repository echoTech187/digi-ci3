<!-- ── Modal Add Menu ── -->
<div class="modal fade bd-example-modal-lg" id="addMenuModal" tabindex="-1" role="dialog" aria-labelledby="addMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header modal-header-primary border-0 mh-premium">
                <div class="d-flex align-items-center">
                    <div class="mh-icon-badge"><i class="fas fa-plus"></i></div>
                    <div class="mh-title-wrap">
                        <h6 class="mh-title" id="addMenuModalLabel">Add New Menu</h6>
                        <small class="mh-subtitle">Configure module path and role access node</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="addMenuForm">
                <div class="modal-body p-0 bg-light">
                    <div class="d-flex g-0 w-100 flex-column flex-lg-row">
                        <div class="col-lg-4 p-4 d-flex flex-column mb-0" style="background: var(--bg-body); border-right: 1px solid rgba(255,255,255,0.05);">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width:40px;height:40px;flex-shrink:0;"><i class="fas fa-info-circle fa-lg"></i></div>
                                <h6 class="fw-bold text-primary mb-0" style="font-size:15px;">Configuration Guide</h6>
                            </div>
                            <p class="text-muted small mb-3" style="font-size:12px;line-height:1.5;">Define how the new navigation item connects to controllers and views.</p>
                        </div>
                        <div class="col-lg-8 p-4 bg-light mb-0">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label class="dt-more-label mb-2">Menu Title</label>
                                        <input type="text" class="dt-more-input" name="title" required placeholder="e.g. Transactions">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mb-3">
                                        <label class="dt-more-label mb-2">Group Module</label>
                                        <div class="d-flex align-items-stretch w-100 dt-input-group">
                                            <select class="dt-more-select group-module-select flex-grow-1 m-0" name="group_modules" style="border-top-right-radius: 0 !important; border-bottom-right-radius: 0 !important; border-right: 0 !important;">
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
                                        <input type="text" class="dt-more-input" name="url" required placeholder="e.g. finance/transactions">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="dt-more-label mb-2">Icon</label>
                                        <input type="text" class="dt-more-input" name="icon" required placeholder="e.g. fas fa-exchange-alt">
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
                                        <input type="number" class="dt-more-input" name="menu_order" value="1" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3 border-0 bg-white justify-content-end">
                    <button type="button" class="btn-dt-cancel mr-2" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn-dt-apply px-4">Create Menu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── Modal Edit Menu ── -->
<div class="modal fade bd-example-modal-lg" id="editMenuModal" tabindex="-1" role="dialog" aria-labelledby="editMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header modal-header-primary border-0 mh-premium">
                <div class="d-flex align-items-center">
                    <div class="mh-icon-badge"><i class="fas fa-edit"></i></div>
                    <div class="mh-title-wrap">
                        <h6 class="mh-title" id="editMenuModalLabel">Edit Menu Node</h6>
                        <small class="mh-subtitle">Update menu properties and route hierarchy</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="editMenuForm">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body p-0 bg-light">
                    <div class="d-flex g-0 w-100 flex-column flex-lg-row">
                        <div class="col-lg-4 p-4 d-flex flex-column mb-0" style="background: var(--bg-body); border-right: 1px solid rgba(255,255,255,0.05);">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width:40px;height:40px;flex-shrink:0;"><i class="fas fa-info-circle fa-lg"></i></div>
                                <h6 class="fw-bold text-primary mb-0" style="font-size:15px;">Modification Guide</h6>
                            </div>
                            <p class="text-muted small mb-3" style="font-size:12px;line-height:1.5;">Update routes and parents carefully to preserve access rights.</p>
                        </div>
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
                                        <input type="number" class="dt-more-input" name="menu_order" id="edit_menu_order" required>
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

<!-- ── Add Group Module Modal ── -->
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="addGroupModal" tabindex="-1" role="dialog" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header modal-header-primary border-0 mh-premium">
                <div class="d-flex align-items-center">
                    <div class="mh-icon-badge"><i class="fas fa-star"></i></div>
                    <div class="mh-title-wrap">
                        <h6 class="mh-title" id="addGroupModalLabel">New Group Module</h6>
                        <small class="mh-subtitle">Create module namespace</small>
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
                <button type="button" class="btn-dt-apply py-2 px-3" id="saveNewGroupBtn">Add</button>
            </div>
        </div>
    </div>
</div>
