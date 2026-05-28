/**
 * GIDI Premium Date Range Picker Component
 * Utilizes Moment.js for date manipulation
 */

class PremiumDateRangePicker {
    constructor(triggerSelector, options = {}) {
        this.$trigger = $(triggerSelector);
        if (!this.$trigger.length) return;

        this.options = $.extend({
            startInput: '#overview-start-date',
            endInput: '#overview-end-date',
            displayText: '#overview-date-range-display',
            singleDate: false,
            onApply: null
        }, options);

        this.$startInput = $(this.options.startInput);
        this.$endInput = this.options.singleDate ? null : $(this.options.endInput);
        this.$displayText = $(this.options.displayText);

        // Date states (read initial values from input fields)
        const startVal = this.$startInput.val();
        
        this.startDate = (startVal && moment(startVal).isValid()) ? moment(startVal) : null;
        if (this.options.singleDate) {
            this.endDate = this.startDate;
        } else {
            const endVal = this.$endInput.val();
            this.endDate = (endVal && moment(endVal).isValid()) ? moment(endVal) : null;
        }
        
        this.tempStartDate = this.startDate ? this.startDate.clone() : null;
        this.tempEndDate = this.endDate ? this.endDate.clone() : null;

        // Calendar viewing state (Left calendar starts at startDate's month, or current month if null)
        this.leftCalendarDate = this.startDate ? this.startDate.clone().startOf('month') : moment().startOf('month');

        this.init();
    }

    init() {
        this.createPopoverHtml();
        this.bindEvents();
        this.renderCalendars();
        this.updateTriggerText();
        this.highlightActivePreset();
    }

    createPopoverHtml() {
        // Create popover elements
        this.$popover = $(`
            <div class="dt-datepicker-popover">
                <div class="dt-datepicker-sidebar">
                    <ul class="dt-datepicker-presets">
                        <li data-preset="today">Today</li>
                        <li data-preset="yesterday">Yesterday</li>
                        <li data-preset="last7">Last 7 Days</li>
                        <li data-preset="last30">Last 30 Days</li>
                        <li data-preset="thisMonth">This Month</li>
                        <li data-preset="lastMonth">Last Month</li>
                        <li data-preset="custom" class="active">Custom Range</li>
                    </ul>
                </div>
                <div class="dt-datepicker-main">
                    <div class="dt-datepicker-calendars">
                        <!-- Left Calendar -->
                        <div class="dt-datepicker-calendar" id="cal-left">
                            <div class="dt-datepicker-header">
                                <button type="button" class="btn-cal-prev"><i class="fas fa-chevron-left"></i></button>
                                <span class="cal-title">Month Year</span>
                                <span style="width:28px;"></span> <!-- Spacer -->
                            </div>
                            <div class="dt-datepicker-days-header">
                                <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
                            </div>
                            <div class="dt-datepicker-days-grid"></div>
                        </div>
                        <!-- Right Calendar -->
                        <div class="dt-datepicker-calendar" id="cal-right">
                            <div class="dt-datepicker-header">
                                <span style="width:28px;"></span> <!-- Spacer -->
                                <span class="cal-title">Month Year</span>
                                <button type="button" class="btn-cal-next"><i class="fas fa-chevron-right"></i></button>
                            </div>
                            <div class="dt-datepicker-days-header">
                                <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
                            </div>
                            <div class="dt-datepicker-days-grid"></div>
                        </div>
                    </div>
                    <div class="dt-datepicker-footer">
                        <button type="button" class="btn btn-sm btn-outline-secondary btn-cal-today">Today</button>
                        <div class="dt-datepicker-footer-right">
                            <span class="dt-datepicker-range-preview"></span>
                            <button type="button" class="btn btn-sm btn-primary btn-cal-apply">Apply</button>
                        </div>
                    </div>
                </div>
            </div>
        `);

        if (this.options.singleDate) {
            this.$popover.addClass('dt-datepicker-single');
        }

        // Append popover to body so it escapes all overflow contexts
        $('body').append(this.$popover);
    }

    bindEvents() {
        const self = this;

        // Toggle popover
        this.$trigger.on('click', function(e) {
            e.stopPropagation();
            self.togglePopover();
        });

        // Prev/Next month navigation
        this.$popover.find('.btn-cal-prev').on('click', function(e) {
            e.stopPropagation();
            self.leftCalendarDate.subtract(1, 'month');
            self.renderCalendars();
        });

        this.$popover.find('.btn-cal-next').on('click', function(e) {
            e.stopPropagation();
            self.leftCalendarDate.add(1, 'month');
            self.renderCalendars();
        });

        // Preset selection
        this.$popover.find('.dt-datepicker-presets li').on('click', function(e) {
            e.stopPropagation();
            const preset = $(this).data('preset');
            self.applyPreset(preset);
        });

        // Today footer button (moves view to current month)
        this.$popover.find('.btn-cal-today').on('click', function(e) {
            e.stopPropagation();
            self.leftCalendarDate = moment().startOf('month');
            self.tempStartDate = moment().startOf('day');
            self.tempEndDate = moment().startOf('day');
            self.renderCalendars();
            self.updateFooterPreview();
            self.$popover.find('.dt-datepicker-presets li').removeClass('active');
            self.$popover.find('[data-preset="today"]').addClass('active');
        });

        // Apply action button
        this.$popover.find('.btn-cal-apply').on('click', function(e) {
            e.stopPropagation();
            self.applySelection();
        });

        // Prevent clicks inside popover from bubbling to document and closing panels
        this.$popover.on('click', function(e) {
            e.stopPropagation();
        });

        // Click outside to close
        $(document).on('click', function(e) {
            if (!self.$popover.hasClass('open')) return;
            if (!$(e.target).closest('.dt-datepicker-popover').length && !$(e.target).closest(self.$trigger).length) {
                self.closePopover();
            }
        });

        // Reposition on scroll or resize while open
        $(window).on('scroll.dtdatepicker resize.dtdatepicker', function() {
            if (self.$popover.hasClass('open')) {
                self.positionPopover();
            }
        });

        // Handle cell hover preview range
        this.$popover.on('mouseenter', '.dt-datepicker-day:not(.empty)', function() {
            if (self.tempStartDate && !self.tempEndDate) {
                const hoverDate = moment($(this).data('date'));
                self.renderHoverPreview(hoverDate);
            }
        });
    }

    togglePopover() {
        if (this.$popover.hasClass('open')) {
            this.closePopover();
        } else {
            // Re-sync temp dates from original values
            this.tempStartDate = this.startDate ? this.startDate.clone() : null;
            this.tempEndDate = this.endDate ? this.endDate.clone() : null;
            this.leftCalendarDate = this.startDate ? this.startDate.clone().startOf('month') : moment().startOf('month');
            
            this.renderCalendars();
            this.updateFooterPreview();
            this.highlightActivePreset();
            
            // Position the fixed popover below the trigger element
            this.positionPopover();
            
            this.$popover.addClass('open');
            this.$trigger.parent().addClass('open');
        }
    }

    positionPopover() {
        const rect = this.$trigger[0].getBoundingClientRect();
        const viewportWidth = window.innerWidth;
        const viewportHeight = window.innerHeight;
        const popoverWidth = this.$popover.outerWidth() || 736;
        const popoverHeight = this.$popover.outerHeight() || 350;

        let left = rect.left;
        // Clamp so the popover doesn't go off-screen to the right (using 24px padding for aesthetics)
        if (left + popoverWidth > viewportWidth - 24) {
            left = viewportWidth - popoverWidth - 24;
        }
        if (left < 24) left = 24;

        // Check if there is enough space below the trigger, otherwise open above
        let top;
        if (rect.bottom + popoverHeight + 8 > viewportHeight && rect.top - popoverHeight - 8 > 0) {
            top = rect.top - popoverHeight - 8;
        } else {
            top = rect.bottom + 8;
        }

        this.$popover.css({
            top: top + 'px',
            left: left + 'px'
        });
    }

    closePopover() {
        this.$popover.removeClass('open');
        this.$trigger.parent().removeClass('open');
    }

    renderCalendars() {
        const leftMonth = this.leftCalendarDate.clone();
        const rightMonth = this.leftCalendarDate.clone().add(1, 'month');

        this.renderCalendarGrid('#cal-left', leftMonth);
        this.renderCalendarGrid('#cal-right', rightMonth);
    }

    renderCalendarGrid(calendarSelector, monthMoment) {
        const $cal = this.$popover.find(calendarSelector);
        $cal.find('.cal-title').text(monthMoment.format('MMMM, YYYY'));

        const $grid = $cal.find('.dt-datepicker-days-grid');
        $grid.empty();

        const firstDay = monthMoment.clone().startOf('month');
        const daysInMonth = monthMoment.daysInMonth();
        const startDayOfWeek = firstDay.day(); // 0 (Sunday) to 6 (Saturday)

        // Padding cells for empty leading days
        for (let i = 0; i < startDayOfWeek; i++) {
            $grid.append('<div class="dt-datepicker-day empty"></div>');
        }

        // Render actual month days
        for (let day = 1; day <= daysInMonth; day++) {
            const dateStr = monthMoment.clone().date(day).format('YYYY-MM-DD');
            const dayMoment = moment(dateStr);
            const $dayCell = $(`<div class="dt-datepicker-day" data-date="${dateStr}">${day}</div>`);

            // Apply active selection range styles
            if (this.tempStartDate && dateStr === this.tempStartDate.format('YYYY-MM-DD')) {
                $dayCell.addClass('start-date');
                if (this.options.singleDate) {
                    $dayCell.addClass('end-date');
                }
            }
            if (!this.options.singleDate) {
                if (this.tempEndDate && dateStr === this.tempEndDate.format('YYYY-MM-DD')) {
                    $dayCell.addClass('end-date');
                }
                if (this.tempStartDate && this.tempEndDate && dayMoment.isBetween(this.tempStartDate, this.tempEndDate, 'day', '()')) {
                    $dayCell.addClass('in-range');
                }
            }

            // Click listener for selecting dates
            const self = this;
            $dayCell.on('click', function(e) {
                e.stopPropagation();
                self.selectDate(dayMoment);
            });

            $grid.append($dayCell);
        }
    }

    selectDate(dateMoment) {
        if (this.options.singleDate) {
            this.tempStartDate = dateMoment.clone();
            this.tempEndDate = dateMoment.clone();
            this.renderCalendars();
            this.updateFooterPreview();
            this.applySelection();
            return;
        }

        if (!this.tempStartDate || (this.tempStartDate && this.tempEndDate)) {
            // First click (or reset): set start date
            this.tempStartDate = dateMoment.clone();
            this.tempEndDate = null;
            this.$popover.find('.dt-datepicker-presets li').removeClass('active');
            this.$popover.find('[data-preset="custom"]').addClass('active');
        } else if (this.tempStartDate && !this.tempEndDate) {
            // Second click: set end date
            if (dateMoment.isBefore(this.tempStartDate, 'day')) {
                this.tempStartDate = dateMoment.clone();
            } else {
                this.tempEndDate = dateMoment.clone();
            }
        }

        this.renderCalendars();
        this.updateFooterPreview();
    }

    renderHoverPreview(hoverDate) {
        if (this.options.singleDate) return;
        if (!this.tempStartDate || this.tempEndDate) return;

        const startStr = this.tempStartDate.format('YYYY-MM-DD');
        const hoverStr = hoverDate.format('YYYY-MM-DD');
        const self = this;

        this.$popover.find('.dt-datepicker-day:not(.empty)').each(function() {
            const cellDateStr = $(this).data('date');
            const $cell = $(this);

            // Reset non-primary range states
            $cell.removeClass('in-range end-date');

            if (cellDateStr === hoverStr && hoverDate.isAfter(self.tempStartDate, 'day')) {
                $cell.addClass('end-date');
            } else if (cellDateStr > startStr && cellDateStr < hoverStr) {
                $cell.addClass('in-range');
            }
        });
        
        // Update footer range preview dynamically on hover
        const previewText = `${this.tempStartDate.format('DD/MM/YYYY')} to ${hoverDate.format('DD/MM/YYYY')}`;
        this.$popover.find('.dt-datepicker-range-preview').text(previewText);
    }

    updateFooterPreview() {
        let previewText = '';
        if (this.options.singleDate) {
            if (this.tempStartDate) {
                previewText = this.tempStartDate.format('DD/MM/YYYY');
            } else {
                previewText = 'Select date...';
            }
        } else {
            if (this.tempStartDate) {
                previewText = this.tempStartDate.format('DD/MM/YYYY');
                if (this.tempEndDate) {
                    previewText += ' to ' + this.tempEndDate.format('DD/MM/YYYY');
                } else {
                    previewText += ' to ...';
                }
            } else {
                previewText = 'Select range...';
            }
        }
        this.$popover.find('.dt-datepicker-range-preview').text(previewText);
    }

    applyPreset(preset) {
        const today = moment().startOf('day');

        switch (preset) {
            case 'today':
                this.tempStartDate = today.clone();
                this.tempEndDate = today.clone();
                break;
            case 'yesterday':
                this.tempStartDate = today.clone().subtract(1, 'day');
                this.tempEndDate = today.clone().subtract(1, 'day');
                break;
            case 'last7':
                this.tempStartDate = today.clone().subtract(6, 'days');
                this.tempEndDate = today.clone();
                break;
            case 'last30':
                this.tempStartDate = today.clone().subtract(29, 'days');
                this.tempEndDate = today.clone();
                break;
            case 'thisMonth':
                this.tempStartDate = today.clone().startOf('month');
                this.tempEndDate = today.clone();
                break;
            case 'lastMonth':
                this.tempStartDate = today.clone().subtract(1, 'month').startOf('month');
                this.tempEndDate = today.clone().subtract(1, 'month').endOf('month');
                break;
            default:
                // Custom range - leave dates as is
                return;
        }

        // Adjust left view date to match new start date's month
        this.leftCalendarDate = this.tempStartDate.clone().startOf('month');
        
        this.$popover.find('.dt-datepicker-presets li').removeClass('active');
        this.$popover.find(`[data-preset="${preset}"]`).addClass('active');

        this.renderCalendars();
        this.updateFooterPreview();
    }

    highlightActivePreset() {
        this.$popover.find('.dt-datepicker-presets li').removeClass('active');
        if (this.options.singleDate) return;
        
        if (!this.startDate || !this.endDate) {
            this.$popover.find('[data-preset="custom"]').addClass('active');
            return;
        }

        const startStr = this.startDate.format('YYYY-MM-DD');
        const endStr = this.endDate.format('YYYY-MM-DD');
        const todayStr = moment().format('YYYY-MM-DD');
        
        if (startStr === todayStr && endStr === todayStr) {
            this.$popover.find('[data-preset="today"]').addClass('active');
        } else if (startStr === moment().subtract(1, 'day').format('YYYY-MM-DD') && endStr === moment().subtract(1, 'day').format('YYYY-MM-DD')) {
            this.$popover.find('[data-preset="yesterday"]').addClass('active');
        } else if (startStr === moment().subtract(6, 'days').format('YYYY-MM-DD') && endStr === todayStr) {
            this.$popover.find('[data-preset="last7"]').addClass('active');
        } else if (startStr === moment().subtract(29, 'days').format('YYYY-MM-DD') && endStr === todayStr) {
            this.$popover.find('[data-preset="last30"]').addClass('active');
        } else if (startStr === moment().startOf('month').format('YYYY-MM-DD') && endStr === todayStr) {
            this.$popover.find('[data-preset="thisMonth"]').addClass('active');
        } else if (startStr === moment().subtract(1, 'month').startOf('month').format('YYYY-MM-DD') && endStr === moment().subtract(1, 'month').endOf('month').format('YYYY-MM-DD')) {
            this.$popover.find('[data-preset="lastMonth"]').addClass('active');
        } else {
            this.$popover.find('[data-preset="custom"]').addClass('active');
        }
    }

    updateTriggerText() {
        if (this.options.singleDate) {
            if (this.startDate) {
                this.$displayText.text(this.startDate.format('DD/MM/YYYY'));
            } else {
                this.$displayText.text('Select Date');
            }
        } else {
            if (this.startDate && this.endDate) {
                const text = `${this.startDate.format('DD/MM/YYYY')} to ${this.endDate.format('DD/MM/YYYY')}`;
                this.$displayText.text(text);
            } else {
                this.$displayText.text('Select Date Range');
            }
        }
    }

    applySelection() {
        if (this.options.singleDate) {
            if (!this.tempStartDate) {
                Swal.fire({
                    title: 'Invalid Date',
                    text: 'Please select a date.',
                    icon: 'warning',
                    customClass: { popup: 'swal2-premium-popup', confirmButton: 'swal2-premium-confirm' },
                    buttonsStyling: false
                });
                return;
            }
            this.startDate = this.tempStartDate.clone();
            this.endDate = this.startDate.clone();

            this.$startInput.val(this.startDate.format('YYYY-MM-DD'));

            this.updateTriggerText();
            this.closePopover();

            if (typeof this.options.onApply === 'function') {
                this.options.onApply(this.startDate, this.endDate);
            } else {
                this.$startInput.trigger('change');
            }
            return;
        }

        if (!this.tempStartDate || !this.tempEndDate) {
            Swal.fire({
                title: 'Invalid Range',
                text: 'Please select both a start and an end date.',
                icon: 'warning',
                customClass: { popup: 'swal2-premium-popup', confirmButton: 'swal2-premium-confirm' },
                buttonsStyling: false
            });
            return;
        }

        // Save selected dates to actual properties
        this.startDate = this.tempStartDate.clone();
        this.endDate = this.tempEndDate.clone();

        // Write values to actual form inputs
        this.$startInput.val(this.startDate.format('YYYY-MM-DD'));
        this.$endInput.val(this.endDate.format('YYYY-MM-DD'));

        this.updateTriggerText();
        this.closePopover();

        // Trigger change events or run custom callbacks
        if (typeof this.options.onApply === 'function') {
            this.options.onApply(this.startDate, this.endDate);
        } else {
            this.$startInput.trigger('change');
            this.$endInput.trigger('change');
        }
    }
}
