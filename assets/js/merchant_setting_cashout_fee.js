/**
 * Merchant Cashout Fee Settings Table & Modal Controls
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

    const merchantId = window.CURRENT_MERCHANT_ID || '';
    const listUrl = window.BASE_URL + 'merchant/setting-cashout-fee/' + merchantId;
    const csrfName = $('meta[name="csrf-token-name"]').attr('content') || 'csrf_token_name';
    const csrfHash = $('meta[name="csrf-token-hash"]').attr('content') || '';

    var table = initServerDataTable("#cashoutFeeTable", listUrl, [
        { data: "no", className: "ps-4 text-muted small" },
        { 
            data: "c_cashoutChannelGroup",
            render: function(data, type, row) {
                return `
                    <div class="d-flex flex-column">
                        <span class="fw-bold text-dark">${data}</span>
                        <span class="text-muted small">Channel: <code class="text-primary">${row.ref_cashoutChannelId}</code></span>
                        <span class="text-muted smaller">Provider: ${row.c_externalIdDefault}</span>
                    </div>
                `;
            }
        },
        { 
            data: "c_feeType",
            render: function(data, type, row) {
                const fee = new Intl.NumberFormat('id-ID').format(row.c_fee);
                return `
                    <div class="d-flex flex-column">
                        <div class="d-flex justify-content-between small"><span class="text-muted">Type:</span><span class="badge badge-light text-dark px-2 py-0">${data}</span></div>
                        <div class="d-flex justify-content-between fw-bold mt-1"><span class="text-muted small">Fixed:</span><span class="text-primary">Rp ${fee}</span></div>
                        <div class="d-flex justify-content-between small"><span class="text-muted">Percentage:</span><span class="text-success fw-bold">${row.c_feePercetange}%</span></div>
                    </div>
                `;
            }
        },
        { 
            data: "c_settlementInterval",
            className: "text-center",
            render: data => `<span class="badge bg-info-soft text-info rounded-pill px-3 py-1">${data} Days</span>`
        },
        { 
            data: "c_amountMin",
            className: "text-right",
            render: function(data, type, row) {
                const min = new Intl.NumberFormat('id-ID').format(data);
                const max = new Intl.NumberFormat('id-ID').format(row.c_amountMax);
                return `<div class="d-flex flex-column align-items-end"><small class="text-muted">Min: Rp ${min}</small><small class="text-muted">Max: Rp ${max}</small></div>`;
            }
        },
        { 
            data: "c_status",
            className: "text-center",
            render: function(data) {
                const isAct = (data === 'Active');
                return `<span class="badge ${isAct ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger'} rounded-pill px-3 py-1">${data}</span>`;
            }
        },
        { 
            data: "actions",
            className: "text-center pe-4",
            orderable: false,
            render: function(data, type, row) {
                return `
                    <div class="d-flex justify-content-center gap-1">
                        <button type="button" class="btn-dt-action-icon btn-dt-action-edit edit-fee-btn" data-row='${JSON.stringify(row)}' title="Edit Fee"><i class="fas fa-pen fa-xs"></i></button>
                        <button type="button" class="btn-dt-action-icon btn-dt-action-delete delete-fee-btn" data-href="${window.BASE_URL}merchant/setting-cashout-fee/delete/${merchantId}/${row.id}" title="Delete Fee"><i class="fas fa-trash fa-xs"></i></button>
                    </div>
                `;
            }
        }
    ], {
        ajax: {
            url: listUrl,
            type: "POST",
            data: function (d) {
                if (csrfName && csrfHash) d[csrfName] = csrfHash;
                d.channel_group = $('#filter_channel_group').val();
                d.channel_id = $('#filter_channel_id').val();
                d.provider = $('#filter_provider').val();
                d.status = $('#filter_status').val();
                d.search_channel = $('#dt-search').val() || '';
            }
        }
    });

    $('#dt-search').on('input', debounce(function() { table.search(this.value).draw(); }, 400));

    // More Filters Panel Handlers
    const $moreBtn = $('#feeMoreFiltersBtn');
    const $morePanel = $('#feeMoreFiltersPanel');

    $moreBtn.on('click', function(e) {
        e.stopPropagation();
        const isOpen = $morePanel.hasClass('dt-panel-open');
        $morePanel.toggleClass('dt-panel-open', !isOpen);
        $moreBtn.toggleClass('dt-open', !isOpen);
    });

    $('#feeMoreApply').on('click', function() {
        let count = 0;
        $('.filter-select').each(function() { if ($(this).val()) count++; });
        $('#feeFilterBadge').text(count).toggle(count > 0);
        $moreBtn.toggleClass('dt-more-filters-active', count > 0);
        table.ajax.reload(null, false);
        $morePanel.removeClass('dt-panel-open');
        $moreBtn.removeClass('dt-open');
    });

    $('#feeMoreClear').on('click', function() {
        $('.filter-select').val('').trigger('change.select2');
        $('#feeFilterBadge').hide();
        $moreBtn.removeClass('dt-more-filters-active');
        table.ajax.reload(null, false);
    });

    $(document).on('click', '.delete-fee-btn', function(e) {
        e.preventDefault();
        const url = $(this).data('href');
        Swal.fire({
            title: 'Are you sure?',
            text: "This fee setting will be permanently deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) window.location.href = url;
        });
    });

    // Cascading options in modal
    $('#c_cashoutChannelGroup, #c_externalIdDefault').on('change', function() {
        const group = $('#c_cashoutChannelGroup').val();
        const ext = $('#c_externalIdDefault').val();
        if (group && ext) {
            $.ajax({
                url: window.BASE_URL + "external/cashout/get-filter-options",
                type: "POST",
                data: { group: group, external_id: ext, [csrfName]: csrfHash },
                dataType: "json",
                success: function(data) {
                    let opts = '<option value="" disabled selected>Select channel ID</option>';
                    data.channels.forEach(function(item) { opts += `<option value="${item}">${item}</option>`; });
                    $('#ref_cashoutChannelId').html(opts).prop('disabled', false);
                }
            });
        }
    });

    $(document).on('click', '.edit-fee-btn', function() {
        const row = $(this).data('row');
        $('#feeModalTitle').text('Edit Cashout Fee Setting');
        $('#feeForm').attr('action', window.BASE_URL + 'merchant/setting-cashout-fee/update/' + row.id);
        $('#c_cashoutChannelGroup').val(row.c_cashoutChannelGroup).trigger('change');
        $('#c_externalIdDefault').val(row.c_externalIdDefault).trigger('change');
        setTimeout(() => {
            $('#ref_cashoutChannelId').val(row.ref_cashoutChannelId).trigger('change');
        }, 300);
        $('#c_feeType').val(row.c_feeType).trigger('change');
        $('#c_fee').val(row.c_fee);
        $('#c_feePercetange').val(row.c_feePercetange);
        $('#c_settlementInterval').val(row.c_settlementInterval);
        $('#c_amountMin').val(row.c_amountMin);
        $('#c_amountMax').val(row.c_amountMax);
        $('#c_status').val(row.c_status).trigger('change');
        $('#feeModal').modal('show');
    });

    $('.add-btn').on('click', function() {
        $('#feeModalTitle').text('Add Cashout Fee Setting');
        $('#feeForm').attr('action', window.BASE_URL + 'merchant/setting-cashout-fee/create');
        $('#feeForm')[0].reset();
        $('#feeForm .select2').val('').trigger('change');
    });
});
