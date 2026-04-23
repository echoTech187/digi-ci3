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
        
        // Data for modals
        $data['merchants'] = $this->db->get_where('merchant', ['c_status' => 'Active', 'c_merchantLevel' => 0])->result();
        $data['channel_groups'] = $this->Chanel->get_cashin_chanel_group();
        $data['channel_external_id_defaults'] = $this->Chanel->get_cashin_chanel_external_id_default();
        // Note: MY_Loader automatically wraps this in templates/layout
        $this->load->view('admin/cashin_external/index', $data);
    }

    public function ajax_list() {
        if (!$this->input->is_ajax_request()) return;
        return $this->Chanel->getCashinExternalDataTable();
    }

    public function add() {
        $this->_validate();

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

        if ($this->Chanel->createCashinChannelXMerchant($data) === true) {
            $this->session->set_flashdata('success', 'Configuration added successfully');
        } else {
            $this->session->set_flashdata('error', 'Failed to add configuration');
        }
        redirect('admin/cashin/external');
    }

    public function update() {
        $id = $this->input->post('id');
        $this->_validate();

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

        if ($this->Chanel->updateCashinChannelXMerchant($id, $data) === true) {
            $this->session->set_flashdata('success', 'Configuration updated successfully');
        } else {
            $this->session->set_flashdata('error', 'Failed to update configuration');
        }
        redirect('admin/cashin/external');
    }

    public function delete($id) {
        if ($this->Chanel->deleteCashinChannelXMerchant($id) === true) {
            $this->session->set_flashdata('success', 'Configuration deleted successfully');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete configuration');
        }
        redirect('admin/cashin/external');
    }

    public function bulk_update() {
        $updateType      = $this->input->post('update_type');
        $merchantId      = $this->input->post('ref_merchantId');
        $currentGroup    = $this->input->post('current_group');
        $currentExternal = $this->input->post('current_externalId');
        $currentChannel  = $this->input->post('current_cashinChannelId');
        $newGroup        = $this->input->post('new_group');
        $newExternal     = $this->input->post('new_externalId');
        $newChannel      = $this->input->post('new_cashinChannelId');

        // Validation: Group is always required
        if (empty($updateType) || empty($currentGroup) || empty($newGroup)) {
            $this->session->set_flashdata('error', 'Update Type, Current Group, and New Group are required');
            redirect('admin/cashin/external');
        }

        if ($updateType === 'merchant' && empty($merchantId)) {
            $this->session->set_flashdata('error', 'Merchant must be selected for Merchant update type');
            redirect('admin/cashin/external');
        }

        // Check if anything is actually changing
        if ($currentGroup === $newGroup && empty($newExternal) && empty($newChannel)) {
            $this->session->set_flashdata('error', 'No changes detected in configuration');
            redirect('admin/cashin/external');
        }

        $data = [
            'update_type'     => $updateType,
            'merchant_id'     => $merchantId,
            'current_group'    => $currentGroup,
            'current_external' => $currentExternal,
            'current_channel'  => $currentChannel,
            'new_group'        => $newGroup,
            'new_external'     => $newExternal,
            'new_channel'      => $newChannel
        ];

        if ($this->Chanel->updateCashinChannelGlobal($data)) {
            $msg = ($updateType === 'merchant') ? 'Channel update successful for selected merchant' : 'Global channel group update successful';
            $this->session->set_flashdata('success', $msg);
        } else {
            $this->session->set_flashdata('error', 'Failed to perform channel update');
        }
        redirect('admin/cashin/external');
    }

    private function _validate() {
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
            redirect('admin/cashin/external');
        }
    }
}
