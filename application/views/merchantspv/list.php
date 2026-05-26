<!-- Begin Page Content -->
<div>

    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <div class="d-flex align-items-center mb-1">
                <a href="<?= base_url('merchant/supervisor'); ?>" class="btn btn-sm btn-light border rounded-circle " title="Back to Supervisors">
                    <i class="fas fa-arrow-left text-primary"></i>
                </a>
                <h4 class="dt-page-title text-dark fw-700 mb-0">Merchants for Supervisor #<?= $supervisor_id ?></h4>
            </div>
            <p class="dt-page-subtitle text-muted">Viewing all merchants currently assigned to this supervisor.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-white text-primary border shadow-sm px-3 py-2" style="font-size: 13px;">
                <i class="fas fa-store mr-1"></i> <?= count($merchants) ?> Merchants Found
            </span>
            <button type="button" class="btn-dt-action btn-dt-action-primary border-0 d-flex align-items-center shadow-sm" id="toggleGuideBtn" >
                <i class="fas fa-book-open mr-2"></i> <span class="d-none d-md-block">Instructions Guide</span>
            </button>
        </div>
    </div>

    <!-- ── Toggleable Page Instructional Drawer ── -->
    <div class="drawer-overlay" id="instructionOverlay"></div>
    <div class="drawer-right" id="instructionDrawer">
        <div class="drawer-header">
            <h6 class="drawer-title"><i class="fas fa-book mr-2"></i> Supervisor Merchants Guide</h6>
            <button type="button" class="drawer-close" id="closeDrawerBtn">&times;</button>
        </div>
        <div class="drawer-body">
            <p class="drawer-desc">This page displays all merchant accounts currently assigned to and supervised by this specific supervisor agent.</p>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-user-shield text-primary mr-2"></i> Supervisor Merchants</div>
                <p class="drawer-card-text">Monitor assigned merchant stores and view details by clicking on the merchant name link.</p>
            </div>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-wallet text-primary mr-2"></i> Balance Audit</div>
                <p class="drawer-card-text">Audit the merchant's Total Balance versus Hold Balance (funds locked or reserved due to pending disputes/verification).</p>
            </div>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-key text-primary mr-2"></i> OpenAPI & Account Status</div>
                <p class="drawer-card-text">Track status for both overall account registration and OpenAPI credentials (Active, Pending, Blocked, or Frozen).</p>
            </div>
        </div>
    </div>

    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">

        <!-- ── Toolbar ── -->
        <form id="spv_merchant_search_form" method="post" action="<?= base_url('merchant/manage/list/' . $supervisor_id); ?>">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <div class="dt-toolbar py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div class="dt-toolbar-left flex-grow-1" style="min-width: 280px;">
                    <div class="dt-search-wrapper">
                        <i class="fas fa-search dt-search-icon"></i>
                        <input type="text" id="dt-global-search" class="dt-search-input" placeholder="Search by username, name and email...">
                    </div>
                </div>
                <div class="dt-toolbar-right d-flex align-items-center gap-2">
                    <!-- More Filters Trigger -->
                    <div class="dt-filter-group dt-more-filters-wrapper">
                        <button type="button" id="spvMoreFiltersBtn" class="dt-more-filters-btn <?= (!empty($this->session->userdata('search_spv_merchant_status')) || !empty($this->session->userdata('search_spv_merchant_openapi_status')) || !empty($this->session->userdata('search_spv_merchant_date_from')) || !empty($this->session->userdata('search_spv_merchant_date_to'))) ? 'dt-more-filters-active' : ''; ?>">
                            <i class="fas fa-sliders-h mr-1 mr-2"></i> Filters
                            <?php 
                                $extra_active = 0;
                                if (!empty($this->session->userdata('search_spv_merchant_status'))) $extra_active++;
                                if (!empty($this->session->userdata('search_spv_merchant_openapi_status'))) $extra_active++;
                                if (!empty($this->session->userdata('search_spv_merchant_date_from')) || !empty($this->session->userdata('search_spv_merchant_date_to'))) $extra_active++;
                                if ($extra_active > 0): 
                            ?>
                                <span class="dt-more-badge"><?= $extra_active; ?></span>
                            <?php endif; ?>
                            <i class="fas fa-chevron-down ml-1 dt-more-arrow"></i>
                        </button>

                        <!-- Dropdown Panel -->
                        <div class="dt-more-panel" id="spvMoreFiltersPanel">
                            <div class="dt-more-panel-header">
                                <span class="dt-more-panel-title"><i class="fas fa-filter mr-1 mr-2"></i> Advanced Filters</span>
                                <a href="<?= base_url('merchant/manage/list/reset/' . $supervisor_id); ?>" class="dt-more-clear">Clear All</a>
                            </div>

                            <div class="dt-more-panel-body">
                                <!-- Registration Date Range -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-calendar-alt mr-1 mr-2"></i> Registration Date</label>
                                    <div class="dt-filter-chip">
                                        <input type="date" name="search_spv_merchant_date_from" class="dt-chip-input" value="<?= $this->session->userdata('search_spv_merchant_date_from'); ?>" title="Date From">
                                        <span class="text-muted mx-1" style="font-size:11px;">→</span>
                                        <input type="date" name="search_spv_merchant_date_to" class="dt-chip-input" value="<?= $this->session->userdata('search_spv_merchant_date_to'); ?>" title="Date To">
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-info-circle mr-1 mr-2"></i> Account Status</label>
                                    <select name="search_spv_merchant_status" class="dt-more-select">
                                        <option value="">All Account Statuses</option>
                                        <option value="Pending" <?= ($this->session->userdata('search_spv_merchant_status') == 'Pending') ? 'selected' : ''; ?>>Pending Approval</option>
                                        <option value="Active" <?= ($this->session->userdata('search_spv_merchant_status') == 'Active') ? 'selected' : ''; ?>>Active</option>
                                        <option value="Blocked" <?= ($this->session->userdata('search_spv_merchant_status') == 'Blocked') ? 'selected' : ''; ?>>Blocked</option>
                                        <option value="Freeze" <?= ($this->session->userdata('search_spv_merchant_status') == 'Freeze') ? 'selected' : ''; ?>>Frozen</option>
                                    </select>
                                </div>
                                
                                <!-- OpenAPI Status -->
                                <div class="dt-more-field">
                                    <label class="dt-more-label"><i class="fas fa-plug mr-1 mr-2"></i> OpenAPI Status</label>
                                    <select name="search_spv_merchant_openapi_status" class="dt-more-select">
                                        <option value="">All OpenAPI Statuses</option>
                                        <option value="Pending" <?= ($this->session->userdata('search_spv_merchant_openapi_status') == 'Pending') ? 'selected' : ''; ?>>Pending Approval</option>
                                        <option value="Active" <?= ($this->session->userdata('search_spv_merchant_openapi_status') == 'Active') ? 'selected' : ''; ?>>Active Access</option>
                                        <option value="Not Active" <?= ($this->session->userdata('search_spv_merchant_openapi_status') == 'Not Active') ? 'selected' : ''; ?>>Deactivated</option>
                                        <option value="Blocked" <?= ($this->session->userdata('search_spv_merchant_openapi_status') == 'Blocked') ? 'selected' : ''; ?>>Blocked</option>
                                        <option value="Freeze" <?= ($this->session->userdata('search_spv_merchant_openapi_status') == 'Freeze') ? 'selected' : ''; ?>>Account Frozen</option>
                                    </select>
                                </div>
                            </div>

                            <div class="dt-more-panel-footer">
                                <button type="submit" name="submit" class="btn-dt-apply btn-dt-action-primary shadow-sm">
                                    <i class="fas fa-check mr-1 mr-2"></i> APPLY FILTER
                                </button>
                                <button type="button" id="spvMoreFiltersClose" class="btn-dt-cancel btn-dt-secondary">
                                    CANCEL
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- ── Table ── -->
        <div class="table-responsive">
            <table id="supervisorMerchantTable" class="table dt-table mb-0" style="width:100%">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">NO</th>
                        <th>MERCHANT NAME</th>
                        <th>TOTAL BALANCE</th>
                        <th>HOLD BALANCE</th>
                        <th>OPENAPI</th>
                        <th>STATUS</th>
                        <th>REGISTRATION DATE</th>
                    </tr>
                </thead>
                <tbody id="supervisorMerchantTableBody">
                    <!-- Data will be loaded via AJAX -->
                </tbody>
            </table>
        </div>

        <!-- Footer Actions -->
        <div class="card-footer bg-white border-top-0 p-4 pt-0">
             <!-- Any bottom buttons could go here -->
        </div>
    </div><!-- /.dt-card -->

</div><!-- /.container-fluid -->

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

    const supervisorId = "<?= $supervisor_id ?>";
    const table = initServerDataTable("#supervisorMerchantTable", "<?= base_url('merchant/manage/list/') ?>" + supervisorId, [
            { "data": "no", "className": "text-center" },
            { 
                "data": "c_name", 
                "className": "font-weight-bold text-nowrap",
                "render": function(data, type, row) {
                    return `<a href="<?= base_url('merchant/manage/detail/') ?>${row.id}" class="text-primary text-decoration-none">${data}</a>`;
                }
            },
            { 
                "data": "c_balanceTotal", 
                "className": "font-weight-bold text-primary text-nowrap",
                "render": function(data) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(data);
                }
            },
            { 
                "data": "c_balanceHold", 
                "className": "text-muted small text-nowrap",
                "render": function(data) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(data);
                }
            },
            { 
                "data": "c_openapiStatus",
                "render": function(data) {
                    let style = 'secondary';
                    if (data === 'Active') style = 'success';
                    else if (data === 'Pending') style = 'warning';
                    else if (data === 'Blocked') style = 'danger';
                    else if (data === 'Freeze') style = 'info';
                    return `<span class="badge badge-${style}-soft text-${style} px-2 py-1">${data}</span>`;
                }
            },
            { 
                "data": "c_status",
                "render": function(data) {
                    let style = 'secondary';
                    if (data === 'Active') style = 'success';
                    else if (data === 'Pending') style = 'warning';
                    else if (data === 'Blocked') style = 'danger';
                    else if (data === 'Freeze') style = 'info';
                    return `<span class="badge badge-${style}-soft text-${style} px-2 py-1">${data}</span>`;
                }
            },
            {
                "data": "c_dateCreated",
                "className": "text-center text-nowrap",
                "render": function(data) {
                    if (!data) return '-';
                    var d = new Date(data);
                    if (isNaN(d)) return data;
                    var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    var day = ('0' + d.getDate()).slice(-2);
                    var month = months[d.getMonth()];
                    var year = d.getFullYear();
                    return '<span class="fw-bold text-dark">' + day + ' ' + month + ' ' + year + '</span>';
                }
            }
        ], {
        order: [[6, 'desc']], // Sort by Registration Date
        language: {
            "info": "Showing _START_ – _END_ of _TOTAL_ entries",
            "infoEmpty": "No entries to show",
            "zeroRecords": '<div class="text-center py-4 text-muted"><i class="fas fa-inbox fa-2x mb-2 d-block mr-2"></i> No merchants assigned to this supervisor.</div>'
        }
    });

    // Global Search — dengan debounce 400ms agar tidak spam AJAX ke server
    $('#dt-global-search').on('input', debounce(function() {
        table.search(this.value).draw();
    }, 400));

    // Update badge "Merchants Found" secara dinamis dari response server
    table.on('xhr.dt', function(e, settings, json) {
        if (json && json.recordsFiltered !== undefined) {
            var count = json.recordsFiltered;
            var label = count === 1 ? 'Merchant Found' : 'Merchants Found';
            $('.badge.bg-white.text-primary').html('<i class="fas fa-store mr-1"></i> ' + count + ' ' + label);
        }
    });

    // More Filters dropdown
    var $moreBtn   = $('#spvMoreFiltersBtn');
    var $morePanel = $('#spvMoreFiltersPanel');
    var $moreClose = $('#spvMoreFiltersClose');

    $moreBtn.on('click', function(e) {
        e.stopPropagation();
        var isOpen = $morePanel.hasClass('dt-panel-open');
        if (isOpen) {
            $morePanel.removeClass('dt-panel-open');
            $moreBtn.removeClass('dt-more-filters-open');
        } else {
            $morePanel.addClass('dt-panel-open');
            $moreBtn.addClass('dt-more-filters-open');
        }
    });

    $moreClose.on('click', function(e) {
        e.stopPropagation();
        $morePanel.removeClass('dt-panel-open');
        $moreBtn.removeClass('dt-more-filters-open');
    });

    $morePanel.on('click', function(e) {
        e.stopPropagation(); // Prevent closing when clicking inside
    });

    $(document).on('click', function() {
        $morePanel.removeClass('dt-panel-open');
        $moreBtn.removeClass('dt-more-filters-open');
    });

    // Select2 for ALL selects inside the More Filters panel
    $('#spvMoreFiltersPanel select').not('.select2-hidden-accessible').select2({
        width: '100%',
        dropdownAutoWidth: true,
        dropdownParent: $(this).parent(),
        minimumResultsForSearch: 0
    });
});
</script>



