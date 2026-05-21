<!-- Begin Page Content -->
<div >
    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title">Merchant Mutations</h4>
            <p class="dt-page-subtitle">Historical ledger of all credit and debit activities for <strong><?php echo $merchant[0]->c_name; ?></strong>.</p>
        </div>
        <div class="d-flex" style="gap:10px;">
            <button type="button" class="btn-dt-action btn-dt-action-primary border-0 d-flex align-items-center shadow-sm" id="toggleGuideBtn" >
                <i class="fas fa-book-open mr-2"></i> <span class="d-none d-md-block">Instructions Guide</span>
            </button>
        </div>
    </div>

    <!-- ── Toggleable Page Instructional Drawer ── -->
    <div class="drawer-overlay" id="instructionOverlay"></div>
    <div class="drawer-right" id="instructionDrawer">
        <div class="drawer-header">
            <h6 class="drawer-title"><i class="fas fa-book mr-2"></i> Mutation Ledger Guide</h6>
            <button type="button" class="drawer-close" id="closeDrawerBtn">&times;</button>
        </div>
        <div class="drawer-body">
            <p class="drawer-desc">This ledger displays the historical record of all credit and debit activities for this merchant.</p>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-history text-primary mr-2"></i> Audit Ledger</div>
                <p class="drawer-card-text">Track timestamps, Credit/Debit positions, routing channels, unique reference numbers, and settlement amounts.</p>
            </div>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-filter text-primary mr-2"></i> Position Filter</div>
                <p class="drawer-card-text">Filter by Credit or Debit. Selecting a position will dynamically load the associated channel sources.</p>
            </div>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-download text-primary mr-2"></i> Export Options</div>
                <p class="drawer-card-text">Use the Export button to download the filtered mutation list as an Excel/CSV spreadsheet report.</p>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm dt-card mb-4">
        <div class="card-body p-0">
            <!-- ── Toolbar / Filters ── -->
            <form id="mutation_form" method="post" action="<?php echo base_url('finance/mutation/' . $id); ?>">
                <div class="dt-toolbar">
                    <div class="dt-toolbar-filters flex-wrap">
                        <!-- Date Filter -->
                        <div class="dt-filter-group">
                            <label class="dt-filter-label">Filter Date</label>
                            <input type="date" id="search_date_mutation" name="search_date_mutation" class="dt-chip-input" style="height: 32px;" value="<?php echo $search_date_mutation_value; ?>">
                        </div>

                        <!-- Position Filter -->
                        <div class="dt-filter-group ml-md-3">
                            <label class="dt-filter-label">Position</label>
                            <select name="search_position" id="search_position" class="dt-more-select px-2" style="height: 32px; border-radius: 6px;">
                                <option value="">All Positions</option>
                                <option value="Credit" <?= ($search_position=='Credit')?'selected':'' ?>>Credit</option>
                                <option value="Debit" <?= ($search_position=='Debit')?'selected':'' ?>>Debit</option>
                            </select>
                        </div>

                        <!-- Channel Filter -->
                        <div class="dt-filter-group ml-md-3" style="min-width: 180px;">
                            <label class="dt-filter-label">Channel Source</label>
                            <select name="search_channel" id="search_channel" class="dt-more-select select2-simple">
                                <option value="">All Channels</option>
                                <?php if (!empty($channels)): ?>
                                    <?php foreach ($channels as $ch): ?>
                                        <option value="<?= $ch ?>" <?= ($search_channel==$ch)?'selected':'' ?>><?= $ch ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Actions -->
                        <div class="dt-filter-group ml-md-auto pt-2 pt-md-0">
                            <label class="dt-filter-label d-none d-md-block">&nbsp;</label>
                            <div class="d-flex" style="gap:6px;">
                                <button type="submit" name="submit" class="btn-dt-action btn-dt-action-success">
                                    <i class="fas fa-filter mr-1 mr-2"></i> Apply
                                </button>
                                <a href="<?php echo $download_url; ?>" class="btn-dt-chip-action btn-dt-action-success border-0">
                                    <i class="fas fa-download"></i> <span class="d-none d-md-block">Export</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <?= $this->session->flashdata('success') ? '<div class="alert alert-success mx-4 mt-3 border-0 shadow-sm"><i class="fas fa-check-circle "></i>'.$this->session->flashdata('success').'</div>' : '' ?>
            <?= isset($alert_message) ? '<div class="alert alert-danger mx-4 mt-3 border-0 shadow-sm"><i class="fas fa-exclamation-triangle "></i>'.$alert_message.'</div>' : '' ?>
            <?= $error_message ? '<div class="alert alert-danger mx-4 mt-3 border-0 shadow-sm"><i class="fas fa-exclamation-circle "></i>'.$error_message.'</div>' : '' ?>
        </div>

        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table dt-table mb-0" style="width:100%">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 70px;">NO</th>
                            <th>TIMESTAMP</th>
                            <th class="text-center">POSITION</th>
                            <th>CHANNEL</th>
                            <th>REFERENCE NO</th>
                            <th>DESCRIPTION</th>
                            <th class="text-right">AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($mutations)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2 d-block mr-2"></i> No mutation data found for the selected period.
                            </td>
                        </tr>
                    <?php else: ?>
                    <?php foreach ($mutations as $mutation): ?>
                        <tr>
                            <td class="text-center font-weight-bold text-muted"><?php echo ++$start ?></td>
                            <td class="small"><?php echo $mutation->timeRefLog; ?></td>
                            <td class="text-center">
                                <?php 
                                    $pos_cls = (strtolower($mutation->c_potition) == 'credit') ? 'success' : 'danger';
                                ?>
                                <span class="badge badge-<?= $pos_cls; ?>-soft text-<?= $pos_cls; ?> px-2 font-weight-bold"><?php echo strtoupper($mutation->c_potition); ?></span>
                            </td>
                            <td><span class="badge badge-dark px-2 font-weight-normal"><?php echo $mutation->channelName; ?></span></td>
                            <td class="small font-weight-bold text-dark"><?php echo $mutation->refNoLog; ?></td>
                            <td class="small text-muted"><?php echo $mutation->description; ?></td>
                            <td class="text-right font-weight-bold text-dark">Rp <?php echo number_format($mutation->c_amount, 0, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

                <div class="d-flex justify-content-center mt-4" style="font-size: 14px;">
                    <div class="pagination-links">
                        <?php echo $pagination; ?>
                    </div>
                </div>                    
            </div>
        </div>
    </div>  
</div>
<!-- /.container-fluid -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<script>
$(document).ready(function () {
    // Drawer Logic
    $('#toggleGuideBtn').on('click', function() {
        $('#instructionDrawer, #instructionOverlay').addClass('open');
        $('body').css('overflow', 'hidden');
    });

    $('#closeDrawerBtn, #instructionOverlay').on('click', function() {
        $('#instructionDrawer, #instructionOverlay').removeClass('open');
        $('body').css('overflow', '');
    });

    const channelSelect = $('#search_channel');

    // INIT SELECT2 (HARUS ENABLED)
    channelSelect.select2({
        theme: 'bootstrap-5',
        placeholder: "All Channel",
        allowClear: true,
        dropdownAutoWidth: true,
        width: '100%'
    });

    // Disable di awal via JS
    channelSelect.prop('disabled', true).trigger('change');

    $('#search_position').on('change', function () {
            console.log('POSITION CHANGED:', this.value);


        const position = this.value;

        channelSelect
            .prop('disabled', true)
            .empty()
            .append('<option value="">Loading...</option>')
            .trigger('change');

        if (!position) {
            channelSelect
                .html('<option value="">All Channel</option>')
                .prop('disabled', true)
                .trigger('change');
            return;
        }

        fetch("<?= base_url('finance/mutation/channels') ?>", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "position=" + encodeURIComponent(position) + "&merchant_id=<?= $id ?>"
        })
        .then(res => res.json())
        .then(data => {

            channelSelect.empty().append('<option value="">All Channel</option>');

            data.forEach(ch => {
                channelSelect.append(new Option(ch, ch));
            });

            // ENABLE SETELAH DATA ADA
            channelSelect.prop('disabled', false).trigger('change');
        })
        .catch(err => {
            console.error(err);
            channelSelect
                .html('<option value="">Error load channel</option>')
                .prop('disabled', true)
                .trigger('change');
        });
    });

});
</script>


