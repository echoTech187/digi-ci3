<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Channels_And_Pricing extends CI_Migration {

    public function up() {
        // 1. Cashin Channel Table
        $this->dbforge->add_field([
            'id' => ['type' => 'VARCHAR', 'constraint' => 30],
            'c_channelGroup' => ['type' => 'VARCHAR', 'constraint' => 30],
            'c_description' => ['type' => 'VARCHAR', 'constraint' => 255],
            'c_externalIdDefault' => ['type' => 'VARCHAR', 'constraint' => 30],
            'c_feeType' => ['type' => 'ENUM("Fixed","Percetange","Both")'],
            'c_fee' => ['type' => 'DOUBLE', 'constraint' => '10,2', 'default' => 0.00],
            'c_feePercetange' => ['type' => 'FLOAT', 'default' => 0],
            'c_settlementInterval' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'c_amountMin' => ['type' => 'DOUBLE', 'constraint' => '20,2', 'default' => 0.00],
            'c_amountMax' => ['type' => 'DOUBLE', 'constraint' => '20,2', 'default' => 0.00],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('cashin_channel');
        $this->db->query('ALTER TABLE cashin_channel ADD UNIQUE KEY cashin_channel_UN_1 (id, c_channelGroup)');

        // 2. Cashout Channel Table
        $this->dbforge->add_field([
            'id' => ['type' => 'VARCHAR', 'constraint' => 30],
            'c_paylabsId' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => TRUE],
            'c_channelGroup' => ['type' => 'VARCHAR', 'constraint' => 30],
            'c_channelGroup2' => ['type' => 'VARCHAR', 'constraint' => 30],
            'c_description' => ['type' => 'VARCHAR', 'constraint' => 255],
            'c_caption' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
            'c_externalIdDefault' => ['type' => 'VARCHAR', 'constraint' => 30],
            'c_feeType' => ['type' => 'ENUM("Fixed","Percetange","Both")'],
            'c_fee' => ['type' => 'DOUBLE', 'constraint' => '10,2', 'default' => 0.00],
            'c_feePercetange' => ['type' => 'FLOAT', 'default' => 0, 'null' => TRUE],
            'c_amountMin' => ['type' => 'DOUBLE', 'constraint' => '20,2', 'default' => 0.00],
            'c_amountMax' => ['type' => 'DOUBLE', 'constraint' => '20,2', 'default' => 0.00],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('cashout_channel');
        $this->db->query('ALTER TABLE cashout_channel ADD UNIQUE KEY cashout_channel_UN_1 (id, c_channelGroup)');

        // 3. Cashin Channel x Merchant Table
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'ref_merchantId' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => TRUE],
            'c_cashinChannelGroup' => ['type' => 'VARCHAR', 'constraint' => 30],
            'ref_cashinChannelId' => ['type' => 'VARCHAR', 'constraint' => 30],
            'c_externalIdDefault' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => TRUE],
            'c_feeType' => ['type' => 'ENUM("Fixed","Percetange","Both")'],
            'c_fee' => ['type' => 'DOUBLE', 'constraint' => '6,2', 'default' => 0.00],
            'c_feePercetange' => ['type' => 'FLOAT', 'default' => 0],
            'c_settlementInterval' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'c_amountMin' => ['type' => 'DOUBLE', 'constraint' => '20,2', 'default' => 0.00],
            'c_amountMax' => ['type' => 'DOUBLE', 'constraint' => '20,2', 'default' => 0.00],
            'c_status' => ['type' => 'ENUM("Active","Not Active")', 'default' => 'Active'],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('cashin_channel_x_merchant');
        $this->db->query('ALTER TABLE cashin_channel_x_merchant ADD UNIQUE KEY cashin_channel_x_merchant_UN_1 (ref_merchantId, ref_cashinChannelId)');

        // 4. Cashout Channel x Merchant Table
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'ref_merchantId' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => TRUE],
            'c_cashoutChannelGroup' => ['type' => 'VARCHAR', 'constraint' => 30],
            'ref_cashoutChannelId' => ['type' => 'VARCHAR', 'constraint' => 30],
            'c_externalIdDefault' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => TRUE],
            'c_feeType' => ['type' => 'ENUM("Fixed","Percetange","Both")'],
            'c_fee' => ['type' => 'DOUBLE', 'constraint' => '6,2', 'default' => 0.00],
            'c_feePercetange' => ['type' => 'FLOAT', 'default' => 0, 'null' => TRUE],
            'c_amountMin' => ['type' => 'DOUBLE', 'constraint' => '20,2', 'default' => 0.00],
            'c_amountMax' => ['type' => 'DOUBLE', 'constraint' => '20,2', 'default' => 0.00],
            'c_status' => ['type' => 'ENUM("Active","Not Active")', 'default' => 'Active'],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('cashout_channel_x_merchant');
        $this->db->query('ALTER TABLE cashout_channel_x_merchant ADD UNIQUE KEY cashout_channel_x_merchant_UN_1 (ref_merchantId, ref_cashoutChannelId)');

        // 5. External x Channel Tables
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'c_cashinExternalId' => ['type' => 'VARCHAR', 'constraint' => 30],
            'c_cashinChannelGroup' => ['type' => 'VARCHAR', 'constraint' => 30],
            'ref_cashinChannelId' => ['type' => 'VARCHAR', 'constraint' => 30],
            'c_feeType' => ['type' => 'ENUM("Fixed","Percetange","Both")'],
            'c_fee' => ['type' => 'DOUBLE', 'constraint' => '6,2', 'default' => 0.00],
            'c_feePercetange' => ['type' => 'FLOAT', 'default' => 0],
            'c_settlementInterval' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'c_amountMin' => ['type' => 'DOUBLE', 'constraint' => '20,2', 'default' => 0.00],
            'c_amountMax' => ['type' => 'DOUBLE', 'constraint' => '20,2', 'default' => 0.00],
            'c_status' => ['type' => 'ENUM("Active","Not Active")', 'default' => 'Active'],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('cashin_external_x_channel');

        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'c_cashoutExternalId' => ['type' => 'VARCHAR', 'constraint' => 30],
            'c_cashoutChannelGroup' => ['type' => 'VARCHAR', 'constraint' => 30],
            'c_cashoutChannelGroup2' => ['type' => 'VARCHAR', 'constraint' => 30],
            'ref_cashoutChannelId' => ['type' => 'VARCHAR', 'constraint' => 30],
            'c_feeType' => ['type' => 'ENUM("Fixed","Percetange","Both")'],
            'c_fee' => ['type' => 'DOUBLE', 'constraint' => '10,2', 'default' => 0.00],
            'c_feePercetange' => ['type' => 'FLOAT', 'default' => 0, 'null' => TRUE],
            'c_amountMin' => ['type' => 'DOUBLE', 'constraint' => '20,2', 'default' => 0.00],
            'c_amountMax' => ['type' => 'DOUBLE', 'constraint' => '20,2', 'default' => 0.00],
            'c_status' => ['type' => 'ENUM("Active","Not Active")', 'default' => 'Active'],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('cashout_external_x_channel');
    }

    public function down() {
        $this->dbforge->drop_table('cashout_external_x_channel', TRUE);
        $this->dbforge->drop_table('cashin_external_x_channel', TRUE);
        $this->dbforge->drop_table('cashout_channel_x_merchant', TRUE);
        $this->dbforge->drop_table('cashin_channel_x_merchant', TRUE);
        $this->dbforge->drop_table('cashout_channel', TRUE);
        $this->dbforge->drop_table('cashin_channel', TRUE);
    }
}
