<div>
    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title">Monitoring DLQ (Failed Notifications)</h4>
            <p class="dt-page-subtitle">Monitoring and manual retry for failed webhook notifications</p>
        </div>

    </div>

    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">
        <!-- ── Toolbar ── -->
        <div class="dt-toolbar">
            <div class="dt-search-wrapper flex-grow-1 mb-2 mb-md-0" style="min-width: 280px;">
                <i class="fas fa-search dt-search-icon"></i>
                <input type="text" id="dt-search" class="dt-search-input" placeholder="Search Trans ID..." value="<?= htmlspecialchars($search_channel ?? ''); ?>">
            </div>

            <!-- RIGHT: Filters & Actions -->
            <div class="dt-toolbar-filters d-flex align-items-center gap-2">


                <!-- More Filters Trigger -->
                <div class="dt-filter-group dt-more-filters-wrapper">
                    <button type="button" id="dlqMoreFiltersBtn" class="dt-more-filters-btn">
                        <i class="fas fa-sliders-h mr-2"></i> Filters
                        <span class="dt-more-badge" id="dlqFilterBadge" style="display: none;">0</span>
                        <i class="fas fa-chevron-down ml-1 dt-more-arrow"></i>
                    </button>

                    <!-- Dropdown Panel -->
                    <div class="dt-more-panel" id="dlqMoreFiltersPanel">
                        <div class="dt-more-panel-header">
                            <span class="dt-more-panel-title"><i class="fas fa-filter mr-2"></i> Advanced Filters</span>
                            <a href="javascript:void(0)" id="dlqMoreClear" class="dt-more-clear">Clear All</a>
                        </div>

                        <div class="dt-more-panel-body">
                            <!-- Merchant -->
                            <div class="dt-more-field">
                                <label class="dt-more-label"><i class="fas fa-store mr-1 mr-2"></i> Merchant</label>
                                <select id="filter-merchant" class="dt-more-select filter-select">
                                    <option value="">All Merchants</option>
                                    <?php foreach ($merchants as $m): ?>
                                        <option value="<?= $m['id'] ?>" <?= (isset($search_merchant) && $search_merchant == $m['id']) ? 'selected' : '' ?>><?= htmlspecialchars($m['c_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Date Range -->
                            <div class="dt-more-field">
                                <label class="dt-more-label"><i class="fas fa-calendar-alt mr-1 mr-2"></i> Period</label>
                                <div class="premium-picker">
                                    <input type="date" id="filter-start-date" class="dt-chip-input filter-input" title="Date From" value="<?= htmlspecialchars($search_date1 ?? ''); ?>">
                                    <span class="picker-separator">to</span>
                                    <input type="date" id="filter-end-date" class="dt-chip-input filter-input" title="Date To" value="<?= htmlspecialchars($search_date2 ?? ''); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="dt-more-panel-footer">
                            <button type="button" id="dlqMoreApply" class="btn-dt-apply btn-dt-action-primary shadow-sm">
                                <i class="fas fa-check mr-2"></i> APPLY FILTER
                            </button>
                            <button type="button" id="dlqMoreFiltersClose" class="btn-dt-cancel btn-dt-secondary">
                                CANCEL
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Export Dropdown -->
                <div class="dropdown">
                    <button class="dt-more-filters-btn dropdown-toggle" type="button" id="exportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background: white; border: 1px solid #d0d7de;">
                        <i class="fas fa-download mr-1"></i> Export
                    </button>
                    <div class="dropdown-menu dropdown-menu-right shadow-sm border-0" aria-labelledby="exportDropdown" style="border-radius: 8px; font-size: 14px; min-width: 150px; padding: 8px;">
                        <a class="dropdown-item d-flex align-items-center py-2" href="javascript:void(0)" id="btn-export-excel" style="border-radius: 4px;">
                            <i class="fas fa-file-excel text-success mr-2"></i> as Excel
                        </a>
                        <a class="dropdown-item d-flex align-items-center py-2" href="javascript:void(0)" id="btn-export-csv" style="border-radius: 4px;">
                            <i class="fas fa-file-csv text-primary mr-2"></i> as CSV
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <!-- ── Table ── -->
        <div class="table-responsive">
            <table id="dlq-table" class="table dt-table mb-0" style="width:100%">
                <thead>
                    <tr>
                        <th width="40" class="ps-4">No</th>
                        <th>Failed Time</th>
                        <th>Merchant</th>
                        <th>Type</th>
                        <th>Trans ID</th>
                        <th width="150" class="text-center pe-4">Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div id="dt-footer-container"></div>
    </div>
</div>

<script>
var table;
$(document).ready(function() {
    
    // Initialize standard custom Datatable
    // Note: Since initServerDataTable might not support custom ajax data directly via its wrapper args in some versions,
    // we use standard DataTables initialization directly or append to it if wrapper supports it.
    // However, looking at standard digi-ci3 wrapper, it usually returns the DataTable instance.
    // If initServerDataTable doesn't support custom data params, we configure it after or modify it.
    // Assuming we can re-init or it's better to use standard datatable code for advanced filters.
    // Let's use standard DataTables initialization to ensure we can pass `data` payload properly.
    
    table = initServerDataTable("#dlq-table", "<?= base_url('DlqController/ajax_list') ?>", [
        { "data": "no", "className": "ps-4 text-muted small", "orderable": false, "searchable": false },
        { 
            "data": "created_at",
            "render": function(data) {
                return '<span class="fw-bold text-dark">' + data + '</span>';
            }
        },
        { 
            "data": "merchant_name",
            "render": function(data, type, row) {
                var name = data ? data : 'Unknown';
                return '<div class="fw-bold text-dark">' + name + '</div>';
            }
        },
        { 
            "data": "type",
            "render": function(data) {
                if (data === 'ewallet') return '<span class="badge badge-primary">E-Wallet</span>';
                if (data === 'virtual-account') return '<span class="badge badge-info">VA</span>';
                if (data === 'qris-mpm') return '<span class="badge badge-warning">QRIS</span>';
                if (data === 'transfer') return '<span class="badge badge-secondary">Transfer</span>';
                return '<span class="badge badge-dark">' + data + '</span>';
            }
        },
        { "data": "ref_transactionId" },
        { 
            "data": null, 
            "className": "text-center pe-4",
            "orderable": false,
            "searchable": false,
            "render": function(data, type, row) {
                var btn = '<button class="btn btn-sm btn-success btn-retry" data-id="'+row.id+'"><i class="fas fa-sync mr-1"></i> Retry</button>';
                return btn;
            }
        }
    ], {
        "ajax": {
            "url": "<?= base_url('DlqController/ajax_list') ?>",
            "type": "POST",
            "data": function(d) {
                var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
                var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';
                if (csrfName && csrfHash) { d[csrfName] = csrfHash; }
                
                d.search_channel = $('#dt-search').val();
                d.merchant_id = $('#filter-merchant').val();
                d.start_date = $('#filter-start-date').val();
                d.end_date = $('#filter-end-date').val();
            }
        }
    });

    // Search Input Logic
    $('#dt-search').on('keyup', debounce(function() {
        table.ajax.reload();
    }, 500));

    // Initialize Select2 for Advanced Filters
    $('#dlqMoreFiltersPanel select').not('.select2-hidden-accessible').each(function () {
        $(this).select2({
            width: '100%',
            dropdownAutoWidth: true,
            dropdownParent: $('body'),
            minimumResultsForSearch: 0
        });
    });

    // Advanced Filters Panel Logic
    $('#dlqMoreFiltersBtn').on('click', function(e) {
        e.stopPropagation();
        var isOpen = $('#dlqMoreFiltersPanel').hasClass('dt-panel-open');
        $('#dlqMoreFiltersPanel').toggleClass('dt-panel-open', !isOpen);
        $(this).toggleClass('dt-open', !isOpen);
    });

    $('#dlqMoreFiltersClose').on('click', function(e) {
        e.stopPropagation();
        $('#dlqMoreFiltersPanel').removeClass('dt-panel-open');
        $('#dlqMoreFiltersBtn').removeClass('dt-open');
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('.dt-more-filters-wrapper').length) {
            $('#dlqMoreFiltersPanel').removeClass('dt-panel-open');
            $('#dlqMoreFiltersBtn').removeClass('dt-open');
        }
    });

    $('#dlqMoreClear').on('click', function(e) {
        e.stopPropagation();
        $('#filter-merchant').val('');
        $('#filter-start-date').val('');
        $('#filter-end-date').val('');
        updateFilterBadge();
        table.ajax.reload();
    });

    $('#dlqMoreApply').on('click', function(e) {
        e.stopPropagation();
        updateFilterBadge();
        table.ajax.reload();
        $('#dlqMoreFiltersPanel').removeClass('dt-panel-open');
        $('#dlqMoreFiltersBtn').removeClass('dt-open');
    });

    function updateFilterBadge() {
        var count = 0;
        if ($('#filter-merchant').val()) count++;
        if ($('#filter-start-date').val()) count++;
        if ($('#filter-end-date').val()) count++;

        var badge = $('#dlqFilterBadge');
        if (count > 0) {
            badge.text(count).show();
        } else {
            badge.hide();
        }
    }

    // Export Logic
    function checkFiltersBeforeExport() {
        var merchant = $('#filter-merchant').val();
        var startDate = $('#filter-start-date').val();
        var endDate = $('#filter-end-date').val();
        
        if (!merchant && !startDate && !endDate) {
            Swal.fire({
                icon: 'warning',
                title: 'No Filters Applied',
                text: 'Please select at least a Merchant or a Date Range before exporting to prevent downloading massive data.',
                confirmButtonColor: '#3085d6'
            });
            return false;
        }
        return true;
    }

    $('#btn-export-csv').on('click', function(e) {
        e.preventDefault();
        if (!checkFiltersBeforeExport()) return;

        var params = $.param({
            merchant_id: $('#filter-merchant').val(),
            start_date: $('#filter-start-date').val(),
            end_date: $('#filter-end-date').val()
        });
        window.location.href = "<?= base_url('DlqController/export_csv') ?>?" + params;
    });

    $('#btn-export-excel').on('click', function(e) {
        e.preventDefault();
        if (!checkFiltersBeforeExport()) return;

        var params = $.param({
            merchant_id: $('#filter-merchant').val(),
            start_date: $('#filter-start-date').val(),
            end_date: $('#filter-end-date').val()
        });
        window.location.href = "<?= base_url('DlqController/export_excel') ?>?" + params;
    });

    // Initialize the badge if there are pre-filled filters from session
    updateFilterBadge();

    // Action Buttons Logic
    $('#dlq-table').on('click', '.btn-retry', function() {
        var id = $(this).data('id');
        var btn = $(this);

        Swal.fire({
            title: 'Retry Notification?',
            text: "This will push the transaction back to the main queue.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, retry it!'
        }).then((result) => {
            if (result.isConfirmed) {
                btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
                
                var csrfName = $('meta[name="csrf-token-name"]').attr('content');
                var csrfHash = $('meta[name="csrf-token-hash"]').attr('content');
                var ajaxData = { id: id };
                if (csrfName && csrfHash) { ajaxData[csrfName] = csrfHash; }

                $.ajax({
                    url: "<?= base_url('DlqController/retry_single') ?>",
                    type: "POST",
                    data: ajaxData,
                    dataType: "json",
                    success: function(res) {
                        if (res.status) {
                            Swal.fire('Success!', res.message, 'success');
                            table.ajax.reload(null, false);
                        } else {
                            Swal.fire('Failed!', res.message, 'error');
                            btn.html('<i class="fas fa-sync"></i> Retry').prop('disabled', false);
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'An error occurred while retrying.', 'error');
                        btn.html('<i class="fas fa-sync"></i> Retry').prop('disabled', false);
                    }
                });
            }
        })
    });



});
</script>
