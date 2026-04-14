<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Chanel extends CI_Model {
    var $table = 'cashin_channel cc';
    var $column_order = array(null, 'cc.id', 'cc.c_channelGroup', 'cc.c_description', 'cc.c_externalIdDefault', 'cc.c_feeType', 'cc.c_fee', null);
    var $column_search = array('cc.id', 'cc.c_channelGroup', 'cc.c_description', 'cc.c_externalIdDefault');
    var $order = array('cc.id' => 'asc');

    private function _get_datatables_query($table, $column_order, $column_search, $order, $where = [])
    {
        $this->db->from($table);
        
        if (!empty($where)) {
            $this->db->where($where);
        }

        $i = 0;
        foreach ($column_search as $item) {
            if ($_POST['search']['value']) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
                if (count($column_search) - 1 == $i)
                    $this->db->group_end();
            }
            $i++;
        }

        if (isset($_POST['order'])) {
            $this->db->order_by($column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($order)) {
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function get_datatables($table, $column_order, $column_search, $order, $where = [])
    {
        $this->_get_datatables_query($table, $column_order, $column_search, $order, $where);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered($table, $column_order, $column_search, $order, $where = [])
    {
        $this->db->select('count(id) as total');
        $this->_get_datatables_query($table, $column_order, $column_search, $order, $where);
        $query = $this->db->get();
        return $query->row()->total;
    }

    public function count_all_dt($table, $where = [])
    {
        $this->db->select('count(id) as total');
        $this->db->from($table);
        if (!empty($where)) {
            $this->db->where($where);
        }
        $query = $this->db->get();
        return $query->row()->total;
    }

        public function get_pulsa_reguler($limit, $start, $provider = null) {
            $this->db->limit($limit, $start);
            $this->db->from('cashout_channel');
            $this->db->where('c_channelGroup', 'ppob');
            $this->db->like('c_channelGroup2', 'pulsa');
            if ($provider) {
                $this->db->like('c_channelGroup2', $provider);
            }
            return $this->db->get()->result();
        }  

        public function get_paket_data($limit, $start, $provider = null) {
            $this->db->limit($limit, $start);
            $this->db->from('cashout_channel');
            $this->db->where('c_channelGroup', 'ppob');
            $this->db->like('c_channelGroup2', 'paket_data');
            if ($provider) {
                $this->db->like('c_channelGroup2', $provider);
            }
            return $this->db->get()->result();
        }  
        public function get_token_listrik($limit, $start){
            $query = "select * from cashout_channel cc where c_channelGroup2 = 'token_pln' order by c_fee asc  ";
            return $this->db->query($query)->result();
        }      

        public function get_topup_gopay($limit, $start){
            $query = "select * from cashout_channel cc where c_channelGroup2 = 'topup_gopay' order by c_fee asc  ";
            return $this->db->query($query)->result();
        }      

        public function get_topup_dana($limit, $start){
            $query = "select * from cashout_channel cc where c_channelGroup2 = 'topup_dana' order by c_fee asc  ";
            return $this->db->query($query)->result();
        }    

        public function get_topup_ovo($limit, $start){
            $query = "select * from cashout_channel cc where c_channelGroup2 = 'topup_ovo' order by c_fee asc  ";
            return $this->db->query($query)->result();
        }      
        public function insert_cashout_chanel($data) {
            return $this->db->insert('cashout_channel', $data);
        }
        public function get_mobile_legend($limit, $start){
            $query = "select * from cashout_channel cc where c_channelGroup2 = 'diamond_mlbb' order by c_fee asc  ";
            return $this->db->query($query)->result();
        }   
        public function get_pubg_mobile($limit, $start){
            $query = "select * from cashout_channel cc where c_channelGroup2 = 'pubg_mobile' order by c_fee asc  ";
            return $this->db->query($query)->result();
        }   
        public function get_free_fire($limit, $start){
            $query = "select * from cashout_channel cc where c_channelGroup2 = 'free_fire' order by c_fee_asc  ";
            return $this->db->query($query)->result();
        }   
        public function get_hago($limit, $start){
            $query = "select * from cashout_channel cc where c_channelGroup2 = 'hago' order by c_fee asc  ";
            return $this->db->query($query)->result();
        }   
        public function get_google_play($limit, $start){
            $query = "select * from cashout_channel cc where c_channelGroup2 = 'google_play' order by c_fee asc  ";
            return $this->db->query($query)->result();
        }   
        public function get_cashin_chanel($limit, $start){
            $query = "SELECT * FROM cashin_channel LIMIT ?, ?";
            return $this->db->query($query, array((int)$start, (int)$limit))->result();
        } 
        public function get_cashin_chanel_group(){
            $query = "SELECT c_channelGroup FROM cashin_channel GROUP BY c_channelGroup";
            return $this->db->query($query)->result();
        }
        public function get_cashin_chanel_id(){
            $query = "SELECT id FROM cashin_channel GROUP BY id";
            return $this->db->query($query)->result();
        }
        public function get_cashin_chanel_external_id_default(){
            $query = "SELECT c_externalIdDefault FROM cashin_channel GROUP BY c_externalIdDefault";
            return $this->db->query($query)->result();
        }
        public function get_cashout_chanel($limit, $start){
            $query = "SELECT * FROM cashout_channel WHERE c_channelGroup != 'ppob' LIMIT ?, ?";
            return $this->db->query($query, array((int)$start, (int)$limit))->result();
        } 
        public function get_cashout_chanel_group(){
            $query = "SELECT c_channelGroup FROM cashout_channel GROUP BY c_channelGroup";
            return $this->db->query($query)->result();
        }
        public function get_cashout_chanel_id(){
            $query = "SELECT id FROM cashout_channel GROUP BY id";
            return $this->db->query($query)->result();
        }
        public function get_cashout_chanel_external_id_default(){
            $query = "SELECT c_externalIdDefault FROM cashout_channel GROUP BY c_externalIdDefault";
            return $this->db->query($query)->result();
        }
        public function get_cashout_channels($externalId, $channelGroup){
            $this->db->from('cashout_channel');
            $this->db->where('c_externalIdDefault', $externalId);
            $this->db->where('c_channelGroup', $channelGroup);
            return $this->db->get()->result();
        }

        public function get_cashout_channels_all(){
            $this->db->from('cashout_channel');
            $this->db->where('c_channelGroup !=', 'ppob');
            return $this->db->get()->result();
        }
        public function get_cashin_channels($externalId, $channelGroup){
            $this->db->from('cashin_channel');
            $this->db->where('c_externalIdDefault', $externalId);
            $this->db->where('c_channelGroup', $channelGroup);
            return $this->db->get()->result();
        }
        public function createCashinChannel($data) {
            return $this->db->insert('cashin_channel', $data);
        }
        public function createCashoutChannel($data) {
            return $this->db->insert('cashout_channel', $data);
        }
        public function insertPaketData($data) {
            return $this->db->insert('cashout_channel', $data);
        }
        public function createCashinChannelXMerchant($data) {
            if ($this->db->insert('cashin_channel_x_merchant', $data)) {
                return true;
            } else {
                return $this->db->error(); // Returns array with 'code' and 'message'
            }
        }

        public function updateCashinChannelXMerchant($id, $data) {
            $this->db->where('id', $id);
            if ($this->db->update('cashin_channel_x_merchant', $data)) {
                return true;
            } else {
                return $this->db->error(); // Returns array with 'code' and 'message'
            }
        }

        public function deleteCashinChannelXMerchant($id) {
            $this->db->where('id', $id);
            if ($this->db->delete('cashin_channel_x_merchant')) {
                return true;
            } else {
                return $this->db->error(); // Returns array with 'code' and 'message'
            }
        }

        public function createCashoutChannelXMerchant($data) {
            if ($this->db->insert('cashout_channel_x_merchant', $data)) {
                return true;
            } else {
                return $this->db->error(); // Returns array with 'code' and 'message'
            }
        }

        public function bulkCreateCashoutChannelXMerchant($data) {
            $this->db->trans_begin();
            foreach ($data as $row) {
                $resp = $this->db->insert('cashout_channel_x_merchant', $row);

                if (!$resp) {
                    $error = $this->db->error();

                    if ($error['code'] == 1062) {
                        continue;
                    }

                    $this->db->trans_rollback();
                    return [
                        'code' => $error['code'],
                        'message' => $error['message'],
                        'row_data' => $row,
                        'last_query' => $this->db->last_query(),
                        'resp' => $resp,
                    ];
                }
            }

            $this->db->trans_commit();
            return true;
        }

        public function updateCashoutChannelXMerchant($id, $data) {
            $this->db->where('id', $id);
            if ($this->db->update('cashout_channel_x_merchant', $data)) {
                return true;
            } else {
                return $this->db->error(); // Returns array with 'code' and 'message'
            }
        }

        public function deleteCashoutChannelXMerchant($id) {
            $this->db->where('id', $id);
            if ($this->db->delete('cashout_channel_x_merchant')) {
                return true;
            } else {
                return $this->db->error(); // Returns array with 'code' and 'message'
            }
        }

        public function bulkCreateCashinChannelXMerchant($data) {
            $this->db->trans_begin();
            foreach ($data as $row) {
                $resp = $this->db->insert('cashin_channel_x_merchant', $row);

                if (!$resp) {
                    $error = $this->db->error();

                    if ($error['code'] == 1062) {
                        continue;
                    }

                    $this->db->trans_rollback();
                    return [
                        'code' => $error['code'],
                        'message' => $error['message'],
                        'row_data' => $row,
                        'last_query' => $this->db->last_query(),
                        'resp' => $resp,
                    ];
                }
            }

            $this->db->trans_commit();
            return true;
        }

        public function get_cashin_summary() {
            $this->db->select("
                COUNT(*) as qty,
                COUNT(DISTINCT c_channelGroup) as total_groups,
                COUNT(DISTINCT c_externalIdDefault) as providers,
                AVG(c_fee) as avg_fee
            ");
            $this->db->from("cashin_channel");
            return $this->db->get()->row();
        }

        public function get_cashout_summary() {
            $this->db->select("
                COUNT(*) as qty,
                COUNT(DISTINCT c_channelGroup) as total_groups,
                COUNT(DISTINCT c_externalIdDefault) as providers,
                AVG(c_fee) as avg_fee
            ");
            $this->db->from("cashout_channel");
            $this->db->where("c_channelGroup !=", "ppob");
            return $this->db->get()->row();
        }
    }
?>