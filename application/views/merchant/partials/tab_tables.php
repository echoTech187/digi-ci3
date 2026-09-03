<!-- ── TAB 2: TRANSACTION HISTORY ── -->
<div class="tab-pane fade p-4" id="nav-history" role="tabpanel" aria-labelledby="history-tab">
    <div class="table-responsive">
        <table class="table dt-table mb-0" id="detailHistoryTable" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Merchant</th>
                    <th>Date Time</th>
                    <th>Product ID</th>
                    <th>Invoice No</th>
                    <th>Customer No</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- ── TAB 3: MUTATION LOG ── -->
<div class="tab-pane fade p-4" id="nav-mutation" role="tabpanel" aria-labelledby="mutation-tab">
    <!-- Global Search Bar -->
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div class="dt-search-wrapper flex-grow-1" style="max-width: 420px; position: relative;">
            <i class="fas fa-search dt-search-icon" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 13px;"></i>
            <input type="text" id="mutation-dt-search" class="dt-search-input form-control form-control-sm border-0 shadow-sm" placeholder="Search by ID, invoice, description, channel, position..." style="border-radius: 10px; padding-left: 36px; height: 38px; font-size: 12.5px; background: rgba(248, 250, 252, 0.95); border: 1px solid rgba(226, 232, 240, 0.8) !important;">
        </div>
        <small id="mutation-filter-status" class="text-muted" style="font-size: 11px;">
            <i class="fas fa-info-circle text-primary mr-1"></i> Default showing <strong>today's mutations</strong>. Type to search all history.
        </small>
    </div>

    <div class="table-responsive">
        <table class="table dt-table mb-0" id="detailMutationTable" style="width:100%">
            <thead>
                <tr>
                    <th width="50">No</th>
                    <th>Date Time</th>
                    <th>Position</th>
                    <th>Channel</th>
                    <th>Description</th>
                    <th class="text-right">Amount</th>
                    <th class="text-right">Balance</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- ── TAB 4: SUB ACCOUNTS ── -->
<div class="tab-pane fade p-4" id="nav-submerchant" role="tabpanel" aria-labelledby="submerchant-tab">
    <div class="table-responsive">
        <table class="table dt-table mb-0" id="detailSubmerchantTable" style="width:100%">
            <thead>
                <tr>
                    <th width="50">No</th>
                    <th>Submerchant Name</th>
                    <th>Email Address</th>
                    <th>Status</th>
                    <th width="120" class="text-center">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
