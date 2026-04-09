<?php defined('BASEPATH') or exit('No direct script access allowed');

class Mutation_model extends CI_Model
{
 
    private function _get_datatables_query($id, $search_date_mutation = null, $position = null, $channel = null, $search_date_mutation_to = null)
    {
        $this->db->select("
            mutation.id, 
            mutation.ref_merchantId, 
            mutation.c_datetime, 
            mutation.c_potition,
            IF(mutation.c_potition = 'Credit', cashin.ref_cashinChannelId, cashout.ref_cashoutChannelId) AS channelName,
            IF(mutation.c_potition = 'Credit', mutation.ref_cashinId, mutation.ref_cashoutId) AS refLog,
            IF(mutation.c_potition = 'Credit', cashin.c_Datetime, cashout.c_Datetime) AS timeRefLog,
            IF(mutation.c_potition = 'Credit', cashin.c_description, cashout.c_description) AS description,
            IF(mutation.c_potition = 'Credit', cashin.c_InvoiceNo, cashout.c_InvoiceNo) AS refNoLog,
            mutation.c_amount,
            mutation.c_BalanceAfter 
        ", FALSE);
        $this->db->from('mutation');
        $this->db->join('cashin', 'cashin.ref_merchantId = mutation.ref_merchantId AND cashin.id = mutation.ref_cashinId', 'left');
        $this->db->join('cashout', 'cashout.ref_merchantId = mutation.ref_merchantId AND cashout.Id = mutation.ref_cashoutId', 'left');
        $this->db->where('mutation.ref_merchantId', $id);

        if ($search_date_mutation && $search_date_mutation_to) {
            $this->db->where('DATE(mutation.c_datetime) >=', date('Y-m-d', strtotime($search_date_mutation)));
            $this->db->where('DATE(mutation.c_datetime) <=', date('Y-m-d', strtotime($search_date_mutation_to)));
        } elseif ($search_date_mutation) {
            $this->db->where('DATE(mutation.c_datetime)', date('Y-m-d', strtotime($search_date_mutation)));
        }

        if (!empty($position)) {
            $this->db->where('mutation.c_potition', $position);
        }

        if (!empty($channel) && !empty($position)) {
            if ($position === 'Credit') {
                $this->db->where('cashin.ref_cashinChannelId', $channel);
            } elseif ($position === 'Debit') {
                $this->db->where('cashout.ref_cashoutChannelId', $channel);
            }
        }

        // Standard DataTables Search
        if (isset($_POST['search']['value']) && $_POST['search']['value'] != "") {
            $search = $_POST['search']['value'];
            $this->db->group_start();
            $this->db->like('mutation.id', $search);
            $this->db->or_like('mutation.c_potition', $search);
            $this->db->group_end();
        }

        // Standard DataTables Order
        if (isset($_POST['order'])) {
            $column_order = [null, 'mutation.id', 'mutation.c_datetime', 'mutation.c_potition', 'channelName', 'description', 'mutation.c_amount', 'mutation.c_BalanceAfter'];
            $this->db->order_by($column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else {
            $this->db->order_by('mutation.id', 'DESC');
        }
    }

    public function get_datatables($id, $search_date_mutation = null, $position = null, $channel = null, $search_date_mutation_to = null)
    {
        $this->_get_datatables_query($id, $search_date_mutation, $position, $channel, $search_date_mutation_to);
        if ($_POST['length'] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        return $this->db->get()->result();
    }

    public function count_filtered($id, $search_date_mutation = null, $position = null, $channel = null, $search_date_mutation_to = null)
    {
        $this->_get_datatables_query($id, $search_date_mutation, $position, $channel, $search_date_mutation_to);
        return $this->db->get()->num_rows();
    }

    public function count_all_dt($id)
    {
        $this->db->from('mutation');
        $this->db->where('ref_merchantId', $id);
        return $this->db->count_all_results();
    }

    public function get_mutations($limit, $start, $id, $search_date_mutation = null, $position = null, $channel = null)
    {
        // Legacy method for non-AJAX pages if any
        $this->_get_datatables_query($id, $search_date_mutation, $position, $channel);
        $this->db->limit($limit, $start);
        return $this->db->get()->result();
    }

    public function get_merchant($id)
    {
        return $this->db->where('id', $id)->get('merchant')->result();
    }

    public function count_mutations($refMerchantId, $search_date_mutation = null, $search_potition = null)
    {
        // Legacy method
        $this->db->from('mutation');
        $this->db->where('ref_merchantId', $refMerchantId);
        if ($search_date_mutation) {
            $this->db->where('DATE(c_datetime)', date('Y-m-d', strtotime($search_date_mutation)));
        }
        if ($search_potition) {
            $this->db->where('c_potition', $search_potition);
        }
        return $this->db->count_all_results();
    }


     public function get_cashin_channels($merchantId)
    {
        return array_column(
            $this->db->select('DISTINCT(ref_cashinChannelId) AS channel')
                ->from('cashin')
                ->where('ref_merchantId', $merchantId)
                ->get()
                ->result_array(),
            'channel'
        );
    }

    public function get_cashout_channels($merchantId)
    {
        return array_column(
            $this->db->select('DISTINCT(ref_cashoutChannelId) AS channel')
                ->from('cashout')
                ->where('ref_merchantId', $merchantId)
                ->get()
                ->result_array(),
            'channel'
        );
    }

    public function get_summary($id, $search_date_mutation = null, $search_date_mutation_to = null)
    {
        $this->db->select("
            SUM(CASE WHEN c_potition = 'Credit' THEN c_amount ELSE 0 END) as total_credit,
            SUM(CASE WHEN c_potition = 'Debit' THEN c_amount ELSE 0 END) as total_debit,
            COUNT(*) as total_count
        ");
        $this->db->from('mutation');
        $this->db->where('ref_merchantId', $id);

        if ($search_date_mutation && $search_date_mutation_to) {
            $this->db->where('DATE(c_datetime) >=', date('Y-m-d', strtotime($search_date_mutation)));
            $this->db->where('DATE(c_datetime) <=', date('Y-m-d', strtotime($search_date_mutation_to)));
        } elseif ($search_date_mutation) {
            $this->db->where('DATE(c_datetime)', date('Y-m-d', strtotime($search_date_mutation)));
        }
        return $this->db->get()->row();
    }
}
?>