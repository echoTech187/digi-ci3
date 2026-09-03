/**
 * GIDI Premium Date Range Picker Component
 * Utilizes Moment.js for date manipulation
 */

// ── 1. Date Preset Helper ──
const DatePresetHelper = {
    getRange(preset) {
        const today = moment().startOf('day');
        switch (preset) {
            case 'today':
                return { start: today.clone(), end: today.clone() };
            case 'yesterday':
                return { start: today.clone().subtract(1, 'day'), end: today.clone().subtract(1, 'day') };
            case 'last7':
                return { start: today.clone().subtract(6, 'days'), end: today.clone() };
            case 'last30':
                return { start: today.clone().subtract(29, 'days'), end: today.clone() };
            case 'thisMonth':
                return { start: today.clone().startOf('month'), end: today.clone() };
            case 'lastMonth':
                return {
                    start: today.clone().subtract(1, 'month').startOf('month'),
                    end: today.clone().subtract(1, 'month').endOf('month')
                };
            default:
                return null;
        }
    },

    matchActivePreset(startDate, endDate) {
        if (!startDate || !endDate) return 'custom';
        const s = startDate.format('YYYY-MM-DD');
        const e = endDate.format('YYYY-MM-DD');
        const today = moment().format('YYYY-MM-DD');
        const yest = moment().subtract(1, 'day').format('YYYY-MM-DD');

        if (s === today && e === today) return 'today';
        if (s === yest && e === yest) return 'yesterday';
        if (s === moment().subtract(6, 'days').format('YYYY-MM-DD') && e === today) return 'last7';
        if (s === moment().subtract(29, 'days').format('YYYY-MM-DD') && e === today) return 'last30';
        if (s === moment().startOf('month').format('YYYY-MM-DD') && e === today) return 'thisMonth';
        if (s === moment().subtract(1, 'month').startOf('month').format('YYYY-MM-DD') &&
            e === moment().subtract(1, 'month').endOf('month').format('YYYY-MM-DD')) return 'lastMonth';
        return 'custom';
    }
};

// ── 2. Position Helper ──
const DatePositionHelper = {
    position($popover, $trigger) {
        if (!$popover.length || !$trigger.length) return;
        const rect = $trigger[0].getBoundingClientRect();
        const vWidth = window.innerWidth;
        const vHeight = window.innerHeight;
        const pWidth = $popover.outerWidth() || 736;
        const pHeight = $popover.outerHeight() || 350;

        let left = rect.left;
        if (left + pWidth > vWidth - 24) left = vWidth - pWidth - 24;
        if (left < 24) left = 24;

        let top = (rect.bottom + pHeight + 8 > vHeight && rect.top - pHeight - 8 > 0)
            ? rect.top - pHeight - 8
            : rect.bottom + 8;

        $popover.css({ top: `${top}px`, left: `${left}px` });
    }
};

// ── 3. Main Premium Date Range Picker Component ──
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
        this.$popover = $(`
            <div class="dt-datepicker-popover ${this.options.singleDate ? 'dt-datepicker-single' : ''}">
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
                        <div class="dt-datepicker-calendar" id="cal-left">
                            <div class="dt-datepicker-header">
                                <button type="button" class="btn-cal-prev"><i class="fas fa-chevron-left"></i></button>
                                <span class="cal-title">Month Year</span>
                                <span style="width:28px;"></span>
                            </div>
                            <div class="dt-datepicker-days-header"><span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span></div>
                            <div class="dt-datepicker-days-grid"></div>
                        </div>
                        <div class="dt-datepicker-calendar" id="cal-right">
                            <div class="dt-datepicker-header">
                                <span style="width:28px;"></span>
                                <span class="cal-title">Month Year</span>
                                <button type="button" class="btn-cal-next"><i class="fas fa-chevron-right"></i></button>
                            </div>
                            <div class="dt-datepicker-days-header"><span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span></div>
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
        $('body').append(this.$popover);
    }

    bindEvents() {
        const self = this;
        this.$trigger.on('click', (e) => { e.stopPropagation(); self.togglePopover(); });
        this.$popover.find('.btn-cal-prev').on('click', (e) => { e.stopPropagation(); self.leftCalendarDate.subtract(1, 'month'); self.renderCalendars(); });
        this.$popover.find('.btn-cal-next').on('click', (e) => { e.stopPropagation(); self.leftCalendarDate.add(1, 'month'); self.renderCalendars(); });
        this.$popover.find('.dt-datepicker-presets li').on('click', function(e) { e.stopPropagation(); self.applyPreset($(this).data('preset')); });

        this.$popover.find('.btn-cal-today').on('click', (e) => {
            e.stopPropagation();
            self.leftCalendarDate = moment().startOf('month');
            self.tempStartDate = moment().startOf('day');
            self.tempEndDate = moment().startOf('day');
            self.renderCalendars();
            self.updateFooterPreview();
            self.$popover.find('.dt-datepicker-presets li').removeClass('active');
            self.$popover.find('[data-preset="today"]').addClass('active');
        });

        this.$popover.find('.btn-cal-apply').on('click', (e) => { e.stopPropagation(); self.applySelection(); });
        this.$popover.on('click', (e) => e.stopPropagation());

        $(document).on('click', (e) => {
            if (!self.$popover.hasClass('open')) return;
            if (!$(e.target).closest('.dt-datepicker-popover').length && !$(e.target).closest(self.$trigger).length) self.closePopover();
        });

        $(window).on('scroll.dtdatepicker resize.dtdatepicker', () => {
            if (self.$popover.hasClass('open')) self.positionPopover();
        });

        this.$popover.on('mouseenter', '.dt-datepicker-day:not(.empty)', function() {
            if (self.tempStartDate && !self.tempEndDate) self.renderHoverPreview(moment($(this).data('date')));
        });
    }

    togglePopover() {
        if (this.$popover.hasClass('open')) {
            this.closePopover();
        } else {
            this.tempStartDate = this.startDate ? this.startDate.clone() : null;
            this.tempEndDate = this.endDate ? this.endDate.clone() : null;
            this.leftCalendarDate = this.startDate ? this.startDate.clone().startOf('month') : moment().startOf('month');
            this.renderCalendars();
            this.updateFooterPreview();
            this.highlightActivePreset();
            this.positionPopover();
            this.popoverOpenState(true);
        }
    }

    positionPopover() {
        DatePositionHelper.position(this.$popover, this.$trigger);
    }

    closePopover() {
        this.popoverOpenState(false);
    }

    popoverOpenState(isOpen) {
        this.$popover.toggleClass('open', isOpen);
        this.$trigger.parent().toggleClass('open', isOpen);
    }

    renderCalendars() {
        this.renderCalendarGrid('#cal-left', this.leftCalendarDate.clone());
        this.renderCalendarGrid('#cal-right', this.leftCalendarDate.clone().add(1, 'month'));
    }

    renderCalendarGrid(selector, monthMoment) {
        const $cal = this.$popover.find(selector);
        $cal.find('.cal-title').text(monthMoment.format('MMMM, YYYY'));
        const $grid = $cal.find('.dt-datepicker-days-grid').empty();

        const firstDay = monthMoment.clone().startOf('month');
        const daysInMonth = monthMoment.daysInMonth();
        const startDayOfWeek = firstDay.day();

        for (let i = 0; i < startDayOfWeek; i++) {
            $grid.append('<div class="dt-datepicker-day empty"></div>');
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const dateStr = monthMoment.clone().date(day).format('YYYY-MM-DD');
            const dayMoment = moment(dateStr);
            const $dayCell = $(`<div class="dt-datepicker-day" data-date="${dateStr}">${day}</div>`);

            if (this.tempStartDate && dateStr === this.tempStartDate.format('YYYY-MM-DD')) {
                $dayCell.addClass('start-date');
                if (this.options.singleDate) $dayCell.addClass('end-date');
            }
            if (!this.options.singleDate) {
                if (this.tempEndDate && dateStr === this.tempEndDate.format('YYYY-MM-DD')) $dayCell.addClass('end-date');
                if (this.tempStartDate && this.tempEndDate && dayMoment.isBetween(this.tempStartDate, this.tempEndDate, 'day', '()')) {
                    $dayCell.addClass('in-range');
                }
            }

            $dayCell.on('click', (e) => { e.stopPropagation(); this.selectDate(dayMoment); });
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
            this.tempStartDate = dateMoment.clone();
            this.tempEndDate = null;
            this.$popover.find('.dt-datepicker-presets li').removeClass('active');
            this.$popover.find('[data-preset="custom"]').addClass('active');
        } else {
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
        if (this.options.singleDate || !this.tempStartDate || this.tempEndDate) return;
        const startStr = this.tempStartDate.format('YYYY-MM-DD');
        const hoverStr = hoverDate.format('YYYY-MM-DD');

        this.$popover.find('.dt-datepicker-day:not(.empty)').each((_, el) => {
            const cellDateStr = $(el).data('date');
            const $cell = $(el);
            $cell.removeClass('in-range end-date');
            if (cellDateStr === hoverStr && hoverDate.isAfter(this.tempStartDate, 'day')) {
                $cell.addClass('end-date');
            } else if (cellDateStr > startStr && cellDateStr < hoverStr) {
                $cell.addClass('in-range');
            }
        });

        this.$popover.find('.dt-datepicker-range-preview').text(`${this.tempStartDate.format('DD/MM/YYYY')} to ${hoverDate.format('DD/MM/YYYY')}`);
    }

    updateFooterPreview() {
        let previewText = 'Select date...';
        if (this.options.singleDate) {
            if (this.tempStartDate) previewText = this.tempStartDate.format('DD/MM/YYYY');
        } else {
            if (this.tempStartDate) {
                previewText = this.tempStartDate.format('DD/MM/YYYY') + (this.tempEndDate ? ` to ${this.tempEndDate.format('DD/MM/YYYY')}` : ' to ...');
            } else {
                previewText = 'Select range...';
            }
        }
        this.$popover.find('.dt-datepicker-range-preview').text(previewText);
    }

    applyPreset(preset) {
        const range = DatePresetHelper.getRange(preset);
        if (!range) return;

        this.tempStartDate = range.start;
        this.tempEndDate = range.end;
        this.leftCalendarDate = this.tempStartDate.clone().startOf('month');

        this.$popover.find('.dt-datepicker-presets li').removeClass('active');
        this.$popover.find(`[data-preset="${preset}"]`).addClass('active');

        this.renderCalendars();
        this.updateFooterPreview();
    }

    highlightActivePreset() {
        this.$popover.find('.dt-datepicker-presets li').removeClass('active');
        if (this.options.singleDate) return;
        const activePreset = DatePresetHelper.matchActivePreset(this.startDate, this.endDate);
        this.$popover.find(`[data-preset="${activePreset}"]`).addClass('active');
    }

    updateTriggerText() {
        if (this.options.singleDate) {
            this.$displayText.text(this.startDate ? this.startDate.format('DD/MM/YYYY') : 'Select Date');
        } else {
            this.$displayText.text((this.startDate && this.endDate) ? `${this.startDate.format('DD/MM/YYYY')} to ${this.endDate.format('DD/MM/YYYY')}` : 'Select Date Range');
        }
    }

    applySelection() {
        if (this.options.singleDate) {
            if (!this.tempStartDate) {
                this.showWarning('Invalid Date', 'Please select a date.');
                return;
            }
            this.startDate = this.tempStartDate.clone();
            this.endDate = this.startDate.clone();
            this.$startInput.val(this.startDate.format('YYYY-MM-DD'));
        } else {
            if (!this.tempStartDate || !this.tempEndDate) {
                this.showWarning('Invalid Range', 'Please select both a start and an end date.');
                return;
            }
            this.startDate = this.tempStartDate.clone();
            this.endDate = this.tempEndDate.clone();
            this.$startInput.val(this.startDate.format('YYYY-MM-DD'));
            this.$endInput.val(this.endDate.format('YYYY-MM-DD'));
        }

        this.updateTriggerText();
        this.closePopover();

        if (typeof this.options.onApply === 'function') {
            this.options.onApply(this.startDate, this.endDate);
        } else {
            this.$startInput.trigger('change');
            if (this.$endInput) this.$endInput.trigger('change');
        }
    }

    showWarning(title, text) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title,
                text,
                icon: 'warning',
                customClass: { popup: 'swal2-premium-popup', confirmButton: 'swal2-premium-confirm' },
                buttonsStyling: false
            });
        }
    }
}
