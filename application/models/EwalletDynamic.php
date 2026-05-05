<?php defined('BASEPATH') or exit('No direct script access allowed');

class EwalletDynamic extends CI_Model
{
    var $table = 'cashin_dynamic_ewallet cde';
    var $column_order = array(null, 'cde.c_datetimeRequest', 's.c_name', 'cde.ref_cashinChannelId', 'cde.c_merchantTransactionId', 'cde.ref_cashinExternalId', 'cde.c_amount', 'cde.c_datetimeExpired', 'cde.c_status', null);
    var $column_search = array('cde.c_merchantTransactionId', 'cde.ref_merchantId', 'cde.ref_subMerchantId', 's.c_name', 'm.c_name');
    var $order = array('cde.id' => 'desc');
    
    // Request-level caching to prevent redundant pre-lookups
    private static $cached_ids = null;
    private static $cached_total = null;
    private static $cached_inv_ids = null;

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
        // Emergency safeguard
        $this->db->query("SET SESSION max_execution_time = 30000");
        
        if ($only_ids) {
            $this->db->select("cde.id");
        } else {
            $this->db->select("cde.id, cde.c_datetimeRequest, cde.ref_cashinChannelId, cde.c_merchantTransactionId, cde.ref_cashinExternalId, cde.c_amount, cde.c_datetimeExpired, cde.c_status, cde.ref_merchantId, cde.ref_subMerchantId, s.c_name as name_submerchant, m.c_name as name_merchant");
        }
        $this->db->from($this->table);
        
        if (!$only_ids || $_POST['search']['value']) {
            $this->db->join('submerchant s', 'cde.ref_subMerchantId = s.id', 'left');
            $this->db->join('merchant m', 'cde.ref_merchantId = m.id', 'left');
        }

        $this->_apply_filters($search_name, $search_date, $search_date_to, $search_transid, $search_status);

        $searchValue = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';
        if ($searchValue) {
            $safeSearch = $this->db->escape_str($searchValue);
            
            if (self::$cached_ids === null) {
                // 1. Always try finding ID matches first (Fast Indexed Lookup)
                $matching_ids = [-1];
                
                // Check in technical ID columns
                $res = $this->db->query("SELECT id FROM cashin_dynamic_ewallet WHERE c_merchantTransactionId LIKE '$safeSearch%' LIMIT 100")->result();
                if (!empty($res)) $matching_ids = array_merge($matching_ids, array_column($res, 'id'));
                
                if (is_numeric($searchValue) && strlen($searchValue) < 15) {
                    $matching_ids[] = (int)$searchValue;
                }

                self::$cached_ids = array_unique($matching_ids);
            }
            $matching_ids = self::$cached_ids;

            // 2. Decide strategy: If IDs found, use them. If not, search by Name.
            if (count($matching_ids) > 1) {
                $this->db->where_in('cde.id', $matching_ids);
            } else {
                // FALLBACK: Name search if no specific ID matched (min 3 chars)
                if (strlen($searchValue) >= 3) {
                    $this->db->group_start();
                    $this->db->like('s.c_name', $searchValue, 'both');
                    $this->db->or_like('m.c_name', $searchValue, 'both');
                    $this->db->group_end();
                } else {
                    $this->db->where('1=0', NULL, FALSE);
                }
            }
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
        $searchValue = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';
        $is_filtered = $search_name || $search_date || $search_date_to || $search_transid || $search_status || (!empty($searchValue));

        if (!$is_filtered) {
            return $this->count_all_dt();
        }

        $this->db->select('count(DISTINCT cde.id) as total');
        $this->db->from($this->table);
        $this->_apply_filters($search_name, $search_date, $search_date_to, $search_transid, $search_status);

        // Truly Smart Search in Count
        if (!empty($searchValue)) {
            $safeSearch = $this->db->escape_str($searchValue);
            
            if (self::$cached_ids === null) {
                $matching_ids = [-1];
                $res = $this->db->query("SELECT id FROM cashin_dynamic_ewallet WHERE c_merchantTransactionId LIKE '$safeSearch%' LIMIT 100")->result();
                if (!empty($res)) $matching_ids = array_merge($matching_ids, array_column($res, 'id'));
                if (is_numeric($searchValue) && strlen($searchValue) < 15) $matching_ids[] = (int)$searchValue;
                self::$cached_ids = array_unique($matching_ids);
            }
            $matching_ids = self::$cached_ids;

            if (count($matching_ids) > 1) {
                $this->db->where_in('cde.id', $matching_ids);
            } else {
                $this->db->join('submerchant s', 'cde.ref_subMerchantId = s.id', 'left');
                $this->db->join('merchant m', 'cde.ref_merchantId = m.id', 'left');
                if (strlen($searchValue) >= 3) {
                    $this->db->group_start();
                    $this->db->like('s.c_name', $searchValue, 'both');
                    $this->db->or_like('m.c_name', $searchValue, 'both');
                    $this->db->group_end();
                } else {
                    $this->db->where('1=0', NULL, FALSE);
                }
            }
        }

        $query = $this->db->get();
        if (!is_object($query) || $query->num_rows() == 0) return 0;
        return $query->row()->total;
    }

    public function count_all_dt($search_name = null, $search_date = null, $search_date_to = null)
    {
        if (self::$cached_total !== null) return self::$cached_total;

        // ULTRA-FAST: Use table status estimates for recordsTotal
        $q = $this->db->query("SHOW TABLE STATUS LIKE 'cashin_dynamic_ewallet'");
        $res = $q->row();
        if ($res && isset($res->Rows) && $res->Rows > 10000) {
            self::$cached_total = (int)$res->Rows;
            return self::$cached_total;
        }

        $this->db->select("count(id) as total");
        $this->db->from($this->table);
        $query = $this->db->get();
        self::$cached_total = $query->row() ? (int)$query->row()->total : 0;
        return self::$cached_total;
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
        $this->load->library('datatables');

        $search_name = $filters['merchant'] ?? null;
        $search_date = $filters['date'] ?? null;
        $search_date_to = $filters['date_to'] ?? null;
        $search_transid = $filters['transid'] ?? null;
        $search_status = $filters['status'] ?? null;

        // Optimized Fetch (Two-Step Lookup)
        $list = $this->get_datatables($search_name, $search_date, $search_date_to, $search_transid, $search_status);
        
        $searchValue = $this->input->post('search')['value'];
        $is_filtered = $search_name || $search_date || $search_date_to || $search_transid || $search_status || (!empty($searchValue));

        $recordsTotal = $this->count_all_dt($search_name, $search_date, $search_date_to);
        $recordsFiltered = $is_filtered ? $this->count_filtered($search_name, $search_date, $search_date_to, $search_transid, $search_status) : $recordsTotal;

        // Use Datatables Library for final processing and JSON output
        return $this->datatables->of($this->table)
            ->set_recordsTotal($recordsTotal)
            ->set_recordsFiltered($recordsFiltered)
            ->set_data($list)
            ->addColumn('no', function($row) {
                static $no = null;
                if ($no === null) $no = intval($this->input->post('start'));
                return ++$no;
            })
            ->addColumn('simulation', function($row) {
                return '<button type="button" class="btn btn-sm btn-dt-action btn-dt-secondary detailEwalletDynamicChannelExternalAjax" 
                        data-merchanttransactionid="' . $row->c_merchantTransactionId . '" 
                        data-ref_cashinexternalid="' . $row->ref_cashinExternalId . '" 
                        data-ref_cashinexternallogewalletidcreate="' . $row->ref_cashinExternalLogEwalletIdCreate . '">
                        <i class="fas fa-info-circle mr-1"></i> DETAILS
                    </button>';
            })
            ->make(true);
    }
}
?>