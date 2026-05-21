<?php defined('BASEPATH') or exit('No direct script access allowed');

class Merchant extends CI_Model
{
    private static $cached_total = null;
 
    private function get_allowed_columns($hasBalancePermission = null) {
        $role_id = $this->session->userdata('role');
        if ($hasBalancePermission === null) {
            $hasBalancePermission = $this->load->library('rbac') ? $this->rbac->has_permission($role_id, 'balance_merchant_module') : true;
        }

        $cols = 'id, c_name, c_email, c_phoneNumber, c_status, c_merchantLevel, c_openapiStatus, c_dateCreated, c_openapiUrlCallbackQrisMpm, c_openapiUrlCallbackVa, c_openapiUrlCallbackEwallet, c_openapiIPAllow, c_openapiSecurityType, c_refSupervisor, ref_entity, c_openapiChannelVaDynamicCreate, c_openapiChannelVaDynamicQuery, c_openapiChannelVaDynamicCancel, c_openapiChannelVaRecurringCreate, c_openapiChannelVaRecurringCancel, c_openapiChannelQrisMpmDynamicCreate, c_openapiChannelQrisMpmDynamicQuery, c_openapiChannelQrisMpmDynamicCancel, c_openapiChannelEwalletDynamicCreate, c_openapiChannelEwalletDynamicQuery, c_openapiChannelEwalletDynamicCancel, c_openapiChannelTransferToBifast, c_openapiChannelTransferToRealtimeOnline, c_allowTransferFromDashboard';
        
        if ($hasBalancePermission) {
            $cols .= ', c_balanceTotal, c_balanceHold';
        } else {
            $cols .= ', (NULL) AS c_balanceTotal, (NULL) AS c_balanceHold';
        }

        return $cols;
    }

    public function get_merchant($limit = null, $start = null, $search_merchant = null, $count_only = false) {
        $this->db->select($this->get_allowed_columns(), FALSE);
        $this->db->from('merchant');
        
        if ($search_merchant) {
            $this->db->like('c_name', $search_merchant);
        }
    
        if ($this->session->userdata('role_id') == 1) {
            $ref_entity = $this->session->userdata('ref_entity');

            // Only apply filter if ref_entity is NOT null or empty
            if ($ref_entity !== null && $ref_entity !== '') {
                $this->db->where('ref_entity', $ref_entity);
            }
        }
    
        if ($count_only) {
            return $this->db->count_all_results();
        }
    
        if ($limit !== null) {
            $this->db->limit($limit, $start);
        }
    
        $this->db->order_by('id', 'DESC');
        return $this->db->get();
    }
    
    public function getMerchantById($id) {
        $this->db->select('id, c_name, c_email, c_status, c_merchantLevel, c_balanceTotal, c_balanceHold, c_openapistatus');
        $this->db->where('id', $id);
        $query = $this->db->get('merchant');
        return $query->row_array();
    }
    public function get_merchants() {
        $this->db->select('id, c_name, c_email, c_status, c_merchantLevel, c_balanceTotal, c_balanceHold, c_openapistatus');
        $query = $this->db->get('merchant');
        return $query->result_array();
    }

    public function getMerchantsBySupervisor($supervisorId)
    {
        $this->db->select('m.id, m.c_name, m.c_balanceTotal, m.c_balanceHold, m.c_openapistatus, m.c_status');
        $this->db->from('merchant m');
        $this->db->join('merchant_supervisor ms', 'm.c_refSupervisor = ms.id');
        $this->db->where('ms.id', $supervisorId);
        return $this->db->get()->result();
    }


    public function get_cashin_channel(){
        $query = "select * from cashin_channel WHERE c_externalIdDefault='internal' ";
        return $this->db->query($query)->result();
    }

    public function get_cashout_channel(){
        $query = "select * from cashout_channel WHERE c_externalIdDefault='internal' ";
        return $this->db->query($query)->result();
    }

    public function get_cashin_channel_x_merchant_by_merchant_id($merchant_id) {
        $this->db->where('ref_merchantId', $merchant_id);
        $query = $this->db->get('cashin_channel_x_merchant');
        return $query->result();
    }

    public function get_cashout_channel_x_merchant_by_merchant_id($merchant_id) {
        $this->db->where('ref_merchantId', $merchant_id);
        $query = $this->db->get('cashout_channel_x_merchant');
        return $query->result();
    }

    public function create_merchant($data, $gvconnectBusinessId, $gvconnectBusinessName) {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $this->db->trans_begin();
        if ($this->db->insert('merchant', $data)) {
            $submerchant_data = [
                'ref_merchantId'    => $this->db->insert_id(),
                'c_name'            => $data['c_name'],
                'c_status'          => $data['c_status'],
                'c_email'           => $data['c_email'],
                'c_gvconnectBusinessId'     => $gvconnectBusinessId,
                'c_gvconnectBusinessName'   => $gvconnectBusinessName,
            ];
            if (!$this->db->insert('submerchant', $submerchant_data)) {
                $err = $this->db->error();
                $this->db->trans_rollback();
                $this->db->db_debug = $db_debug;
                return $err;
            }

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                $this->db->db_debug = $db_debug;
                return ['code' => '500', 'message' => 'Transaction failed'];
            } else {
                $this->db->trans_commit();
                $this->db->db_debug = $db_debug;
                return true;
            }
        } else {
            $err = $this->db->error();
            $this->db->trans_rollback();
            $this->db->db_debug = $db_debug;
            return $err; // Returns array with 'code' and 'message'
        }
    }

    public function setAllOpenApiStatus($newStatus) {
        $this->db->update('merchant', ['c_openapistatus' => $newStatus]);
    }

    public function setActiveMerchantsOpenApiStatus($newStatus) {
        $this->db->where('c_status', 'Active');
        $this->db->update('merchant', ['c_openapistatus' => $newStatus]);
    }

    public function getMaintenanceStatus() {
    $this->db->select('status');
    $this->db->from('maintenance_status');
    $this->db->limit(1);
    $query = $this->db->get();
    if ($query->num_rows() > 0) {
        return $query->row()->status;
    }
    return 'Active'; // default kalau kosong
}

public function setMaintenanceStatus($newStatus) {
    // Asumsikan hanya 1 row di tabel
    $this->db->update('maintenance_status', ['status' => $newStatus], ['id' => 1]);
}

    public function get_merchant_by_id($merchant_id) {
        $cols = $this->get_allowed_columns();
        // Prefix columns with 'm.' for the join context
        $prefixedCols = implode(', ', array_map(function($col) {
            $col = trim($col);
            if (strpos($col, '(') !== false) return $col; // Don't prefix (NULL)
            return 'm.' . $col;
        }, explode(',', $cols)));

        $this->db->select($prefixedCols, FALSE);
        $this->db->select('s.c_gvconnectBusinessId');
        $this->db->from('merchant m');
        $this->db->join('submerchant s', 's.ref_merchantId = m.id', 'left');
        $this->db->where('m.id', $merchant_id);
        return $this->db->get()->row_array();
    }

    public function update_merchant($merchant_id, $data) {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $this->db->where('id', $merchant_id);
        $success = $this->db->update('merchant', $data);
        $error = $this->db->error();
        $this->db->db_debug = $db_debug;
        return $success ? true : $error;
    }

    /* Server-Side DataTables Helpers */
    private function _get_datatables_query($table, $column_order, $column_search, $order, $where = [], $count_only = false)
    {
        // Emergency 30-second safeguard
        $this->db->query("SET SESSION max_execution_time = 30000");
        
        if (!$count_only) {
            if (strpos($table, 'merchant') !== false && strpos($table, 'supervisor') === false && strpos($table, 'channel') === false) {
                $this->db->select('id, c_name, c_email, c_balanceTotal, c_status, c_merchantLevel, c_openapistatus');
            } else {
                $this->db->select('id');
            }
        }
        $this->db->from($table);
        
        if (!empty($where)) {
            $this->db->where($where);
        }

        $i = 0;
        foreach ($column_search as $item) {
            if ($_POST['search']['value']) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
                if (count($column_search) - 1 == $i)
                    $this->db->group_end();
            }
            $i++;
        }

        if (!$count_only) {
            if (isset($_POST['order'])) {
                $this->db->order_by($column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
            } else if (isset($order)) {
                $this->db->order_by(key($order), $order[key($order)]);
            }
        }
    }

    public function get_datatables($table, $column_order, $column_search, $order, $where = [])
    {
        $this->_get_datatables_query($table, $column_order, $column_search, $order, $where);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered($table, $column_order, $column_search, $order, $where = [])
    {
        $is_filtered = (!empty($where) || (isset($_POST['search']['value']) && !empty($_POST['search']['value'])));
        if (!$is_filtered) {
            return $this->count_all_dt($table, $where);
        }

        $this->db->select('count(id) as total');
        $this->_get_datatables_query($table, $column_order, $column_search, $order, $where, true);
        $query = $this->db->get();
        return $query->row()->total;
    }

    public function count_all_dt($table, $where = [])
    {
        if (empty($where) && self::$cached_total !== null) return self::$cached_total;

        if (!empty($where)) {
            $this->db->select('count(id) as total');
            $this->db->from($table);
            $this->db->where($where);
            $query = $this->db->get();
            return $query->row()->total;
        }

        // ULTRA-FAST: Use table status estimates for recordsTotal
        $table_name = explode(' ', $table)[0];
        $q = $this->db->query("SHOW TABLE STATUS LIKE '{$table_name}'");
        $res = $q->row();
        if ($res && isset($res->Rows)) {
            self::$cached_total = (int)$res->Rows;
            return self::$cached_total;
        }

        $this->db->select("count(id) as total");
        $this->db->from($table_name);
        $query = $this->db->get();
        self::$cached_total = $query->row() ? (int)$query->row()->total : 0;
        return self::$cached_total;
    }

    /**
     * RBAC Delegation Helpers
     */
    public function get_rbac_permissions()
    {
        $this->db->select('id, c_code, c_name');
        $this->db->from('rbac_permissions');
        $this->db->order_by('c_name', 'ASC');
        return $this->db->get()->result();
    }

    public function get_merchant_explicit_grants($merchantId)
    {
        $this->db->from('rbac_merchant_access_grants');
        $this->db->where('ref_granteeMerchantId', $merchantId);
        // Using NULL or 0 for System/Admin level permissions to handle MySQL constraints flexibly
        $this->db->where('(ref_granterMerchantId IS NULL OR ref_granterMerchantId = 0)', null, false);
        return $this->db->get()->result();
    }

    public function save_merchant_delegation($granteeId, $permissionId, $action)
    {
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        // Action can be: 'Grant', 'Deny', 'Inherit'
        if ($action === 'Inherit') {
            // Delete any explicit grant by Admin (NULL or 0 granter)
            $this->db->where('ref_granteeMerchantId', $granteeId);
            $this->db->where('ref_permissionId', $permissionId);
            $this->db->where('(ref_granterMerchantId IS NULL OR ref_granterMerchantId = 0)', null, false);
            $success = $this->db->delete('rbac_merchant_access_grants');
            $err = $this->db->error();
            $this->db->db_debug = $db_debug;
            return $success ? true : $err;
        } else {
            $isAllowed = ($action === 'Grant') ? 1 : 0;
            
            // Check if exists using NULL or 0 for system granter
            $this->db->from('rbac_merchant_access_grants');
            $this->db->where('ref_granteeMerchantId', $granteeId);
            $this->db->where('ref_permissionId', $permissionId);
            $this->db->where('(ref_granterMerchantId IS NULL OR ref_granterMerchantId = 0)', null, false);
            $exists = $this->db->get()->row();

            $data = [
                'ref_granteeMerchantId' => $granteeId,
                'ref_permissionId'      => $permissionId,
                'ref_granterMerchantId' => NULL, // Must be NULL to satisfy fk_rbac_access_granter foreign key referencing merchant(id)
                'c_isAllowed'           => $isAllowed,
                'c_grantedByUserId'     => $this->session->userdata('id'), // Admin's user ID is 'id', not 'user_id'
                'c_updatedAt'           => date('Y-m-d H:i:s')
            ];

            if ($exists) {
                $this->db->where('id', $exists->id);
                $success = $this->db->update('rbac_merchant_access_grants', $data);
            } else {
                $data['c_grantedAt'] = date('Y-m-d H:i:s');
                $success = $this->db->insert('rbac_merchant_access_grants', $data);
            }
            $err = $this->db->error();
            $this->db->db_debug = $db_debug;
            return $success ? true : $err;
        }
    }

    /**
     * Server-Side DataTables Handler using the Datatables library.
     * This centralizes the data retrieval and formatting logic for the merchant list.
     * 
     * @param array $where Filter conditions
     * @param bool $hasBalancePermission Permission flag for balance column
     * @return void Outputs JSON response
     */
    public function getMerchantDataTable($where, $hasBalancePermission)
    {
        $this->load->library('datatables');
        
        $cols = $this->get_allowed_columns($hasBalancePermission);
        // Prefix columns with 'm.' for the join/from context
        $prefixedCols = implode(', ', array_map(function($col) {
            $col = trim($col);
            if (strpos($col, '(') !== false) return $col; // Don't prefix (NULL)
            return 'm.' . $col;
        }, explode(',', $cols)));

        return $this->datatables->of('merchant m')
            ->select($prefixedCols)
            ->set_column_order([null, 'm.id', 'm.c_name', 'm.c_balanceTotal', 'm.c_status', 'm.c_dateCreated', null])
            ->set_column_search(['m.id', 'm.c_name', 'm.c_email'])
            ->set_default_order(['m.c_dateCreated' => 'desc'])
            ->where($where)
            ->addColumn('no', function ($row) {
                static $no = null;
                if ($no === null) $no = intval($this->input->post('start'));
                return ++$no;
            })
            ->addColumn('hasBalancePermission', function ($row) use ($hasBalancePermission) {
                return $hasBalancePermission;
            })
            ->editColumn('c_merchantLevel', function ($row) {
                return isset($row->c_merchantLevel) ? $row->c_merchantLevel : 0;
            })
            ->make(true);
    }
    // --- DataTables Server-Side Processing for Fee Settings ---
    
    public function get_fee_datatables_handler($type, $merchant_id)
    {
        $this->load->library('datatables');
        
        $table = ($type === 'cashin') ? 'cashin_channel_x_merchant' : 'cashout_channel_x_merchant';
        $prefix = ($type === 'cashin') ? 'cashin' : 'cashout';
        $channel_group_col = "c_{$prefix}ChannelGroup";
        $channel_id_col = "ref_{$prefix}ChannelId";
        
        $dt = $this->datatables->of($table)
            ->set_column_order([null, $channel_group_col, 'c_fee', null, null, 'c_status', null])
            ->set_column_search([$channel_group_col, $channel_id_col, 'c_externalIdDefault', 'c_status'])
            ->set_default_order(['id' => 'desc'])
            ->where('ref_merchantId', $merchant_id);

        if ($this->input->post('channel_group')) {
            $dt->where($channel_group_col, $this->input->post('channel_group'));
        }
        if ($this->input->post('channel_id')) {
            $dt->where($channel_id_col, $this->input->post('channel_id'));
        }
        if ($this->input->post('provider')) {
            $dt->where('c_externalIdDefault', $this->input->post('provider'));
        }
        if ($this->input->post('status')) {
            $dt->where('c_status', $this->input->post('status'));
        }

        return $dt->addColumn('no', function($row) {
                static $no = null;
                if ($no === null) $no = intval($this->input->post('start'));
                return ++$no;
            })
            ->make(true);
    }
    public function get_merchant_spv_handler($where = [])
    {
        $this->load->library('datatables');
        
        return $this->datatables->of('merchant_supervisor')
            ->set_column_order(['id', 'c_name', 'c_username', 'c_email', 'c_status', 'c_created_date'])
            ->set_column_search(['c_name', 'c_username', 'c_email', 'c_status'])
            ->set_default_order(['c_created_date' => 'desc'])
            ->where($where)
            ->addColumn('no', function($row) {
                static $no = null;
                if ($no === null) $no = intval($this->input->post('start'));
                return ++$no;
            })
            ->make(true);
    }

    public function get_merchants_by_supervisor_handler($supervisor_id, $hasBalancePermission = false, $where = [])
    {
        $this->load->library('datatables');

        $cols = $this->get_allowed_columns($hasBalancePermission);
        // Prefix with merchant.
        $prefixedCols = implode(', ', array_map(function($col) {
            $col = trim($col);
            if (strpos($col, '(') !== false) return $col; // Don't prefix (NULL)
            return 'merchant.' . $col;
        }, explode(',', $cols)));

        return $this->datatables->of('merchant')
            ->select($prefixedCols)
            ->set_column_order(['merchant.id', 'merchant.c_name', 'merchant.c_balanceTotal', 'merchant.c_balanceHold', 'merchant.c_openapiStatus', 'merchant.c_status', 'merchant.c_dateCreated'])
            ->set_column_search(['merchant.c_name', 'merchant.id', 'merchant.c_email'])
            ->set_default_order(['merchant.c_dateCreated' => 'desc'])
            ->where('merchant.c_refSupervisor', $supervisor_id)
            ->where($where)
            ->addColumn('no', function($row) {
                static $no = null;
                if ($no === null) $no = intval($this->input->post('start'));
                return ++$no;
            })
            ->make(true);
    }
}
