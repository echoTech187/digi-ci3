<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js'></script>

<!-- Begin Page Content -->
<div class="container-fluid pb-4">

    <!-- ── Page Header ── -->
    <div class="dt-page-header d-flex align-items-center justify-content-between">
        <div>
            <h4 class="dt-page-title">Holiday Calendar</h4>
            <p class="dt-page-subtitle">Manage and visualize public holidays across the system.</p>
        </div>
        
    </div>

    <!-- ── Alert Messages ── -->
    <?php if ($this->session->flashdata('success')) : ?>
        <div class="alert alert-success border-0 shadow-sm animate__animated animate__fadeIn mb-4" style="border-radius: 12px; background: #ecfdf5; color: #065f46; border-left: 4px solid #10b981 !important;">
            <i class="fas fa-check-circle mr-2"></i> <?= $this->session->flashdata('success'); ?>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')) : ?>
        <div class="alert alert-danger border-0 shadow-sm animate__animated animate__fadeIn mb-4" style="border-radius: 12px; background: #fef2f2; color: #991b1b; border-left: 4px solid #ef4444 !important;">
            <i class="fas fa-exclamation-circle mr-2"></i> <?= $this->session->flashdata('error'); ?>
        </div>
    <?php endif; ?>

    <!-- ── Calendar Card ── -->
    <div class="card border-0 shadow-sm dt-card" style="border-radius: 20px; overflow: hidden;">
        <div class="dt-toolbar border-0 d-flex align-items-center justify-content-between" style="padding: 24px; border-bottom: 1px solid var(--border-color) !important;">
            <div class="d-flex align-items-center gap-4">
                <div class="d-flex align-items-center gap-2">
                    <div style="width: 10px; height: 10px; border-radius: 50%; background: var(--primary); box-shadow: 0 0 0 4px var(--primary-soft);"></div>
                    <span style="font-size: 13px; font-weight: 700; color: #4a5568;">Active Holiday</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div style="width: 10px; height: 10px; border-radius: 50%; background: #e2e8f0; box-shadow: 0 0 0 4px #f1f5f9;"></div>
                    <span style="font-size: 13px; font-weight: 700; color: #94a3b8;">Inactive / Disabled</span>
                </div>
            </div>
            <button type="button" class="btn-dt-chip-action btn-dt-action-primary border-0 px-4" onclick="openAddHolidayModal()">
                <i class="fas fa-calendar-plus"></i> Add New Holiday
            </button>
        </div>
        <div class="card-body p-4">
            <div id="calendar" style="min-height: 700px;"></div>
        </div>
    </div>

</div>

<!-- ── Add / Edit Holiday Modal ── -->
<div class="modal fade" id="settingHolidayModal" tabindex="-1" role="dialog" aria-labelledby="settingHolidayModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
            <div class="modal-header border-0 py-4" style="background: linear-gradient(135deg, var(--primary) 0%, #4f39ce 100%) !important; color: white;">
                <h6 class="modal-title font-weight-bold" id="settingHolidayModalTitle" style="font-size: 1.1rem; letter-spacing: 0.5px;">
                    <i class="fas fa-calendar-check mr-2"></i>MANAGE HOLIDAY
                </h6>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.8; outline: none;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" action="<?= base_url('admin/manageHoliday'); ?>">
                <div class="modal-body p-4 text-dark">
                    <div class="mb-4">
                        <label class="mb-2 d-block" style="font-weight: 700; color: #374151; font-size: 13px;">Selected Date</label>
                        <div class="input-group-custom position-relative">
                            <i class="fas fa-calendar-day position-absolute" style="left: 16px; top: 50%; transform: translateY(-50%); color: var(--primary); z-index: 5;"></i>
                            <input class="form-control"
                                   style="height: 54px; padding-left: 48px; border-radius: 14px; border: 1.5px solid #e5e7eb; background: #f9fafb; font-weight: 600; color: #111827;"
                                   type="text"
                                   id="c_date"
                                   name="c_date"
                                   readonly
                                   required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="mb-2 d-block" style="font-weight: 700; color: #374151; font-size: 13px;">Holiday Name / Description <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               required 
                               id="c_desc" 
                               name="c_desc" 
                               placeholder="e.g. Lunar New Year"
                               style="height: 54px; border-radius: 14px; border: 1.5px solid #e5e7eb; padding: 0 16px; font-size: 14px; width: 100%; transition: all 0.2s;">
                    </div>

                    <div class="mb-4">
                        <label class="mb-2 d-block" style="font-weight: 700; color: #374151; font-size: 13px;">Availability Status <span class="text-danger">*</span></label>
                        <select class="form-control" 
                                id="c_status" 
                                required 
                                name="c_status"
                                style="height: 54px; border-radius: 14px; border: 1.5px solid #e5e7eb; padding: 0 16px; font-size: 14px; width: 100%;">
                            <option value="" selected disabled>Select status</option>
                            <option value="Active">Active Holiday</option>
                            <option value="Not Active">Not Active (Regular Day)</option>
                        </select>
                    </div>

                    <input type="hidden" id="c_action" name="c_action" value="create"/>
                </div>
                <div class="modal-footer px-4 pb-4 border-0">
                    <button type="button" class="btn btn-light" data-dismiss="modal" style="height: 50px; border-radius: 12px; font-weight: 700; color: #6b7280; background: #f3f4f6; border: none; padding: 0 24px;">Cancel</button>
                    <button type="submit" class="btn" style="height: 50px; border-radius: 12px; font-weight: 700; color: #fff; background: var(--primary); border: none; padding: 0 32px; box-shadow: 0 4px 14px var(--primary-glow);">
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
    var calendarEl = document.getElementById('calendar');
    
    // Fix: access properties directly from the raw event object
    const rawEvents = <?= json_encode($holidays) ?> || [];
    const formattedEvents = rawEvents.map(event => {
        // Handle both flat property and extendedProps (if any)
        const status = event.status || (event.extendedProps && event.extendedProps.status);
        return {
            ...event,
            classNames: [status === 'Active' ? 'holiday-active' : 'holiday-inactive']
        };
    });

    calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'multiMonthYear',
      headerToolbar: {
        left:   'prev,next today',
        center: 'title',
        right:  'multiMonthYear,dayGridMonth'
      },
      multiMonthMaxColumns: 3,
      events: formattedEvents,
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