<!-- Begin Page Content -->
<div class="container-fluid pb-4">
    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title"><?= $title; ?></h4>
            <p class="dt-page-subtitle">Manage Mobile Legends: Bang Bang diamond products and pricing.</p>
        </div>
    </div>

    <!-- ── KPI Summary Cards ── -->
    <div class="dt-summary-row mb-4">
        <div class="dt-summary-card dt-summary-blue">
            <div class="dt-summary-body">
                <div class="dt-summary-label">TOTAL PRODUCTS</div>
                <div class="dt-summary-value" id="kpi-count">0</div>
                <div class="dt-summary-sub"><i class="fas fa-tags mr-1"></i>Diamond SKUs</div>
            </div>
            <div class="dt-summary-icon dt-icon-blue">
                <i class="fas fa-gamepad"></i>
            </div>
        </div>

        <div class="dt-summary-card dt-summary-purple">
            <div class="dt-summary-body">
                <div class="dt-summary-label">CATEGORY</div>
                <div class="dt-summary-value">GAMES</div>
                <div class="dt-summary-sub"><i class="fas fa-vr-cardboard mr-1"></i>Entertainment</div>
            </div>
            <div class="dt-summary-icon dt-icon-purple">
                <i class="fas fa-trophy"></i>
            </div>
        </div>

        <div class="dt-summary-card dt-summary-yellow">
            <div class="dt-summary-body">
                <div class="dt-summary-label">CURRENCY</div>
                <div class="dt-summary-value">IDR</div>
                <div class="dt-summary-sub"><i class="fas fa-money-bill-wave mr-1"></i>Indonesian Rupiah</div>
            </div>
            <div class="dt-summary-icon dt-icon-yellow">
                <i class="fas fa-coins"></i>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm dt-card">
        <!-- ── Toolbar ── -->
        <div class="dt-toolbar">
            <div class="dt-search-wrapper">
                <i class="fas fa-search dt-search-icon"></i>
                <input type="text" id="globalProductSearch" class="dt-search-input" placeholder="Search by name, category, or description...">
            </div>
            <div class="dt-toolbar-filters">
                <!-- Filter & Reset Actions -->
                <button type="button" class="btn-dt-chip-action btn-dt-action-success border-0" data-toggle="modal" data-target="#exampleModal">
                    <i class="fas fa-plus"></i> <span class="d-none d-md-block">Top Up Mobile Legend</span>
                </button>
            </div>
        </div>
        <div class="card-body p-0">

             <!-- Alert Messages -->
             <?php if ($this->session->flashdata('message')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $this->session->flashdata('message'); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $this->session->flashdata('error'); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            
            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content border-0 shadow-sm">
                        <div class="modal-header modal-header-primary border-0 py-3">
                            <h5 class="modal-title font-weight-bold text-white" id="exampleModalLabel">
                                <i class="fas fa-plus-circle mr-2"></i>Top Up Diamond Mobile Legend
                            </h5>
                            <button type="button" class="close text-white outline-none" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <?php echo validation_errors(); ?>
                            <form method="post" action="<?php echo base_url('admin/createProduk'); ?>">
                                <div class="form-group row">
                                    <label for="caption" class="col-sm-3 col-form-label">Caption</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="caption" name ="caption" >
                                        <input type="hidden" class="form-control" id="caption" name ="channelgroup" value= "ppob" >
                                        <input type="hidden" class="form-control" id="caption" name ="channelgroup2" value= "diamond_mlbb">
                                        <input type="hidden" class="form-control" id="caption" name ="name" value= "mobilelegend">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="description" class="col-sm-3 col-form-label">Description</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" id="description" name="description"></textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="price" class="col-sm-3 col-form-label">Price</label>
                                    <div class="col-sm-8">
                                        <input type="number" class="form-control" id="price" name="price">
                                    </div>
                                </div>
                                <div class="modal-footer border-0 px-0 pb-0">
                                    <button type="button" class="btn-dt-cancel" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn-dt-apply px-4 ml-2">
                                        <i class="fas fa-save mr-2"></i> Buy
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
                            <th>FEE</th>
                            <th class="text-center">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

            <div class="modal fade" id="debitBalanceModal" tabindex="-1" aria-labelledby="debitBalanceModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header modal-header-primary border-0 py-3">
                            <h5 class="modal-title font-weight-bold text-white" id="debitBalanceModalLabel"><i class="fas fa-edit mr-2"></i>Debit Balance</h5>
                            <button type="button" class="close text-white outline-none" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="post" action="<?php echo base_url('admin/createDebitBalance'); ?>">
                                <div class="form-group row">
                                    <label for="merchant" class="col-sm-3 col-form-label">Merchant </label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" readonly id="merchantNameDebit">
                                        <input type="hidden" id="merchantIdDebit" name="merchantIdDebit">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="description" class="col-sm-3 col-form-label">Channel Id </label>
                                    <div class="col-sm-8">
                                        <select name="channelId" class="form-control">
                                            <option value="">Select Channel</option>
                                            <?php foreach ($cashout_channels as $cashout_channel): ?>
                                                <option value="<?php echo $cashout_channel->id; ?>"><?php echo $cashout_channel->id; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="description" class="col-sm-3 col-form-label">Description</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="description" name ="description" >
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="amount" class="col-sm-3 col-form-label">Amount</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="amount" name="amount">
                                    </div>
                                </div>
                                <div class="modal-footer border-0 px-0 pb-0 mt-4 justify-content-center">
                                    <button type="button" class="btn-dt-cancel" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn-dt-apply px-4 ml-2" onclick="javascript: return confirm('Data correct ??')">
                                        <i class="fas fa-check mr-2"></i> Buy
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
<!-- /.container-fluid -->

<script src="<?= base_url('assets/js/server-datatables.js') ?>"></script>
<script>
$(document).ready(function() {
    var table = initServerDataTable('#productTable', "<?= base_url('admin/mobilelegend') ?>", [
        { data: 'no' },
        { data: 'c_caption' },
        { data: 'c_description' },
        { data: 'c_fee' },
        { data: 'action', orderable: false, searchable: false, className: 'text-center' }
    ]);
    table.on('draw', function() {
        $('#kpi-count').text(table.page.info().recordsTotal);
    });
    $('#globalProductSearch').on('keyup', function() {
        table.search(this.value).draw();
    });

});
</script>

<!-- End of Main Content -->
