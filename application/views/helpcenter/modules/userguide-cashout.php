<div id="module-ug-cashout" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">
        
        <p class="doc-lead text-muted" style="line-height: 1.7;">The <strong>Cashout Dashboard</strong> is where you configure which outbound payment channels a merchant can use to disburse money. Learn how to manage Cashout Fee settings, execute Bulk Updates, and control channel lifecycles.</p>
                        
        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> UI Overview — Configuration Parameters</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width: 30%;">Parameter</th>
                            <th class="p-3 border-0">Description & Logic</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>CHANNEL GROUP & EXTERNAL ID</strong></td><td class="p-3 border-0">The macro-category (e.g., <code>bifast</code>, <code>rtol</code>) and the upstream provider (e.g., <code>quantum</code>, <code>paylabs</code>).</td></tr>
                        <tr><td class="p-3 border-0"><strong>SPECIFIC CHANNEL ID</strong></td><td class="p-3 border-0">The exact disbursement endpoint identifier (e.g., <code>bifast_bca</code>, <code>bifast_mandiri</code>).</td></tr>
                        <tr><td class="p-3 border-0"><strong>FEE TYPE</strong></td><td class="p-3 border-0">The deduction model: <strong>Fixed</strong>, <strong>Percentage</strong>, or <strong>Both</strong> (Hybrid).</td></tr>
                        <tr><td class="p-3 border-0"><strong>FIXED / PERCENTAGE FEE</strong></td><td class="p-3 border-0">The numeric fee value. Must be explicitly <code>0</code> if not used. Percentage is between <code>0</code> and <code>100</code>.</td></tr>
                        <tr><td class="p-3 border-0"><strong>AMOUNT MIN & MAX</strong></td><td class="p-3 border-0">Optional transactional floor/ceiling. Use <code>0</code> to inherit global gateway limits.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Architecture -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-project-diagram text-primary mr-2"></i> 1. Architecture: Cashout Processing</h5>
            <p class="text-muted mb-4">This module governs the commercial terms between the payment gateway and the merchant for outbound transactions. The gateway evaluates these rules in real-time:</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">A merchant requests a disbursement via API or Dashboard.</li>
                    <li class="mb-3">The system checks if the merchant has an <strong>Active</strong> Cashout configuration for that specific channel.</li>
                    <li class="mb-3">The fee is calculated based on the <strong>Fee Type</strong> configuration.</li>
                    <li class="mb-2">The requested principal amount PLUS the calculated fee is deducted from the merchant's Master Balance. If the balance is insufficient, the request is rejected.</li>
                </ol>
            </div>
            
            <div class="doc-callout callout-important shadow-sm mt-4">
                <div class="callout-icon"><i class="fas fa-clipboard-check text-danger"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Prerequisites</strong>
                    <p class="mb-0 text-muted small">Before configuring cashout fees, ensure the target merchant has an <a href="javascript:void(0)" onclick="if(window.activateHelpModule){ window.activateHelpModule('module-ug-merchant-edit'); window.scrollTo(0,0); }" class="text-primary font-weight-bold text-decoration-none">Active</a> operational status, and the underlying Master Channel is globally enabled in the <a href="javascript:void(0)" onclick="if(window.activateHelpModule){ window.activateHelpModule('module-ug-channel'); window.scrollTo(0,0); }" class="text-primary font-weight-bold text-decoration-none">Gateway Channel</a> settings.</p>
                </div>
            </div>
        </div>

        <!-- Section 2: Step-by-Step -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-edit text-success mr-2"></i> 2. Provisioning & Managing Channels</h5>
            <p class="text-muted mb-4">How to manage the disbursement channels a merchant can utilize.</p>

            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Workflow A: Add a Single Mapping</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Click <strong><i class="fas fa-plus"></i> Add Channel</strong> on the action bar.</li>
                    <li class="mb-3">Select the <strong>Channel Group</strong>, <strong>External ID</strong>, and <strong>Specific Channel ID</strong>.</li>
                    <li class="mb-3">Define the <strong>Fee Structure</strong> (Type, Fixed, Percentage).</li>
                    <li class="mb-3">Set the Status to <strong>Active</strong>.</li>
                    <li class="mb-2">Click <strong>Save Configuration</strong>.</li>
                </ol>
            </div>

            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Workflow B: Bulk Provisioning</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Click <strong><i class="fas fa-layer-group"></i> Bulk Add</strong> on the toolbar.</li>
                    <li class="mb-3">Select the target <strong>Cashout Channel Group</strong> and <strong>External ID Default</strong>.</li>
                    <li class="mb-3">Input the universal fee structure that will apply to all channels in the group.</li>
                    <li class="mb-2">Click <strong>Apply Bulk Settings</strong>. The system will safely skip any channels you have already configured individually.</li>
                </ol>
            </div>
            
            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Workflow C: Editing or Deleting</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Locate the target configuration in the data table.</li>
                    <li class="mb-3">Click the action menu (⋮) and select <strong>Edit Setting</strong> to modify fees/status, or <strong>Delete</strong> to permanently remove access.</li>
                    <li class="mb-2">Routing identifiers (Channel Group, External ID) cannot be edited. If they are wrong, delete the entry and create a new one.</li>
                </ol>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Common Issues & Troubleshooting</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_co_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 1: Specific Channel ID dropdown is empty
                </a>
                <div id="faq_en_co_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> The form relies on cascading logic. Ensure you select the Channel Group and External ID first. If it's still empty, it means the upstream provider hasn't registered any endpoints for that group in the global Master Channel settings.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_co_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 2: Insufficient Balance Errors
                </a>
                <div id="faq_en_co_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> If a merchant attempts a disbursement and receives this error, their current master balance does not cover BOTH the requested cashout principal amount AND the configured cashout fee combined. Cashout fees are added on top of the principal.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_co_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 3: "Invalid Amount Limits (Min > Max)" error
                </a>
                <div id="faq_en_co_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> The Minimum boundary you entered is larger than the Maximum boundary, which is logically impossible. Use <code>0</code> for both if you just want to use the global default limits.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_co_4" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 4: What happens if I set a channel to "Inactive"?
                </a>
                <div id="faq_en_co_4" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> The API endpoint is strictly locked. Any new disbursement requests for this channel will be instantly rejected by the gateway with a <code>403 Forbidden</code> or <code>503 Service Unavailable</code> error.
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Dasbor <strong>Cashout</strong> adalah tempat Anda mengatur kanal pengeluaran dana apa saja yang bisa digunakan merchant. Pelajari cara mengelola pengaturan Biaya Cashout, melakukan Bulk Update, dan mengontrol siklus hidup kanal.</p>
                        
        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> Ikhtisar UI — Parameter Konfigurasi</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width: 30%;">Parameter</th>
                            <th class="p-3 border-0">Deskripsi & Logika</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>CHANNEL GROUP & EXTERNAL ID</strong></td><td class="p-3 border-0">Kategori makro (misal, <code>bifast</code>) dan penyedia layanan di hulu (misal, <code>quantum</code>).</td></tr>
                        <tr><td class="p-3 border-0"><strong>SPECIFIC CHANNEL ID</strong></td><td class="p-3 border-0">Pengidentifikasi <em>endpoint</em> pengeluaran pasti (misal, <code>bifast_bca</code>).</td></tr>
                        <tr><td class="p-3 border-0"><strong>FEE TYPE</strong></td><td class="p-3 border-0">Model pemotongan biaya: <strong>Fixed</strong> (Tetap), <strong>Percentage</strong> (Persentase), atau <strong>Both</strong> (Keduanya).</td></tr>
                        <tr><td class="p-3 border-0"><strong>FIXED / PERCENTAGE FEE</strong></td><td class="p-3 border-0">Nilai biaya. Harus diisi <code>0</code> jika tidak digunakan. Persentase harus antara <code>0</code> hingga <code>100</code>.</td></tr>
                        <tr><td class="p-3 border-0"><strong>AMOUNT MIN & MAX</strong></td><td class="p-3 border-0">Batas nilai transaksi paling rendah/tinggi opsional. Isi <code>0</code> untuk mewarisi limit global.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Architecture -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-project-diagram text-primary mr-2"></i> 1. Arsitektur: Pemrosesan Cashout</h5>
            <p class="text-muted mb-4">Modul ini mengatur ketentuan komersial transaksi keluar. Gateway mengevaluasi aturan-aturan ini secara real-time:</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Merchant meminta penarikan/pengeluaran dana (disbursement) via API atau Dasbor.</li>
                    <li class="mb-3">Sistem mengecek apakah merchant memiliki konfigurasi Cashout berstatus <strong>Active</strong> untuk kanal tersebut.</li>
                    <li class="mb-3">Biaya dihitung secara matematis berdasarkan konfigurasi <strong>Fee Type</strong>.</li>
                    <li class="mb-2">Sistem memotong total Nominal Pencairan DITAMBAH Biaya langsung dari Master Balance merchant. Jika saldo tidak cukup, permintaan akan ditolak.</li>
                </ol>
            </div>
            
            <div class="doc-callout callout-important shadow-sm mt-4">
                <div class="callout-icon"><i class="fas fa-clipboard-check text-danger"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Prasyarat</strong>
                    <p class="mb-0 text-muted small">Sebelum mengatur biaya cashout, pastikan merchant memiliki status operasional <a href="javascript:void(0)" onclick="if(window.activateHelpModule){ window.activateHelpModule('module-ug-merchant-edit'); window.scrollTo(0,0); }" class="text-primary font-weight-bold text-decoration-none">Active</a>, dan Master Channel yang mendasarinya sudah diaktifkan secara global di pengaturan <a href="javascript:void(0)" onclick="if(window.activateHelpModule){ window.activateHelpModule('module-ug-channel'); window.scrollTo(0,0); }" class="text-primary font-weight-bold text-decoration-none">Gateway Channel</a>.</p>
                </div>
            </div>
        </div>

        <!-- Section 2: Step-by-Step -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-edit text-success mr-2"></i> 2. Mendaftarkan & Mengelola Kanal</h5>
            <p class="text-muted mb-4">Cara mengatur kanal pengeluaran dana yang bisa dipakai oleh merchant.</p>

            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Alur Kerja A: Menambahkan Satu Mapping Baru</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Klik <strong><i class="fas fa-plus"></i> Add Channel</strong> di bilah aksi.</li>
                    <li class="mb-3">Pilih <strong>Channel Group</strong>, <strong>External ID</strong>, dan <strong>Specific Channel ID</strong>.</li>
                    <li class="mb-3">Tentukan <strong>Fee Structure</strong> (Tipe, Fixed, Persentase).</li>
                    <li class="mb-3">Pastikan Status disetel ke <strong>Active</strong>.</li>
                    <li class="mb-2">Klik <strong>Save Configuration</strong>.</li>
                </ol>
            </div>

            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Alur Kerja B: Registrasi Massal (Bulk Update)</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Klik tombol <strong><i class="fas fa-layer-group"></i> Bulk Add</strong> di toolbar.</li>
                    <li class="mb-3">Pilih <strong>Cashout Channel Group</strong> tujuan dan <strong>External ID Default</strong>-nya.</li>
                    <li class="mb-3">Masukkan struktur biaya universal yang akan diaplikasikan.</li>
                    <li class="mb-2">Klik <strong>Apply Bulk Settings</strong>. Sistem cerdas secara otomatis akan melewati (skip) kanal-kanal yang sudah Anda atur manual sebelumnya.</li>
                </ol>
            </div>
            
            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-chevron-circle-right text-success mr-2"></i> Alur Kerja C: Mengedit atau Menghapus</h6>
            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Cari konfigurasi yang ingin diubah di tabel data.</li>
                    <li class="mb-3">Klik menu aksi (⋮) lalu pilih <strong>Edit Setting</strong> untuk merombak biaya/status, atau <strong>Delete</strong> untuk mencabut hak akses permanen.</li>
                    <li class="mb-2">Pengidentifikasi (Channel Group, External ID) terkunci dan tak bisa diedit. Jika salah, hapus baris tersebut lalu buat ulang yang baru.</li>
                </ol>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Panduan Pemecahan Masalah (FAQ)</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_co_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 1: Dropdown Specific Channel ID kosong (tak ada pilihan)
                </a>
                <div id="faq_id_co_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Formulir ini punya logika bertingkat. Pastikan Anda memilih Channel Group dan External ID lebih dulu. Jika tetap kosong, berarti provider hulu (upstream) memang belum mendaftarkan satupun endpoint di master global gateway.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_co_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 2: Error Saldo Tidak Mencukupi (Insufficient Balance)
                </a>
                <div id="faq_id_co_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Jika merchant mencoba melakukan pencairan dan mendapat <em>error</em> saldo tidak cukup, ini karena saldo master mereka saat ini tidak dapat menutupi nominal pencairan pokok DITAMBAH dengan total biaya Cashout. Biaya Cashout dikenakan memotong saldo master, bukan mengurangi uang yang diterima di rekening tujuan.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_co_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 3: Error "Invalid Amount Limits (Min > Max)"
                </a>
                <div id="faq_id_co_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Batas Minimum yang Anda input lebih besar daripada batas Maksimum; hal ini mustahil secara logika. Masukkan angka <code>0</code> untuk keduanya jika Anda hanya ingin memakai limit bawaan sistem.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_co_4" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 4: Apa akibat jika status kanal saya set "Inactive"?
                </a>
                <div id="faq_id_co_4" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Endpoint API tersebut seketika dikunci. Permintaan (request) pengeluaran dana baru dari merchant akan langsung ditendang oleh gateway dengan error <code>403 Forbidden</code> atau <code>503 Service Unavailable</code>.
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
