<!-- Begin Page Content -->
<div>
    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title"><?= $title; ?></h4>
            <p class="dt-page-subtitle">Manage Regular Pulsa products and denominations by provider.</p>
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
        <form id="pulsa_reguler_form" onsubmit="return false;">
            <div class="dt-toolbar">
                <div class="dt-search-wrapper">
                    <i class="fas fa-search dt-search-icon"></i>
                    <input type="text" id="globalProductSearch" class="dt-search-input" placeholder="Search by name, category, or description...">
                </div>
                <div class="dt-toolbar-filters">
                    <div class="dt-filter-group">
                        <label class="dt-filter-label"><i class="fas fa-broadcast-tower mr-1 mr-2"></i> Provider</label>
                        <div class="dt-filter-chip">
                            <select id="provider" name="provider" class="dt-chip-select">
                                <option value="">All Providers</option>
                                <option value="pulsa_xl" <?= $this->session->userdata('provider') == 'pulsa_xl' ? 'selected' : ''; ?>>XL Axiata</option>
                                <option value="pulsa_axis" <?= $this->session->userdata('provider') == 'pulsa_axis' ? 'selected' : ''; ?>>Axis</option>
                                <option value="pulsa_telkomsel" <?= $this->session->userdata('provider') == 'pulsa_telkomsel' ? 'selected' : ''; ?>>Telkomsel</option>
                                <option value="pulsa_tri" <?= $this->session->userdata('provider') == 'pulsa_tri' ? 'selected' : ''; ?>>Tri (3)</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" class="btn-dt-chip-action btn-dt-action-success border-0" data-toggle="modal" data-target="#exampleModal">
                        <i class="fas fa-plus"></i> <span class="d-none d-md-block">Add Product</span>
                    </button>
                </div>
            </div>
        </form>

        <div class="card-body p-0">
            <div class="p-0">       
                <div class="table-responsive">       
                    <table id="productTable" class="table dt-table mb-0" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>NO</th>
                                <th>PROVIDER</th>
                                <th>CAPTION</th>
                                <th>DESCRIPTION</th>
                                <th>PRICE</th>
                                <th class="text-center">ACTION</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Add Product Modal -->
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header modal-header-primary border-0 mh-premium">
                <div class="d-flex align-items-center">
                    <div class="mh-icon-badge"><i class="fas fa-plus-circle"></i></div>
                    <div class="mh-title-wrap">
                        <h6 class="mh-title" id="exampleModalLabel">ADD PRODUCT</h6>
                        <small class="mh-subtitle">Create and register new data record</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="addProductForm">
                <div class="modal-body p-0 bg-light">
                    <div class="d-flex g-0 w-100 flex-column flex-lg-row">
                        <div class="col-lg-4 p-4 d-flex flex-column mb-0" >
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width:40px;height:40px;flex-shrink:0;"><i class="fas fa-sim-card fa-lg"></i></div>
                                <h6 class="fw-bold text-primary mb-0" style="font-size:15px;">Product Guide</h6>
                            </div>
                            <p class="text-muted small mb-3" style="font-size:12px;line-height:1.5;">Register new pulsa reguler denomination for a specific provider.</p>
                            <div class="p-3 mb-3" style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:12px;">
                                <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size:12px;"><i class="fas fa-broadcast-tower text-warning mr-2"></i> 1. Provider</h6>
                                <p class="text-muted mb-0" style="font-size:11px;line-height:1.4;">Select the network operator this denomination belongs to (XL, Axis, Telkomsel, Tri).</p>
                            </div>
                            <div class="p-3" style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:12px;">
                                <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size:12px;"><i class="fas fa-tag text-info mr-2"></i> 2. Caption & Price</h6>
                                <p class="text-muted mb-0" style="font-size:11px;line-height:1.4;">Caption is the customer-facing label. Price is the base sell rate in IDR.</p>
                            </div>
                        </div>
                        <div class="col-lg-8 p-4 bg-light mb-0">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Provider</label>
                                <select name="channelgroup2" class="form-control border-1" required>
                                    <option value="">Select Provider</option>
                                    <option value="pulsa_xl">XL</option>
                                    <option value="pulsa_axis">Axis</option>
                                    <option value="pulsa_telkomsel">Telkomsel</option>
                                    <option value="pulsa_tri">Tri</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Caption</label>
                                <input type="text" class="form-control border-1" name="caption" required>
                                <input type="hidden" name="channelgroup" value="ppob">
                                <input type="hidden" name="name" value="pulsa_reguler">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Description</label>
                                <textarea class="form-control border-1" name="description" rows="2"></textarea>
                            </div>
                            <div class="mb-0">
                                <label class="form-label small fw-bold text-muted">Price</label>
                                <input type="text" class="input-rupiah form-control border-1" name="price" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3 border-0 bg-white justify-content-end">
                    <button type="button" class="btn-dt-cancel mr-2" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn-dt-apply px-4"><i class="fas fa-save mr-2"></i> Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header modal-header-primary border-0 mh-premium">
                <div class="d-flex align-items-center">
                    <div class="mh-icon-badge"><i class="fas fa-edit"></i></div>
                    <div class="mh-title-wrap">
                        <h6 class="mh-title" id="editProductModalLabel">Edit Product</h6>
                        <small class="mh-subtitle">Modify and update existing product information</small>
                    </div>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="editProductForm">
                <input type="hidden" name="view_name" value="pulsa_reguler">
                <input type="hidden" id="edit_product_id" name="id">
                <div class="modal-body p-0 bg-light">
                    <div class="d-flex g-0 w-100 flex-column flex-lg-row">
                        <div class="col-lg-4 p-4 d-flex flex-column mb-0" >
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center mr-3" style="width:40px;height:40px;flex-shrink:0;"><i class="fas fa-edit fa-lg"></i></div>
                                <h6 class="fw-bold text-warning mb-0" style="font-size:15px;">Edit Guide</h6>
                            </div>
                            <p class="text-muted small mb-3" style="font-size:12px;line-height:1.5;">Update denomination details. Price changes apply immediately to all merchant catalogues.</p>
                            <div class="p-3" style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:12px;">
                                <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size:12px;"><i class="fas fa-exclamation-circle text-warning mr-2"></i> Price Impact</h6>
                                <p class="text-muted mb-0" style="font-size:11px;line-height:1.4;">Price updates affect active merchant fee calculations. Review fee settings after any price changes.</p>
                            </div>
                        </div>
                        <div class="col-lg-8 p-4 bg-light mb-0">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Provider</label>
                                <select id="edit_channelgroup2" name="channelgroup2" class="form-control border-1" required>
                                    <option value="">Select Provider</option>
                                    <option value="pulsa_xl">XL</option>
                                    <option value="pulsa_axis">Axis</option>
                                    <option value="pulsa_telkomsel">Telkomsel</option>
                                    <option value="pulsa_tri">Tri</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Product Caption</label>
                                <input type="text" class="form-control border-1" id="edit_caption" name="caption" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">Description</label>
                                <textarea class="form-control border-1" id="edit_description" name="description" rows="2"></textarea>
                            </div>
                            <div class="mb-0">
                                <label class="form-label small fw-bold text-muted">Price / Fee (IDR)</label>
                                <input type="text" class="input-rupiah form-control border-1" id="edit_price" name="price" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-4 py-3 border-0 bg-white justify-content-end">
                    <button type="button" class="btn-dt-cancel mr-2" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-dt-apply px-4"><i class="fas fa-save mr-2"></i> Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
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
    window.productTable = initServerDataTable('#productTable', "<?= base_url('product/pulsa-reguler') ?>", [
        { data: 'no' },
        { 
            data: 'c_channelGroup2',
            render: function(data, type, row) {
                if(!data) return '-';
                // Remove 'pulsa_' prefix and uppercase
                return '<span class="badge badge-info px-2 py-1">' + data.replace('pulsa_', '').toUpperCase() + '</span>';
            }
        },
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
                                    onclick="editProduct('${(row.id || '').replace(/'/g, "\\'")}', '${(row.c_caption || '').replace(/'/g, "\\'")}', '${(row.c_description || '').replace(/'/g, "\\'")}', '${row.c_fee}', '${(row.c_channelGroup2 || '').replace(/'/g, "\\'")}')">
                                    <i class="fas fa-edit text-primary mr-2"></i> Edit Product
                                </button>
                            </li>
                            <li>
                                <div class="dropdown-divider my-1"></div>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item text-danger" onclick="deleteProduct('${(row.id || '').replace(/'/g, "\\'")}')">
                                    <i class="fas fa-trash-alt mr-2"></i> Delete Product
                                </button>
                            </li>
                        </ul>
                    </div>
                `;
            }
        }
    ], {
        "ajax": {
            "url": "<?= base_url('product/pulsa-reguler') ?>",
            "type": "POST",
            "data": function (d) {
                var csrfName = $('meta[name="csrf-token-name"]').attr('content');
                var csrfHash = $('meta[name="csrf-token-hash"]').attr('content');
                if (csrfName && csrfHash) {
                    d[csrfName] = csrfHash;
                }
                d.provider = $('#provider').val();
            }
        }
    });

    $('#provider').on('change', function() {
        window.productTable.ajax.reload();
    });

    $('#globalProductSearch').on('keyup', function() {
        window.productTable.search(this.value).draw();
    });
});

function editProduct(id, caption, description, fee, provider) {
    $('#edit_product_id').val(id);
    $('#edit_caption').val(caption);
    $('#edit_description').val(description);
    $('#edit_channelgroup2').val(provider).trigger('change');
    
    // Parse the fee as float to remove trailing decimal zeroes, then convert to int
    var cleanFee = Math.floor(parseFloat(fee));
    $('#edit_price').val(cleanFee);
    
    $('#editProductModal').modal('show');
}

function deleteProduct(id) {
    Swal.fire({
        title: 'Delete Product?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            var csrfName = $('meta[name="csrf-token-name"]').attr('content');
            var csrfHash = $('meta[name="csrf-token-hash"]').attr('content');
            var data = {};
            if (csrfName && csrfHash) {
                data[csrfName] = csrfHash;
            }
            
            $.ajax({
                url: "<?= base_url('product/delete/') ?>" + id,
                type: 'POST',
                dataType: 'json',
                data: data,
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire('Deleted!', res.message, 'success');
                        window.productTable.ajax.reload(null, false);
                    } else {
                        Swal.fire('Error!', res.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'An unexpected error occurred.', 'error');
                }
            });
        }
    });
}

// Manual AJAX for Add Form
$('#addProductForm').on('submit', function(e) {
    e.preventDefault();
    var form = $(this);
    var btn = form.find('button[type="submit"]');
    var originalBtnText = btn.html();
    
    btn.html('<i class="fas fa-spinner fa-spin mr-2"></i> Processing...').prop('disabled', true);
    
    $.ajax({
        url: "<?= base_url('product/create') ?>",
        type: 'POST',
        data: form.serialize(),
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                $('.modal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: res.message
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.trigger('reset');
                        window.productTable.ajax.reload(null, false);
                    }
                });
            } else {
                Swal.fire('Error!', res.message, 'error');
            }
        },
        error: function() {
            Swal.fire('Error!', 'An unexpected error occurred.', 'error');
        },
        complete: function() {
            btn.html(originalBtnText).prop('disabled', false);
        }
    });
});

// Manual AJAX for Edit Form
$('#editProductForm').on('submit', function(e) {
    e.preventDefault();
    var form = $(this);
    var btn = form.find('button[type="submit"]');
    var originalBtnText = btn.html();
    
    btn.html('<i class="fas fa-spinner fa-spin mr-2"></i> Processing...').prop('disabled', true);
    
    $.ajax({
        url: "<?= base_url('product/update') ?>",
        type: 'POST',
        data: form.serialize(),
        dataType: 'json',
        success: function(res) {
            if (res.status === 'success') {
                $('.modal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: res.message
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.productTable.ajax.reload(null, false);
                    }
                });
            } else {
                Swal.fire('Error!', res.message, 'error');
            }
        },
        error: function() {
            Swal.fire('Error!', 'An unexpected error occurred.', 'error');
        },
        complete: function() {
            btn.html(originalBtnText).prop('disabled', false);
        }
    });
});
</script>
