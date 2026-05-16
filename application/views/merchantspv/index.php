<!-- Begin Page Content -->
<div>

    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title text-dark fw-700">Merchant Supervisor</h4>
            <p class="dt-page-subtitle text-muted">Manage and organize merchant supervisors and their assigned merchants.</p>
        </div>
    </div>

    <!-- ── KPI Summary Cards ── -->
    

    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">

        <!-- ── Toolbar ── -->
        <div class="dt-toolbar py-3 px-4">
            <div class="dt-toolbar-left">
                <div class="dt-search-wrapper">
                    <i class="fas fa-search dt-search-icon"></i>
                    <input type="text" id="dt-global-search" class="dt-search-input" placeholder="Search supervisors...">
                </div>
            </div>
            <div class="dt-toolbar-right">
                <div class="dt-filter-group">
                    <div class="d-flex" style="gap:6px;">
                        <button type="button" class="btn-dt-chip-action btn-dt-primary" data-toggle="modal" data-target="#registerMerchantSpv">
                            <i class="fas fa-plus mr-1 mr-2"></i> Add Supervisor
                        </button>
                    </div>
                </div>
            </div>
        </div>

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
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <!-- Header Legacy Migrated -->
            <div class="modal-header modal-header-primary border-0 mh-premium">
                <div class="d-flex align-items-center">
                    <div class="mh-icon-badge">
                        <i class="fas fa-store"></i>
                    </div>
                    <div class="mh-title-wrap">
                        <h6 class="mh-title"  id="registerMerchantSpvLabel">Register Merchant SPV</h6>
                        <small class="mh-subtitle" >Manage and process information details</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 text-dark">
                <form method="post" action="<?= base_url('admin/registerMerchantSpv'); ?>">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                    
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="dt-more-label mb-2">SPV Name</label>
                            <input type="text" class="dt-more-input" required name="c_name" placeholder="Full Name">
                        </div>
                        <div class="col-md-6">
                            <label class="dt-more-label mb-2">Username</label>
                            <input type="text" class="dt-more-input" required name="c_username" placeholder="username123">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="dt-more-label mb-2">Email Address</label>
                        <input type="email" class="dt-more-input" required name="c_email" placeholder="email@example.com">
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="dt-more-label mb-2">Password</label>
                            <input type="password" class="dt-more-input" required name="c_password" placeholder="••••••••">
                        </div>
                        <div class="col-md-6">
                            <label class="dt-more-label mb-2">Confirm Password</label>
                            <input type="password" class="dt-more-input" required name="c_confirmPassword" placeholder="••••••••">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="dt-more-label mb-2">Assigned Merchants</label>
                        <select class="form-control select2-merchant" id="c_merchant_spv" name="c_merchant_spv[]" multiple="multiple" style="width: 100%;" required>
                        </select>
                        <div class="form-text text-muted small mt-2">Search and select one or more merchants for this supervisor.</div>
                    </div>

                    <div class="mb-4">
                        <label class="dt-more-label mb-2 d-block">Account Status</label>
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

                    <div class="modal-footer px-0 pb-0 border-0 pt-3 mt-2">
                        <button type="button" class="btn-dt-cancel" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn-dt-apply px-4 ">
                            <i class="fas fa-save  mr-2"></i> Register Supervisor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/server-datatables.js') ?>"></script>
<script>
$(document).ready(function() {
    const table = initServerDataTable("#merchantSpvTable", "<?= base_url('admin/merchant_spv') ?>", [
            { "data": "no", "className": "text-center" },
            { "data": "c_name", "className": "font-weight-bold text-dark" },
            { 
                "data": "c_username",
                "render": function(data) {
                    return `<span class="badge badge-light text-dark text-dark border px-2 py-1">${data}</span>`;
                }
            },
            { "data": "c_email" },
            { 
                "data": "c_status",
                "render": function(data) {
                    let statusClass = 'secondary';
                    if(data == 'Active') statusClass = 'success';
                    else if(data == 'Pending') statusClass = 'warning';
                    else if(data == 'Blocked' || data == 'Freeze') statusClass = 'danger';
                    return `<span class="badge badge-${statusClass}">${data}</span>`;
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
                                    <a class="dropdown-item" href="<?= base_url('admin/listMerchants/') ?>${row.id}">
                                        <i class="fas fa-store text-primary mr-2"></i> View Merchants
                                    </a>
                                </li>
                                <li class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger delete-btn" href="javascript:void(0)" data-href="<?= base_url('admin/deleteMerchantSpv/') ?>${row.id}">
                                        <i class="fas fa-trash-alt mr-2"></i> Delete
                                    </a>
                                </li>
                            </ul>
                        </div>
                    `;
                }
            }
        ], {
        order: [[1, 'asc']],
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
                url: "<?= base_url('admin/merchant_spv/search'); ?>",
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

    // Ensure tooltips/popovers work if any

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
});
</script>



