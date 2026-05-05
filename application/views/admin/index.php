<!-- Begin Page Content -->
<div>

    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title"><?= $title; ?></h4>
            <p class="dt-page-subtitle">Manage administrator accounts, access levels, and role assignments.</p>
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

    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">

        <!-- ── Toolbar ── -->
        <div class="dt-toolbar py-3 px-4">
            <!-- LEFT: Global Search -->
            <div class="dt-search-wrapper">
                <i class="fas fa-search dt-search-icon"></i>
                <input type="text" id="adminGlobalSearch" class="dt-search-input" placeholder="Search by email, name, or role...">
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
                        <th>STATUS</th>
                        <th>LEVEL</th>
                        <th>ROLE</th>
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

<script>
    $(document).ready(function () {
        var table = initServerDataTable('#adminTable', "<?= base_url('admin/listAdmin') ?>", [
                { data: 'no', orderable: false, className: 'text-center' },
                { data: 'c_email', className: 'font-weight-bold text-primary small' },
                { data: 'c_name', className: 'font-weight-bold' },
                { data: 'c_status', render: function(data) {
                    var d = (data || '').toLowerCase();
                    var cls = 'secondary';
                    if (d === 'active') cls = 'success';
                    else if (d === 'blocked' || d === 'freeze') cls = 'danger';
                    else if (d === 'pending') cls = 'warning';
                    return '<span class="badge badge-' + cls + ' px-2 py-1">' + data + '</span>';
                }},
                { data: 'c_level', render: function(data) {
                    return '<span class="badge badge-light text-dark border px-2 py-1 text-uppercase small">' + data + '</span>';
                }},
                { data: 'role_name', className: 'text-dark' },
                {
                    data: null, 
                    orderable: false, 
                    className: 'text-center',
                    render: function(data, type, row) {
                        return `
                            <div class="dropdown">
                                <button class="btn btn-sm text-muted rounded-circle p-2 border-0 bg-transparent" type="button" data-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-2">
                                    <li>
                                        <button type="button" class="dropdown-item py-2 edit-btn" 
                                            data-toggle="modal" data-target=".manageUserModal" 
                                            data-id="${row.id}" 
                                            data-name="${row.c_name}" 
                                            data-role="${row.role_id}" 
                                            data-status="${row.c_status}" 
                                            data-level="${row.c_level}">
                                            <i class="fas fa-edit text-primary mr-2"></i> Manage Account
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        `;
                    }
                }
            ]);

        // Global Search with Debounce
        $('#adminGlobalSearch').on('input', debounce(function() {
            table.search(this.value).draw();
        }, 400));

        // Fill modal on edit button click
        $('#adminTable').on('click', '.edit-btn', function () {
            var $btn = $(this);
            $('#c_name').val(($btn.attr('data-name') || '').trim());
            $('#c_level').val(($btn.attr('data-level') || '').trim());
            
            var roleId = ($btn.attr('data-role') || '').trim();
            $('#role_id').val(roleId).trigger('change');
            
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

            $('#c_password').val('');
            $('#manageUserModalLabel').text('Manage Admin Account');
            const id = $btn.attr('data-id');
            $('#manageUserForm').attr('action', '<?= base_url('admin/manageUsers') ?>/' + id);
        });
    });
</script>

<!-- ── Manage User Modal ── -->
<div class="modal fade manageUserModal" id="manageUserModal" tabindex="-1" role="dialog" aria-labelledby="manageUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <!-- Header Legacy Migrated -->
            <div class="modal-header modal-header-primary border-0 mh-premium">
                <div class="d-flex align-items-center">
                    <div class="mh-icon-badge">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="mh-title-wrap">
                        <h6 class="mh-title" id="manageUserModalLabel">MANAGE ADMIN ACCOUNT</h6>
                        <small class="mh-subtitle" >Manage and process information details</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="manageUserForm" action="<?= base_url('admin/manageUsers'); ?>" method="post">
                <div class="modal-body p-4 text-dark">
                    <div class="mb-3">
                        <label class="dt-more-label mb-2">Full Name</label>
                        <input type="text" class="dt-more-input" id="c_name" name="c_name" placeholder="Admin full name">
                    </div>
                    <div class="mb-3">
                        <label class="dt-more-label mb-2">Level</label>
                        <input type="text" class="dt-more-input" id="c_level" name="c_level" placeholder="e.g. superadmin">
                    </div>
                    <div class="mb-3">
                        <label class="dt-more-label mb-2">New Password (leave blank to keep current)</label>
                        <input type="password" class="dt-more-input" id="c_password" name="c_password" placeholder="Enter new password">
                    </div>
                    <div class="mb-3">
                        <label class="dt-more-label mb-2">Role</label>
                        <select class="dt-more-select" id="role_id" required name="role_id">
                            <option value="" selected disabled>Select role</option>
                            <?php foreach ($roles as $row): ?>
                                <option value="<?= $row->id ?>"><?= $row->role_name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="dt-more-label mb-2">Status</label>
                        <select class="dt-more-select" id="c_status" required name="c_status">
                            <option value="" selected disabled>Select status</option>
                            <option value="Pending">Pending</option>
                            <option value="Active">Active</option>
                            <option value="Blocked">Blocked</option>
                            <option value="Freeze">Freeze</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer px-4 pb-4 border-0 pt-0">
                    <button type="button" class="btn-dt-cancel" data-dismiss="modal">CLOSE</button>
                    <button type="submit" class="btn-dt-apply px-4 ">
                        <i class="fas fa-save  mr-2"></i> SAVE CHANGES
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
