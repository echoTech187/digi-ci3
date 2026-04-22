<?php defined('BASEPATH') or exit('No direct script access allowed');

class QRISDynamic extends CI_Model
{
    var $table = 'cashin_dynamic_qris_mpm cdq';
    var $column_order = array(null, 'cdq.c_datetimeRequest', 'm.c_name', 's.c_name', 'cdq.c_merchantTransactionId', 'cdq.ref_cashinExternalId', 'cdq.c_amount', 'cdq.c_datetimeExpired', 'cdq.c_status');
    var $column_search = array('cdq.c_merchantTransactionId', 'cdq.ref_merchantId', 'cdq.ref_subMerchantId', 's.c_name', 'm.c_name');
    var $order = array('cdq.id' => 'desc');

    private function _apply_filters($search_name = null, $search_date = null, $search_transid = null, $search_status = null, $search_reff = null, $search_date_to = null)
    {
        if ($search_name) {
            $this->db->where('cdq.ref_merchantId', $search_name);
        }

        if ($search_date && $search_date_to) {
            $date_from = date('Y-m-d 00:00:00', strtotime($search_date));
            $date_to = date('Y-m-d 23:59:59', strtotime($search_date_to));
            $this->db->where('cdq.c_datetimeRequest >=', $date_from);
            $this->db->where('cdq.c_datetimeRequest <=', $date_to);
        } elseif ($search_date) {
            $formatted_date = date('Y-m-d', strtotime($search_date));
            $this->db->where('cdq.c_datetimeRequest >=', $formatted_date . ' 00:00:00');
            $this->db->where('cdq.c_datetimeRequest <=', $formatted_date . ' 23:59:59');
        }

        if ($search_transid) {
            $search_transid = trim($search_transid);
            if ($search_transid !== '') {
                $safeTrans = $this->db->escape_str($search_transid);
                // Pre-Lookup IDs to keep query indexed
                $res = $this->db->query("SELECT id FROM cashin_dynamic_qris_mpm WHERE c_merchantTransactionId LIKE '$safeTrans%' LIMIT 100")->result();
                if (!empty($res)) {
                    $this->db->where_in('cdq.id', array_column($res, 'id'));
                } else {
                    $this->db->where('1=0', NULL, FALSE);
                }
            }
        }

        if ($search_status) {
            $this->db->where('cdq.c_status', $search_status);
        }

        if ($search_reff) {
            // Only join external if explicitly searched
            $this->db->join('external_paydgn_qris_mpm_create epq', 'cdq.ref_cashinExternalLogQrisMpmIdCreate = epq.id', 'left');
            $this->db->where('epq.refId', $search_reff);
            $this->db->where('cdq.ref_cashinExternalId', 'paydgn');
        }
    }

    private function _get_datatables_query($search_name = null, $search_date = null, $search_transid = null, $search_status = null, $search_reff = null, $search_date_to = null, $only_ids = false, $count_only = false)
    {
        // Emergency 3-second safeguard
        $this->db->query("SET SESSION max_execution_time = 10000");
        
        if ($count_only) {
            $this->db->select("count(cdq.id) as total");
        } else if ($only_ids) {
            $this->db->select("cdq.id");
        } else {
            $this->db->select("cdq.*, s.c_name as name_submerchant, m.c_name as name_merchant");
        }
        $this->db->from($this->table);
        
        $searchValue = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';
        $sort_col = isset($_POST['order']['0']['column']) ? $this->column_order[$_POST['order']['0']['column']] : '';

        // Join only if needed for text search, sorting or full data
        $isTextSearch = $searchValue && !preg_match('/^(GD|INV|[0-9]{8,})/i', $searchValue);
        if (!$only_ids && !$count_only || $search_name || $isTextSearch || strpos($sort_col, 's.') !== false || strpos($sort_col, 'm.') !== false) {
            $this->db->join('submerchant s', 's.id = cdq.ref_subMerchantId', 'left');
            $this->db->join('merchant m', 'm.id = cdq.ref_merchantId', 'left');
        }

        $this->_apply_filters($search_name, $search_date, $search_transid, $search_status, $search_reff, $search_date_to);

        if ($searchValue) {
            $safeSearch = $this->db->escape_str($searchValue);
            
            // Detect technical IDs (Numeric > 8 digits, or starting with GD/INV/0000)
            $isTechnicalId = preg_match('/^([0-9]{8,30}|(GD|INV|0000)[0-9a-zA-Z]+)/i', $searchValue);

            if ($isTechnicalId) {
                $matching_ids = [-1];
                
                // 1. Merchant Trans ID match
                $res = $this->db->query("SELECT id FROM cashin_dynamic_qris_mpm WHERE c_merchantTransactionId LIKE '$safeSearch%' LIMIT 100")->result();
                if (!empty($res)) $matching_ids = array_merge($matching_ids, array_column($res, 'id'));
                
                // 2. Direct PK match if numeric
                if (is_numeric($searchValue) && strlen($searchValue) < 15) {
                    $matching_ids[] = (int)$searchValue;
                }

                $this->db->where_in('cdq.id', array_unique($matching_ids));
            } else {
                // TEXT SEARCH: Merchant or Submerchant name (min 4 chars)
                if (strlen($searchValue) >= 4) {
                    $this->db->group_start();
                    $this->db->like('s.c_name', $searchValue, 'after');
                    $this->db->or_like('m.c_name', $searchValue, 'after');
                    $this->db->group_end();
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

    public function get_datatables($search_name = null, $search_date = null, $search_transid = null, $search_status = null, $search_reff = null, $search_date_to = null)
    {
        // STEP 1: Get matching IDs only (Fast)
        $this->_get_datatables_query($search_name, $search_date, $search_transid, $search_status, $search_reff, $search_date_to, true);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        if (!$query) return array();
        
        $id_results = $query->result();
        
        if (empty($id_results)) return array();
        
        $ids = array_column($id_results, 'id');
        
        // STEP 2: Fetch full details for those specific IDs
        $this->db->select("cdq.*, s.c_name as name_submerchant, m.c_name as name_merchant");
        $this->db->from($this->table);
        $this->db->join('submerchant s', 's.id = cdq.ref_subMerchantId', 'left');
        $this->db->join('merchant m', 'm.id = cdq.ref_merchantId', 'left');
        
        $this->db->where_in('cdq.id', $ids);
        
        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
        
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered($search_name = null, $search_date = null, $search_transid = null, $search_status = null, $search_reff = null, $search_date_to = null)
    {
        $searchValue = $this->input->post('search')['value'];
        $is_filtered = $search_name || $search_date || $search_date_to || $search_transid || $search_status || $search_reff || (!empty($searchValue));

        if (!$is_filtered) {
            return $this->count_all_dt();
        }

        $this->_get_datatables_query($search_name, $search_date, $search_transid, $search_status, $search_reff, $search_date_to, false, true);
        $query = $this->db->get();
        if (!is_object($query) || $query->num_rows() == 0) return 0;
        return $query->row()->total;
    }

    public function get_datatables_handler($filters = [])
    {
        $this->load->library('datatables');

        $search_name = $filters['merchant'] ?? null;
        $search_date = $filters['date'] ?? null;
        $search_date_to = $filters['date_to'] ?? null;
        $search_transid = $filters['transid'] ?? null;
        $search_status = $filters['status'] ?? null;
        $search_reff = $filters['reff'] ?? null;

        // Optimized Fetch (Two-Step Lookup)
        $list = $this->get_datatables($search_name, $search_date, $search_transid, $search_status, $search_reff, $search_date_to);
        
        $searchValue = $this->input->post('search')['value'];
        $is_filtered = $search_name || $search_date || $search_date_to || $search_transid || $search_status || $search_reff || (!empty($searchValue));

        $recordsTotal = $this->count_all_dt($search_name, $search_date, $search_date_to);
        $recordsFiltered = $is_filtered ? $this->count_filtered($search_name, $search_date, $search_transid, $search_status, $search_reff, $search_date_to) : $recordsTotal;

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
            ->editColumn('name_merchant', function($row) {
                return ' [' . ($row->ref_merchantId ?? '-') . '] - ' . ($row->name_merchant ?? '-');
            })
            ->editColumn('name_submerchant', function($row) {
                return ' [' . ($row->ref_subMerchantId ?? '-') . '] - ' . ($row->name_submerchant ?? '-');
            })
            ->make(true);
    }
    public function count_all_dt($search_name = null, $search_date = null, $search_date_to = null)
    {
        $table_name = explode(' ', $this->table)[0];
        $query = $this->db->query("SELECT TABLE_ROWS FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$table_name}'");
        $result = $query->row();
        return $result ? (int)$result->TABLE_ROWS : 0;
    }

    public function get_merchant()
    {
        $query = "select id,c_name from merchant ";
        return $this->db->query($query)->result();
    }
}
?>