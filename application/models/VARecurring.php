<?php defined('BASEPATH') OR exit('No direct script access allowed');

class VARecurring extends CI_Model {
    var $table = 'cashin_recurring_va crv';
    var $column_order = array(null, 'crv.c_datetimeRequest', 'm.c_name', 's.c_name', 'crv.c_merchantTransactionId', 'crv.ref_cashinChannelId', 'crv.ref_cashinExternalId', 'crv.c_vaNumber', 'crv.c_amount', 'crv.c_status');
    var $column_search = array('crv.c_vaNumber', 'crv.c_merchantTransactionId', 's.c_name', 'm.c_name');
    var $order = array('crv.id' => 'desc');

    private function _get_datatables_query($search_name = null, $search_date = null, $search_sub = null, $only_ids = false)
    {
        // Emergency 3-second safeguard
        $this->db->query("SET SESSION max_execution_time = 10000");
        
        if ($only_ids) {
            $this->db->select("crv.id");
        } else {
            $this->db->select("crv.*, s.c_name as name_submerchant, m.c_name as name_merchant");
        }
        $this->db->from($this->table);
        
        if (!$only_ids || $_POST['search']['value']) {
            $this->db->join('submerchant s', 's.id = crv.ref_subMerchantId', 'left');
            $this->db->join('merchant m', 'm.id = crv.ref_merchantId', 'left');
        }

        if ($search_name) {
            $this->db->where('crv.ref_merchantId', $search_name);
        }
        if ($search_date) {
            $formatted_date = date('Y-m-d', strtotime($search_date));
            if (!empty($_SESSION['search_date_var_to'])) {
                $formatted_date_to = date('Y-m-d', strtotime($_SESSION['search_date_var_to']));
                $this->db->where("crv.c_datetimeRequest >= '$formatted_date 00:00:00' AND crv.c_datetimeRequest <= '$formatted_date_to 23:59:59'");
            } else {
                $this->db->where("crv.c_datetimeRequest >= '$formatted_date 00:00:00' AND crv.c_datetimeRequest <= '$formatted_date 23:59:59'");
            }
        }
        if ($search_sub) {
            $this->db->where('crv.ref_subMerchantId', $search_sub);
        }

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

    public function get_datatables($search_name = null, $search_date = null, $search_sub = null)
    {
        // STEP 1: Get matching IDs only
        $this->_get_datatables_query($search_name, $search_date, $search_sub, true);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        $id_results = $query->result();
        
        if (empty($id_results)) return array();
        
        $ids = array_column($id_results, 'id');
        
        // STEP 2: Fetch full records for matching IDs
        $this->db->select("crv.*, s.c_name as name_submerchant, m.c_name as name_merchant");
        $this->db->from($this->table);
        $this->db->join('submerchant s', 's.id = crv.ref_subMerchantId', 'left');
        $this->db->join('merchant m', 'm.id = crv.ref_merchantId', 'left');
        
        $this->db->where_in('crv.id', $ids);
        
        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
        
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered($search_name = null, $search_date = null, $search_sub = null)
    {
        $is_filtered = ($search_name || $search_date || $search_sub || (isset($_POST['search']['value']) && !empty($_POST['search']['value'])));
        if (!$is_filtered) {
            return $this->count_all_dt();
        }

        $this->db->select('count(DISTINCT crv.id) as total');
        $this->db->from($this->table);
        
        // Lean joins based on active filters
        if (isset($_POST['search']['value']) && $_POST['search']['value']) {
            $this->db->join('submerchant s', 's.id = crv.ref_subMerchantId', 'left');
            $this->db->join('merchant m', 'm.id = crv.ref_merchantId', 'left');
        }
        
        if ($search_name) {
            $this->db->where('crv.ref_merchantId', $search_name);
        }
        if ($search_date) {
            $formatted_date = date('Y-m-d', strtotime($search_date));
            if (!empty($_SESSION['search_date_var_to'])) {
                $formatted_date_to = date('Y-m-d', strtotime($_SESSION['search_date_var_to']));
                $this->db->where("crv.c_datetimeRequest >= '$formatted_date 00:00:00' AND crv.c_datetimeRequest <= '$formatted_date_to 23:59:59'");
            } else {
                $this->db->where("crv.c_datetimeRequest >= '$formatted_date 00:00:00' AND crv.c_datetimeRequest <= '$formatted_date 23:59:59'");
            }
        }
        if ($search_sub) {
            $this->db->where('crv.ref_subMerchantId', $search_sub);
        }

        if (isset($_POST['search']['value']) && $_POST['search']['value']) {
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
    public function get_summary($search_name = null, $search_date = null, $search_date_to = null, $search_va = null, $search_trxid = null)
    {
        $this->db->select("COUNT(crv.id) as qty, SUM(crv.c_amount) as total_amount");
        $this->db->from($this->table);
        
        if ($search_name) {
            $this->db->join('merchant m', 'crv.ref_merchantId = m.id', 'left');
            $this->db->where('crv.ref_merchantId', $search_name);
        }
        
        if ($search_date) {
            $search_date = date('Y-m-d', strtotime($search_date));
            if ($search_date_to) {
                $search_date_to = date('Y-m-d', strtotime($search_date_to));
                $this->db->where("crv.c_datetimeRequest >= '$search_date 00:00:00'");
                $this->db->where("crv.c_datetimeRequest <= '$search_date_to 23:59:59'");
            } else {
                $this->db->where("crv.c_datetimeRequest >= '$search_date 00:00:00'");
                $this->db->where("crv.c_datetimeRequest <= '$search_date 23:59:59'");
            }
        }

        return $this->db->get()->row();
    }

    public function count_all_dt($search_name = null, $search_date = null, $search_date_to = null)
    {
        $table_name = explode(' ', $this->table)[0];
        $query = $this->db->query("SELECT TABLE_ROWS FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$table_name}'");
        $result = $query->row();
        return $result ? (int)$result->TABLE_ROWS : 0;
    }

    public function get_varecurring($limit, $start, $search_date_var = null, $search_name_var= null, $search_submerchant_var= null) {
        $query = "SELECT crv.*, s.c_name as name_submerchant, merchant.c_name as name_merchant
                 from cashin_recurring_va crv 
                 join submerchant s on s.id = crv.ref_subMerchantId
                 left join merchant on crv.ref_merchantId = merchant.id";

        $query .= " WHERE 1=1 ";

        if ($search_date_var) {
                $formatted_date = date('Y-m-d', strtotime($search_date_var));
                $query .= " AND crv.c_datetimeRequest >= '$formatted_date 00:00:00' AND crv.c_datetimeRequest <= '$formatted_date 23:59:59'";
            }

        if ($search_name_var) {
            $query .= " AND merchant.id = $search_name_var";
        }
        if ($search_submerchant_var) {
            $query .= " AND s.id LIKE $search_submerchant_var";
        }
        $query .= " ORDER BY crv.id DESC
                    LIMIT $start, $limit";

                    // var_dump($query);
        return $this->db->query($query)->result();
    }

    public function count_varecurring($refMerchantId, $search_date_var = null) {
        $this->db->select('count(crv.id) as total');
        $this->db->from('cashin_recurring_va crv');
        $this->db->where('crv.ref_merchantId', $refMerchantId);

        if ($search_date_var) {
            $formatted_date = date('Y-m-d', strtotime($search_date_var));
            $this->db->where("crv.c_datetimeRequest >= '$formatted_date 00:00:00'");
            $this->db->where("crv.c_datetimeRequest <= '$formatted_date 23:59:59'");
        }

        $query = $this->db->get();
        return $query->row()->total;
    }
        
    public function get_merchant(){
            $query = "select id,c_name from merchant ";
            return $this->db->query($query)->result();
        }

    public function getDataVaRecurringChannelExternal($ref_cashinExternalId, $ref_cashinExternalLogVaIdCreate) {
        $TransactionIdExternal1         = null;
        $TransactionIdExternal2         = null;

        $DatetimeRequest                = null;
        $RequestHeader                  = null;
        $RequestBody                    = null;

        $DatetimeResponse               = null;
        $ResponseHeader                 = null;
        $ResponseBody                   = null;

        if ($ref_cashinExternalId == 'ifp') {
            $qtxt1_1    = "SELECT c_orderId, c_transactionId, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_ifp_va_create WHERE id='$ref_cashinExternalLogVaIdCreate'";
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
            $qtxt1_1    = "SELECT c_customId, c_vaNumber, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_gvpay_va_create WHERE id='$ref_cashinExternalLogVaIdCreate'";
            $query1_1   = $this->db->query($qtxt1_1);
            $result1_1  = $query1_1->num_rows() ? $query1_1->row() : false;
            if($result1_1) {
                $TransactionIdExternal1     = $result1_1->c_customId;
                $TransactionIdExternal2     = $result1_1->c_vaNumber;
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
    }
?>