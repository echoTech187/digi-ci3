/**
 * GIDI Core Global JavaScript Engine
 * Modular architecture for UI, Responsive Tables, Filter Drawers, and Health Checks
 */
(function (window, document, $) {
	'use strict';

	// ── 1. Sidebar & Navigation Module ──
	const SidebarModule = {
		init() {
			this.$sidebar = $('.sb-sidebar');
			this.$body = $('body');
			this.$wrapper = $('#wrapper');

			if (!$('.sb-mobile-overlay').length) $('<div class="sb-mobile-overlay"></div>').appendTo('body');
			this.$overlay = $('.sb-mobile-overlay');

			if (!$('.sb-flyout-backdrop').length) $('<div class="sb-flyout-backdrop"></div>').appendTo('body');
			this.$flyoutBackdrop = $('.sb-flyout-backdrop');

			this.initSidebarState();
			this.bindEvents();
		},

		initSidebarState() {
			const w = window.innerWidth;
			if (w >= 768 && w < 992) {
				this.$sidebar.addClass('toggled');
				this.$wrapper.addClass('sb-toggled');
				this.$body.addClass('sidebar-toggled');
			} else if (w >= 992) {
				try {
					const saved = localStorage.getItem('sb_collapsed') === '1';
					this.$sidebar.toggleClass('toggled', saved);
					this.$wrapper.toggleClass('sb-toggled', saved);
				} catch (e) {
					console.warn('Unable to restore sidebar state:', e);
				}
			}
		},

		bindEvents() {
			const self = this;

			$(document).on('click', '#sidebarToggle', function (e) {
				if (window.innerWidth >= 992) {
					self.$sidebar.toggleClass('toggled');
					self.$wrapper.toggleClass('sb-toggled');
					try {
						localStorage.setItem('sb_collapsed', self.$sidebar.hasClass('toggled') ? '1' : '0');
					} catch (err) {
						console.warn('Unable to persist sidebar state:', err);
					}
				} else if (window.innerWidth >= 768) {
					e.preventDefault();
					self.$sidebar.addClass('toggled');
					self.$wrapper.addClass('sb-toggled');
				}
			});

			$(document).on('click', '#sidebarToggleTop', function (e) {
				if (window.innerWidth < 768) {
					e.preventDefault();
					e.stopPropagation();
					self.$sidebar.hasClass('mobile-open') ? self.closeMobileSidebar() : self.openMobileSidebar();
				}
			});

			$(document).on('click', '#closeSidebarMobile, .sb-mobile-overlay', () => self.closeMobileSidebar());

			$(window).on('resize', () => {
				if (window.innerWidth >= 768) {
					self.closeMobileSidebar();
					$('body').css('overflow', '');
				}
				self.closeFlyout();
			});

			// Flyout Submenu
			$(document).on('click', '.sb-has-sub > .sb-nav-link', function (e) {
				if (!self.$sidebar.hasClass('toggled')) return;
				e.preventDefault();
				e.stopPropagation();
				const $flyout = $(this).closest('.sb-has-sub').find('> .sb-flyout');
				$flyout.hasClass('active') ? self.closeFlyout() : self.openFlyout($flyout, $(this));
			});

			this.$flyoutBackdrop.on('click', () => self.closeFlyout());
			$('#sidebarToggle').on('click', () => self.closeFlyout());
			$(document).on('keydown.flyout', (e) => { if (e.key === 'Escape') self.closeFlyout(); });
		},

		openMobileSidebar() {
			this.$sidebar.addClass('mobile-open');
			this.$overlay.addClass('active');
			$('body').css('overflow', 'hidden');
		},

		closeMobileSidebar() {
			this.closeFlyout();
			this.$body.removeClass('sidebar-toggled');
			this.$sidebar.removeClass('mobile-open');
			this.$overlay.removeClass('active');
			$('body').css('overflow', '');
		},

		openFlyout($flyout, $triggerLink) {
			const rect = $triggerLink[0].getBoundingClientRect();
			let topPos = rect.top;
			const winH = $(window).height();

			$flyout.css({ top: topPos, visibility: 'hidden' }).addClass('active');
			const fh = $flyout.outerHeight();
			$flyout.css({ visibility: '' });

			if (topPos + fh > winH - 10) topPos = Math.max(10, winH - fh - 10);
			$flyout.css('top', topPos);
			this.$flyoutBackdrop.addClass('active');
		},

		closeFlyout() {
			$('.sb-flyout.active').removeClass('active');
			this.$flyoutBackdrop.removeClass('active');
		}
	};

	// ── 2. Responsive Table & Action Dropdown Module ──
	const TableResponsiveModule = {
		init() {
			this.initLabels();
			this.bindEvents();
		},

		initLabels() {
			if (window.innerWidth <= 768) {
				$('.dt-table').each(function () {
					const $table = $(this);
					const labels = [];
					$table.find('thead th').each(function () { labels.push($(this).text().trim()); });

					$table.find('tbody tr').each(function () {
						$(this).find('td').each(function (idx) {
							if (labels[idx]) $(this).attr('data-label', labels[idx]);
							const val = $(this).text().trim();
							if (val === '' || val === 'null') $(this).text('-');

							const $dropdown = $(this).find('.dropdown');
							if ($dropdown.length) {
								const num = $dropdown.find('.dropdown-item').length;
								$dropdown.toggleClass('mobile-inline-actions', num > 0 && num < 3);
							}
						});
					});
				});

				$('.btn-dt-secondary, .btn-dt-primary').has('.fa-sync-alt, .fa-sync').each(function () {
					const textNodes = $(this).contents().filter(function () {
						return this.nodeType === 3 && this.nodeValue.trim() !== '';
					});
					if (textNodes.length) {
						const text = textNodes[0].nodeValue;
						textNodes[0].nodeValue = '';
						$(this).append(`<span class="btn-text-mobile-hide">${text}</span>`).find('i').removeClass('mr-1 mr-2');
					}
				});
			} else {
				$('.dt-table .dropdown').removeClass('mobile-inline-actions');
			}
		},

		bindEvents() {
			const self = this;
			$(document).on('draw.dt', () => self.initLabels());
			$(window).on('resize', () => self.initLabels());

			$(document).on('click', '.dt-table tbody tr', function (e) {
				if (window.innerWidth < 992 && !$(e.target).closest('.dropdown, .dropdown-menu, button, a, input, select').length) {
					$(this).toggleClass('dt-row-expanded');
				}
			});

			$(document).on('show.bs.dropdown', '.dt-table .dropdown', function () {
				const $toggle = $(this).find('[data-toggle="dropdown"]');
				const $dropdown = $(this).find('.dropdown-menu');
				$dropdown.data('original-parent', $(this));

				if (window.innerWidth >= 576) {
					$dropdown.detach().appendTo('body').addClass('dt-desktop-dropdown-ported');
					const offset = $toggle.offset();
					const leftPos = offset.left - ($dropdown.outerWidth() - $toggle.outerWidth());
					$dropdown.css({
						display: 'block', position: 'absolute',
						top: offset.top + $toggle.outerHeight() + 4,
						left: leftPos > 10 ? leftPos : 10,
						'z-index': 10050, opacity: '0'
					}).animate({ opacity: '1' }, 120);
				} else {
					$dropdown.detach().appendTo('body').addClass('dt-mobile-bottom-sheet');
					$('body').addClass('dt-actions-sheet-open');
					setTimeout(() => $dropdown.addClass('show'), 10);
				}
			});

			$(document).on('hide.bs.dropdown', '.dt-table .dropdown', function () {
				window.innerWidth >= 576 ? self.closeDesktopDropdown() : self.closeMobileBottomSheet();
			});

			$(document).on('click', '.dt-mobile-bottom-sheet .dropdown-item', () => self.closeMobileBottomSheet());
			$(document).on('click', '.dt-desktop-dropdown-ported .dropdown-item', () => self.closeDesktopDropdown());

			$(document).on('click', function (e) {
				if (window.innerWidth < 576 && $('body').hasClass('dt-actions-sheet-open')) {
					if (!$(e.target).closest('.dt-mobile-bottom-sheet, [data-toggle="dropdown"]').length) self.closeMobileBottomSheet();
				} else if (window.innerWidth >= 576 && $('body > .dt-desktop-dropdown-ported').length) {
					if (!$(e.target).closest('.dt-desktop-dropdown-ported, [data-toggle="dropdown"]').length) self.closeDesktopDropdown();
				}
			});
		},

		closeDesktopDropdown() {
			const $ported = $('body > .dt-desktop-dropdown-ported');
			if ($ported.length) {
				const $parent = $ported.data('original-parent');
				if ($parent && $parent.length) {
					$ported.detach().appendTo($parent).removeClass('dt-desktop-dropdown-ported').css({
						display: '', position: '', top: '', left: '', 'z-index': '', opacity: ''
					});
				}
			}
		},

		closeMobileBottomSheet() {
			const $sheet = $('body > .dt-mobile-bottom-sheet');
			if ($sheet.length) {
				$sheet.removeClass('show');
				$('body').removeClass('dt-actions-sheet-open');
				setTimeout(() => {
					const $parent = $sheet.data('original-parent');
					if ($parent && $parent.length) $sheet.detach().appendTo($parent).removeClass('dt-mobile-bottom-sheet');
				}, 250);
			}
		}
	};

	// ── 3. Universal Filter Drawer Module ──
	const FilterDrawerModule = {
		init() {
			this.portFilters();
			this.bindEvents();
		},

		portFilters() {
			if (window.innerWidth > 768) return;
			if (!$('.dt-panel-overlay').length) $('body').append('<div class="dt-panel-overlay"></div>');

			$('.dt-toolbar').each(function () {
				const $toolbar = $(this);
				const $filters = $toolbar.find('.dt-toolbar-filters > .dt-filter-group');
				const $realFilters = $filters.filter(function () { return $(this).find('input, select').length > 0; });
				let $moreWrapper = $toolbar.find('.dt-more-filters-wrapper');

				if ($realFilters.length > 0 && $moreWrapper.length === 0) {
					const skeleton = `
						<div class="dt-more-filters-wrapper">
							<button type="button" class="dt-more-filters-btn"><i class="fas fa-filter"></i></button>
							<div class="dt-more-panel">
								<div class="dt-more-panel-header">
									<span class="dt-more-panel-title"><i class="fas fa-filter mr-1"></i>Advanced Filters</span>
									<a href="#" class="dt-more-clear" id="btnClearFilters">Clear All</a>
								</div>
								<div class="dt-more-panel-body"></div>
								<div class="dt-more-panel-footer"></div>
							</div>
						</div>`;

					let $fContainer = $toolbar.find('.dt-toolbar-filters');
					if (!$fContainer.length) {
						$toolbar.append('<div class="dt-toolbar-filters"></div>');
						$fContainer = $toolbar.find('.dt-toolbar-filters');
					}
					$fContainer.append(skeleton);

					$toolbar.find('.dt-more-filters-btn').on('click', function (e) {
						e.preventDefault(); e.stopPropagation();
						const $panel = $(this).next('.dt-more-panel');
						$('.dt-more-panel').not($panel).removeClass('dt-panel-open');
						$(this).toggleClass('dt-open');
						$panel.toggleClass('dt-panel-open');
					});

					$toolbar.find('#btnClearFilters').on('click', function (e) {
						e.preventDefault();
						const $resetLink = $toolbar.find('a[href*="reset"]').first();
						$resetLink.length ? (window.location.href = $resetLink.attr('href')) : $toolbar.find('form').trigger('reset');
					});
				}

				const $target = $toolbar.find('.dt-more-panel-body');
				const $filtersToMigrate = $toolbar.find('.dt-toolbar-filters .dt-filter-group').not('.ported-filter, .dt-more-filters-wrapper');

				if ($filtersToMigrate.length && $target.length) {
					let hasMoved = false;
					$filtersToMigrate.each(function () {
						const $group = $(this);
						if ($group.closest('.dt-more-panel').length) return;

						const $subBtn = $group.find('button[type="submit"]');
						if ($subBtn.length) {
							const $footer = $toolbar.find('.dt-more-panel-footer');
							if (!$footer.find('button[type="submit"]').length) {
								const $applyBtn = $subBtn.clone().appendTo($footer).addClass('btn-block btn-dt-primary').html('<i class="fas fa-check mr-1"></i> APPLY FILTER');
								$applyBtn.on('click', () => {
									$toolbar.find('.dt-more-filters-btn').removeClass('dt-open');
									$toolbar.find('.dt-more-panel').removeClass('dt-panel-open');
								});
								const $cancelBtn = $('<button type="button" class="btn-dt-cancel" id="btnCloseFilters">CANCEL</button>').appendTo($footer);
								$cancelBtn.on('click', () => {
									$toolbar.find('.dt-more-filters-btn').removeClass('dt-open');
									$toolbar.find('.dt-more-panel').removeClass('dt-panel-open');
								});
							}
							return;
						}

						const $select = $group.find('select');
						const isSelect2 = $select.hasClass('select2-hidden-accessible');

						$group.addClass('ported-filter');
						$group.find('.dt-filter-label').addClass('dt-more-label').removeClass('dt-filter-label');
						$group.find('.dt-filter-chip').addClass('dt-more-field-container').removeClass('dt-filter-chip');
						$target.append($group);
						hasMoved = true;

						if (isSelect2) {
							$select.select2('destroy').select2({ width: '100%', dropdownAutoWidth: true, dropdownParent: $('body'), minimumResultsForSearch: 0 });
						}
					});

					if (hasMoved) $toolbar.find('.dt-toolbar-filters').addClass('filters-ported');
				}
			});
		},

		bindEvents() {
			const self = this;
			$(window).on('resize', () => { if (window.innerWidth <= 768) self.portFilters(); });

			$(document).on('click', '.dt-panel-overlay, .btn-dt-cancel, #btnCloseFilters, .dt-more-panel-footer button.btn-dt-secondary', () => {
				$('.dt-more-panel').removeClass('dt-panel-open');
				$('.dt-more-filters-btn, #btnToggleFilters, #vaMoreFiltersBtn').removeClass('dt-open');
				$('.dt-panel-overlay').removeClass('active');
				$('body').removeClass('drawer-open-lock').css('overflow', '');
			});
		}
	};

	// ── 4. Form & UI Helpers Module ──
	const FormHelperModule = {
		init() {
			setTimeout(() => $('.alert:not(.alert-permanent)').slideUp('slow'), 5000);

			$('select').not('.dataTables_length select, .swal2-select, .dt-more-panel select').each(function () {
				if (!$(this).hasClass('select2-hidden-accessible')) {
					$(this).select2({ width: '100%', dropdownAutoWidth: true, minimumResultsForSearch: 0 });
				}
			});

			$(document).on('select2:open', () => {
				setTimeout(() => {
					const sf = document.querySelector('.select2-container--open .select2-search__field');
					if (sf) sf.focus();
				}, 10);
			});

			window.loadingBtn = function ($btn, customText) {
				const text = customText || 'Processing...';
				if (!$btn.hasClass('no-loader')) {
					if (!$btn.data('original-html')) $btn.data('original-html', $btn.html());
					$btn.prop('disabled', true).html(`<i class="fas fa-circle-notch fa-spin mr-2"></i> ${text}`);
				}
			};

			window.restoreBtn = function ($form) {
				const $formObj = $form ? ($form instanceof jQuery ? $form : $($form)) : $('form');
				$formObj.find('button[type="submit"]').each(function () {
					const $b = $(this);
					$b.data('original-html') ? $b.html($b.data('original-html')).prop('disabled', false) : $b.prop('disabled', false);
				});
			};

			$(document).on('submit', 'form', function () { window.loadingBtn($(this).find('button[type="submit"]')); });

			$(document).on('hidden.bs.modal', '.modal', function () {
				$(this).find('form').each(function () {
					if (!$(this).hasClass('no-auto-reset')) {
						window.restoreBtn($(this));
						this.reset();
						$(this).find('select.select2, select.select2-hidden-accessible').each(function () {
							const defaultVal = $(this).find('option[selected]').val() || '';
							$(this).val($(this).prop('multiple') ? [] : defaultVal).trigger('change.select2');
						});
					}
				});
			});

			$(document).ajaxComplete(() => {
				$('form').each(function () {
					const $btn = $(this).find('button[type="submit"]');
					if ($btn.length && $btn.prop('disabled') && $btn.data('original-html')) window.restoreBtn($(this));
				});
			});
		}
	};

	// ── 5. DatePicker Migration & Health Monitor Module ──
	const ExtraFeatureModule = {
		init() {
			this.initDatePickers();
			this.initHealthCheck();
		},

		initDatePickers() {
			$('.premium-picker, .dt-filter-chip').each(function () {
				const $chip = $(this);
				const $dates = $chip.find('input[type="date"]');
				if (!$dates.length || typeof PremiumDateRangePicker === 'undefined') return;

				const isSingle = $dates.length === 1;
				const $start = $dates.eq(0);
				const $end = isSingle ? null : $dates.eq(1);

				const startId = $start.attr('id') || `dt-s-${Math.random().toString(36).substr(2, 7)}`;
				const triggerId = `dt-t-${Math.random().toString(36).substr(2, 7)}`;
				const displayId = `dt-d-${Math.random().toString(36).substr(2, 7)}`;

				$start.attr('id', startId).attr('type', 'hidden');
				let endId = null;
				let displayText = isSingle ? 'Select Date' : 'Select Date Range';

				if (!isSingle) {
					endId = $end.attr('id') || `dt-e-${Math.random().toString(36).substr(2, 7)}`;
					$end.attr('id', endId).attr('type', 'hidden');
					if ($start.val() && $end.val() && moment($start.val()).isValid() && moment($end.val()).isValid()) {
						displayText = `${moment($start.val()).format('DD/MM/YYYY')} to ${moment($end.val()).format('DD/MM/YYYY')}`;
					}
				} else if ($start.val() && moment($start.val()).isValid()) {
					displayText = moment($start.val()).format('DD/MM/YYYY');
				}

				$chip.contents().filter(function () { return this.nodeType === 3 || $(this).is('span, i'); }).remove();
				$chip.append(`
					<div class="dt-datepicker-trigger-wrapper" style="width: 100%;">
						<div class="dt-datepicker-trigger" id="${triggerId}" style="width: 100%;">
							<i class="far fa-calendar-alt dt-datepicker-trigger-icon"></i>
							<span id="${displayId}">${displayText}</span>
							<i class="fas fa-chevron-down dt-datepicker-trigger-chevron"></i>
						</div>
					</div>`).css('width', '100%');

				new PremiumDateRangePicker(`#${triggerId}`, {
					startInput: `#${startId}`,
					endInput: endId ? `#${endId}` : null,
					displayText: `#${displayId}`,
					singleDate: isSingle
				});
			});
		},

		initHealthCheck() {
			if (!window.BASE_URL) return;
			setInterval(() => {
				$.ajax({
					url: `${window.BASE_URL}health/db-check`,
					type: 'GET',
					dataType: 'json',
					timeout: 3000
				}).fail((xhr, status, err) => {
					console.warn('Database health check request failed:', status, err);
				});
			}, 120000);
		}
	};

	// ── Master Initialization ──
	$(document).ready(() => {
		SidebarModule.init();
		TableResponsiveModule.init();
		FilterDrawerModule.init();
		FormHelperModule.init();
		ExtraFeatureModule.init();
	});

})(window, document, jQuery);
