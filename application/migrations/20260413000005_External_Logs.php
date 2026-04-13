<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_External_Logs extends CI_Migration {

    public function up() {
        // 1. OpenAPI Log Table
        $this->dbforge->add_field([
            'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'c_serviceName' => ['type' => 'VARCHAR', 'constraint' => 30],
            'ref_merchantId' => ['type' => 'INT', 'constraint' => 11],
            'ref_subMerchantId' => ['type' => 'INT', 'null' => TRUE],
            'c_requestIp' => ['type' => 'VARCHAR', 'constraint' => 30],
            'c_requestId' => ['type' => 'VARCHAR', 'constraint' => 100],
            'c_requestDatetime' => ['type' => 'DATETIME'],
            'c_requestHeader' => ['type' => 'JSON'],
            'c_requestBody' => ['type' => 'JSON'],
            'c_responseDatetime' => ['type' => 'DATETIME', 'null' => TRUE],
            'c_responseHeader' => ['type' => 'JSON', 'null' => TRUE],
            'c_responseBody' => ['type' => 'JSON', 'null' => TRUE],
            'c_statusCode' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => TRUE],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('openapi_log');

        // 2. SNAP Token Log
        $this->dbforge->add_field([
            'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'c_datetimeRequest' => ['type' => 'DATETIME'],
            'c_requestHeader' => ['type' => 'TEXT'],
            'c_requestBody' => ['type' => 'TEXT'],
            'c_datetimeResponse' => ['type' => 'DATETIME', 'null' => TRUE],
            'c_responseHeader' => ['type' => 'TEXT', 'null' => TRUE],
            'c_responseBody' => ['type' => 'TEXT', 'null' => TRUE],
            'c_responseCode' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => TRUE],
            'c_responseToken' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => TRUE],
            'c_datetimeExpired' => ['type' => 'DATETIME', 'null' => TRUE],
            'c_status' => ['type' => 'ENUM("Pending","Success","Failed","Timeout")', 'default' => 'Pending'],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('external_gvconnect_snap_token');

        // 3. QRIS Callback Log
        $this->dbforge->add_field([
            'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'c_datetimeRequest' => ['type' => 'DATETIME'],
            'c_datetimePayment' => ['type' => 'DATETIME'],
            'c_requestHeader' => ['type' => 'TEXT'],
            'c_requestBody' => ['type' => 'TEXT'],
            'c_businessId' => ['type' => 'VARCHAR', 'constraint' => 100],
            'c_billNumber' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => TRUE],
            'c_referenceLabel' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => TRUE],
            'c_invoiceNo' => ['type' => 'VARCHAR', 'constraint' => 100],
            'c_amount' => ['type' => 'DOUBLE', 'constraint' => '20,2'],
            'c_mdr' => ['type' => 'DOUBLE', 'constraint' => '6,2', 'null' => TRUE],
            'c_amountFinal' => ['type' => 'DOUBLE', 'constraint' => '20,2', 'null' => TRUE],
            'c_issuerId' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => TRUE],
            'c_issuerName' => ['type' => 'VARCHAR', 'constraint' => 200, 'null' => TRUE],
            'c_issuerRrn' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => TRUE],
            'c_status' => ['type' => 'ENUM("Pending","Success","Failed","Timeout")', 'default' => 'Pending'],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('external_gvconnect_snap_qris_mpm_callback');

        // 4. OpenAPI Callback Log
        $this->dbforge->add_field([
            'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'ref_merchantId' => ['type' => 'INT', 'constraint' => 11],
            'c_type' => ['type' => 'ENUM("Qris Mpm","Va","Ewallet","Disbursement")', 'null' => TRUE],
            'c_typeChannel' => ['type' => 'ENUM("Static","Dynamic","Recurring","Bifast")', 'null' => TRUE],
            'c_invoiceNo' => ['type' => 'VARCHAR', 'constraint' => 100],
            'c_merchantTransactionId' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => TRUE],
            'c_urlDestination' => ['type' => 'TEXT'],
            'c_requestDatetime' => ['type' => 'DATETIME'],
            'c_requestHeader' => ['type' => 'JSON', 'null' => TRUE],
            'c_requestBody' => ['type' => 'JSON'],
            'c_responseDatetime' => ['type' => 'DATETIME', 'null' => TRUE],
            'c_responseHeader' => ['type' => 'JSON', 'null' => TRUE],
            'c_responseBody' => ['type' => 'TEXT', 'null' => TRUE],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('openapi_log_callback');
    }

    public function down() {
        $this->dbforge->drop_table('openapi_log_callback', TRUE);
        $this->dbforge->drop_table('external_gvconnect_snap_qris_mpm_callback', TRUE);
        $this->dbforge->drop_table('external_gvconnect_snap_token', TRUE);
        $this->dbforge->drop_table('openapi_log', TRUE);
    }
}
