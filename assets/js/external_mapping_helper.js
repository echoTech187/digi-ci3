/**
 * External Mapping Helper Module
 * Encapsulates reusable UI handlers, Select2 customizations, and confirmation builders.
 */
var ExternalMappingHelper = (function($) {
    'use strict';

    function initDrawer() {
        $('#toggleGuideBtn').on('click', function() {
            $('#instructionDrawer, #instructionOverlay').addClass('open');
            $('body').css('overflow', 'hidden');
        });

        $('#closeDrawerBtn, #instructionOverlay').on('click', function() {
            $('#instructionDrawer, #instructionOverlay').removeClass('open');
            $('body').css('overflow', '');
        });
    }

    function initSelect2Modals() {
        $('#feeModal, #globalUpdateModal').on('shown.bs.modal', function() {
            var $modal = $(this);
            $modal.find('.select2').each(function() {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2('destroy');
                }
                $(this).select2({ 
                    dropdownParent: $('body'),
                    width: '100%',
                    minimumResultsForSearch: 0,
                    closeOnSelect: $(this).prop('multiple') ? false : true
                });
            });
        });

        $('#globalUpdateModal').on('hidden.bs.modal', function() {
            $(this).find('form').each(function() { this.reset(); });
            $(this).find('.select2').val('').trigger('change');
        });
    }

    function initSelect2FilterActions() {
        $(document).on('select2:open', '#global_merchant', function() {
            setTimeout(function() {
                var $dropdown = $('.select2-container--open .select2-dropdown');
                if ($dropdown.length && $dropdown.find('.select2-custom-actions').length === 0) {
                    var $actions = $(`
                        <div class="select2-custom-actions d-flex justify-content-between align-items-center px-3 py-2 bg-light border-bottom" style="font-size: 12px; z-index: 9999;">
                            <span class="text-primary font-weight-bold action-btn select-all-filtered-action" style="cursor: pointer;"><i class="fas fa-check-double mr-1"></i> Select All Filtered</span>
                            <span class="text-danger font-weight-bold action-btn clear-all-action" style="cursor: pointer;"><i class="fas fa-times mr-1"></i> Clear All</span>
                        </div>
                    `);
                    $dropdown.prepend($actions);
                }
            }, 10);
        });

        $(document).on('click', '.select-all-filtered-action', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var $select = $('#global_merchant');
            var searchLower = ($('.select2-container--open .select2-search__field').val() || '').toLowerCase().trim();
            var currentValues = $select.val() || [];
            if (!Array.isArray(currentValues)) currentValues = [];
            var newValues = currentValues.slice();

            $select.find('option').each(function() {
                var text = $(this).text().toLowerCase();
                var val = $(this).val();
                if (!searchLower || text.indexOf(searchLower) !== -1) {
                    if (newValues.indexOf(val) === -1) newValues.push(val);
                }
            });

            $select.val(newValues).trigger('change');
            $select.select2('close').select2('open');
        });

        $(document).on('click', '.clear-all-action', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('#global_merchant').val([]).trigger('change');
            $('#global_merchant').select2('close').select2('open');
        });
    }

    function buildConfirmationHtml(scope, cur, target) {
        return `
            <div class="text-left small mb-3" style="font-size:13px;">
                <div class="fw-bold text-primary mb-2 border-bottom pb-1">Update Scope</div>
                <div class="mb-1"><span class="text-muted">Type:</span> <strong>${scope.type}</strong></div>
                ${scope.merchant ? `<div class="mb-3"><span class="text-muted">Merchant:</span> <strong>${scope.merchant}</strong></div>` : ''}
                <div class="row mt-3 mx-0">
                    <div class="col-6 border-right px-1" style="border-right: 1px solid #e3e6f0;">
                        <div class="fw-bold text-danger mb-2 border-bottom pb-1">Current (Filter)</div>
                        <div class="mb-1"><span class="text-muted">Group:</span> <br><strong>${cur.group}</strong></div>
                        <div class="mb-1"><span class="text-muted">External ID:</span> <br><strong>${cur.ext}</strong></div>
                        <div class="mb-1"><span class="text-muted">Channel ID:</span> <br><strong>${cur.channel}</strong></div>
                        <div class="mb-1"><span class="text-muted">Status:</span> <br><strong>${cur.status}</strong></div>
                    </div>
                    <div class="col-6 px-1">
                        <div class="fw-bold text-success mb-2 border-bottom pb-1">New (Target)</div>
                        <div class="mb-1"><span class="text-muted">Group:</span> <br><strong>${target.group}</strong></div>
                        <div class="mb-1"><span class="text-muted">External ID:</span> <br><strong>${target.ext}</strong></div>
                        <div class="mb-1"><span class="text-muted">Channel ID:</span> <br><strong>${target.channel}</strong></div>
                        <div class="mb-1"><span class="text-muted">Status:</span> <br><strong>${target.status}</strong></div>
                    </div>
                </div>
            </div>
            <div class="alert alert-warning py-2 mb-0 small text-left">
                <i class="fas fa-exclamation-triangle mr-1"></i> This will affect all matching channel mappings!
            </div>
        `;
    }

    function initDeleteButtonHandler() {
        $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault();
            const url = $(this).data('href');
            Swal.fire({
                title: 'Are you sure?',
                text: "This configuration will be permanently deleted!",
                icon: 'warning',
                showCancelButton: true,
                customClass: { popup: 'swal2-premium-popup', confirmButton: 'swal2-premium-confirm', cancelButton: 'swal2-premium-cancel', actions: 'swal2-premium-actions' },
                buttonsStyling: false,
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) window.location.href = url;
            });
        });
    }

    return {
        initDrawer: initDrawer,
        initSelect2Modals: initSelect2Modals,
        initSelect2FilterActions: initSelect2FilterActions,
        buildConfirmationHtml: buildConfirmationHtml,
        initDeleteButtonHandler: initDeleteButtonHandler
    };
})(jQuery);
