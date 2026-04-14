<?php defined('BASEPATH') or exit('No direct script access allowed');

class Merchant extends CI_Model
{
 
    public function get_merchant($limit = null, $start = null, $search_merchant = null, $count_only = false) {
        $this->db->select('*');
        $this->db->from('merchant');
        
        if ($search_merchant) {
            $this->db->like('c_name', $search_merchant);
        }
    
        if ($this->session->userdata('role_id') == 1) {
            $ref_entity = $this->session->userdata('ref_entity');

            // Only apply filter if ref_entity is NOT null or empty
            if (!empty($ref_entity)) {
                $this->db->where('ref_entity', $ref_entity);
            }
        }
    
        if ($count_only) {
            return $this->db->count_all_results();  // Return total row count
        }
    
        $this->db->order_by('id', 'DESC');
        if ($limit !== null && $start !== null) {
            $this->db->limit($limit, $start);
        }
    
        return $this->db->get()->result();
    }
    
    public function getMerchantById($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('merchant'); // Adjust table name if necessary
        return $query->row_array(); // Return a single merchant as an associative array
    }
    public function get_merchants() {
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
                $this->db->trans_rollback();
                return $this->db->error();
            }

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                return ['code' => '500', 'message' => 'Transaction failed'];
            } else {
                $this->db->trans_commit();
                return true;
            }
        } else {
            $this->db->trans_rollback();
            return $this->db->error(); // Returns array with 'code' and 'message'
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
        return $this->db->get_where('merchant', ['id' => $merchant_id])->row_array();
    }

    public function update_merchant($merchant_id, $data) {
        $this->db->where('id', $merchant_id);
        return $this->db->update('merchant', $data);
    }

    /* Server-Side DataTables Helpers */
    private function _get_datatables_query($table, $column_order, $column_search, $order, $where = [])
    {
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

        if (isset($_POST['order'])) {
            $this->db->order_by($column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($order)) {
            $this->db->order_by(key($order), $order[key($order)]);
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
        $this->_get_datatables_query($table, $column_order, $column_search, $order, $where);
        return $this->db->count_all_results();
    }

    public function count_all_dt($table, $where = [])
    {
        $this->db->from($table);
        if (!empty($where)) {
            $this->db->where($where);
        }
        return $this->db->count_all_results();
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
        // Using NULL for System/Admin level permissions
        $this->db->where('ref_granterMerchantId IS NULL', null, false);
        return $this->db->get()->result();
    }

    public function save_merchant_delegation($granteeId, $permissionId, $action)
    {
        // Action can be: 'Grant', 'Deny', 'Inherit'
        if ($action === 'Inherit') {
            // Delete any explicit grant by Admin (NULL granter)
            $this->db->where('ref_granteeMerchantId', $granteeId);
            $this->db->where('ref_permissionId', $permissionId);
            $this->db->where('ref_granterMerchantId', NULL);
            return $this->db->delete('rbac_merchant_access_grants');
        } else {
            $isAllowed = ($action === 'Grant') ? 1 : 0;
            
            // Check if exists using NULL for system granter
            $this->db->from('rbac_merchant_access_grants');
            $this->db->where('ref_granteeMerchantId', $granteeId);
            $this->db->where('ref_permissionId', $permissionId);
            $this->db->where('ref_granterMerchantId IS NULL', null, false);
            $exists = $this->db->get()->row();

            $data = [
                'ref_granteeMerchantId' => $granteeId,
                'ref_permissionId'      => $permissionId,
                'ref_granterMerchantId' => NULL,
                'c_isAllowed'           => $isAllowed,
                'c_grantedByUserId'     => $this->session->userdata('id'), // Admin's user ID is 'id', not 'user_id'
                'c_updatedAt'           => date('Y-m-d H:i:s')
            ];

            if ($exists) {
                $this->db->where('id', $exists->id);
                return $this->db->update('rbac_merchant_access_grants', $data);
            } else {
                $data['c_grantedAt'] = date('Y-m-d H:i:s');
                return $this->db->insert('rbac_merchant_access_grants', $data);
            }
        }
    }
}
