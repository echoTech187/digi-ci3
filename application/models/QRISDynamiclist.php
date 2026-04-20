<?php defined('BASEPATH') or exit('No direct script access allowed');

class QRISDynamiclist extends CI_Model
{
    public function get_qris_dynamic_data($start, $length, $search = null, $orderColumn = null, $orderDir = 'DESC')
    {
        // Emergency 3-second safeguard
        $this->db->query("SET SESSION max_execution_time = 10000");
        
        $this->db->select('cashin_dynamic_qris_mpm.*, submerchant.c_name as name_submerchant, merchant.c_name as name_merchant');
        $this->db->from('cashin_dynamic_qris_mpm');
        $this->db->join('submerchant', 'cashin_dynamic_qris_mpm.ref_subMerchantId = submerchant.id', 'left');
        $this->db->join('merchant', 'cashin_dynamic_qris_mpm.ref_merchantId = merchant.id', 'left');

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('cashin_dynamic_qris_mpm.c_datetimeRequest', $search);
            $this->db->or_like('cashin_dynamic_qris_mpm.c_merchantTransactionId', $search);
            $this->db->or_like('submerchant.c_name', $search);
            $this->db->or_like('merchant.c_name', $search);
            $this->db->or_like('cashin_dynamic_qris_mpm.c_referenceNo', $search);
            $this->db->or_like('cashin_dynamic_qris_mpm.c_status', $search);
            $this->db->or_like('cashin_dynamic_qris_mpm.c_amount', $search);
            $this->db->group_end();
        }

        if (!empty($orderColumn)) {
            $columns = ['cashin_dynamic_qris_mpm.id', 'cashin_dynamic_qris_mpm.c_datetimeRequest', 'merchant.c_name', 'submerchant.c_name', 'cashin_dynamic_qris_mpm.c_merchantTransactionId','cashin_dynamic_qris_mpm.c_referenceNo', 'cashin_dynamic_qris_mpm.ref_cashinExternalId', 'cashin_dynamic_qris_mpm.c_amount', 'cashin_dynamic_qris_mpm.c_datetimeExpired', 'cashin_dynamic_qris_mpm.c_status'];
            $this->db->order_by($columns[$orderColumn], $orderDir);
        } else {
            $this->db->order_by('cashin_dynamic_qris_mpm.id', 'DESC');
        }

        $this->db->limit($length, $start);
        return $this->db->get()->result();
    }

    public function count_all_qris_dynamic()
    {
        $query = $this->db->query("SELECT TABLE_ROWS FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'cashin_dynamic_qris_mpm'");
        $result = $query->row();
        return $result ? (int)$result->TABLE_ROWS : 0;
    }

    public function count_filtered_qris_dynamic($search = null)
    {
        $is_filtered = !empty($search);
        if (!$is_filtered) {
            return $this->count_all_qris_dynamic();
        }

        $this->db->select('count(cashin_dynamic_qris_mpm.id) as total');
        $this->db->from('cashin_dynamic_qris_mpm');
        
        if (!empty($search)) {
            $this->db->join('submerchant', 'cashin_dynamic_qris_mpm.ref_subMerchantId = submerchant.id', 'left');
            $this->db->join('merchant', 'cashin_dynamic_qris_mpm.ref_merchantId = merchant.id', 'left');

            $this->db->group_start();
            $this->db->like('cashin_dynamic_qris_mpm.c_datetimeRequest', $search);
            $this->db->or_like('cashin_dynamic_qris_mpm.c_merchantTransactionId', $search);
            $this->db->or_like('submerchant.c_name', $search);
            $this->db->or_like('merchant.c_name', $search);
            $this->db->or_like('cashin_dynamic_qris_mpm.c_referenceNo', $search);
            $this->db->or_like('cashin_dynamic_qris_mpm.c_status', $search);
            $this->db->or_like('cashin_dynamic_qris_mpm.c_amount', $search);
            $this->db->group_end();
        }
        
        $query = $this->db->get();
        return $query->row()->total;
    }
}
