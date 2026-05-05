<!-- Begin Page Content -->
<div>
    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title"><?= $title; ?></h4>
            <p class="dt-page-subtitle">Manage Google Play Vouchers and denominations.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm dt-card">
        <!-- ── Toolbar ── -->
        <form id="googleplay_form" onsubmit="return false;">
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
                <div class="modal-dialog">
                    <div class="modal-content border-0 shadow-sm">
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
                        <div class="modal-body p-4 bg-light">
                            <form method="post" action="<?php echo base_url('admin/ServiceController/createProduk'); ?>">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Caption</label>
                                    <input type="text" class="form-control border-1" name="caption" required>
                                    <input type="hidden" name="channelgroup" value="ppob">
                                    <input type="hidden" name="channelgroup2" value="google_play">
                                    <input type="hidden" name="name" value="googleplay">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Description</label>
                                    <textarea class="form-control border-1" name="description" rows="2" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">Price</label>
                                    <input type="number" class="form-control border-1" name="price" required>
                                </div>
                                <div class="modal-footer border-0 px-0 pb-0 mt-4">
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
                <div class="modal-dialog">
                    <div class="modal-content border-0 shadow-lg">
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
                        <div class="modal-body p-4 bg-light">
                            <form method="post" action="<?php echo base_url('admin/ServiceController/updateProduct'); ?>">
                                <input type="hidden" name="view_name" value="googleplay">
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
                                <div class="modal-footer border-0 px-0 pb-0 mt-4">
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
</div>

<script src="<?= base_url('assets/js/server-datatables.js') ?>"></script>
<script>
$(document).ready(function() {
    window.productTable = initServerDataTable('#productTable', "<?= base_url('admin/googleplay') ?>", [
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
                        <button class="btn btn-sm text-muted rounded-circle p-2 border-0 bg-transparent" type="button" data-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-2">
                            <li>
                                <button type="button" class="dropdown-item py-2" 
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
            "url": "<?= base_url('admin/googleplay') ?>",
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
