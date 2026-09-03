/**
 * Merchant Supervisor DataTable & Modal Management
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

    const spvAjaxUrl = window.BASE_URL + 'merchant/supervisor';
    const csrfName = $('meta[name="csrf-token-name"]').attr('content') || 'csrf_token_name';
    const csrfHash = $('meta[name="csrf-token-hash"]').attr('content') || '';

    const table = initServerDataTable("#merchantSpvTable", spvAjaxUrl, [
        { "data": "no", "className": "text-center" },
        { "data": "c_name", "className": "font-weight-bold text-dark text-nowrap" },
        { 
            "data": "c_username",
            "render": data => `<span class="badge badge-light text-dark border px-2 py-1">${data}</span>`
        },
        { "data": "c_email", "className": "text-nowrap" },
        { 
            "data": "c_status",
            "render": function(data) {
                let statusClass = 'secondary';
                if (data === 'Active') statusClass = 'success';
                else if (data === 'Pending') statusClass = 'warning';
                else if (data === 'Blocked') statusClass = 'danger';
                else if (data === 'Freeze') statusClass = 'info';
                return `<span class="badge badge-${statusClass}-soft text-${statusClass} px-2 py-1">${data}</span>`;
            }
        },
        {
            "data": "c_created_date",
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
            "data": null,
            "className": "text-center",
            "orderable": false,
            render: function(data, type, row) {
                return `
                    <div class="dropdown">
                        <button class="btn btn-sm rounded-circle p-2 border-0 bg-transparent" type="button" data-toggle="dropdown" data-boundary="viewport" aria-expanded="false">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right shadow-sm border-0">
                            <a class="dropdown-item edit-spv-btn" href="javascript:void(0)" data-id="${row.id}"><i class="fas fa-edit mr-2 text-warning"></i> Edit Details</a>
                            <a class="dropdown-item delete-spv-btn text-danger" href="javascript:void(0)" data-id="${row.id}" data-name="${row.c_name}"><i class="fas fa-trash mr-2"></i> Delete SPV</a>
                        </div>
                    </div>
                `;
            }
        }
    ]);

    $('#dt-global-search').on('input', debounce(function() { table.search(this.value).draw(); }, 400));

    // More Filters Panel Handler
    const $moreBtn = $('#spvMoreFiltersBtn');
    const $morePanel = $('#spvMoreFiltersPanel');

    $moreBtn.on('click', function(e) {
        e.stopPropagation();
        const isOpen = $morePanel.hasClass('dt-panel-open');
        $morePanel.toggleClass('dt-panel-open', !isOpen);
        $moreBtn.toggleClass('dt-open', !isOpen);
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#spvMoreFiltersPanel, #spvMoreFiltersBtn').length) {
            $morePanel.removeClass('dt-panel-open');
            $moreBtn.removeClass('dt-open');
        }
    });

    // Select2 Merchant Init
    $('.select2-merchant').select2({
        placeholder: "Type to search merchants...",
        allowClear: true,
        dropdownParent: $('body'),
        ajax: {
            url: window.BASE_URL + 'merchant/supervisor/get_merchants_ajax',
            dataType: 'json',
            delay: 250,
            data: function (params) { return { q: params.term }; },
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return { id: item.id, text: item.c_name + ' (' + item.c_email + ')' };
                    })
                };
            },
            cache: true
        }
    });

    $(document).on('click', '.edit-spv-btn', function() {
        const spvId = $(this).data('id');
        $.ajax({
            url: window.BASE_URL + 'merchant/supervisor/get_spv_ajax/' + spvId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    const spv = response.data;
                    $('#editMerchantSpvForm').attr('action', window.BASE_URL + 'merchant/supervisor/update/' + spv.id);
                    $('#edit_c_name').val(spv.c_name);
                    $('#edit_c_username').val(spv.c_username);
                    $('#edit_c_email').val(spv.c_email);
                    $('#edit_c_password, #edit_c_confirmPassword').val('');
                    $('input[name="c_status"][value="' + spv.c_status + '"]').prop('checked', true);

                    const select = $('#edit_c_merchant_spv');
                    select.empty();
                    if (response.merchants && response.merchants.length > 0) {
                        response.merchants.forEach(function(m) {
                            const opt = new Option(m.c_name + ' (' + m.c_email + ')', m.id, true, true);
                            select.append(opt);
                        });
                        select.trigger('change');
                    }
                    $('#editMerchantSpv').modal('show');
                }
            }
        });
    });

    $(document).on('click', '.delete-spv-btn', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        Swal.fire({
            title: 'Delete Supervisor?',
            text: `Are you sure you want to delete supervisor ${name}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel'
        }).then((res) => {
            if (res.isConfirmed) {
                window.location.href = window.BASE_URL + 'merchant/supervisor/delete/' + id;
            }
        });
    });
});
