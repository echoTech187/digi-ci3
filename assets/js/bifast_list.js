/**
 * BI-FAST Transactions List & External Communication Logs
 */
$(document).ready(function() {
    'use strict';

    // Instruction Drawer Toggle
    $('#toggleGuideBtn').on('click', function() {
        $('#instructionDrawer, #instructionOverlay').addClass('open');
        $('body').css('overflow', 'hidden');
    });

    $('#closeDrawerBtn, #instructionOverlay').on('click', function() {
        $('#instructionDrawer, #instructionOverlay').removeClass('open');
        $('body').css('overflow', '');
    });

    const listUrl = window.BASE_URL + 'finance/bi-fast';
    const detailBaseUrl = window.BASE_URL + 'finance/bi-fast/detail/';
    const resendBaseUrl = window.BASE_URL + 'finance/bi-fast/notification/resend/';

    var table = initServerDataTable("#bifastTable", listUrl, [
        { data: 'no', orderable: false },
        {
            data: 'c_datetime',
            className: 'text-nowrap',
            render: function(data) { return moment(data).format('DD-MM-YYYY HH:mm:ss'); }
        },
        {
            data: 'name_merchant',
            className: 'text-nowrap',
            render: function(data, type, row) { return ' [' + row.ref_merchantId + '] - ' + data; }
        },
        { data: 'c_merchantTransactionId', className: 'text-nowrap' },
        { data: 'c_invoiceNo', className: 'text-nowrap' },
        {
            data: 'ref_cashoutExternalId',
            className: 'text-nowrap',
            render: function(data) { return data ? '<span class="badge badge-primary">' + data.toUpperCase() + '</span>' : '-'; }
        },
        {
            data: 'ref_cashoutChannelId',
            className: 'text-nowrap',
            render: function(data, type, row) {
                return row.channel_description ? '<div class="font-weight-bold text-dark">' + row.channel_description + '</div><small class="text-muted">' + data + '</small>' : data;
            }
        },
        { data: 'c_accountNo', className: 'text-nowrap' },
        { data: 'c_beneficiaryAccountName', className: 'text-nowrap' },
        {
            data: 'c_amount',
            className: 'text-nowrap',
            render: function(data) { return 'Rp ' + number_format(data, 0, ',', '.'); }
        },
        {
            data: 'c_fee',
            className: 'text-nowrap',
            render: function(data) { return 'Rp ' + number_format(data, 0, ',', '.'); }
        },
        {
            data: 'c_status',
            className: 'text-nowrap',
            render: function(data) {
                var badge = (data === 'Success') ? 'badge-success' : (data === 'Failed' ? 'badge-danger' : 'badge-primary');
                return '<span class="badge badge-pill ' + badge + '">' + data + '</span>';
            }
        },
        {
            data: 'c_responseBody',
            className: 'text-nowrap',
            render: function(data) {
                if (!data) return '-';
                try {
                    var dec = typeof data === 'string' ? JSON.parse(data) : data;
                    return dec.responseMessage || dec.message || '-';
                } catch(e) { return '-'; }
            }
        },
        {
            data: 'id', 
            orderable: false, 
            searchable: false,
            render: function(data, type, row) {
                return `
                    <div class="dropdown">
                        <button class="btn btn-sm rounded-circle p-2 border-0 bg-transparent" type="button" data-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                        <ul class="dropdown-menu dropdown-menu-right border-0 shadow-lg">
                            <li><a href="${detailBaseUrl}${data}" class="dropdown-item"><i class="fas fa-eye text-primary mr-2"></i> Detail</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><button class="dropdown-item resend-notif-btn" data-href="${resendBaseUrl}${data}/${row.ref_merchantId}"><i class="fas fa-paper-plane text-warning mr-2"></i> Resend</button></li>
                            <li><a class="dropdown-item btn-info-request" href="#" data-merchanttransactionid="${row.c_merchantTransactionId}" data-ref_cashoutexternalid="${row.ref_cashoutExternalId}" data-ref_cashoutexternallogbifastid="${row.ref_cashoutExternalLogBifastId}"><i class="fas fa-info-circle text-info mr-2"></i> Info Request</a></li>
                        </ul>
                    </div>
                `;
            }
        }
    ], {
        order: [[2, 'desc']]
    });

    $('#bifastGlobalSearch').on('input', debounce(function() { table.search(this.value).draw(); }, 400));

    // More Filters Panel Handler
    const $moreBtn = $('#bifastMoreFiltersBtn');
    const $morePanel = $('#bifastMoreFiltersPanel');

    $moreBtn.on('click', function(e) {
        e.stopPropagation();
        const isOpen = $morePanel.hasClass('dt-panel-open');
        $morePanel.toggleClass('dt-panel-open', !isOpen);
        $moreBtn.toggleClass('dt-open', !isOpen);
    });

    $('#bifastMoreFiltersClose').on('click', function() {
        $morePanel.removeClass('dt-panel-open');
        $moreBtn.removeClass('dt-open');
    });

    $('.bifast-select2').each(function () {
        $(this).select2({ width: '100%', dropdownParent: $('body'), minimumResultsForSearch: 5 });
    });

    // Info Request AJAX
    $(document).on('click', '.btn-info-request', function(e) {
        e.preventDefault();
        const csrfName = $('meta[name="csrf-token-name"]').attr('content') || 'csrf_token_name';
        const csrfHash = $('meta[name="csrf-token-hash"]').attr('content') || '';
        const extId = $(this).data('ref_cashoutexternalid');
        const logId = $(this).data('ref_cashoutexternallogbifastid');

        $('#detailBiFastSubtitle').text($(this).data('merchanttransactionid'));

        $.ajax({
            url: window.BASE_URL + 'finance/bi-fast/channel/external',
            method: 'POST',
            data: { ref_cashoutExternalId: extId, ref_cashoutExternalLogBifastId: logId, [csrfName]: csrfHash },
            dataType: 'json',
            success: function(resp) {
                $('#cashoutExternalId').text(extId || '—');
                $('#TransactionIdExternal1').val(resp.TransactionIdExternal1 || '—');
                $('#TransactionIdExternal2').val(resp.TransactionIdExternal2 || '—');
                $('#RequestDatetime').val(resp.RequestDatetime || '—');
                $('#RequestHeaderCode').text(JSON.stringify(resp.RequestHeader, null, 2));
                $('#RequestBodyCode').text(JSON.stringify(resp.RequestBody, null, 2));
                $('#ResponseDatetime').val(resp.ResponseDatetime || '—');
                $('#ResponseHeaderCode').text(JSON.stringify(resp.ResponseHeader, null, 2));
                $('#ResponseBodyCode').text(JSON.stringify(resp.ResponseBody, null, 2));
                $('#detailBiFastChannelExternalModal').modal('show');
            },
            error: function() {
                Swal.fire({ title: 'Error!', text: 'Failed to load transaction details.', icon: 'error' });
            }
        });
    });

    $(document).on('click', '.resend-notif-btn', function(e) {
        e.preventDefault();
        const href = $(this).data('href');
        Swal.fire({
            title: 'Resend Notification',
            text: 'Are you sure you want to resend this notification?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, resend!'
        }).then((res) => {
            if (res.isConfirmed) window.location.href = href;
        });
    });

    $(document).on('click', '.btn-copy-json', function() {
        const text = $('#' + $(this).data('target')).text();
        const $btn = $(this);
        if (navigator.clipboard && text) {
            navigator.clipboard.writeText(text).then(function() {
                $btn.html('<i class="fas fa-check"></i> Copied!');
                setTimeout(function() { $btn.html('<i class="fas fa-copy"></i> Copy Body'); }, 1800);
            });
        }
    });
});
