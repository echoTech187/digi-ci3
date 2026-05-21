<!-- Begin Page Content -->
<div>
    <!-- ── Page Header ── -->
        <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title"><?= $title; ?></h4>
            <p class="dt-page-subtitle">Manage OVO Top Up products and denominations.</p>
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
            <h6 class="drawer-title"><i class="fas fa-book mr-2"></i> Product Guide</h6>
            <button type="button" class="drawer-close" id="closeDrawerBtn">&times;</button>
        </div>
        <div class="drawer-body">
            <p class="drawer-desc">This product catalog allows administrators to manage top-up products, denominations, and pricing.</p>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-gamepad text-primary mr-2"></i> Product List</div>
                <p class="drawer-card-text">Audit PPOB/Game denomination catalog settings, including display caption, internal description, and pricing details.</p>
            </div>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-tag text-primary mr-2"></i> Pricing Setup</div>
                <p class="drawer-card-text">Configure product sale prices. Pricing updates affect merchant fee calculations and payment checkout rates instantly.</p>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm dt-card">
        <!-- ── Toolbar ── -->
        <form id="topup_ovo_form" onsubmit="return false;">
        <div class="dt-toolbar">
            <div class="dt-search-wrapper">
                <i class="fas fa-search dt-search-icon"></i>
                <input type="text" id="globalProductSearch" class="dt-search-input" placeholder="Search by name, category, or description...">
            </div>
            <div class="dt-toolbar-actions">
                 <button type="button" class="btn-dt-chip-action btn-dt-action-success border-0" data-toggle="modal" data-target="#exampleModal">
                    <i class="fas fa-plus"></i> <span class="d-none d-md-block">Add Product</span>
                </button>
            </div>
        </div>
        </form>

        <div class="card-body p-0">
            <!-- Add Product Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow-sm" style="border-radius:16px;overflow:hidden;">
                        <div class="modal-header modal-header-primary border-0 mh-premium">
                            <div class="d-flex align-items-center">
                                <div class="mh-icon-badge">
                                    <i class="fas fa-plus-circle"></i>
                                </div>
                                <div class="mh-title-wrap">
                                    <h6 class="mh-title" id="exampleModalLabel">ADD PRODUCT</h6>
                                    <small class="mh-subtitle">Create and register new data record</small>
                                </div>
                            </div>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form method="post" action="<?php echo base_url('admin/ServiceController/createProduk'); ?>
                            <div class="modal-body p-0 bg-light">
                                <div class="d-flex g-0 w-100 flex-column flex-lg-row">
                                    <div class="col-lg-4 p-4 d-flex flex-column mb-0" style="background:#202328;border-right:1px solid rgba(255,255,255,0.05);color:#fff;">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width:40px;height:40px;flex-shrink:0;"><i class="fas fa-wallet fa-lg"></i></div>
                                            <h6 class="fw-bold text-danger mb-0" style="font-size:15px;">OVO Guide</h6>
                                        </div>
                                        <p class="text-muted small mb-3" style="font-size:12px;line-height:1.5;">Register a new OVO e-wallet top-up denomination.</p>
                                        <div class="p-3 mb-3" style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:12px;">
                                            <h6 class="fw-bold text-white mb-1 d-flex align-items-center" style="font-size:12px;"><i class="fas fa-shield-alt text-danger mr-2"></i> OVO Compliance</h6>
                                            <p class="text-muted mb-0" style="font-size:11px;line-height:1.4;">OVO enforces KYC-verified limits. Ensure denomination amounts comply with OVO's maximum balance policy.</p>
                                        </div>
                                    </div>
                                    <div class="col-lg-8 p-4 bg-light mb-0">
                                        ">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Caption</label>
                                    <input type="text" class="form-control border-1" name="caption" required>
                                    <input type="hidden" name="channelgroup" value="ppob">
                                    <input type="hidden" name="channelgroup2" value="topup_ovo">
                                    <input type="hidden" name="name" value="topupovo">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Description</label>
                                    <textarea class="form-control border-1" name="description" rows="2" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Price</label>
                                    <input type="number" class="form-control border-1" name="price" required>
                                </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer px-4 py-3 border-0 bg-white justify-content-end">
                                <button type="button" class="btn-dt-cancel" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn-dt-apply px-4">
                                        <i class="fas fa-save mr-2"></i> Save Product
                                    </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-0">       
            <div class="table-responsive">       
                <table id="productTable" class="table dt-table mb-0" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>CAPTION</th>
                            <th>DESCRIPTION</th>
                            <th>PRICE</th>
                            <th class="text-center">ACTION</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <!-- Edit Product Modal -->
            <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
                
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
                        <div class="modal-header modal-header-primary border-0 mh-premium">
                            <div class="d-flex align-items-center">
                                <div class="mh-icon-badge">
                                    <i class="fas fa-edit"></i>
                                </div>
                                <div class="mh-title-wrap">
                                    <h6 class="mh-title" id="editProductModalLabel">Edit Product</h6>
                                    <small class="mh-subtitle">Modify and update existing product information</small>
                                </div>
                            </div>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form method="post" action="<?php echo base_url('admin/ServiceController/updateProduct'); ?>
                            <div class="modal-body p-0 bg-light">
                                <div class="d-flex g-0 w-100 flex-column flex-lg-row">
                                    <div class="col-lg-4 p-4 d-flex flex-column mb-0" style="background:#202328;border-right:1px solid rgba(255,255,255,0.05);color:#fff;">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center mr-3" style="width:40px;height:40px;flex-shrink:0;"><i class="fas fa-edit fa-lg"></i></div>
                                            <h6 class="fw-bold text-warning mb-0" style="font-size:15px;">Edit Guide</h6>
                                        </div>
                                        <p class="text-muted small mb-3" style="font-size:12px;line-height:1.5;">Update OVO top-up details. Price changes apply immediately to all merchant catalogues.</p>
                                        <div class="p-3 mb-3" style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:12px;">
                                            <h6 class="fw-bold text-white mb-1 d-flex align-items-center" style="font-size:12px;"><i class="fas fa-exclamation-circle text-warning mr-2"></i> Price Impact</h6>
                                            <p class="text-muted mb-0" style="font-size:11px;line-height:1.4;">Price updates affect active merchant fee calculations. Review fee settings after any price changes.</p>
                                        </div>
                                    </div>
                                    <div class="col-lg-8 p-4 bg-light mb-0">
                                        ">
                                <input type="hidden" name="view_name" value="topupovo">
                                <input type="hidden" id="edit_product_id" name="id">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Product Caption</label>
                                    <input type="text" class="form-control border-1" id="edit_caption" name="caption" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Description</label>
                                    <textarea class="form-control border-1" id="edit_description" name="description" rows="3" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Price / Fee (IDR)</label>
                                    <input type="number" class="form-control border-1" id="edit_price" name="price" required>
                                </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer px-4 py-3 border-0 bg-white justify-content-end">
                                <button type="button" class="btn-dt-cancel" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn-dt-apply px-4">
                                        <i class="fas fa-save mr-2"></i> Save Changes
                                    </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/server-datatables.js') ?>"></script>
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
    window.productTable = initServerDataTable('#productTable', "<?= base_url('product/ewallet/ovo') ?>", [
        { data: 'no' },
        { data: 'c_caption' },
        { data: 'c_description' },
        { 
            data: 'c_fee',
            className: 'font-weight-bold text-dark',
            render: function(data) {
                return 'Rp ' + number_format(data, 0, ',', '.');
            }
        },
        { 
            data: null, 
            orderable: false, 
            searchable: false, 
            className: 'text-center',
            render: function(data, type, row) {
                return `
                    <div class="dropdown">
                        <button class="btn btn-sm rounded-circle p-2 border-0 bg-transparent" type="button" data-toggle="dropdown" data-boundary="viewport" aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right shadow border-0 py-2">
                            <li>
                                <button type="button" class="dropdown-item" 
                                    onclick="editProduct('${(row.id || '').replace(/'/g, "\\'")}', '${(row.c_caption || '').replace(/'/g, "\\'")}', '${(row.c_description || '').replace(/'/g, "\\'")}', '${row.c_fee}')">
                                    <i class="fas fa-edit text-primary mr-2"></i> Edit Product
                                </button>
                            </li>
                        </ul>
                    </div>
                `;
            }
        }
    ], {
        "ajax": {
            "url": "<?= base_url('product/ewallet/ovo') ?>",
            "type": "POST",
            "data": function (d) {
                var csrfName = $('meta[name="csrf-token-name"]').attr('content');
                var csrfHash = $('meta[name="csrf-token-hash"]').attr('content');
                if (csrfName && csrfHash) {
                    d[csrfName] = csrfHash;
                }
            }
        }
    });

    $('#globalProductSearch').on('keyup', function() {
        window.productTable.search(this.value).draw();
    });
});

function editProduct(id, caption, description, fee, provider) {
    $('#edit_product_id').val(id);
    $('#edit_caption').val(caption);
    $('#edit_description').val(description);
    
    // Clean fee from formatting
    var cleanFee = fee.toString().replace(/[^0-9]/g, '');
    $('#edit_price').val(cleanFee);
    
    $('#editProductModal').modal('show');
}
</script>
