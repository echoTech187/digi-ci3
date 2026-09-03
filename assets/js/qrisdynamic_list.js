/**
 * QRIS Dynamic Transactions DataTables & Modal Details
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

    const ajaxUrl = window.BASE_URL + 'qris/dynamic/data-json';
    const csrfName = $('meta[name="csrf-token-name"]').attr('content') || 'csrf_token_name';
    const csrfHash = $('meta[name="csrf-token-hash"]').attr('content') || '';

    const table = initServerDataTable("#qrisDynamicTable", ajaxUrl, [
        { "data": "no", "orderable": false, "className": "text-center align-middle" },
        { 
            "data": "c_created_datetime",
            "className": "align-middle",
            "render": data => '<div class="font-weight-bold text-dark text-nowrap">' + moment(data).format('DD MMM YYYY') + '</div><div class="small text-muted">' + moment(data).format('HH:mm:ss') + '</div>'
        },
        { 
            "data": "merchant_name",
            "className": "align-middle",
            "render": function(data, type, row) {
                return '<div class="font-weight-bold text-primary">' + data + '</div><div class="small text-muted">ID: #' + row.ref_merchantId + '</div>';
            }
        },
        { 
            "data": "sub_merchant_name",
            "className": "align-middle",
            "render": function(data, type, row) {
                if (!data || data === '-') return '<span class="text-muted small">-</span>';
                return '<div class="font-weight-bold text-dark">' + data + '</div><div class="small text-muted">ID: #' + row.ref_subMerchantId + '</div>';
            }
        },
        { 
            "data": "c_merchantTransactionId",
            "className": "align-middle font-family-monospace small",
            "render": data => '<span class="text-dark font-weight-bold">' + data + '</span>'
        },
        { 
            "data": "c_cashinChannelGroup",
            "className": "align-middle",
            "render": function(data, type, row) {
                return '<span class="badge badge-light border px-2 py-1 text-uppercase">' + (data || 'QRIS') + '</span><div class="small text-muted mt-1">' + (row.ref_cashinChannelId || '') + '</div>';
            }
        },
        { 
            "data": "c_externalId",
            "className": "align-middle",
            "render": function(data, type, row) {
                return '<a href="javascript:void(0)" class="btn-detail-external font-weight-bold text-primary" data-id="' + row.id + '"><i class="fas fa-external-link-alt mr-1"></i>' + data + '</a>';
            }
        },
        { 
            "data": "c_amount",
            "className": "align-middle font-weight-bold text-dark text-right",
            "render": data => 'Rp ' + number_format(data, 0, ',', '.')
        },
        { 
            "data": "c_expired_datetime",
            "className": "align-middle",
            "render": function(data) {
                if (!data) return '-';
                return '<div class="text-nowrap">' + moment(data).format('DD MMM YYYY') + '</div><div class="small text-muted">' + moment(data).format('HH:mm:ss') + '</div>';
            }
        },
        { 
            "data": "c_status",
            "className": "align-middle text-center",
            "render": function(data) {
                let badge = 'badge-secondary';
                if (data === 'Paid' || data === 'SUCCESS') badge = 'badge-success';
                else if (data === 'Pending' || data === 'Created') badge = 'badge-warning';
                else if (data === 'Failed' || data === 'Expired' || data === 'Cancel') badge = 'badge-danger';
                return '<span class="badge badge-pill ' + badge + ' px-3 py-1">' + data + '</span>';
            }
        }
    ]);

    $('#qrisDynamicGlobalSearch').on('input', debounce(function() { table.search(this.value).draw(); }, 400));

    // More Filters Panel Handler
    const $moreBtn = $('#qrisMoreFiltersBtn');
    const $morePanel = $('#qrisMoreFiltersPanel');

    $moreBtn.on('click', function(e) {
        e.stopPropagation();
        const isOpen = $morePanel.hasClass('dt-panel-open');
        $morePanel.toggleClass('dt-panel-open', !isOpen);
        $moreBtn.toggleClass('dt-open', !isOpen);
    });

    $('#qrisMoreFiltersClose').on('click', function() {
        $morePanel.removeClass('dt-panel-open');
        $moreBtn.removeClass('dt-open');
    });

    $(document).on('click', '.btn-detail-external', function() {
        const id = $(this).data('id');
        $.ajax({
            url: window.BASE_URL + "qris/dynamic/detail-channel-external/" + id,
            type: "GET",
            dataType: "json",
            success: function(res) {
                if (res.status && res.data) {
                    const d = res.data;
                    $('#cashinExternalId').text(d.c_cashinExternalId || '-');
                    $('#TransactionIdExternal1').text(d.c_transactionIdExternal1 || '-');
                    $('#TransactionIdExternal2').text(d.c_transactionIdExternal2 || '-');
                    $('#RequestDatetime').text(d.c_requestDatetime ? moment(d.c_requestDatetime).format('DD MMM YYYY HH:mm:ss') : '-');
                    $('#ResponseDatetime').text(d.c_responseDatetime ? moment(d.c_responseDatetime).format('DD MMM YYYY HH:mm:ss') : '-');
                    
                    try { $('#RequestHeader').text(JSON.stringify(JSON.parse(d.c_requestHeader), null, 2)); } catch(e) { $('#RequestHeader').text(d.c_requestHeader || '-'); }
                    try { $('#RequestBody').text(JSON.stringify(JSON.parse(d.c_requestBody), null, 2)); } catch(e) { $('#RequestBody').text(d.c_requestBody || '-'); }
                    try { $('#ResponseHeader').text(JSON.stringify(JSON.parse(d.c_responseHeader), null, 2)); } catch(e) { $('#ResponseHeader').text(d.c_responseHeader || '-'); }
                    try { $('#ResponseBody').text(JSON.stringify(JSON.parse(d.c_responseBody), null, 2)); } catch(e) { $('#ResponseBody').text(d.c_responseBody || '-'); }

                    $('#detailQrisDynamicChannelExternalModal').modal('show');
                }
            }
        });
    });

    $(document).on('click', '.copy-ref-btn', function() {
        const target = $(this).data('target');
        const text = $(target).text().trim();
        if (text && text !== '-') {
            navigator.clipboard.writeText(text);
            Swal.fire({ title: 'Copied!', text: 'External Ref ID copied to clipboard.', icon: 'success', timer: 1500, showConfirmButton: false });
        }
    });
});
