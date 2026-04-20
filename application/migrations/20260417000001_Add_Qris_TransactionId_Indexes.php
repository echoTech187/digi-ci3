<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_Qris_TransactionId_Indexes extends CI_Migration
{
    public function up()
    {
        // Add single-column indexes for Merchant Transaction ID lookups.
        // These filters are executed without ref_merchantId, so the existing
        // compound indexes are not usable for exact lookup by c_merchantTransactionId alone.
        $this->db->query("CREATE INDEX idx_cashin_dynamic_qris_mpm_merchantTransactionId ON cashin_dynamic_qris_mpm (c_merchantTransactionId)");
        $this->db->query("CREATE INDEX idx_cashin_recurring_qris_mpm_merchantTransactionId ON cashin_recurring_qris_mpm (c_merchantTransactionId)");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE cashin_dynamic_qris_mpm DROP INDEX idx_cashin_dynamic_qris_mpm_merchantTransactionId");
        $this->db->query("ALTER TABLE cashin_recurring_qris_mpm DROP INDEX idx_cashin_recurring_qris_mpm_merchantTransactionId");
    }
}
