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
        
        // Data for modals
        $data['merchants'] = $this->db->get_where('merchant', ['c_status' => 'Active', 'c_merchantLevel' => 0])->result();
        $data['channel_groups'] = $this->Chanel->get_cashout_chanel_group();
        $data['channel_external_id_defaults'] = $this->Chanel->get_cashout_chanel_external_id_default();
        // Note: MY_Loader automatically wraps this in templates/layout
        $this->load->view('admin/cashout_external/index', $data);
    }

    public function ajax_list() {
        if (!$this->input->is_ajax_request()) return;
        return $this->Chanel->getCashoutExternalDataTable();
    }

    public function add() {
        $this->_validate();

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

        if ($this->Chanel->createCashoutChannelXMerchant($data) === true) {
            $this->session->set_flashdata('success', 'Configuration added successfully');
        } else {
            $this->session->set_flashdata('error', 'Failed to add configuration');
        }
        redirect('admin/cashout/external');
    }

    public function update() {
        $id = $this->input->post('id');
        $this->_validate();

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

        if ($this->Chanel->updateCashoutChannelXMerchant($id, $data) === true) {
            $this->session->set_flashdata('success', 'Configuration updated successfully');
        } else {
            $this->session->set_flashdata('error', 'Failed to update configuration');
        }
        redirect('admin/cashout/external');
    }

    public function delete($id) {
        if ($this->Chanel->deleteCashoutChannelXMerchant($id) === true) {
            $this->session->set_flashdata('success', 'Configuration deleted successfully');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete configuration');
        }
        redirect('admin/cashout/external');
    }

    private function _validate() {
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
            $this->session->set_flashdata('error', validation_errors());
            redirect('admin/cashout/external');
        }
    }
}
