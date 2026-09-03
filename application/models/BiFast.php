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

    private function _get_datatables_query($search_name = null, $date_from = null, $date_to = null, $search_transid = null, $search_external_reff = null, $search_channel = null, $search_status = null, $search_internal_channel = null, $only_ids = false, $count_only = false)
    {
        $this->db->query("SET SESSION max_execution_time = 30000");
        $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

        if ($count_only) {
            $this->db->select("count(DISTINCT cpb.ref_cashoutId) as total");
        } elseif ($only_ids) {
            $this->db->select("MAX(cpb.id) as id");
        } else {
            $this->db->select("cpb.id, cpb.c_datetime, cpb.c_merchantTransactionId, cpb.ref_cashoutExternalId, cpb.ref_cashoutChannelId, cpb.c_accountNo, cpb.c_amount, cpb.c_fee, cpb.c_status, cpb.ref_merchantId, cpb.ref_cashoutId, m.c_name AS name_merchant, c.c_invoiceNo, mab.c_beneficiaryAccountName, COALESCE(epb.c_responseBody, egb.c_responseBody) AS c_responseBody");
        }

        $this->db->from($this->table);
        $isInvoiceSearch = (preg_match('/^BIFAST|^INV/i', $searchValue));
        $sort_col = isset($_POST['order']['0']['column']) ? $this->column_order[$_POST['order']['0']['column']] : '';

        $joined_merchant = false;
        if ((!$only_ids && !$count_only) || $search_name || $searchValue || strpos($sort_col, 'm.') !== false) {
            $this->db->join('merchant m', 'm.id = cpb.ref_merchantId');
            $joined_merchant = true;
        }

        if ((!$only_ids && !$count_only) || $isInvoiceSearch || strpos($sort_col, 'c.') !== false) {
            $this->db->join('cashout c', 'c.id = cpb.ref_cashoutId');
        }

        if ((!$only_ids && !$count_only) || $searchValue || strpos($sort_col, 'mab.') !== false) {
            $this->db->join('merchant_account_bank mab', 'mab.c_beneficiaryAccountNo = cpb.c_accountNo AND mab.ref_cashoutChannelId = cpb.ref_cashoutChannelId AND mab.ref_merchantId = cpb.ref_merchantId', 'left');
        }

        if (!$only_ids && !$count_only) {
            $this->db->join('external_paylabs_disbursement_transfer_bank epb', 'epb.ref_cashoutPaymentBifastId = cpb.id', 'left');
            $this->db->join('external_gvconnect_snap_disbursement_transfer_bank egb', 'egb.ref_cashoutPaymentBifastId = cpb.id', 'left');
            $this->db->join('cashout_channel cc', 'cc.id = cpb.ref_cashoutChannelId', 'left');
        }

        if ($search_name) {
            $this->db->where('cpb.ref_merchantId', $search_name);
        }
        if ($date_from && $date_to) {
            $this->db->where('cpb.c_datetime >=', $date_from);
            $this->db->where('cpb.c_datetime <=', $date_to);
        }
        if ($search_transid && !$searchValue) {
            $this->db->where('cpb.c_merchantTransactionId', $search_transid);
        }
        if ($search_status) {
            $this->db->where('cpb.c_status', $search_status);
        }
        if ($search_internal_channel) {
            $this->db->where('cpb.ref_cashoutChannelId', $search_internal_channel);
        }
        if ($search_channel && !$searchValue) {
            $this->db->where('cpb.ref_cashoutExternalId', $search_channel);
        }

        if ($search_external_reff && !$searchValue) {
            $ext_map = [
                'paylabs'   => ['tbl' => 'external_paylabs_disbursement_transfer_bank epb', 'col' => 'epb.c_referenceNo'],
                'gvconnect' => ['tbl' => 'external_gvconnect_snap_disbursement_transfer_bank egb', 'col' => 'egb.c_partnerReferenceNo'],
                'ifp'       => ['tbl' => 'external_ifp_bifast_transfer_interbank eif', 'col' => 'eif.c_referenceNo'],
                'paydgn'    => ['tbl' => 'external_paydgn_disbursement_transfer_bank epd', 'col' => 'epd.c_refId']
            ];

            if (isset($ext_map[$search_channel])) {
                $target = $ext_map[$search_channel];
                if ($only_ids || $count_only) {
                    $this->db->join($target['tbl'], 'epb.ref_cashoutPaymentBifastId = cpb.id');
                }
                $this->db->where($target['col'], $search_external_reff);
            } else {
                if ($only_ids || $count_only) {
                    $this->db->join('external_paylabs_disbursement_transfer_bank epb', 'epb.ref_cashoutPaymentBifastId = cpb.id', 'left');
                    $this->db->join('external_gvconnect_snap_disbursement_transfer_bank egb', 'egb.ref_cashoutPaymentBifastId = cpb.id', 'left');
                    $this->db->join('external_ifp_bifast_transfer_interbank eif', 'eif.ref_cashoutPaymentBifastId = cpb.id', 'left');
                    $this->db->join('external_paydgn_disbursement_transfer_bank epd', 'epd.ref_cashoutPaymentBifastId = cpb.id', 'left');
                }
                $this->db->group_start();
                $this->db->where('epb.c_referenceNo', $search_external_reff);
                $this->db->or_where('egb.c_partnerReferenceNo', $search_external_reff);
                $this->db->or_where('eif.c_referenceNo', $search_external_reff);
                $this->db->or_where('epd.c_refId', $search_external_reff);
                $this->db->group_end();
            }
        }

        if ($searchValue) {
            $safeSearchValue = $this->db->escape_str($searchValue);
            static $cached_ids = null;
            static $cached_inv_ids = null;
            static $last_query = null;

            if ($cached_ids === null || $last_query !== $searchValue) {
                $last_query = $searchValue;
                $matching_ids = [-1];
                $matching_inv_ids = [-1];
                $op = (strlen($searchValue) >= 15) ? '=' : 'LIKE';
                $val = (strlen($searchValue) >= 15) ? "'$safeSearchValue'" : "'$safeSearchValue%'";

                $cpb_res = $this->db->query("SELECT id FROM cashout_payment_bifast WHERE c_merchantTransactionId $op $val OR c_accountNo $op $val LIMIT 100")->result();
                if (!empty($cpb_res)) {
                    $matching_ids = array_merge($matching_ids, array_column($cpb_res, 'id'));
                }

                $mab_res = $this->db->query("SELECT cpb.id FROM cashout_payment_bifast cpb JOIN merchant_account_bank mab ON mab.c_beneficiaryAccountNo = cpb.c_accountNo AND mab.ref_cashoutChannelId = cpb.ref_cashoutChannelId AND mab.ref_merchantId = cpb.ref_merchantId WHERE mab.c_beneficiaryAccountName LIKE '$safeSearchValue%' LIMIT 100")->result();
                if (!empty($mab_res)) {
                    $matching_ids = array_merge($matching_ids, array_column($mab_res, 'id'));
                }

                if (count($matching_ids) <= 1 || strlen($searchValue) < 15) {
                    if (strlen($searchValue) >= 4) {
                        $inv_res = $this->db->query("SELECT id FROM cashout WHERE c_invoiceNo $op $val LIMIT 50")->result();
                        if (!empty($inv_res)) {
                            $matching_inv_ids = array_merge($matching_inv_ids, array_column($inv_res, 'id'));
                        }
                    }
                }

                if (is_numeric($searchValue) && strlen($searchValue) < 15) {
                    $matching_ids[] = (int) $searchValue;
                }

                $cached_ids = array_unique($matching_ids);
                $cached_inv_ids = array_unique($matching_inv_ids);
            }

            if (count($cached_ids) > 1 || count($cached_inv_ids) > 1) {
                $this->db->group_start();
                if (count($cached_ids) > 1) {
                    $this->db->where_in('cpb.id', $cached_ids);
                }
                if (count($cached_inv_ids) > 1) {
                    if (count($cached_ids) > 1) {
                        $this->db->or_where_in('cpb.ref_cashoutId', $cached_inv_ids);
                    } else {
                        $this->db->where_in('cpb.ref_cashoutId', $cached_inv_ids);
                    }
                }
                $this->db->group_end();
            } else {
                if (strlen($searchValue) >= 3) {
                    if (!$joined_merchant) {
                        $this->db->join('merchant m', 'cpb.ref_merchantId = m.id', 'left');
                    }
                    $this->db->like('m.c_name', $searchValue, 'both');
                } else {
                    $this->db->where('1=0', null, false);
                }
            }
        }

        if (!$count_only) {
            $this->db->group_by('cpb.ref_cashoutId');
        }

        if (!$count_only) {
            if (isset($_POST['order'])) {
                $sort_col = $this->column_order[$_POST['order']['0']['column']];
                if ($only_ids && ($sort_col == 'cpb.id' || $sort_col == 'id')) {
                    $this->db->order_by('id', $_POST['order']['0']['dir'], false);
                } elseif ($sort_col) {
                    $this->db->order_by($sort_col, $_POST['order']['0']['dir']);
                }
            } elseif (isset($this->order)) {
                $order = $this->order;
                $key = key($order);
                if ($only_ids && ($key == 'cpb.id' || $key == 'id')) {
                    $this->db->order_by('id', $order[$key], false);
                } else {
                    $this->db->order_by($key, $order[$key]);
                }
            }
        }
    }

    public function get_datatables($search_name = null, $date_from = null, $date_to = null, $search_transid = null, $search_external_reff = null, $search_channel = null, $search_status = null, $search_internal_channel = null)
    {
        $this->_get_datatables_query($search_name, $date_from, $date_to, $search_transid, $search_external_reff, $search_channel, $search_status, $search_internal_channel, true);
        if ($_POST['length'] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        $id_results = $query->result();

        if (empty($id_results)) {
            return [];
        }

        $ids = array_column($id_results, 'id');
        $this->db->select("cpb.*, m.c_name AS name_merchant, m.c_merchantLevel, c.c_invoiceNo, mab.c_beneficiaryAccountName,
                          COALESCE(epb.c_responseBody, egb.c_responseBody, eif.c_responseBody, epd.c_responseBody) AS c_responseBody, cc.c_description AS channel_description", false);
        $this->db->from($this->table);
        $this->db->join('cashout c', 'c.id = cpb.ref_cashoutId', 'left');
        $this->db->join('merchant m', 'm.id = cpb.ref_merchantId', 'left');
        $this->db->join('merchant_account_bank mab', 'mab.c_beneficiaryAccountNo = cpb.c_accountNo AND mab.ref_cashoutChannelId = cpb.ref_cashoutChannelId AND mab.ref_merchantId = cpb.ref_merchantId', 'left');
        $this->db->join('external_paylabs_disbursement_transfer_bank epb', 'epb.ref_cashoutPaymentBifastId = cpb.id', 'left');
        $this->db->join('external_gvconnect_snap_disbursement_transfer_bank egb', 'egb.ref_cashoutPaymentBifastId = cpb.id', 'left');
        $this->db->join('external_ifp_bifast_transfer_interbank eif', 'eif.ref_cashoutPaymentBifastId = cpb.id', 'left');
        $this->db->join('external_paydgn_disbursement_transfer_bank epd', 'epd.ref_cashoutPaymentBifastId = cpb.id', 'left');
        $this->db->join('cashout_channel cc', 'cc.id = cpb.ref_cashoutChannelId', 'left');
        $this->db->where_in('cpb.id', $ids);
        $this->db->order_by('cpb.id', 'desc');

        return $this->db->get()->result();
    }

    public function count_filtered($search_name = null, $date_from = null, $date_to = null, $search_transid = null, $search_external_reff = null, $search_channel = null, $search_status = null, $search_internal_channel = null)
    {
        $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
        $is_filtered = $search_name || $date_from || $date_to || $search_transid || $search_external_reff || $search_channel || $search_status || $search_internal_channel || (!empty($searchValue));

        if (!$is_filtered) {
            return $this->count_all_dt($search_name, $date_from, $date_to);
        }

        $this->_get_datatables_query($search_name, $date_from, $date_to, $search_transid, $search_external_reff, $search_channel, $search_status, $search_internal_channel, false, true);
        $query = $this->db->get();
        if (!is_object($query) || $query->num_rows() == 0) {
            return 0;
        }
        return $query->row()->total;
    }

    public function count_all_dt($search_name = null, $date_from = null, $date_to = null)
    {
        if (self::$cached_total !== null) {
            return self::$cached_total;
        }

        if (!$search_name && !$date_from && !$date_to) {
            $q = $this->db->query("SHOW TABLE STATUS LIKE 'cashout_payment_bifast'");
            $res = $q->row();
            if ($res && isset($res->Rows) && $res->Rows > 10000) {
                self::$cached_total = (int) $res->Rows;
                return self::$cached_total;
            }
        }

        $this->db->select("count(DISTINCT cpb.ref_cashoutId) as total");
        $this->db->from($this->table);
        if ($search_name) {
            $this->db->where('cpb.ref_merchantId', $search_name);
        }
        if ($date_from && $date_to) {
            $this->db->where('cpb.c_datetime >=', $date_from);
            $this->db->where('cpb.c_datetime <=', $date_to);
        }
        $query = $this->db->get();
        self::$cached_total = $query->row() ? (int) $query->row()->total : 0;
        return self::$cached_total;
    }

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

        $date_from_query = !empty($date_from) ? date('Ymd', strtotime($date_from)) . "000001" : null;
        $date_to_query = !empty($date_to) ? date('Ymd', strtotime($date_to)) . "235959" : null;

        $list = $this->get_datatables($search_name, $date_from_query, $date_to_query, $search_transid, $search_external_reff, $search_channel, $search_status, $search_internal_channel);
        $searchValue = $this->input->post('search')['value'];
        $is_filtered = $search_name || $date_from || $date_to || $search_transid || $search_external_reff || $search_channel || $search_status || $search_internal_channel || (!empty($searchValue));

        $recordsTotal = $this->count_all_dt($search_name, $date_from_query, $date_to_query);
        $recordsFiltered = $is_filtered ? $this->count_filtered($search_name, $date_from_query, $date_to_query, $search_transid, $search_external_reff, $search_channel, $search_status, $search_internal_channel) : $recordsTotal;

        $original_start = $_POST['start'];
        $_POST['start'] = 0;

        $output = $this->datatables->of($this->table)
            ->set_recordsTotal($recordsTotal)
            ->set_recordsFiltered($recordsFiltered)
            ->set_data($list)
            ->addColumn('no', function ($row) use ($original_start) {
                static $no = null;
                if ($no === null) {
                    $no = intval($original_start);
                }
                return ++$no;
            })
            ->make(false);

        $_POST['start'] = $original_start;
        $output['draw'] = intval($this->input->post('draw'));

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($output));
    }
}