<div id="module-history" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">
        <div class="text-center mb-5 mt-3">
            <div class="mb-3">
                <i class="fas fa-history text-primary" style="font-size: 3rem;"></i>
            </div>
            <h2 class="font-weight-bold">History &amp; Audit Trail</h2>
            <p class="text-muted lead" style="max-width: 800px; margin: 0 auto;">A comprehensive ledger of all transactions. Utilize advanced session-persistent filtering and asynchronous bulk downloads to orchestrate financial audits without performance bottlenecks.</p>
        </div>
        
        <hr class="doc-divider">

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-search text-primary mr-2"></i> 1. Deep Session Search &amp; Filtering</h3>
            <p class="text-muted mb-4">The History module handles massive datasets using Server-Side processing. To maintain state across page navigations, all search queries are aggressively cached in the server's session memory.</p>
            
            <div class="pl-3">
                <div class="mb-4">
                    <h5 class="font-weight-bold text-dark"><i class="fas fa-angle-right text-primary mr-2"></i>Global Lookup Priority</h5>
                    <p class="text-muted mb-0 ml-4">The main search bar simultaneously queries exact matches for <code>Invoice</code>, <code>Transaction ID</code>, and <code>Phone Number</code>. If a match is found, the system enforces a strict session lock on that search term (<code>last_dt_search_history</code>).</p>
                </div>
                <div class="mb-4">
                    <h5 class="font-weight-bold text-dark"><i class="fas fa-angle-right text-primary mr-2"></i>Advanced Matrix Filters</h5>
                    <div class="text-muted mb-0 ml-4">
                        Complex audits require multi-dimensional filtering. The advanced panel allows:
                        <ul class="mt-2">
                            <li><strong>Date Picker:</strong> Narrow bounds to specific operational days.</li>
                            <li><strong>Merchant Name:</strong> Isolate a single business entity's volume.</li>
                            <li><strong>Status Filters:</strong> Segment by <code>Success</code>, <code>Pending</code>, <code>Failed</code>, or <code>Expired</code> states.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="doc-callout callout-info mb-4 shadow-sm border-left-info">
                <div class="callout-icon"><i class="fas fa-sync-alt"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">The "Ghost Filter" Phenomenon</strong>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-1"><strong>Diagnosis:</strong> If you navigate to another module and return to History, you may notice the table appears empty or is still showing older results. This is because your previous session filters are still active in the background.</li>
                        <li class="mb-1"><strong>Resolution:</strong> Always click the <strong>Reset Filter</strong> button to explicitly flush <code>$this->session->unset_userdata()</code> and restore the default global view.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-cloud-download-alt text-primary mr-2"></i> 2. Asynchronous Data Export</h3>
            <p class="text-muted mb-4">Extracting large transaction datasets can cause critical database locks (MySQL table locks) and memory exhaustion. The gateway mitigates this via background queueing.</p>
            
            <ol class="text-muted ml-3">
                <li class="mb-2">Apply your desired date and status filters. The system requires at least one active filter to prevent "Select All" queries that could crash the server.</li>
                <li class="mb-2">Click the <strong>Download Excel</strong> action.</li>
                <li class="mb-2">Instead of freezing your browser, the controller instantly registers an async task in the <code>admin_download</code> table with the status <em>"Your request is being processed."</em></li>
                <li class="mb-2">Navigate to the <strong>Download Report</strong> menu where you can safely retrieve the generated CSV/Excel file once the background worker finishes compiling it.</li>
            </ol>
        </div>
    </div>
    
    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">
        <div class="text-center mb-5 mt-3">
            <div class="mb-3">
                <i class="fas fa-history text-primary" style="font-size: 3rem;"></i>
            </div>
            <h2 class="font-weight-bold">Riwayat &amp; Jejak Audit (History)</h2>
            <p class="text-muted lead" style="max-width: 800px; margin: 0 auto;">Buku besar komprehensif dari semua transaksi. Manfaatkan filter persisten berbasis sesi dan unduhan asinkron untuk melakukan audit finansial massal tanpa membebani performa server.</p>
        </div>
        
        <hr class="doc-divider">

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-search text-primary mr-2"></i> 1. Pencarian Sesi Mendalam &amp; Filter</h3>
            <p class="text-muted mb-4">Modul History menangani kumpulan data raksasa menggunakan pemrosesan Server-Side. Untuk mempertahankan kondisi Anda saat berpindah halaman, semua kueri secara agresif disimpan di memori Sesi server.</p>
            
            <div class="pl-3">
                <div class="mb-4">
                    <h5 class="font-weight-bold text-dark"><i class="fas fa-angle-right text-primary mr-2"></i>Prioritas Pencarian Global</h5>
                    <p class="text-muted mb-0 ml-4">Bilah pencarian utama akan mencari kecocokan persis atas <code>Invoice</code>, <code>Transaction ID</code>, dan <code>Nomor Telepon</code> secara bersamaan. Jika ditemukan, sistem mengunci pencarian tersebut (<code>last_dt_search_history</code>).</p>
                </div>
                <div class="mb-4">
                    <h5 class="font-weight-bold text-dark"><i class="fas fa-angle-right text-primary mr-2"></i>Filter Matriks Lanjutan</h5>
                    <div class="text-muted mb-0 ml-4">
                        Audit kompleks butuh filter multi-dimensi. Panel lanjutan memungkinkan:
                        <ul class="mt-2">
                            <li><strong>Date Picker:</strong> Membatasi rentang hari operasional tertentu.</li>
                            <li><strong>Nama Merchant:</strong> Mengisolasi volume transaksi satu entitas bisnis saja.</li>
                            <li><strong>Filter Status:</strong> Memecah transaksi menjadi <code>Success</code>, <code>Pending</code>, <code>Failed</code>, atau <code>Expired</code>.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="doc-callout callout-info mb-4 shadow-sm border-left-info">
                <div class="callout-icon"><i class="fas fa-sync-alt"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-dark" style="font-size: 16px;">Fenomena "Filter Hantu" (Ghost Filter)</strong>
                    <ul class="small text-muted mb-0 pl-3">
                        <li class="mb-1"><strong>Diagnosa:</strong> Jika Anda keluar ke modul lain dan kembali ke History, tabel mungkin terlihat kosong atau menampilkan data lama. Hal ini karena Sesi pencarian Anda masih menyangkut di latar belakang.</li>
                        <li class="mb-1"><strong>Resolusi:</strong> Selalu klik tombol <strong>Reset Filter</strong> untuk membilas (<code>unset_userdata</code>) sesi dan mengembalikan tabel ke tampilan awal (default).</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="mb-5">
            <h3 class="mb-4"><i class="fas fa-cloud-download-alt text-primary mr-2"></i> 2. Ekspor Data Asinkron (Background Task)</h3>
            <p class="text-muted mb-4">Mengekstrak data transaksi dalam jumlah raksasa secara langsung dapat memicu *Table Lock* pada database MySQL dan kehabisan memori server. Sistem mencegah ini melalui antrean latar belakang.</p>
            
            <ol class="text-muted ml-3">
                <li class="mb-2">Terapkan filter rentang tanggal dan status yang diinginkan. Anda diwajibkan menyalakan minimal satu filter, untuk mencegah eksekusi "Pilih Semua" (Select All) yang bisa membuat server *down*.</li>
                <li class="mb-2">Klik aksi <strong>Download Excel</strong>.</li>
                <li class="mb-2">Alih-alih membekukan browser Anda (loading panjang), <em>controller</em> akan mendaftarkan permintaan Anda ke dalam tabel <code>admin_download</code> secara instan dengan notifikasi <em>"Permintaan sedang diproses."</em></li>
                <li class="mb-2">Pindah ke menu <strong>Download Report</strong>, di mana Anda bisa mengambil hasil file Excel/CSV secara aman saat pekerja latar belakang (<em>background worker</em>) telah selesai menyusun datanya.</li>
            </ol>
        </div>
    </div>
</div>
