<?php defined('BASEPATH') or exit('No direct script access allowed');

class CashinExternalController extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->library('rbac');
        $this->load->library('form_validation');
        $this->load->model('Model_user');
        $this->load->model('Chanel');
        $this->load->model('Merchant');
        is_logged_in();
    }

    public function index() {
        $data['title'] = 'Cashin External Merchant';
        $data['user'] = $this->Model_user->view_user()->row_array();
        
        // Data for modals and filters
        $this->db->select('id, c_name, c_email');
        $data['merchants'] = $this->db->get_where('merchant', ['c_status' => 'Active', 'c_merchantLevel' => 0])->result();
        $data['channel_groups'] = $this->Chanel->get_cashin_chanel_group();
        $data['channel_ids'] = $this->Chanel->get_cashin_chanel_id();
        $data['channel_external_id_defaults'] = $this->Chanel->get_cashin_chanel_external_id_default();
        // Clear session if direct access (not ajax) without parameters
        if (!$this->input->is_ajax_request() && $this->input->get('search_channel') === null && $this->input->post('search_channel') === null) {
            $this->session->unset_userdata('search_external_cashin');
        }

        // Session-based search persistence
        $search_channel = $this->input->get('search_channel') ?: $this->input->post('search_channel');
        if ($search_channel !== null) {
            $this->session->set_userdata('search_external_cashin', $search_channel);
        } else {
            $search_channel = $this->session->userdata('search_external_cashin');
        }

        // Note: MY_Loader automatically wraps this in templates/layout
        $this->load->view('admin/cashin_external/index', $data);
    }

    public function ajax_list() {
        if (!$this->input->is_ajax_request()) return;
        
        $search_channel = $this->input->get('search_channel') ?: $this->input->post('search_channel');
        if ($search_channel !== null) {
            $this->session->set_userdata('search_external_cashin', $search_channel);
        } else {
            $search_channel = $this->session->userdata('search_external_cashin');
        }

        return $this->Chanel->getCashinExternalDataTable($search_channel);
    }

    public function add_view() {
        $data['title'] = 'Add Cashin External Mapping';
        $data['user'] = $this->Model_user->view_user()->row_array();
        
        $this->db->select('id, c_name, c_email');
        $data['merchants'] = $this->db->get_where('merchant', ['c_status' => 'Active', 'c_merchantLevel' => 0])->result();
        $data['channel_groups'] = $this->Chanel->get_cashin_chanel_group();
        $data['channel_external_id_defaults'] = $this->Chanel->get_cashin_chanel_external_id_default();
        
        $this->load->view('admin/cashin_external/add', $data);
    }

    public function edit_view($id) {
        if (!$id) redirect('external/cashin');
        
        $data['title'] = 'Edit Cashin External Mapping';
        $data['user'] = $this->Model_user->view_user()->row_array();
        $data['mapping'] = $this->db->get_where('cashin_channel_x_merchant', ['id' => $id])->row_array();
        
        if (!$data['mapping']) {
            $this->session->set_flashdata('error', 'Mapping not found');
            redirect('external/cashin');
        }

        $this->db->select('id, c_name, c_email');
        $data['merchants'] = $this->db->get_where('merchant', ['c_status' => 'Active', 'c_merchantLevel' => 0])->result();
        $data['channel_groups'] = $this->Chanel->get_cashin_chanel_group();
        $data['channel_external_id_defaults'] = $this->Chanel->get_cashin_chanel_external_id_default();
        
        $this->load->view('admin/cashin_external/edit', $data);
    }

    public function add() {
        $this->_validate('add');

        $data = [
            'ref_merchantId'        => $this->input->post('ref_merchantId'),
            'c_cashinChannelGroup'  => $this->input->post('c_cashinChannelGroup'),
            'ref_cashinChannelId'   => $this->input->post('ref_cashinChannelId'),
            'c_externalIdDefault'   => $this->input->post('c_externalIdDefault'),
            'c_feeType'             => $this->input->post('c_feeType'),
            'c_fee'                 => $this->input->post('c_fee'),
            'c_feePercetange'       => $this->input->post('c_feePercetange'),
            'c_settlementInterval'  => $this->input->post('c_settlementInterval'),
            'c_amountMin'           => $this->input->post('c_amountMin'),
            'c_amountMax'           => $this->input->post('c_amountMax'),
            'c_status'              => $this->input->post('c_status'),
        ];

        $result = $this->Chanel->createCashinChannelXMerchant($data);
        if ($result === true) {
            $this->session->set_flashdata('success', 'Configuration added successfully');
        } else {
            $code = isset($result['code']) ? $result['code'] : 0;
            if ($code == 1142) {
                $this->session->set_flashdata('error', 'Access Denied. You do not have sufficient database privileges to add external channel configurations.');
            } elseif ($code == 1062) {
                $this->session->set_flashdata('warning', 'Failed to add configuration: This merchant already has a configuration for the selected Channel ID.');
            } else {
                $this->session->set_flashdata('error', 'Unable to add configuration due to a system constraint. Please verify your input or contact technical support.');
            }
        }
        redirect('external/cashin');
    }

    public function update() {
        $id = $this->input->post('id');
        $this->_validate('edit', $id);

        $data = [
            'ref_merchantId'        => $this->input->post('ref_merchantId'),
            'c_cashinChannelGroup'  => $this->input->post('c_cashinChannelGroup'),
            'ref_cashinChannelId'   => $this->input->post('ref_cashinChannelId'),
            'c_externalIdDefault'   => $this->input->post('c_externalIdDefault'),
            'c_feeType'             => $this->input->post('c_feeType'),
            'c_fee'                 => $this->input->post('c_fee'),
            'c_feePercetange'       => $this->input->post('c_feePercetange'),
            'c_settlementInterval'  => $this->input->post('c_settlementInterval'),
            'c_amountMin'           => $this->input->post('c_amountMin'),
            'c_amountMax'           => $this->input->post('c_amountMax'),
            'c_status'              => $this->input->post('c_status'),
        ];

        $result = $this->Chanel->updateCashinChannelXMerchant($id, $data);
        if ($result === true) {
            $this->session->set_flashdata('success', 'Configuration updated successfully');
        } else {
            $code = isset($result['code']) ? $result['code'] : 0;
            if ($code == 1142) {
                $this->session->set_flashdata('error', 'Access Denied. You do not have sufficient database privileges to modify external channel configurations.');
            } elseif ($code == 1062) {
                $this->session->set_flashdata('warning', 'Failed to update configuration: This merchant already has a configuration for the selected Channel ID.');
            } else {
                $this->session->set_flashdata('error', 'Unable to update configuration due to a system constraint. Please verify your input or contact technical support.');
            }
        }
        redirect('external/cashin');
    }

    public function delete($id) {
        $result = $this->Chanel->deleteCashinChannelXMerchant($id);
        if ($result === true) {
            $this->session->set_flashdata('success', 'Configuration deleted successfully');
        } else {
            $code = isset($result['code']) ? $result['code'] : 0;
            if ($code == 1142) {
                $this->session->set_flashdata('error', 'Access Denied. You do not have sufficient database privileges to delete external channel configurations.');
            } elseif ($code == 1451) {
                $this->session->set_flashdata('error', 'Cannot delete this configuration because it is currently linked to existing transaction records.');
            } else {
                $this->session->set_flashdata('error', 'Unable to delete configuration due to a system constraint. Please contact technical support.');
            }
        }
        redirect('external/cashin');
    }

    public function bulk_update() {
        $updateType      = $this->input->post('update_type');
        $merchantId      = $this->input->post('ref_merchantId');
        $currentGroup    = $this->input->post('current_group');
        $currentExternal = $this->input->post('current_externalId');
        $currentChannel  = $this->input->post('current_cashinChannelId');
        $currentStatus   = $this->input->post('current_status');
        $newGroup        = $this->input->post('new_group');
        $newExternal     = $this->input->post('new_externalId');
        $newChannel      = $this->input->post('new_cashinChannelId');
        $newStatus       = $this->input->post('new_status');

        // Validation: Group is always required
        if (empty($updateType) || empty($currentGroup) || empty($newGroup)) {
            if ($this->input->is_ajax_request()) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => 'Update Type, Current Group, and New Group are required'
                    ]));
            }
            $this->session->set_flashdata('error', 'Update Type, Current Group, and New Group are required');
            redirect('external/cashin');
        }

        if ($updateType === 'merchant' && empty($merchantId)) {
            if ($this->input->is_ajax_request()) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => 'Merchant must be selected for Merchant update type'
                    ]));
            }
            $this->session->set_flashdata('error', 'Merchant must be selected for Merchant update type');
            redirect('external/cashin');
        }

        // Check if anything is actually changing
        if ($currentGroup === $newGroup && empty($newExternal) && empty($newChannel) && empty($newStatus)) {
            if ($this->input->is_ajax_request()) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => 'No changes detected in configuration'
                    ]));
            }
            $this->session->set_flashdata('error', 'No changes detected in configuration');
            redirect('external/cashin');
        }

        $data = [
            'update_type'      => $updateType,
            'merchant_id'      => $merchantId,
            'current_group'    => $currentGroup,
            'current_external' => $currentExternal,
            'current_channel'  => $currentChannel,
            'current_status'   => $currentStatus,
            'new_group'        => $newGroup,
            'new_external'     => $newExternal,
            'new_channel'      => $newChannel,
            'new_status'       => $newStatus
        ];

        $result = $this->Chanel->updateCashinChannelGlobal($data);
        if ($result === true) {
            $msg = ($updateType === 'merchant') ? 'Channel update successful for selected merchant' : 'Global channel group update successful';
            if ($this->input->is_ajax_request()) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => true,
                        'message' => $msg
                    ]));
            }
            $this->session->set_flashdata('success', $msg);
        } else {
            $code = isset($result['code']) ? $result['code'] : 0;
            if ($code == 1142) {
                $errMsg = 'Access Denied. You do not have sufficient database privileges to perform bulk channel updates.';
            } else {
                $errMsg = 'Unable to perform bulk channel update due to a system constraint. Please contact technical support.';
            }
            if ($this->input->is_ajax_request()) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => $errMsg
                    ]));
            }
            $this->session->set_flashdata('error', $errMsg);
        }
        redirect('external/cashin');
    }

    private function _validate($mode = 'add', $id = null) {
        $rules = [
            ['field' => 'ref_merchantId',       'label' => 'Merchant',           'rules' => 'required'],
            ['field' => 'ref_cashinChannelId',      'label' => 'Channel ID',          'rules' => 'required'],
            ['field' => 'c_cashinChannelGroup',     'label' => 'Channel Group',       'rules' => 'required'],
            ['field' => 'c_externalIdDefault',      'label' => 'External Default',    'rules' => 'required'],
            ['field' => 'c_feeType',                'label' => 'Fee Type',            'rules' => 'required'],
            ['field' => 'c_fee',                    'label' => 'Fee',                 'rules' => 'required|numeric'],
            ['field' => 'c_feePercetange',          'label' => 'Fee Percentage',      'rules' => 'required|numeric'],
            ['field' => 'c_settlementInterval',     'label' => 'Settlement Interval', 'rules' => 'required|numeric'],
            ['field' => 'c_amountMin',              'label' => 'Amount Min',          'rules' => 'required|numeric'],
            ['field' => 'c_amountMax',              'label' => 'Amount Max',          'rules' => 'required|numeric'],
            ['field' => 'c_status',                 'label' => 'Status',              'rules' => 'required'],
        ];
        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            if ($mode == 'add') {
                redirect('external/cashin/create');
            }else if($mode == 'edit') {
                redirect('external/cashin/edit/' . $id);
            }else{
                redirect('external/cashin');
            }
        }
    }

    public function get_channel_ids() {
        if (!$this->input->is_ajax_request()) return;
        $group = $this->input->post('group');
        $externalId = $this->input->post('external_id');

        $this->db->select('ref_cashinChannelId as id');
        $this->db->from('cashin_external_x_channel');
        if (!empty($group)) {
            $this->db->where('c_cashinChannelGroup', $group);
        }
        if (!empty($externalId)) {
            $this->db->where('c_cashinExternalId', $externalId);
        }
        $this->db->group_by('ref_cashinChannelId');
        $channels = $this->db->get()->result();

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($channels));
    }

    public function get_filter_options() {
        if (!$this->input->is_ajax_request()) return;
        $group = $this->input->post('group');
        $externalId = $this->input->post('external_id');

        $this->db->select('c_cashinExternalId as provider');
        $this->db->from('cashin_external_x_channel');
        if (!empty($group)) {
            $this->db->where('c_cashinChannelGroup', $group);
        }
        $this->db->group_by('c_cashinExternalId');
        $providers = $this->db->get()->result_array();

        $this->db->select('ref_cashinChannelId as id');
        $this->db->from('cashin_external_x_channel');
        if (!empty($group)) {
            $this->db->where('c_cashinChannelGroup', $group);
        }
        if (!empty($externalId)) {
            $this->db->where('c_cashinExternalId', $externalId);
        }
        $this->db->group_by('ref_cashinChannelId');
        $channels = $this->db->get()->result_array();

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'providers' => array_column($providers, 'provider'),
                'channels' => array_column($channels, 'id')
            ]));
    }

    public function get_merchant_mappings() {
        if (!$this->input->is_ajax_request()) return;
        $merchantId = $this->input->post('merchant_id');

        $this->db->select('c_cashinChannelGroup as group');
        $this->db->from('cashin_external_x_channel');
        $this->db->where('ref_merchantId', $merchantId);
        $this->db->group_by('c_cashinChannelGroup');
        $groups = $this->db->get()->result_array();

        $this->db->select('c_cashinExternalId as provider');
        $this->db->from('cashin_external_x_channel');
        $this->db->where('ref_merchantId', $merchantId);
        $this->db->group_by('c_cashinExternalId');
        $providers = $this->db->get()->result_array();

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'groups' => array_column($groups, 'group'),
                'providers' => array_column($providers, 'provider')
            ]));
    }
}
