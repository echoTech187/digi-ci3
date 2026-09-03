/**
 * Merchant Management Table & Action Modals (Credit, Debit, Delegate)
 */
$(document).ready(function() {
    'use strict';

    $('#toggleGuideBtn').on('click', function() {
        $('#instructionDrawer, #instructionOverlay').addClass('open');
        $('body').css('overflow', 'hidden');
    });

    $('#closeDrawerBtn, #instructionOverlay').on('click', function() {
        $('#instructionDrawer, #instructionOverlay').removeClass('open');
        $('body').css('overflow', '');
    });

    const ajaxUrl = window.BASE_URL + 'merchant/manage';
    const csrfName = $('meta[name="csrf-token-name"]').attr('content') || 'csrf_token_name';
    const csrfHash = $('meta[name="csrf-token-hash"]').attr('content') || '';

    var table = initServerDataTable("#merchantTable", ajaxUrl, [
        { "data": "no", "orderable": false, "className": "ps-4 text-muted small" },
        { 
            "data": "id",
            "className": "text-left text-nowrap",
            "render": data => '<span class="fw-bold text-dark">#' + data + '</span>'
        },
        { 
            "data": "c_name",
            "className": "text-left text-nowrap",
            "render": function(data, type, row) {
                return `<div class="d-flex flex-column"><a href="${window.BASE_URL}merchant/manage/detail/${row.id}" class="fw-bold text-primary text-decoration-none">${data}</a><span class="text-muted small">${row.c_email}</span></div>`;
            }
        },
        { 
            "data": "c_balanceTotal",
            "orderable": false,
            "render": function(data, type, row) {
                var total = parseFloat(data);
                var hold = parseFloat(row.c_balanceHold);
                var available = total - hold;
                return `
                    <div class="d-flex flex-column" style="min-width: 150px;">
                        <div class="d-flex justify-content-between small mb-1"><span class="text-muted">Total:</span><span class="fw-bold text-dark">Rp ${number_format(total, 0, ',', '.')}</span></div>
                        <div class="d-flex justify-content-between small mb-1"><span class="text-muted">Hold:</span><span class="text-warning fw-bold">Rp ${number_format(hold, 0, ',', '.')}</span></div>
                        <div class="d-flex justify-content-between small border-top pt-1 mt-1"><span class="text-muted">Available:</span><span class="text-success fw-bold">Rp ${number_format(available, 0, ',', '.')}</span></div>
                    </div>
                `;
            }
        },
        { 
            "data": "c_dateCreated", 
            "orderable": true,
            "className": "text-center text-nowrap",
            "render": function(data) {
                if (!data) return '-';
                var d = new Date(data);
                if (isNaN(d)) return data;
                var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                return '<span class="fw-bold text-dark">' + ('0' + d.getDate()).slice(-2) + ' ' + months[d.getMonth()] + ' ' + d.getFullYear() + '</span>';
            }
        },
        { 
            "data": "c_status",
            "orderable": false,
            "render": function(data) {
                var status_bg = 'bg-secondary-soft', status_text = 'text-secondary';
                if (data === 'Active') { status_bg = 'bg-success-soft'; status_text = 'text-success'; }
                else if (data === 'Pending') { status_bg = 'bg-warning-soft'; status_text = 'text-warning'; }
                else if (data === 'Blocked') { status_bg = 'bg-danger-soft'; status_text = 'text-danger'; }
                return `<span class="badge ${status_bg} ${status_text} rounded-pill px-3 py-1 font-weight-bold" style="font-size: 11px;">${data}</span>`;
            }
        },
        { 
            "data": "c_openapiStatus",
            "orderable": false,
            "render": function(data) {
                var is_act = (data === 'Active');
                return `<span class="badge ${is_act ? 'bg-success-soft text-success' : 'bg-secondary-soft text-secondary'} rounded-pill px-3 py-1 font-weight-bold" style="font-size: 11px;">${data || 'Inactive'}</span>`;
            }
        },
        { 
            "data": "actions",
            "orderable": false,
            "className": "text-center pe-4",
            "render": function(data, type, row) {
                const bUrl = window.BASE_URL;
                return `
                    <div class="dropdown">
                        <button class="btn-dt-action-icon btn-dt-action-more dropdown-toggle shadow-none" type="button" data-toggle="dropdown" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                        <ul class="dropdown-menu dropdown-menu-right shadow-sm border-0 py-2" style="border-radius: 12px; min-width: 200px;">
                            <li><a class="dropdown-item py-2 px-3 small font-weight-bold text-dark" href="${bUrl}merchant/manage/detail/${row.id}"><i class="fas fa-chart-line text-primary mr-2" style="width: 16px;"></i> Detail Dashboard</a></li>
                            <li><a class="dropdown-item py-2 px-3 small font-weight-bold text-dark" href="${bUrl}merchant/manage/edit/${row.id}"><i class="fas fa-edit text-warning mr-2" style="width: 16px;"></i> Edit Profile</a></li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li><a class="dropdown-item py-2 px-3 small font-weight-bold text-success btn-credit-balance" href="javascript:void(0)" data-id="${row.id}" data-name="${row.c_name}"><i class="fas fa-plus-circle mr-2" style="width: 16px;"></i> Credit (Add Balance)</a></li>
                            <li><a class="dropdown-item py-2 px-3 small font-weight-bold text-danger btn-debit-balance" href="javascript:void(0)" data-id="${row.id}" data-name="${row.c_name}"><i class="fas fa-minus-circle mr-2" style="width: 16px;"></i> Debit (Deduct Balance)</a></li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li><a class="dropdown-item py-2 px-3 small font-weight-bold text-dark" href="${bUrl}merchant/sub-account/${row.id}"><i class="fas fa-users text-info mr-2" style="width: 16px;"></i> Sub Accounts</a></li>
                            <li><a class="dropdown-item py-2 px-3 small font-weight-bold text-dark" href="${bUrl}finance/mutation/${row.id}"><i class="fas fa-history text-secondary mr-2" style="width: 16px;"></i> Mutation Log</a></li>
                            <li><a class="dropdown-item py-2 px-3 small font-weight-bold text-dark" href="${bUrl}merchant/setting-cashin-fee/${row.id}"><i class="fas fa-cog text-primary mr-2" style="width: 16px;"></i> Cashin Fee Settings</a></li>
                            <li><a class="dropdown-item py-2 px-3 small font-weight-bold text-dark" href="${bUrl}merchant/setting-cashout-fee/${row.id}"><i class="fas fa-cog text-warning mr-2" style="width: 16px;"></i> Cashout Fee Settings</a></li>
                            <li><a class="dropdown-item py-2 px-3 small font-weight-bold text-purple btn-delegate-access" href="javascript:void(0)" data-id="${row.id}" data-name="${row.c_name}"><i class="fas fa-user-shield text-purple mr-2" style="width: 16px;"></i> Delegate Access</a></li>
                        </ul>
                    </div>
                `;
            }
        }
    ]);

    $('#merchantGlobalSearch').on('input', debounce(function() { table.search(this.value).draw(); }, 400));

    // More Filters Panel Handler
    const $moreBtn = $('#merchantMoreFiltersBtn');
    const $morePanel = $('#merchantMoreFiltersPanel');

    $moreBtn.on('click', function(e) {
        e.stopPropagation();
        const isOpen = $morePanel.hasClass('dt-panel-open');
        $morePanel.toggleClass('dt-panel-open', !isOpen);
        $moreBtn.toggleClass('dt-open', !isOpen);
    });

    $('#merchantMoreFiltersClose').on('click', function() {
        $morePanel.removeClass('dt-panel-open');
        $moreBtn.removeClass('dt-open');
    });

    $(document).on('click', '.btn-credit-balance', function() {
        $('#merchantId').val($(this).data('id'));
        $('#merchantName').val($(this).data('name'));
        $('#creditDescription').val('');
        $('#amountCredit, #rawAmountCredit').val('');
        $('#creditBalanceModal').modal('show');
    });

    $(document).on('click', '.btn-debit-balance', function() {
        $('#merchantIdDebit').val($(this).data('id'));
        $('#merchantNameDebit').val($(this).data('name'));
        $('#debitDescription').val('');
        $('#amountDebit, #rawAmountDebit').val('');
        $('#debitBalanceModal').modal('show');
    });

    $('#creditBalanceForm').on('submit', function(e) {
        e.preventDefault();
        const amt = $('#amountCredit').val().replace(/\D/g, '');
        $('#rawAmountCredit').val(amt);
        $.ajax({
            url: window.BASE_URL + "merchant/balance/credit",
            type: "POST",
            data: $(this).serialize() + "&" + csrfName + "=" + csrfHash,
            dataType: "json",
            success: function(res) {
                if (res.status) {
                    $('#creditBalanceModal').modal('hide');
                    Swal.fire({ title: 'Success!', text: res.message, icon: 'success' }).then(() => table.ajax.reload(null, false));
                } else {
                    Swal.fire({ title: 'Error!', text: res.message, icon: 'error' });
                }
            }
        });
    });

    $('#debitBalanceForm').on('submit', function(e) {
        e.preventDefault();
        const amt = $('#amountDebit').val().replace(/\D/g, '');
        $('#rawAmountDebit').val(amt);
        $.ajax({
            url: window.BASE_URL + "merchant/balance/debit",
            type: "POST",
            data: $(this).serialize() + "&" + csrfName + "=" + csrfHash,
            dataType: "json",
            success: function(res) {
                if (res.status) {
                    $('#debitBalanceModal').modal('hide');
                    Swal.fire({ title: 'Success!', text: res.message, icon: 'success' }).then(() => table.ajax.reload(null, false));
                } else {
                    Swal.fire({ title: 'Error!', text: res.message, icon: 'error' });
                }
            }
        });
    });

    // Delegate Access Handlers
    $(document).on('click', '.btn-delegate-access', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        $('#delegateMerchantId').val(id);
        $('#delegateMerchantName').text(name);
        $('#permissionsLoader').show();
        $('#permissionsList').find('.permission-group, .alert').remove();
        $('#delegateModal').modal('show');

        $.ajax({
            url: window.BASE_URL + 'merchant/manage/get-delegated-permissions/' + id,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                $('#permissionsLoader').hide();
                if (res.status && res.permissions) {
                    renderPermissions(res.permissions);
                } else {
                    $('#permissionsList').append('<div class="alert alert-info py-2 small mb-0">No delegatable permissions available.</div>');
                }
            },
            error: function() {
                $('#permissionsLoader').hide();
                $('#permissionsList').append('<div class="alert alert-danger py-2 small mb-0">Failed to load permissions.</div>');
            }
        });
    });

    function renderPermissions(perms) {
        let html = '';
        Object.keys(perms).forEach(group => {
            html += `<div class="permission-group mb-3 pb-2 border-bottom"><h6 class="font-weight-bold text-primary mb-2 small text-uppercase">${group}</h6><div class="row">`;
            perms[group].forEach(p => {
                const checked = p.is_granted ? 'checked' : '';
                html += `
                    <div class="col-md-6 mb-2">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="perm_${p.id}" name="permissions[]" value="${p.id}" ${checked}>
                            <label class="custom-control-label small font-weight-bold text-dark" for="perm_${p.id}">${p.name}</label>
                            <small class="d-block text-muted" style="font-size: 11px;">${p.description || ''}</small>
                        </div>
                    </div>
                `;
            });
            html += `</div></div>`;
        });
        $('#permissionsList').append(html);
    }

    $('#delegateForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: window.BASE_URL + 'merchant/manage/save-delegated-permissions',
            type: 'POST',
            data: $(this).serialize() + "&" + csrfName + "=" + csrfHash,
            dataType: 'json',
            success: function(res) {
                if (res.status) {
                    $('#delegateModal').modal('hide');
                    Swal.fire({ title: 'Success!', text: 'Permissions successfully delegated.', icon: 'success' });
                } else {
                    Swal.fire({ title: 'Error!', text: res.message || 'Failed to save permissions.', icon: 'error' });
                }
            }
        });
    });
});
