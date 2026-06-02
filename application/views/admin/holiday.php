<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js'></script>


<!-- Begin Page Content -->
<div class=" w-100">
    <!-- ── Toggleable Page Instructional Drawer ── -->
    <div class="drawer-overlay" id="instructionOverlay"></div>
    <div class="drawer-right" id="instructionDrawer">
        <div class="drawer-header">
            <h6 class="drawer-title"><i class="fas fa-book mr-2"></i> Holiday Calendar Guide</h6>
            <button type="button" class="drawer-close" id="closeDrawerBtn">&times;</button>
        </div>
        <div class="drawer-body">
            <p class="drawer-desc">Configure public holidays that affect settlement delays and payout clearing schedules.</p>
            
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-calendar-alt text-primary mr-2"></i> Holiday Setting</div>
                <p class="drawer-card-text">Add national holidays to postpone settlement payouts automatically.</p>
            </div>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-calendar-day text-primary mr-2"></i> Calendar View</div>
                <p class="drawer-card-text">Interactive FullCalendar representation to visually check upcoming non-working days.</p>
            </div>
            <div class="drawer-card">
                <div class="drawer-card-title"><i class="fas fa-sync text-primary mr-2"></i> System Sync</div>
                <p class="drawer-card-text">Automated calculation adjustments for T+1 / T+2 settlement schedules based on holiday dates.</p>
            </div>
        </div>
    </div>


    <!-- ── Page Header ── -->
    <div class="dt-page-header d-flex align-items-center justify-content-between">
        <div>
            <h4 class="dt-page-title">Holiday Calendar</h4>
            <p class="dt-page-subtitle">Manage and visualize public holidays across the system.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-light border shadow-sm mr-2 d-flex align-items-center" id="toggleGuideBtn">
                <i class="fas fa-book-open text-primary mr-2"></i> <span class="d-none d-md-block">Instructions Guide</span>
            </button>
        </div>
    </div>

    <!-- Alerts Standardized to Swal2 Premium -->
    <script>
        $(document).ready(function() {
            <?php if ($this->session->flashdata('success')) : ?>
                Swal.fire({
                    title: 'Success!',
                    text: '<?= $this->session->flashdata('success'); ?>',
                    icon: 'success',
                    customClass: {
                        popup: 'swal2-premium-popup',
                        confirmButton: 'swal2-premium-confirm'
                    },
                    buttonsStyling: false
                });
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')) : ?>
                Swal.fire({
                    title: 'Error!',
                    html: '<?= trim(str_replace(["\r", "\n"], '', $this->session->flashdata('error'))); ?>',
                    icon: 'error',
                    customClass: {
                        popup: 'swal2-premium-popup',
                        confirmButton: 'swal2-premium-confirm'
                    },
                    buttonsStyling: false
                });
            <?php endif; ?>
        });
    </script>

    <!-- ── Desktop Calendar ── -->
    <div class="d-none d-md-block">
        <div class="fc-card border-0 shadow-sm dt-card" style="border-radius: 20px; overflow: hidden;">
            <div class="dt-toolbar border-0 d-flex align-items-center justify-content-between flex-wrap gap-3" style="padding: 16px 20px; border-bottom: 1px solid var(--border-color) !important;">
                <div class="d-flex align-items-center flex-wrap gap-2 gap-md-4">
                    <div class="legend-item-custom">
                        <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--danger); box-shadow: 0 0 0 3px var(--danger-soft);"></div>
                        <span>Active <span class="d-none d-sm-inline">Holiday</span></span>
                    </div>
                    <div class="legend-item-custom">
                        <div style="width: 8px; height: 8px; border-radius: 50%; background: #94a3b8; box-shadow: 0 0 0 3px rgba(148, 163, 184, 0.2);"></div>
                        <span>Inactive <span class="d-none d-sm-inline">/ Disabled</span></span>
                    </div>
                </div>
                <button type="button" class="btn-dt-action btn-dt-action-success border-0 px-3 px-md-4" style="height: 38px;" onclick="openAddHolidayModal()">
                    <i class="fas fa-calendar-plus"></i> <span class="d-none d-md-inline">Add New Holiday</span>
                </button>
            </div>
            <div class="card-body p-2 p-md-4">
                <div id="calendar" style="min-height: 700px;"></div>
            </div>
        </div>
    </div>

    <!-- ── Mobile Premium Experience ── -->
    <div class="d-md-none animate__animated animate__fadeIn">
        <div class="fc-mobile-card mb-4">
            <div id="calendar-mobile"></div>
        </div>
        
        <div class="d-flex align-items-center justify-content-between mb-3 px-1">
            <h5 class="mb-0 fw-bold" style="font-size: 1.1rem; letter-spacing: -0.02em;">MONTHLY EVENTS</h5>
            <span class="badge badge-soft-primary px-3 py-2" style="border-radius: 10px; font-size: 11px;" id="mobile-event-count">0 Holidays</span>
        </div>

        <div id="mobile-event-list" class="mobile-event-container">
            <!-- Dynamic list will be rendered here -->
        </div>
    </div>

</div>

<!-- Floating Action Button (Mobile) -->
<button class="fab-btn d-md-none" onclick="openAddHolidayModal()" title="Add Holiday">
    <i class="fas fa-plus"></i>
</button>

<!-- ── Add / Edit Holiday Modal ── -->
<div class="modal fade" data-backdrop="static" data-keyboard="false" id="settingHolidayModal" tabindex="-1" role="dialog" aria-labelledby="settingHolidayModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
            <div class="modal-header border-0 py-4" style="background: linear-gradient(135deg, var(--primary) 0%, #4f39ce 100%) !important; color: white;">
                <h6 class="modal-title font-weight-bold" id="settingHolidayModalTitle" style="font-size: 1.1rem; letter-spacing: 0.5px;">
                    <i class="fas fa-calendar-check  mr-2"></i> MANAGE HOLIDAY
                </h6>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.8; outline: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="<?= base_url('access-control/holiday/manage'); ?>">
                <div class="modal-body p-0">
                    <div class="d-flex g-0 w-100 flex-column flex-lg-row">
                        <div class="col-lg-5 p-4 d-flex flex-column mb-0 modal-guide-sidebar">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width:40px;height:40px;flex-shrink:0;"><i class="fas fa-calendar-alt fa-lg"></i></div>
                                <h6 class="fw-bold text-primary mb-0" style="font-size:15px;">Holiday Guide</h6>
                            </div>
                            <p class="text-muted small mb-3" style="font-size:12px;line-height:1.5;">Configure public holidays that affect settlement delays and payout clearing schedules.</p>
                            <div class="modal-guide-card mb-3">
                                <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size:12px;"><i class="fas fa-clock text-warning mr-2"></i> Settlement Impact</h6>
                                <p class="mb-0" style="font-size:11px;line-height:1.4;">Active holidays will delay T+1/T+2 settlement calculations. Payouts are automatically shifted to the next business day.</p>
                            </div>
                            <div class="modal-guide-card">
                                <h6 class="fw-bold mb-1 d-flex align-items-center" style="font-size:12px;"><i class="fas fa-info-circle text-info mr-2"></i> Status Options</h6>
                                <p class="mb-0" style="font-size:11px;line-height:1.4;"><b>Active</b> = Non-working day (delays payouts). <b>Not Active</b> = Regular business day.</p>
                            </div>
                        </div>
                        <div class="col-lg-7 p-4 modal-input-pane mb-0">
                            <div class="mb-4">
                                <label>Selected Date</label>
                                <div class="input-group-custom position-relative">
                                    <i class="fas fa-calendar-day position-absolute" style="left: 16px; top: 50%; transform: translateY(-50%); color: var(--primary); z-index: 5;"></i>
                                    <input class="form-control modal-input-custom has-icon"
                                           type="text"
                                           id="c_date"
                                           name="c_date"
                                           readonly
                                           required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label>Holiday Name / Description <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control modal-input-custom" 
                                       required 
                                       id="c_desc" 
                                       name="c_desc" 
                                       placeholder="e.g. Lunar New Year">
                            </div>

                            <div class="mb-4">
                                <label>Availability Status <span class="text-danger">*</span></label>
                                <select class="form-control modal-input-custom" 
                                        id="c_status" 
                                        required 
                                        name="c_status">
                                    <option value="" selected disabled>Select status</option>
                                    <option value="Active">Active Holiday</option>
                                    <option value="Not Active">Not Active (Regular Day)</option>
                                </select>
                            </div>

                            <input type="hidden" id="c_action" name="c_action" value="create"/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-4 pb-4 border-0">
                    <button type="button" class="btn btn-modal-cancel" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-modal-submit">
                        Save Holiday
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
  let calendar;

  function openAddHolidayModal() {
      const today = new Date().toISOString().split('T')[0];
      resetModal('create', today);
      $('#settingHolidayModal').modal('show');
  }

  function resetModal(action, date = '', desc = '', status = '') {
      document.getElementById('c_action').value = action;
      document.getElementById('c_date').value   = date;
      document.getElementById('c_desc').value   = desc;
      document.getElementById('c_status').value = status;

      const title = action === 'create' ? 'ADD HOLIDAY' : 'EDIT HOLIDAY';
      const icon = action === 'create' ? 'fa-calendar-check' : 'fa-edit';
      
      document.getElementById('settingHolidayModalTitle').innerHTML =
          `<i class="fas ${icon} mr-3"></i>${title} &nbsp;—&nbsp; <span style="font-weight: 400; opacity: 0.8;">${date}</span>`;
  }

  document.addEventListener('DOMContentLoaded', function() {
    const isMobile = window.innerWidth < 768;
    const calendarEl = document.getElementById(isMobile ? 'calendar-mobile' : 'calendar');
    
    // Fix: access properties directly from the raw event object
    const rawEvents = <?= json_encode($holidays) ?> || [];
    const formattedEvents = rawEvents.map(event => {
        const status = event.status || (event.extendedProps && event.extendedProps.status);
        return {
            ...event,
            display: 'background',
            classNames: [status === 'Active' ? 'holiday-active' : 'holiday-inactive']
        };
    });

    function renderMobileEventList(view) {
        if (!isMobile) return;
        
        const listContainer = document.getElementById('mobile-event-list');
        const countBadge = document.getElementById('mobile-event-count');
        const currentMonth = view.currentStart.getMonth();
        const currentYear = view.currentStart.getFullYear();

        const monthlyEvents = formattedEvents.filter(ev => {
            const evDate = new Date(ev.start);
            return evDate.getMonth() === currentMonth && evDate.getFullYear() === currentYear;
        }).sort((a,b) => new Date(a.start) - new Date(b.start));

        countBadge.textContent = `${monthlyEvents.length} Holiday${monthlyEvents.length !== 1 ? 's' : ''}`;

        if (monthlyEvents.length === 0) {
            listContainer.innerHTML = `
                <div class="text-center py-5">
                    <div class="mb-3"><i class="fas fa-calendar-day fa-3x text-light"></i></div>
                    <p class="text-muted small fw-bold">No holidays found for this month.</p>
                </div>`;
            return;
        }

        listContainer.innerHTML = monthlyEvents.map(ev => `
            <div class="mobile-event-item animate__animated animate__fadeInUp" onclick="openEditModal('${ev.start}', '${ev.desc || ev.title}', '${ev.status}')">
                <div class="mobile-event-date">
                    <span class="day">${new Date(ev.start).getDate()}</span>
                    <span class="month">${new Date(ev.start).toLocaleString('default', { month: 'short' })}</span>
                </div>
                <div class="mobile-event-content">
                    <div class="title">${ev.desc || ev.title}</div>
                    <div class="status">
                        <span class="badge ${ev.status === 'Active' ? 'badge-soft-danger' : 'badge-soft-secondary'} px-2 py-1" style="font-size: 10px; border-radius: 6px;">
                            ${ev.status}
                        </span>
                    </div>
                </div>
                <div class="mobile-event-arrow">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </div>
        `).join('');
    }

    // Helper for mobile list clicks
    window.openEditModal = function(date, desc, status) {
        resetModal('update', date, desc, status);
        $('#settingHolidayModal').modal('show');
    };

    calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: isMobile ? 'dayGridMonth' : 'multiMonthYear',
      headerToolbar: isMobile ? {
        left:   'prev,next',
        center: 'title',
        right:  'today'
      } : {
        left:   'prev,next today',
        center: 'title',
        right:  'multiMonthYear,dayGridMonth'
      },
      multiMonthMaxColumns: 3,
      datesSet: function(info) {
          if (isMobile) renderMobileEventList(info.view);
      },
      events: formattedEvents,
      eventDidMount: function(info) {
          const title = info.event.extendedProps.desc || info.event.title;
          const status = info.event.extendedProps.status;
          const tooltipText = `${title} (${status === 'Active' ? 'Active Holiday' : 'Inactive'})`;
          
          $(info.el).attr('data-toggle', 'tooltip');
          $(info.el).attr('data-placement', 'top');
          $(info.el).attr('title', tooltipText);
          $(info.el).tooltip({
              container: 'body',
              trigger: 'hover'
          });
      },
      eventWillUnmount: function(info) {
          $(info.el).tooltip('dispose');
      },
      dateClick: function(e) {
        const date = e.dateStr;
        const existingEvent = calendar.getEvents().find(ev => {
            const evDate = ev.startStr || ev.start.toISOString().split('T')[0];
            return evDate === date;
        });
        
        if (existingEvent) {
          resetModal('update', date, existingEvent.extendedProps.desc || existingEvent.title, existingEvent.extendedProps.status);
        } else {
          resetModal('create', date);
        }

        $('#settingHolidayModal').modal('show');
      },
      eventClick: function(info) {
          const date = info.event.startStr || info.event.start.toISOString().split('T')[0];
          resetModal('update', date, info.event.extendedProps.desc || info.event.title, info.event.extendedProps.status);
          $('#settingHolidayModal').modal('show');
      }
    });
    calendar.render();
  });
</script>



<script>
$(document).ready(function() {
    // Drawer Toggle Logic
    $('#toggleGuideBtn').on('click', function() {
        $('#instructionDrawer, #instructionOverlay').addClass('open');
        $('body').css('overflow', 'hidden');
    });

    $('#closeDrawerBtn, #instructionOverlay').on('click', function() {
        $('#instructionDrawer, #instructionOverlay').removeClass('open');
        $('body').css('overflow', '');
    });
});
</script>
