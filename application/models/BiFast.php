<?php defined('BASEPATH') OR exit('No direct script access allowed');

class BiFast extends CI_Model {

    // DataTables variables
    var $table = 'cashout_payment_bifast cpb';
    var $column_order = array(null, 'm.c_name', 'cpb.c_datetime', 'cpb.c_merchantTransactionId', 'c.c_invoiceNo', 'cpb.ref_cashoutChannelId', 'cpb.c_accountNo', 'mab.c_beneficiaryAccountName', 'cpb.c_amount', 'cpb.c_fee', 'cpb.c_status', null, null);
    var $column_search = array('cpb.id', 'm.c_name', 'cpb.c_merchantTransactionId', 'cpb.c_accountNo', 'mab.c_beneficiaryAccountName');
    private static $cached_total = null;
    var $order = array('cpb.id' => 'desc');

    private function _get_datatables_query($search_name = null, $date_from = null, $date_to = null, $search_transid = null, $search_external_reff = null, $search_channel = null, $search_status = null, $search_internal_channel = null, $only_ids = false, $count_only = false)
    {
        // Emergency safeguard
        $this->db->query("SET SESSION max_execution_time = 30000");
        
        $searchValue = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';

        if ($count_only) {
            $this->db->select("count(DISTINCT cpb.ref_cashoutId) as total");
        } else if ($only_ids) {
            $this->db->select("MAX(cpb.id) as id");
        } else {
            $this->db->select("cpb.id, cpb.c_datetime, cpb.c_merchantTransactionId, cpb.ref_cashoutChannelId, cpb.c_accountNo, cpb.c_amount, cpb.c_fee, cpb.c_status, cpb.ref_merchantId, cpb.ref_cashoutId, m.c_name AS name_merchant, c.c_invoiceNo, mab.c_beneficiaryAccountName,
                               COALESCE(epb.c_responseBody, egb.c_responseBody) AS c_responseBody");
        }
        
        $this->db->from($this->table);
        
        // Essential joins for base data
        $isInvoiceSearch = (preg_match('/^BIFAST|^INV/i', $searchValue));
        $sort_col = isset($_POST['order']['0']['column']) ? $this->column_order[$_POST['order']['0']['column']] : '';

        // Always join merchant if we are searching, filtering by it, or sorting by it
        $joined_merchant = false;
        if (!$only_ids && !$count_only || $search_name || $searchValue || strpos($sort_col, 'm.') !== false) {
            $this->db->join('merchant m', 'm.id = cpb.ref_merchantId');
            $joined_merchant = true;
        }
        
        // Join cashout only if searching for invoice prefix, sorting by it, or getting full data
        if (!$only_ids && !$count_only || $isInvoiceSearch || strpos($sort_col, 'c.') !== false) {
            $this->db->join('cashout c', 'c.id = cpb.ref_cashoutId');
        }

        // Join MAB only if searching, sorting by it, or getting full data
        if (!$only_ids && !$count_only || $searchValue || strpos($sort_col, 'mab.') !== false) {
            $this->db->join('merchant_account_bank mab', 'mab.c_beneficiaryAccountNo = cpb.c_accountNo AND mab.ref_cashoutChannelId = cpb.ref_cashoutChannelId AND mab.ref_merchantId = cpb.ref_merchantId', 'left');
        }

        // Join external tables only if getting full data (not for ID-only or count queries)
        // Specific joins for filtering are handled below in filter logic
        if (!$only_ids && !$count_only) {
            $this->db->join('external_paylabs_disbursement_transfer_bank epb', 'epb.ref_cashoutPaymentBifastId = cpb.id', 'left');
            $this->db->join('external_gvconnect_snap_disbursement_transfer_bank egb', 'egb.ref_cashoutPaymentBifastId = cpb.id', 'left');
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
        
        // Handle External Channel and External Reff ID filters
        if (($search_channel || $search_external_reff) && !$searchValue) {
            if ($search_channel == "paylabs") {
                // Channel selected: paylabs
                if ($search_external_reff) {
                    // Channel + Reff ID: join and filter by reff ID
                    if ($only_ids || $count_only) $this->db->join('external_paylabs_disbursement_transfer_bank epb', 'epb.ref_cashoutPaymentBifastId = cpb.id');
                    $this->db->where('epb.c_referenceNo', $search_external_reff);
                } else {
                    // Channel only: filter by ref_cashoutExternalId
                    $this->db->where('cpb.ref_cashoutExternalId', $search_channel);
                }
            } else if ($search_channel == "gvconnect") {
                // Channel selected: gvconnect
                if ($search_external_reff) {
                    // Channel + Reff ID: join and filter by reff ID
                    if ($only_ids || $count_only) $this->db->join('external_gvconnect_snap_disbursement_transfer_bank egb', 'egb.ref_cashoutPaymentBifastId = cpb.id');
                    $this->db->where('egb.c_partnerReferenceNo', $search_external_reff);
                } else {
                    // Channel only: filter by ref_cashoutExternalId
                    $this->db->where('cpb.ref_cashoutExternalId', $search_channel);
                }
            } else if ($search_channel == "ifp") {
                // Channel selected: ifp
                if ($search_external_reff) {
                    // Channel + Reff ID: join and filter by reff ID
                    if ($only_ids || $count_only) $this->db->join('external_ifp_bifast_transfer_interbank eif', 'eif.ref_cashoutPaymentBifastId = cpb.id');
                    $this->db->where('eif.c_referenceNo', $search_external_reff);
                } else {
                    // Channel only: filter by ref_cashoutExternalId
                    $this->db->where('cpb.ref_cashoutExternalId', $search_channel);
                }
            } else if ($search_channel == "paydgn") {
                // Channel selected: paydgn
                if ($search_external_reff) {
                    // Channel + Reff ID: join and filter by reff ID
                    if ($only_ids || $count_only) $this->db->join('external_paydgn_disbursement_transfer_bank epd', 'epd.ref_cashoutPaymentBifastId = cpb.id');
                    $this->db->where('epd.c_refId', $search_external_reff);
                } else {
                    // Channel only: filter by ref_cashoutExternalId
                    $this->db->where('cpb.ref_cashoutExternalId', $search_channel);
                }
            } else if ($search_external_reff) {
                // Reff ID only without channel: should not happen (handled in controller with alert)
                // But if it reaches here, search in all tables with OR
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
            
            // CACHING LOGIC: Prevent redundant scans across multiple calls (count + fetch)
            static $cached_ids = null;
            static $cached_inv_ids = null;
            static $last_query = null;

            if ($cached_ids === null || $last_query !== $searchValue) {
                $last_query = $searchValue;
                $matching_ids = [-1];
                $matching_inv_ids = [-1];

                $op = (strlen($searchValue) >= 15) ? '=' : 'LIKE';
                $val = (strlen($searchValue) >= 15) ? "'$safeSearchValue'" : "'$safeSearchValue%'";

                // 1. Priority: Check technical ID columns (Merchant Trans ID & Account No) & Beneficiary Name
                $cpb_res = $this->db->query("SELECT id FROM cashout_payment_bifast WHERE c_merchantTransactionId $op $val OR c_accountNo $op $val LIMIT 100")->result();
                if (!empty($cpb_res)) $matching_ids = array_merge($matching_ids, array_column($cpb_res, 'id'));

                $mab_res = $this->db->query("SELECT cpb.id FROM cashout_payment_bifast cpb JOIN merchant_account_bank mab ON mab.c_beneficiaryAccountNo = cpb.c_accountNo AND mab.ref_cashoutChannelId = cpb.ref_cashoutChannelId AND mab.ref_merchantId = cpb.ref_merchantId WHERE mab.c_beneficiaryAccountName LIKE '$safeSearchValue%' LIMIT 100")->result();
                if (!empty($mab_res)) $matching_ids = array_merge($matching_ids, array_column($mab_res, 'id'));

                // 2. Check Invoice Number (Only if specific ID not found)
                if (count($matching_ids) <= 1 || strlen($searchValue) < 15) {
                    if (strlen($searchValue) >= 4) {
                        $inv_q = "SELECT id FROM cashout WHERE c_invoiceNo $op $val ";
                        $inv_res = $this->db->query($inv_q . " LIMIT 50")->result();
                        if (!empty($inv_res)) $matching_inv_ids = array_merge($matching_inv_ids, array_column($inv_res, 'id'));
                    }
                }

                // 3. Direct PK match
                if (is_numeric($searchValue) && strlen($searchValue) < 15) {
                    $matching_ids[] = (int)$searchValue;
                }

                $cached_ids = array_unique($matching_ids);
                $cached_inv_ids = array_unique($matching_inv_ids);
            }

            // 2. Decide strategy
            if (count($cached_ids) > 1 || count($cached_inv_ids) > 1) {
                $this->db->group_start();
                if (count($cached_ids) > 1) $this->db->where_in('cpb.id', $cached_ids);
                if (count($cached_inv_ids) > 1) {
                    if (count($cached_ids) > 1) $this->db->or_where_in('cpb.ref_cashoutId', $cached_inv_ids);
                    else $this->db->where_in('cpb.ref_cashoutId', $cached_inv_ids);
                }
                $this->db->group_end();
            } else {
                // FALLBACK: Name search if no specific ID matched (min 3 chars)
                if (strlen($searchValue) >= 3) {
                    // Ensure joins for name search fallback
                    if (!$joined_merchant) {
                        $this->db->join('merchant m', 'cpb.ref_merchantId = m.id', 'left');
                        $joined_merchant = true;
                    }
                    $this->db->like('m.c_name', $searchValue, 'both');
                } else {
                    $this->db->where('1=0', NULL, FALSE);
                }
            }
        }

        // Deduplication
        // Deduplication: Always group by invoice ID
        if (!$count_only) {
            $this->db->group_by('cpb.ref_cashoutId');
        }

        if (!$count_only) {
            if (isset($_POST['order'])) {
                $sort_col = $this->column_order[$_POST['order']['0']['column']];
                if ($only_ids && ($sort_col == 'cpb.id' || $sort_col == 'id')) {
                    $this->db->order_by('id', $_POST['order']['0']['dir'], FALSE);
                } else if ($sort_col) {
                    $this->db->order_by($sort_col, $_POST['order']['0']['dir']);
                }
            } else if (isset($this->order)) {
                $order = $this->order;
                $key = key($order);
                if ($only_ids && ($key == 'cpb.id' || $key == 'id')) {
                    $this->db->order_by('id', $order[$key], FALSE);
                } else {
                    $this->db->order_by($key, $order[$key]);
                }
            }
        }
    }

    public function get_datatables($search_name = null, $date_from = null, $date_to = null, $search_transid = null, $search_external_reff = null, $search_channel = null, $search_status = null, $search_internal_channel = null)
    {
        // STEP 1: Get only IDs for the current page (Fast query)
        $this->_get_datatables_query($search_name, $date_from, $date_to, $search_transid, $search_external_reff, $search_channel, $search_status, $search_internal_channel, true);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        $id_results = $query->result();
        
        if (empty($id_results)) return array();
        
        $ids = array_column($id_results, 'id');
        
        // STEP 2: Fetch full data for only these specific IDs
        $this->db->select("cpb.*, m.c_name AS name_merchant, m.c_merchantLevel, c.c_invoiceNo, mab.c_beneficiaryAccountName,
                           COALESCE(epb.c_responseBody, egb.c_responseBody, eif.c_responseBody, epd.c_responseBody) AS c_responseBody", FALSE);
        $this->db->from($this->table);
        $this->db->join('cashout c', 'c.id = cpb.ref_cashoutId', 'left');
        $this->db->join('merchant m', 'm.id = cpb.ref_merchantId', 'left');
        $this->db->join('merchant_account_bank mab', 'mab.c_beneficiaryAccountNo = cpb.c_accountNo AND mab.ref_cashoutChannelId = cpb.ref_cashoutChannelId AND mab.ref_merchantId = cpb.ref_merchantId', 'left');
        $this->db->join('external_paylabs_disbursement_transfer_bank epb', 'epb.ref_cashoutPaymentBifastId = cpb.id', 'left');
        $this->db->join('external_gvconnect_snap_disbursement_transfer_bank egb', 'egb.ref_cashoutPaymentBifastId = cpb.id', 'left');
        $this->db->join('external_ifp_bifast_transfer_interbank eif', 'eif.ref_cashoutPaymentBifastId = cpb.id', 'left');
        $this->db->join('external_paydgn_disbursement_transfer_bank epd', 'epd.ref_cashoutPaymentBifastId = cpb.id', 'left');
        
        $this->db->where_in('cpb.id', $ids);
        
        // Use stable default ordering only; ignore client-side column sorting requests.
        $this->db->order_by('cpb.id', 'desc');
        
        $query = $this->db->get();
        return $query->result();
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
        if (!is_object($query) || $query->num_rows() == 0) return 0;
        return $query->row()->total;
    }

    public function count_all_dt($search_name = null, $date_from = null, $date_to = null)
    {
        if (self::$cached_total !== null) return self::$cached_total;

        // If no filters, use the fastest possible estimate from metadata (Instant)
        if (!$search_name && !$date_from && !$date_to) {
            $q = $this->db->query("SHOW TABLE STATUS LIKE 'cashout_payment_bifast'");
            $res = $q->row();
            if ($res && isset($res->Rows) && $res->Rows > 10000) {
                self::$cached_total = (int)$res->Rows;
                return self::$cached_total;
            }
        }

        $this->db->select("count(DISTINCT cpb.ref_cashoutId) as total");
        $this->db->from($this->table);
        if ($search_name) $this->db->where('cpb.ref_merchantId', $search_name);
        if ($date_from && $date_to) {
            $this->db->where('cpb.c_datetime >=', $date_from);
            $this->db->where('cpb.c_datetime <=', $date_to);
        }
        $query = $this->db->get();
        self::$cached_total = $query->row() ? (int)$query->row()->total : 0;
        return self::$cached_total;
    }


    public function get_bifast($limit, $start, $date_from = null, $date_to = null, $search_name_bifast = null, $search_transid_bifast = null, $search_external_reff_id = null, $search_channel_bifast = null, $search_status_transaction_bifast = null)
    {
        $query = " FROM cashout_payment_bifast
                   JOIN cashout 
                        ON cashout.id = cashout_payment_bifast.ref_cashoutId
                   JOIN merchant 
                        ON merchant.id = cashout_payment_bifast.ref_merchantId
                   LEFT JOIN merchant_account_bank ON merchant_account_bank.c_beneficiaryAccountNo = cashout_payment_bifast.c_accountNo
                            AND merchant_account_bank.ref_cashoutChannelId = cashout_payment_bifast.ref_cashoutChannelId
                            AND merchant_account_bank.ref_merchantId = cashout_payment_bifast.ref_merchantId
                   LEFT JOIN external_paylabs_disbursement_transfer_bank 
                        ON external_paylabs_disbursement_transfer_bank.ref_cashoutPaymentBifastId = cashout_payment_bifast.id 
                   LEFT JOIN external_gvconnect_snap_disbursement_transfer_bank 
                        ON external_gvconnect_snap_disbursement_transfer_bank.ref_cashoutPaymentBifastId = cashout_payment_bifast.id 
                   LEFT JOIN external_ifp_bifast_transfer_interbank 
                        ON external_ifp_bifast_transfer_interbank.ref_cashoutPaymentBifastId = cashout_payment_bifast.id 
                   LEFT JOIN external_paydgn_disbursement_transfer_bank 
                        ON external_paydgn_disbursement_transfer_bank.ref_cashoutPaymentBifastId = cashout_payment_bifast.id 
                   WHERE 1=1
                 ";
                 

        // Optimized: Create a lean query for counting total rows without unnecessary joins
        $lean_query = " FROM cashout_payment_bifast WHERE 1=1 ";
        if (!empty($date_from) && !empty($date_to)) {
            $lean_query .= " and cashout_payment_bifast.c_datetime >= '$date_from' AND cashout_payment_bifast.c_datetime <= '$date_to'";
        }
        if ($search_name_bifast) {
            $lean_query .= " AND cashout_payment_bifast.ref_merchantId = $search_name_bifast";
        }
        if (!empty($search_transid_bifast)) {
            $lean_query .= " AND cashout_payment_bifast.c_merchantTransactionId ='$search_transid_bifast'";
        }
        if (!empty($search_status_transaction_bifast)) {
            $lean_query .= " AND cashout_payment_bifast.c_status ='$search_status_transaction_bifast'";
        }

        $total_query = "SELECT COUNT(*) as total_rows " . $lean_query;
        $total_rows = $this->db->query($total_query)->row()->total_rows;

        $data_query = "SELECT 
                merchant.c_name AS name_merchant,
                cashout_payment_bifast.id, 
                cashout_payment_bifast.ref_merchantId,
                cashout_payment_bifast.c_datetime, 
                cashout.c_invoiceNo, 
                cashout_payment_bifast.c_merchantTransactionId,
                cashout_payment_bifast.ref_cashoutChannelId, 
                cashout_payment_bifast.c_amount, 
                cashout_payment_bifast.c_fee, 
                cashout_payment_bifast.c_status,
                cashout_payment_bifast.c_feeExternal,
                cashout_payment_bifast.c_accountNo,
                cashout_payment_bifast.ref_cashoutExternalId,
                cashout_payment_bifast.ref_cashoutExternalLogBifastId,
                merchant_account_bank.c_beneficiaryAccountName,
                COALESCE(
                    external_paylabs_disbursement_transfer_bank.c_responseBody,
                    external_gvconnect_snap_disbursement_transfer_bank.c_responseBody,
                    external_ifp_bifast_transfer_interbank.c_responseBody,
                    external_paydgn_disbursement_transfer_bank.c_responseBody
                ) AS c_responseBody " . $query . " Order BY cashout_payment_bifast.id DESC LIMIT $start, $limit";

        $data = $this->db->query($data_query)->result();
        return [
        'total_rows' => $total_rows,
        'data' => $data
        ];
    }
    
    

    public function get_summary($date_from, $date_to, $refMerchantId = null) {
        // $this->db->select('COUNT(id) as qty, SUM(c_amount) as amount, SUM(c_fee) as fee, SUM(c_feeExternal) as fee_external');
        $query = "SELECT COUNT(a.id) as qty, SUM(a.c_amount) as amount, SUM(a.c_fee) as fee, SUM(a.c_feeExternal) as fee_external
        FROM cashout_payment_bifast a
        WHERE a.c_datetime  >= '$date_from' AND a.c_datetime <= '$date_to'";

        if (!empty($refMerchantId)) {
            $query .= " AND a.ref_merchantId = '$refMerchantId'";
        }

        return $this->db->query($query)->result_array();
    }

    public function getBifastDetail($id)
    {
        $query = "SELECT cashout_payment_bifast.*, cashout.*, merchant.c_name as name_merchant, merchant_account_bank.c_beneficiaryAccountName
        FROM cashout_payment_bifast 
        JOIN cashout ON cashout.id = cashout_payment_bifast.ref_cashoutId
        JOIN merchant ON merchant.id = cashout_payment_bifast.ref_merchantId
        LEFT JOIN merchant_account_bank ON merchant_account_bank.c_beneficiaryAccountNo = cashout_payment_bifast.c_accountNo
                AND merchant_account_bank.ref_cashoutChannelId = cashout_payment_bifast.ref_cashoutChannelId
                AND merchant_account_bank.ref_merchantId = cashout_payment_bifast.ref_merchantId
        WHERE cashout_payment_bifast.id = ?";

        return $this->db->query($query, array($id))->result_array();
    }
    
    public function get_merchant(){
        $query = "select id, c_name from merchant ";
        return $this->db->query($query)->result();
    }

    public function get_channels(){
        $query = "SELECT c_cashoutExternalId FROM cashout_external_x_channel  
                WHERE c_cashoutChannelGroup = 'bifast' 
                GROUP BY c_cashoutExternalId  ";
        return $this->db->query($query)->result();
    }

    public function get_internal_channels(){
        $query = "SELECT id, c_description FROM cashout_channel 
                WHERE c_channelGroup = 'bifast' 
                ORDER BY c_description ASC";
        return $this->db->query($query)->result();
    }

    public function getDataBiFastChannelExternal($ref_cashoutExternalId, $ref_cashoutExternalLogQrisMpmIdCreate) {
        
        $TransactionIdExternal1         = null;
        $TransactionIdExternal2         = null;

        $DatetimeRequest                = null;
        $RequestHeader                  = null;
        $RequestBody                    = null;

        $DatetimeResponse               = null;
        $ResponseHeader                 = null;
        $ResponseBody                   = null;

        if ($ref_cashoutExternalId == 'gvconnect') {

            $qtxt1_1    = "SELECT c_partnerReferenceNo, c_referenceNo, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_gvconnect_snap_disbursement_transfer_bank WHERE id='$ref_cashoutExternalLogQrisMpmIdCreate'";
            $query1_1   = $this->db->query($qtxt1_1);
            $result1_1  = $query1_1->num_rows() ? $query1_1->row() : false;
            if($result1_1) {

                $TransactionIdExternal1     = $result1_1->c_partnerReferenceNo;
                $TransactionIdExternal2     = $result1_1->c_referenceNo;
                
                $DatetimeRequest            = $result1_1->c_datetimeRequest;
                $RequestHeader              = $result1_1->c_requestHeader;
                $RequestBody                = $result1_1->c_requestBody;

                $DatetimeResponse           = $result1_1->c_datetimeResponse;
                $ResponseHeader             = $result1_1->c_responseHeader;
                $ResponseBody               = $result1_1->c_responseBody;

            }

        } else if ($ref_cashoutExternalId == 'ifp') {

            $qtxt1_1    = "SELECT c_partnerReferenceNo, c_referenceNo, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_ifp_bifast_transfer_interbank WHERE id='$ref_cashoutExternalLogQrisMpmIdCreate'";
            $query1_1   = $this->db->query($qtxt1_1);
            $result1_1  = $query1_1->num_rows() ? $query1_1->row() : false;
            if($result1_1) {
                
                $TransactionIdExternal1     = $result1_1->c_partnerReferenceNo;
                $TransactionIdExternal2     = $result1_1->c_referenceNo;

                $DatetimeRequest            = $result1_1->c_datetimeRequest;
                $RequestHeader              = $result1_1->c_requestHeader;
                $RequestBody                = $result1_1->c_requestBody;

                $DatetimeResponse           = $result1_1->c_datetimeResponse;
                $ResponseHeader             = $result1_1->c_responseHeader;
                $ResponseBody               = $result1_1->c_responseBody;
            }

        } else if($ref_cashoutExternalId == 'inacash'){
            $qtxt1_1    = "SELECT c_refId, c_partnerRefId, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_inacash_disbursement_transfer_bank WHERE id='$ref_cashoutExternalLogQrisMpmIdCreate'";
            $query1_1   = $this->db->query($qtxt1_1);
            $result1_1  = $query1_1->num_rows() ? $query1_1->row() : false;
            if($result1_1) {
                
                $TransactionIdExternal1     = $result1_1->c_refId;
                $TransactionIdExternal2     = $result1_1->c_partnerRefId;

                $DatetimeRequest            = $result1_1->c_datetimeRequest;
                $RequestHeader              = $result1_1->c_requestHeader;
                $RequestBody                = $result1_1->c_requestBody;

                $DatetimeResponse           = $result1_1->c_datetimeResponse;
                $ResponseHeader             = $result1_1->c_responseHeader;
                $ResponseBody               = $result1_1->c_responseBody;
            }
        } else if ($ref_cashoutExternalId == 'stm') {

            $qtxt1_1    = "SELECT client_trans_reference, refIdTransfer, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_stm_disbursement_transfer_bank WHERE id='$ref_cashoutExternalLogQrisMpmIdCreate'";
            $query1_1   = $this->db->query($qtxt1_1);
            $result1_1  = $query1_1->num_rows() ? $query1_1->row() : false;
            if($result1_1) {
                
                $TransactionIdExternal1     = $result1_1->client_trans_reference;
                $TransactionIdExternal2     = $result1_1->refIdTransfer;

                $DatetimeRequest            = $result1_1->c_datetimeRequest;
                $RequestHeader              = $result1_1->c_requestHeader;
                $RequestBody                = $result1_1->c_requestBody;

                $DatetimeResponse           = $result1_1->c_datetimeResponse;
                $ResponseHeader             = $result1_1->c_responseHeader;
                $ResponseBody               = $result1_1->c_responseBody;
            }

        } else if ($ref_cashoutExternalId == 'paylabs') {

            $qtxt1_1    = "SELECT c_partnerReferenceNo, c_referenceNo, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_paylabs_disbursement_transfer_bank WHERE id='$ref_cashoutExternalLogQrisMpmIdCreate'";
            $query1_1   = $this->db->query($qtxt1_1);
            $result1_1  = $query1_1->num_rows() ? $query1_1->row() : false;
            if($result1_1) {
                
                $TransactionIdExternal1     = $result1_1->c_partnerReferenceNo;
                $TransactionIdExternal2     = $result1_1->c_referenceNo;

                $DatetimeRequest            = $result1_1->c_datetimeRequest;
                $RequestHeader              = $result1_1->c_requestHeader;
                $RequestBody                = $result1_1->c_requestBody;

                $DatetimeResponse           = $result1_1->c_datetimeResponse;
                $ResponseHeader             = $result1_1->c_responseHeader;
                $ResponseBody               = $result1_1->c_responseBody;
            }

        } else if ($ref_cashoutExternalId == 'paydgn') {

            $qtxt1_1    = "SELECT c_refId, c_partnerRefId, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_paydgn_disbursement_transfer_bank WHERE id='$ref_cashoutExternalLogQrisMpmIdCreate'";
            $query1_1   = $this->db->query($qtxt1_1);
            $result1_1  = $query1_1->num_rows() ? $query1_1->row() : false;
            if($result1_1) {
                
                $TransactionIdExternal1     = $result1_1->c_refId;
                $TransactionIdExternal2     = $result1_1->c_partnerRefId;

                $DatetimeRequest            = $result1_1->c_datetimeRequest;
                $RequestHeader              = $result1_1->c_requestHeader;
                $RequestBody                = $result1_1->c_requestBody;

                $DatetimeResponse           = $result1_1->c_datetimeResponse;
                $ResponseHeader             = $result1_1->c_responseHeader;
                $ResponseBody               = $result1_1->c_responseBody;
            }

        } else if ($ref_cashoutExternalId == 'quantum') {

            $qtxt1_1    = "SELECT c_requestId, c_transactionId, c_datetimeRequest, c_requestHeader, c_requestBody, c_datetimeResponse, c_responseHeader, c_responseBody FROM external_quantum_bifast_transfer WHERE id='$ref_cashoutExternalLogQrisMpmIdCreate'";
            $query1_1   = $this->db->query($qtxt1_1);
            $result1_1  = $query1_1->num_rows() ? $query1_1->row() : false;
            if($result1_1) {
                
                $TransactionIdExternal1     = $result1_1->c_requestId;
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
        $search_status = $filters['status'] ?? null;

        // Format dates for query
        $date_from_query = !empty($date_from) ? date('Ymd', strtotime($date_from)) . "000001" : null;
        $date_to_query = !empty($date_to) ? date('Ymd', strtotime($date_to)) . "235959" : null;

        // Optimized Fetch (Two-Step Lookup)
        $list = $this->get_datatables($search_name, $date_from_query, $date_to_query, $search_transid, $search_external_reff, $search_channel, $search_status, $search_internal_channel);
        
        $searchValue = $this->input->post('search')['value'];
        $is_filtered = $search_name || $date_from || $date_to || $search_transid || $search_external_reff || $search_channel || $search_status || $search_internal_channel || (!empty($searchValue));

        $recordsTotal = $this->count_all_dt($search_name, $date_from_query, $date_to_query);
        $recordsFiltered = $is_filtered ? $this->count_filtered($search_name, $date_from_query, $date_to_query, $search_transid, $search_external_reff, $search_channel, $search_status, $search_internal_channel) : $recordsTotal;

        // Trick the library to NOT re-slice our already-paginated $list
        $original_start = $_POST['start'];
        $_POST['start'] = 0; 

        $output = $this->datatables->of($this->table)
            ->set_recordsTotal($recordsTotal)
            ->set_recordsFiltered($recordsFiltered)
            ->set_data($list)
            ->addColumn('no', function($row) use ($original_start) {
                static $no = null;
                if ($no === null) $no = intval($original_start);
                return ++$no;
            })
            ->make(false);
            
        // Restore original start and output JSON
        $_POST['start'] = $original_start;
        $output['draw'] = intval($this->input->post('draw'));
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($output));
    }
}
?>