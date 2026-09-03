/**
 * Cashin External Merchant Channel Management
 */
$(document).ready(function() {
    'use strict';

    ExternalMappingHelper.initDrawer();
    ExternalMappingHelper.initSelect2Modals();
    ExternalMappingHelper.initSelect2FilterActions();
    ExternalMappingHelper.initDeleteButtonHandler();

    const listUrl = window.BASE_URL + 'external/cashin/list';
    const editUrl = window.BASE_URL + 'external/cashin/edit/';
    const deleteUrl = window.BASE_URL + 'external/cashin/delete/';
    const csrfName = $('meta[name="csrf-token-name"]').attr('content') || 'csrf_token_name';
    const csrfHash = $('meta[name="csrf-token-hash"]').attr('content') || '';

    // Initialize DataTable
    var table = initServerDataTable("#cashinTable", listUrl, [
        { "data": "no", "className": "ps-4 text-muted small" },
        { 
            "data": "merchant_name",
            "render": function(data, type, row) {
                return `<div class="fw-bold text-dark">${data}</div><small class="text-muted">ID: ${row.ref_merchantId}</small>`;
            }
        },
        { 
            "data": "c_cashinChannelGroup",
            "render": function(data, type, row) {
                return `
                    <div class="d-flex flex-column">
                        <span class="fw-bold text-dark">${data}</span>
                        <span class="text-muted small">Channel: <code class="text-primary">${row.ref_cashinChannelId}</code></span>
                        <span class="text-muted smaller">Provider: ${row.c_externalIdDefault}</span>
                    </div>
                `;
            }
        },
        { 
            "data": "c_feeType",
            "render": function(data, type, row) {
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
            "data": "c_settlementInterval",
            "className": "text-center",
            "render": function(data) {
                return `<span class="badge bg-info-soft text-info rounded-pill px-3 py-1">${data} Days</span>`;
            }
        },
        { 
            "data": "c_amountMin",
            "className": "text-right",
            "render": function(data, type, row) {
                const min = new Intl.NumberFormat('id-ID').format(data);
                const max = new Intl.NumberFormat('id-ID').format(row.c_amountMax);
                return `<div class="d-flex flex-column align-items-end"><small class="text-muted">Min: Rp ${min}</small><small class="text-muted">Max: Rp ${max}</small></div>`;
            }
        },
        { 
            "data": "c_status",
            "className": "text-center",
            "render": function(data) {
                const isAct = (data === 'Active');
                const badgeClass = isAct ? 'bg-success-soft text-success' : 'bg-danger-soft text-danger';
                return `<span class="badge ${badgeClass} rounded-pill px-3 py-1">${data}</span>`;
            }
        },
        { 
            "data": "actions",
            "className": "text-center pe-4",
            "orderable": false,
            "render": function(data, type, row) {
                return `
                    <div class="d-flex justify-content-center gap-1">
                        <a href="${editUrl}${row.id}" class="btn-dt-action-icon btn-dt-action-edit" title="Edit Configuration"><i class="fas fa-pen fa-xs"></i></a>
                        <button type="button" class="btn-dt-action-icon btn-dt-action-delete delete-btn" data-href="${deleteUrl}${row.id}" title="Delete Configuration"><i class="fas fa-trash fa-xs"></i></button>
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
                d.merchant_id = $('#filter_merchant').val();
                d.channel_group = $('#filter_channel_group').val();
                d.channel_id = $('#filter_channel_id').val();
                d.provider = $('#filter_provider').val();
                d.status = $('#filter_status').val();
                d.search_channel = $('#dt-search').val() || '';
            }
        }
    });

    $('#dt-search').on('input', debounce(function() { table.search(this.value).draw(); }, 400));

    // More Filters Panel Handler
    const $moreBtn = $('#cashinMoreFiltersBtn');
    const $morePanel = $('#cashinMoreFiltersPanel');

    $moreBtn.on('click', function(e) {
        e.stopPropagation();
        const isOpen = $morePanel.hasClass('dt-panel-open');
        $morePanel.toggleClass('dt-panel-open', !isOpen);
        $moreBtn.toggleClass('dt-open', !isOpen);
    });

    $('#cashinMoreApply').on('click', function() {
        let count = 0;
        $('.filter-select').each(function() { if ($(this).val()) count++; });
        $('#cashinFilterBadge').text(count).toggle(count > 0);
        $moreBtn.toggleClass('dt-more-filters-active', count > 0);
        table.ajax.reload(null, false);
        $morePanel.removeClass('dt-panel-open');
        $moreBtn.removeClass('dt-open');
    });

    $('#cashinMoreClear').on('click', function() {
        $('.filter-select').val('').trigger('change.select2');
        $('#cashinFilterBadge').hide();
        $moreBtn.removeClass('dt-more-filters-active');
        table.ajax.reload(null, false);
    });

    // Cascading Advanced Filters
    $('#filter_channel_group').on('change', function() {
        const group = $(this).val();
        $('#filter_provider').val('').trigger('change.select2');
        $('#filter_channel_id').val('').trigger('change.select2');
        fetchFilterOptions(group, '', true);
    });

    $('#filter_provider').on('change', function() {
        const group = $('#filter_channel_group').val();
        const ext = $(this).val();
        $('#filter_channel_id').val('').trigger('change.select2');
        fetchFilterOptions(group, ext, false);
    });

    function fetchFilterOptions(group, external_id, updateProvider) {
        $.ajax({
            url: window.BASE_URL + "external/cashin/get-filter-options",
            type: "POST",
            data: { group: group, external_id: external_id, [csrfName]: csrfHash },
            dataType: "json",
            success: function(data) {
                if (updateProvider) {
                    let opts = '<option value="">All External IDs</option>';
                    data.providers.forEach(function(item) { opts += `<option value="${item}">${item}</option>`; });
                    $('#filter_provider').html(opts).prop('disabled', false).trigger('change.select2');
                }
                let chanOpts = '<option value="">All Channel IDs</option>';
                data.channels.forEach(function(item) { chanOpts += `<option value="${item}">${item}</option>`; });
                $('#filter_channel_id').html(chanOpts).prop('disabled', false).trigger('change.select2');
            }
        });
    }

    // Modal Bulk Update Dynamic Cascading
    $('#global_current_group').on('change', function() {
        const group = $(this).val();
        if (!group) return;
        $.ajax({
            url: window.BASE_URL + "external/cashin/get-filter-options",
            type: "POST",
            data: { group: group, [csrfName]: csrfHash },
            dataType: "json",
            success: function(data) {
                let pOpts = '<option value="">All External IDs</option>';
                data.providers.forEach(function(item) { pOpts += `<option value="${item}">${item}</option>`; });
                $('#global_current_external').html(pOpts).prop('disabled', false).trigger('change.select2');
                let cOpts = '<option value="">All Channel IDs</option>';
                data.channels.forEach(function(item) { cOpts += `<option value="${item}">${item}</option>`; });
                $('#global_current_channel').html(cOpts).prop('disabled', false).trigger('change.select2');
            }
        });
    });

    $('#global_new_group').on('change', function() {
        const group = $(this).val();
        if (!group) return;
        $.ajax({
            url: window.BASE_URL + "external/cashin/get-filter-options",
            type: "POST",
            data: { group: group, [csrfName]: csrfHash },
            dataType: "json",
            success: function(data) {
                let pOpts = '<option value="">Don\'t Update (Keep Original)</option>';
                data.providers.forEach(function(item) { pOpts += `<option value="${item}">${item}</option>`; });
                $('#global_new_external').html(pOpts).prop('disabled', false).trigger('change.select2');
                let cOpts = '<option value="">Don\'t Update (Keep Original)</option>';
                data.channels.forEach(function(item) { cOpts += `<option value="${item}">${item}</option>`; });
                $('#global_new_channel').html(cOpts).prop('disabled', false).trigger('change.select2');
            }
        });
    });

    // Form Submit Confirmation & AJAX
    $('#globalUpdateForm').on('submit', function(e) {
        e.preventDefault();
        const $form = $(this);
        const curGroup = $('#global_current_group').val();
        const newGroup = $('#global_new_group').val();

        if (!curGroup || !newGroup) {
            Swal.fire({ icon: 'error', title: 'Missing Information', text: 'Current and New Group are required!' });
            return;
        }

        const scope = {
            type: $('input[name="update_type"]:checked').val() === 'group' ? 'Entire Group' : 'Selected Merchants',
            merchant: $('#global_merchant option:selected').map(function() { return $(this).text().split(' | ')[1]; }).get().join(', ')
        };
        const cur = {
            group: $('#global_current_group option:selected').text() || '-',
            ext: $('#global_current_external option:selected').text() || 'All External IDs',
            channel: $('#global_current_channel option:selected').text() || 'All Channel IDs',
            status: $('#global_current_status option:selected').text() || 'All Statuses'
        };
        const target = {
            group: $('#global_new_group option:selected').text() || '-',
            ext: $('#global_new_external option:selected').text() || 'Keep Original',
            channel: $('#global_new_channel option:selected').text() || 'Keep Original',
            status: $('#global_new_status option:selected').text() || 'Keep Original'
        };

        Swal.fire({
            title: 'Confirm Bulk Update?',
            html: ExternalMappingHelper.buildConfirmationHtml(scope, cur, target),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, update all!'
        }).then((res) => {
            if (res.isConfirmed) {
                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: $form.serialize(),
                    dataType: 'json',
                    success: function(resp) {
                        if (resp && resp.status === true) {
                            $('#globalUpdateModal').modal('hide');
                            Swal.fire({ icon: 'success', title: 'Success!', text: resp.message }).then(() => location.reload());
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error!', text: resp.message || 'Update failed' });
                        }
                    }
                });
            }
        });
    });
});
