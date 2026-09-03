/**
 * Cashout Channel Master Configuration
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

    const listUrl = window.BASE_URL + 'channel/cashout';
    const deleteBaseUrl = window.BASE_URL + 'channel/cashout/delete/';
    const csrfName = $('meta[name="csrf-token-name"]').attr('content') || 'csrf_token_name';
    const csrfHash = $('meta[name="csrf-token-hash"]').attr('content') || '';

    var table = initServerDataTable('#cashoutTable', listUrl, [
        { data: 'no', orderable: false, className: 'text-center' },
        { data: 'ref_cashoutChannelId', className: 'font-weight-bold text-primary dt-id-column' },
        {
            data: 'c_channelGroup',
            render: function(data) {
                return '<span class="badge badge-light text-dark border px-2 py-1 text-uppercase">' + data + '</span>';
            }
        },
        { data: 'c_description', className: 'small text-muted' },
        { data: 'c_externalIdDefault', className: 'text-dark' },
        {
            data: 'c_feeType',
            render: function(data) {
                var cls = (data.toLowerCase() === 'fixed') ? 'text-info' : 'text-purple';
                return '<span class="' + cls + ' font-weight-bold" style="font-size:11px;">' + data.toUpperCase() + '</span>';
            }
        },
        {
            data: 'c_fee',
            className: 'font-weight-bold text-dark text-nowrap',
            render: function(data) { return 'Rp ' + number_format(data, 0, ',', '.'); }
        },
        {
            data: 'c_feePercetange',
            className: 'font-weight-bold text-dark text-nowrap',
            render: function(data) { return number_format(data, 0, ',', '.') + '%'; }
        },
        {
            data: null, 
            orderable: false, 
            className: 'text-center',
            render: function(data, type, row) {
                return `
                    <div class="dropdown">
                        <button class="btn btn-sm rounded-circle p-2 border-0 bg-transparent" type="button" data-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                        <ul class="dropdown-menu dropdown-menu-right shadow border-0 py-2">
                            <li><button type="button" class="dropdown-item edit-cashout" data-toggle="modal" data-target="#editChanelModal" data-id="${row.id}" data-channelid="${row.ref_cashoutChannelId}" data-group="${row.c_channelGroup}" data-desc="${row.c_description || ''}" data-ext="${row.c_externalIdDefault || ''}" data-feetype="${row.c_feeType || ''}" data-fee="${row.c_fee || 0}" data-min="${row.c_amountMin || 10000}" data-max="${row.c_amountMax || 50000000}"><i class="fas fa-edit text-primary mr-2"></i> Edit Channel</button></li>
                            <li><button type="button" class="dropdown-item delete-cashout text-danger" data-id="${row.id}"><i class="fas fa-trash-alt mr-2"></i> Delete Channel</button></li>
                        </ul>
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
                d.external_id = $('#filter_external_id').val();
                d.search_channel = $('#cashoutGlobalSearch').val() || '';
            }
        }
    });

    $('#cashoutGlobalSearch').on('input', debounce(function() { table.search(this.value).draw(); }, 400));

    $(document).on('click', '.edit-cashout', function() {
        $('#edit_pk_id').val($(this).data('id'));
        $('#edit_id').val($(this).data('channelid'));
        $('#edit_chanelgroup').val($(this).data('group'));
        $('#edit_description').val($(this).data('desc'));
        $('#edit_externaldefault').val($(this).data('ext'));
        
        var ft = ($(this).data('feetype') || '').toLowerCase();
        if (ft === 'fixed') $('#edit_feetype').val('Fixed');
        else if (ft === 'percetange' || ft === 'percentage') $('#edit_feetype').val('Percetange');
        else if (ft === 'both') $('#edit_feetype').val('Both');
        else $('#edit_feetype').val('');
        
        $('#edit_feetype').trigger('change');
        $('#edit_fee').val(Math.floor(parseFloat($(this).data('fee'))));
        $('#edit_amountmin').val(Math.floor(parseFloat($(this).data('min'))));
        $('#edit_amountmax').val(Math.floor(parseFloat($(this).data('max'))));
    });

    $(document).on('click', '.delete-cashout', function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to delete cash-out channel " + id + ". This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) window.location.href = deleteBaseUrl + id;
        });
    });

    // More Filters Panel
    const $moreBtn = $('#cashoutMoreFiltersBtn');
    const $morePanel = $('#cashoutMoreFiltersPanel');

    $moreBtn.on('click', function(e) {
        e.stopPropagation();
        const isOpen = $morePanel.hasClass('dt-panel-open');
        $morePanel.toggleClass('dt-panel-open', !isOpen);
        $moreBtn.toggleClass('dt-open', !isOpen);
    });

    $('#cashoutMoreApply').on('click', function() {
        let count = 0;
        $('.filter-select').each(function() { if ($(this).val()) count++; });
        $('#cashoutFilterBadge').text(count).toggle(count > 0);
        $moreBtn.toggleClass('dt-more-filters-active', count > 0);
        table.ajax.reload(null, false);
        $morePanel.removeClass('dt-panel-open');
        $moreBtn.removeClass('dt-open');
    });

    $('#cashoutMoreClear').on('click', function() {
        $('.filter-select').val('').trigger('change.select2');
        $('#cashoutFilterBadge').hide();
        $moreBtn.removeClass('dt-more-filters-active');
        table.ajax.reload(null, false);
    });

    $('#filter_channel_group').on('change', function() {
        const group = $(this).val();
        $('#filter_external_id').prop('disabled', true).html('<option value="">Loading...</option>').trigger('change.select2');

        $.ajax({
            url: window.BASE_URL + "channel/get-master-filter-options",
            type: "POST",
            data: { type: 'cashout', group: group, [csrfName]: csrfHash },
            dataType: "json",
            success: function(data) {
                let opts = '<option value="">All External IDs</option>';
                data.providers.forEach(function(item) { opts += `<option value="${item}">${item}</option>`; });
                $('#filter_external_id').html(opts).prop('disabled', false).trigger('change.select2');
            },
            error: function() {
                $('#filter_external_id').prop('disabled', false).html('<option value="">All External IDs</option>').trigger('change.select2');
            }
        });
    });
});
