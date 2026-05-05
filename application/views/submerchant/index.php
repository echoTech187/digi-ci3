<?php
$id = $this->uri->segment(3);
?>

<div>
    <!-- ── Page Header ── -->
    <div class="dt-page-header">
        <div>
            <h4 class="dt-page-title">Sub Accounts Management</h4>
            <p class="dt-page-subtitle">Managing sub accounts for <strong><?= $merchant[0]->c_name ?></strong></p>
        </div>
    </div>

    <!-- Alerts -->
    <!-- Alerts Standardized to Swal2 Premium -->
    <script>
        $(document).ready(function() {
            <?php if ($this->session->flashdata('success')) : ?>
                Swal.fire({
                    title: 'Success!',
                    text: '<?= $this->session->flashdata('success'); ?>',
                    icon: 'success',
                    customClass: {
                        popup: 'swal2-premium-popup',
                        confirmButton: 'swal2-premium-confirm'
                    },
                    buttonsStyling: false
                });
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')) : ?>
                Swal.fire({
                    title: 'Error!',
                    html: '<?= trim(str_replace(["\r", "\n"], '', $this->session->flashdata('error'))); ?>',
                    icon: 'error',
                    customClass: {
                        popup: 'swal2-premium-popup',
                        confirmButton: 'swal2-premium-confirm'
                    },
                    buttonsStyling: false
                });
            <?php endif; ?>
        });
    </script>

    <!-- ── Main Data Card ── -->
    <div class="card border-0 shadow-sm dt-card">
        <!-- ── Toolbar ── -->
        <div class="dt-toolbar">
            <!-- LEFT: Global Search -->
            <div class="dt-search-wrapper">
                <i class="fas fa-search dt-search-icon"></i>
                <input type="text" id="dt-search" class="dt-search-input" placeholder="Search by name, ID, or email...">
            </div>

            <!-- RIGHT: Actions -->
            <div class="dt-toolbar-filters">
                <div class="dt-filter-group">
                    <label class="dt-filter-label">&nbsp;</label>
                    <button type="button" class="btn-dt-chip-action btn-dt-secondary" onclick="location.reload()">
                        <i class="fas fa-sync-alt mr-1 mr-2"></i> Refresh
                    </button>
                </div>
            </div>
        </div>

        <!-- ── Table ── -->
        <div class="table-responsive">
            <table id="submerchantTable" class="table dt-table mb-0" style="width:100%">
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

        <!-- Pagination/Info handled via JS container in footer -->
         <div class="dt-footer" id="dt-footer-container"></div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize Server-side DataTable
    var table = initServerDataTable("#submerchantTable", "<?= base_url('admin/submerchant/'.$id) ?>", [
        { data: 'no', orderable: false },
        { 
            data: 'c_name', 
            className: 'font-weight-bold text-gray-800',
            render: function(data, type, row) {
                return '<div>' + data + '</div><small class="text-muted">ID: ' + row.id + '</small>';
            }
        },
        { data: 'c_email' },
        { 
            data: 'c_status', 
            className: 'text-center',
            render: function(data, type, row) {
                var status_class = (data == 'Active') ? 'success' : 'secondary';
                return '<span class="badge badge-' + status_class + '">' + data + '</span>';
            }
        },
        { 
            data: 'id', 
            className: 'text-center', 
            orderable: false,
            render: function(data, type, row) {
                var baseUrl = "<?= base_url() ?>";
                return `
                    <div class="dropdown">
                        <button class="btn btn-sm text-muted rounded-circle p-2 border-0 bg-transparent" type="button" data-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-2">
                            <li>
                                <a class="dropdown-item py-2" href="${baseUrl}admin/submerchant/${data}">
                                    <i class="fas fa-users mr-2 text-success"></i>Sub Accounts
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <button type="button" class="dropdown-item py-2 edit-sub-btn" 
                                    data-toggle="modal" data-target="#subMerchantModal"
                                    data-id="${data}"
                                    data-name="${row.c_name}"
                                    data-email="${row.c_email}"
                                    data-merchantid="${row.parent_merchant_id}"
                                    data-businessname="${row.c_gvconnectBusinessName}"
                                    data-businessid="${row.c_gvconnectBusinessId}"
                                    data-key="${row.c_gvconnectGVConnectKey}"
                                    data-qris="${row.c_gvconnectStaticQrisRaw}"
                                    data-bni="${row.c_gvconnectStaticVaBni}"
                                    data-bca="${row.c_gvconnectStaticVaBca}"
                                    data-cimb="${row.c_gvconnectStaticVaCimb}"
                                    data-permata="${row.c_gvconnectStaticVaPermata}"
                                    data-status="${row.c_status}">
                                    <i class="fas fa-edit mr-2 text-info"></i>Edit Details
                                </button>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="${baseUrl}admin/mutation/${data}">
                                    <i class="fas fa-exchange-alt mr-2 text-warning"></i>Mutations
                                </a>
                            </li>
                        </ul>
                    </div>
                `;
            }
        }
    ]);

    // Apply Global Search filter if search_val exists in URL
    const urlParams = new URLSearchParams(window.location.search);
    const searchVal = urlParams.get('search_val');
    if (searchVal) {
        setTimeout(() => {
            table.search(searchVal).draw();
            $('#dt-search').val(searchVal);
        }, 500);
    }

    // Global search with Debounce
    $('#dt-search').on('input', debounce(function() {
        table.search(this.value).draw();
    }, 400));
});
</script>
