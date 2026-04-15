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

    private function _get_datatables_query($search_name = null, $search_date = null, $search_date_to = null, $search_transid = null, $search_status = null)
    {
        $this->db->select("cde.*, s.c_name as name_submerchant, m.c_name as name_merchant");
        $this->db->from($this->table);
        $this->db->join('submerchant s', 'cde.ref_subMerchantId = s.id', 'left');
        $this->db->join('merchant m', 'cde.ref_merchantId = m.id', 'left');

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
        $this->_get_datatables_query($search_name, $search_date, $search_date_to, $search_transid, $search_status);
        if (isset($_POST['length']) && $_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered($search_name = null, $search_date = null, $search_date_to = null, $search_transid = null, $search_status = null)
    {
        $this->db->select('count(cde.id) as total');
        // Optimized: Only join if global search is used
        $this->db->from($this->table);
        $this->_apply_filters($search_name, $search_date, $search_date_to, $search_transid, $search_status);

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
        $this->db->select('count(cde.id) as total');
        $this->db->from($this->table);
        $this->_apply_filters($search_name, $search_date, $search_date_to);
        $query = $this->db->get();
        return $query->row()->total;
    }

    public function get_summary($search_name = null, $search_date = null, $search_date_to = null, $search_transid = null, $search_status = null)
    {
        $today = date('Y-m-d');
        $is_today_included = false;
        $is_history_included = false;

        $total_qty = 0;
        $total_amount = 0;

        // Determine range
        $start_date = $search_date ? date('Y-m-d', strtotime($search_date)) : null;
        $end_date = $search_date_to ? date('Y-m-d', strtotime($search_date_to)) : ($search_date ? $start_date : null);

        if (!$start_date) {
            // All Time
            $is_today_included = true;
            $is_history_included = true;
        } else {
            if ($start_date < $today) $is_history_included = true;
            if ($end_date >= $today || $start_date == $today) $is_today_included = true;
        }

        // 1. Get Historical Data from Summary Table
        if ($is_history_included) {
            $this->db->select('SUM(total_qty) as qty, SUM(total_amount) as amount');
            $this->db->from('tr_summary_daily');
            $this->db->where('transaction_type', 'EWALLET_DYNAMIC');
            
            if ($start_date) {
                $this->db->where('summary_date >=', $start_date);
                $this->db->where('summary_date <', $today);
                if ($end_date && $end_date < $today) {
                    $this->db->where('summary_date <=', $end_date);
                }
            } else {
                $this->db->where('summary_date <', $today);
            }

            if ($search_name) {
                $this->db->where('ref_merchantId', $search_name);
            }
            $hist = $this->db->get()->row();
            $total_qty += $hist->qty ?: 0;
            $total_amount += $hist->amount ?: 0;
        }

        // 2. Get Live Data for Today from Main Table
        if ($is_today_included) {
            $this->db->select("COUNT(*) as qty, SUM(c_amount) as total_trx");
            $this->db->from($this->table);
            
            $this->db->where('cde.c_datetimeRequest >=', $today . ' 00:00:00');
            $this->db->where('cde.c_datetimeRequest <=', $today . ' 23:59:59');

            $this->_apply_filters($search_name, $today, $today, $search_transid, $search_status);
            
            $live = $this->db->get()->row();
            $total_qty += $live->qty ?: 0;
            $total_amount += $live->total_trx ?: 0;
        }

        return (object)[
            'qty' => $total_qty,
            'total_trx' => $total_amount
        ];
    }



    public function get_merchant()
    {
        $query = "select * from merchant ";
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