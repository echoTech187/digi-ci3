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

        $dt = $this->datatables->of('cashin_recurring_qris_mpm crqm')
            ->select("crqm.id, crqm.c_datetimeRequest, crqm.c_merchantTransactionId, 'qris_mpm' AS ref_cashinChannelId, crqm.ref_cashinExternalId, crqm.ref_cashinExternalLogQrisMpmIdCreate, crqm.c_amount, crqm.c_status, crqm.ref_merchantId, crqm.ref_subMerchantId, m.c_name as merchant_name, s.c_name as sub_account_name", false)
            ->join('merchant m', 'm.id = crqm.ref_merchantId', 'left')
            ->join('submerchant s', 's.id = crqm.ref_subMerchantId', 'left')
            ->set_column_order([null, 'crqm.c_datetimeRequest', 'm.c_name', 's.c_name', 'crqm.c_merchantTransactionId', null, 'crqm.ref_cashinExternalId', 'crqm.c_amount', 'crqm.c_status'])
            ->set_column_search(['crqm.c_merchantTransactionId', 's.c_name', 'm.c_name'])
            ->set_default_order(['crqm.id' => 'desc']);

        if ($search_name) $dt->where('crqm.ref_merchantId', $search_name);
        if ($search_external_channel) $dt->where('crqm.ref_cashinExternalId', $search_external_channel);
        if ($search_status) $dt->where('crqm.c_status', $search_status);
        if ($search_submerchant) $dt->where('crqm.ref_subMerchantId', $search_submerchant);
        if ($search_transid) $dt->where('crqm.c_merchantTransactionId', $search_transid);
        if ($search_date && $search_date_to) {
            $dt->where('crqm.c_datetimeRequest >=', date('Y-m-d', strtotime($search_date)) . ' 00:00:00')
               ->where('crqm.c_datetimeRequest <=', date('Y-m-d', strtotime($search_date_to)) . ' 23:59:59');
        } elseif ($search_date) {
            $dt->where('crqm.c_datetimeRequest >=', date('Y-m-d', strtotime($search_date)) . ' 00:00:00')
               ->where('crqm.c_datetimeRequest <=', date('Y-m-d', strtotime($search_date)) . ' 23:59:59');
        }

        return $dt->addColumn('no', function($row) {
                static $no = null;
                if ($no === null) $no = intval($this->input->post('start'));
                return ++$no;
            })
            ->make(true);
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
