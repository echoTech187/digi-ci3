<?php
$id = $this->uri->segment(3);
?>

<div>
    <!-- ── Page Header ── -->
    <div class="dt-page-header d-flex align-items-center justify-content-between">
        <div>
            <h4 class="dt-page-title">Sub Accounts Management</h4>
            <p class="dt-page-subtitle">Managing sub accounts for <strong><?= $merchant[0]->c_name ?></strong></p>
        </div>
        <button type="button" class="btn-dt-action btn-dt-action-primary border-0 d-flex align-items-center shadow-sm" id="toggleGuideBtn" >
            <i class="fas fa-book-open mr-2"></i> <span class="d-none d-md-block">Instructions Guide</span>
        </button>
    </div>

    <!-- ── Toggleable Page Instructional Drawer ── -->
    <div class="drawer-overlay" id="instructionOverlay"></div>
    <div class="drawer-right" id="instructionDrawer">
        <div class="drawer-header">
            <h6 class="drawer-title"><i class="fas fa-book mr-2"></i> Sub Accounts Guide</h6>
            <button type="button" class="drawer-close" id="closeDrawerBtn">&times;</button>
        </div>
        <div class="drawer-body">
            <p class="drawer-desc">This dashboard allows you to manage the sub-merchant accounts hierarchy under <strong><?= $merchant[0]->c_name ?></strong>. You can register new branch accounts, modify configurations, or drill down into further sub-levels.</p>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-sitemap text-primary mr-2"></i> Hierarchy Management</div>
                <p class="drawer-card-text">Create multi-level sub-accounts to represent branches or business units. Each sub-account operates under the parent's permission ceiling.</p>
            </div>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-plus-circle text-primary mr-2"></i> Add Sub Account</div>
                <p class="drawer-card-text">Click 'Add Sub Account' to register a new branch with dedicated credentials, GVConnect keys, and Static Virtual Accounts.</p>
            </div>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-edit text-primary mr-2"></i> Edit Details</div>
                <p class="drawer-card-text">Use the Action menu (⋮) to update an existing sub-account's profile, API keys, or Virtual Account numbers instantly.</p>
            </div>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-exchange-alt text-primary mr-2"></i> Mutation Logs</div>
                <p class="drawer-card-text">Directly access financial transaction and mutation records specific to any sub-account from the action menu.</p>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    <!-- Alerts Standardized to Swal2 Premium -->
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

    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">
        <!-- ── Toolbar ── -->
        <div class="dt-toolbar">
            <!-- LEFT: Global Search -->
            <div class="dt-search-wrapper">
                <i class="fas fa-search dt-search-icon"></i>
                <input type="text" id="dt-search" class="dt-search-input" placeholder="Search by name, ID, or email...">
            </div>
            
            <!-- RIGHT: Actions -->
            <div class="dt-toolbar-filters">
                <button type="button" class="btn-dt-action btn-dt-action-success add-sub-btn border-0 d-flex align-items-center shadow-sm" data-toggle="modal" data-target="#subMerchantModal" style="height: 38px; border-radius: 8px; padding: 0 16px; font-weight: 600; font-size: 13px;">
                    <i class="fas fa-plus mr-2"></i> Add Sub Account
                </button>
            </div>
        </div>

        <!-- ── Table ── -->
        <div class="table-responsive">
            <table id="submerchantTable" class="table dt-table mb-0" style="width:100%">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Submerchant Name</th>
                        <th>Email Address</th>
                        <th>Status</th>
                        <th width="120" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <!-- Pagination/Info handled via JS container in footer -->
         <div class="dt-footer" id="dt-footer-container"></div>
    </div>
</div>

<!-- ── Sub Merchant Modal (Add & Edit) ── -->
<div class="modal fade" id="subMerchantModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header modal-header-primary border-0 mh-premium">
                <div class="d-flex align-items-center">
                    <div class="mh-icon-badge">
                        <i class="fas fa-store-alt"></i>
                    </div>
                    <div class="mh-title-wrap">
                        <h6 class="mh-title" id="subMerchantModalTitle">Add Sub Account</h6>
                        <small class="mh-subtitle" id="subMerchantModalSubtitle">Register a new sub account under this hierarchy</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="subMerchantForm" action="<?= base_url('merchant/sub-account/register') ?>" method="POST" class="w-100 mb-0">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <input type="hidden" name="ref_merchantId" id="ref_merchantId" value="<?= $id ?>">
                
                <div class="modal-body p-0 bg-light">
                    <div class="d-flex g-0 w-100 flex-column flex-lg-row">
                        <!-- Left Information Sidebar -->
                        <div class="col-lg-4 p-4 d-flex flex-column justify-content-between mb-0" style="background:#202328;color:#fff;border-right:1px solid rgba(255,255,255,0.05);">
                            <div>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3 shadow-sm" style="width: 40px; height: 40px; background-color: rgba(0, 123, 255, 0.2) !important;">
                                        <i class="fas fa-info-circle fa-lg text-primary"></i>
                                    </div>
                                    <h6 class="fw-bold text-primary mb-0" style="font-size: 15px;">Configuration Guide</h6>
                                </div>
                                <p class="text-muted small mb-4" style="font-size: 12px; line-height: 1.5;">Follow the instructions below to configure sub-account details, GVConnect credentials, and Virtual Accounts accurately.</p>
                                
                                <div class="d-flex flex-column gap-3">
                                    <div class="bg-white p-3 rounded-4 shadow-sm border-0 mb-3" style="background-color: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important;">
                                        <h6 class="fw-bold text-white mb-1 d-flex align-items-center" style="font-size: 12.5px;"><i class="fas fa-sitemap text-warning mr-2"></i> 1. Hierarchy Integrity</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.5;">Sub-accounts operate as branch entities under the parent merchant's clearance and financial settlement umbrella.</p>
                                    </div>
                                    <div class="bg-white p-3 rounded-4 shadow-sm border-0 mb-3" style="background-color: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important;">
                                        <h6 class="fw-bold text-white mb-1 d-flex align-items-center" style="font-size: 12.5px;"><i class="fas fa-plug text-info mr-2"></i> 2. GVConnect Gateway</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.5;">Ensure Business ID and Keys match the gateway configuration for seamless static VA and QRIS generation.</p>
                                    </div>
                                    <div class="bg-white p-3 rounded-4 shadow-sm border-0 mb-3" style="background-color: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important;">
                                        <h6 class="fw-bold text-white mb-1 d-flex align-items-center" style="font-size: 12.5px;"><i class="fas fa-user-shield text-success mr-2"></i> 3. Status Control</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px; line-height: 1.5;">Toggling account status instantly enables or revokes access to the merchant portal and API endpoints.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white p-3 rounded-4 shadow-sm border-0 mt-3 d-flex align-items-center" style="background-color: rgba(255, 255, 255, 0.03) !important; border: 1px solid rgba(255, 255, 255, 0.08) !important;">
                                <i class="fas fa-lightbulb text-warning fa-2x mr-3"></i>
                                <span class="text-muted" style="font-size: 11px; line-height: 1.4;">Need help? Contact system administrator for advanced routing configurations.</span>
                            </div>
                        </div>

                        <!-- Right Form Area -->
                        <div class="col-lg-8 p-4 bg-light mb-0">
                            <div class="row g-4 mb-4">
                                <div class="col-md-12 mb-4">
                                    <div class="card h-100 border-0 shadow-none p-4 rounded-4" style="">
                                        <h6 class="text-primary fw-bold mb-3 d-flex align-items-center">
                                            <i class="fas fa-info-circle mr-2"></i> BASIC INFORMATION
                                        </h6>
                                        <div class="row g-3">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small fw-bold text-muted">Sub Account Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control border-1 bg-dark text-white" name="c_name" id="modal_c_name" required placeholder="e.g. Branch Store 1" style="border-color: rgba(255,255,255,0.1);">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small fw-bold text-muted">Email Address <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control border-1 bg-dark text-white" name="c_email" id="modal_c_email" required placeholder="e.g. branch1@store.com" style="border-color: rgba(255,255,255,0.1);">
                                            </div>
                                            <div class="col-md-6 mb-0">
                                                <label class="form-label small fw-bold text-muted">Account Status <span class="text-danger">*</span></label>
                                                <select class="form-control border-1 bg-dark text-white" name="c_status" id="modal_c_status" required style="border-color: rgba(255,255,255,0.1);">
                                                    <option value="Active">Active</option>
                                                    <option value="Inactive">Inactive</option>
                                                    <option value="Blocked">Blocked</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="card h-100 border-0 shadow-none p-4 rounded-4" style="">
                                        <h6 class="text-primary fw-bold mb-3 d-flex align-items-center">
                                            <i class="fas fa-plug mr-2"></i> GVCONNECT &amp; STATIC VA
                                        </h6>
                                        <div class="row g-3">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small fw-bold text-muted">GVConnect Business ID <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control border-1 bg-dark text-white" name="c_gvconnectBusinessId" id="modal_c_gvconnectBusinessId" required placeholder="Business ID" style="border-color: rgba(255,255,255,0.1);">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small fw-bold text-muted">GVConnect Business Name</label>
                                                <input type="text" class="form-control border-1 bg-dark text-white" name="c_gvconnectBusinessName" id="modal_c_gvconnectBusinessName" placeholder="Business Name" style="border-color: rgba(255,255,255,0.1);">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label class="form-label small fw-bold text-muted">GVConnect Key</label>
                                                <input type="text" class="form-control border-1 bg-dark text-white" name="c_gvconnectGVConnectKey" id="modal_c_gvconnectGVConnectKey" placeholder="API Key / Secret" style="border-color: rgba(255,255,255,0.1);">
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label class="form-label small fw-bold text-muted">Static QRIS Raw</label>
                                                <textarea class="form-control border-1 bg-dark text-white" name="c_gvconnectStaticQrisRaw" id="modal_c_gvconnectStaticQrisRaw" rows="2" placeholder="QRIS Raw String" style="border-color: rgba(255,255,255,0.1);"></textarea>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small fw-bold text-muted">Static VA BNI</label>
                                                <input type="text" class="form-control border-1 bg-dark text-white" name="c_gvconnectStaticVaBni" id="modal_c_gvconnectStaticVaBni" placeholder="BNI Virtual Account" style="border-color: rgba(255,255,255,0.1);">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small fw-bold text-muted">Static VA BCA</label>
                                                <input type="text" class="form-control border-1 bg-dark text-white" name="c_gvconnectStaticVaBca" id="modal_c_gvconnectStaticVaBca" placeholder="BCA Virtual Account" style="border-color: rgba(255,255,255,0.1);">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label small fw-bold text-muted">Static VA CIMB</label>
                                                <input type="text" class="form-control border-1 bg-dark text-white" name="c_gvconnectStaticVaCimb" id="modal_c_gvconnectStaticVaCimb" placeholder="CIMB Virtual Account" style="border-color: rgba(255,255,255,0.1);">
                                            </div>
                                            <div class="col-md-6 mb-0">
                                                <label class="form-label small fw-bold text-muted">Static VA PERMATA</label>
                                                <input type="text" class="form-control border-1 bg-dark text-white" name="c_gvconnectStaticVaPermata" id="modal_c_gvconnectStaticVaPermata" placeholder="Permata Virtual Account" style="border-color: rgba(255,255,255,0.1);">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 p-3 justify-content-end px-4" style=" border-top: 1px solid rgba(255,255,255,0.05) !important;">
                    <button type="button" class="btn-dt-cancel" data-dismiss="modal">CANCEL</button>
                    <button type="submit" class="btn-dt-apply px-4 shadow-sm">
                        <i class="fas fa-save mr-2"></i> <span id="subMerchantModalBtnText">SAVE ACCOUNT</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize Server-side DataTable
    var table = initServerDataTable("#submerchantTable", "<?= base_url('merchant/sub-account/'.$id) ?>", [
        { data: 'no', orderable: false },
        { 
            data: 'c_name', 
            className: 'font-weight-bold text-gray-800',
            render: function(data, type, row) {
                return '<div>' + data + '</div><small class="text-muted">ID: ' + row.id + '</small>';
            }
        },
        { data: 'c_email' },
        { 
            data: 'c_status', 
            className: 'text-center',
            render: function(data, type, row) {
                var status_class = (data == 'Active') ? 'success' : 'secondary';
                return '<span class="badge badge-' + status_class + '">' + data + '</span>';
            }
        },
        { 
            data: 'id', 
            className: 'text-center', 
            orderable: false,
            render: function(data, type, row) {
                var baseUrl = "<?= base_url() ?>";
                return `
                    <div class="dropdown">
                        <button class="btn btn-sm rounded-circle p-2 border-0 bg-transparent" type="button" data-toggle="dropdown" data-boundary="viewport" aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right shadow border-0 py-2">
                            <li>
                                <a class="dropdown-item" href="${baseUrl}merchant/sub-account/${data}">
                                    <i class="fas fa-users mr-2 text-success"></i>Sub Accounts
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <button type="button" class="dropdown-item edit-sub-btn" 
                                    data-toggle="modal" data-target="#subMerchantModal"
                                    data-id="${data}"
                                    data-name="${row.c_name}"
                                    data-email="${row.c_email}"
                                    data-merchantid="${row.parent_merchant_id}"
                                    data-businessname="${row.c_gvconnectBusinessName}"
                                    data-businessid="${row.c_gvconnectBusinessId}"
                                    data-key="${row.c_gvconnectGVConnectKey}"
                                    data-qris="${row.c_gvconnectStaticQrisRaw}"
                                    data-bni="${row.c_gvconnectStaticVaBni}"
                                    data-bca="${row.c_gvconnectStaticVaBca}"
                                    data-cimb="${row.c_gvconnectStaticVaCimb}"
                                    data-permata="${row.c_gvconnectStaticVaPermata}"
                                    data-status="${row.c_status}">
                                    <i class="fas fa-edit mr-2 text-info"></i>Edit Details
                                </button>
                            </li>
                            <li>
                                <a class="dropdown-item" href="${baseUrl}finance/mutation/${data}">
                                    <i class="fas fa-exchange-alt mr-2 text-warning"></i>Mutations
                                </a>
                            </li>
                        </ul>
                    </div>
                `;
            }
        }
    ]);

    // Apply Global Search filter if search_val exists in URL
    const urlParams = new URLSearchParams(window.location.search);
    const searchVal = urlParams.get('search_val');
    if (searchVal) {
        setTimeout(() => {
            table.search(searchVal).draw();
            $('#dt-search').val(searchVal);
        }, 500);
    }

    // Global search with Debounce
    $('#dt-search').on('input', debounce(function() {
        table.search(this.value).draw();
    }, 400));

    // Handle Add Button Click
    $(document).on('click', '.add-sub-btn', function() {
        $('#subMerchantForm').attr('action', "<?= base_url('merchant/sub-account/register') ?>");
        $('#subMerchantModalTitle').text("Add Sub Account");
        $('#subMerchantModalSubtitle').text("Register a new sub account under this hierarchy");
        $('#subMerchantModalBtnText').text("REGISTER ACCOUNT");
        
        // Reset form fields
        $('#subMerchantForm')[0].reset();
        $('#ref_merchantId').val("<?= $id ?>");
        $('#modal_c_status').val("Active");
    });

    // Handle Edit Button Click
    $(document).on('click', '.edit-sub-btn', function() {
        const id = $(this).data('id');
        $('#subMerchantForm').attr('action', "<?= base_url('merchant/sub-account/edit/') ?>" + id);
        $('#subMerchantModalTitle').text("Edit Sub Account Details");
        $('#subMerchantModalSubtitle').text("Update configuration for: " + $(this).data('name'));
        $('#subMerchantModalBtnText').text("SAVE CHANGES");
        
        // Populate fields
        $('#ref_merchantId').val("<?= $id ?>");
        $('#modal_c_name').val($(this).data('name'));
        $('#modal_c_email').val($(this).data('email'));
        $('#modal_c_status').val($(this).data('status') || 'Active');
        $('#modal_c_gvconnectBusinessId').val($(this).data('businessid'));
        $('#modal_c_gvconnectBusinessName').val($(this).data('businessname'));
        $('#modal_c_gvconnectGVConnectKey').val($(this).data('key'));
        $('#modal_c_gvconnectStaticQrisRaw').val($(this).data('qris'));
        $('#modal_c_gvconnectStaticVaBni').val($(this).data('bni'));
        $('#modal_c_gvconnectStaticVaBca').val($(this).data('bca'));
        $('#modal_c_gvconnectStaticVaCimb').val($(this).data('cimb'));
        $('#modal_c_gvconnectStaticVaPermata').val($(this).data('permata'));
    });

    // Handle Instructional Drawer Toggle
    $('#toggleGuideBtn').on('click', function() {
        $('#instructionDrawer').addClass('open');
        $('#instructionOverlay').addClass('open');
    });

    $('#closeDrawerBtn, #instructionOverlay').on('click', function() {
        $('#instructionDrawer').removeClass('open');
        $('#instructionOverlay').removeClass('open');
    });
});
</script>
