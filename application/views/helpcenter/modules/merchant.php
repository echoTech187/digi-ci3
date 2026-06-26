<div id="module-merchant" class="hc-doc-section">
    <div class="ug-module-content">
        <!-- EN CONTENT -->
        <div class="lang-content lang-en" style="display:block;">
            
            <!-- 1. Conceptual Overview -->
            <div class="hc-premium-overview mb-4">
                <h4 class="font-weight-bold mb-3"><i class="fas fa-book text-primary mr-2"></i> Overview</h4>
                <p class="mb-0">The <strong>Merchant Management</strong> page is the central hub for onboarding new business clients, managing their operational lifecycle, and monitoring their global account health. Every entity that transacts through the payment gateway must be registered here.</p>
            </div>

            <!-- 2. Visual Step-by-Step Walkthrough -->
            <h4 class="font-weight-bold mb-4 border-bottom pb-2">Procedural Walkthrough</h4>

            <!-- Step 1 (Image Right) -->
            <div class="row hc-step-row align-items-center mb-5">
                <div class="col-lg-5 order-2 order-lg-1">
                    <div class="hc-step-number">1</div>
                    <h3 class="hc-step-title">Access the Module</h3>
                    <div class="pl-4 border-left border-success ml-2 mt-3">
                        <ol class="hc-step-desc mb-0">
                            <li class="mb-2">From the main dashboard, navigate to the <strong>Merchant Management</strong> menu on the left sidebar.</li>
                            <li class="mb-2">Click to open the Merchant Directory.</li>
                        </ol>
                    </div>
                </div>
                <div class="col-lg-7 order-1 order-lg-2 mb-4 mb-lg-0">
                    <div class="mac-window">
                        <div class="mac-body">
                            <img src="<?= base_url('assets/img/helpcenter/actual_dashboard_step1_premium.png') ?>" alt="Sidebar Navigation" style="width: 100%; display: block; object-fit: cover; object-position: left top; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2 (Image Left) -->
            <div class="row hc-step-row align-items-center mb-5">
                <div class="col-lg-8">
                    <div class="hc-step-number">2</div>
                    <h3 class="hc-step-title">Searching & Filtering</h3>
                    <p class="text-muted mb-4">Use the built-in search and filters to track down specific merchants.</p>

                    <div class="pl-4 border-left border-success ml-2 mt-3">
                        <ol class="hc-step-desc mb-0">
                            <li class="mb-3"><strong>Quick Search:</strong> Type in the <em>Search by name, ID, email, or Business ID...</em> box to instantly filter the table.</li>
                            <li class="mb-3"><strong>Advanced Filters:</strong> Click the <i class="fas fa-sliders-h"></i> <strong>Filters</strong> button to open the <strong>Advanced Filters</strong> panel.</li>
                            <li class="mb-3">Configure your parameters: <strong>Registration Date</strong>, <strong>Account Status</strong>, or <strong>OpenAPI Status</strong>.</li>
                            <li class="mb-2">Click the area outside the dropdown to load the data. Active filters are indicated by a red badge number. Click <strong>Clear All</strong> to clear all filters.</li>
                        </ol>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="mac-window">
                        <div class="mac-body">
                            <img src="<?= base_url('assets/img/helpcenter/advanced_filters_matching.png') ?>" alt="Advanced Filters" style="width: 100%; display: block; object-fit: cover; object-position: left top; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3 (Image Right) -->
            <div class="row hc-step-row align-items-center mb-5">
                <div class="col-lg-12 order-2 order-xl-1">
                    <div class="hc-step-number">3</div>
                    <h3 class="hc-step-title">UI Overview — Merchant Directory</h3>
                    <div class="col-lg-12 order-1 order-lg-2 mb-4 mb-lg-0 p-0">
                        <div class="mac-window mb-4">
                            <div class="mac-body">
                                <img src="<?= base_url('assets/img/helpcenter/data_table.png') ?>" alt="Data Table" style="width: 100%; display: block; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive m-0">
                        <table class="table table-bordered hc-ref-table bg-white">
                            <thead>
                                <tr>
                                    <th class="p-3 border-0" style="width: 30%;">Column / Filter</th>
                                    <th class="p-3 border-0">Description & Logic</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td class="p-3 border-0"><strong>Merchant Name & Email</strong></td><td class="p-3 border-0">The registered business entity. The email is their unique login identifier.</td></tr>
                                <tr><td class="p-3 border-0"><strong>Account Status</strong></td><td class="p-3 border-0">Governs Dashboard login. <span style="color:#16a34a;font-weight:600;">Active</span> = Login allowed. <span style="color:#dc2626;font-weight:600;">Blocked/Frozen</span> = Login denied.</td></tr>
                                <tr><td class="p-3 border-0"><strong>OpenAPI Status</strong></td><td class="p-3 border-0">Governs API endpoints. If Blocked, all programmatic payment requests are rejected (even if Account Status is Active).</td></tr>
                                <tr><td class="p-3 border-0"><strong>Master Balance</strong></td><td class="p-3 border-0">The real-time global settlement balance. This is updated synchronously with every transaction.</td></tr>
                                <tr><td colspan="2" class="p-3 border-0 bg-light"><strong>Action Menu (⋮)</strong> - Provides direct links to manage this specific merchant:</td></tr>
                                <tr><td class="p-3 border-0 pl-4"><i class="fas fa-eye text-primary mr-2"></i> <strong>Detail Merchant</strong></td><td class="p-3 border-0">View a comprehensive, read-only summary of the merchant's profile, active configurations, and current operational limits without the risk of accidental modification.
                                <br><a href="javascript:void(0);" onclick="window.activateHelpModule('module-ug-merchant-detail'); window.scrollTo(0,0);" class="text-primary mt-2 d-inline-block">Read Guide <i class="fas fa-arrow-right"></i></a></td></tr>
                                <tr><td class="p-3 border-0 pl-4"><i class="fas fa-edit text-info mr-2"></i> <strong>Edit Merchant</strong></td><td class="p-3 border-0">Modify the merchant's core identity, update callback URLs, reset access credentials, or instantly toggle their Account and OpenAPI status.</td></tr>
                                <tr><td class="p-3 border-0 pl-4"><i class="fas fa-exchange-alt text-warning mr-2"></i> <strong>Mutation Log</strong></td><td class="p-3 border-0">Access the merchant's detailed financial ledger. This provides a chronological record of all balance movements, settlements, and manual adjustments for auditing purposes.
                                <br><a href="javascript:void(0);" onclick="window.activateHelpModule('module-ug-merchant-mutation'); window.scrollTo(0,0);" class="text-primary mt-2 d-inline-block">Read Guide <i class="fas fa-arrow-right"></i></a></td></tr>
                                <tr><td class="p-3 border-0 pl-4"><i class="fas fa-users text-purple mr-2" style="color: #6f42c1;"></i> <strong>Sub Accounts</strong></td><td class="p-3 border-0">Navigate to the sub-account management dashboard. This allows you to oversee or provision branch-level accounts operating under this master merchant's umbrella.
                                <br><a href="javascript:void(0);" onclick="window.activateHelpModule('module-ug-merchant-subaccount'); window.scrollTo(0,0);" class="text-primary mt-2 d-inline-block">Read Guide <i class="fas fa-arrow-right"></i></a></td></tr>
                                <tr><td class="p-3 border-0 pl-4"><i class="fas fa-key text-dark mr-2"></i> <strong>Delegate</strong></td><td class="p-3 border-0">Assign specific operational roles or delegate temporary administrative access to the merchant's workspace for support or troubleshooting purposes.
                                <br><a href="javascript:void(0);" onclick="window.activateHelpModule('module-ug-merchant-delegate'); window.scrollTo(0,0);" class="text-primary mt-2 d-inline-block">Read Guide <i class="fas fa-arrow-right"></i></a></td></tr>
                                <tr><td class="p-3 border-0 pl-4"><i class="fas fa-plus-circle text-success mr-2"></i> <strong>Add Credit Balance</strong></td><td class="p-3 border-0">Manually inject funds into the merchant's master balance. Typically used for offline settlements, refunds, or promotional top-ups. Requires a descriptive note for auditing.
                                <br><a href="javascript:void(0);" onclick="window.activateHelpModule('module-ug-merchant-credit'); window.scrollTo(0,0);" class="text-primary mt-2 d-inline-block">Read Guide <i class="fas fa-arrow-right"></i></a></td></tr>
                                <tr><td class="p-3 border-0 pl-4"><i class="fas fa-minus-circle text-danger mr-2"></i> <strong>Deduct Debit Balance</strong></td><td class="p-3 border-0">Manually deduct funds from the merchant's master balance. Useful for operational corrections, fee collections, or chargebacks. Requires a descriptive note for auditing.
                                <br><a href="javascript:void(0);" onclick="window.activateHelpModule('module-ug-merchant-debit'); window.scrollTo(0,0);" class="text-primary mt-2 d-inline-block">Read Guide <i class="fas fa-arrow-right"></i></a></td></tr>
                                <tr><td class="p-3 border-0 pl-4"><i class="fas fa-cog text-secondary mr-2"></i> <strong>Cashin Fee Settings</strong></td><td class="p-3 border-0">Configure personalized inbound payment routing rules, transaction limits, and specialized pricing structures (MDR) that override global gateway defaults for this specific merchant.
                                <br><a href="javascript:void(0);" onclick="window.activateHelpModule('module-ug-merchant-cashin'); window.scrollTo(0,0);" class="text-primary mt-2 d-inline-block">Read Guide <i class="fas fa-arrow-right"></i></a></td></tr>
                                <tr><td class="p-3 border-0 pl-4"><i class="fas fa-cog text-secondary mr-2"></i> <strong>Cashout Fee Settings</strong></td><td class="p-3 border-0">Establish customized outbound disbursement fees, set daily withdrawal limits, and manage the specific cashout providers available to this merchant.
                                <br><a href="javascript:void(0);" onclick="window.activateHelpModule('module-ug-merchant-cashout'); window.scrollTo(0,0);" class="text-primary mt-2 d-inline-block">Read Guide <i class="fas fa-arrow-right"></i></a></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Step 4 (Image Left) -->
            <div class="row hc-step-row align-items-center mb-5">
                <div class="col-lg-12">
                    <div class="hc-step-number">4</div>
                    <h3 class="hc-step-title">Onboarding a New Merchant</h3>
                    <p class="text-muted mb-4">Register new business entities and initialize their operational configurations.</p>
                    <div class="mac-window mb-4">
                        <div class="mac-body">
                            <img src="<?= base_url('assets/img/helpcenter/form_modal.png') ?>" alt="Form Modal" style="width: 100%; display: block; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                        </div>
                    </div>
                    <div class="pl-4 border-left border-success ml-2 mt-3">
                        <ol class="hc-step-desc mb-0">
                            <li class="mb-3">Click the <strong><i class="fas fa-plus"></i> Register Merchant</strong> button.</li>
                            <li class="mb-3">
                                Fill out the comprehensive registration form fields:
                                <div class="mt-2 text-muted pl-3 border-left mb-2"><strong>Account Information</strong></div>
                                <ul class="text-muted mb-3" style="list-style-type: disc;">
                                    <li class="mb-1"><strong>Merchant Name:</strong> The official business name. Required.</li>
                                    <li class="mb-1"><strong>Merchant Email:</strong> Primary contact and login ID. Must be strictly unique across the entire system. Required.</li>
                                    <li class="mb-1"><strong>Merchant Phone:</strong> Primary contact number (digits only).</li>
                                    <li class="mb-1"><strong>GVConnect Business ID:</strong> Optional identifier for external integrations.</li>
                                    <li class="mb-1"><strong>Password & Confirm Password:</strong> Initial login credential (minimum 6 characters). Required.</li>
                                </ul>
                                <div class="text-muted pl-3 border-left mb-2"><strong>OpenAPI Configuration</strong></div>
                                <ul class="text-muted mb-3" style="list-style-type: disc;">
                                    <li class="mb-1"><strong>Whitelist IP:</strong> Restrict API access to specific IPs (semicolon separated, e.g., <code>1.2.3.4; 5.6.7.8</code>).</li>
                                    <li class="mb-1"><strong>Callback QRIS MPM / E-wallet / VA:</strong> The HTTPS endpoints on the merchant's server where transaction webhooks will be sent.</li>
                                </ul>
                                <div class="text-muted pl-3 border-left mb-2"><strong>Service Permissions</strong></div>
                                <ul class="text-muted mb-2" style="list-style-type: disc;">
                                    <li class="mb-1"><strong>Virtual Account / QRIS & E-Wallet / Transfer:</strong> Checkboxes to explicitly grant or deny access to specific transaction channels (e.g., <code>VA Dynamic Create</code>, <code>BI-FAST Transfer</code>).</li>
                                </ul>
                            </li>
                            <li class="mb-3">Optionally assign them to a Supervisor Group if they belong to a parent franchise.</li>
                            <li class="mb-2">Click <strong>Register</strong> and immediately copy the generated Secret URL to securely transmit to the client.</li>
                        </ol>
                    </div>
                </div>
            </div>

            
            <!-- Step 5 (Edit Merchant) -->
            <div class="row hc-step-row align-items-center mb-5">
                <div class="col-lg-12">
                    <div class="hc-step-number">5</div>
                    <h3 class="hc-step-title">Editing Merchant Profile</h3>
                    <p class="text-muted mb-4">Keep merchant contact information, statuses, and credentials up-to-date.</p>
                    <div class="pl-4 border-left border-success ml-2 mt-3">
                        <ol class="hc-step-desc mb-0">
                            <li class="mb-3">Open the <strong>Action Menu (⋮)</strong> for a specific merchant and click <strong><i class="fas fa-edit text-info"></i> Edit Merchant</strong>.</li>
                            <li class="mb-3">
                                A dedicated configuration page will load. You can update the following fields:
                                <div class="mt-2 text-muted pl-3 border-left mb-2"><strong>Account Information</strong></div>
                                <ul class="text-muted mb-3" style="list-style-type: disc;">
                                    <li class="mb-1"><strong>Merchant Name & Phone & GVConnect ID:</strong> Update general business details.</li>
                                    <li class="mb-1"><strong>Merchant Email:</strong> Must remain unique. <em>Note: Changing this will forcefully terminate the merchant's active login session.</em></li>
                                    <li class="mb-1"><strong>Password:</strong> Leave blank to keep the current password, or input a new one to forcefully reset it.</li>
                                </ul>
                                <div class="text-muted pl-3 border-left mb-2"><strong>OpenAPI Configuration & Permissions</strong></div>
                                <ul class="text-muted mb-3" style="list-style-type: disc;">
                                    <li class="mb-1"><strong>Whitelist IP & Callbacks:</strong> Adjust network security and webhook endpoints.</li>
                                    <li class="mb-1"><strong>Service Permissions Checkboxes:</strong> Grant or revoke access to specific payment channels in real-time.</li>
                                </ul>
                                <div class="text-muted pl-3 border-left mb-2"><strong>System Status</strong></div>
                                <ul class="text-muted mb-2" style="list-style-type: disc;">
                                    <li class="mb-1"><strong>Merchant Account Status:</strong> Controls dashboard login. <code>Active</code> allows login, <code>Blocked</code> or <code>Freeze</code> denies login.</li>
                                    <li class="mb-1"><strong>OpenAPI Access Status:</strong> Controls programmatic API. <code>Active</code> allows transactions, <code>Blocked</code> halts all incoming API requests and webhooks.</li>
                                </ul>
                            </li>
                            <li class="mb-3">If they lost their dashboard access, you can manually input a new Password here.</li>
                            <li class="mb-2">Click <strong>Save Changes</strong> to instantly apply the new configurations. Note: Changing an email will invalidate their current login session.</li>
                        </ol>
                    </div>
                </div>
            </div>




            <!-- Parameter Reference & Validations -->
            <h4 class="font-weight-bold mt-5 mb-4 border-bottom pb-2">Parameter Reference</h4>
            <div class="mb-5">
                <p class="text-muted mb-4">When registering or editing a merchant, the following core parameters define their identity and access levels within the gateway:</p>
                <div class="table-responsive m-0">
                    <table class="table table-bordered hc-ref-table bg-white">
                        <thead>
                            <tr>
                                <th class="p-3 border-0" style="width: 25%;">Parameter</th>
                                <th class="p-3 border-0">Description & Validation</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="p-3 border-0"><strong>Merchant Name</strong></td>
                                <td class="p-3 border-0">The legal or operational name of the business entity. This is displayed on invoices and dashboards.</td>
                            </tr>
                            <tr>
                                <td class="p-3 border-0"><strong>Merchant Email</strong></td>
                                <td class="p-3 border-0">The primary contact email. This serves as the unique global identifier and login username. <em>Validation: Must be unique across the entire system.</em></td>
                            </tr>
                            <tr>
                                <td class="p-3 border-0"><strong>Password</strong></td>
                                <td class="p-3 border-0">The login credential for the merchant dashboard. Required during initial onboarding.</td>
                            </tr>
                            <tr>
                                <td class="p-3 border-0"><strong>Account Status</strong></td>
                                <td class="p-3 border-0">Toggles dashboard access. <code>Active</code> permits login, while <code>Blocked</code> revokes all dashboard access immediately.</td>
                            </tr>
                            <tr>
                                <td class="p-3 border-0"><strong>OpenAPI Status</strong></td>
                                <td class="p-3 border-0">Controls programmatic access. <code>Active</code> allows processing API payments, while <code>Blocked</code> rejects all inbound API requests and halts webhooks.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-bell text-info mr-2"></i> System Notifications</h6>
                <div class="d-flex flex-column mb-4">
                    <div class="mb-3">
                        <div class="p-3 rounded border" style="background-color: rgba(22, 163, 74, 0.05); border-color: rgba(22, 163, 74, 0.2) !important;">
                            <strong class="text-success d-block mb-3"><i class="fas fa-check-circle mr-1"></i> Success Events</strong>
                            <div class="d-flex align-items-center mb-0 small text-muted">
                                <i class="fas fa-info-circle text-success mr-2"></i>
                                <div><strong>Merchant Created:</strong> <code class="ml-1">Merchant successfully registered.</code></div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-1">
                        <div class="p-3 rounded border" style="background-color: rgba(220, 38, 38, 0.05); border-color: rgba(220, 38, 38, 0.2) !important;">
                            <strong class="text-danger d-block mb-3"><i class="fas fa-exclamation-circle mr-1"></i> Error Events & Solutions</strong>
                            
                            <div class="mb-3 small">
                                <div class="text-muted mb-2"><i class="fas fa-times-circle text-danger mr-2"></i><strong>Duplicate Email (1062):</strong> <code class="ml-1">Email address already in use.</code></div>
                                <div class="p-2 rounded text-dark" style="background-color: #fff3cd; border-left: 3px solid #ffc107; margin-left: 20px;">
                                    <i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solution:</strong> Ask the client for a different email address or search for the existing merchant record.
                                </div>
                            </div>
                            
                            <div class="mb-0 small">
                                <div class="text-muted mb-2"><i class="fas fa-times-circle text-danger mr-2"></i><strong>Access Denied (1142):</strong> <code class="ml-1">Access Denied. You do not have sufficient privileges.</code></div>
                                <div class="p-2 rounded text-dark" style="background-color: #fff3cd; border-left: 3px solid #ffc107; margin-left: 20px;">
                                    <i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solution:</strong> The MySQL user lacks INSERT privileges. Contact the Database Administrator.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Troubleshooting -->
            <h4 class="font-weight-bold mb-4 border-bottom pb-2">Troubleshooting & FAQ</h4>
            <div class="hc-faq-item">
                <div class="hc-faq-q">
                    <i class="fas fa-exclamation-circle text-danger"></i> 
                    <span>Why do I get an "Email already registered" error during onboarding?
                    </span>
                </div>
                <p class="hc-faq-a">The email address is already bound to another merchant entity. Emails act as unique global identifiers in the gateway. Use the Global Search to find the existing account, or ask the client for a different organizational email.</p>
            </div>
            <div class="hc-faq-item">
                <div class="hc-faq-q">
                    <i class="fas fa-question-circle text-warning"></i> 
                    <span>Why can the merchant log in but their API payments are failing?
                    </span>
                </div>
                <p class="hc-faq-a">This implies an asymmetry between their statuses. Open the Edit Merchant form and verify that the <strong>OpenAPI Status</strong> is <code>Active</code>. If it is active, check their API Secret Key and IP Whitelist in the Secret configurations.</p>
            </div>
            <div class="hc-faq-item">
                <div class="hc-faq-q">
                    <i class="fas fa-info-circle text-primary"></i> 
                    <span>Why can't I find a specific merchant in the list?
                    </span>
                </div>
                <p class="hc-faq-a">Your previous search or status filters are likely still active in your session. Click the <strong>Clear All</strong> button in the Advanced Filters panel to clear all session variables and reload the complete, unfiltered directory.</p>
            </div>
            <div class="hc-faq-item">
                <div class="hc-faq-q">
                    <i class="fas fa-lock text-secondary"></i> 
                    <span>What should I do if the merchant lost their Secret URL or it expired?
                    </span>
                </div>
                <p class="hc-faq-a">The auto-generated Secret URL self-destructs after 24 hours. To grant access, an Admin must open the <strong>Edit Merchant</strong> form, manually reset the password, and share the new credentials securely.</p>
            </div>
            <div class="hc-faq-item">
                <div class="hc-faq-q">
                    <i class="fas fa-network-wired text-info"></i> 
                    <span>Why are webhook callbacks failing to reach the Merchant's server?
                    </span>
                </div>
                <p class="hc-faq-a">Verify that the Merchant's <strong>OpenAPI Status</strong> is Active. If it is, navigate to the <strong>Secret Keys</strong> module to ensure their webhook URL is correctly formatted, accessible, and responding within the 5-second timeout window.</p>
            </div>
            <div class="hc-faq-item">
                <div class="hc-faq-q">
                    <i class="fas fa-hand-holding-usd text-danger"></i> 
                    <span>Why was my manual balance deduction (Deduct Debit) rejected?
                    </span>
                </div>
                <p class="hc-faq-a">The system enforces a strict non-negative balance policy. You cannot manually deduct a debit amount that exceeds the merchant's current Master Balance.</p>
            </div>

            
<!-- What's Next -->
            <div class="mt-5 pt-4 border-top" style="border-color: var(--hc-border) !important;">
                <h6 class="font-weight-bold mb-3 text-muted">What's Next?</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded" onclick="window.activateHelpModule('module-ug-merchant-subaccount'); window.scrollTo(0,0);" style="cursor: pointer; background: var(--hc-bg); border-color: var(--hc-border); transition: all 0.2s;" onmouseover="this.style.borderColor='var(--hc-active-text)'" onmouseout="this.style.borderColor='var(--hc-border)'">
                            <div class="font-weight-bold text-primary mb-1">Step 2: Sub-Accounts <i class="fas fa-arrow-right float-right mt-1"></i></div>
                            <div class="small text-muted">Learn how to create branch accounts and assign supervisors.</div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded" onclick="window.activateHelpModule('module-secret'); window.scrollTo(0,0);" style="cursor: pointer; background: var(--hc-bg); border-color: var(--hc-border); transition: all 0.2s;" onmouseover="this.style.borderColor='var(--hc-active-text)'" onmouseout="this.style.borderColor='var(--hc-border)'">
                            <div class="font-weight-bold text-primary mb-1">Step 3: Secret Keys <i class="fas fa-arrow-right float-right mt-1"></i></div>
                            <div class="small text-muted">Configure API keys, webhooks, and IP whitelists for this merchant.</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- ID CONTENT -->
        <div class="lang-content lang-id" style="display:none;">

            <!-- 1. Conceptual Overview -->
            <div class="hc-premium-overview mb-4">
                <h4 class="font-weight-bold mb-3"><i class="fas fa-book text-primary mr-2"></i> Ikhtisar (Overview)</h4>
                <p class="mb-0">Halaman <strong>Merchant Management</strong> adalah pusat utama untuk melakukan onboarding klien bisnis baru, mengelola siklus operasional mereka, dan memantau kesehatan akun secara global. Setiap entitas yang bertransaksi melalui payment gateway wajib terdaftar di sini.</p>
            </div>

            <!-- 2. Visual Step-by-Step Walkthrough -->
            <h4 class="font-weight-bold mb-4 border-bottom pb-2">Panduan Langkah-demi-Langkah</h4>

            <!-- Step 1 (Image Right) -->
            <div class="row hc-step-row align-items-center mb-5">
                <div class="col-lg-5 order-2 order-lg-1">
                    <div class="hc-step-number">1</div>
                    <h3 class="hc-step-title">Mengakses Modul</h3>
                    <div class="pl-4 border-left border-success ml-2 mt-3">
                        <ol class="hc-step-desc mb-0">
                            <li class="mb-2">Dari dasbor utama, navigasikan kursor ke menu <strong>Merchant Management</strong> di bilah sisi kiri.</li>
                            <li class="mb-2">Klik untuk membuka direktori Merchant.</li>
                        </ol>
                    </div>
                </div>
                <div class="col-lg-7 order-1 order-lg-2 mb-4 mb-lg-0">
                    <div class="mac-window">
                        <div class="mac-body">
                            <img src="<?= base_url('assets/img/helpcenter/actual_dashboard_step1_premium.png') ?>" alt="Sidebar Navigation" style="width: 100%; display: block; object-fit: cover; object-position: left top; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2 (Image Left) -->
            <div class="row hc-step-row align-items-center mb-5">
                <div class="col-lg-8">
                    <div class="hc-step-number">2</div>
                    <h3 class="hc-step-title">Pencarian & Pemfilteran</h3>
                    <p class="text-muted mb-4">Gunakan pencarian bawaan dan filter untuk melacak merchant spesifik.</p>

                    <div class="pl-4 border-left border-success ml-2 mt-3">
                        <ol class="hc-step-desc mb-0">
                            <li class="mb-3"><strong>Pencarian Cepat:</strong> Ketik di kotak <em>Search by name, ID, email, or Business ID...</em> untuk memfilter tabel secara instan.</li>
                            <li class="mb-3"><strong>Filter Lanjutan:</strong> Klik tombol <i class="fas fa-sliders-h"></i> <strong>Filters</strong> untuk membuka panel <strong>Advanced Filters</strong>.</li>
                            <li class="mb-3">Saring parameter Anda: <strong>Registration Date</strong>, <strong>Account Status</strong>, atau <strong>OpenAPI Status</strong>.</li>
                            <li class="mb-2">Klik area di luar dropdown untuk memuat data. Filter aktif ditandai dengan lencana merah. Klik <strong>Clear All</strong> untuk mengosongkan semua filter.</li>
                        </ol>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="mac-window">
                        <div class="mac-body">
                            <img src="<?= base_url('assets/img/helpcenter/advanced_filters_matching.png') ?>" alt="Filter Lanjutan" style="width: 100%; display: block; object-fit: cover; object-position: left top; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3 (Image Right) -->
            <div class="row hc-step-row align-items-center mb-5">
                <div class="col-lg-12 order-2 order-xl-1">
                    <div class="hc-step-number">3</div>
                    <h3 class="hc-step-title">Ikhtisar UI — Direktori Merchant</h3>
                    <div class="col-lg-12 order-1 order-lg-2 mb-4 mb-lg-0 p-0">
                        <div class="mac-window mb-4">
                            <div class="mac-body">
                                <img src="<?= base_url('assets/img/helpcenter/data_table.png') ?>" alt="Tabel Data" style="width: 100%; display: block; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive m-0">
                        <table class="table table-bordered hc-ref-table bg-white">
                            <thead>
                                <tr>
                                    <th class="p-3 border-0" style="width: 30%;">Kolom / Filter</th>
                                    <th class="p-3 border-0">Deskripsi & Logika</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td class="p-3 border-0"><strong>Nama & Email Merchant</strong></td><td class="p-3 border-0">Entitas bisnis yang terdaftar. Email berfungsi sebagai pengidentifikasi login unik mereka.</td></tr>
                                <tr><td class="p-3 border-0"><strong>Account Status</strong></td><td class="p-3 border-0">Mengatur hak login Dashboard. <span style="color:#16a34a;font-weight:600;">Active</span> = Bisa login. <span style="color:#dc2626;font-weight:600;">Blocked/Frozen</span> = Login ditolak.</td></tr>
                                <tr><td class="p-3 border-0"><strong>OpenAPI Status</strong></td><td class="p-3 border-0">Mengatur endpoint API. Jika Blocked, semua request transaksi via sistem akan ditolak (walaupun Account Status berstatus Active).</td></tr>
                                <tr><td class="p-3 border-0"><strong>Master Balance</strong></td><td class="p-3 border-0">Saldo penyelesaian (settlement) global secara real-time. Diperbarui seketika seiring terjadinya transaksi.</td></tr>
                                <tr><td colspan="2" class="p-3 border-0 bg-light"><strong>Menu Aksi (⋮)</strong> - Pintasan manajemen spesifik untuk merchant ini:</td></tr>
                                <tr><td class="p-3 border-0 pl-4"><i class="fas fa-eye text-primary mr-2"></i> <strong>Detail Merchant</strong></td><td class="p-3 border-0">Melihat ringkasan komprehensif profil merchant, konfigurasi aktif, dan batasan operasional tanpa risiko modifikasi tidak sengaja.
                                <br><a href="javascript:void(0);" onclick="window.activateHelpModule('module-ug-merchant-detail'); window.scrollTo(0,0);" class="text-primary mt-2 d-inline-block">Baca Panduan <i class="fas fa-arrow-right"></i></a></td></tr>
                                <tr><td class="p-3 border-0 pl-4"><i class="fas fa-edit text-info mr-2"></i> <strong>Edit Merchant</strong></td><td class="p-3 border-0">Memodifikasi identitas inti merchant, memperbarui URL callback, mereset kredensial akses, atau mengubah status Account dan OpenAPI.</td></tr>
                                <tr><td class="p-3 border-0 pl-4"><i class="fas fa-exchange-alt text-warning mr-2"></i> <strong>Mutation Log</strong></td><td class="p-3 border-0">Mengakses buku besar keuangan (ledger) merchant secara mendetail. Menampilkan rekaman kronologis perpindahan saldo, settlement, dan penyesuaian manual untuk keperluan audit.
                                <br><a href="javascript:void(0);" onclick="window.activateHelpModule('module-ug-merchant-mutation'); window.scrollTo(0,0);" class="text-primary mt-2 d-inline-block">Baca Panduan <i class="fas fa-arrow-right"></i></a></td></tr>
                                <tr><td class="p-3 border-0 pl-4"><i class="fas fa-users text-purple mr-2" style="color: #6f42c1;"></i> <strong>Sub Accounts</strong></td><td class="p-3 border-0">Membuka dashboard manajemen sub-akun. Memungkinkan Anda untuk mengawasi atau membuat akun tingkat cabang di bawah naungan merchant utama ini.
                                <br><a href="javascript:void(0);" onclick="window.activateHelpModule('module-ug-merchant-subaccount'); window.scrollTo(0,0);" class="text-primary mt-2 d-inline-block">Baca Panduan <i class="fas fa-arrow-right"></i></a></td></tr>
                                <tr><td class="p-3 border-0 pl-4"><i class="fas fa-key text-dark mr-2"></i> <strong>Delegate</strong></td><td class="p-3 border-0">Menetapkan peran operasional khusus atau mendelegasikan akses administratif sementara ke ruang kerja merchant untuk tujuan dukungan atau pemecahan masalah.
                                <br><a href="javascript:void(0);" onclick="window.activateHelpModule('module-ug-merchant-delegate'); window.scrollTo(0,0);" class="text-primary mt-2 d-inline-block">Baca Panduan <i class="fas fa-arrow-right"></i></a></td></tr>
                                <tr><td class="p-3 border-0 pl-4"><i class="fas fa-plus-circle text-success mr-2"></i> <strong>Add Credit Balance</strong></td><td class="p-3 border-0">Menambahkan dana secara manual ke saldo utama merchant. Biasanya digunakan untuk settlement offline, pengembalian dana (refund), atau top-up promosi. Memerlukan catatan deskriptif untuk audit.
                                <br><a href="javascript:void(0);" onclick="window.activateHelpModule('module-ug-merchant-credit'); window.scrollTo(0,0);" class="text-primary mt-2 d-inline-block">Baca Panduan <i class="fas fa-arrow-right"></i></a></td></tr>
                                <tr><td class="p-3 border-0 pl-4"><i class="fas fa-minus-circle text-danger mr-2"></i> <strong>Deduct Debit Balance</strong></td><td class="p-3 border-0">Memotong dana secara manual dari saldo utama merchant. Berguna untuk koreksi operasional, penagihan biaya, atau chargeback. Memerlukan catatan deskriptif untuk audit.
                                <br><a href="javascript:void(0);" onclick="window.activateHelpModule('module-ug-merchant-debit'); window.scrollTo(0,0);" class="text-primary mt-2 d-inline-block">Baca Panduan <i class="fas fa-arrow-right"></i></a></td></tr>
                                <tr><td class="p-3 border-0 pl-4"><i class="fas fa-cog text-secondary mr-2"></i> <strong>Cashin Fee Settings</strong></td><td class="p-3 border-0">Mengonfigurasi aturan rute pembayaran masuk khusus, batasan transaksi, dan struktur harga khusus (MDR) yang mengesampingkan default gateway global untuk merchant ini.
                                <br><a href="javascript:void(0);" onclick="window.activateHelpModule('module-ug-merchant-cashin'); window.scrollTo(0,0);" class="text-primary mt-2 d-inline-block">Baca Panduan <i class="fas fa-arrow-right"></i></a></td></tr>
                                <tr><td class="p-3 border-0 pl-4"><i class="fas fa-cog text-secondary mr-2"></i> <strong>Cashout Fee Settings</strong></td><td class="p-3 border-0">Menetapkan biaya pengeluaran dana (disbursement) khusus, mengatur batasan penarikan harian, dan mengelola penyedia cashout khusus yang tersedia untuk merchant ini.
                                <br><a href="javascript:void(0);" onclick="window.activateHelpModule('module-ug-merchant-cashout'); window.scrollTo(0,0);" class="text-primary mt-2 d-inline-block">Baca Panduan <i class="fas fa-arrow-right"></i></a></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Step 4 (Image Left) -->
            <div class="row hc-step-row align-items-center mb-5">
                <div class="col-lg-12">
                    <div class="hc-step-number">4</div>
                    <h3 class="hc-step-title">Onboarding Merchant Baru</h3>
                    <p class="text-muted mb-4">Mendaftarkan entitas bisnis baru dan menginisialisasi konfigurasi operasional mereka.</p>
                    <div class="mac-window mb-4">
                        <div class="mac-body">
                            <img src="<?= base_url('assets/img/helpcenter/form_modal.png') ?>" alt="Form Modal" style="width: 100%; display: block; border-bottom-left-radius: 11px; border-bottom-right-radius: 11px;">
                        </div>
                    </div>
                    <div class="pl-4 border-left border-success ml-2 mt-3">
                        <ol class="hc-step-desc mb-0">
                            <li class="mb-3">Klik tombol <strong><i class="fas fa-plus"></i> Register Merchant</strong>.</li>
                            <li class="mb-3">
                                Isi setiap kolom pada formulir pendaftaran secara mendetail:
                                <div class="mt-2 text-muted pl-3 border-left mb-2"><strong>Account Information</strong></div>
                                <ul class="text-muted mb-3" style="list-style-type: disc;">
                                    <li class="mb-1"><strong>Merchant Name:</strong> Nama resmi entitas bisnis. Wajib.</li>
                                    <li class="mb-1"><strong>Merchant Email:</strong> Email utama dan ID login. Harus valid dan belum terdaftar sama sekali di sistem (wajib unik). Wajib.</li>
                                    <li class="mb-1"><strong>Merchant Phone:</strong> Nomor telepon kontak utama.</li>
                                    <li class="mb-1"><strong>GVConnect Business ID:</strong> Pengenal opsional untuk integrasi eksternal.</li>
                                    <li class="mb-1"><strong>Password & Confirm Password:</strong> Kredensial login awal (minimal 6 karakter). Wajib.</li>
                                </ul>
                                <div class="text-muted pl-3 border-left mb-2"><strong>OpenAPI Configuration</strong></div>
                                <ul class="text-muted mb-3" style="list-style-type: disc;">
                                    <li class="mb-1"><strong>Whitelist IP:</strong> Batasi akses API hanya dari IP tertentu (pisahkan dengan titik koma, misal: <code>1.2.3.4; 5.6.7.8</code>).</li>
                                    <li class="mb-1"><strong>Callback QRIS MPM / E-wallet / VA:</strong> Endpoint HTTPS di server merchant tempat webhook transaksi akan dikirim.</li>
                                </ul>
                                <div class="text-muted pl-3 border-left mb-2"><strong>Service Permissions</strong></div>
                                <ul class="text-muted mb-2" style="list-style-type: disc;">
                                    <li class="mb-1"><strong>Virtual Account / QRIS & E-Wallet / Transfer:</strong> Centang kotak untuk memberikan atau menolak akses ke saluran transaksi spesifik (misal: <code>VA Dynamic Create</code>).</li>
                                </ul>
                            </li>
                            <li class="mb-3">Pilih Grup Supervisor jika klien merupakan cabang dari perusahaan induk (opsional).</li>
                            <li class="mb-2">Klik <strong>Register</strong> lalu segera salin Secret URL yang muncul untuk dikirimkan secara aman ke klien.</li>
                        </ol>
                    </div>
                </div>
            </div>

            
            <!-- Step 5 (Edit Merchant) -->
            <div class="row hc-step-row align-items-center mb-5">
                <div class="col-lg-12">
                    <div class="hc-step-number">5</div>
                    <h3 class="hc-step-title">Mengedit Profil Merchant</h3>
                    <p class="text-muted mb-4">Menjaga agar informasi kontak, status, dan kredensial merchant tetap mutakhir.</p>
                    <div class="pl-4 border-left border-success ml-2 mt-3">
                        <ol class="hc-step-desc mb-0">
                            <li class="mb-3">Buka <strong>Menu Aksi (⋮)</strong> pada merchant tertentu dan klik <strong><i class="fas fa-edit text-info"></i> Edit Merchant</strong>.</li>
                            <li class="mb-3">
                                Halaman konfigurasi khusus akan dimuat. Anda dapat memperbarui field-field berikut:
                                <div class="mt-2 text-muted pl-3 border-left mb-2"><strong>Account Information</strong></div>
                                <ul class="text-muted mb-3" style="list-style-type: disc;">
                                    <li class="mb-1"><strong>Merchant Name & Phone & GVConnect ID:</strong> Perbarui detail umum bisnis.</li>
                                    <li class="mb-1"><strong>Merchant Email:</strong> Harus tetap unik. <em>Catatan: Mengubah nilai ini akan secara otomatis memutuskan (logout) sesi login aktif merchant.</em></li>
                                    <li class="mb-1"><strong>Password:</strong> Kosongkan jika tidak ingin diubah, atau isi untuk mereset password merchant secara paksa.</li>
                                </ul>
                                <div class="text-muted pl-3 border-left mb-2"><strong>OpenAPI Configuration & Permissions</strong></div>
                                <ul class="text-muted mb-3" style="list-style-type: disc;">
                                    <li class="mb-1"><strong>Whitelist IP & Callbacks:</strong> Sesuaikan keamanan jaringan dan endpoint webhook.</li>
                                    <li class="mb-1"><strong>Service Permissions Checkboxes:</strong> Berikan atau cabut izin akses untuk saluran pembayaran tertentu secara real-time.</li>
                                </ul>
                                <div class="text-muted pl-3 border-left mb-2"><strong>System Status</strong></div>
                                <ul class="text-muted mb-2" style="list-style-type: disc;">
                                    <li class="mb-1"><strong>Merchant Account Status:</strong> Mengontrol login dasbor. <code>Active</code> mengizinkan login, <code>Blocked/Freeze</code> menolak login.</li>
                                    <li class="mb-1"><strong>OpenAPI Access Status:</strong> Mengontrol API. <code>Active</code> mengizinkan transaksi, <code>Blocked</code> menghentikan semua permintaan API dan webhook yang masuk.</li>
                                </ul>
                            </li>
                            <li class="mb-3">Jika merchant kehilangan akses login, Anda dapat memasukkan Password baru secara manual di sini.</li>
                            <li class="mb-2">Klik <strong>Save Changes</strong> untuk langsung menerapkan konfigurasi baru. Catatan: Mengubah email akan langsung memutuskan sesi login mereka saat ini.</li>
                        </ol>
                    </div>
                </div>
            </div>




            <!-- Parameter Reference & Validations -->
            <h4 class="font-weight-bold mt-5 mb-4 border-bottom pb-2">Referensi Parameter</h4>
            <div class="mb-5">
                <p class="text-muted mb-4">Saat mendaftarkan atau mengedit merchant, parameter inti berikut menentukan identitas dan tingkat akses mereka di dalam gateway:</p>
                <div class="table-responsive m-0">
                    <table class="table table-bordered hc-ref-table bg-white">
                        <thead>
                            <tr>
                                <th class="p-3 border-0" style="width: 25%;">Parameter</th>
                                <th class="p-3 border-0">Deskripsi & Validasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="p-3 border-0"><strong>Merchant Name</strong></td>
                                <td class="p-3 border-0">Nama entitas bisnis yang sah atau operasional. Nama ini akan ditampilkan di invoice dan dasbor.</td>
                            </tr>
                            <tr>
                                <td class="p-3 border-0"><strong>Merchant Email</strong></td>
                                <td class="p-3 border-0">Email kontak utama. Berfungsi sebagai pengidentifikasi global unik dan username login. <em>Validasi: Harus unik di seluruh sistem.</em></td>
                            </tr>
                            <tr>
                                <td class="p-3 border-0"><strong>Password</strong></td>
                                <td class="p-3 border-0">Kredensial login untuk dasbor merchant. Wajib diisi saat proses pendaftaran awal.</td>
                            </tr>
                            <tr>
                                <td class="p-3 border-0"><strong>Account Status</strong></td>
                                <td class="p-3 border-0">Mengatur akses dasbor. <code>Active</code> mengizinkan login, sedangkan <code>Blocked</code> langsung mencabut seluruh akses dasbor.</td>
                            </tr>
                            <tr>
                                <td class="p-3 border-0"><strong>OpenAPI Status</strong></td>
                                <td class="p-3 border-0">Mengontrol akses terprogram (API). <code>Active</code> mengizinkan pemrosesan transaksi API, sedangkan <code>Blocked</code> menolak semua permintaan API masuk dan menghentikan webhook.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h6 class="font-weight-bold mb-3 mt-4 text-dark"><i class="fas fa-bell text-info mr-2"></i> Notifikasi Sistem</h6>
                <div class="d-flex flex-column mb-4">
                    <div class="mb-3">
                        <div class="p-3 rounded border" style="background-color: rgba(22, 163, 74, 0.05); border-color: rgba(22, 163, 74, 0.2) !important;">
                            <strong class="text-success d-block mb-3"><i class="fas fa-check-circle mr-1"></i> Notifikasi Sukses</strong>
                            <div class="d-flex align-items-center mb-0 small text-muted">
                                <i class="fas fa-info-circle text-success mr-2"></i>
                                <div><strong>Merchant Dibuat:</strong> <code class="ml-1">Merchant successfully registered.</code></div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-1">
                        <div class="p-3 rounded border" style="background-color: rgba(220, 38, 38, 0.05); border-color: rgba(220, 38, 38, 0.2) !important;">
                            <strong class="text-danger d-block mb-3"><i class="fas fa-exclamation-circle mr-1"></i> Notifikasi Error & Solusinya</strong>
                            
                            <div class="mb-3 small">
                                <div class="text-muted mb-2"><i class="fas fa-times-circle text-danger mr-2"></i><strong>Duplikat Email (1062):</strong> <code class="ml-1">Email address already in use.</code></div>
                                <div class="p-2 rounded text-dark" style="background-color: #fff3cd; border-left: 3px solid #ffc107; margin-left: 20px;">
                                    <i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solusi:</strong> Email sudah dipakai akun lain. Silakan pakai alamat email yang lain atau gunakan fitur pencarian untuk mencari akun tersebut.
                                </div>
                            </div>
                            
                            <div class="mb-0 small">
                                <div class="text-muted mb-2"><i class="fas fa-times-circle text-danger mr-2"></i><strong>Access Denied (1142):</strong> <code class="ml-1">Access Denied. You do not have sufficient privileges.</code></div>
                                <div class="p-2 rounded text-dark" style="background-color: #fff3cd; border-left: 3px solid #ffc107; margin-left: 20px;">
                                    <i class="fas fa-lightbulb text-warning mr-1"></i> <strong>Solusi:</strong> User MySQL tidak memiliki hak akses INSERT. Silakan kontak Database Administrator Anda.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Troubleshooting -->
            <h4 class="font-weight-bold mb-4 border-bottom pb-2">Panduan Pemecahan Masalah (FAQ)</h4>
            <div class="hc-faq-item">
                <div class="hc-faq-q">
                    <i class="fas fa-exclamation-circle text-danger"></i> 
                    <span>Mengapa muncul error "Email already registered" saat pendaftaran?
                    </span>
                </div>
                <p class="hc-faq-a">Alamat email sudah digunakan oleh entitas merchant lain. Email bersifat sebagai identitas tunggal di gateway. Gunakan pencarian untuk melacak akun yang lama, atau mintalah email yang berbeda kepada klien.</p>
            </div>
            <div class="hc-faq-item">
                <div class="hc-faq-q">
                    <i class="fas fa-question-circle text-warning"></i> 
                    <span>Mengapa merchant bisa login dasbor tetapi transaksi API mereka ditolak?
                    </span>
                </div>
                <p class="hc-faq-a">Terdapat perbedaan status. Buka formulir Edit Merchant dan pastikan <strong>OpenAPI Status</strong> tersetel ke <code>Active</code>. Jika sudah aktif, masalah mungkin terletak pada API Secret Key mereka atau IP Whitelist yang salah.</p>
            </div>
            <div class="hc-faq-item">
                <div class="hc-faq-q">
                    <i class="fas fa-info-circle text-primary"></i> 
                    <span>Mengapa saya tidak dapat menemukan merchant tertentu di tabel?
                    </span>
                </div>
                <p class="hc-faq-a">Pencarian (Search) atau filter status dari sesi Anda sebelumnya kemungkinan masih aktif. Klik tombol <strong>Clear All</strong> pada panel Advanced Filters untuk membersihkan sesi dan memuat ulang seluruh direktori.</p>
            </div>
            <div class="hc-faq-item">
                <div class="hc-faq-q">
                    <i class="fas fa-lock text-secondary"></i> 
                    <span>Apa yang harus dilakukan jika merchant kehilangan Secret URL atau tautan sudah kedaluwarsa?
                    </span>
                </div>
                <p class="hc-faq-a">Secret URL yang dihasilkan otomatis akan hancur dalam 24 jam. Untuk memberikan akses, Admin harus membuka formulir <strong>Edit Merchant</strong>, mereset kata sandi secara manual, dan membagikan kredensial baru dengan aman.</p>
            </div>
            <div class="hc-faq-item">
                <div class="hc-faq-q">
                    <i class="fas fa-network-wired text-info"></i> 
                    <span>Mengapa callback webhook gagal terkirim ke server Merchant?
                    </span>
                </div>
                <p class="hc-faq-a">Pastikan <strong>OpenAPI Status</strong> milik Merchant dalam keadaan Active. Jika sudah, buka modul <strong>Secret Keys</strong> untuk memastikan URL webhook mereka valid, dapat diakses publik, dan merespons dalam batas waktu timeout 5 detik.</p>
            </div>
            <div class="hc-faq-item">
                <div class="hc-faq-q">
                    <i class="fas fa-hand-holding-usd text-danger"></i> 
                    <span>Mengapa pemotongan saldo manual (Deduct Debit) saya ditolak?
                    </span>
                </div>
                <p class="hc-faq-a">Sistem menerapkan kebijakan ketat agar saldo tidak boleh minus (non-negative balance). Anda tidak dapat memotong saldo dengan jumlah yang melebihi Master Balance merchant saat ini.</p>
            </div>

            
<!-- What's Next -->
            
            <div class="hc-faq-item">
                <div class="hc-faq-q">
                    <i class="fas fa-ban text-danger"></i> 
                    <span>Mengapa saya tidak bisa mengubah Master Balance langsung dari formulir Edit Merchant?</span>
                </div>
                <p class="hc-faq-a">Untuk menjaga jejak audit yang ketat dan riwayat finansial yang tidak dapat diubah secara sewenang-wenang, Master Balance tidak bisa diedit langsung. Anda harus menggunakan menu <strong>Add Credit Balance</strong> atau <strong>Deduct Debit Balance</strong>, yang mewajibkan penulisan catatan alasan dan akan merekam ID Admin yang bertanggung jawab.</p>
            </div>
            <div class="hc-faq-item">
                <div class="hc-faq-q">
                    <i class="fas fa-trash-slash text-secondary"></i> 
                    <span>Bagaimana cara menghapus akun Merchant secara permanen (Delete)?</span>
                </div>
                <p class="hc-faq-a">Demi integritas relasi data historis dan finansial di seluruh gateway, akun Merchant tidak dapat dihapus secara permanen (hard-delete). Jika kontrak kerja sama berakhir, Anda cukup mengubah <strong>Account Status</strong> dan <strong>OpenAPI Status</strong> menjadi <code>Blocked</code> untuk membekukan akun tersebut selamanya.</p>
            </div>
            <div class="hc-faq-item">
                <div class="hc-faq-q">
                    <i class="fas fa-users text-primary"></i> 
                    <span>Apakah saya bisa memindahkan Merchant ke Grup Supervisor lain di kemudian hari?</span>
                </div>
                <p class="hc-faq-a">Bisa. Anda dapat memindahkan mereka ke Grup Supervisor yang berbeda kapan saja melalui menu <strong>Edit Merchant</strong>. Perubahan ini berlaku instan, dan supervisor baru akan langsung mendapatkan akses pelaporan terhadap metrik merchant tersebut.</p>
            </div>
            <div class="hc-faq-item">
                <div class="hc-faq-q">
                    <i class="fas fa-envelope text-warning"></i> 
                    <span>Apa yang terjadi jika Merchant meminta perubahan alamat email utama mereka?</span>
                </div>
                <p class="hc-faq-a">Email adalah identitas unik global mereka di sistem. Walaupun dapat diubah melalui <strong>Edit Merchant</strong>, hal ini akan langsung memutus sesi login mereka saat ini. Pastikan Anda berkoordinasi terlebih dahulu dengan merchant agar mereka tidak kebingungan saat mendadak ter-logout.</p>
            </div>

            <div class="mt-5 pt-4 border-top" style="border-color: var(--hc-border) !important;">
                <h6 class="font-weight-bold mb-3 text-muted">Selanjutnya</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded" onclick="window.activateHelpModule('module-ug-merchant-subaccount'); window.scrollTo(0,0);" style="cursor: pointer; background: var(--hc-bg); border-color: var(--hc-border); transition: all 0.2s;" onmouseover="this.style.borderColor='var(--hc-active-text)'" onmouseout="this.style.borderColor='var(--hc-border)'">
                            <div class="font-weight-bold text-primary mb-1">Langkah 2: Sub-Accounts <i class="fas fa-arrow-right float-right mt-1"></i></div>
                            <div class="small text-muted">Pelajari cara membuat akun cabang dan menetapkan supervisor.</div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="p-3 border rounded" onclick="window.activateHelpModule('module-secret'); window.scrollTo(0,0);" style="cursor: pointer; background: var(--hc-bg); border-color: var(--hc-border); transition: all 0.2s;" onmouseover="this.style.borderColor='var(--hc-active-text)'" onmouseout="this.style.borderColor='var(--hc-border)'">
                            <div class="font-weight-bold text-primary mb-1">Langkah 3: Secret Keys <i class="fas fa-arrow-right float-right mt-1"></i></div>
                            <div class="small text-muted">Konfigurasikan API key, webhook, dan whitelist IP untuk merchant ini.</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>