<?php defined('BASEPATH') or exit('No direct script access allowed');

class EwalletDynamic extends CI_Model
{
    var $table = 'cashin_dynamic_ewallet cde';
    var $column_order = array(null, 'cde.c_datetimeRequest', 's.c_name', 'cde.ref_cashinChannelId', 'cde.c_merchantTransactionId', 'cde.ref_cashinExternalId', 'cde.c_amount', 'cde.c_datetimeExpired', 'cde.c_status', null);
    var $column_search = array('cde.c_merchantTransactionId', 'cde.ref_merchantId', 'cde.ref_subMerchantId', 's.c_name', 'm.c_name');
    var $order = array('cde.id' => 'desc');

    private function _apply_filters($search_name = null, $search_date = null, $search_date_to = null, $search_transid = null, $search_status = null)
    {
        if ($search_name) {
            $this->db->where('cde.ref_merchantId', $search_name);
        }
        if ($search_date) {
            $this->db->where('cde.c_datetimeRequest >=', date('Y-m-d', strtotime($search_date)) . ' 00:00:00');
        }
        if ($search_date_to) {
            $this->db->where('cde.c_datetimeRequest <=', date('Y-m-d', strtotime($search_date_to)) . ' 23:59:59');
        }
        if ($search_transid) {
            $this->db->where('cde.c_merchantTransactionId', $search_transid);
        }
        if ($search_status) {
            $this->db->where('cde.c_status', $search_status);
        }
    }

    private function _get_datatables_query($search_name = null, $search_date = null, $search_date_to = null, $search_transid = null, $search_status = null, $only_ids = false)
    {
        // Emergency 3-second safeguard
        $this->db->query("SET SESSION max_execution_time = 10000");
        
        if ($only_ids) {
            $this->db->select("cde.id");
        } else {
            $this->db->select("cde.*, s.c_name as name_submerchant, m.c_name as name_merchant");
        }
        $this->db->from($this->table);
        
        if (!$only_ids || $_POST['search']['value']) {
            $this->db->join('submerchant s', 'cde.ref_subMerchantId = s.id', 'left');
            $this->db->join('merchant m', 'cde.ref_merchantId = m.id', 'left');
        }

        $this->_apply_filters($search_name, $search_date, $search_date_to, $search_transid, $search_status);

        $i = 0;
        foreach ($this->column_search as $item) {
            if (isset($_POST['search']['value']) && $_POST['search']['value']) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
                if (count($this->column_search) - 1 == $i)
                    $this->db->group_end();
            }
            $i++;
        }

        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function get_datatables($search_name = null, $search_date = null, $search_date_to = null, $search_transid = null, $search_status = null)
    {
        // STEP 1: Get matching IDs only (Fast)
        $this->_get_datatables_query($search_name, $search_date, $search_date_to, $search_transid, $search_status, true);
        if (isset($_POST['length']) && $_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        $id_results = $query->result();
        
        if (empty($id_results)) return array();
        
        $ids = array_column($id_results, 'id');
        
        // STEP 2: Fetch full details for those IDs
        $this->db->select("cde.*, s.c_name as name_submerchant, m.c_name as name_merchant");
        $this->db->from($this->table);
        $this->db->join('submerchant s', 'cde.ref_subMerchantId = s.id', 'left');
        $this->db->join('merchant m', 'cde.ref_merchantId = m.id', 'left');
        
        $this->db->where_in('cde.id', $ids);
        
        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
        
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered($search_name = null, $search_date = null, $search_date_to = null, $search_transid = null, $search_status = null)
    {
        $is_filtered = ($search_name || $search_date || $search_date_to || $search_transid || $search_status || (isset($_POST['search']['value']) && !empty($_POST['search']['value'])));
        if (!$is_filtered) {
            return $this->count_all_dt();
        }

        $this->db->select('count(DISTINCT cde.id) as total');
        $this->db->from($this->table);
        $this->_apply_filters($search_name, $search_date, $search_date_to, $search_transid, $search_status);

        // Lean joins for global search
        if (isset($_POST['search']['value']) && $_POST['search']['value']) {
            $this->db->join('submerchant s', 'cde.ref_subMerchantId = s.id', 'left');
            $this->db->join('merchant m', 'cde.ref_merchantId = m.id', 'left');
            
            $i = 0;
            foreach ($this->column_search as $item) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
                if (count($this->column_search) - 1 == $i)
                    $this->db->group_end();
                $i++;
            }
        }

        $query = $this->db->get();
        return $query->row()->total;
    }

    public function count_all_dt($search_name = null, $search_date = null, $search_date_to = null)
    {
        $table_name = explode(' ', $this->table)[0];
        $query = $this->db->query("SELECT TABLE_ROWS FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$table_name}'");
        $result = $query->row();
        return $result ? (int)$result->TABLE_ROWS : 0;
    }

    public function get_summary($search_name = null, $search_date = null, $search_date_to = null, $search_transid = null, $search_status = null)
    {
        $this->db->select("COUNT(*) as qty, SUM(c_amount) as total_trx");
        $this->db->from($this->table);
        $this->_apply_filters($search_name, $search_date, $search_date_to, $search_transid, $search_status);
        return $this->db->get()->row();
    }



    public function get_merchant()
    {
        $query = "select id,c_name from merchant ";
        return $this->db->query($query)->result();
    }

    public function getDataEwalletDynamicChannelExternal($ref_cashinExternalId, $ref_cashinExternalLogEwalletIdCreate) {
        
        $TransactionIdExternal1         = null;
        $TransactionIdExternal2         = null;

        $DatetimeRequest                = null;
        $RequestHeader                  = null;
        $RequestBody                    = null;

        $DatetimeResponse               = null;
        $ResponseHeader                 = null;
        $ResponseBody                   = null;

        $ref_cashinExternalId = strtolower($ref_cashinExternalId);

        if ($ref_cashinExternalId == 'ifp') {

            $qtxt1_1    = "SELECT c_orderId, c_transactionId, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_ifp_ewallet_create WHERE id='$ref_cashinExternalLogEwalletIdCreate'";
            $query1_1   = $this->db->query($qtxt1_1);
            $result1_1  = $query1_1->num_rows() ? $query1_1->row() : false;
            if($result1_1) {

                $TransactionIdExternal1     = $result1_1->c_orderId;
                $TransactionIdExternal2     = $result1_1->c_transactionId;
                
                $DatetimeRequest            = $result1_1->c_datetimeRequest;
                $RequestHeader              = $result1_1->c_requestHeader;
                $RequestBody                = $result1_1->c_requestBody;

                $DatetimeResponse           = $result1_1->c_datetimeResponse;
                $ResponseHeader             = $result1_1->c_responseHeader;
                $ResponseBody               = $result1_1->c_responseBody;

            }

        } elseif ($ref_cashinExternalId == 'gvpay' || $ref_cashinExternalId == 'gvconnect') {

            $qtxt1_1    = "SELECT c_customId, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_gvpay_ewallet_create WHERE id='$ref_cashinExternalLogEwalletIdCreate'";
            $query1_1   = $this->db->query($qtxt1_1);
            $result1_1  = $query1_1->num_rows() ? $query1_1->row() : false;
            if($result1_1) {

                $TransactionIdExternal1     = $result1_1->c_customId;
                $TransactionIdExternal2     = '-';
                
                $DatetimeRequest            = $result1_1->c_datetimeRequest;
                $RequestHeader              = $result1_1->c_requestHeader;
                $RequestBody                = $result1_1->c_requestBody;

                $DatetimeResponse           = $result1_1->c_datetimeResponse;
                $ResponseHeader             = $result1_1->c_responseHeader;
                $ResponseBody               = $result1_1->c_responseBody;

            }

        }

        return array(
                    'TransactionIdExternal1'    => $TransactionIdExternal1, 
                    'TransactionIdExternal2'    => $TransactionIdExternal2, 
                    'RequestDatetime'           => $DatetimeRequest, 
                    'RequestHeader'             => json_decode($RequestHeader, true),
                    'RequestBody'               => json_decode($RequestBody, true),
                    'ResponseDatetime'          => $DatetimeResponse,
                    'ResponseHeader'            => json_decode($ResponseHeader, true),
                    'ResponseBody'              => json_decode($ResponseBody, true)
                );
    }

    public function get_datatables_handler($filters = [])
    {
        $search_name = $filters['merchant'] ?? null;
        $search_date = $filters['date'] ?? null;
        $search_date_to = $filters['date_to'] ?? null;
        $search_transid = $filters['transid'] ?? null;
        $search_status = $filters['status'] ?? null;

        $list = $this->get_datatables($search_name, $search_date, $search_date_to, $search_transid, $search_status);
        
        $data = [];
        $no = intval($this->input->post('start'));
        foreach ($list as $items) {
            $no++;
            $row = (array)$items;
            $row['no'] = $no;
            $data[] = $row;
        }

        $recordsTotal = $this->count_all_dt($search_name, $search_date, $search_date_to);
        
        // Consistency: Use approx count if no filters, exact if filtered
        $is_filtered = $search_name || $search_date || $search_date_to || $search_transid || $search_status || (!empty($this->input->post('search')['value']));
        $recordsFiltered = $is_filtered ? $this->count_filtered($search_name, $search_date, $search_date_to, $search_transid, $search_status) : $recordsTotal;

        $output = [
            "draw" => intval($this->input->post("draw")),
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data,
        ];

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($output));
    }
}
?>