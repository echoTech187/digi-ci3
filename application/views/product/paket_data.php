<!-- Begin Page Content -->
<div class="container-fluid pb-4">
    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title"><?= $title; ?></h4>
            <p class="dt-page-subtitle">Manage Internet Data Packages and denominations by provider.</p>
        </div>
    </div>

    <!-- ── Filter & KPI Row ── -->
    
        
    <div class="dt-summary-row mb-4">
        <div class="dt-summary-card dt-summary-blue mb-0">
            <div class="dt-summary-body">
                <div class="dt-summary-label">ACTIVE PACKAGES</div>
                <div class="dt-summary-value" id="kpi-count">0</div>
                <div class="dt-summary-sub"><i class="fas fa-tags mr-1"></i>Internet bundles</div>
            </div>
            <div class="dt-summary-icon dt-icon-blue">
                <i class="fas fa-globe"></i>
            </div>
        </div>
        <div class="dt-summary-card dt-summary-purple mb-0">
            <div class="dt-summary-body">
                <div class="dt-summary-label">PROVIDER</div>
                <div class="dt-summary-value text-uppercase" id="kpi-provider">ALL</div>
                <div class="dt-summary-sub"><i class="fas fa-network-wired mr-1"></i>Selected gateway</div>
            </div>
            <div class="dt-summary-icon dt-icon-purple">
                <i class="fas fa-satellite-dish"></i>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm dt-card">
        <!-- ── Toolbar ── -->
        <form id="paket_data_form" onsubmit="return false;">
        <div class="dt-toolbar">
            <div class="dt-search-wrapper">
                <i class="fas fa-search dt-search-icon"></i>
                <input type="text" id="globalProductSearch" class="dt-search-input" placeholder="Search by name, category, or description...">
            </div>
            <div class="dt-toolbar-filters">
                <div class="dt-filter-group">
                    <label class="dt-filter-label"><i class="fas fa-satellite-dish mr-1"></i>Provider</label>
                    <div class="dt-filter-chip">
                        <select id="provider" name="provider" class="dt-chip-select">
                            <option value="">All Providers</option>
                            <option value="paket_data_xl" <?= $this->session->userdata('provider') == 'paket_data_xl' ? 'selected' : ''; ?>>XL Axiata</option>
                            <option value="paket_data_axis" <?= $this->session->userdata('provider') == 'paket_data_axis' ? 'selected' : ''; ?>>Axis</option>
                            <option value="paket_data_telkomsel" <?= $this->session->userdata('provider') == 'paket_data_telkomsel' ? 'selected' : ''; ?>>Telkomsel</option>
                            <option value="paket_data_tri" <?= $this->session->userdata('provider') == 'paket_data_tri' ? 'selected' : ''; ?>>Tri (3)</option>
                        </select>
                    </div>
                </div>
                <!-- Filter & Reset Actions -->
                <button type="button" class="btn-dt-chip-action btn-dt-action-success border-0" data-toggle="modal" data-target="#exampleModal">
                    <i class="fas fa-plus mr-1"></i> Buy Data Package
                </button>
                
            </div>
        </div>
        </form>

        <div class="card-body p-0">

                       
            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content border-0 shadow-sm">
                        <div class="modal-header modal-header-primary border-0 py-3">
                            <h5 class="modal-title font-weight-bold text-white" id="exampleModalLabel">
                                <i class="fas fa-plus-circle mr-2"></i>Buy Data Package
                            </h5>
                            <button type="button" class="close text-white outline-none" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <?php echo validation_errors(); ?>
                            <form method="post" action="<?php echo base_url('admin/createpaketdata'); ?>">
                                <div class="form-group row">
                                    <label for="channelgroup2" class="col-sm-3 col-form-label">Provider</label>
                                    <div class="col-sm-8">
                                        <select name="channelgroup2" id="channelgroup2" class="form-control">
                                            <option value="">Select Provider</option>
                                            <option value="paket_data_xl">XL</option>
                                            <option value="paket_data_axis">Axis</option>
                                            <option value="paket_data_telkomsel">Telkomsel</option>
                                            <option value="paket_data_tri">Tri</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="caption" class="col-sm-3 col-form-label">Caption</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="caption" name ="caption" >
                                        <input type="hidden" class="form-control" id="caption" name ="channelgroup" value= "ppob" >
                                        <!-- <input type="hidden" class="form-control" id="caption" name ="channelgroup2" value= "paket_data"> -->
                                        <input type="hidden" class="form-control" id="caption" name ="name" value= "paket_data">
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
                                        <i class="fas fa-check mr-2"></i> Save
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
    window.productTable = initServerDataTable('#productTable', "<?= base_url('admin/paket_data') ?>", [
        { data: 'no' },
        { data: 'c_caption' },
        { data: 'c_description' },
        { data: 'c_fee' },
        { data: 'action', orderable: false, searchable: false, className: 'text-center' }
    ], {
        "ajax": {
            "url": "<?= base_url('admin/paket_data') ?>",
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
        var providerText = $("#provider option:selected").text();
        if ($(this).val() === "") {
            providerText = "All";
        }
        $('#kpi-provider').text(providerText.replace('pulsa_', '').toUpperCase());
        window.productTable.ajax.reload();
    });


    window.productTable.on('draw', function() {
        $('#kpi-count').text(window.productTable.page.info().recordsTotal);
    });
    $('#globalProductSearch').on('keyup', function() {
        window.productTable.search(this.value).draw();
    });

});
</script>
