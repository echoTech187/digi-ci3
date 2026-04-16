<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_DatetimePayment_Index extends CI_Migration
{
    public function up()
    {
        // Add indexes to accelerate Dashboard analytics and mutations.
        // The c_datetimePayment fields were heavily scanned (full table scan) over 23M rows.
        
        $this->db->query("CREATE INDEX idx_qris_datetimepayment ON cashin_payment_qris_mpm (c_datetimePayment)");
        $this->db->query("CREATE INDEX idx_va_datetimepayment ON cashin_payment_va (c_datetimePayment)");
        $this->db->query("CREATE INDEX idx_ewallet_datetimepayment ON cashin_payment_ewallet (c_datetimePayment)");
        $this->db->query("CREATE INDEX idx_bifast_status_datetime ON cashout_payment_bifast (c_status, c_datetime)");

        // 1. Mutation Table Indexes
        $this->db->query("CREATE INDEX idx_mutation_merchant ON mutation (ref_merchantId)");
        $this->db->query("CREATE INDEX idx_mutation_datetime ON mutation (c_datetime)");

        // 2. Transaction Payment Indexes (QRIS, VA, E-Wallet)
        $this->db->query("CREATE INDEX idx_qris_merchant ON cashin_payment_qris_mpm (ref_merchantId)");
        $this->db->query("CREATE INDEX idx_qris_submerchant ON cashin_payment_qris_mpm (ref_subMerchantId)");
        $this->db->query("CREATE INDEX idx_qris_datetime ON cashin_payment_qris_mpm (c_datetime)");
        $this->db->query("CREATE INDEX idx_qris_settlement ON cashin_payment_qris_mpm (c_isSettlementRealtime)");

        $this->db->query("CREATE INDEX idx_va_merchant ON cashin_payment_va (ref_merchantId)");
        $this->db->query("CREATE INDEX idx_va_submerchant ON cashin_payment_va (ref_subMerchantId)");
        $this->db->query("CREATE INDEX idx_va_datetime ON cashin_payment_va (c_datetime)");
        $this->db->query("CREATE INDEX idx_va_settlement ON cashin_payment_va (c_isSettlementRealtime)");

        $this->db->query("CREATE INDEX idx_ewallet_merchant ON cashin_payment_ewallet (ref_merchantId)");
        $this->db->query("CREATE INDEX idx_ewallet_submerchant ON cashin_payment_ewallet (ref_subMerchantId)");
        $this->db->query("CREATE INDEX idx_ewallet_datetime ON cashin_payment_ewallet (c_datetime)");
        $this->db->query("CREATE INDEX idx_ewallet_settlement ON cashin_payment_ewallet (c_isSettlementRealtime)");

        // 3. Cashin & Cashout Indexes
        $this->db->query("CREATE INDEX idx_cashin_merchant ON cashin (ref_merchantId)");
        $this->db->query("CREATE INDEX idx_cashout_merchant ON cashout (ref_merchantId)");

        // 4. User Management Indexes
        $this->db->query("CREATE INDEX idx_submerchant_merchant ON submerchant (ref_merchantId)");
        $this->db->query("CREATE INDEX idx_merchant_status ON merchant (c_status)");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE cashin_payment_qris_mpm DROP INDEX idx_qris_datetimepayment");
        $this->db->query("ALTER TABLE cashin_payment_va DROP INDEX idx_va_datetimepayment");
        $this->db->query("ALTER TABLE cashin_payment_ewallet DROP INDEX idx_ewallet_datetimepayment");
        $this->db->query("ALTER TABLE cashout_payment_bifast DROP INDEX idx_bifast_status_datetime");

        // Rollback Mutation Indexes
        $this->db->query("ALTER TABLE mutation DROP INDEX idx_mutation_merchant");
        $this->db->query("ALTER TABLE mutation DROP INDEX idx_mutation_datetime");

        // Rollback Transaction Payment Indexes
        $this->db->query("ALTER TABLE cashin_payment_qris_mpm DROP INDEX idx_qris_merchant");
        $this->db->query("ALTER TABLE cashin_payment_qris_mpm DROP INDEX idx_qris_submerchant");
        $this->db->query("ALTER TABLE cashin_payment_qris_mpm DROP INDEX idx_qris_datetime");
        $this->db->query("ALTER TABLE cashin_payment_qris_mpm DROP INDEX idx_qris_settlement");

        $this->db->query("ALTER TABLE cashin_payment_va DROP INDEX idx_va_merchant");
        $this->db->query("ALTER TABLE cashin_payment_va DROP INDEX idx_va_submerchant");
        $this->db->query("ALTER TABLE cashin_payment_va DROP INDEX idx_va_datetime");
        $this->db->query("ALTER TABLE cashin_payment_va DROP INDEX idx_va_settlement");

        $this->db->query("ALTER TABLE cashin_payment_ewallet DROP INDEX idx_ewallet_merchant");
        $this->db->query("ALTER TABLE cashin_payment_ewallet DROP INDEX idx_ewallet_submerchant");
        $this->db->query("ALTER TABLE cashin_payment_ewallet DROP INDEX idx_ewallet_datetime");
        $this->db->query("ALTER TABLE cashin_payment_ewallet DROP INDEX idx_ewallet_settlement");

        // Rollback Cashin & Cashout Indexes
        $this->db->query("ALTER TABLE cashin DROP INDEX idx_cashin_merchant");
        $this->db->query("ALTER TABLE cashout DROP INDEX idx_cashout_merchant");

        // Rollback User Management Indexes
        $this->db->query("ALTER TABLE submerchant DROP INDEX idx_submerchant_merchant");
        $this->db->query("ALTER TABLE merchant DROP INDEX idx_merchant_status");
    }
}
