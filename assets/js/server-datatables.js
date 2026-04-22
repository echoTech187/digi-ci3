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
        "ordering": false,
        "language": {
            "processing": '<div class="dt-loader-inner"><div class="spinner-border text-primary" role="status"></div><div class="small font-weight-bold ml-2">PROCESSING DATA...</div></div>',
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
            var $info  = $(api.table().container()).find('.dt-footer-info');

            var currPage   = info.page + 1;
            var totalPages = info.pages || 1;

            // Render Info text with better formatting
            if ($info.length) {
                var total = info.recordsDisplay;
                if (total === 0) {
                    $info.html('Showing <strong>0</strong> of <strong>0</strong> results');
                } else {
                    var end = info.end;
                    $info.html('Showing <strong>' + end + '</strong> of <strong>' + number_format(total) + '</strong> results');
                }
            }

            // Render Pager
            var pagerHtml = '<div class="dt-pager-nav">';
            
            // Previous Button
            pagerHtml += '<button class="dt-pager-btn dt-prev-btn" ' + (info.page === 0 ? 'disabled' : '') + '>';
            pagerHtml += '<i class="fas fa-chevron-left mr-md-2"></i><span class="dt-pager-btn-txt">Previous</span>';
            pagerHtml += '</button>';

            // Page Numbers
            pagerHtml += '<ul class="dt-pager-numbers d-none d-md-flex">';
            pagerHtml += generatePagerNumbers(info);
            pagerHtml += '</ul>';

            // Next Button
            pagerHtml += '<button class="dt-pager-btn dt-next-btn" ' + (info.page >= totalPages - 1 ? 'disabled' : '') + '>';
            pagerHtml += '<span class="dt-pager-btn-txt">Next</span><i class="fas fa-chevron-right ml-md-2"></i>';
            pagerHtml += '</button>';

            pagerHtml += '</div>';

            $pager.html(pagerHtml);

            // Bind Events
            $pager.find('.dt-prev-btn').off('click').on('click', function() {
                if (!$(this).prop('disabled')) { api.page('previous').draw('page'); }
            });
            $pager.find('.dt-next-btn').off('click').on('click', function() {
                if (!$(this).prop('disabled')) { api.page('next').draw('page'); }
            });
            $pager.find('.dt-pager-link').off('click').on('click', function() {
                var page = $(this).data('page');
                if (page !== undefined) { api.page(page).draw('page'); }
            });
        }
    };

    /**
     * Internal Helper: Generate numeric page links with ellipses
     */
    function generatePagerNumbers(info) {
        var current = info.page;
        var last = info.pages - 1;
        var delta = 1; // Number of pages back and forth from current
        var left = current - delta;
        var right = current + delta + 1;
        var range = [];
        var rangeWithDots = [];
        var l;

        if (info.pages <= 1) return '<li class="dt-pager-item"><button class="dt-pager-link active">1</button></li>';

        for (var i = 0; i <= last; i++) {
            if (i == 0 || i == last || (i >= left && i < right)) {
                range.push(i);
            }
        }

        for (var i of range) {
            if (l !== undefined) {
                if (i - l === 2) {
                    rangeWithDots.push(l + 1);
                } else if (i - l !== 1) {
                    rangeWithDots.push('...');
                }
            }
            rangeWithDots.push(i);
            l = i;
        }

        var html = '';
        for (var i of rangeWithDots) {
            if (i === '...') {
                html += '<li class="dt-pager-ellipsis">...</li>';
            } else {
                html += '<li class="dt-pager-item">';
                html += '<button class="dt-pager-link ' + (i === current ? 'active' : '') + '" data-page="' + i + '">' + (i + 1) + '</button>';
                html += '</li>';
            }
        }
        return html;
    }

    // Merge default options with any additional options provided
    var finalOptions = $.extend(true, {}, defaultOptions, additionalOptions);
    var table = $(tableId).DataTable(finalOptions);
    var target = $(tableId).closest('.table-responsive');
    if (!target.length) target = $(".table-responsive").first();

    // Show loading state on init AND on every subsequent request (search, paginate)
    target.addClass("dt-processing-active");
    table.on('preXhr.dt', function() {
        target.addClass("dt-processing-active");
    });
    table.on('xhr.dt', function (e, settings, json) {
        target.removeClass("dt-processing-active");
    });
    // Initialize and return the DataTable instance
    return table;


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
        // Trigger blur to ensure any pending changes are committed if needed
        $(this).blur().focus(); 
    }
});

/**
 * Global AJAX CSRF Injection
 * Ensures all jQuery AJAX POST requests include the security token required by CI3.
 */
$(document).ajaxSend(function(event, jqXHR, settings) {
    if (settings.type.toUpperCase() === 'POST') {
        var csrfName = $('meta[name="csrf-token-name"]').attr('content');
        var csrfHash = $('meta[name="csrf-token-hash"]').attr('content');
        
        if (csrfName && csrfHash) {
            // Handle string data (serialized)
            if (typeof settings.data === 'string') {
                if (settings.data.indexOf(csrfName + '=') === -1) {
                    settings.data += (settings.data ? '&' : '') + csrfName + '=' + csrfHash;
                }
            } 
            // Handle object data
            else if (typeof settings.data === 'object' && !(settings.data instanceof FormData)) {
                settings.data[csrfName] = csrfHash;
            }
        }
    }
});
