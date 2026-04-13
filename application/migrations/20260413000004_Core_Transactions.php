<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Core_Transactions extends CI_Migration {

    public function up() {
        // 1. Cashin Table
        $this->dbforge->add_field([
            'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'ref_merchantId' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => TRUE],
            'c_datetime' => ['type' => 'DATETIME'],
            'c_invoiceNo' => ['type' => 'VARCHAR', 'constraint' => 100],
            'c_description' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => TRUE],
            'c_cashinChannelGroup' => ['type' => 'VARCHAR', 'constraint' => 30],
            'ref_cashinChannelId' => ['type' => 'VARCHAR', 'constraint' => 30],
            'c_amount' => ['type' => 'DOUBLE', 'constraint' => '20,2'],
            'ref_cashoutId' => ['type' => 'BIGINT', 'constraint' => 20, 'null' => TRUE],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('cashin');
        $this->db->query('ALTER TABLE cashin ADD UNIQUE KEY cashin_UN_1 (c_datetime, c_invoiceNo)');

        // 2. Cashout Table
        $this->dbforge->add_field([
            'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'ref_merchantId' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => TRUE],
            'c_datetime' => ['type' => 'DATETIME'],
            'c_invoiceNo' => ['type' => 'VARCHAR', 'constraint' => 100],
            'c_description' => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => TRUE],
            'c_cashoutChannelGroup' => ['type' => 'VARCHAR', 'constraint' => 30],
            'ref_cashoutChannelId' => ['type' => 'VARCHAR', 'constraint' => 30],
            'c_amount' => ['type' => 'DOUBLE', 'constraint' => '20,2'],
            'ref_cashinId' => ['type' => 'BIGINT', 'constraint' => 20, 'null' => TRUE],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('cashout');
        $this->db->query('ALTER TABLE cashout ADD UNIQUE KEY cashout_UN_1 (c_datetime, c_invoiceNo)');

        // 3. Mutation Table
        $this->dbforge->add_field([
            'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'ref_merchantId' => ['type' => 'INT', 'constraint' => 11],
            'c_datetime' => ['type' => 'DATETIME'],
            'c_potition' => ['type' => 'ENUM("Credit","Debit")'],
            'ref_cashinId' => ['type' => 'BIGINT', 'constraint' => 20, 'null' => TRUE],
            'ref_cashoutId' => ['type' => 'BIGINT', 'constraint' => 20, 'null' => TRUE],
            'c_amount' => ['type' => 'DOUBLE', 'constraint' => '20,2'],
            'c_balanceAfter' => ['type' => 'DOUBLE', 'constraint' => '20,2'],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('mutation');

        // 4. Cashin Payment VA
        $this->dbforge->add_field([
            'id' => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'ref_merchantId' => ['type' => 'INT', 'constraint' => 11],
            'ref_subMerchantId' => ['type' => 'INT', 'constraint' => 11],
            'c_cashinChannelGroup' => ['type' => 'VARCHAR', 'constraint' => 30],
            'ref_cashinChannelId' => ['type' => 'VARCHAR', 'constraint' => 30],
            'ref_cashinId' => ['type' => 'BIGINT', 'constraint' => 20, 'null' => TRUE],
            'c_type' => ['type' => 'ENUM("Static","Dynamic","Recurring")'],
            'c_datetime' => ['type' => 'DATETIME'],
            'c_vaNumber' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => TRUE],
            'c_amount' => ['type' => 'DOUBLE', 'constraint' => '20,2'],
            'c_fee' => ['type' => 'DOUBLE', 'constraint' => '10,2'],
            'ref_cashinExternalId' => ['type' => 'VARCHAR', 'constraint' => 30],
            'c_datetimePayment' => ['type' => 'DATETIME'],
            'c_isSettlementRealtime' => ['type' => 'ENUM("0","1")'],
            'c_datetimeSettlement' => ['type' => 'DATETIME', 'null' => TRUE],
            'c_feeExternal' => ['type' => 'DOUBLE', 'constraint' => '8,2'],
            'c_isSettlementRealtimeExternal' => ['type' => 'ENUM("0","1")'],
            'c_datetimeSettlementExternal' => ['type' => 'DATETIME', 'null' => TRUE],
            'c_sourcePaymentLogIsCallbackOrStatus' => ['type' => 'ENUM("Callback","Check Status")'],
            'ref_sourcePaymentLogId' => ['type' => 'BIGINT', 'constraint' => 20],
            'ref_sourceCheckStatusLogId' => ['type' => 'BIGINT', 'constraint' => 20],
            'c_sendNotificationToMerchant' => ['type' => 'ENUM("0","1")', 'default' => '0'],
            'ref_cashinDynamicVaId' => ['type' => 'BIGINT', 'constraint' => 20, 'null' => TRUE],
            'ref_cashinRecurringVaId' => ['type' => 'BIGINT', 'constraint' => 20, 'null' => TRUE],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('cashin_payment_va');

        // Note: Similar fields for QRIS and Ewallet, omitted for brevity in mock but should be consistent
    }

    public function down() {
        $this->dbforge->drop_table('cashin_payment_va', TRUE);
        $this->dbforge->drop_table('mutation', TRUE);
        $this->dbforge->drop_table('cashout', TRUE);
        $this->dbforge->drop_table('cashin', TRUE);
    }
}
