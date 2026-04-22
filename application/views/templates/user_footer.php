</div>
<!-- End of Main Content -->

<!-- Footer -->
<footer class="sticky-footer">
    <div class="container my-auto">
        <div class="copyright text-center my-auto">
            <span>Copyright &copy; Gidi.co.id <?= date('Y'); ?></span>
        </div>
    </div>
</footer>
<!-- End of Footer -->


</div>
<!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden; background: var(--bg-card, #1e293b);">
            <div class="modal-header border-0 py-4 px-4 d-flex align-items-center">
                <div class="d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px; border-radius: 50%; background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                    <i class="fas fa-sign-out-alt fa-lg"></i>
                </div>
                <h5 class="modal-title font-weight-bold" id="exampleModalLabel" style=" font-size: 1.25rem;">Ready to Leave?</h5>
                <button class="close ml-auto" type="button" data-dismiss="modal" aria-label="Close" style="color: var(--text-muted, #94a3b8); text-shadow: none; opacity: 1;">
                    <span aria-hidden="true" style="font-size: 1.5rem;">×</span>
                </button>
            </div>
            <div class="modal-body px-4 py-4 text-muted" style="color: var(--text-muted, #94a3b8); font-size: 0.95rem; line-height: 1.6;">
                Select <strong>"Logout"</strong> below if you are ready to end your current session and secure your account.
            </div>
            <div class="modal-footer border-0 px-4 py-4">
                <button class="btn font-weight-bold px-4 py-2" type="button" data-dismiss="modal" style="border-radius: 12px; background: rgba(148, 163, 184, 0.1); color: var(--text-muted, #94a3b8); border: none;">Cancel</button>
                <a class="btn font-weight-bold px-4 py-2" href="<?= base_url('auth/logout'); ?>" style="border-radius: 12px; background: #ef4444; color: white; border: none; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="<?= base_url('assets/'); ?>js/sb-admin-2.min.js"></script>

    <!-- Page level custom scripts -->
<script src="<?= base_url('assets/'); ?>js/demo/datatables-demo.js"></script>

<script>
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });

    $('.form-check-input').on('click', function() {
        const menuId = $(this).data('menu');
        const roleId = $(this).data('role');

        $.ajax({
            url: "<?= base_url('admin/changeaccess'); ?>",
            type: 'post',
            data: {
                menuId: menuId,
                roleId: roleId
            },
            success: function() {
                document.location.href = "<?= base_url('admin/roleaccess/'); ?>" + roleId;
            }
        });

    });
</script>

    <!-- Sidebar Toggle Logic (Refactored for sb-sidebar) -->
    <script>
        $(document).ready(function() {
            // ── New Sidebar Toggle System ──
            var $sidebar = $('.sb-sidebar');
            var $body = $('body');
            var $wrapper = $('#wrapper');
            
            // Create mobile overlay if not exists
            if ($('.sb-mobile-overlay').length === 0) {
                $('<div class="sb-mobile-overlay"></div>').appendTo('body');
            }
            var $overlay = $('.sb-mobile-overlay');

            // Desktop toggle: collapse/expand sidebar
            $(document).on('click', '#sidebarToggle', function() {
                if (window.innerWidth >= 768) {
                    $sidebar.toggleClass('toggled');
                    $wrapper.toggleClass('sb-toggled');
                    // Persist state
                    try {
                        localStorage.setItem('sb_collapsed', $sidebar.hasClass('toggled') ? '1' : '0');
                    } catch(e) {}
                }
            });

            // Restore desktop collapse state
            if (window.innerWidth >= 768) {
                try {
                    if (localStorage.getItem('sb_collapsed') === '1') {
                        $sidebar.addClass('toggled');
                        $wrapper.addClass('sb-toggled');
                    }
                } catch(e) {}
            }

            // Mobile toggle: slide in/out
            function openMobileSidebar() {
                $sidebar.addClass('mobile-open');
                $overlay.addClass('active');
                $('body').css('overflow', 'hidden');
            }

            function closeMobileSidebar() {
                closeFlyout();
                $body.removeClass('sidebar-toggled');
                $sidebar.removeClass('mobile-open');
                $overlay.removeClass('active');
                $('body').css('overflow', '');
            }

            // Open via topbar toggle button
            $(document).on('click', '#sidebarToggleTop', function(e) {
                if (window.innerWidth < 768) {
                    e.preventDefault();
                    e.stopPropagation();
                    if ($sidebar.hasClass('mobile-open')) {
                        closeMobileSidebar();
                    } else {
                        openMobileSidebar();
                    }
                }
            });

            // Close via X button inside sidebar
            $(document).on('click', '#closeSidebarMobile', function(e) {
                e.preventDefault();
                closeMobileSidebar();
            });

            // Close via overlay click
            $(document).on('click', '.sb-mobile-overlay', function() {
                closeMobileSidebar();
            });

            // Reset on resize
            $(window).on('resize', function() {
                if (window.innerWidth >= 768) {
                    closeMobileSidebar();
                    $('body').css('overflow', '');
                }
                closeFlyout();
            });

            // ── Mini Sidebar Flyout Submenu ──
            var $flyoutBackdrop = $('<div class="sb-flyout-backdrop"></div>').appendTo('body');

            function openFlyout($flyout, $triggerLink) {
                // Position aligned to the clicked nav item
                var rect = $triggerLink[0].getBoundingClientRect();
                var topPos = rect.top;
                var winH = $(window).height();

                // Temporarily show to measure height
                $flyout.css({ top: topPos, visibility: 'hidden' }).addClass('active');
                var fh = $flyout.outerHeight();
                $flyout.css({ visibility: '' });

                // Clamp so it doesn't overflow viewport bottom
                if (topPos + fh > winH - 10) {
                    topPos = Math.max(10, winH - fh - 10);
                }
                $flyout.css('top', topPos);
                $flyoutBackdrop.addClass('active');
            }

            function closeFlyout() {
                $('.sb-flyout.active').removeClass('active');
                $flyoutBackdrop.removeClass('active');
            }

            // Click on a submenu-parent nav link
            $(document).on('click', '.sb-has-sub > .sb-nav-link', function(e) {
                // Only intercept when sidebar is in mini/toggled mode
                if (!$sidebar.hasClass('toggled')) return;

                e.preventDefault();
                e.stopPropagation();

                var $link = $(this);
                var $item = $link.closest('.sb-has-sub');
                var $flyout = $item.find('> .sb-flyout');

                if ($flyout.hasClass('active')) {
                    closeFlyout();
                } else {
                    closeFlyout();
                    openFlyout($flyout, $link);
                }
            });

            // Close flyout when clicking on backdrop
            $flyoutBackdrop.on('click', function() {
                closeFlyout();
            });

            // Close flyout when sidebar is expanded back
            $('#sidebarToggle').on('click', function() {
                closeFlyout();
            });

            // Close on Escape key
            $(document).on('keydown.flyout', function(e) {
                if (e.key === 'Escape') closeFlyout();
            });

            // Global Select2 Initialization
            $('select')
                .not('.dataTables_length select')
                .not('.swal2-select')
                .not('.dt-more-panel select')
                .each(function() {
                    var $el = $(this);
                    if (!$el.hasClass('select2-hidden-accessible')) {
                        $el.select2({
                            width: '100%',
                            minimumResultsForSearch: 0
                        });
                    }
                });

            // Fallback: If any .dt-more-panel select was NOT initialized per-page,
            // initialize it when the panel opens (using event delegation)
            $(document).on('click', '.dt-more-filters-btn', function() {
                var $panel = $(this).closest('.dt-more-filters-wrapper').find('.dt-more-panel');
                $panel.find('select').not('.select2-hidden-accessible').each(function() {
                    $(this).select2({
                        width: '100%',
                        dropdownParent: $panel,
                        minimumResultsForSearch: 0
                    });
                });
            });

            /* Dynamic DataTables Responsive Labels & Actions */
            function initMobileTableLabels() {
                if (window.innerWidth <= 768) {
                    $('.dt-table').each(function() {
                        var $table = $(this);
                        var labels = [];
                        
                        // Collect labels from headers
                        $table.find('thead th').each(function() {
                            labels.push($(this).text().trim());
                        });
                        
                        // Apply labels to each cell
                        $table.find('tbody tr').each(function() {
                            $(this).find('td').each(function(index) {
                                if (labels[index]) {
                                    $(this).attr('data-label', labels[index]);
                                }
                                
                                // Replace null/empty values with dash
                                var cellValue = $(this).text().trim();
                                if (cellValue === "" || cellValue === "null") {
                                    $(this).text('-');
                                }
                                
                                // Conditional Action Drawer logic (< 3 items)
                                var $dropdown = $(this).find('.dropdown');
                                if ($dropdown.length > 0) {
                                    var numItems = $dropdown.find('.dropdown-item').length;
                                    if (numItems > 0 && numItems < 3) {
                                        $dropdown.addClass('mobile-inline-actions');
                                    } else {
                                        $dropdown.removeClass('mobile-inline-actions');
                                    }
                                }
                            });
                        });
                    });

                    // Hide refresh text on mobile globally
                    $('.btn-dt-secondary, .btn-dt-primary').has('.fa-sync-alt, .fa-sync').each(function() {
                        var textNodes = $(this).contents().filter(function() {
                            return this.nodeType === 3 && this.nodeValue.trim() !== '';
                        });
                        if (textNodes.length > 0) {
                            var text = textNodes[0].nodeValue;
                            textNodes[0].nodeValue = '';
                            $(this).append('<span class="btn-text-mobile-hide">' + text + '</span>');
                            $(this).find('i').removeClass('mr-1 mr-2'); // Remove margin since it's just icon
                        }
                    });

                } else {
                    $('.dt-table .dropdown').removeClass('mobile-inline-actions');
                }
            }

            // Execute on load and after every DataTables draw
            initMobileTableLabels();
            $(document).on('draw.dt', function() {
                initMobileTableLabels();
            });

            $(window).on('resize', initMobileTableLabels);

            /* Mobile Bottom Drawer & Backdrop Logic */
            $(document).on('show.bs.dropdown', '.dt-table .dropdown', function() {
                if (window.innerWidth <= 768) {
                    $('body').addClass('drawer-open');
                    
                    // Close on backdrop click (simulated by body::after)
                    // Since it's CSS-based, we just need to ensure clicking outside or on links works.
                }
            });

            $(document).on('hide.bs.dropdown', '.dt-table .dropdown', function() {
                if (window.innerWidth <= 768) {
                    $('body').removeClass('drawer-open');
                }
            });

            // Ensure drawer closes when an item is clicked
            $(document).on('click', '.dt-table .dropdown-item', function() {
                if (window.innerWidth <= 768) {
                    $(this).closest('.dropdown-menu').dropdown('hide');
                }
            });

            /* ── Universal Filter Porter (Mobile) ── */
            function portFiltersToDrawer() {
                if (window.innerWidth <= 768) {
                    // Create overlay if not exists
                    if ($('.dt-panel-overlay').length === 0) {
                        $('body').append('<div class="dt-panel-overlay"></div>');
                    }

                    $('.dt-toolbar').each(function() {
                        var $toolbar = $(this);
                        var $filters = $toolbar.find('.dt-toolbar-filters > .dt-filter-group');
                        
                        // Real filters are those with actual input/select elements
                        var $realFilters = $filters.filter(function() {
                            return $(this).find('input, select').length > 0;
                        });
                        
                        var $moreWrapper = $toolbar.find('.dt-more-filters-wrapper');
                        
                        // ONLY create drawer if REAL filters (inputs/selects) exist.
                        if ($realFilters.length > 0 && $moreWrapper.length === 0) {
                            var skeleton = 
                                '<div class="dt-more-filters-wrapper">' +
                                    '<button type="button" class="dt-more-filters-btn">' +
                                        '<i class="fas fa-filter"></i>' +
                                    '</button>' +
                                    '<div class="dt-more-panel">' +
                                        '<div class="dt-more-panel-header">' +
                                            '<span class="dt-more-panel-title"><i class="fas fa-filter mr-1"></i>Advanced Filters</span>' +
                                            '<a href="#" class="dt-more-clear" id="btnClearFilters">Clear All</a>' +
                                        '</div>' +
                                        '<div class="dt-more-panel-body"></div>' +
                                        '<div class="dt-more-panel-footer"></div>' +
                                    '</div>' +
                                '</div>';
                            
                            var $fContainer = $toolbar.find('.dt-toolbar-filters');
                            if ($fContainer.length === 0) {
                                $toolbar.append('<div class="dt-toolbar-filters"></div>');
                                $fContainer = $toolbar.find('.dt-toolbar-filters');
                            }
                            $fContainer.append(skeleton);
                            
                            // Re-bind Toggle Logic
                            $toolbar.find('.dt-more-filters-btn').on('click', function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                var $panel = $(this).next('.dt-more-panel');
                                $('.dt-more-panel').not($panel).removeClass('dt-panel-open');
                                $(this).toggleClass('dt-open');
                                $panel.toggleClass('dt-panel-open');
                            });
                            
                            // Clear All: redirect to reset URL
                            $toolbar.find('#btnClearFilters').on('click', function(e) {
                                e.preventDefault();
                                var $resetLink = $toolbar.find('a[href*="reset"]').first();
                                if ($resetLink.length) {
                                    window.location.href = $resetLink.attr('href');
                                } else {
                                    $toolbar.find('form').trigger('reset');
                                }
                            });
                            
                            $moreWrapper = $toolbar.find('.dt-more-filters-wrapper');
                        }

                        // 2. Filter migration
                        var $target = $toolbar.find('.dt-more-panel-body');
                        var $filtersToMigrate = $toolbar.find('.dt-toolbar-filters .dt-filter-group').not('.ported-filter');

                        if ($filtersToMigrate.length > 0 && $target.length > 0) {
                            var hasMovedAnything = false;
                            $filtersToMigrate.each(function() {
                                var $group = $(this);
                                if ($group.closest('.dt-more-panel').length > 0) return; // Already in drawer

                                var $subBtn = $group.find('button[type="submit"]');
                                if ($subBtn.length > 0) {
                                    var $footer = $toolbar.find('.dt-more-panel-footer');
                                    if ($footer.find('button[type="submit"]').length === 0) {
                                        // Clone subBtn and add close logic
                                        var $applyBtn = $subBtn.clone().appendTo($footer).addClass('btn-block btn-dt-primary').html('<i class="fas fa-check mr-1"></i> APPLY FILTER');
                                        
                                        $applyBtn.on('click', function() {
                                            $toolbar.find('.dt-more-filters-btn').removeClass('dt-open');
                                            $toolbar.find('.dt-more-panel').removeClass('dt-panel-open');
                                        });

                                        // Add CANCEL button to footer with close logic
                                        var $cancelBtn = $('<button type="button" class="btn-dt-cancel" id="btnCloseFilters">CANCEL</button>').appendTo($footer);
                                        $cancelBtn.on('click', function() {
                                            $toolbar.find('.dt-more-filters-btn').removeClass('dt-open');
                                            $toolbar.find('.dt-more-panel').removeClass('dt-panel-open');
                                        });
                                    }

                                    // Global listener for ANY apply button in this panel (including static ones)
                                    $toolbar.on('click', '.dt-more-panel-footer button[type="submit"], .dt-more-panel button[type="submit"]', function() {
                                        $toolbar.find('.dt-more-filters-btn').removeClass('dt-open');
                                        $toolbar.find('.dt-more-panel').removeClass('dt-panel-open');
                                    });
                                    return;
                                }

                                var $select = $group.find('select');
                                var isSelect2 = $select.hasClass('select2-hidden-accessible');

                                $group.addClass('ported-filter');
                                $group.find('.dt-filter-label').addClass('dt-more-label').removeClass('dt-filter-label');
                                $group.find('.dt-filter-chip').addClass('dt-more-field-container').removeClass('dt-filter-chip');
                                
                                $target.append($group);
                                hasMovedAnything = true;

                                if (isSelect2) {
                                    $select.select2('destroy');
                                    $select.select2({
                                        width: '100%',
                                        dropdownParent: $target.closest('.dt-more-panel'),
                                        minimumResultsForSearch: 0
                                    });
                                }
                            });
                            
                            if (hasMovedAnything) {
                                $toolbar.find('.dt-toolbar-filters').addClass('filters-ported');
                            }
                        }
                    });

                    // 4. Handle Drawer Overlay Toggle (using MutationObserver for absolute reliability)
                    var observer = new MutationObserver(function(mutations) {
                        mutations.forEach(function(mutation) {
                            if (mutation.attributeName === "class") {
                                var $target = $(mutation.target);
                                if ($target.hasClass('dt-panel-open')) {
                                    $('.dt-panel-overlay').addClass('active');
                                    $('body').addClass('drawer-open-lock').css('overflow', 'hidden');
                                    
                                    // Precise Cleanup: ONLY hide buttons containing reset icons or text
                                    $target.find('.dt-mobile-reset-icon').hide();
                                    $target.find('a, button').filter(function() {
                                        var text = $(this).text().trim().toLowerCase();
                                        return text.includes('reset') || $(this).find('i.fa-undo').length > 0;
                                    }).not('.btn-dt-cancel, #btnCloseFilters').hide();
                                } else {
                                    // Check if ANY panel is still open
                                    if ($('.dt-more-panel.dt-panel-open').length === 0) {
                                        $('.dt-panel-overlay').removeClass('active');
                                        $('body').removeClass('drawer-open-lock').css('overflow', '');
                                    }
                                }
                            }
                        });
                    });

                    $('.dt-more-panel').each(function() {
                        observer.observe(this, { attributes: true });
                    });

                    // 5. Close handlers
                    $(document).on('click', '.dt-panel-overlay, .btn-dt-cancel, #btnCloseFilters, .dt-more-panel-footer button.btn-dt-secondary', function() {
                         $('.dt-more-panel').removeClass('dt-panel-open');
                         $('.dt-more-filters-btn, #btnToggleFilters, #vaMoreFiltersBtn').removeClass('dt-open');
                    });

                    // 6. Final cleanup: Remove any undesired duplicate reset buttons from drawer
                    $('.dt-more-panel .dt-mobile-reset-icon, .dt-more-panel .btn-dt-secondary').filter(function() {
                        return $(this).text().trim().toLowerCase().includes('reset') || $(this).find('i.fa-undo').length > 0;
                    }).hide(); // Hide instead of remove to avoid breaking local scripts
                }
            }
            // Run initially and also on window resize to re-init if needed
            portFiltersToDrawer();
            $(window).on('resize', debounce(function() {
                if (window.innerWidth <= 768) portFiltersToDrawer();
            }, 250));
        });
    </script>
    <!-- Global UX Enhancements -->
    <script>
        $(document).ready(function() {
            // 1. Auto-hide Success Alerts (smooth slide up)
            setTimeout(function() {
                $('.alert-success').slideUp('slow');
            }, 5000);

            // 2. Fix Select2 Auto-Focus in Bootstrap Modals
            $(document).on('select2:open', function() {
                setTimeout(function() {
                    let searchField = document.querySelector('.select2-container--open .select2-search__field');
                    if (searchField) searchField.focus();
                }, 10);
            });
            
            // 3. Smooth Button Loading Logic
            $(document).on('submit', 'form', function() {
                let $btn = $(this).find('button[type="submit"]');
                if (!$btn.hasClass('no-loader')) {
                    let originalHtml = $btn.html();
                    $btn.data('original-html', originalHtml);
                    $btn.prop('disabled', true).html('<i class="fas fa-circle-notch fa-spin mr-2"></i> Processing...');
                }
            });
        });
    </script>
</body>

</html>

