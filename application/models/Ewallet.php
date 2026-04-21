<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ewallet extends CI_Model {
    var $table = 'cashin_payment_ewallet cpe';
    var $column_order = array(null, 'cpe.c_datetime', 's.c_name', 'c.c_invoiceNo', 'cpe.c_type', 'cpe.ref_cashinChannelId', 'cpe.c_amount', 'cpe.c_mdr', 'cpe.c_fee', 'cpe.c_datetimeSettlement', 'cde.c_merchantTransactionId', null);
    var $column_search = array('cpe.id', 'c.c_invoiceNo', 'cde.c_merchantTransactionId', 's.c_name', 'm.c_name');
    var $order = array('cpe.id' => 'desc');

    private function _get_datatables_query($search_name = null, $date_from = null, $date_to = null, $search_date_settlement = null, $search_invoice_no = null, $only_ids = false, $count_only = false)
    {
        // Emergency 3-second safeguard
        $this->db->query("SET SESSION max_execution_time = 3000");
        
        if ($count_only) {
            $this->db->select("count(cpe.id) as total");
        } else if ($only_ids) {
            $this->db->select("cpe.id");
        } else {
            $this->db->select("cpe.*, m.c_name as name_merchant, s.c_name as name_submerchant, c.c_invoiceNo, 
                               cde.c_merchantTransactionId AS Merchant_Transaction_Id");
        }
        $this->db->from($this->table);
        
        // Essential joins
        $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
        $isInvoiceSearch = (preg_match('/^INV/i', $searchValue));
        $sort_col = isset($_POST['order']['0']['column']) ? $this->column_order[$_POST['order']['0']['column']] : '';

        // Join cashin only if searching for invoice, sorting by it, or getting full data
        // Base joins only added if needed for sorting or full data (Deferred Join pattern)
        if (!$only_ids && !$count_only || $search_name || $searchValue || strpos($sort_col, 's.') !== false || strpos($sort_col, 'm.') !== false) {
            $this->db->join('submerchant s', 'cpe.ref_subMerchantId = s.id', 'left');
            $this->db->join('merchant m', 'cpe.ref_merchantId = m.id', 'left');
        }

        // Apply Basic Filters
        if ($search_name) {
            $this->db->where('cpe.ref_merchantId', $search_name);
        }
        if ($date_from && $date_to) {
            $this->db->where('cpe.c_datetimePayment >=', $date_from);
            $this->db->where('cpe.c_datetimePayment <=', $date_to);
        }
        if ($search_date_settlement) {
            $formatted_date = date('Y-m-d', strtotime($search_date_settlement));
            $this->db->where('cpe.c_datetimeSettlement >=', $formatted_date . ' 00:00:00');
            $this->db->where('cpe.c_datetimeSettlement <=', $formatted_date . ' 23:59:59');
        }
        if ($search_invoice_no) {
            $search_invoice_no = trim($search_invoice_no);
            if ($search_invoice_no !== '') {
                $safeInv = $this->db->escape_str($search_invoice_no);
                $this->db->where_in('cpe.ref_cashinId', "SELECT id FROM cashin WHERE c_invoiceNo LIKE '$safeInv%'", FALSE);
            }
        }

        if ($searchValue) {
            $safeSearch = $this->db->escape_str($searchValue);
            
            // Detect technical IDs (Numeric > 8 digits, or starting with INV/GD/GR/0000)
            $isTechnicalId = preg_match('/^([0-9]{8,30}|(INV|GD|GR|0000)[0-9a-zA-Z]+)/i', $searchValue);
            $isInvoiceSearch = preg_match('/^INV/i', $searchValue);

            if ($isTechnicalId) {
                $matching_ids = [-1];
                
                // 1. Direct ID match
                if (is_numeric($searchValue) && strlen($searchValue) < 15) {
                    $matching_ids[] = (int)$searchValue;
                }
                
                // 2. Invoice No match (via sub-query lookup)
                $inv_res = $this->db->query("SELECT id FROM cashin WHERE c_invoiceNo LIKE '$safeSearch%' LIMIT 50")->result();
                $inv_ids = array_column($inv_res, 'id');
                
                // 3. Merchant Trans ID match (via dynamic ewallet lookup)
                $cde_res = $this->db->query("SELECT id FROM cashin_dynamic_ewallet WHERE c_merchantTransactionId LIKE '$safeSearch%' LIMIT 50")->result();
                if (!empty($cde_res)) {
                    $cde_ids = array_column($cde_res, 'id');
                    $cpe_res = $this->db->query("SELECT id FROM cashin_payment_ewallet WHERE ref_cashinDynamicEwalletId IN (".implode(',', $cde_ids).") LIMIT 50")->result();
                    if (!empty($cpe_res)) $matching_ids = array_merge($matching_ids, array_column($cpe_res, 'id'));
                }
                
                // Construct the combined filter for technical IDs
                $this->db->group_start();
                if (count($matching_ids) > 1) {
                    $this->db->where_in('cpe.id', array_unique($matching_ids));
                }
                if (!empty($inv_ids)) {
                    if (count($matching_ids) > 1) {
                        $this->db->or_where_in('cpe.ref_cashinId', $inv_ids);
                    } else {
                        $this->db->where_in('cpe.ref_cashinId', $inv_ids);
                    }
                } else if (count($matching_ids) <= 1) {
                    $this->db->where('1=0', NULL, FALSE);
                }
                $this->db->group_end();
                
            } else {
                // TEXT SEARCH: Submerchant name only (min 4 chars)
                if (strlen($searchValue) >= 4) {
                    $this->db->like('s.c_name', $searchValue, 'after');
                } else {
                    $this->db->where('1=0', NULL, FALSE);
                }
            }
        }

        if (!$count_only) {
            if (isset($_POST['order'])) {
                $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
            } else if (isset($this->order)) {
                $order = $this->order;
                $this->db->order_by(key($order), $order[key($order)]);
            }
        }

    }

    public function get_datatables($search_name = null, $date_from = null, $date_to = null, $search_date_settlement = null, $search_invoice_no = null)
    {
        // STEP 1: Get matching IDs only (Fast)
        $this->_get_datatables_query($search_name, $date_from, $date_to, $search_date_settlement, $search_invoice_no, true);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        if (!$query) return array(); // Fail-safe
        
        $id_results = $query->result();
        
        if (empty($id_results)) return array();
        
        $ids = array_column($id_results, 'id');
        
        // STEP 2: Fetch full details for those IDs
        $this->db->select("cpe.*, m.c_name as name_merchant, s.c_name as name_submerchant, c.c_invoiceNo, 
                           cde.c_merchantTransactionId AS Merchant_Transaction_Id");
        $this->db->from($this->table);
        $this->db->join('cashin c', 'c.id = cpe.ref_cashinId');
        $this->db->join('submerchant s', 'cpe.ref_subMerchantId = s.id');
        $this->db->join('merchant m', 'cpe.ref_merchantId = m.id');
        $this->db->join('cashin_dynamic_ewallet cde', 'cde.ref_merchantId = cpe.ref_merchantId AND cde.id = cpe.ref_cashinDynamicEwalletId', 'left');
        
        $this->db->where_in('cpe.id', $ids);
        
        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
        
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered($search_name = null, $date_from = null, $date_to = null, $search_date_settlement = null, $search_invoice_no = null)
    {
        $this->_get_datatables_query($search_name, $date_from, $date_to, $search_date_settlement, $search_invoice_no, false, true);
        $query = $this->db->get();
        return $query->row()->total;
    }

    public function count_all_dt($search_name = null, $date_from = null, $date_to = null)
    {
        $table_name = explode(' ', $this->table)[0];
        $query = $this->db->query("SELECT TABLE_ROWS FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$table_name}'");
        $result = $query->row();
        return $result ? (int)$result->TABLE_ROWS : 0;
    }


    
        public function ewallet_detail($id)
        {
            $query = "SELECT a.c_datetime, a.ref_merchantId, c.c_name AS name_merchant, a.ref_subMerchantId, 
                        d.c_name AS name_submerchant, b.c_invoiceNo, 
                        a.c_type, a.ref_cashinChannelId, 
                        a.c_amount, a.c_mdr, a.c_fee, a.c_datetimePayment,
                        a.c_isSettlementRealtime, a.c_datetimeSettlement,
                        e.c_merchantTransactionId AS Merchant_Transaction_Id
                        FROM cashin_payment_ewallet a   
                        JOIN cashin b ON b.id=a.ref_cashinId
                        JOIN merchant c ON a.ref_merchantId=c.id
                        JOIN submerchant d ON a.ref_subMerchantId=d.id
                        LEFT JOIN cashin_dynamic_ewallet e ON (e.ref_merchantId=a.ref_merchantId AND e.id=a.ref_cashinDynamicEwalletId) 
                        WHERE a.id ='$id'";

            return $this->db->query($query)->result_array();

        }

    public function insertEwalletDynamic($dataInsert2) {
        foreach ($dataInsert2 as $key => $value) {
            if (is_array($value)) {
                $dataInsert2[$key] = json_encode($value);
            }
        }

        $this->db->insert('cashin_dynamic_ewallet', $dataInsert2);
        return ($this->db->affected_rows() != 1) ? false : true;
    }
    
    public function updateEwalletDynamic($data, $id) {
        $this->db->where('id', $id);
        $this->db->update('cashin_dynamic_ewallet', $data);
        return ($this->db->affected_rows() != 1) ? false : true;
    }
    public function get_detail_ewallet($idRequest2) {
        $query = "select cdv.*, m.c_name, m.id from cashin_dynamic_ewallet cdv join merchant m on m.id = cdv.ref_merchantId where cdv.id = $idRequest2";
        return $this->db->query($query)->result_array();
    }

    public function get_qris($limit, $start, $search_date_qris = null, $search_date_qris_to = null, $search_name_qris = null, $search_date_qris_settlement = null, $search_invoice_no = null)
    {

        $query = "SELECT 
                    merchant.c_name as name_merchant,
                    cashin_payment_qris_mpm.id, 
                    cashin_payment_qris_mpm.c_datetime, 
                    submerchant.c_name as name_submerchant, 
                    cashin.c_invoiceNo, 
                    cashin_payment_qris_mpm.c_type,
                    cashin_payment_qris_mpm.ref_merchantId, 
                    cashin_payment_qris_mpm.ref_subMerchantId, 
                    cashin_payment_qris_mpm.c_amount, 
                    cashin_payment_qris_mpm.c_mdr, 
                    cashin_payment_qris_mpm.c_fee,
                    cashin_payment_qris_mpm.c_datetimePayment, cashin_payment_qris_mpm.c_isSettlementRealtime, 
                    cashin_payment_qris_mpm.c_datetimeSettlement, cashin_payment_qris_mpm.c_isSettlementRealtimeExternal, 
                    cashin_payment_qris_mpm.c_feeExternal, cashin_payment_qris_mpm.c_datetimeSettlementExternal,
                    IF(cashin_payment_qris_mpm.c_type='Dynamic', cashin_dynamic_qris_mpm.c_merchantTransactionId, cashin_recurring_qris_mpm.c_merchantTransactionId) AS Merchant_Transaction_Id
                    FROM cashin_payment_qris_mpm 
                    JOIN cashin on cashin.id = cashin_payment_qris_mpm.ref_cashinId
                    JOIN submerchant on cashin_payment_qris_mpm.ref_subMerchantId = submerchant.id 
                    JOIN merchant on cashin_payment_qris_mpm.ref_merchantId = merchant.id
                    LEFT JOIN cashin_dynamic_qris_mpm on (cashin_dynamic_qris_mpm.ref_subMerchantId = cashin_payment_qris_mpm.ref_subMerchantId AND cashin_dynamic_qris_mpm.id=cashin_payment_qris_mpm.ref_cashinDynamicQrisMpmId)
                    LEFT JOIN cashin_recurring_qris_mpm on (cashin_recurring_qris_mpm.ref_subMerchantId = cashin_payment_qris_mpm.ref_subMerchantId AND cashin_recurring_qris_mpm.id=cashin_payment_qris_mpm.ref_cashinRecurringQrisMpmId)";

        $query .= " WHERE 1=1 ";

        if (!empty($search_name_qris)) {
            $query .= " and cashin_payment_qris_mpm.ref_merchantId = '$search_name_qris'";
        }

        if (!empty($search_date_qris) && !empty($search_date_qris_to)) {
            // $search_date_qris = date('Y-m-d', strtotime($search_date_qris));
            $query .= " and cashin_payment_qris_mpm.c_datetime >= '$search_date_qris' AND cashin_payment_qris_mpm.c_datetime <= '$search_date_qris_to'";
        }

        if (!empty($search_date_qris_settlement)) {
            $formatted_date = date('Y-m-d', strtotime($search_date_qris_settlement));
            $query .= " and cashin_payment_qris_mpm.c_datetimeSettlement >= '$formatted_date 00:00:00' AND cashin_payment_qris_mpm.c_datetimeSettlement <= '$formatted_date 23:59:59'";
        }

        if (!empty($search_invoice_no)) {
            $query .= " and cashin.c_invoiceNo= '$search_invoice_no'";
        }

        $query .= " ORDER BY cashin_payment_qris_mpm.id DESC
                    LIMIT $start, $limit";

        return $this->db->query($query)->result();
    }

    public function count_qris($refMerchantId, $search_date_qris = null)
    {
        $this->db->from('cashin_payment_qris_mpm');
        $this->db->join('cashin', 'cashin.id = cashin_payment_qris_mpm.ref_cashinId');
        $this->db->join('merchant', 'merchant.id = cashin_payment_qris_mpm.ref_merchantId');
        $this->db->join('submerchant', 'submerchant.id = cashin_payment_qris_mpm.ref_subMerchantId');
        $this->db->where('cashin_payment_qris_mpm.ref_merchantId', $refMerchantId);

        if ($search_date_qris) {
            $this->db->where('cashin_payment_qris_mpm.c_datetime', $search_date_qris);
        }

        return $this->db->count_all_results();
    }

    public function get_merchant()
    {
        $query = "select id,c_name from merchant ";
        return $this->db->query($query)->result();
    }

    public function get_summary($date_from, $date_to, $refMerchantId = null) {
        // $this->db->select('COUNT(id) as qty, SUM(c_amount) as amount, SUM(c_fee) as fee, SUM(c_feeExternal) as fee_external');
        $query = "SELECT COUNT(a.id) as qty, SUM(a.c_amount) as amount, SUM(a.c_fee) as fee, SUM(a.c_feeExternal) as fee_external
        FROM cashin_payment_ewallet a
        WHERE a.c_datetimePayment  >= '$date_from' AND a.c_datetimePayment <= '$date_to'";

        if (!empty($refMerchantId)) {
            $query .= " AND a.ref_merchantId = '$refMerchantId'";
        }

        return $this->db->query($query)->result_array();
    }

    public function monthly_ewallet() {
        $year = date('Y');
        // Optimized: Avoid MONTH() index-killer
        $query = "SELECT MONTH(c_datetimePayment) AS month, SUM(c_amount) AS amount
                  FROM cashin_payment_ewallet 
                  WHERE c_datetimePayment >= '$year-01-01 00:00:00' AND c_datetimePayment <= '$year-12-31 23:59:59'
                  GROUP BY MONTH(c_datetimePayment)
                  ORDER BY month";
        return $this->db->query($query)->result_array();
    }
}
?>