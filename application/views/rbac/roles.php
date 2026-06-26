<div>
    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title">Merchant Role Management</h4>
            <p class="dt-page-subtitle">Define and manage access levels and permissions for the Merchant Portal</p>
        </div>
        <div class="dt-page-actions">
            <button class="btn-dt-action btn-dt-action-success" data-toggle="modal" data-target="#roleModal">
                <i class="fas fa-plus"></i> <span class="d-none d-md-block">Create New Role</span>
            </button>
        </div>
    </div>

    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">
        <!-- ── Toolbar ── -->
        <div class="dt-toolbar">
            <!-- LEFT: Global Search -->
            <div class="dt-search-wrapper">
                <i class="fas fa-search dt-search-icon"></i>
                <input type="text" id="dt-search" class="dt-search-input" placeholder="Search roles..." value="<?= $this->session->userdata('last_dt_search_roles') ?: '' ?>">
            </div>

            <!-- RIGHT: Legend -->
            <div class="dt-toolbar-filters">
                <span class="badge badge-success-soft text-success px-3 py-2 mr-2">
                    <i class="fas fa-shield-alt mr-1"></i> System Roles Protected
                </span>
            </div>
        </div>

        <!-- ── Table ── -->
        <div class="table-responsive">
            <table id="roleTable" class="table dt-table mb-0" style="width:100%">
                <thead>
                    <tr>
                        <th width="80">ID</th>
                        <th>Role Name</th>
                        <th>Type</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <div class="dt-footer" id="dt-footer-container"></div>
    </div>
</div>

<!-- ── Role Modal ── -->
<div class="modal fade" id="roleModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <form action="<?= base_url('merchant/access-control/roles/save'); ?>" method="POST" class="w-100">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <div class="modal-content border-0 shadow-lg rounded-xxl overflow-hidden">
                <div class="modal-header modal-header-primary border-0 mh-premium">
                    <div class="mh-icon-badge">
                        <img src="<?= base_url('assets/img/profile/default.jpg'); ?>" class="mh-avatar-img">
                    </div>
                    <div class="mh-title-wrap">
                        <h5 class="mh-title" id="roleModalLabel">Role Configuration</h5>
                        <span class="mh-subtitle">Define role identity and assign granular permissions</span>
                    </div>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" name="role_id" id="role_id">
                    
                    <!-- Role Identity Section -->
                    <div class="form-group mb-4">
                        <label class="text-uppercase small font-weight-bold text-muted mb-2">Role Name</label>
                        <div class="input-group shadow-none border-0">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-transparent border-0 px-0 mr-3 text-primary">
                                    <i class="fas fa-id-badge"></i>
                                </span>
                            </div>
                            <input type="text" name="c_name" id="c_name" class="form-control border-0 shadow-none font-weight-bold h-auto py-2" placeholder="e.g. Finance Administrator" required>
                        </div>
                        <small id="system-notice" class="text-info mt-2 d-none">
                            <i class="fas fa-info-circle mr-1"></i> System roles cannot be renamed to ensure platform stability.
                        </small>
                    </div>

                    <div class="dt-divider mb-4"></div>

                    <!-- Permissions Section -->
                    <h6 class="text-uppercase small font-weight-bold text-muted mb-4 d-flex align-items-center">
                        <i class="fas fa-key mr-2 text-primary"></i> Granular Permissions
                    </h6>
                    
                    <div class="row" style="max-height: 50vh; overflow-y: auto; overflow-x: hidden; padding-right: 10px;">
                        <?php foreach ($permissions as $group => $perms): ?>
                        <div class="col-12 mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="badge badge-pill badge-primary-soft text-primary px-3 py-1 font-weight-bold text-uppercase" style="letter-spacing: 1px; font-size: 10px;">
                                    <?= $group; ?>
                                </div>
                                <div class="flex-grow-1 ml-3 border-bottom" style="opacity: 0.1;"></div>
                            </div>
                            <div class="row">
                                <?php foreach ($perms as $p): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="perm-card p-3 border rounded-xl h-100">
                                        <div class="custom-control custom-checkbox dt-checkbox">
                                            <input type="checkbox" name="permissions[]" value="<?= $p['id']; ?>" 
                                                   class="custom-control-input perm-check" id="p_<?= $p['id']; ?>">
                                            <label class="custom-control-label pl-2" for="p_<?= $p['id']; ?>">
                                                <div class="font-weight-bold text-dark small"><?= $p['c_description']; ?></div>
                                                <code class="text-muted" style="font-size: 10px;"><?= $p['c_name']; ?></code>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light px-4 font-weight-bold" data-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm font-weight-bold">SAVE CHANGES</button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
.rounded-xxl { border-radius: 28px !important; }
.rounded-xl { border-radius: 16px !important; }
.perm-card { transition: all 0.2s; background: var(--bg-card); border: 1.5px solid var(--border-color) !important; }
.perm-card:hover { border-color: var(--primary) !important; background: var(--bg-body) !important; }
.dt-divider { height: 1px; background: var(--border-color); }

/* Custom Checkbox Styling */
.dt-checkbox .custom-control-input:checked ~ .custom-control-label::before {
    background-color: var(--primary);
    border-color: var(--primary);
}
.dt-checkbox .custom-control-label::before {
    border-radius: 6px;
    width: 20px;
    height: 20px;
    top: 2px;
}
.dt-checkbox .custom-control-label::after {
    width: 20px;
    height: 20px;
    top: 2px;
}
</style>

<script>
$(document).ready(function() {
    // Initialize Server-side DataTable
    var table = initServerDataTable("#roleTable", "<?= base_url('merchant/access-control/roles') ?>", [
        { 
            data: 'id', 
            className: 'text-center',
            render: function(data) {
                return `<code class="text-muted font-weight-bold">#${data}</code>`;
            }
        },
        { 
            data: 'c_name',
            render: function(data) {
                return `<div class="font-weight-bold text-dark h6 mb-0">${data}</div>`;
            }
        },
        { 
            data: 'c_isSystem',
            className: 'text-center',
            render: function(data) {
                if (data == 1) {
                    return `<span class="badge badge-pill badge-success-soft text-success px-3 py-1">
                                <i class="fas fa-check-circle mr-1"></i> System
                            </span>`;
                } else {
                    return `<span class="badge badge-pill badge-secondary-soft text-secondary px-3 py-1">
                                <i class="fas fa-user-tag mr-1"></i> Custom
                            </span>`;
                }
            }
        },
        { 
            data: 'id',
            orderable: false,
            className: 'text-center',
            render: function(data, type, row) {
                return `
                    <button class="btn btn-sm btn-light text-primary btn-edit-role shadow-none px-3" 
                            data-id="${data}" 
                            data-name="${row.c_name.replace(/"/g, '&quot;')}"
                            data-is-system="${row.c_isSystem}">
                        <i class="fas fa-cog mr-1"></i> Configure
                    </button>
                `;
            }
        }
    ], {
        "ordering": false,
        "order": [[1, 'asc']],
        "search": { "search": "<?= $this->session->userdata('last_dt_search_roles') ?: '' ?>" }
    });

    // ── TOPBAR SEARCH SYNC ──
    // Listen for search events from the topbar
    $(document).on('topbar-search', function(e, searchValue) {
        $('#dt-search').val(searchValue);
        table.search(searchValue).draw();
    });

    // Global search with Debounce
    $('#dt-search').on('input', debounce(function() {
        table.search(this.value).draw();
    }, 400));


    // Handle Edit Action
    $(document).on('click', '.btn-edit-role', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const isSystem = $(this).data('is-system');
        
        $('#role_id').val(id);
        $('#c_name').val(name);
        
        if (isSystem == 1) {
            $('#c_name').attr('readonly', true);
            $('#system-notice').removeClass('d-none');
        } else {
            $('#c_name').attr('readonly', false);
            $('#system-notice').addClass('d-none');
        }

        $('.perm-check').prop('checked', false);

        // Load permissions via AJAX
        $.getJSON('<?= base_url('merchant/access-control/roles/permissions/'); ?>' + id, function(data) {
            if (Array.isArray(data)) {
                data.forEach(function(pid) {
                    $('#p_' + pid).prop('checked', true);
                });
            }
        });

        $('#roleModal').modal('show');
    });

    // Reset modal on close
    $('#roleModal').on('hidden.bs.modal', function () {
        $('#role_id').val('');
        $('#c_name').val('').attr('readonly', false);
        $('.perm-check').prop('checked', false);
        $('#system-notice').addClass('d-none');
    });

    // Success flash notification (Handled globally in footer)
});
</script>
