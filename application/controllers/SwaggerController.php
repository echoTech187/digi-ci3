<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @OA\Info(
 *     title="GIDI Payment Gateway - Admin API Documentation",
 *     version="1.0.0",
 *     description="Dokumentasi Resmi OpenAPI 3.0 Presisi 100% Sesuai routes.php di C:\xampp74\htdocs\gidi2\admin\public_html\html\application\config\routes.php.",
 *     @OA\Contact(
 *         name="GIDI Technical Team",
 *         email="admin@gidi.co.id",
 *         url="https://admin.gidi.co.id"
 *     )
 * )
 * @OA\Server(
 *     url="https://admin.gidi.co.id",
 *     description="Production Admin Server"
 * )
 * @OA\Server(
 *     url="https://staggingadmin.gidi.co.id",
 *     description="Local Development Server"
 * )
 */
class SwaggerController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Render Halaman Interactive Swagger UI Portal
     */
    public function index()
    {
        $csrf_name = $this->security->get_csrf_token_name();
        $csrf_hash = $this->security->get_csrf_hash();

        $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GIDI Admin API Documentation - Swagger UI</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/swagger-ui-dist@4.15.5/swagger-ui.css" />
    <style>
        html { box-sizing: border-box; overflow-y: scroll; }
        *, *:before, *:after { box-sizing: inherit; }
        body { margin:0; background: #0f172a; color: #f8fafc; font-family: sans-serif; }
        .swagger-ui .topbar { display: none; }
        .swagger-ui { max-width: 1300px; margin: 0 auto; padding: 20px; background: #ffffff; border-radius: 12px; margin-top: 20px; margin-bottom: 30px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
        .gidi-header { background: linear-gradient(135deg, #1e293b, #0f172a); color: #ffffff; padding: 25px 40px; text-align: center; border-bottom: 3px solid #3b82f6; }
        .gidi-header h1 { margin: 0; font-size: 26px; font-weight: 700; letter-spacing: 0.5px; }
        .gidi-header p { margin: 8px 0 0; color: #94a3b8; font-size: 14px; }
        .gidi-actions { margin-top: 15px; display: flex; justify-content: center; gap: 12px; }
        .btn-download { display: inline-flex; align-items: center; gap: 6px; background: #2563eb; color: #ffffff; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; font-size: 14px; transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(37,99,235,0.3); }
        .btn-download:hover { background: #1d4ed8; transform: translateY(-1px); box-shadow: 0 6px 16px rgba(37,99,235,0.4); }
        .btn-secondary { background: #334155; }
        .btn-secondary:hover { background: #475569; }
    </style>
</head>
<body>
    <div class="gidi-header">
        <h1>🚀 GIDI Payment Gateway — Admin API Master Portal</h1>
        <p>Interactive OpenAPI 3.0 Specification (Sesuai 100% dengan routes.php gidi2/admin)</p>
        <div class="gidi-actions">
            <a href="' . base_url('docs/openapi.json?download=1') . '" class="btn-download">
                📥 Download OpenAPI JSON (For Postman Import)
            </a>
            <a href="' . base_url('docs/openapi.json') . '" target="_blank" class="btn-download btn-secondary">
                🔗 View Raw JSON API
            </a>
        </div>
    </div>
    <div id="swagger-ui"></div>
    <script src="https://cdn.jsdelivr.net/npm/swagger-ui-dist@4.15.5/swagger-ui-bundle.js" charset="UTF-8"></script>
    <script src="https://cdn.jsdelivr.net/npm/swagger-ui-dist@4.15.5/swagger-ui-standalone-preset.js" charset="UTF-8"></script>
    <script>
        window.onload = function() {
            window.ui = SwaggerUIBundle({
                url: "' . base_url('docs/openapi.json') . '",
                dom_id: "#swagger-ui",
                deepLinking: true,
                docExpansion: "none",
                defaultModelsExpandDepth: -1,
                syntaxHighlight: false,
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                plugins: [
                    SwaggerUIBundle.plugins.DownloadUrl
                ],
                layout: "StandaloneLayout",
                requestInterceptor: function(req) {
                    req.headers["X-Requested-With"] = "XMLHttpRequest";
                    req.headers["X-CSRF-TOKEN"] = "' . $csrf_hash . '";
                    req.headers["' . $csrf_name . '"] = "' . $csrf_hash . '";
                    if (req.method === "POST" || req.method === "PUT" || req.method === "DELETE") {
                        if (req.headers["Content-Type"] && req.headers["Content-Type"].indexOf("application/json") !== -1) {
                            try {
                                var bodyObj = req.body ? JSON.parse(req.body) : {};
                                bodyObj["' . $csrf_name . '"] = "' . $csrf_hash . '";
                                req.body = JSON.stringify(bodyObj);
                            } catch(e) {
                                console.warn("Failed to parse JSON body for CSRF injection", e);
                            }
                        } else if (typeof req.body === "string" && req.body.length > 0) {
                            if (req.body.indexOf("' . $csrf_name . '=") === -1) {
                                req.body += "&' . $csrf_name . '=' . $csrf_hash . '";
                            }
                        }
                    }
                    return req;
                }
            });
        };
    </script>
</body>
</html>';

        echo $html;
    }

    /**
     * Helper membuat objek Request Body OpenAPI 3.0 (JSON & Form)
     */
    private function _json_body($example)
    {
        $properties = [];
        if (is_array($example)) {
            foreach ($example as $k => $v) {
                $type = is_int($v) ? "integer" : (is_bool($v) ? "boolean" : (is_array($v) ? "array" : "string"));
                $properties[$k] = ["type" => $type, "example" => $v];
            }
        }

        $schema = ["type" => "object"];
        if (!empty($properties)) {
            $schema["properties"] = (object)$properties;
        }

        return [
            "required" => true,
            "content" => [
                "application/json" => [
                    "schema" => $schema
                ]
            ]
        ];
    }

    /**
     * Helper membuat objek Form Request Body OpenAPI 3.0
     */
    private function _form_body($properties)
    {
        return [
            "required" => true,
            "content" => [
                "application/x-www-form-urlencoded" => [
                    "schema" => [
                        "type" => "object",
                        "properties" => $properties
                    ]
                ],
                "application/json" => [
                    "schema" => [
                        "type" => "object",
                        "properties" => $properties
                    ]
                ]
            ]
        ];
    }

    /**
     * Helper membuat objek Response 200 OpenAPI 3.0
     */
    private function _resp200()
    {
        return [
            "200" => [
                "description" => "Success",
                "content" => [
                    "application/json" => [
                        "schema" => [
                            "type" => "object",
                            "properties" => new stdClass()
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Menyajikan Spesifikasi JSON OpenAPI 3.0 Presisi 1-ke-1 dengan routes.php gidi2/admin
     */
    public function openapi_json()
    {
        $csrf_name = $this->security->get_csrf_token_name();
        $csrf_hash = $this->security->get_csrf_hash();

        if ($this->input->get('download') == '1') {
            header('Content-Description: File Transfer');
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="gidi_admin_openapi_v1.json"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
        } else {
            $this->output->set_content_type('application/json');
        }

        $spec = [
            "openapi" => "3.0.0",
            "info" => [
                "title" => "GIDI Payment Gateway - Complete Admin API Specification",
                "description" => "Spesifikasi Resmi OpenAPI 3.0 Sinkron 100% Sesuai Rute di C:\xampp74\htdocs\gidi2\admin\public_html\html\application\config\routes.php.",
                "version" => "1.0.0",
                "contact" => [
                    "name" => "GIDI Technical Team",
                    "email" => "admin@gidi.co.id"
                ]
            ],
            "servers" => [
                ["url" => base_url(), "description" => "Current Application Server"],
                ["url" => "https://admin.gidi.co.id", "description" => "Production Admin Server"]
            ],
            "tags" => [
                ["name" => "Authentication & Account", "description" => "Data APIs -> Admin Login, Password, Register & Token Verify"],
                ["name" => "Dashboard & Analytics Data", "description" => "Data APIs -> OpenAPI Toggle, Balance Sync, Today/Monthly/Analytics Stats JSON"],
                ["name" => "Global Search Data", "description" => "Data APIs -> Global Transaction Search & Recent Queries"],
                ["name" => "BI-FAST Transactions Data", "description" => "Data APIs -> DataTable JSON, Downloads, Channel External & Resend Notif"],
                ["name" => "Virtual Account Data", "description" => "Data APIs -> DataTable JSON, Details, Dynamic/Recurring & Resend Notif"],
                ["name" => "QRIS Transactions Data", "description" => "Data APIs -> DataTable JSON, Details, Dynamic/Recurring & Resend Notif"],
                ["name" => "E-Wallet Transactions Data", "description" => "Data APIs -> DataTable JSON, Details, Dynamic & Resend Notif"],
                ["name" => "Finance History & Mutation Data", "description" => "Data APIs -> History & Mutation DataTable JSON, Downloads & Channel Filters"],
                ["name" => "Merchant Management Data", "description" => "Data APIs -> Merchant/Supervisor Registration, Search, Update, Details & Overview Ajax"],
                ["name" => "Merchant Fee & Balance Data", "description" => "Data APIs -> Cashin/Cashout Fee Settings, Group Fees, Manual Credit/Debit Balance & Permissions"],
                ["name" => "Merchant Sub-Account Data", "description" => "Data APIs -> Submerchant Registration, Edit & Details"],
                ["name" => "Report Download Data", "description" => "Data APIs -> Export Statements & Reset Filters"],
                ["name" => "User Access & Menu Data", "description" => "Data APIs -> Admin Management, Holiday Control, Role Access & Menu Config Ajax"],
                ["name" => "Cashin External Channels Data", "description" => "Data APIs -> Cashin Channels List, Add/Update/Delete & Bulk Mappings"],
                ["name" => "Cashout External Channels Data", "description" => "Data APIs -> Cashout Channels List, Add/Update/Delete & Bulk Mappings"],
                ["name" => "Channel Management Data", "description" => "Data APIs -> Master Cashin/Cashout Channels Create/Update/Delete & Filter Options"],
                ["name" => "Service & Products Data (PPOB)", "description" => "Data APIs -> PPOB Products Create/Update/Delete"],
                ["name" => "Health & Merchant RBAC Data", "description" => "Data APIs -> System Health DB Check, Merchant Roles & Menu Save"]
            ],
            "paths" => [
                // ── Dashboard & Analytics Data ──
                "/dashboard/toggle-openapi" => ["post" => ["tags" => ["Dashboard & Analytics Data"], "summary" => "POST /dashboard/toggle-openapi — Toggle Merchant OpenAPI Status", "requestBody" => $this->_json_body(["status" => 1]), "responses" => $this->_resp200()]],
                "/dashboard/maintenance-status" => ["get" => ["tags" => ["Dashboard & Analytics Data"], "summary" => "GET /dashboard/maintenance-status — Fetch Maintenance Status JSON", "responses" => $this->_resp200()]],
                "/dashboard/sync-balance" => ["get" => ["tags" => ["Dashboard & Analytics Data"], "summary" => "GET /dashboard/sync-balance — Sync Merchant Available Balance", "responses" => $this->_resp200()]],
                "/dashboard/recent-mutations/json" => ["post" => ["tags" => ["Dashboard & Analytics Data"], "summary" => "POST /dashboard/recent-mutations/json — Fetch Recent Mutations JSON", "requestBody" => $this->_json_body(["merchant_id" => 10023, "limit" => 10]), "responses" => $this->_resp200()]],
                "/dashboard/today-stats/json" => ["post" => ["tags" => ["Dashboard & Analytics Data"], "summary" => "POST /dashboard/today-stats/json — Fetch Today Stats JSON", "requestBody" => $this->_json_body(["merchant_id" => 10023]), "responses" => $this->_resp200()]],
                "/dashboard/monthly-stats/json" => ["post" => ["tags" => ["Dashboard & Analytics Data"], "summary" => "POST /dashboard/monthly-stats/json — Fetch Monthly Stats JSON", "requestBody" => $this->_json_body(["merchant_id" => 10023, "year" => 2026, "month" => 8]), "responses" => $this->_resp200()]],
                "/dashboard/metadata/json" => ["post" => ["tags" => ["Dashboard & Analytics Data"], "summary" => "POST /dashboard/metadata/json — Fetch Dashboard Metadata JSON", "requestBody" => $this->_json_body(["merchant_id" => 10023]), "responses" => $this->_resp200()]],
                "/dashboard/analytics-data/json" => ["post" => ["tags" => ["Dashboard & Analytics Data"], "summary" => "POST /dashboard/analytics-data/json — Fetch Analytics Data JSON", "requestBody" => $this->_json_body(["merchant_id" => 10023, "period" => "daily"]), "responses" => $this->_resp200()]],

                // ── Global Search Data ──
                "/dashboard/global-search" => ["post" => ["tags" => ["Global Search Data"], "summary" => "POST /dashboard/global-search — Search Transactions JSON", "requestBody" => $this->_json_body(["keyword" => "QRIS_MPM_240220_00000032"]), "responses" => $this->_resp200()]],
                "/dashboard/recent-search" => ["post" => ["tags" => ["Global Search Data"], "summary" => "POST /dashboard/recent-search — Fetch Recent Search Queries JSON", "requestBody" => $this->_json_body(["merchant_id" => 10023]), "responses" => $this->_resp200()]],

                // ── Authentication & Account ──
                "/auth/login" => ["post" => ["tags" => ["Authentication & Account"], "summary" => "POST /auth/login — Admin Login (JSON)", "requestBody" => $this->_form_body(["email" => ["type" => "string", "example" => "support1@gidi.co.id"], "password" => ["type" => "string", "example" => "AdminSecret123!"], $csrf_name => ["type" => "string", "example" => $csrf_hash]]), "responses" => $this->_resp200()]],
                "/auth/logout" => ["post" => ["tags" => ["Authentication & Account"], "summary" => "POST /auth/logout — Admin Logout (JSON)", "responses" => $this->_resp200()]],
                "/auth/change-password" => ["post" => ["tags" => ["Authentication & Account"], "summary" => "POST /auth/change-password — Change Password", "requestBody" => $this->_form_body(["current_password" => ["type" => "string", "example" => "OldPass123!"], "new_password" => ["type" => "string", "example" => "NewPass123!"], "confirm_password" => ["type" => "string", "example" => "NewPass123!"], $csrf_name => ["type" => "string", "example" => $csrf_hash]]), "responses" => $this->_resp200()]],
                "/auth/forgot-password" => ["post" => ["tags" => ["Authentication & Account"], "summary" => "POST /auth/forgot-password — Request Password Reset", "requestBody" => $this->_form_body(["email" => ["type" => "string", "example" => "support1@gidi.co.id"], $csrf_name => ["type" => "string", "example" => $csrf_hash]]), "responses" => $this->_resp200()]],
                "/auth/register" => ["post" => ["tags" => ["Authentication & Account"], "summary" => "POST /auth/register — Register Admin User", "requestBody" => $this->_form_body(["c_name" => ["type" => "string", "example" => "Admin Baru"], "c_email" => ["type" => "string", "example" => "newadmin@gidi.co.id"], "role_id" => ["type" => "integer", "example" => 2], $csrf_name => ["type" => "string", "example" => $csrf_hash]]), "responses" => $this->_resp200()]],
                "/auth/reset-password" => ["post" => ["tags" => ["Authentication & Account"], "summary" => "POST /auth/reset-password — Reset Password with Token", "requestBody" => $this->_form_body(["token" => ["type" => "string", "example" => "hash123token"], "new_password" => ["type" => "string", "example" => "NewPass123!"], $csrf_name => ["type" => "string", "example" => $csrf_hash]]), "responses" => $this->_resp200()]],
                "/auth/verify" => ["post" => ["tags" => ["Authentication & Account"], "summary" => "POST /auth/verify — Verify Token/Email", "requestBody" => $this->_form_body(["token" => ["type" => "string", "example" => "hash123token"], "email" => ["type" => "string", "example" => "support1@gidi.co.id"], $csrf_name => ["type" => "string", "example" => $csrf_hash]]), "responses" => $this->_resp200()]],

                // ── BI-FAST Transactions Data ──
                "/finance/bi-fast" => ["post" => ["tags" => ["BI-FAST Transactions Data"], "summary" => "POST /finance/bi-fast — BI-FAST DataTable JSON", "requestBody" => $this->_json_body(["draw" => 1, "start" => 0, "length" => 10, "merchant_id" => 10023, "date_from" => "2026-08-01", "date_to" => "2026-08-27"]), "responses" => $this->_resp200()]],
                "/finance/bi-fast/reset" => ["get" => ["tags" => ["BI-FAST Transactions Data"], "summary" => "GET /finance/bi-fast/reset — Reset BI-FAST Filters", "responses" => $this->_resp200()]],
                "/finance/bi-fast/detail/{id}" => ["get" => ["tags" => ["BI-FAST Transactions Data"], "summary" => "GET /finance/bi-fast/detail/{id} — BI-FAST Detail JSON", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 1052]], "responses" => $this->_resp200()]],
                "/finance/bi-fast/download" => ["post" => ["tags" => ["BI-FAST Transactions Data"], "summary" => "POST /finance/bi-fast/download — Export BI-FAST Excel Statement", "requestBody" => $this->_json_body(["merchant_id" => 10023, "date_from" => "2026-08-01", "date_to" => "2026-08-27", "format" => "xlsx"]), "responses" => $this->_resp200()]],
                "/finance/bi-fast/channel/external" => ["post" => ["tags" => ["BI-FAST Transactions Data"], "summary" => "POST /finance/bi-fast/channel/external — BI-FAST Channel External Log", "requestBody" => $this->_json_body(["ref_cashoutExternalId" => "PAYLABS", "ref_cashoutExternalLogBifastId" => 1]), "responses" => $this->_resp200()]],
                "/finance/bi-fast/notification/resend/{id}/{merchantId}" => ["post" => ["tags" => ["BI-FAST Transactions Data"], "summary" => "POST /finance/bi-fast/notification/resend — Resend BI-FAST Webhook", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 1052], ["name" => "merchantId", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 10023]], "requestBody" => $this->_json_body(["resend" => true]), "responses" => $this->_resp200()]],

                // ── Virtual Account Data ──
                "/finance/virtual-account" => ["post" => ["tags" => ["Virtual Account Data"], "summary" => "POST /finance/virtual-account — VA DataTable JSON", "requestBody" => $this->_json_body(["draw" => 1, "start" => 0, "length" => 10, "merchant_id" => 10023, "date_from" => "2026-08-01", "date_to" => "2026-08-27"]), "responses" => $this->_resp200()]],
                "/finance/virtual-account/reset" => ["get" => ["tags" => ["Virtual Account Data"], "summary" => "GET /finance/virtual-account/reset — Reset VA Filters", "responses" => $this->_resp200()]],
                "/finance/virtual-account/detail/{id}" => ["get" => ["tags" => ["Virtual Account Data"], "summary" => "GET /finance/virtual-account/detail/{id} — VA Detail JSON", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 8812]], "responses" => $this->_resp200()]],
                "/finance/virtual-account/download" => ["post" => ["tags" => ["Virtual Account Data"], "summary" => "POST /finance/virtual-account/download — Export VA Statement", "requestBody" => $this->_json_body(["merchant_id" => 10023, "date_from" => "2026-08-01", "date_to" => "2026-08-27"]), "responses" => $this->_resp200()]],
                "/virtual-account/dynamic" => ["post" => ["tags" => ["Virtual Account Data"], "summary" => "POST /virtual-account/dynamic — Dynamic VA DataTable JSON", "requestBody" => $this->_json_body(["draw" => 1, "start" => 0, "length" => 10, "merchant_id" => 10023]), "responses" => $this->_resp200()]],
                "/virtual-account/dynamic/reset" => ["get" => ["tags" => ["Virtual Account Data"], "summary" => "GET /virtual-account/dynamic/reset — Reset Dynamic VA Filters", "responses" => $this->_resp200()]],
                "/virtual-account/recurring" => ["post" => ["tags" => ["Virtual Account Data"], "summary" => "POST /virtual-account/recurring — Recurring VA DataTable JSON", "requestBody" => $this->_json_body(["draw" => 1, "start" => 0, "length" => 10, "merchant_id" => 10023]), "responses" => $this->_resp200()]],
                "/virtual-account/recurring/reset" => ["get" => ["tags" => ["Virtual Account Data"], "summary" => "GET /virtual-account/recurring/reset — Reset Recurring VA Filters", "responses" => $this->_resp200()]],
                "/virtual-account/notification/resend/{id}/{merchantId}" => ["post" => ["tags" => ["Virtual Account Data"], "summary" => "POST /virtual-account/notification/resend — Resend VA Webhook", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 8812], ["name" => "merchantId", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 10023]], "requestBody" => $this->_json_body(["resend" => true]), "responses" => $this->_resp200()]],
                "/virtual-account/dynamic/channel/external" => ["post" => ["tags" => ["Virtual Account Data"], "summary" => "POST /virtual-account/dynamic/channel/external — Dynamic VA External Log", "requestBody" => $this->_json_body(["ref_cashinExternalId" => "ifp", "parentId" => 1, "ref_cashinExternalLogVaIdCreate" => 1]), "responses" => $this->_resp200()]],
                "/virtual-account/recurring/channel/external" => ["post" => ["tags" => ["Virtual Account Data"], "summary" => "POST /virtual-account/recurring/channel/external — Recurring VA External Log", "requestBody" => $this->_json_body(["ref_cashinExternalId" => "ifp", "parentId" => 1, "ref_cashinExternalLogVaIdCreate" => 1]), "responses" => $this->_resp200()]],

                // ── QRIS Transactions Data ──
                "/finance/qris" => ["post" => ["tags" => ["QRIS Transactions Data"], "summary" => "POST /finance/qris — QRIS DataTable JSON", "requestBody" => $this->_json_body(["draw" => 1, "start" => 0, "length" => 10, "merchant_id" => 10023, "date_from" => "2026-08-01", "date_to" => "2026-08-27"]), "responses" => $this->_resp200()]],
                "/finance/qris/reset" => ["get" => ["tags" => ["QRIS Transactions Data"], "summary" => "GET /finance/qris/reset — Reset QRIS Filters", "responses" => $this->_resp200()]],
                "/finance/qris/detail/{id}" => ["get" => ["tags" => ["QRIS Transactions Data"], "summary" => "GET /finance/qris/detail/{id} — QRIS Detail JSON", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 9912]], "responses" => $this->_resp200()]],
                "/finance/qris/download" => ["post" => ["tags" => ["QRIS Transactions Data"], "summary" => "POST /finance/qris/download — Export QRIS Statement", "requestBody" => $this->_json_body(["merchant_id" => 10023, "date_from" => "2026-08-01", "date_to" => "2026-08-27"]), "responses" => $this->_resp200()]],
                "/qris/dynamic" => ["post" => ["tags" => ["QRIS Transactions Data"], "summary" => "POST /qris/dynamic — Dynamic QRIS DataTable JSON", "requestBody" => $this->_json_body(["draw" => 1, "start" => 0, "length" => 10, "merchant_id" => 10023]), "responses" => $this->_resp200()]],
                "/qris/dynamic/reset" => ["get" => ["tags" => ["QRIS Transactions Data"], "summary" => "GET /qris/dynamic/reset — Reset Dynamic QRIS Filters", "responses" => $this->_resp200()]],
                "/qris/recurring" => ["post" => ["tags" => ["QRIS Transactions Data"], "summary" => "POST /qris/recurring — Recurring QRIS DataTable JSON", "requestBody" => $this->_json_body(["draw" => 1, "start" => 0, "length" => 10, "merchant_id" => 10023]), "responses" => $this->_resp200()]],
                "/qris/recurring/reset" => ["get" => ["tags" => ["QRIS Transactions Data"], "summary" => "GET /qris/recurring/reset — Reset Recurring QRIS Filters", "responses" => $this->_resp200()]],
                "/qris/notification/resend/{id}/{merchantId}" => ["post" => ["tags" => ["QRIS Transactions Data"], "summary" => "POST /qris/notification/resend — Resend QRIS Webhook", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 9912], ["name" => "merchantId", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 10023]], "requestBody" => $this->_json_body(["resend" => true]), "responses" => $this->_resp200()]],
                "/qris/dynamic/channel/external" => ["post" => ["tags" => ["QRIS Transactions Data"], "summary" => "POST /qris/dynamic/channel/external — Dynamic QRIS External Log", "requestBody" => $this->_json_body(["ref_cashinExternalId" => "PAYLABS", "parentId" => 1, "ref_cashinExternalLogQrisMpmIdCreate" => 1]), "responses" => $this->_resp200()]],
                "/qris/recurring/channel/external" => ["post" => ["tags" => ["QRIS Transactions Data"], "summary" => "POST /qris/recurring/channel/external — Recurring QRIS External Log", "requestBody" => $this->_json_body(["ref_cashinExternalId" => "PAYLABS", "parentId" => 1, "ref_cashinExternalLogQrisMpmIdCreate" => 1]), "responses" => $this->_resp200()]],
                "/qris/dynamic/list" => ["post" => ["tags" => ["QRIS Transactions Data"], "summary" => "POST /qris/dynamic/list — Dynamic QRIS List JSON", "requestBody" => $this->_json_body(["draw" => 1, "start" => 0, "length" => 10]), "responses" => $this->_resp200()]],

                // ── E-Wallet Transactions Data ──
                "/finance/e-wallet" => ["post" => ["tags" => ["E-Wallet Transactions Data"], "summary" => "POST /finance/e-wallet — E-Wallet DataTable JSON", "requestBody" => $this->_json_body(["draw" => 1, "start" => 0, "length" => 10, "merchant_id" => 10023, "date_from" => "2026-08-01", "date_to" => "2026-08-27"]), "responses" => $this->_resp200()]],
                "/finance/e-wallet/reset" => ["get" => ["tags" => ["E-Wallet Transactions Data"], "summary" => "GET /finance/e-wallet/reset — Reset E-Wallet Filters", "responses" => $this->_resp200()]],
                "/finance/e-wallet/detail/{id}" => ["get" => ["tags" => ["E-Wallet Transactions Data"], "summary" => "GET /finance/e-wallet/detail/{id} — E-Wallet Detail JSON", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 7712]], "responses" => $this->_resp200()]],
                "/finance/e-wallet/download" => ["post" => ["tags" => ["E-Wallet Transactions Data"], "summary" => "POST /finance/e-wallet/download — Export E-Wallet Statement", "requestBody" => $this->_json_body(["merchant_id" => 10023, "date_from" => "2026-08-01", "date_to" => "2026-08-27"]), "responses" => $this->_resp200()]],
                "/e-wallet/dynamic" => ["post" => ["tags" => ["E-Wallet Transactions Data"], "summary" => "POST /e-wallet/dynamic — Dynamic E-Wallet DataTable JSON", "requestBody" => $this->_json_body(["draw" => 1, "start" => 0, "length" => 10, "merchant_id" => 10023]), "responses" => $this->_resp200()]],
                "/e-wallet/dynamic/reset" => ["get" => ["tags" => ["E-Wallet Transactions Data"], "summary" => "GET /e-wallet/dynamic/reset — Reset E-Wallet Filters", "responses" => $this->_resp200()]],
                "/finance/e-wallet/notification/resend/{id}/{merchantId}" => ["post" => ["tags" => ["E-Wallet Transactions Data"], "summary" => "POST /finance/e-wallet/notification/resend — Resend Webhook", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 7712], ["name" => "merchantId", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 10023]], "requestBody" => $this->_json_body(["resend" => true]), "responses" => $this->_resp200()]],
                "/finance/e-wallet/channel/external" => ["post" => ["tags" => ["E-Wallet Transactions Data"], "summary" => "POST /finance/e-wallet/channel/external — E-Wallet External Log", "requestBody" => $this->_json_body(["ref_cashinExternalId" => "PAYLABS", "parentId" => 1, "ref_cashinExternalLogQrisMpmIdCreate" => 1]), "responses" => $this->_resp200()]],
                "/e-wallet/dynamic/channel/external" => ["post" => ["tags" => ["E-Wallet Transactions Data"], "summary" => "POST /e-wallet/dynamic/channel/external — Dynamic E-Wallet External Log", "requestBody" => $this->_json_body(["ref_cashinExternalId" => "PAYLABS", "ref_cashinExternalLogEwalletIdCreate" => 1]), "responses" => $this->_resp200()]],

                // ── History & Mutation Data ──
                "/finance/history" => ["post" => ["tags" => ["Finance History & Mutation Data"], "summary" => "POST /finance/history — History Transactions DataTable JSON", "requestBody" => $this->_json_body(["draw" => 1, "start" => 0, "length" => 10, "merchant_id" => 10023]), "responses" => $this->_resp200()]],
                "/finance/history/reset" => ["get" => ["tags" => ["Finance History & Mutation Data"], "summary" => "GET /finance/history/reset — Reset History Filters", "responses" => $this->_resp200()]],
                "/finance/history/download" => ["post" => ["tags" => ["Finance History & Mutation Data"], "summary" => "POST /finance/history/download — Export History Statement", "requestBody" => $this->_json_body(["merchant_id" => 10023, "date_from" => "2026-08-01", "date_to" => "2026-08-27"]), "responses" => $this->_resp200()]],
                "/finance/mutation" => ["post" => ["tags" => ["Finance History & Mutation Data"], "summary" => "POST /finance/mutation — Balance Mutations DataTable JSON", "requestBody" => $this->_json_body(["draw" => 1, "start" => 0, "length" => 10, "merchant_id" => 10023, "date_from" => "2026-08-27"]), "responses" => $this->_resp200()]],
                "/finance/mutation/reset/{id}" => ["get" => ["tags" => ["Finance History & Mutation Data"], "summary" => "GET /finance/mutation/reset/{id} — Reset Mutation Filters", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 10023]], "responses" => $this->_resp200()]],
                "/finance/mutation/download" => ["post" => ["tags" => ["Finance History & Mutation Data"], "summary" => "POST /finance/mutation/download — Export Mutation Statement", "requestBody" => $this->_json_body(["merchant_id" => 10023, "date_from" => "2026-08-01", "date_to" => "2026-08-27"]), "responses" => $this->_resp200()]],
                "/finance/mutation/channels" => ["post" => ["tags" => ["Finance History & Mutation Data"], "summary" => "POST /finance/mutation/channels — Get Channels by Position", "requestBody" => $this->_json_body(["position" => 1]), "responses" => $this->_resp200()]],
                "/finance/mutation/{id}" => ["get" => ["tags" => ["Finance History & Mutation Data"], "summary" => "GET /finance/mutation/{id} — Fetch Mutation Detail JSON", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 10023]], "responses" => $this->_resp200()]],

                // ── Merchant Management Data ──
                "/merchant/manage/reset" => ["get" => ["tags" => ["Merchant Management Data"], "summary" => "GET /merchant/manage/reset — Reset Merchant Filters", "responses" => $this->_resp200()]],
                "/merchant/supervisor/reset" => ["get" => ["tags" => ["Merchant Management Data"], "summary" => "GET /merchant/supervisor/reset — Reset Supervisor Filters", "responses" => $this->_resp200()]],
                "/merchant/supervisor/delete/{id}" => ["post" => ["tags" => ["Merchant Management Data"], "summary" => "POST /merchant/supervisor/delete/{id} — Delete Supervisor", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 5]], "responses" => $this->_resp200()]],
                "/merchant/manage/register" => ["post" => ["tags" => ["Merchant Management Data"], "summary" => "POST /merchant/manage/register — Register New Merchant", "requestBody" => $this->_json_body(["c_name" => "Toko Berkah Utama", "c_email" => "tokoberkah@gmail.com", "c_phoneNumber" => "081234567890", "c_password" => "AdminSecret123!", "c_confirmPassword" => "AdminSecret123!"]), "responses" => $this->_resp200()]],
                "/merchant/supervisor/register" => ["post" => ["tags" => ["Merchant Management Data"], "summary" => "POST /merchant/supervisor/register — Register Merchant Supervisor", "requestBody" => $this->_json_body(["c_name" => "Supervisor Alpha", "c_username" => "spv_alpha", "c_email" => "spvalpha@gmail.com", "c_status" => "Active", "c_password" => "AdminSecret123!", "c_confirmPassword" => "AdminSecret123!"]), "responses" => $this->_resp200()]],
                "/merchant/supervisor/get/{id}" => ["get" => ["tags" => ["Merchant Management Data"], "summary" => "GET /merchant/supervisor/get/{id} — Get Supervisor JSON", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 5]], "responses" => $this->_resp200()]],
                "/merchant/supervisor/update/{id}" => ["post" => ["tags" => ["Merchant Management Data"], "summary" => "POST /merchant/supervisor/update/{id} — Update Supervisor", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 5]], "requestBody" => $this->_json_body(["c_name" => "Supervisor Beta", "c_username" => "spv_beta", "c_email" => "spvbeta@gmail.com", "c_status" => "Active"]), "responses" => $this->_resp200()]],
                "/merchant/supervisor/search" => ["post" => ["tags" => ["Merchant Management Data"], "summary" => "POST /merchant/supervisor/search — Search Supervisors JSON", "requestBody" => $this->_json_body(["keyword" => "Berkah"]), "responses" => $this->_resp200()]],
                "/merchant/manage/list/reset/{id}" => ["get" => ["tags" => ["Merchant Management Data"], "summary" => "GET /merchant/manage/list/reset/{id} — Reset Merchant Sublist Filters", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 10023]], "responses" => $this->_resp200()]],
                "/merchant/manage/list/{id}" => ["get" => ["tags" => ["Merchant Management Data"], "summary" => "GET /merchant/manage/list/{id} — Fetch Sublist Merchants JSON", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 10023]], "responses" => $this->_resp200()]],
                "/merchant/manage/update/{id}" => ["post" => ["tags" => ["Merchant Management Data"], "summary" => "POST /merchant/manage/update/{id} — Update Merchant Data", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 10023]], "requestBody" => $this->_json_body(["c_name" => "Toko Berkah Updated", "c_email" => "tokoberkah@gmail.com", "c_phoneNumber" => "081299998888", "c_openapiStatus" => "Active", "c_status" => "Active"]), "responses" => $this->_resp200()]],
                "/merchant/manage/search" => ["post" => ["tags" => ["Merchant Management Data"], "summary" => "POST /merchant/manage/search — Search Merchants JSON", "requestBody" => $this->_json_body(["keyword" => "Toko"]), "responses" => $this->_resp200()]],
                "/merchant/manage/add" => ["post" => ["tags" => ["Merchant Management Data"], "summary" => "POST /merchant/manage/add — Add Merchant Account", "requestBody" => $this->_json_body(["c_name" => "Toko Baru", "c_email" => "tokobaru@gmail.com", "c_phoneNumber" => "081234567890", "c_password" => "Password123!", "c_confirmPassword" => "Password123!"]), "responses" => $this->_resp200()]],
                "/private/secret" => ["post" => ["tags" => ["Merchant Management Data"], "summary" => "POST /private/secret — Regenerate Merchant Secret", "requestBody" => $this->_json_body(["merchant_id" => 10023]), "responses" => $this->_resp200()]],
                "/merchant/manage/detail/{id}" => ["get" => ["tags" => ["Merchant Management Data"], "summary" => "GET /merchant/manage/detail/{id} — Merchant Detail JSON", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 10023]], "responses" => $this->_resp200()]],
                "/merchant/manage/history-ajax/{id}" => ["post" => ["tags" => ["Merchant Management Data"], "summary" => "POST /merchant/manage/history-ajax/{id} — History Ajax JSON", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 10023]], "requestBody" => $this->_json_body(["draw" => 1, "start" => 0, "length" => 10]), "responses" => $this->_resp200()]],
                "/merchant/manage/mutation-ajax/{id}" => ["post" => ["tags" => ["Merchant Management Data"], "summary" => "POST /merchant/manage/mutation-ajax/{id} — Mutation Ajax JSON", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 10023]], "requestBody" => $this->_json_body(["draw" => 1, "start" => 0, "length" => 10]), "responses" => $this->_resp200()]],
                "/merchant/manage/submerchant-ajax/{id}" => ["post" => ["tags" => ["Merchant Management Data"], "summary" => "POST /merchant/manage/submerchant-ajax/{id} — Submerchant Ajax JSON", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 10023]], "requestBody" => $this->_json_body(["draw" => 1, "start" => 0, "length" => 10]), "responses" => $this->_resp200()]],
                "/merchant/manage/overview-ajax/{id}" => ["post" => ["tags" => ["Merchant Management Data"], "summary" => "POST /merchant/manage/overview-ajax/{id} — Overview Ajax JSON", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 10023]], "requestBody" => $this->_json_body(["merchant_id" => 10023]), "responses" => $this->_resp200()]],

                // ── Fee Settings & Balance Credit/Debit ──
                "/merchant/setting-cashin-fee/{id}" => ["get" => ["tags" => ["Merchant Fee & Balance Data"], "summary" => "GET /merchant/setting-cashin-fee/{id} — Fetch Cashin Fee Settings JSON", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 10023]], "responses" => $this->_resp200()]],
                "/merchant/setting-cashin-fee/create" => ["post" => ["tags" => ["Merchant Fee & Balance Data"], "summary" => "POST /merchant/setting-cashin-fee/create — Create Cashin Fee", "requestBody" => $this->_json_body(["ref_merchantId" => 10023, "ref_cashinChannelId" => "va_bni", "c_cashinChannelGroup" => "VA", "c_externalIdDefault" => "1", "c_feeType" => "Fixed", "c_fee" => 2500, "c_feePercetange" => 0, "c_settlementInterval" => 0, "c_amountMin" => 0, "c_amountMax" => 100000000, "c_status" => "Active"]), "responses" => $this->_resp200()]],
                "/merchant/setting-cashin-fee/bulk-create/{id}" => ["post" => ["tags" => ["Merchant Fee & Balance Data"], "summary" => "POST /merchant/setting-cashin-fee/bulk-create/{id} — Bulk Cashin Fee", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 10023]], "requestBody" => $this->_json_body(["ref_merchantId" => 10023, "c_cashinChannelGroup" => "virtual_account", "c_externalIdDefault" => "1", "c_feeType" => "Flat", "c_fee" => 2500, "c_feePercetange" => 0, "c_amountMin" => 10000, "c_amountMax" => 10000000, "c_status" => "Active"]), "responses" => $this->_resp200()]],
                "/merchant/setting-cashin-fee/edit/{id}/{channelId}" => ["post" => ["tags" => ["Merchant Fee & Balance Data"], "summary" => "POST /merchant/setting-cashin-fee/edit — Edit Cashin Fee", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 10023], ["name" => "channelId", "in" => "path", "required" => true, "schema" => ["type" => "string"], "example" => "va_bni"]], "requestBody" => $this->_json_body(["ref_cashinChannelId" => "va_bni", "c_cashinChannelGroup" => "virtual_account", "c_externalIdDefault" => "1", "c_feeType" => "Flat", "c_fee" => 2500, "c_feePercetange" => 0, "c_settlementInterval" => 0, "c_amountMin" => 10000, "c_amountMax" => 10000000, "c_status" => "Active"]), "responses" => $this->_resp200()]],
                "/merchant/setting-cashin-fee/delete/{id}/{channelId}" => ["post" => ["tags" => ["Merchant Fee & Balance Data"], "summary" => "POST /merchant/setting-cashin-fee/delete — Delete Cashin Fee", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 10023], ["name" => "channelId", "in" => "path", "required" => true, "schema" => ["type" => "string"], "example" => "va_bni"]], "requestBody" => $this->_json_body(["delete" => true]), "responses" => $this->_resp200()]],
                "/merchant/setting-cashin-fee/groups" => ["get" => ["tags" => ["Merchant Fee & Balance Data"], "summary" => "GET /merchant/setting-cashin-fee/groups — Cashin Groups JSON", "responses" => $this->_resp200()]],
                "/merchant/setting-cashout-fee/{id}" => ["get" => ["tags" => ["Merchant Fee & Balance Data"], "summary" => "GET /merchant/setting-cashout-fee/{id} — Fetch Cashout Fee Settings JSON", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 10023]], "responses" => $this->_resp200()]],
                "/merchant/setting-cashout-fee/create" => ["post" => ["tags" => ["Merchant Fee & Balance Data"], "summary" => "POST /merchant/setting-cashout-fee/create — Create Cashout Fee", "requestBody" => $this->_json_body(["ref_merchantId" => 10023, "ref_cashoutChannelId" => "bi_fast", "c_cashoutChannelGroup" => "Bank", "c_externalIdDefault" => "1", "c_feeType" => "Fixed", "c_fee" => 5000, "c_feePercetange" => 0, "c_amountMin" => 0, "c_amountMax" => 100000000, "c_status" => "Active"]), "responses" => $this->_resp200()]],
                "/merchant/setting-cashout-fee/bulk-create/{id}" => ["post" => ["tags" => ["Merchant Fee & Balance Data"], "summary" => "POST /merchant/setting-cashout-fee/bulk-create/{id} — Bulk Cashout Fee", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 10023]], "requestBody" => $this->_json_body(["ref_merchantId" => 10023, "c_cashoutChannelGroup" => "bifast", "c_externalIdDefault" => "1", "c_feeType" => "Flat", "c_fee" => 5000, "c_feePercetange" => 0, "c_amountMin" => 10000, "c_amountMax" => 50000000, "c_status" => "Active"]), "responses" => $this->_resp200()]],
                "/merchant/setting-cashout-fee/edit/{id}/{channelId}" => ["post" => ["tags" => ["Merchant Fee & Balance Data"], "summary" => "POST /merchant/setting-cashout-fee/edit — Edit Cashout Fee", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 10023], ["name" => "channelId", "in" => "path", "required" => true, "schema" => ["type" => "string"], "example" => "bi_fast"]], "requestBody" => $this->_json_body(["ref_cashoutChannelId" => "bi_fast", "c_cashoutChannelGroup" => "bifast", "c_externalIdDefault" => "1", "c_feeType" => "Flat", "c_fee" => 5000, "c_feePercetange" => 0, "c_settlementInterval" => 0, "c_amountMin" => 10000, "c_amountMax" => 50000000, "c_status" => "Active"]), "responses" => $this->_resp200()]],
                "/merchant/setting-cashout-fee/delete/{id}/{channelId}" => ["post" => ["tags" => ["Merchant Fee & Balance Data"], "summary" => "POST /merchant/setting-cashout-fee/delete — Delete Cashout Fee", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 10023], ["name" => "channelId", "in" => "path", "required" => true, "schema" => ["type" => "string"], "example" => "bi_fast"]], "requestBody" => $this->_json_body(["delete" => true]), "responses" => $this->_resp200()]],
                "/merchant/setting-cashout-fee/groups" => ["get" => ["tags" => ["Merchant Fee & Balance Data"], "summary" => "GET /merchant/setting-cashout-fee/groups — Cashout Groups JSON", "responses" => $this->_resp200()]],
                "/merchant/balance/credit" => ["post" => ["tags" => ["Merchant Fee & Balance Data"], "summary" => "POST /merchant/balance/credit — Add Manual Credit Balance", "requestBody" => $this->_json_body(["merchantId" => 10023, "channelId" => "SYSTEM", "rawAmountCredit" => 1000000, "description" => "Manual Topup Balance Admin"]), "responses" => $this->_resp200()]],
                "/merchant/balance/debit" => ["post" => ["tags" => ["Merchant Fee & Balance Data"], "summary" => "POST /merchant/balance/debit — Apply Manual Debit Balance Penalty", "requestBody" => $this->_json_body(["merchantIdDebit" => 10023, "channelId" => "SYSTEM", "rawAmountDebit" => 500000, "description" => "Manual Penalty Balance Admin"]), "responses" => $this->_resp200()]],
                "/merchant/permissions/{id}" => ["get" => ["tags" => ["Merchant Fee & Balance Data"], "summary" => "GET /merchant/permissions/{id} — Fetch Merchant Permissions JSON", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 10023]], "responses" => $this->_resp200()]],
                "/merchant/permissions/{id}/save" => ["post" => ["tags" => ["Merchant Fee & Balance Data"], "summary" => "POST /merchant/permissions/{id}/save — Save Permissions", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 10023]], "requestBody" => $this->_json_body(["permissions" => ["1" => "Grant", "2" => "Deny"]]), "responses" => $this->_resp200()]],

                // ── Merchant Sub-Account Data ──
                "/merchant/sub-account/reset" => ["get" => ["tags" => ["Merchant Sub-Account Data"], "summary" => "GET /merchant/sub-account/reset — Reset Submerchant Filters", "responses" => $this->_resp200()]],
                "/merchant/sub-account/register" => ["post" => ["tags" => ["Merchant Sub-Account Data"], "summary" => "POST /merchant/sub-account/register — Register Submerchant", "requestBody" => $this->_json_body(["ref_merchantId" => 10023, "c_name" => "Sub Account Alpha", "c_email" => "subaccount@gidi.co.id", "c_status" => "Active"]), "responses" => $this->_resp200()]],
                "/merchant/sub-account/edit/{id}" => ["post" => ["tags" => ["Merchant Sub-Account Data"], "summary" => "POST /merchant/sub-account/edit/{id} — Edit Submerchant", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 10024]], "requestBody" => $this->_json_body(["c_name" => "Sub Account Alpha Updated", "c_email" => "subaccount@gidi.co.id", "c_status" => "Active"]), "responses" => $this->_resp200()]],
                "/merchant/sub-account/list" => ["get" => ["tags" => ["Merchant Sub-Account Data"], "summary" => "GET /merchant/sub-account/list — Fetch Submerchants List JSON", "responses" => $this->_resp200()]],
                "/merchant/sub-account/{id}" => ["get" => ["tags" => ["Merchant Sub-Account Data"], "summary" => "GET /merchant/sub-account/{id} — Submerchant Detail JSON", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 10024]], "responses" => $this->_resp200()]],

                // ── Report Download Data ──
                "/report/download/reset" => ["get" => ["tags" => ["Report Download Data"], "summary" => "GET /report/download/reset — Reset Report Filters", "responses" => $this->_resp200()]],
                "/report/download/export" => ["post" => ["tags" => ["Report Download Data"], "summary" => "POST /report/download/export — Export Statement Excel", "requestBody" => $this->_json_body(["filename" => "report_statement_10023.xlsx", "merchant_id" => 10023, "date_from" => "2026-08-01", "date_to" => "2026-08-27", "format" => "xlsx"]), "responses" => $this->_resp200()]],
                "/report/balance-log/reset" => ["get" => ["tags" => ["Report Download Data"], "summary" => "GET /report/balance-log/reset — Reset Balance Log Filters", "responses" => $this->_resp200()]],

                // ── User Access & Menu Data ──
                "/access-control/accounts/create" => ["post" => ["tags" => ["User Access & Menu Data"], "summary" => "POST /access-control/accounts/create — Create New Admin Account", "requestBody" => $this->_json_body(["c_email" => "admin@gidi.co.id", "c_name" => "Admin Alpha", "c_level" => "2", "c_status" => "Active", "role_id" => "1", "c_password" => "AdminSecret123!", "c_password_confirm" => "AdminSecret123!"]), "responses" => $this->_resp200()]],
                "/access-control/holiday/manage" => ["post" => ["tags" => ["User Access & Menu Data"], "summary" => "POST /access-control/holiday/manage — Manage Holiday Calendar", "requestBody" => $this->_json_body(["c_date" => "2026-12-25", "c_desc" => "Hari Natal", "c_status" => "Active", "c_action" => "create"]), "responses" => $this->_resp200()]],
                "/access-control/accounts/update/{id}" => ["post" => ["tags" => ["User Access & Menu Data"], "summary" => "POST /access-control/accounts/update/{id} — Update Admin Account", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 5]], "requestBody" => $this->_json_body(["c_email" => "admin@gidi.co.id", "c_name" => "Admin Alpha Updated", "c_level" => "2", "c_status" => "Active", "role_id" => "1"]), "responses" => $this->_resp200()]],
                "/access-control/accounts/delete/{id}" => ["post" => ["tags" => ["User Access & Menu Data"], "summary" => "POST /access-control/accounts/delete/{id} — Delete Admin Account", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 5]], "requestBody" => $this->_json_body(["delete" => true]), "responses" => $this->_resp200()]],
                "/menu/change-menu/{id}" => ["post" => ["tags" => ["User Access & Menu Data"], "summary" => "POST /menu/change-menu/{id} — Toggle Menu Status", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 1]], "requestBody" => $this->_json_body(["status" => 1]), "responses" => $this->_resp200()]],
                "/menu/update-menu" => ["post" => ["tags" => ["User Access & Menu Data"], "summary" => "POST /menu/update-menu — Update Menu Title", "requestBody" => $this->_json_body(["id" => 1, "menu" => "Dashboard Updated"]), "responses" => $this->_resp200()]],
                "/menu/update-menu/ajax" => ["post" => ["tags" => ["User Access & Menu Data"], "summary" => "POST /menu/update-menu/ajax — Update Menu Ajax", "requestBody" => $this->_json_body(["id" => 1, "menu" => "Dashboard Updated"]), "responses" => $this->_resp200()]],
                "/menu/sub-menu/update" => ["post" => ["tags" => ["User Access & Menu Data"], "summary" => "POST /menu/sub-menu/update — Update Sub-Menu Data", "requestBody" => $this->_json_body(["id" => 2, "menu_id" => 1, "title" => "Sub Menu Updated", "url" => "dashboard/sub", "icon" => "fas fa-fw fa-folder", "is_active" => 1]), "responses" => $this->_resp200()]],
                "/menu/delete/{id}" => ["post" => ["tags" => ["User Access & Menu Data"], "summary" => "POST /menu/delete/{id} — Delete Menu Item", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 5]], "requestBody" => $this->_json_body(["delete" => true]), "responses" => $this->_resp200()]],
                "/menu/sub-menu/delete/{id}" => ["post" => ["tags" => ["User Access & Menu Data"], "summary" => "POST /menu/sub-menu/delete/{id} — Delete Sub-Menu Item", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 8]], "requestBody" => $this->_json_body(["delete" => true]), "responses" => $this->_resp200()]],
                "/access-control/roles/access/{id}" => ["get" => ["tags" => ["User Access & Menu Data"], "summary" => "GET /access-control/roles/access/{id} — Role Access Detail JSON", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 2]], "responses" => $this->_resp200()]],
                "/access-control/roles/change-access" => ["post" => ["tags" => ["User Access & Menu Data"], "summary" => "POST /access-control/roles/change-access — Toggle Role Access", "requestBody" => $this->_json_body(["role_id" => 2, "menu_id" => 1]), "responses" => $this->_resp200()]],
                "/menu/save/ajax" => ["post" => ["tags" => ["User Access & Menu Data"], "summary" => "POST /menu/save/ajax — Save New Menu Item", "requestBody" => $this->_json_body(["title" => "Menu Baru", "url" => "new-menu"]), "responses" => $this->_resp200()]],
                "/menu/delete/ajax" => ["post" => ["tags" => ["User Access & Menu Data"], "summary" => "POST /menu/delete/ajax — Delete Menu Item Ajax", "requestBody" => $this->_json_body(["id" => 10]), "responses" => $this->_resp200()]],
                "/menu/get/{id}" => ["get" => ["tags" => ["User Access & Menu Data"], "summary" => "GET /menu/get/{id} — Fetch Menu Detail JSON", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 1]], "responses" => $this->_resp200()]],
                "/user/change-password" => ["post" => ["tags" => ["User Access & Menu Data"], "summary" => "POST /user/change-password — Update Admin User Password", "requestBody" => $this->_form_body(["current_password" => ["type" => "string", "example" => "OldPass123!"], "new_password" => ["type" => "string", "example" => "NewPass123!"], "confirm_password" => ["type" => "string", "example" => "NewPass123!"]]), "responses" => $this->_resp200()]],

                // ── Cashin External Channels Data ──
                "/external/cashin/list" => ["post" => ["tags" => ["Cashin External Channels Data"], "summary" => "POST /external/cashin/list — Cashin Channels DataTable JSON", "requestBody" => $this->_json_body(["draw" => 1, "start" => 0, "length" => 10]), "responses" => $this->_resp200()]],
                "/external/cashin/add" => ["post" => ["tags" => ["Cashin External Channels Data"], "summary" => "POST /external/cashin/add — Add Cashin External Channel", "requestBody" => $this->_json_body(["ref_merchantId" => 10023, "ref_cashinChannelId" => "va_bni", "c_cashinChannelGroup" => "VA", "c_externalIdDefault" => "1", "c_feeType" => "Fixed", "c_fee" => 2500, "c_feePercetange" => 0, "c_settlementInterval" => 0, "c_amountMin" => 0, "c_amountMax" => 100000000, "c_status" => "Active"]), "responses" => $this->_resp200()]],
                "/external/cashin/update" => ["post" => ["tags" => ["Cashin External Channels Data"], "summary" => "POST /external/cashin/update — Update Cashin Channel", "requestBody" => $this->_json_body(["id" => 1, "ref_merchantId" => 10023, "ref_cashinChannelId" => "va_bni", "c_cashinChannelGroup" => "VA", "c_externalIdDefault" => "1", "c_feeType" => "Fixed", "c_fee" => 2500, "c_feePercetange" => 0, "c_settlementInterval" => 0, "c_amountMin" => 0, "c_amountMax" => 100000000, "c_status" => "Active"]), "responses" => $this->_resp200()]],
                "/external/cashin/delete/{id}" => ["post" => ["tags" => ["Cashin External Channels Data"], "summary" => "POST /external/cashin/delete/{id} — Delete Cashin Channel", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 1]], "requestBody" => $this->_json_body(["delete" => true]), "responses" => $this->_resp200()]],
                "/external/cashin/bulk-update" => ["post" => ["tags" => ["Cashin External Channels Data"], "summary" => "POST /external/cashin/bulk-update — Bulk Update Mappings", "requestBody" => $this->_json_body(["update_type" => "global", "current_group" => "VA", "new_group" => "VA", "current_externalId" => "PAYLABS", "new_externalId" => "IFP", "current_cashinChannelId" => "va_bni", "new_cashinChannelId" => "va_bni", "current_status" => "Active", "new_status" => "Active"]), "responses" => $this->_resp200()]],
                "/external/cashin/get-channels" => ["get" => ["tags" => ["Cashin External Channels Data"], "summary" => "GET /external/cashin/get-channels — Fetch Channel IDs JSON", "responses" => $this->_resp200()]],
                "/external/cashin/get-filter-options" => ["get" => ["tags" => ["Cashin External Channels Data"], "summary" => "GET /external/cashin/get-filter-options — Fetch Filter Options JSON", "responses" => $this->_resp200()]],
                "/external/cashin/get-merchant-mappings" => ["post" => ["tags" => ["Cashin External Channels Data"], "summary" => "POST /external/cashin/get-merchant-mappings — Fetch Mappings JSON", "requestBody" => $this->_json_body(["merchant_ids" => [10023, 10024]]), "responses" => $this->_resp200()]],

                // ── Cashout External Channels Data ──
                "/external/cashout/list" => ["post" => ["tags" => ["Cashout External Channels Data"], "summary" => "POST /external/cashout/list — Cashout Channels DataTable JSON", "requestBody" => $this->_json_body(["draw" => 1, "start" => 0, "length" => 10]), "responses" => $this->_resp200()]],
                "/external/cashout/add" => ["post" => ["tags" => ["Cashout External Channels Data"], "summary" => "POST /external/cashout/add — Add Cashout External Channel", "requestBody" => $this->_json_body(["ref_merchantId" => 10023, "ref_cashoutChannelId" => "bi_fast", "c_cashoutChannelGroup" => "Bank", "c_externalIdDefault" => "1", "c_feeType" => "Fixed", "c_fee" => 5000, "c_feePercetange" => 0, "c_amountMin" => 0, "c_amountMax" => 100000000, "c_status" => "Active"]), "responses" => $this->_resp200()]],
                "/external/cashout/update" => ["post" => ["tags" => ["Cashout External Channels Data"], "summary" => "POST /external/cashout/update — Update Cashout Channel", "requestBody" => $this->_json_body(["id" => 1, "ref_merchantId" => 10023, "ref_cashoutChannelId" => "bi_fast", "c_cashoutChannelGroup" => "Bank", "c_externalIdDefault" => "1", "c_feeType" => "Fixed", "c_fee" => 5000, "c_feePercetange" => 0, "c_amountMin" => 0, "c_amountMax" => 100000000, "c_status" => "Active"]), "responses" => $this->_resp200()]],
                "/external/cashout/delete/{id}" => ["post" => ["tags" => ["Cashout External Channels Data"], "summary" => "POST /external/cashout/delete/{id} — Delete Cashout Channel", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 1]], "requestBody" => $this->_json_body(["delete" => true]), "responses" => $this->_resp200()]],
                "/external/cashout/bulk-update" => ["post" => ["tags" => ["Cashout External Channels Data"], "summary" => "POST /external/cashout/bulk-update — Bulk Update Cashout Mappings", "requestBody" => $this->_json_body(["update_type" => "global", "current_group" => "Bank", "new_group" => "Bank", "current_externalId" => "PAYLABS", "new_externalId" => "IFP", "current_cashoutChannelId" => "bi_fast", "new_cashoutChannelId" => "bi_fast", "current_status" => "Active", "new_status" => "Active"]), "responses" => $this->_resp200()]],
                "/external/cashout/get-channels" => ["get" => ["tags" => ["Cashout External Channels Data"], "summary" => "GET /external/cashout/get-channels — Fetch Cashout Channel IDs JSON", "responses" => $this->_resp200()]],
                "/external/cashout/get-filter-options" => ["get" => ["tags" => ["Cashout External Channels Data"], "summary" => "GET /external/cashout/get-filter-options — Fetch Cashout Filter Options JSON", "responses" => $this->_resp200()]],
                "/external/cashout/get-merchant-mappings" => ["post" => ["tags" => ["Cashout External Channels Data"], "summary" => "POST /external/cashout/get-merchant-mappings — Fetch Cashout Mappings JSON", "requestBody" => $this->_json_body(["merchant_ids" => [10023, 10024]]), "responses" => $this->_resp200()]],

                // ── Channel Management Data ──
                "/channel/cashin/create" => ["post" => ["tags" => ["Channel Management Data"], "summary" => "POST /channel/cashin/create — Create Master Cashin Channel", "requestBody" => $this->_json_body(["id" => "va_mandiri", "chanelgroup" => "VA", "description" => "Virtual Account Mandiri", "externaldefault" => "1", "feetype" => "Fixed", "fee" => 2500, "settlementinterval" => 0, "amountmin" => 0, "amountmax" => 100000000]), "responses" => $this->_resp200()]],
                "/channel/cashout/create" => ["post" => ["tags" => ["Channel Management Data"], "summary" => "POST /channel/cashout/create — Create Master Cashout Channel", "requestBody" => $this->_json_body(["id" => "bi_fast", "chanelgroup" => "CASHOUT", "description" => "BI-FAST Cashout", "externaldefault" => "1", "feetype" => "Fixed", "fee" => 5000, "amountmin" => 0, "amountmax" => 100000000]), "responses" => $this->_resp200()]],
                "/channel/cashin/update" => ["post" => ["tags" => ["Channel Management Data"], "summary" => "POST /channel/cashin/update — Update Cashin Channel", "requestBody" => $this->_json_body(["pk_id" => 1, "id" => "va_mandiri", "chanelgroup" => "VA", "description" => "VA Mandiri Updated", "externaldefault" => "1", "feetype" => "Fixed", "fee" => 2500, "settlementinterval" => 0, "amountmin" => 0, "amountmax" => 100000000]), "responses" => $this->_resp200()]],
                "/channel/cashout/update" => ["post" => ["tags" => ["Channel Management Data"], "summary" => "POST /channel/cashout/update — Update Cashout Channel", "requestBody" => $this->_json_body(["pk_id" => 2, "id" => "bi_fast", "chanelgroup" => "Bank", "description" => "BI-FAST Updated", "externaldefault" => "1", "feetype" => "Fixed", "fee" => 5000, "amountmin" => 0, "amountmax" => 100000000]), "responses" => $this->_resp200()]],
                "/channel/cashin/delete/{channelId}" => ["post" => ["tags" => ["Channel Management Data"], "summary" => "POST /channel/cashin/delete — Delete Cashin Channel", "parameters" => [["name" => "channelId", "in" => "path", "required" => true, "schema" => ["type" => "string"], "example" => "va_bni"]], "requestBody" => $this->_json_body(["delete" => true]), "responses" => $this->_resp200()]],
                "/channel/cashout/delete/{channelId}" => ["post" => ["tags" => ["Channel Management Data"], "summary" => "POST /channel/cashout/delete — Delete Cashout Channel", "parameters" => [["name" => "channelId", "in" => "path", "required" => true, "schema" => ["type" => "string"], "example" => "bi_fast"]], "requestBody" => $this->_json_body(["delete" => true]), "responses" => $this->_resp200()]],
                "/channel/get-master-filter-options" => ["get" => ["tags" => ["Channel Management Data"], "summary" => "GET /channel/get-master-filter-options — Master Channel Options JSON", "responses" => $this->_resp200()]],

                // ── Service & Products Data (PPOB) ──
                "/product/create" => ["post" => ["tags" => ["Service & Products Data (PPOB)"], "summary" => "POST /product/create — Create PPOB Product Item", "requestBody" => $this->_json_body(["caption" => "Pulsa 10RB", "price" => 10200, "description" => "Pulsa Reguler 10k", "name" => "pulsa_10k", "channelgroup" => "ppob"]), "responses" => $this->_resp200()]],
                "/product/update" => ["post" => ["tags" => ["Service & Products Data (PPOB)"], "summary" => "POST /product/update — Update PPOB Product Item", "requestBody" => $this->_json_body(["id" => "pulsa_10k", "caption" => "Pulsa 10RB Updated", "price" => 10500, "description" => "Pulsa Reguler 10k Updated"]), "responses" => $this->_resp200()]],
                "/product/delete/{id}" => ["post" => ["tags" => ["Service & Products Data (PPOB)"], "summary" => "POST /product/delete/{id} — Delete PPOB Product Item", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 1]], "requestBody" => $this->_json_body(["delete" => true]), "responses" => $this->_resp200()]],

                // ── Health & Merchant RBAC Data ──
                "/health/db-check" => ["get" => ["tags" => ["Health & Merchant RBAC Data"], "summary" => "GET /health/db-check — Health Check DB Connection Status", "responses" => $this->_resp200()]],
                "/merchant/access-control/roles" => ["get" => ["tags" => ["Health & Merchant RBAC Data"], "summary" => "GET /merchant/access-control/roles — Merchant Roles List JSON", "responses" => $this->_resp200()]],
                "/merchant/access-control/roles/reset" => ["get" => ["tags" => ["Health & Merchant RBAC Data"], "summary" => "GET /merchant/access-control/roles/reset — Reset Roles Filter", "responses" => $this->_resp200()]],
                "/merchant/access-control/roles/save" => ["post" => ["tags" => ["Health & Merchant RBAC Data"], "summary" => "POST /merchant/access-control/roles/save — Save Merchant Role", "requestBody" => $this->_json_body(["role_name" => "Manager", "permissions" => ["view_report"]]), "responses" => $this->_resp200()]],
                "/merchant/access-control/roles/permissions/{id}" => ["get" => ["tags" => ["Health & Merchant RBAC Data"], "summary" => "GET /merchant/access-control/roles/permissions/{id} — Role Permissions JSON", "parameters" => [["name" => "id", "in" => "path", "required" => true, "schema" => ["type" => "integer"], "example" => 2]], "responses" => $this->_resp200()]],
                "/merchant/access-control/menus" => ["get" => ["tags" => ["Health & Merchant RBAC Data"], "summary" => "GET /merchant/access-control/menus — Merchant Menus List JSON", "responses" => $this->_resp200()]],
                "/merchant/access-control/menus/save" => ["post" => ["tags" => ["Health & Merchant RBAC Data"], "summary" => "POST /merchant/access-control/menus/save — Save Merchant Menu", "requestBody" => $this->_json_body(["menu_title" => "Report Sub", "url" => "merchant/report"]), "responses" => $this->_resp200()]]
            ]
        ];

        $json_data = json_encode($spec, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        @file_put_contents(FCPATH . 'gidi_admin_openapi_v1.json', $json_data);

        if ($this->input->get('download') == '1') {
            echo $json_data;
        } else {
            $this->output->set_output($json_data);
        }
    }
}
