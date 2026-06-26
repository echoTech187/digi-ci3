<?php
$search_date_mutation_value    = $this->session->userdata('search_mutation_date1') ?: '';
$search_date_mutation_to_value = $this->session->userdata('search_mutation_date2') ?: '';
$search_position_value         = $this->session->userdata('search_mutation_position') ?: '';
$search_channel_value          = $this->session->userdata('search_mutation_channel') ?: '';
$id = $this->uri->segment(3);

// Badge count for More Filters
$extra_active = 0;
if ($search_date_mutation_value)    $extra_active++;
if ($search_date_mutation_to_value) $extra_active++;
if ($search_position_value)         $extra_active++;
if ($search_channel_value)          $extra_active++;

$download_url = base_url('finance/mutation/download') 
    . "?id=" . $id 
    . "&search_mutation_date1=" . $search_date_mutation_value 
    . "&search_mutation_date2=" . $search_date_mutation_to_value;
?>

<div>
    <!-- ── Toggleable Page Instructional Drawer ── -->
    <div class="drawer-overlay" id="instructionOverlay"></div>
    <div class="drawer-right" id="instructionDrawer">
        <div class="drawer-header">
            <h6 class="drawer-title"><i class="fas fa-book mr-2"></i> Mutation Log Guide</h6>
            <button type="button" class="drawer-close" id="closeDrawerBtn">&times;</button>
        </div>
        <div class="drawer-body">
            <p class="drawer-desc">Audit trail tracking every single balance change (credits, debits, holds, releases) for the merchant.</p>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-exchange-alt text-primary mr-2"></i> Transaction Types</div>
                <p class="drawer-card-text">Track debits (disbursements, fees) and credits (incoming VA, QRIS, e-wallet settlements).</p>
            </div>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-wallet text-primary mr-2"></i> Running Balance</div>
                <p class="drawer-card-text">View the precise available balance snapshot at the exact moment of mutation.</p>
            </div>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-receipt text-primary mr-2"></i> Double-Entry Auditing</div>
                <p class="drawer-card-text">Reconcile mutation records against transaction invoices to maintain ledger integrity.</p>
            </div>
        </div>
    </div>

    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title">Mutation Log</h4>
            <p class="dt-page-subtitle">Tracking financial movements for <strong><?= $merchant[0]->c_name ?></strong></p>
        
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-light border shadow-sm mr-2 d-flex align-items-center" id="toggleGuideBtn">
                <i class="fas fa-book-open text-primary mr-2"></i> <span class="d-none d-md-block">Instructions Guide</span>
            </button>
        </div>
    </div>
    </div>

    <!-- ── KPI Summary Cards ── -->
    

    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">
        <!-- ── Toolbar ── -->
        <form id="filter-form" method="post" action="<?= base_url('finance/mutation/' . $id) ?>">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            
            <div class="dt-toolbar">
                <!-- LEFT: Global Search -->
                <div class="dt-search-wrapper">
                    <i class="fas fa-search dt-search-icon"></i>
                    <?php $active_mutation_search = $this->session->userdata('last_dt_search_mutation') ?: ''; ?>
                    <input type="text" id="dt-search" class="dt-search-input" placeholder="Search by Channel, Description, ID..." value="<?= $active_mutation_search; ?>">
                </div>

                <!-- RIGHT: Filters & Download -->
                <div class="dt-toolbar-filters">
                    
                    <!-- More Filters Trigger -->
                    <div class="dt-filter-group dt-more-filters-wrapper">
                        <label class="dt-filter-label">&nbsp;</label>
                        <button type="button" id="moreFiltersBtn" class="dt-more-filters-btn <?= $extra_active > 0 ? 'dt-more-filters-active' : ''; ?>">
                            <i class="fas fa-sliders-h mr-1 mr-2"></i> Filters
                            <?php if ($extra_active > 0): ?>
                                <span class="dt-more-badge"><?= $extra_active; ?></span>
                            <?php endif; ?>
                            <i class="fas fa-chevron-down ml-1 dt-more-arrow"></i>
                        </button>

                        <!-- Dropdown Panel -->
                        <div class="dt-more-panel" id="moreFiltersPanel">
                            <div class="dt-more-panel-header">
                                <span class="dt-more-panel-title"><i class="fas fa-filter mr-1 mr-2"></i> Advanced Filters</span>
                                <a href="<?= base_url('finance/mutation/reset/' . $id); ?>" class="dt-more-clear">Clear All</a>
                            </div>

                            <div class="dt-more-panel-body">
                                <!-- Period: Date Range -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-calendar-alt mr-1 mr-2"></i> Date Range</label>
                                    <div class="premium-picker">
                                        <input type="date" name="search_date_mutation" class="dt-chip-input" value="<?= $search_date_mutation_value ?>" title="From Date">
                                        <span class="text-muted mx-1" style="font-size:11px;">→</span>
                                        <input type="date" name="search_date_mutation_to" class="dt-chip-input" value="<?= $search_date_mutation_to_value ?>" title="To Date">
                                    </div>
                                </div>

                                <!-- Position Select -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-exchange-alt mr-1 mr-2"></i> Position</label>
                                    <select name="search_position" id="search_position" class="dt-more-select">
                                        <option value="">All Positions</option>
                                        <option value="Credit" <?= ($search_position_value=='Credit')?'selected':'' ?>>Credit (Cash In)</option>
                                        <option value="Debit" <?= ($search_position_value=='Debit')?'selected':'' ?>>Debit (Cash Out)</option>
                                    </select>
                                </div>

                                <!-- Channel Select -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-layer-group mr-1 mr-2"></i> Channel</label>
                                    <select name="search_channel" id="search_channel" class="dt-more-select select2-channel">
                                        <option value="">All Channels</option>
                                        <?php if (!empty($channels)): ?>
                                            <?php foreach ($channels as $ch): ?>
                                                <option value="<?= $ch ?>" <?= ($search_channel_value==$ch)?'selected':'' ?>><?= $ch ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="dt-more-panel-footer">
                                <button type="submit" class="btn-dt-apply btn-dt-action-primary shadow-sm">
                                    <i class="fas fa-check mr-1 mr-2"></i> APPLY FILTER
                                </button>
                                <button type="button" id="moreFiltersClose" class="btn-dt-cancel btn-dt-secondary">
                                    CANCEL
                                </button>
                            </div>
                        </div><!-- /dt-more-panel -->
                    </div>

                    <!-- Action: Download -->
                    <div class="dt-filter-group">
                         <label class="dt-filter-label">&nbsp;</label>
                         <a href="<?= $download_url ?>" class="btn-dt-chip-action btn-dt-action-success ">
                            <i class="fas fa-download"></i> <span class="d-none d-md-block">Download</span>
                        </a>
                    </div>

                </div><!-- /.dt-toolbar-filters -->
            </div>
        </form>

        <!-- ── Table ── -->
        <div class="table-responsive">
            <table id="mutationTable" class="table dt-table mb-0" style="width:100%">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Date Time</th>
                        <th>Position</th>
                        <th>Channel</th>
                        <th>Description</th>
                        <th class="text-right">Balance Before</th>
                        <th class="text-right">Amount</th>
                        <th class="text-right">Balance After</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <!-- Pagination handled by drawCallback -->
        <div class="dt-footer" id="dt-footer-container"></div>
    </div>
</div>
                                           

<script>
$(document).ready(function() {
    // Select2 integration inside filters panel
    const channelSelect = $('.select2-channel');
    channelSelect.each(function() {
        $(this).select2({
            width: '100%',
            dropdownAutoWidth: true,
            dropdownParent: $('body'),
            minimumResultsForSearch: 5,
            placeholder: "All Channels",
            allowClear: true
        });
    });

    // Sub-channel dynamic loading
    $('#search_position').on('change', function() {
        const position = this.value;
        channelSelect.prop('disabled', true).empty().append('<option value="">Loading...</option>').trigger('change');

        if (!position) {
            channelSelect.html('<option value="">All Channels</option>').prop('disabled', true).trigger('change');
            return;
        }

        $.post("<?= base_url('finance/mutation/channels') ?>", { 
            position: position, 
            merchant_id: "<?= $id ?>" 
        }, function(data) {
            channelSelect.empty().append('<option value="">All Channels</option>');
            data.forEach(ch => channelSelect.append(new Option(ch, ch)));
            channelSelect.prop('disabled', false).trigger('change');
        }, 'json').fail(() => {
            channelSelect.html('<option value="">Error loading</option>').prop('disabled', true).trigger('change');
        });
    });

    // Disable channel if no position selected initially
    if (!$('#search_position').val()) {
        channelSelect.prop('disabled', true);
    }

    // Initialize Server-side DataTable
    var table = initServerDataTable("#mutationTable", "<?= base_url('finance/mutation/'.$id) ?>", [
        { 
            data: 'no', 
            orderable: false, 
            className: 'text-center' 
        },
        { 
            data: 'c_datetime',
            render: function(data){
                return '<i class="far fa-clock mr-1 text-muted"></i>' + (data ? data : '-');
            }
        },
        { 
            data: 'c_position_raw', // Use raw data
            render: function(data) {
                if (!data) return '-';
                var d = data.toLowerCase();
                var cls = (d === 'credit') ? 'success' : 'danger';
                return '<span class="badge badge-pill badge-' + cls + '-soft text-' + cls + ' px-3 py-1">' + data + '</span>';
            }
        },
        { 
            data: 'channelName',
            render: function(data) {
                return '<span class="badge badge-light border px-2 py-1 text-uppercase small">' + (data || '-') + '</span>';
            }
        },
        { 
            data: 'description',
            className: 'small text-muted'
        },
        { 
            data: 'c_balance_before_raw', 
            className: 'text-right font-weight-bold text-secondary', 
            render: function(data){
                return 'Rp ' + number_format(data, 0, ',', '.');
            }
        },
        { 
            data: 'c_amount_raw', 
            className: 'text-right font-weight-bold', 
            render: function(data, type, row){
                var isCredit = (row.c_position_raw || '').toLowerCase() === 'credit';
                var colorCls = isCredit ? 'text-success' : 'text-danger';
                var prefix = isCredit ? '+ Rp ' : '- Rp ';
                return '<span class="' + colorCls + '">' + prefix + number_format(data, 0, ',', '.') + '</span>';
            }
        },
        { 
            data: 'c_balance_raw', 
            className: 'text-right font-weight-bold text-dark', 
            render: function(data){
                return 'Rp ' + number_format(data, 0, ',', '.');
            }
        }
    ], {
        "search": {
            "search": "<?= $this->session->userdata('last_dt_search_mutation') ?: '' ?>"
        }
    });

    // Global search
    // Global search with Debounce
    $('#dt-search').on('input', debounce(function() {
        table.search(this.value).draw();
    }, 400));

    // ── More Filters dropdown ──
    const $moreBtn   = $('#moreFiltersBtn');
    const $morePanel = $('#moreFiltersPanel');
    const $moreClose = $('#moreFiltersClose');

    $moreBtn.on('click', function(e) {
        e.stopPropagation();
        const isOpen = $morePanel.hasClass('dt-panel-open');
        $morePanel.toggleClass('dt-panel-open', !isOpen);
        $moreBtn.toggleClass('dt-open', !isOpen);
    });

    $moreClose.on('click', function() {
        $morePanel.removeClass('dt-panel-open');
        $moreBtn.removeClass('dt-open');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.dt-more-filters-wrapper').length) {
            $morePanel.removeClass('dt-panel-open');
            $moreBtn.removeClass('dt-open');
        }
    });
});
</script>




<script>
$(document).ready(function() {
    // Drawer Toggle Logic
    $('#toggleGuideBtn').on('click', function() {
        $('#instructionDrawer, #instructionOverlay').addClass('open');
        $('body').css('overflow', 'hidden');
    });

    $('#closeDrawerBtn, #instructionOverlay').on('click', function() {
        $('#instructionDrawer, #instructionOverlay').removeClass('open');
        $('body').css('overflow', '');
    });
});
</script>
