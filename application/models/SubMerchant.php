<?php defined('BASEPATH') or exit('No direct script access allowed');

class SubMerchant extends CI_Model
{
    private static $cached_total = null;

    private function _get_datatables_query($id)
    {
        // Emergency safeguard
        $this->db->query("SET SESSION max_execution_time = 30000");
        $this->db->select('m.id, m.c_name, m.c_email, m.c_status, m.c_merchantLevel, NULL as c_gvconnectBusinessId, NULL as c_gvconnectBusinessName, s.c_gvconnectGVConnectKey, s.c_gvconnectStaticQrisRaw, s.c_gvconnectStaticVaBni, s.c_gvconnectStaticVaBca, s.c_gvconnectStaticVaCimb, s.c_gvconnectStaticVaPermata', FALSE);
        $this->db->from('merchant m');
        $this->db->join('submerchant s', 's.ref_merchantId = m.id', 'left');
        $this->db->where('m.parent_merchant_id', $id);
        $this->db->where('m.c_merchantLevel >', 0);

        if (isset($_POST['search']['value']) && $_POST['search']['value'] != "") {
            $search = $_POST['search']['value'];
            $this->db->group_start();
            $this->db->like('m.c_name', $search);
            $this->db->or_like('m.c_email', $search);
            $this->db->or_like('m.id', $search);
            $this->db->or_like('m.c_status', $search);
            $this->db->or_like('s.c_gvconnectBusinessId', $search);
            $this->db->or_like('s.c_gvconnectBusinessName', $search);
            $this->db->group_end();
        }

        if (isset($_POST['order'])) {
            $column_order = [null, 'c_name', 'c_email', 'c_gvconnectBusinessId', 'c_status'];
            $this->db->order_by($column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else {
            $this->db->order_by('id', 'DESC');
        }
    }

    public function get_datatables($id)
    {
        $this->_get_datatables_query($id);
        if ($_POST['length'] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        return $this->db->get()->result();
    }

    public function count_filtered($id)
    {
        $is_filtered = (isset($_POST['search']['value']) && !empty($_POST['search']['value']));
        if (!$is_filtered) {
            return $this->count_all_dt($id);
        }

        $this->db->select('count(merchant.id) as total');
        $this->_get_datatables_query($id);
        $query = $this->db->get();
        return $query->row()->total;
    }

    public function count_all_dt($id)
    {
        if (self::$cached_total !== null) return self::$cached_total;

        $this->db->select('count(id) as total');
        $this->db->from('merchant');
        $this->db->where('parent_merchant_id', $id);
        $this->db->where('c_merchantLevel >', 0);
        $query = $this->db->get();
        self::$cached_total = $query->row() ? (int)$query->row()->total : 0;
        return self::$cached_total;
    }

    public function create_submerchant($data)
    {
        // This now creates a Level 1+ Merchant (Legacy method, kept for backward compatibility if needed)
        $this->db->insert('merchant', $data);
        return true;
    }

    /**
     * Standardized method to create SubMerchant, matching gidi-merchant's hierarchy logic.
     */
    public function create_submerchant_standard($parentId, $data, $subData)
    {
        // 1. Get Parent Info for Ancestor Path & Level
        $this->db->select('c_merchantLevel, c_ancestorPath, c_refSupervisor');
        $parent = $this->db->get_where('merchant', ['id' => $parentId])->row_array();
        
        if (!$parent) return false;

        $data['parent_merchant_id'] = $parentId;
        $data['c_merchantLevel'] = $parent['c_merchantLevel'] + 1;
        $data['c_openapiStatus'] = $data['c_status']; // Sync openapi status
        $data['c_dateCreated'] = date('Y-m-d H:i:s');
        
        if (isset($parent['c_refSupervisor'])) {
            $data['c_refSupervisor'] = $parent['c_refSupervisor'];
        }

        // 2. Insert to Merchant Table
        $this->db->insert('merchant', $data);
        $newId = $this->db->insert_id();

        if (!$newId) return false;

        // 3. Generate and Update Ancestor Path
        $newPath = ($parent['c_ancestorPath'] ? $parent['c_ancestorPath'] : $parentId) . '/' . $newId;
        $this->db->where('id', $newId)->update('merchant', ['c_ancestorPath' => $newPath]);

        // 4. Insert GVConnect Data to Submerchant Table
        if (!empty($subData)) {
            $subData['ref_merchantId'] = $newId;
            $this->db->insert('submerchant', $subData);
        }

        // 5. Auto-assign Default Role for Merchant Portal
        $defaultRole = $this->db->get_where('rbac_roles', ['c_isDefault' => 1])->row_array();
        if ($defaultRole) {
            $roleData = [
                'c_user_type' => 'merchant',
                'ref_userId' => $newId,
                'ref_roleId' => $defaultRole['id'],
                'ref_grantedByMerchantId' => $parentId,
                'c_isActive' => 1,
                'c_grantedAt' => date('Y-m-d H:i:s')
            ];
            $this->db->insert('rbac_user_roles', $roleData);
        }

        return $newId;
    }

    public function update_submerchant($id, $data)
    {
        return $this->db->where('id', $id)->update('merchant', $data);
    }

    /**
     * Standardized DataTables handler for SubAccount list.
     */
    public function get_datatables_handler($id)
    {
        $this->load->library('datatables');
        return $this->datatables->of('merchant m')
            ->select('m.id, m.c_name, m.c_email, m.c_status, m.c_merchantLevel, s.c_gvconnectBusinessId, s.c_gvconnectBusinessName, s.c_gvconnectGVConnectKey, s.c_gvconnectStaticQrisRaw, s.c_gvconnectStaticVaBni, s.c_gvconnectStaticVaBca, s.c_gvconnectStaticVaCimb, s.c_gvconnectStaticVaPermata', FALSE)
            ->join('submerchant s', 's.ref_merchantId = m.id', 'left')
            ->where('m.parent_merchant_id', $id)
            ->where('m.c_merchantLevel >', 0)
            ->set_column_order([null, 'm.c_name', 'm.c_email', 's.c_gvconnectBusinessId', 'm.c_status'])
            ->set_column_search(['m.c_name', 'm.c_email', 'm.id', 'm.c_status', 's.c_gvconnectBusinessId', 's.c_gvconnectBusinessName'])
            ->set_default_order(['m.id' => 'desc'])
            ->addColumn('no', function($row) {
                static $no = null;
                if ($no === null) $no = intval($this->input->post('start'));
                return ++$no;
            })
            ->make(true);
    }
}