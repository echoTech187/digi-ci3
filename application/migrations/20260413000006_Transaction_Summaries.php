<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Transaction_Summaries extends CI_Migration
{
    public function up()
    {
        $this->db->query("CREATE TABLE `tr_summary_daily` (
            `summary_date` date NOT NULL,
            `transaction_type` varchar(50) NOT NULL COMMENT 'Contoh: QRIS, VA, EWALLET, BIFAST, PPOB',
            `ref_merchantId` int(11) NOT NULL,
            `total_qty` int(11) DEFAULT 0,
            `total_amount` decimal(20,2) DEFAULT 0.00,
            `total_fee` decimal(20,2) DEFAULT 0.00,
            `total_fee_ext` decimal(20,2) DEFAULT 0.00,
            `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`summary_date`, `transaction_type`, `ref_merchantId`),
            KEY `idx_merchant_date` (`ref_merchantId`, `summary_date`),
            KEY `idx_type_date` (`transaction_type`, `summary_date`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    }

    public function down()
    {
        $this->dbforge->drop_table('tr_summary_daily');
    }
}
