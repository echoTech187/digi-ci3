<div id="module-ug-settlement-holidays" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">The <strong>Settlement Holidays</strong> module is used to configure public and national holidays that impact banking operations. Any date marked as an active holiday will automatically pause T+1/T+2 settlement calculations, shifting payout schedules to the next available business day.</p>

        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> UI Overview</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:25%">Feature</th>
                            <th class="p-3 border-0">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Interactive Calendar</strong></td><td class="p-3 border-0">Visualizes all active and inactive holidays across the month. Clicking a date allows you to instantly add or edit a holiday.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Holiday Date</strong></td><td class="p-3 border-0">The specific calendar date of the holiday that affects banking operations.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Description</strong></td><td class="p-3 border-0">The name or reason for the holiday (e.g., <em>Eid al-Fitr</em>, <em>Christmas Day</em>).</td></tr>
                        <tr><td class="p-3 border-0"><strong>Status Toggle</strong></td><td class="p-3 border-0">Can be set to <code>Active</code> (delays settlements) or <code>Inactive</code> (ignored by the settlement engine).</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Procedural Walkthrough -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Procedural Walkthrough</h4>
        
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">1</div>
                <h3 class="hc-step-title">Form Validations & Constraints</h3>
                <div class="table-responsive shadow-sm mb-4 mt-3" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                    <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                        <thead style="background: rgba(0,0,0,0.4);">
                            <tr>
                                <th class="p-3 border-0" style="width: 25%;">Constraint Type</th>
                                <th class="p-3 border-0">System Enforcement Rule</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td class="p-3 border-0"><strong>Required Fields</strong></td><td class="p-3 border-0"><code>Date</code>, <code>Holiday Name / Description</code>, and <code>Status</code> must be provided when adding a holiday.</td></tr>
                            <tr><td class="p-3 border-0"><strong>Unique Dates</strong></td><td class="p-3 border-0">Only one holiday record can exist per specific date.</td></tr>
                        </tbody>
                    </table>
                </div>

                <!-- System Notifications -->
                <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-bell text-info mr-2"></i> System Notifications</h6>
                <div class="d-flex flex-column mb-4">
                    <div class="mb-3">
                        <div class="p-3 rounded border" style="background-color: rgba(22, 163, 74, 0.05); border-color: rgba(22, 163, 74, 0.2) !important;">
                            <strong class="text-success d-block mb-2"><i class="fas fa-check-circle mr-1"></i> Success Events</strong>
                            <ul class="small text-muted mb-0 pl-3">
                                <li class="mb-0"><strong>Holiday Saved:</strong> <code>Holiday successfully added/updated.</code></li>
                            </ul>
                        </div>
                    </div>
                    <div class="mb-1">
                        <div class="p-3 rounded border" style="background-color: rgba(220, 38, 38, 0.05); border-color: rgba(220, 38, 38, 0.2) !important;">
                            <strong class="text-danger d-block mb-2"><i class="fas fa-exclamation-circle mr-1"></i> Error Events & Solutions</strong>
                            <ul class="small text-muted mb-0 pl-3">
                                <li class="mb-3">
                                    <strong>Duplicate Date (1062):</strong> <code>Failed to save: A holiday for this date already exists.</code>
                                    <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solution:</strong> Edit the existing holiday on that date instead of creating a new one.</div>
                                </li>
                                <li class="mb-0">
                                    <strong>Access Denied (1142):</strong> <code>Insufficient privileges.</code>
                                    <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solution:</strong> You do not have the required permissions to modify the holiday calendar. Contact the Administrator.</div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">2</div>
                <h3 class="hc-step-title">Managing the Calendar</h3>
                <p class="text-muted mb-4">Properly maintaining the calendar ensures accurate automated payouts to merchants.</p>

                <div class="pl-4 border-left border-success ml-2 mb-4">
                    <ol class="text-muted mb-0">
                        <li class="mb-3">Click the <strong>Add New Holiday</strong> button at the top right, or simply click on any date cell inside the calendar grid.</li>
                        <li class="mb-3">Select the exact <strong>Holiday Date</strong>.</li>
                        <li class="mb-3">Enter the <strong>Description</strong> so the finance team knows the reason for the settlement delay.</li>
                        <li class="mb-3">Set the status to <strong>Active</strong>.</li>
                        <li class="mb-2">Click <strong>Save Holiday</strong>. The settlement engine instantly recalculates pending T+1/T+2 payout dates to bypass this new holiday.</li>
                    </ol>
                </div>
            </div>
        </div>

    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Modul <strong>Settlement Holidays</strong> digunakan untuk mengonfigurasi hari libur nasional atau publik yang berdampak pada operasional perbankan. Setiap tanggal yang ditandai sebagai hari libur aktif akan secara otomatis menjeda perhitungan settlement T+1/T+2, menggeser jadwal pencairan ke hari kerja berikutnya.</p>

        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> Ikhtisar UI</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:25%">Fitur</th>
                            <th class="p-3 border-0">Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Kalender Interaktif</strong></td><td class="p-3 border-0">Memvisualisasikan semua hari libur aktif dan tidak aktif sepanjang bulan. Mengklik tanggal memungkinkan Anda untuk langsung menambah atau mengedit hari libur.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Tanggal Libur</strong></td><td class="p-3 border-0">Tanggal kalender spesifik hari libur yang memengaruhi operasional perbankan.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Deskripsi</strong></td><td class="p-3 border-0">Nama atau alasan hari libur tersebut (misalnya, <em>Idul Fitri</em>, <em>Hari Raya Natal</em>).</td></tr>
                        <tr><td class="p-3 border-0"><strong>Toggle Status</strong></td><td class="p-3 border-0">Bisa diatur ke <code>Active</code> (menunda settlement) atau <code>Inactive</code> (diabaikan oleh engine settlement).</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Procedural Walkthrough -->
        <h4 class="font-weight-bold mb-4 border-bottom pb-2">Procedural Walkthrough</h4>
        
        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">1</div>
                <h3 class="hc-step-title">Validasi Form & Batasan (Constraints)</h3>
                <div class="table-responsive shadow-sm mb-4 mt-3" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                    <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                        <thead style="background: rgba(0,0,0,0.4);">
                            <tr>
                                <th class="p-3 border-0" style="width: 25%;">Tipe Validasi</th>
                                <th class="p-3 border-0">Aturan Penegakan Sistem</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td class="p-3 border-0"><strong>Kolom Wajib</strong></td><td class="p-3 border-0">Kolom <code>Date</code>, <code>Holiday Name / Description</code>, dan <code>Status</code> wajib diisi saat menambahkan hari libur.</td></tr>
                            <tr><td class="p-3 border-0"><strong>Keunikan Tanggal</strong></td><td class="p-3 border-0">Hanya satu entri hari libur yang boleh ada pada satu tanggal tertentu.</td></tr>
                        </tbody>
                    </table>
                </div>

                <!-- System Notifications -->
                <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-bell text-info mr-2"></i> Notifikasi Sistem</h6>
                <div class="d-flex flex-column mb-4">
                    <div class="mb-3">
                        <div class="p-3 rounded border" style="background-color: rgba(22, 163, 74, 0.05); border-color: rgba(22, 163, 74, 0.2) !important;">
                            <strong class="text-success d-block mb-2"><i class="fas fa-check-circle mr-1"></i> Notifikasi Sukses</strong>
                            <ul class="small text-muted mb-0 pl-3">
                                <li class="mb-0"><strong>Data Tersimpan:</strong> <code>Holiday successfully added/updated.</code></li>
                            </ul>
                        </div>
                    </div>
                    <div class="mb-1">
                        <div class="p-3 rounded border" style="background-color: rgba(220, 38, 38, 0.05); border-color: rgba(220, 38, 38, 0.2) !important;">
                            <strong class="text-danger d-block mb-2"><i class="fas fa-exclamation-circle mr-1"></i> Notifikasi Error & Solusinya</strong>
                            <ul class="small text-muted mb-0 pl-3">
                                <li class="mb-3">
                                    <strong>Duplikat Tanggal (1062):</strong> <code>Failed to save: A holiday for this date already exists.</code>
                                    <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solusi:</strong> Edit hari libur yang sudah ada pada tanggal tersebut alih-alih membuat yang baru.</div>
                                </li>
                                <li class="mb-0">
                                    <strong>Access Denied (1142):</strong> <code>Insufficient privileges.</code>
                                    <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solusi:</strong> Anda tidak memiliki izin untuk memodifikasi kalender hari libur. Hubungi Administrator Sistem Anda.</div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row hc-step-row align-items-start mb-4">
            <div class="col-lg-12">
                <div class="hc-step-number">2</div>
                <h3 class="hc-step-title">Mengelola Kalender</h3>
                <p class="text-muted mb-4">Menjaga kalender tetap terbarui sangat penting agar pembayaran ke merchant tidak terjadi kesalahan perhitungan jatuh tempo.</p>

                <div class="pl-4 border-left border-success ml-2 mb-4">
                    <ol class="text-muted mb-0">
                        <li class="mb-3">Klik tombol <strong>Add New Holiday</strong> di kanan atas, atau klik langsung pada kotak tanggal mana saja di dalam kalender.</li>
                        <li class="mb-3">Pilih <strong>Holiday Date</strong> yang tepat.</li>
                        <li class="mb-3">Masukkan <strong>Description</strong> agar tim keuangan mengetahui alasan penundaan pencairan.</li>
                        <li class="mb-3">Atur status ke <strong>Active</strong>.</li>
                        <li class="mb-2">Klik <strong>Save Holiday</strong>. Mesin settlement seketika menghitung ulang tanggal jatuh tempo T+1/T+2 yang sedang tertunda agar melewati hari libur baru ini.</li>
                    </ol>
                </div>
            </div>
        </div>

    </div>
</div>
