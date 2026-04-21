<!-- Begin Page Content -->
<div>

    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title"><?= $title; ?></h4>
            <p class="dt-page-subtitle">Manage and configure available cash-in payment channels and fee structures.</p>
        </div>
    </div>

    <!-- ── KPI Summary Cards ── -->
    

    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">

        <!-- ── Toolbar ── -->
        <div class="dt-toolbar">
            <!-- LEFT: Global Search -->
            <div class="dt-search-wrapper">
                <i class="fas fa-search dt-search-icon"></i>
                <input type="text" id="cashinGlobalSearch" class="dt-search-input" placeholder="Search by Channel, ID, or Category...">
            </div>
            <div class="dt-toolbar-filters">
                <!-- Filter & Reset Actions -->
                <button type="button" class="btn-dt-chip-action btn-dt-action-success border-0" data-toggle="modal" data-target=".bd-example-modal-lg">
                    <i class="fas fa-plus"></i> <span class="d-none d-md-block">New Cash In Channel</span>
                </button>
            </div>
        </div>

        <!-- ── Alert Messages ── -->
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success mx-4 mt-3 mb-0 border-0 shadow-sm animate__animated animate__fadeIn">
                <i class="fas fa-check-circle "></i> <?= $this->session->flashdata('success'); ?>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger mx-4 mt-3 mb-0 border-0 shadow-sm animate__animated animate__fadeIn">
                <i class="fas fa-exclamation-circle "></i> <?= $this->session->flashdata('error'); ?>
            </div>
        <?php endif; ?>

        <!-- ── Table ── -->
        <div class="table-responsive">
            <table class="table dt-table mb-0" id="cashinTable" style="width:100%">
                <thead>
                    <tr>
                        <th>NO</th>
                        <th>ID</th>
                        <th>CHANNEL GROUP</th>
                        <th>DESCRIPTION</th>
                        <th>EXTERNAL ID DEFAULT</th>
                        <th>FEE TYPE</th>
                        <th>FEE</th>
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

<!-- ── Modal Tambah Chanel ── -->
<div class="modal fade bd-example-modal-lg" id="addChanelModal" tabindex="-1" role="dialog" aria-labelledby="addChanelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <!-- Header Legacy Migrated -->
<div class="modal-header modal-header-primary border-0 mh-premium">
    <div class="d-flex align-items-center">
        <div class="mh-icon-badge">
            <i class="fas fa-star"></i>
        </div>
        <div class="mh-title-wrap">
            <h6 class="mh-title" id="addChanelModalLabel">New Cash In Channel</h6>
            <small class="mh-subtitle" >Manage and process information details</small>
        </div>
    </div>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
            <div class="modal-body p-4 text-dark">
                <?php if(validation_errors()): ?>
                    <div class="alert alert-danger mb-4 shadow-sm border-0 small">
                        <i class="fas fa-exclamation-triangle "></i> <?= validation_errors(); ?>
                    </div>
                <?php endif; ?>
                
                <form method="post" action="<?= base_url('admin/createCashinChanel'); ?>">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="dt-more-label mb-2">Channel ID</label>
                            <input type="text" class="dt-more-input" required name="id" placeholder="e.g. MANDIRI_VA">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="dt-more-label mb-2">Channel Group</label>
                            <input type="text" class="dt-more-input" required name="chanelgroup" placeholder="e.g. VIRTUAL_ACCOUNT">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="dt-more-label mb-2">Description</label>
                        <textarea class="dt-more-input" name="description" rows="2" placeholder="Briefly describe this channel..."></textarea>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="dt-more-label mb-2">External Default</label>
                            <input type="text" class="dt-more-input" required name="externaldefault" placeholder="Provider reference">
                        </div>
                        <div class="col-md-6">
                            <label class="dt-more-label mb-2">Fee Type</label>
                            <select class="dt-more-select" required name="feetype">
                                <option value="" selected disabled>Select fee type</option>
                                <option value="fixed">Fixed</option>
                                <option value="Percentage">Percentage</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label class="dt-more-label mb-2">Fee Value</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text border-right-0" style="border-radius:8px 0 0 8px; font-size:12px;">Rp</span>
                                </div>
                                <input type="number" class="dt-more-input h-auto" required name="fee" style="border-radius:0 8px 8px 0;">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <label class="dt-more-label mb-2">Amount Min</label>
                            <input type="number" class="dt-more-input" required name="amountmin" value="10000">
                        </div>
                        <div class="col-md-4">
                            <label class="dt-more-label mb-2">Amount Max</label>
                            <input type="number" class="dt-more-input" required name="amountmax" value="10000000">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="dt-more-label mb-2">Settlement Interval (Days)</label>
                        <input type="number" class="dt-more-input" required name="settlementinterval" value="1">
                    </div>

                    <div class="modal-footer px-0 pb-0 border-0 pt-3">
                        <button type="button" class="btn-dt-cancel" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn-dt-apply px-4 ">
                            <i class="fas fa-save  mr-2"></i> Save New Channel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ── Modal Debit Balance (Action) ── -->
<div class="modal fade" id="debitBalanceModal" tabindex="-1" aria-labelledby="debitBalanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <!-- Header Legacy Migrated -->
<div class="modal-header modal-header-primary border-0 mh-premium">
    <div class="d-flex align-items-center">
        <div class="mh-icon-badge">
            <i class="fas fa-edit"></i>
        </div>
        <div class="mh-title-wrap">
            <h6 class="mh-title"  id="debitBalanceModalLabel">Edit Channel Info</h6>
            <small class="mh-subtitle" >Modify and update existing information</small>
        </div>
    </div>
    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity:0.8;">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
            <div class="modal-body p-4 text-dark">
                <form method="post" action="<?= base_url('admin/createDebitBalance'); ?>">
                    <div class="mb-3">
                        <label class="dt-more-label mb-2">ID / Merchant</label>
                        <input type="text" class="dt-more-input bg-light" readonly id="merchantNameDebit">
                        <input type="hidden" id="merchantIdDebit" name="merchantIdDebit">
                    </div>
                    <div class="mb-3">
                        <label class="dt-more-label mb-2">Channel Group</label>
                        <input type="text" class="dt-more-input bg-light" readonly id="channelGroupDebit">
                    </div>
                    <div class="mb-3">
                        <label class="dt-more-label mb-2">Description</label>
                        <input type="text" class="dt-more-input" name="description" placeholder="Enter reason/info">
                    </div>
                    <div class="mb-4">
                        <label class="dt-more-label mb-2">Amount Adjustment</label>
                        <input type="number" class="dt-more-input" name="amount" placeholder="0">
                    </div>
                    
                    <div class="modal-footer px-0 pb-0 border-0 pt-2">
                        <button type="button" class="btn-dt-cancel" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn-dt-apply px-4 " onclick="return confirm('Is the data correct?')">
                            <i class="fas fa-check  mr-2"></i> Process Action
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Standardize DataTables for premium look
        var table = initServerDataTable('#cashinTable', "<?= base_url('admin/cashin') ?>", [
                {data: 'no', orderable: false, className: 'text-center'},
                {data: 'id', className: 'font-weight-bold text-primary dt-id-column'},
                {data: 'c_channelGroup', render: function(data){
                    return '<span class="badge badge-light text-dark border px-2 py-1 text-uppercase" style="font-size:10px; letter-spacing:0.5px; border-radius:4px;">'+data+'</span>';
                }},
                {data: 'c_description', className: 'small text-muted'},
                {data: 'c_externalIdDefault', className: 'text-dark'},
                {data: 'c_feeType', render: function(data){
                    var cls = (data.toLowerCase() === 'fixed') ? 'text-info' : 'text-primary';
                    return '<span class="'+cls+' font-weight-bold" style="font-size:11px;">'+data.toUpperCase()+'</span>';
                }},
                {data: 'c_fee', className: 'font-weight-bold text-dark text-nowrap', render: function(data, type, row) {
                    return 'Rp ' + number_format(data, 0, ',', '.');
                }},
                {
                    data: null, 
                    orderable: false, 
                    className: 'text-center',
                    render: function(data, type, row) {
                        return '<div class="text-center">' +
                               '    <button class="btn btn-sm btn-outline-primary edit-cashin px-3 shadow-none font-weight-bold" ' +
                               '        data-toggle="modal" data-target="#debitBalanceModal" ' +
                               '        data-id="' + row.id + '" ' +
                               '        data-group="' + row.c_channelGroup + '">' +
                               '        <i class="fas fa-edit mr-1"></i> Edit' +
                               '    </button>' +
                               '</div>';
                    }
                }
            ]);

        // Global Search mapping
        $('#cashinGlobalSearch').on('keyup', function() {
            table.search(this.value).draw();
        });

        // Edit button mapping
        $(document).on('click', '.edit-cashin', function() {
            var id = $(this).data('id');
            var group = $(this).data('group');
            $('#merchantNameDebit').val(id);
            $('#merchantIdDebit').val(id);
            $('#channelGroupDebit').val(group);
        });
    });
</script>



