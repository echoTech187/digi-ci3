<?php defined('BASEPATH') or exit('No direct script access allowed');

class CashoutExternalController extends CI_Controller {
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
        $data['title'] = 'Cashout External Merchant';
        $data['user'] = $this->Model_user->view_user()->row_array();
        
        // Data for modals and filters
        $this->db->select('id, c_name, c_email');
        $data['merchants'] = $this->db->get_where('merchant', ['c_status' => 'Active', 'c_merchantLevel' => 0])->result();
        $data['channel_groups'] = $this->Chanel->get_cashout_chanel_group();
        $data['channel_ids'] = $this->Chanel->get_cashout_chanel_id();
        $data['channel_external_id_defaults'] = $this->Chanel->get_cashout_chanel_external_id_default();
        // Clear session if direct access (not ajax) without parameters
        if (!$this->input->is_ajax_request() && $this->input->get('search_channel') === null && $this->input->post('search_channel') === null) {
            $this->session->unset_userdata('search_external_cashout');
        }

        // Session-based search persistence
        $search_channel = $this->input->get('search_channel') ?: $this->input->post('search_channel');
        if ($search_channel !== null) {
            $this->session->set_userdata('search_external_cashout', $search_channel);
        } else {
            $search_channel = $this->session->userdata('search_external_cashout');
        }

        // Note: MY_Loader automatically wraps this in templates/layout
        $this->load->view('admin/cashout_external/index', $data);
    }

    public function ajax_list() {
        $raw_json = json_decode($this->input->raw_input_stream, true);
        if (!empty($raw_json) && is_array($raw_json)) {
            foreach ($raw_json as $k => $v) {
                if ($this->input->post($k) === NULL) {
                    $_POST[$k] = $v;
                }
            }
        }

        $search_channel = $this->input->get('search_channel') ?: $this->input->post('search_channel');
        if ($search_channel !== null) {
            $this->session->set_userdata('search_external_cashout', $search_channel);
        } else {
            $search_channel = $this->session->userdata('search_external_cashout');
        }

        $out = $this->Chanel->getCashoutExternalDataTable($search_channel);
        $this->output
            ->set_content_type('application/json')
            ->set_output(is_string($out) ? $out : json_encode($out));
    }

    public function add_view() {
        $data['title'] = 'Add Cashout External Mapping';
        $data['user'] = $this->Model_user->view_user()->row_array();
        
        $this->db->select('id, c_name, c_email');
        $data['merchants'] = $this->db->get_where('merchant', ['c_status' => 'Active', 'c_merchantLevel' => 0])->result();
        $data['channel_groups'] = $this->Chanel->get_cashout_chanel_group();
        $data['channel_external_id_defaults'] = $this->Chanel->get_cashout_chanel_external_id_default();
        
        $this->load->view('admin/cashout_external/add', $data);
    }

    public function edit_view($id) {
        if (!$id) redirect('external/cashout');
        
        $data['title'] = 'Edit Cashout External Mapping';
        $data['user'] = $this->Model_user->view_user()->row_array();
        $data['mapping'] = $this->db->get_where('cashout_channel_x_merchant', ['id' => $id])->row_array();
        
        if (!$data['mapping']) {
            $this->session->set_flashdata('error', 'Mapping not found');
            redirect('external/cashout');
        }

        $this->db->select('id, c_name, c_email');
        $data['merchants'] = $this->db->get_where('merchant', ['c_status' => 'Active', 'c_merchantLevel' => 0])->result();
        $data['channel_groups'] = $this->Chanel->get_cashout_chanel_group();
        $data['channel_external_id_defaults'] = $this->Chanel->get_cashout_chanel_external_id_default();
        
        $this->load->view('admin/cashout_external/edit', $data);
    }

    public function add() {
        $raw_json = json_decode($this->input->raw_input_stream, true);
        if (!empty($raw_json) && is_array($raw_json)) {
            foreach ($raw_json as $k => $v) {
                if ($this->input->get($k) === null && $this->input->post($k) === null) {
                    $_POST[$k] = $v;
                }
            }
        }

        $accept = strtolower($this->input->get_request_header('Accept') ?: '');
        $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1' || $this->input->method() === 'post';



        $this->_validate('add');

        $data = [
            'ref_merchantId'        => $this->input->post('ref_merchantId'),
            'c_cashoutChannelGroup' => $this->input->post('c_cashoutChannelGroup'),
            'ref_cashoutChannelId'  => $this->input->post('ref_cashoutChannelId'),
            'c_externalIdDefault'   => $this->input->post('c_externalIdDefault'),
            'c_feeType'             => $this->input->post('c_feeType'),
            'c_fee'                 => $this->input->post('c_fee'),
            'c_feePercetange'       => $this->input->post('c_feePercetange'),
            'c_amountMin'           => $this->input->post('c_amountMin'),
            'c_amountMax'           => $this->input->post('c_amountMax'),
            'c_status'              => $this->input->post('c_status'),
        ];

        $result = $this->Chanel->createCashoutChannelXMerchant($data);
        if ($result === true) {
            if ($is_api_request) {
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status' => true,
                    'message' => 'Configuration added successfully'
                ]));
            }
            $this->session->set_flashdata('success', 'Configuration added successfully');
        } else {
            $code = isset($result['code']) ? $result['code'] : 0;
            $msg = 'Unable to add configuration due to a system constraint.';
            if ($code == 1142) {
                $msg = 'Access Denied. You do not have sufficient database privileges to add external channel configurations.';
            } elseif ($code == 1062) {
                $msg = 'Failed to add configuration: This merchant already has a configuration for the selected Channel ID.';
            }
            if ($is_api_request) {
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status' => false,
                    'message' => $msg
                ]));
            }
            $this->session->set_flashdata('error', $msg);
        }
        redirect('external/cashout');
    }

    public function update() {
        $raw_json = json_decode($this->input->raw_input_stream, true);
        if (!empty($raw_json) && is_array($raw_json)) {
            foreach ($raw_json as $k => $v) {
                if ($this->input->get($k) === null && $this->input->post($k) === null) {
                    $_POST[$k] = $v;
                }
            }
        }

        $accept = strtolower($this->input->get_request_header('Accept') ?: '');
        $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1' || $this->input->method() === 'post';

        $id = $this->input->post('id') ?: $this->uri->segment(4);
        $existing = $id ? $this->db->get_where('cashout_channel_x_merchant', ['id' => $id])->row_array() : null;



        $this->_validate('edit', $id);

        $data = [
            'ref_merchantId'        => $this->input->post('ref_merchantId'),
            'c_cashoutChannelGroup' => $this->input->post('c_cashoutChannelGroup'),
            'ref_cashoutChannelId'  => $this->input->post('ref_cashoutChannelId'),
            'c_externalIdDefault'   => $this->input->post('c_externalIdDefault'),
            'c_feeType'             => $this->input->post('c_feeType'),
            'c_fee'                 => $this->input->post('c_fee'),
            'c_feePercetange'       => $this->input->post('c_feePercetange'),
            'c_amountMin'           => $this->input->post('c_amountMin'),
            'c_amountMax'           => $this->input->post('c_amountMax'),
            'c_status'              => $this->input->post('c_status'),
        ];

        $result = $this->Chanel->updateCashoutChannelXMerchant($id, $data);
        if ($result === true) {
            if ($is_api_request) {
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status' => true,
                    'message' => 'Configuration updated successfully'
                ]));
            }
            $this->session->set_flashdata('success', 'Configuration updated successfully');
        } else {
            $code = isset($result['code']) ? $result['code'] : 0;
            $msg = 'Unable to update configuration due to a system constraint.';
            if ($code == 1142) {
                $msg = 'Access Denied. You do not have sufficient database privileges to modify external channel configurations.';
            } elseif ($code == 1062) {
                $msg = 'Failed to update configuration: This merchant already has a configuration for the selected Channel ID.';
            }
            if ($is_api_request) {
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status' => false,
                    'message' => $msg
                ]));
            }
            $this->session->set_flashdata('error', $msg);
        }
        redirect('external/cashout');
    }

    public function delete($id) {
        $accept = strtolower($this->input->get_request_header('Accept') ?: '');
        $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1' || $this->input->method() === 'post';

        $result = $this->Chanel->deleteCashoutChannelXMerchant($id);
        if ($result === true) {
            if ($is_api_request) {
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status' => true,
                    'message' => 'Configuration deleted successfully'
                ]));
            }
            $this->session->set_flashdata('success', 'Configuration deleted successfully');
        } else {
            $code = isset($result['code']) ? $result['code'] : 0;
            $msg = 'Unable to delete configuration due to a system constraint.';
            if ($code == 1142) {
                $msg = 'Access Denied. You do not have sufficient database privileges to delete external channel configurations.';
            } elseif ($code == 1451) {
                $msg = 'Cannot delete this configuration because it is currently linked to existing transaction records.';
            }
            if ($is_api_request) {
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status' => false,
                    'message' => $msg
                ]));
            }
            $this->session->set_flashdata('error', $msg);
        }
        redirect('external/cashout');
    }

    public function bulk_update() {
        $raw_json = json_decode($this->input->raw_input_stream, true);
        if (!empty($raw_json) && is_array($raw_json)) {
            foreach ($raw_json as $k => $v) {
                if ($this->input->get($k) === null && $this->input->post($k) === null) {
                    $_POST[$k] = $v;
                }
            }
        }

        $accept = strtolower($this->input->get_request_header('Accept') ?: '');
        $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1' || $this->input->method() === 'post';

        $updateType      = $this->input->post('update_type');
        $merchantId      = $this->input->post('ref_merchantId') ?: $this->input->post('merchant_id');
        $currentGroup    = $this->input->post('current_group');
        $currentExternal = $this->input->post('current_externalId') ?: $this->input->post('current_external_id');
        $currentChannel  = $this->input->post('current_cashoutChannelId') ?: $this->input->post('current_cashout_channel_id');
        $currentStatus   = $this->input->post('current_status');
        $newGroup        = $this->input->post('new_group');
        $newExternal     = $this->input->post('new_externalId') ?: $this->input->post('new_external_id');
        $newChannel      = $this->input->post('new_cashoutChannelId') ?: $this->input->post('new_cashout_channel_id');
        $newStatus       = $this->input->post('new_status');

        // Validation: Group is always required
        if (empty($updateType) || empty($currentGroup) || empty($newGroup)) {
            if ($is_api_request) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => 'Update Type, Current Group, and New Group are required'
                    ]));
            }
            $this->session->set_flashdata('error', 'Update Type, Current Group, and New Group are required');
            redirect('external/cashout');
            return;
        }

        if ($updateType === 'merchant' && (empty($merchantId) || (is_array($merchantId) && count($merchantId) === 0))) {
            if ($is_api_request) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => 'At least one merchant must be selected for Merchant update type'
                    ]));
            }
            $this->session->set_flashdata('error', 'At least one merchant must be selected for Merchant update type');
            redirect('external/cashout');
            return;
        }

        // Check if anything is actually changing
        $isGroupChanged = ($newGroup !== $currentGroup);
        $isExtChanged = (!empty($newExternal) && $newExternal !== $currentExternal);
        $isChanChanged = (!empty($newChannel) && $newChannel !== $currentChannel);
        $isStatusChanged = (!empty($newStatus) && $newStatus !== $currentStatus);

        if (!$isGroupChanged && !$isExtChanged && !$isChanChanged && !$isStatusChanged) {
            if ($is_api_request) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => 'No changes detected in configuration'
                    ]));
            }
            $this->session->set_flashdata('error', 'No changes detected in configuration');
            redirect('external/cashout');
            return;
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

        $result = $this->Chanel->updateCashoutChannelGlobal($data);
        if ($result === true) {
            $msg = ($updateType === 'merchant') ? 'Channel update successful for selected merchant' : 'Global channel group update successful';
            if ($is_api_request) {
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
            if ($code == 400) {
                $errMsg = isset($result['message']) ? $result['message'] : 'Invalid configuration combination.';
            } elseif ($code == 1142) {
                $errMsg = 'Access Denied. You do not have sufficient database privileges to perform bulk channel updates.';
            } else {
                $errMsg = 'Unable to perform bulk channel update due to a system constraint. Please contact technical support.';
            }
            if ($is_api_request) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => $errMsg
                    ]));
            }
            $this->session->set_flashdata('error', $errMsg);
        }
        redirect('external/cashout');
    }

    private function _validate($mode = 'add', $id = null) {
        $rules = [
            ['field' => 'ref_merchantId',        'label' => 'Merchant',           'rules' => 'required'],
            ['field' => 'ref_cashoutChannelId',      'label' => 'Channel ID',          'rules' => 'required'],
            ['field' => 'c_cashoutChannelGroup',     'label' => 'Channel Group',       'rules' => 'required'],
            ['field' => 'c_externalIdDefault',      'label' => 'External Default',    'rules' => 'required'],
            ['field' => 'c_feeType',                'label' => 'Fee Type',            'rules' => 'required'],
            ['field' => 'c_fee',                    'label' => 'Fee',                 'rules' => 'required|numeric'],
            ['field' => 'c_feePercetange',          'label' => 'Fee Percentage',      'rules' => 'required|numeric'],
            ['field' => 'c_amountMin',              'label' => 'Amount Min',          'rules' => 'required|numeric'],
            ['field' => 'c_amountMax',              'label' => 'Amount Max',          'rules' => 'required|numeric'],
            ['field' => 'c_status',                 'label' => 'Status',              'rules' => 'required'],
        ];
        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE) {
            $accept = strtolower((string)$this->input->get_request_header('Accept') ?: '');
            $is_api = $this->input->is_ajax_request() 
                || strpos($accept, 'json') !== false
                || strpos(strtolower((string)$this->input->get_request_header('Content-Type')), 'json') !== false
                || $this->input->get('json') == '1'
                || $this->input->method() === 'post';

            if ($is_api) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => false,
                        'message' => trim(strip_tags(validation_errors())) ?: 'Validation error: Missing required fields.'
                    ]))->_display();
                exit;
            }

            $this->session->set_flashdata('error', validation_errors());
            if ($mode == 'add') {
                redirect('external/cashout/create');
            } else if($mode == 'edit' && !empty($id)) {
                redirect('external/cashout/edit/' . $id);
            } else {
                redirect('external/cashout');
            }
        }
    }

    public function get_channel_ids() {
        if (!$this->input->is_ajax_request()) return;
        $group = $this->input->post('group');
        $externalId = $this->input->post('external_id');

        $this->db->select('ref_cashoutChannelId as id');
        $this->db->from('cashout_external_x_channel');
        if (!empty($group)) {
            $this->db->where('c_cashoutChannelGroup', $group);
        }
        if (!empty($externalId)) {
            $this->db->where('c_cashoutExternalId', $externalId);
        }
        $this->db->group_by('ref_cashoutChannelId');
        $channels = $this->db->get()->result();

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($channels));
    }

    public function get_filter_options() {
        if (!$this->input->is_ajax_request()) return;
        $group = $this->input->post('group');
        $externalId = $this->input->post('external_id');

        $this->db->select('c_cashoutExternalId as provider');
        $this->db->from('cashout_external_x_channel');
        if (!empty($group)) {
            $this->db->where('c_cashoutChannelGroup', $group);
        }
        $this->db->group_by('c_cashoutExternalId');
        $providers = $this->db->get()->result_array();

        $this->db->select('ref_cashoutChannelId as id');
        $this->db->from('cashout_external_x_channel');
        if (!empty($group)) {
            $this->db->where('c_cashoutChannelGroup', $group);
        }
        if (!empty($externalId)) {
            $this->db->where('c_cashoutExternalId', $externalId);
        }
        $this->db->group_by('ref_cashoutChannelId');
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

        if (empty($merchantId)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'groups' => [],
                    'providers' => []
                ]));
        }

        $this->db->select('c_cashoutChannelGroup as group');
        $this->db->from('cashout_channel_x_merchant');
        if (is_array($merchantId)) {
            $this->db->where_in('ref_merchantId', $merchantId);
        } else {
            $this->db->where('ref_merchantId', $merchantId);
        }
        $this->db->group_by('c_cashoutChannelGroup');
        $groups = $this->db->get()->result_array();

        $this->db->select('c_externalIdDefault as provider');
        $this->db->from('cashout_channel_x_merchant');
        if (is_array($merchantId)) {
            $this->db->where_in('ref_merchantId', $merchantId);
        } else {
            $this->db->where('ref_merchantId', $merchantId);
        }
        $this->db->group_by('c_externalIdDefault');
        $providers = $this->db->get()->result_array();

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'groups' => array_values(array_filter(array_column($groups, 'group'))),
                'providers' => array_values(array_filter(array_column($providers, 'provider')))
            ]));
    }
}
