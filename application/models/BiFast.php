<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * BiFast Model
 * Handles BI-FAST transaction queries, two-step DataTables processing, logs, and external channel payload resolution.
 */
class BiFast extends CI_Model
{
    var $table = 'cashout_payment_bifast cpb';
    var $column_order = [
        null, 'm.c_name', 'cpb.c_datetime', 'cpb.c_merchantTransactionId',
        'c.c_invoiceNo', 'cpb.ref_cashoutExternalId', 'cpb.ref_cashoutChannelId',
        'cpb.c_accountNo', 'mab.c_beneficiaryAccountName', 'cpb.c_amount',
        'cpb.c_fee', 'cpb.c_status', null, null
    ];
    var $column_search = ['cpb.id', 'm.c_name', 'cpb.c_merchantTransactionId', 'cpb.c_accountNo', 'mab.c_beneficiaryAccountName'];
    private static $cached_total = null;
    var $order = ['cpb.id' => 'desc'];



    public function get_summary($date_from, $date_to, $refMerchantId = null)
    {
        $params = [$date_from, $date_to];
        $sql = "SELECT COUNT(a.id) as qty, SUM(a.c_amount) as amount, SUM(a.c_fee) as fee, SUM(a.c_feeExternal) as fee_external FROM cashout_payment_bifast a WHERE a.c_datetime >= ? AND a.c_datetime <= ?";

        if (!empty($refMerchantId)) {
            $sql .= " AND a.ref_merchantId = ?";
            $params[] = $refMerchantId;
        }
        return $this->db->query($sql, $params)->result_array();
    }

    public function getBifastDetail($id)
    {
        $sql = "SELECT cashout_payment_bifast.*, cashout.*, merchant.c_name as name_merchant, merchant_account_bank.c_beneficiaryAccountName FROM cashout_payment_bifast JOIN cashout ON cashout.id = cashout_payment_bifast.ref_cashoutId JOIN merchant ON merchant.id = cashout_payment_bifast.ref_merchantId LEFT JOIN merchant_account_bank ON merchant_account_bank.c_beneficiaryAccountNo = cashout_payment_bifast.c_accountNo AND merchant_account_bank.ref_cashoutChannelId = cashout_payment_bifast.ref_cashoutChannelId AND merchant_account_bank.ref_merchantId = cashout_payment_bifast.ref_merchantId WHERE cashout_payment_bifast.id = ?";
        return $this->db->query($sql, [$id])->result_array();
    }

    public function get_merchant()
    {
        return $this->db->select('id, c_name')->get('merchant')->result();
    }

    public function get_channels()
    {
        return $this->db->select('c_cashoutExternalId')
            ->where('c_cashoutChannelGroup', 'bifast')
            ->where('c_status', 'Active')
            ->group_by('c_cashoutExternalId')
            ->get('cashout_external_x_channel')
            ->result();
    }

    public function get_channel_mappings()
    {
        return $this->db->select('c_cashoutExternalId, ref_cashoutChannelId')
            ->where('c_cashoutChannelGroup', 'bifast')
            ->where('c_status', 'Active')
            ->get('cashout_external_x_channel')
            ->result_array();
    }

    public function get_internal_channels()
    {
        return $this->db->select('id, c_description')
            ->where('c_channelGroup', 'bifast')
            ->order_by('c_description', 'ASC')
            ->get('cashout_channel')
            ->result();
    }

    public function getDataBiFastChannelExternal($ref_cashoutExternalId, $ref_cashoutExternalLogQrisMpmIdCreate)
    {
        $tableMap = [
            'gvconnect' => ['tbl' => 'external_gvconnect_snap_disbursement_transfer_bank', 'k1' => 'c_partnerReferenceNo', 'k2' => 'c_referenceNo'],
            'ifp'       => ['tbl' => 'external_ifp_bifast_transfer_interbank', 'k1' => 'c_partnerReferenceNo', 'k2' => 'c_referenceNo'],
            'inacash'   => ['tbl' => 'external_inacash_disbursement_transfer_bank', 'k1' => 'c_refId', 'k2' => 'c_partnerRefId'],
            'stm'       => ['tbl' => 'external_stm_disbursement_transfer_bank', 'k1' => 'client_trans_reference', 'k2' => 'refIdTransfer'],
            'paylabs'   => ['tbl' => 'external_paylabs_disbursement_transfer_bank', 'k1' => 'c_partnerReferenceNo', 'k2' => 'c_referenceNo'],
            'paydgn'    => ['tbl' => 'external_paydgn_disbursement_transfer_bank', 'k1' => 'c_refId', 'k2' => 'c_partnerRefId'],
            'quantum'   => ['tbl' => 'external_quantum_bifast_transfer', 'k1' => 'c_requestId', 'k2' => 'c_transactionId']
        ];

        if (isset($tableMap[$ref_cashoutExternalId])) {
            $cfg = $tableMap[$ref_cashoutExternalId];
            $row = $this->db->get_where($cfg['tbl'], ['id' => $ref_cashoutExternalLogQrisMpmIdCreate])->row();
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

    public function get_datatables_handler($filters = [])
    {
        $this->load->library('datatables');
        $search_name = $filters['merchant'] ?? null;
        $date_from = $filters['date_from'] ?? null;
        $date_to = $filters['date_to'] ?? null;
        $search_transid = $filters['transid'] ?? null;
        $search_external_reff = $filters['external_reff'] ?? null;
        $search_channel = $filters['channel'] ?? null;
        $search_internal_channel = $filters['internal_channel'] ?? null;
        $search_status = $filters['search_status'] ?? null;

        $dt = $this->datatables->of('cashout_payment_bifast cpb')
            ->select('cpb.id, cpb.c_datetime, cpb.c_amount, cpb.c_fee, cpb.c_status, cpb.ref_merchantId, cpb.ref_cashoutId, cpb.ref_cashoutChannelId, m.c_name as merchant_name, c.c_invoiceNo, cpb.c_merchantTransactionId, cpb.ref_cashoutExternalId, cpb.c_accountNo, COALESCE(NULLIF(TRIM(mab.c_beneficiaryAccountName), ""), "-") as c_beneficiaryAccountName, cc.c_description as channel_name', FALSE)
            ->join('merchant m', 'm.id = cpb.ref_merchantId', 'left')
            ->join('cashout c', 'c.id = cpb.ref_cashoutId', 'left')
            ->join('cashout_channel cc', 'cc.id = cpb.ref_cashoutChannelId', 'left')
            ->join('merchant_account_bank mab', 'mab.c_beneficiaryAccountNo = cpb.c_accountNo AND mab.ref_merchantId = cpb.ref_merchantId', 'left')
            ->set_column_order([null, 'cpb.c_datetime', 'm.c_name', 'c.c_invoiceNo', 'cpb.ref_cashoutChannelId', 'cpb.c_amount', 'cpb.c_fee', 'cpb.c_status', null])
            ->set_column_search(['cpb.id', 'c.c_invoiceNo', 'm.c_name'])
            ->set_default_order(['cpb.id' => 'desc']);

        if ($search_name) $dt->where('cpb.ref_merchantId', $search_name);
        if ($search_channel) $dt->where('cpb.ref_cashoutChannelId', $search_channel);
        if ($search_status) $dt->where('cpb.c_status', $search_status);
        if ($date_from && $date_to) {
            $dt->where('cpb.c_datetime >=', date('Y-m-d', strtotime($date_from)) . ' 00:00:00')
               ->where('cpb.c_datetime <=', date('Y-m-d', strtotime($date_to)) . ' 23:59:59');
        }
        if ($search_transid) $dt->where('c.c_invoiceNo', $search_transid);

        return $dt->addColumn('no', function($row) {
                static $no = null;
                if ($no === null) $no = intval($this->input->post('start'));
                return ++$no;
            })
            ->make(true);
    }
}