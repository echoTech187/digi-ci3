<div id="module-ug-cashin" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">
        <div class="text-center mb-5 mt-3">
            <div class="mb-3">
                <i class="fas fa-desktop text-primary" style="font-size: 3rem;"></i>
            </div>
            <h2 class="font-weight-bold">Cashin Dashboard Guide</h2>
            <p class="text-muted lead" style="max-width: 800px; margin: 0 auto;">Learn how to manage Cashin Fee settings, execute Bulk Updates, and use Advanced Filters via the Admin Dashboard UI.</p>
        </div>

        <hr class="doc-divider">

        <h3 class="mb-4"><i class="fas fa-tasks text-success mr-2"></i> Operational Procedures</h3>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom">
                <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-plus text-primary mr-2"></i> A. Workflow: Provision a Single New Mapping (Add Mapping)</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4 mt-2">
                    <img src="/digi-ci3/assets/img/helpcenter/add_mapping.png" class="img-fluid rounded shadow-sm border" alt="Add Mapping Form Illustration" style="max-height: 400px; width: 100%; object-fit: cover; object-position: top;">
                </div>
                <ol class="text-muted ml-3 mb-0" style="line-height: 1.8;">
                    <li class="mb-2">Click the <strong><i class="fas fa-plus"></i> Add Mapping</strong> button at the top right of the dashboard.</li>
                    <li class="mb-2"><strong>Merchant Selection Stage:</strong> Select the target client from the <strong>Select Merchant</strong> dropdown.</li>
                    <li class="mb-2"><strong>Channel Configuration Stage:</strong>
                        <ul>
                            <li>Select the <strong>Channel Group</strong> (e.g., <code>qris</code>).</li>
                            <li>Select the <strong>External ID Default</strong> (e.g., <code>xendit</code>).</li>
                            <li>Select the exact machine route in <strong>Specific Channel ID</strong> (e.g., <code>qris_gpn</code>).</li>
                        </ul>
                    </li>
                    <li class="mb-2"><strong>Fee Structure & Settlement Stage:</strong>
                        <ul>
                            <li>Set the <strong>Fee Type</strong> (<code>IDR (Fixed)</code> or <code>Percentage (%)</code>).</li>
                            <li>Input the <strong>Settlement Interval (Days)</strong>.</li>
                            <li>Input the monetary deductions. <em>Ensure the unused fee type is explicitly filled with <code>0</code></em>.</li>
                        </ul>
                    </li>
                    <li class="mb-2">Define <strong>Amount Min/Max</strong> limits and ensure <strong>Status</strong> is <code>Active</code>.</li>
                    <li class="mb-2">Click <strong><i class="fas fa-save"></i> Save Configuration</strong>.</li>
                </ol>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom">
                <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-globe text-primary mr-2"></i> B. Workflow: Execute Mass Provider Migration (Bulk Update)</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4 mt-2">
                    <img src="/digi-ci3/assets/img/helpcenter/bulk_update.png" class="img-fluid rounded shadow-sm border" alt="Bulk Update Modal Illustration" style="max-height: 400px; width: 100%; object-fit: cover; object-position: top;">
                </div>
                <ol class="text-muted ml-3 mb-0" style="line-height: 1.8;">
                    <li class="mb-2">Click the <strong><i class="fas fa-globe"></i> Edit Mapping</strong> button above the search menu.</li>
                    <li class="mb-2"><strong>Define Scope:</strong> Choose either <em>Edit Mapping (Global)</em> or <em>Specific Merchant</em>.</li>
                    <li class="mb-2"><strong>Define CURRENT CONFIGURATION:</strong> Tell the system which old data needs replacing.</li>
                    <li class="mb-2"><strong>Define NEW CONFIGURATION:</strong> The destination route. Use <code>Don't Update (Keep Original)</code> if keeping the same provider.</li>
                    <li class="mb-2">Click <strong>UPDATE ALL MERCHANTS</strong>.</li>
                </ol>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom">
                <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-sliders-h text-primary mr-2"></i> C. Workflow: Using Advanced Search Filters</h5>
            </div>
            <div class="card-body">
                <ol class="text-muted ml-3 mb-0" style="line-height: 1.8;">
                    <li class="mb-2">On the main Cashin grid, click the <strong><i class="fas fa-sliders-h"></i> Filters</strong> button at the top right.</li>
                    <li class="mb-2">Filter by Merchant, Channel Group, External ID Default, Channel ID, or Status.</li>
                    <li class="mb-2">Click <strong><i class="fas fa-check"></i> APPLY FILTER</strong>.</li>
                </ol>
            </div>
        </div>
    </div>
    
    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">
        <div class="text-center mb-5 mt-3">
            <div class="mb-3">
                <i class="fas fa-desktop text-primary" style="font-size: 3rem;"></i>
            </div>
            <h2 class="font-weight-bold">Panduan Dashboard Cashin</h2>
            <p class="text-muted lead" style="max-width: 800px; margin: 0 auto;">Pelajari cara mengelola pengaturan Biaya Cashin, melakukan Bulk Update, dan menggunakan Filter Lanjutan via UI Admin Dashboard.</p>
        </div>

        <hr class="doc-divider">

        <h3 class="mb-4"><i class="fas fa-tasks text-success mr-2"></i> Prosedur Operasional</h3>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom">
                <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-plus text-primary mr-2"></i> A. Alur Kerja: Menambahkan Mapping Baru (Add Mapping)</h5>
            </div>
            <div class="card-body">
                <ol class="text-muted ml-3 mb-0" style="line-height: 1.8;">
                    <li class="mb-2">Klik tombol <strong><i class="fas fa-plus"></i> Add Mapping</strong> di kanan atas dashboard.</li>
                    <li class="mb-2"><strong>Pemilihan Merchant:</strong> Pilih klien dari dropdown.</li>
                    <li class="mb-2"><strong>Konfigurasi Kanal:</strong> Pilih Group, External ID, dan Specific Channel ID.</li>
                    <li class="mb-2"><strong>Struktur Biaya:</strong> Tentukan tipe biaya (Fixed/Percentage) dan masa penyelesaian (Settlement).</li>
                    <li class="mb-2">Tentukan limit dan pastikan statusnya <code>Active</code>.</li>
                    <li class="mb-2">Klik <strong><i class="fas fa-save"></i> Save Configuration</strong>.</li>
                </ol>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom">
                <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-globe text-primary mr-2"></i> B. Alur Kerja: Migrasi Provider Massal (Bulk Update)</h5>
            </div>
            <div class="card-body">
                <ol class="text-muted ml-3 mb-0" style="line-height: 1.8;">
                    <li class="mb-2">Klik tombol <strong><i class="fas fa-globe"></i> Edit Mapping</strong>.</li>
                    <li class="mb-2"><strong>Tentukan Cakupan:</strong> Pilih Global atau Merchant Spesifik.</li>
                    <li class="mb-2"><strong>Tentukan Konfigurasi Saat Ini:</strong> Pilih data lama yang ingin diganti.</li>
                    <li class="mb-2"><strong>Tentukan Konfigurasi Baru:</strong> Pilih rute tujuan.</li>
                    <li class="mb-2">Klik <strong>UPDATE ALL MERCHANTS</strong>.</li>
                </ol>
            </div>
        </div>
    </div>
</div>
