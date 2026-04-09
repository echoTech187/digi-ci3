/**
 * Reusable Server-Side DataTable Initialization
 * 
 * @param {string} tableId - The ID of the table element (e.g., '#myTable')
 * @param {string} ajaxUrl - The URL for the AJAX request
 * @param {array} columns - Array of column definitions
 * @param {object} additionalOptions - Any additional DataTables options to override defaults
 */
function initServerDataTable(tableId, ajaxUrl, columns, additionalOptions = {}) {
    var defaultOptions = {
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": ajaxUrl,
            "type": "POST",
            "data": function (d) {
                // Automatically append CSRF token if present
                var csrfName = $('meta[name="csrf-token-name"]').attr('content');
                var csrfHash = $('meta[name="csrf-token-hash"]').attr('content');
                if (csrfName && csrfHash) {
                    d[csrfName] = csrfHash;
                }
            },
            "error": function (xhr, error, thrown) {
                console.error("DataTables Error:", error, thrown);
            }
        },
        "columns": columns,
        "language": {
            "processing": '<i class="fa fa-spinner fa-spin fa-2x fa-fw mx-auto d-block text-primary"></i>',
            "info": "Showing _START_ – _END_ of _TOTAL_ results",
            "infoEmpty": "No results to show",
            "infoFiltered": "",
            "zeroRecords": '<div class="text-center py-4 text-muted"><i class="fas fa-inbox fa-2x mb-2 d-block"></i>No data available.</div>'
        },
        "dom": 'rt<"dt-footer"<"dt-footer-info"i><"dt-footer-pager">>',
        "createdRow": function(row, data, dataIndex) {
            // Automatically add data-label for mobile stacking based on <thead> text
            var $table = $(this);
            var $headers = $table.find('thead th');
            $('td', row).each(function(colIndex) {
                var headerText = $($headers[colIndex]).text().trim();
                $(this).attr('data-label', headerText);
            });
        },
        "drawCallback": function(settings) {
            var api    = this.api();
            var info   = api.page.info();
            var $pager = $(api.table().container()).find('.dt-footer-pager');

            var currPage   = info.page + 1;
            var totalPages = info.pages || 1;

            $pager.html(
                '<button class="dt-nav-btn dt-prev-btn" ' + (info.page === 0 ? 'disabled' : '') + '>' +
                    '<i class="fas fa-chevron-left"></i> PREVIOUS' +
                '</button>' +
                '<span class="dt-page-counter">' +
                    '<strong>' + currPage + '</strong> of <strong>' + totalPages + '</strong>' +
                '</span>' +
                '<button class="dt-nav-btn dt-next-btn" ' + (info.page >= totalPages - 1 ? 'disabled' : '') + '>' +
                    'NEXT <i class="fas fa-chevron-right"></i>' +
                '</button>'
            );

            $pager.find('.dt-prev-btn').off('click').on('click', function() {
                if (!$(this).prop('disabled')) { api.page('previous').draw('page'); }
            });
            $pager.find('.dt-next-btn').off('click').on('click', function() {
                if (!$(this).prop('disabled')) { api.page('next').draw('page'); }
            });
        }
    };

    // Merge default options with any additional options provided
    var finalOptions = $.extend(true, {}, defaultOptions, additionalOptions);

    // Initialize and return the DataTable instance
    return $(tableId).DataTable(finalOptions);
}

/**
 * JS Number Format Helper
 */
function number_format(number, decimals, dec_point, thousands_sep) {
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + (Math.round(n * k) / k).toFixed(prec);
        };
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

/**
 * Debounce Helper - Limits the rate at which a function can fire.
 */
function debounce(func, wait, immediate) {
    var timeout;
    return function() {
        var context = this, args = arguments;
        var later = function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
}

/**
 * Global CSRF Injection for Legacy Forms
 */
$(document).ready(function () {
    var csrfName = $('meta[name="csrf-token-name"]').attr('content');
    var csrfHash = $('meta[name="csrf-token-hash"]').attr('content');

    if (csrfName && csrfHash) {
        // Inject CSRF into all POST forms that don't already have it
        $('form[method="post"]').each(function () {
            if ($(this).find('input[name="' + csrfName + '"]').length === 0) {
                $(this).append('<input type="hidden" name="' + csrfName + '" value="' + csrfHash + '">');
            }
        });
    }
});

/**
 * Global optimized search handling
 * Prevents full page reload when Enter is pressed in search inputs
 */
$(document).on('keypress', '.dt-search-input', function(e) {
    if (e.which == 13) { // Enter key
        e.preventDefault();
        $(this).trigger('change'); // Force update if needed
    }
});
