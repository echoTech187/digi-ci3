<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * QRISRecurring Model
 * Handles recurring QRIS requests, DataTables lookups, caching, and external provider payload resolution.
 */
class QRISRecurring extends CI_Model
{
    var $table = 'cashin_recurring_qris_mpm as crqm';
    var $column_order = [
        null, 'crqm.c_datetimeRequest', 'm.c_name', 's.c_name',
        'crqm.c_merchantTransactionId', 'ref_cashinChannelId',
        'crqm.ref_cashinExternalId', 'crqm.c_amount', 'crqm.c_status'
    ];
    var $column_search = ['crqm.c_merchantTransactionId', 'crqm.ref_merchantId', 'crqm.ref_subMerchantId', 's.c_name', 'm.c_name'];
    var $order = ['crqm.id' => 'desc'];

    private static $cached_ids = null;
    private static $cached_total = null;

    private function _apply_filters($search_name = null, $search_date = null, $search_date_to = null, $search_submerchant = null, $search_transid = null, $search_status = null, $search_channel = null, $search_external_channel = null)
    {
        if ($search_name) {
            $this->db->where('crqm.ref_merchantId', $search_name);
        }
        if ($search_date) {
            $this->db->where('crqm.c_datetimeRequest >=', date('Y-m-d', strtotime($search_date)) . ' 00:00:00');
        }
        if ($search_date_to) {
            $this->db->where('crqm.c_datetimeRequest <=', date('Y-m-d', strtotime($search_date_to)) . ' 23:59:59');
        }
        if ($search_submerchant) {
            $this->db->where('crqm.ref_subMerchantId', $search_submerchant);
        }
        if ($search_transid) {
            $search_transid = trim($search_transid);
            if ($search_transid !== '') {
                $safeTrans = $this->db->escape_str($search_transid);
                $this->db->where("(crqm.c_merchantTransactionId LIKE '$safeTrans%' OR crqm.id LIKE '$safeTrans%' OR crqm.c_referenceNo LIKE '$safeTrans%')");
            }
        }
        if ($search_status) {
            $this->db->where('crqm.c_status', $search_status);
        }
        if ($search_channel && $search_channel !== 'qris_mpm') {
            $this->db->where('1=0', null, false);
        }
        if ($search_external_channel) {
            $this->db->where('crqm.ref_cashinExternalId', $search_external_channel);
        }
    }

    private function _get_datatables_query($search_name = null, $search_date = null, $search_date_to = null, $search_submerchant = null, $search_transid = null, $only_ids = false, $count_only = false, $search_status = null, $search_channel = null, $search_external_channel = null)
    {
        $this->db->query("SET SESSION max_execution_time = 30000");

        if ($count_only) {
            $this->db->select("count(crqm.id) as total");
        } elseif ($only_ids) {
            $this->db->select("crqm.id");
        } else {
            $this->db->select("crqm.*, s.c_name as name_submerchant, m.c_name as name_merchant");
        }
        $this->db->from($this->table);

        $searchValue = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';
        $sort_col = isset($_POST['order']['0']['column']) ? $this->column_order[$_POST['order']['0']['column']] : '';

        $isTextSearch = $searchValue && !preg_match('/^(GR|INV|[0-9]{8,})/i', $searchValue);
        $joined_merchant_submerchant = false;
        if (!$only_ids && !$count_only || $search_name || $isTextSearch || strpos($sort_col, 's.') !== false || strpos($sort_col, 'm.') !== false) {
            $this->db->join('submerchant s', 's.id = crqm.ref_subMerchantId', 'left');
            $this->db->join('merchant m', 'm.id = crqm.ref_merchantId', 'left');
            $joined_merchant_submerchant = true;
        }

        $this->_apply_filters($search_name, $search_date, $search_date_to, $search_submerchant, $search_transid, $search_status, $search_channel, $search_external_channel);

        if ($searchValue) {
            $safeSearch = $this->db->escape_str($searchValue);
            if (self::$cached_ids === null) {
                $matching_ids = [-1];
                $res = $this->db->query("SELECT id FROM cashin_recurring_qris_mpm WHERE c_merchantTransactionId LIKE '$safeSearch%' OR c_referenceNo LIKE '$safeSearch%' LIMIT 100")->result();
                if (!empty($res)) {
                    $matching_ids = array_merge($matching_ids, array_column($res, 'id'));
                }
                if (is_numeric($searchValue) && strlen($searchValue) < 15) {
                    $matching_ids[] = (int) $searchValue;
                }
                self::$cached_ids = array_unique($matching_ids);
            }
            $matching_ids = self::$cached_ids;

            if (count($matching_ids) > 1) {
                $this->db->where_in('crqm.id', $matching_ids);
            } else {
                if (strlen($searchValue) >= 3) {
                    if (!$joined_merchant_submerchant) {
                        $this->db->join('submerchant s', 'crqm.ref_subMerchantId = s.id', 'left');
                        $this->db->join('merchant m', 'crqm.ref_merchantId = m.id', 'left');
                    }
                    $this->db->group_start();
                    $this->db->like('s.c_name', $searchValue, 'both');
                    $this->db->or_like('m.c_name', $searchValue, 'both');
                    $this->db->group_end();
                } else {
                    $this->db->where('1=0', null, false);
                }
            }
        }

        if (!$count_only) {
            if (isset($_POST['order'])) {
                $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
            } elseif (isset($this->order)) {
                $this->db->order_by(key($this->order), $this->order[key($this->order)]);
            }
        }
    }

    public function get_datatables($search_name = null, $search_date = null, $search_date_to = null, $search_submerchant = null, $search_transid = null, $search_status = null, $search_channel = null, $search_external_channel = null)
    {
        $this->_get_datatables_query($search_name, $search_date, $search_date_to, $search_submerchant, $search_transid, true, false, $search_status, $search_channel, $search_external_channel);
        if ($_POST['length'] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        if (!$query) return [];

        $id_results = $query->result();
        if (empty($id_results)) return [];

        $ids = array_column($id_results, 'id');
        $this->db->select("crqm.*, m.c_name as name_merchant, m.c_merchantLevel, s.c_name as name_submerchant, 'qris_mpm' AS ref_cashinChannelId, IF(cc.id IS NULL OR cc.id = '', 'QRIS', cc.id) AS channel_description", false);
        $this->db->from($this->table);
        $this->db->join('merchant m', 'crqm.ref_merchantId = m.id', 'left');
        $this->db->join('submerchant s', 's.id = crqm.ref_subMerchantId', 'left');
        $this->db->join('cashin_external_x_channel cc', "cc.id = 'qris_mpm'", 'left');
        $this->db->where_in('crqm.id', $ids);

        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } elseif (isset($this->order)) {
            $this->db->order_by(key($this->order), $this->order[key($this->order)]);
        }

        return $this->db->get()->result();
    }

    public function count_filtered($search_name = null, $search_date = null, $search_date_to = null, $search_submerchant = null, $search_transid = null, $search_status = null, $search_channel = null, $search_external_channel = null)
    {
        $searchValue = $this->input->post('search')['value'] ?? '';
        $is_filtered = $search_name || $search_date || $search_date_to || $search_submerchant || $search_transid || $search_status || $search_channel || $search_external_channel || (!empty($searchValue));

        if (!$is_filtered) {
            return $this->count_all_dt();
        }

        $this->_get_datatables_query($search_name, $search_date, $search_date_to, $search_submerchant, $search_transid, false, true, $search_status, $search_channel, $search_external_channel);
        $query = $this->db->get();
        return (is_object($query) && $query->num_rows() > 0) ? $query->row()->total : 0;
    }

    public function get_datatables_handler($filters = [])
    {
        $this->load->library('datatables');

        $search_name = $filters['merchant'] ?? null;
        $search_date = $filters['date'] ?? null;
        $search_date_to = $filters['date_to'] ?? null;
        $search_submerchant = $filters['submerchant'] ?? null;
        $search_transid = $filters['transid'] ?? null;
        $search_status = $filters['status'] ?? null;
        $search_channel = $filters['channel'] ?? null;
        $search_external_channel = $filters['external_channel'] ?? null;

        $list = $this->get_datatables($search_name, $search_date, $search_date_to, $search_submerchant, $search_transid, $search_status, $search_channel, $search_external_channel);
        $searchValue = $this->input->post('search')['value'] ?? '';
        $is_filtered = $search_name || $search_date || $search_date_to || $search_submerchant || $search_transid || $search_status || $search_channel || $search_external_channel || (!empty($searchValue));

        $recordsTotal = $this->count_all_dt($search_name, $search_date, $search_date_to);
        $recordsFiltered = $is_filtered ? $this->count_filtered($search_name, $search_date, $search_date_to, $search_submerchant, $search_transid, $search_status, $search_channel, $search_external_channel) : $recordsTotal;

        return $this->datatables->of($this->table)
            ->set_recordsTotal($recordsTotal)
            ->set_recordsFiltered($recordsFiltered)
            ->set_data($list)
            ->addColumn('no', function ($row) {
                static $no = null;
                if ($no === null) $no = intval($this->input->post('start'));
                return ++$no;
            })
            ->make(true);
    }

    public function count_all_dt($search_name = null, $search_date = null, $search_date_to = null)
    {
        if (self::$cached_total !== null) return self::$cached_total;

        $q = $this->db->query("SHOW TABLE STATUS LIKE 'cashin_recurring_qris_mpm'");
        $res = $q->row();
        if ($res && isset($res->Rows) && $res->Rows > 10000) {
            self::$cached_total = (int) $res->Rows;
            return self::$cached_total;
        }

        $query = $this->db->select("count(id) as total")->from($this->table)->get();
        self::$cached_total = $query->row() ? (int) $query->row()->total : 0;
        return self::$cached_total;
    }

    public function getDataQrisRecurringChannelExternal($ref_cashinExternalId, $ref_cashinExternalLogQrisMpmIdCreate, $parentId = null)
    {
        $providerMap = [
            'ifp'       => ['table' => 'external_ifp_qris_mpm_create', 'k1' => 'c_orderId', 'k2' => 'c_transactionId', 'hasParent' => false],
            'quantum'   => ['table' => 'external_quantum_qris_mpm_create', 'k1' => 'c_transactionId', 'k2' => 'c_quantumSubMerchantId', 'hasParent' => false],
            'paylabs'   => ['table' => 'external_paylabs_qris_mpm_create', 'k1' => 'c_platformTradeNo', 'k2' => 'c_merchantTradeNo', 'hasParent' => true],
            'paylabs2'  => ['table' => 'external_paylabs2_qris_mpm_create', 'k1' => 'c_platformTradeNo', 'k2' => 'c_merchantTradeNo', 'hasParent' => true],
            'inacash'   => ['table' => 'external_inacash_qris_mpm_create', 'k1' => 'refId', 'k2' => 'partnerRefId', 'hasParent' => true],
            'paydgn'    => ['table' => 'external_paydgn_qris_mpm_create', 'k1' => 'refId', 'k2' => 'partnerRefId', 'hasParent' => true],
            'gvconnect' => ['table' => 'external_gvconnect_snap_qris_mpm_create', 'k1' => 'c_partnerReferenceNo', 'k2' => 'c_referenceLabel', 'hasParent' => true],
            'gvpay'     => ['table' => 'external_gvconnect_snap_qris_mpm_create', 'k1' => 'c_partnerReferenceNo', 'k2' => 'c_referenceLabel', 'hasParent' => true],
            'yukk'      => ['table' => 'external_yukk_qris_mpm_create', 'k1' => 'c_referenceNo', 'k2' => 'c_partnerReferenceNo', 'hasParent' => true],
            'ezeelink'  => ['table' => 'external_ezeelink_qris_mpm_create', 'k1' => 'c_transactionId', 'k2' => 'c_transactionCode', 'hasParent' => true],
            'stm'       => ['table' => 'external_stm_qris_mpm_create', 'k1' => 'qris_reff_code', 'k2' => 'client_reference', 'hasParent' => true]
        ];

        $provider = strtolower((string) $ref_cashinExternalId);
        if (isset($providerMap[$provider])) {
            $cfg = $providerMap[$provider];
            $isLogEmpty = empty($ref_cashinExternalLogQrisMpmIdCreate) || $ref_cashinExternalLogQrisMpmIdCreate === 'null' || $ref_cashinExternalLogQrisMpmIdCreate === 'undefined';

            if ($cfg['hasParent'] && $isLogEmpty && !empty($parentId) && $parentId !== 'null' && $parentId !== 'undefined') {
                $row = $this->db->get_where($cfg['table'], ['ref_cashinRecurringQrisMpmId' => $parentId])->row();
            } else {
                $row = $this->db->get_where($cfg['table'], ['id' => $ref_cashinExternalLogQrisMpmIdCreate])->row();
            }

            if ($row) {
                return [
                    'TransactionIdExternal1' => $row->{$cfg['k1']} ?? null,
                    'TransactionIdExternal2' => $row->{$cfg['k2']} ?? null,
                    'RequestDatetime'        => $row->c_datetimeRequest ?? null,
                    'RequestHeader'          => json_decode($row->c_requestHeader ?? '', true),
                    'RequestBody'            => json_decode($row->c_requestBody ?? '', true),
                    'ResponseDatetime'       => $row->c_datetimeResponse ?? null,
                    'ResponseHeader'         => json_decode($row->c_responseHeader ?? '', true),
                    'ResponseBody'           => json_decode($row->c_responseBody ?? '', true)
                ];
            }
        }

        return [
            'TransactionIdExternal1' => null,
            'TransactionIdExternal2' => null,
            'RequestDatetime'        => null,
            'RequestHeader'          => null,
            'RequestBody'            => null,
            'ResponseDatetime'       => null,
            'ResponseHeader'         => null,
            'ResponseBody'           => null
        ];
    }

    public function get_merchant()
    {
        return $this->db->select('id, c_name')->get('merchant')->result();
    }
}
