<div id="module-ug-prepaid-products" class="hc-doc-section">
    <!-- EN CONTENT -->
    <div class="lang-content lang-en" style="display:block;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">The Prepaid Products module (which includes Regular Pulsa, Data Packages, Electricity Tokens, and E-Wallet Top-Ups) is where you manage the catalog of digital products available to merchants. Changes made here instantly affect merchant catalogues and transaction pricing.</p>

        <hr class="my-4">

        <div class="mb-5 p-4" style="background: var(--hc-sidebar-bg); border-radius: 12px; border: 1px solid var(--hc-border);">
            <h6 class="font-weight-bold mb-3" style="color: var(--hc-heading);">End-to-End PPOB Purchase Lifecycle</h6>
            <div class="mermaid">
            sequenceDiagram
                autonumber
                participant M as Merchant App
                participant G as Gidi Gateway
                participant A as PPOB Aggregator
                participant P as Provider (Telkomsel, PLN)
                
                M->>G: POST /Prepaid/purchase (Product Code + Target Number)
                G->>G: Deduct Merchant Balance (Pre-auth)
                G->>A: Forward Purchase Request
                A->>P: Inject Pulsa/Token
                
                alt Provider Processing (Pending)
                    P-->>A: Status: Processing
                    A-->>G: Status: Pending
                    G-->>M: HTTP 200 (Status: Pending)
                    
                    loop Webhook Polling
                        A->>G: Async Callback (Success/Failed)
                    end
                else Provider Immediate Success
                    P-->>A: Status: Success (SN Code)
                    A-->>G: Status: Success
                    G-->>M: HTTP 200 (Status: Success + SN Code)
                end
                
                alt If Failed
                    G->>G: Auto-Refund Merchant Balance
                end
            </div>
        </div>

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> UI Overview — Data Columns</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:25%">Column</th>
                            <th class="p-3 border-0">What It Means</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>PROVIDER</strong></td><td class="p-3 border-0">The network operator or vendor (e.g., XL, Telkomsel, DANA).</td></tr>
                        <tr><td class="p-3 border-0"><strong>CAPTION</strong></td><td class="p-3 border-0">The exact product name or denomination label as it appears to merchants and customers.</td></tr>
                        <tr><td class="p-3 border-0"><strong>DESCRIPTION</strong></td><td class="p-3 border-0">Internal notes or extended details about the specific product package.</td></tr>
                        <tr><td class="p-3 border-0"><strong>PRICE</strong></td><td class="p-3 border-0">The base selling rate (in IDR) charged to the merchant's balance.</td></tr>
                        <tr><td class="p-3 border-0"><strong>ACTION</strong></td><td class="p-3 border-0">A dropdown menu allowing you to Edit or Delete the specific product.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Workflow Filtering -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-search text-primary mr-2"></i> 1. Searching and Filtering Products</h5>
            <p class="text-muted mb-4">If you have hundreds of products, use the toolbar to find exactly what you need to update.</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3"><strong>Quick Search:</strong> Type in the <em>Search by name, category, or description...</em> box to instantly filter the table.</li>
                    <li class="mb-2"><strong>Provider Filter:</strong> Click the <strong>Provider</strong> dropdown in the toolbar to isolate the catalog to a specific network operator (e.g., only showing Telkomsel products).</li>
                </ol>
            </div>
        </div>

        <!-- Section 2: Add Product -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-plus-circle text-success mr-2"></i> 2. Adding a New Product</h5>
            <p class="text-muted mb-4">When a new denomination or package becomes available from the upstream provider, you must register it here so merchants can sell it.</p>

            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Click the green <strong><i class="fas fa-plus"></i> Add Product</strong> button in the top right toolbar.</li>
                    <li class="mb-3">In the <em>ADD PRODUCT</em> modal, select the relevant <strong>Provider</strong> from the dropdown.</li>
                    <li class="mb-3">Enter the <strong>Caption</strong> (customer-facing label, e.g., `Pulsa Telkomsel 50.000`) and an optional <strong>Description</strong>.</li>
                    <li class="mb-3">Enter the <strong>Price</strong> (base selling price in Rupiah, no currency symbols).</li>
                    <li class="mb-2">Click <strong>Save Product</strong>. The product goes live immediately.</li>
                </ol>
            </div>
        </div>

        <!-- Section 3: Edit/Delete -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-edit text-info mr-2"></i> 3. Editing or Deleting a Product</h5>
            <p class="text-muted mb-4">If upstream pricing changes, you must update your catalog to maintain margins.</p>

            <div class="pl-4 border-left border-info ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3"><strong>Edit:</strong> Click the <i class="fas fa-ellipsis-v"></i> icon in the ACTION column, select <strong>Edit Product</strong>, update the fields, and click Save Changes.</li>
                    <li class="mb-2"><strong>Delete:</strong> Click the <i class="fas fa-ellipsis-v"></i> icon, select <strong>Delete Product</strong>, and confirm. The product will be hidden from all merchant catalogs.</li>
                </ol>
            </div>
            
            <div class="doc-callout callout-warning shadow-sm mt-4">
                <div class="callout-icon"><i class="fas fa-exclamation-triangle text-warning"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Price Updates are Immediate</strong>
                    <p class="mb-0 text-muted small">Once you save an edited price, all active merchant fee calculations will immediately start using the new price. This does not retroactively affect past transactions.</p>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Common Issues &amp; Troubleshooting</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_pp_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 1: I updated the price, but merchants are still seeing the old price.
                </a>
                <div id="faq_en_pp_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> The merchant dashboard aggressively caches the product catalog for performance. The cache automatically clears every 5 minutes, or merchants can force a refresh from their side.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_pp_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 2: What happens if I delete a product that was just purchased?
                </a>
                <div id="faq_en_pp_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> Deleting a product hides it from the catalog. Any pending transactions for that product will still process normally based on the exact price data captured at the moment of checkout.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_en_pp_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Issue 3: Can I set different product prices for different merchants?
                </a>
                <div id="faq_en_pp_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Answer:</strong> No. The product catalog prices are strictly global. If you want to offer discounts to specific merchants, you should adjust their general MDR/Fee settings instead.
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ID CONTENT -->
    <div class="lang-content lang-id" style="display:none;">

        <p class="doc-lead text-muted" style="line-height: 1.7;">Modul Produk Prabayar (Prepaid Products, mencakup Pulsa Reguler, Paket Data, Token Listrik, dan Top-Up E-Wallet) adalah tempat Anda mengelola katalog produk digital yang tersedia untuk merchant. Perubahan di sini akan langsung memengaruhi katalog dan harga merchant.</p>

        <hr class="my-4">

        <div class="mb-5 p-4" style="background: var(--hc-sidebar-bg); border-radius: 12px; border: 1px solid var(--hc-border);">
            <h6 class="font-weight-bold mb-3" style="color: var(--hc-heading);">Siklus Hidup End-to-End Pembelian PPOB</h6>
            <div class="mermaid">
            sequenceDiagram
                autonumber
                participant M as Aplikasi Merchant
                participant G as Gidi Gateway
                participant A as Agregator PPOB
                participant P as Provider (Telkomsel, PLN)
                
                M->>G: POST /Prepaid/purchase (Kode Produk + Nomor Tujuan)
                G->>G: Potong Saldo Merchant (Pre-auth)
                G->>A: Teruskan Permintaan Pembelian
                A->>P: Injeksi Pulsa/Token
                
                alt Provider Sedang Memproses (Pending)
                    P-->>A: Status: Memproses
                    A-->>G: Status: Pending
                    G-->>M: HTTP 200 (Status: Pending)
                    
                    loop Menunggu Webhook
                        A->>G: Callback Async (Sukses/Gagal)
                    end
                else Provider Langsung Sukses
                    P-->>A: Status: Sukses (Kode SN)
                    A-->>G: Status: Sukses
                    G-->>M: HTTP 200 (Status: Sukses + Kode SN)
                end
                
                alt Jika Gagal
                    G->>G: Auto-Refund Saldo Merchant
                end
            </div>
        </div>

        <!-- UI Overview Table -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-th-list text-primary mr-2"></i> Ikhtisar UI — Kolom Data</h5>
            <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);">
                <table class="table table-borderless table-striped small mb-0" style="background: rgba(255,255,255,0.02);">
                    <thead style="background: rgba(0,0,0,0.4);">
                        <tr>
                            <th class="p-3 border-0" style="width:25%">Kolom</th>
                            <th class="p-3 border-0">Artinya</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td class="p-3 border-0"><strong>PROVIDER</strong></td><td class="p-3 border-0">Operator jaringan atau vendor (misal: XL, Telkomsel, DANA).</td></tr>
                        <tr><td class="p-3 border-0"><strong>CAPTION</strong></td><td class="p-3 border-0">Nama pasti produk atau label denominasi sebagaimana akan muncul bagi merchant dan nasabah.</td></tr>
                        <tr><td class="p-3 border-0"><strong>DESCRIPTION</strong></td><td class="p-3 border-0">Catatan internal atau detail tambahan tentang paket produk spesifik tersebut.</td></tr>
                        <tr><td class="p-3 border-0"><strong>PRICE</strong></td><td class="p-3 border-0">Harga jual dasar (dalam IDR) yang dibebankan ke saldo merchant.</td></tr>
                        <tr><td class="p-3 border-0"><strong>ACTION</strong></td><td class="p-3 border-0">Menu dropdown yang memungkinkan Anda untuk Mengedit (Edit) atau Menghapus (Delete) produk.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 1: Workflow Filtering -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-search text-primary mr-2"></i> 1. Mencari dan Memfilter Produk</h5>
            <p class="text-muted mb-4">Jika Anda memiliki ratusan produk, gunakan toolbar untuk menemukan produk yang perlu Anda perbarui dengan cepat.</p>

            <div class="pl-4 border-left border-primary ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3"><strong>Pencarian Cepat:</strong> Ketik di kotak <em>Search by name, category, or description...</em> untuk langsung memfilter tabel.</li>
                    <li class="mb-2"><strong>Filter Provider:</strong> Klik dropdown <strong>Provider</strong> di toolbar untuk membatasi katalog hanya pada satu operator jaringan tertentu (misal: hanya Telkomsel).</li>
                </ol>
            </div>
        </div>

        <!-- Section 2: Add Product -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-plus-circle text-success mr-2"></i> 2. Menambahkan Produk Baru</h5>
            <p class="text-muted mb-4">Saat denominasi atau paket baru tersedia dari penyedia hulu, daftarkan di sini agar merchant dapat menjualnya.</p>

            <div class="pl-4 border-left border-success ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3">Klik tombol hijau <strong><i class="fas fa-plus"></i> Add Product</strong> di toolbar kanan atas.</li>
                    <li class="mb-3">Di modal <em>ADD PRODUCT</em>, pilih <strong>Provider</strong> yang relevan dari dropdown.</li>
                    <li class="mb-3">Masukkan <strong>Caption</strong> (label untuk pelanggan, misal `Pulsa Telkomsel 50.000`) dan <strong>Description</strong> (opsional).</li>
                    <li class="mb-3">Masukkan <strong>Price</strong> (harga jual dasar dalam Rupiah, tanpa simbol).</li>
                    <li class="mb-2">Klik <strong>Save Product</strong>. Produk tersebut akan langsung aktif.</li>
                </ol>
            </div>
        </div>

        <!-- Section 3: Edit/Delete -->
        <div class="mb-5">
            <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-edit text-info mr-2"></i> 3. Mengedit atau Menghapus Produk</h5>
            <p class="text-muted mb-4">Jika harga dari hulu berubah, Anda harus memperbarui katalog Anda untuk mempertahankan margin keuntungan.</p>

            <div class="pl-4 border-left border-info ml-2 mb-4">
                <ol class="text-muted mb-0">
                    <li class="mb-3"><strong>Edit:</strong> Klik ikon <i class="fas fa-ellipsis-v"></i> di kolom ACTION, pilih <strong>Edit Product</strong>, perbarui isian yang diperlukan, lalu klik Save Changes.</li>
                    <li class="mb-2"><strong>Hapus:</strong> Klik ikon <i class="fas fa-ellipsis-v"></i>, pilih <strong>Delete Product</strong>, dan konfirmasi. Produk akan dihapus dari semua katalog.</li>
                </ol>
            </div>
            
            <div class="doc-callout callout-warning shadow-sm mt-4">
                <div class="callout-icon"><i class="fas fa-exclamation-triangle text-warning"></i></div>
                <div class="callout-content">
                    <strong class="d-block mb-1 text-body" style="font-size: 16px;">Pembaruan Harga Bersifat Instan</strong>
                    <p class="mb-0 text-muted small">Begitu Anda menyimpan harga baru, seluruh perhitungan biaya merchant yang aktif akan langsung menggunakan harga tersebut. Namun, hal ini tidak berlaku mundur untuk transaksi lama.</p>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <h5 class="font-weight-bold mb-4 mt-5 d-flex align-items-center"><i class="fas fa-question-circle text-warning mr-3"></i> Panduan Pemecahan Masalah (FAQ)</h5>
        <div class="faq-accordion mb-5">
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_pp_1" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 1: Harga sudah saya update, tapi di merchant masih harga lama.
                </a>
                <div id="faq_id_pp_1" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Dasbor merchant melakukan cache pada katalog produk untuk menghemat resource. Cache otomatis dibersihkan setiap 5 menit, atau merchant dapat menekan tombol <em>refresh</em> secara manual di dasbor mereka.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_pp_2" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 2: Apa yang terjadi jika saya menghapus produk yang baru saja dibeli?
                </a>
                <div id="faq_id_pp_2" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Menghapus produk hanya akan menyembunyikannya dari katalog. Transaksi pending untuk produk tersebut akan tetap diproses dengan normal sesuai data harga yang dikunci saat checkout dilakukan.
                    </div>
                </div>
            </div>
            <div class="border-0 mb-3 border-bottom pb-2">
                <a href="#faq_id_pp_3" data-toggle="collapse" class="d-block text-body text-decoration-none font-weight-bold pb-2">
                    <i class="fas fa-chevron-right mr-2 text-muted" style="font-size:0.8rem;"></i> Masalah 3: Bisakah saya mengatur harga berbeda untuk merchant tertentu?
                </a>
                <div id="faq_id_pp_3" class="collapse">
                    <div class="text-muted px-4 pb-4 pt-1" style="line-height: 1.7; font-size: 0.9rem;">
                        <strong>Jawaban:</strong> Tidak. Harga katalog produk prabayar bersifat murni global. Jika Anda ingin memberikan diskon khusus pada merchant VIP, silakan kurangi pengaturan persentase MDR/Biaya Transaksi mereka saja.
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
