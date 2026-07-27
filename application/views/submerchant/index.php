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
 
 </div>

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
 <?php if ($merchant_level < 3): ?>
 <button type="button" class="btn-dt-action btn-dt-action-success add-sub-btn border-0 d-flex align-items-center shadow-sm" data-toggle="modal" data-target="#subMerchantModal" style="height: 38px; border-radius: 8px; padding: 0 16px; font-weight: 600; font-size: 13px;">
 <i class="fas fa-plus"></i> Add Sub Account
 </button>
 <?php else: ?>
 <span class="badge badge-sm badge-danger">Maximum of 4 Sub Accounts reached</span>
 <?php endif; ?>
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
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="subMerchantModal" tabindex="-1" role="dialog" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered" role="document">
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
 <!-- Right Form Area -->
 <div class="col-lg-12 p-4 bg-light mb-0">
 <div class="row g-4 mb-4">
 <div class="col-md-12 mb-4">
 <div class="card h-100 border-0 shadow-none p-4 rounded-4">
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
 <div class="col-md-12 mb-0">
 <label class="form-label small fw-bold text-muted">Callback Transfer</label>
 <input type="text" class="form-control border-1 bg-dark text-white" name="c_callbackTransfer" id="modal_c_callbackTransfer" placeholder="e.g. transfer/disbursement" style="border-color: rgba(255,255,255,0.1);">
 </div>
 <div class="col-md-12 mb-0">
 <label class="form-label small fw-bold text-muted">Account Status <span class="text-danger">*</span></label>
 <select class="form-control border-1 bg-dark text-white" name="c_status" id="modal_c_status" required style="border-color: rgba(255,255,255,0.1);">
 <option value="Active">Active</option>
 <option value="Pending">Pending</option>
 <option value="Blocked">Blocked</option>
 <option value="Freeze">Freeze</option>
 </select>
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
 var merchant_level = "<?= $merchant_level ?>";
 return `
 <div class="dropdown">
 <button class="btn btn-sm rounded-circle p-2 border-0 bg-transparent" type="button" data-toggle="dropdown" data-boundary="viewport" aria-expanded="false">
 <i class="fas fa-ellipsis-v"></i>
 </button>
 <ul class="dropdown-menu dropdown-menu-right shadow border-0 py-2">
 ${merchant_level < 3 ? `
 <li>
 <a class="dropdown-item" href="${baseUrl}merchant/sub-account/${data}">
 <i class="fas fa-users mr-2 text-success"></i>Sub Accounts
 </a>
 </li>
 <li><hr class="dropdown-divider"></li>
 ` : ''}
 <li>
 <button type="button" class="dropdown-item edit-sub-btn" 
 data-toggle="modal" data-target="#subMerchantModal"
 data-id="${data}"
 data-name="${row.c_name}"
 data-email="${row.c_email}"
 data-merchantid="${row.parent_merchant_id}"
 data-callbacktransfer="${row.c_callbackTransfer}"
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
 $('#modal_c_callbackTransfer').val($(this).data('callbacktransfer'));
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
