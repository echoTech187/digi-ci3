<div id="module-ug-merchant-delegate" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">The <strong>Delegate</strong> action sets the <strong>Permission Ceiling</strong> for a merchant. It defines the maximum set of features (such as Mutation, QRIS, or Virtual Account) the merchant is allowed to access and further delegate down to their own sub-accounts and staff.</p>

        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> UI Overview — Delegation Modal</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width: 30%;">Interface Element</th>
                            <th class="p-3 border-0">What It Means</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Permission Name</strong></td><td class="p-3 border-0">The specific module or feature (e.g., Mutation, Balance History, Purchase, QRIS, Virtual Account).</td></tr>
                        <tr><td class="p-3 border-0"><strong>Action (Grant/Deny)</strong></td><td class="p-3 border-0">Toggles to explicitly allow (<span class="badge badge-success-soft text-success">GRANT</span>) or block (<span class="badge badge-danger-soft text-danger">DENY</span>) access to the permission.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Save Changes</strong></td><td class="p-3 border-0">Applies the new permission ceiling. The changes take effect immediately across the merchant's hierarchy.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Architecture -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-project-diagram text-primary mr-2"></i> 1. How Delegation Works (Inheritance & Flow)</h5>
            <p class="text-muted mb-4">The delegation system operates on a strict top-down inheritance model to ensure secure access control:</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3"><strong>Permission Ceiling:</strong> You can only delegate permissions that you personally possess. You cannot grant access beyond your own security clearance.</li>
                    <li class="mb-3"><strong>Inheritance & Flow:</strong> Granting a permission allows the merchant to use the feature themselves, <strong>and</strong> gives them the authority to further delegate it down their hierarchy (e.g., to their sub-accounts or cashiers).</li>
                    <li class="mb-2"><strong>Granular Controls:</strong> Toggling between Grant and Deny acts as an immediate override. A "Deny" overrides any default role permissions the merchant might have, instantly blocking access.</li>
                </ol>
            </div>
        </div>

        <!-- Section 2: Step-by-Step -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-edit text-success mr-2"></i> 2. Managing Permission Ceilings</h5>
            <p class="text-muted mb-4">How to adjust the features a merchant is allowed to use.</p>

            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Navigate to <strong>Merchant Panel → Merchant Management</strong>.</li>
                    <li class="mb-3">Find the target merchant, click the action menu (⋮) on the right, and select <strong>Delegate</strong>.</li>
                    <li class="mb-3">Review the list of available permissions in the <strong>Delegate Permission Ceiling</strong> modal.</li>
                    <li class="mb-3">Click <strong>GRANT</strong> to allow the feature, or <strong>DENY</strong> to block it.</li>
                    <li class="mb-2">Click <strong>Save Changes</strong>. The access limits will be instantly applied.</li>
                </ol>
            </div>
        </div>

        <!-- New Section: Form Validations -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-shield-alt text-primary mr-2"></i> 3. Form Validations & Constraints</h5>
            <div class="table-responsive shadow-sm mb-4" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width: 25%;">Constraint Type</th>
                            <th class="p-3 border-0">System Enforcement Rule</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Permission Ceiling Limit</strong></td><td class="p-3 border-0">You cannot grant permissions that are not assigned to your own user role.</td></tr>
                    </tbody>
                </table>
            </div>

            <!-- New Section: System Notifications -->
            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-bell text-info mr-2"></i> System Notifications</h6>
            <div class="d-flex flex-column mb-4">
                <div class="mb-3">
                    <div class="p-3 rounded border" style="background-color: rgba(22, 163, 74, 0.05); border-color: rgba(22, 163, 74, 0.2) !important;">
                        <strong class="text-success d-block mb-2"><i class="fas fa-check-circle mr-1"></i> Success Events</strong>
                        <ul class="small text-muted mb-0 pl-3">
                            <li class="mb-0"><strong>Success:</strong> <code>[Count] updated</code></li>
                        </ul>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="p-3 rounded border" style="background-color: rgba(220, 38, 38, 0.05); border-color: rgba(220, 38, 38, 0.2) !important;">
                        <strong class="text-danger d-block mb-2"><i class="fas fa-exclamation-circle mr-1"></i> Error Events & Solutions</strong>
                        <ul class="small text-muted mb-0 pl-3">
                            <li class="mb-3">
                                <strong>Missing Identifier:</strong> <code>ID missing</code>
                                <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solution:</strong> Ensure you are delegating from a valid merchant record. Try refreshing the page.</div>
                            </li>
                            <li class="mb-0">
                                <strong>No Selection:</strong> <code>No data</code>
                                <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solution:</strong> You must toggle at least one permission before saving changes.</div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Common Issues &amp; Troubleshooting</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_mdel_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 1: What happens if I DENY a permission they are already using?
                </a>
                <div id="faq_en_mdel_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> The merchant will instantly lose access to that feature. Furthermore, because of top-down inheritance, any sub-accounts or staff members they had previously delegated that feature to will <strong>also</strong> lose access automatically.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_mdel_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 2: Why are some features missing from the Delegate list?
                </a>
                <div id="faq_en_mdel_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> The system enforces the "Permission Ceiling" rule. You can only see and delegate permissions that your own administrator account possesses. If you lack a specific clearance, you cannot grant it to others.
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Aksi <strong>Delegate</strong> mengatur <strong>Batas Maksimal Izin (Permission Ceiling)</strong> untuk merchant. Ini menentukan kumpulan fitur maksimal (seperti Mutasi, QRIS, atau Virtual Account) yang boleh diakses oleh merchant dan didelegasikan lebih lanjut ke sub-akun serta staf mereka sendiri.</p>

        <hr class="my-4">

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> Ikhtisar UI — Modal Delegasi</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width: 30%;">Elemen Antarmuka</th>
                            <th class="p-3 border-0">Artinya</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Permission Name</strong></td><td class="p-3 border-0">Modul atau fitur spesifik (misal: Mutasi, Riwayat Saldo, Pembelian, QRIS, Virtual Account).</td></tr>
                        <tr><td class="p-3 border-0"><strong>Action (Grant/Deny)</strong></td><td class="p-3 border-0">Tombol untuk secara eksplisit mengizinkan (<span class="badge badge-success-soft text-success">GRANT</span>) atau memblokir (<span class="badge badge-danger-soft text-danger">DENY</span>) akses ke izin tersebut.</td></tr>
                        <tr><td class="p-3 border-0"><strong>Save Changes</strong></td><td class="p-3 border-0">Menerapkan batas izin yang baru. Perubahan langsung berlaku di seluruh hierarki merchant.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Architecture -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-project-diagram text-primary mr-2"></i> 1. Cara Kerja Delegasi (Inheritance & Flow)</h5>
            <p class="text-muted mb-4">Sistem delegasi beroperasi menggunakan model pewarisan *top-down* yang ketat untuk memastikan kontrol akses yang aman:</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3"><strong>Batas Izin (Permission Ceiling):</strong> Anda hanya dapat mendelegasikan izin yang Anda miliki sendiri. Anda tidak dapat memberikan akses melampaui tingkat otorisasi Anda sendiri.</li>
                    <li class="mb-3"><strong>Pewarisan & Alur (Inheritance & Flow):</strong> Memberikan izin memungkinkan merchant menggunakan fitur tersebut secara langsung, <strong>dan</strong> memberi mereka wewenang untuk mendelegasikannya lebih lanjut ke hierarki di bawahnya (misal: ke sub-akun atau kasir mereka).</li>
                    <li class="mb-2"><strong>Kontrol Granular:</strong> Mengubah opsi antara Grant dan Deny berfungsi sebagai penimpaan langsung. Sebuah "Deny" akan membatalkan izin default apa pun dari *role* merchant, dan langsung memblokir akses.</li>
                </ol>
            </div>
        </div>

        <!-- Section 2: Step-by-Step -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-edit text-success mr-2"></i> 2. Mengatur Batas Izin (Permission Ceiling)</h5>
            <p class="text-muted mb-4">Cara menyesuaikan fitur yang boleh digunakan oleh merchant.</p>

            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Navigasikan ke <strong>Merchant Panel → Merchant Management</strong>.</li>
                    <li class="mb-3">Cari merchant yang dituju, klik menu aksi (⋮) di sebelah kanan, dan pilih <strong>Delegate</strong>.</li>
                    <li class="mb-3">Tinjau daftar izin yang tersedia di modal <strong>Delegate Permission Ceiling</strong>.</li>
                    <li class="mb-3">Klik <strong>GRANT</strong> untuk mengizinkan fitur, atau <strong>DENY</strong> untuk memblokirnya.</li>
                    <li class="mb-2">Klik <strong>Save Changes</strong>. Batasan akses akan langsung diterapkan.</li>
                </ol>
            </div>
        </div>

        <!-- New Section: Form Validations -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-shield-alt text-primary mr-2"></i> 3. Validasi Form & Batasan (Constraints)</h5>
            <div class="table-responsive shadow-sm mb-4" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width: 25%;">Tipe Validasi</th>
                            <th class="p-3 border-0">Aturan Penegakan Sistem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>Batas Permission Ceiling</strong></td><td class="p-3 border-0">Anda tidak dapat memberikan izin yang tidak dimiliki oleh *role* pengguna Anda sendiri.</td></tr>
                    </tbody>
                </table>
            </div>

            <!-- New Section: System Notifications -->
            <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-bell text-info mr-2"></i> Notifikasi Sistem</h6>
            <div class="d-flex flex-column mb-4">
                <div class="mb-3">
                    <div class="p-3 rounded border" style="background-color: rgba(22, 163, 74, 0.05); border-color: rgba(22, 163, 74, 0.2) !important;">
                        <strong class="text-success d-block mb-2"><i class="fas fa-check-circle mr-1"></i> Notifikasi Sukses</strong>
                        <ul class="small text-muted mb-0 pl-3">
                            <li class="mb-0"><strong>Pembaruan:</strong> <code>[Jumlah] updated</code></li>
                        </ul>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="p-3 rounded border" style="background-color: rgba(220, 38, 38, 0.05); border-color: rgba(220, 38, 38, 0.2) !important;">
                        <strong class="text-danger d-block mb-2"><i class="fas fa-exclamation-circle mr-1"></i> Notifikasi Error & Solusinya</strong>
                        <ul class="small text-muted mb-0 pl-3">
                            <li class="mb-3">
                                <strong>ID Hilang:</strong> <code>ID missing</code>
                                <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solusi:</strong> Pastikan Anda membuka pengaturan dari merchant yang valid. Coba segarkan halaman.</div>
                            </li>
                            <li class="mb-0">
                                <strong>Pilihan Kosong:</strong> <code>No data</code>
                                <div class="text-dark mt-1"><i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solusi:</strong> Tidak ada perubahan yang disimpan. Anda harus mengubah minimal satu pengaturan izin sebelum menyimpan.</div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Panduan Pemecahan Masalah (FAQ)</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_mdel_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 1: Apa yang terjadi jika saya menekan DENY pada izin yang sedang mereka gunakan?
                </a>
                <div id="faq_id_mdel_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Merchant akan seketika kehilangan akses ke fitur tersebut. Selain itu, karena efek pewarisan *top-down*, setiap sub-akun atau staf yang sebelumnya telah mereka delegasikan fitur tersebut <strong>juga</strong> akan kehilangan akses secara otomatis.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_mdel_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 2: Mengapa beberapa fitur tidak muncul di daftar Delegate?
                </a>
                <div id="faq_id_mdel_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Sistem menegakkan aturan "Batas Izin" (Permission Ceiling). Anda hanya bisa melihat dan mendelegasikan izin yang dimiliki oleh akun administrator Anda sendiri. Jika Anda tidak memiliki otorisasi tertentu, Anda tidak dapat memberikannya kepada orang lain.
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
