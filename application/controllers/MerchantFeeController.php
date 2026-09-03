<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * MerchantFeeController
 * Manages Cashin and Cashout fee configurations for individual and bulk channels.
 */
class MerchantFeeController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(['session', 'rbac', 'pagination', 'form_validation']);
        $this->load->model(['Model_user', 'Mutation_model', 'Chanel', 'Merchant']);
        is_logged_in();

        global $internalUrlHit, $externalUrlHit;
        $this->internalUrlHit = $internalUrlHit;
        $this->externalUrlHit = $externalUrlHit;
    }

    private function _parseRawJson()
    {
        $raw = json_decode($this->input->raw_input_stream, true);
        if (!empty($raw) && is_array($raw)) {
            foreach ($raw as $k => $v) {
                if ($this->input->get($k) === null && $this->input->post($k) === null) {
                    $_POST[$k] = $v;
                }
            }
        }
    }

    private function _isApi()
    {
        $accept = strtolower($this->input->get_request_header('Accept') ?: '');
        return $this->input->is_ajax_request()
            || strpos($accept, 'json') !== false
            || $this->input->get('json') == '1'
            || $this->input->method() === 'post';
    }

    private function _respond($isApi, $status, $msg, $redirectUrl)
    {
        if ($isApi) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'  => $status,
                'message' => $msg
            ]));
            return;
        }
        $this->session->set_flashdata($status ? 'success' : 'error', $msg);
        redirect($redirectUrl);
    }

    public function settingcashinfee()
    {
        $merchant_id = $this->uri->segment(3);
        if (!$merchant_id) {
            $this->session->set_flashdata('error', 'Merchant ID not found.');
            redirect('merchant/manage');
            return;
        }

        if ($this->input->is_ajax_request()) {
            try {
                return $this->Merchant->get_fee_datatables_handler('cashin', $merchant_id);
            } catch (\Throwable $e) {
                log_message('error', 'Cashin Fee AJAX error: ' . $e->getMessage());
                echo json_encode([
                    "draw"            => intval($this->input->post("draw")),
                    "recordsTotal"    => 0,
                    "recordsFiltered" => 0,
                    "data"            => [],
                    "error"           => "Error"
                ]);
                return;
            }
        }

        $merchant = $this->Merchant->get_merchant_by_id($merchant_id);
        $channels = $this->Merchant->get_cashin_channel_x_merchant_by_merchant_id($merchant_id);
        $active = 0;
        $inactive = 0;

        foreach ($channels as $r) {
            if ($r->c_status == 'Active') {
                $active++;
            } else {
                $inactive++;
            }
        }

        $data = [
            'title'                        => 'Setting Cashin Fee',
            'user'                         => $this->Model_user->view_user()->row_array(),
            'merchant_id'                  => $merchant_id,
            'merchant_name'                => $merchant ? $merchant['c_name'] : 'Unknown',
            'breadcrumb_replace'           => [$merchant_id => $merchant ? $merchant['c_name'] : 'Unknown'],
            'cashin_channel_x_merchant'    => $channels,
            'channel_groups'               => $this->Chanel->get_cashin_chanel_group(),
            'channel_ids'                  => $this->Chanel->get_cashin_chanel_id(),
            'channel_external_id_defaults' => $this->Chanel->get_cashin_chanel_external_id_default(),
            'total_channels'               => count($channels),
            'active_channels'              => $active,
            'inactive_channels'            => $inactive
        ];
        $this->load->view('merchant/setting-fee', $data);
    }

    public function createSettingCashinFee()
    {
        $this->_parseRawJson();
        $isApi = $this->_isApi();
        $merchant_id = $this->input->post('ref_merchantId');

        if (!$merchant_id) {
            $this->_respond($isApi, false, 'Merchant ID is required.', 'merchant/manage');
            return;
        }

        $rules = [
            ['field' => 'ref_cashinChannelId', 'label' => 'Channel ID', 'rules' => 'required'],
            ['field' => 'c_cashinChannelGroup', 'label' => 'Channel Group', 'rules' => 'required'],
            ['field' => 'c_externalIdDefault', 'label' => 'External Default', 'rules' => 'required'],
            ['field' => 'c_feeType', 'label' => 'Fee Type', 'rules' => 'required'],
            ['field' => 'c_fee', 'label' => 'Fee', 'rules' => 'required|numeric'],
            ['field' => 'c_feeExternal', 'label' => 'External Fee', 'rules' => 'required|numeric'],
            ['field' => 'c_settlementType', 'label' => 'Settlement Type', 'rules' => 'required'],
            ['field' => 'c_status', 'label' => 'Status', 'rules' => 'required']
        ];
        $this->form_validation->set_rules($rules);

        if ($this->form_validation->run() == false) {
            $this->_respond($isApi, false, validation_errors(' ', ' '), 'merchant/settingcashinfee/' . $merchant_id);
            return;
        }

        $post = [
            'ref_merchantId'        => $merchant_id,
            'ref_cashinChannelId'   => $this->input->post('ref_cashinChannelId'),
            'c_cashinChannelGroup'  => $this->input->post('c_cashinChannelGroup'),
            'c_externalIdDefault'   => $this->input->post('c_externalIdDefault'),
            'c_feeType'             => $this->input->post('c_feeType'),
            'c_fee'                 => $this->input->post('c_fee'),
            'c_feeExternal'         => $this->input->post('c_feeExternal'),
            'c_settlementType'      => $this->input->post('c_settlementType'),
            'c_status'              => $this->input->post('c_status')
        ];

        $res = $this->lib_internallist->insertCashinFee($post);
        $msg = $res['responseMessage'] ?? 'Process finished.';
        $ok = (isset($res['responseCode']) && $res['responseCode'] == '00');
        $this->_respond($isApi, $ok, $msg, 'merchant/settingcashinfee/' . $merchant_id);
    }

    public function editSettingCashinFee()
    {
        $this->_parseRawJson();
        $isApi = $this->_isApi();
        $merchant_id = $this->input->post('ref_merchantId');
        $id = $this->input->post('id');

        if (!$merchant_id || !$id) {
            $this->_respond($isApi, false, 'Invalid parameters.', 'merchant/manage');
            return;
        }

        $rules = [
            ['field' => 'ref_cashinChannelId', 'label' => 'Channel ID', 'rules' => 'required'],
            ['field' => 'c_cashinChannelGroup', 'label' => 'Channel Group', 'rules' => 'required'],
            ['field' => 'c_externalIdDefault', 'label' => 'External Default', 'rules' => 'required'],
            ['field' => 'c_feeType', 'label' => 'Fee Type', 'rules' => 'required'],
            ['field' => 'c_fee', 'label' => 'Fee', 'rules' => 'required|numeric'],
            ['field' => 'c_feeExternal', 'label' => 'External Fee', 'rules' => 'required|numeric'],
            ['field' => 'c_settlementType', 'label' => 'Settlement Type', 'rules' => 'required'],
            ['field' => 'c_status', 'label' => 'Status', 'rules' => 'required']
        ];
        $this->form_validation->set_rules($rules);

        if ($this->form_validation->run() == false) {
            $this->_respond($isApi, false, validation_errors(' ', ' '), 'merchant/settingcashinfee/' . $merchant_id);
            return;
        }

        $post = [
            'id'                   => $id,
            'ref_merchantId'       => $merchant_id,
            'ref_cashinChannelId'  => $this->input->post('ref_cashinChannelId'),
            'c_cashinChannelGroup' => $this->input->post('c_cashinChannelGroup'),
            'c_externalIdDefault'  => $this->input->post('c_externalIdDefault'),
            'c_feeType'            => $this->input->post('c_feeType'),
            'c_fee'                => $this->input->post('c_fee'),
            'c_feeExternal'        => $this->input->post('c_feeExternal'),
            'c_settlementType'     => $this->input->post('c_settlementType'),
            'c_status'             => $this->input->post('c_status')
        ];

        $res = $this->lib_internallist->updateCashinFee($post);
        $msg = $res['responseMessage'] ?? 'Process finished.';
        $ok = (isset($res['responseCode']) && $res['responseCode'] == '00');
        $this->_respond($isApi, $ok, $msg, 'merchant/settingcashinfee/' . $merchant_id);
    }

    public function deleteSettingCashinFee($id)
    {
        $isApi = $this->_isApi();
        $merchant_id = $this->uri->segment(4);

        if (!$id || !$merchant_id) {
            $this->_respond($isApi, false, 'Invalid parameters.', 'merchant/manage');
            return;
        }

        $res = $this->lib_internallist->deleteCashinFee(['id' => $id, 'ref_merchantId' => $merchant_id]);
        $msg = $res['responseMessage'] ?? 'Process finished.';
        $ok = (isset($res['responseCode']) && $res['responseCode'] == '00');
        $this->_respond($isApi, $ok, $msg, 'merchant/settingcashinfee/' . $merchant_id);
    }

    public function settingcashoutfee()
    {
        $merchant_id = $this->uri->segment(3);
        if (!$merchant_id) {
            $this->session->set_flashdata('error', 'Merchant ID not found.');
            redirect('merchant/manage');
            return;
        }

        if ($this->input->is_ajax_request()) {
            try {
                return $this->Merchant->get_fee_datatables_handler('cashout', $merchant_id);
            } catch (\Throwable $e) {
                log_message('error', 'Cashout Fee AJAX error: ' . $e->getMessage());
                echo json_encode([
                    "draw"            => intval($this->input->post("draw")),
                    "recordsTotal"    => 0,
                    "recordsFiltered" => 0,
                    "data"            => [],
                    "error"           => "Error"
                ]);
                return;
            }
        }

        $merchant = $this->Merchant->get_merchant_by_id($merchant_id);
        $channels = $this->Merchant->get_cashout_channel_x_merchant_by_merchant_id($merchant_id);
        $active = 0;
        $inactive = 0;

        foreach ($channels as $r) {
            if ($r->c_status == 'Active') {
                $active++;
            } else {
                $inactive++;
            }
        }

        $data = [
            'title'                        => 'Setting Cashout Fee',
            'user'                         => $this->Model_user->view_user()->row_array(),
            'merchant_id'                  => $merchant_id,
            'merchant_name'                => $merchant ? $merchant['c_name'] : 'Unknown',
            'breadcrumb_replace'           => [$merchant_id => $merchant ? $merchant['c_name'] : 'Unknown'],
            'cashout_channel_x_merchant'   => $channels,
            'channel_groups'               => $this->Chanel->get_cashout_chanel_group(),
            'channel_ids'                  => $this->Chanel->get_cashout_chanel_id(),
            'channel_external_id_defaults' => $this->Chanel->get_cashout_chanel_external_id_default(),
            'total_channels'               => count($channels),
            'active_channels'              => $active,
            'inactive_channels'            => $inactive
        ];
        $this->load->view('merchant/setting-fee-cashout', $data);
    }

    public function createSettingCashoutFee()
    {
        $this->_parseRawJson();
        $isApi = $this->_isApi();
        $merchant_id = $this->input->post('ref_merchantId');

        if (!$merchant_id) {
            $this->_respond($isApi, false, 'Merchant ID is required.', 'merchant/manage');
            return;
        }

        $rules = [
            ['field' => 'ref_cashoutChannelId', 'label' => 'Channel ID', 'rules' => 'required'],
            ['field' => 'c_cashoutChannelGroup', 'label' => 'Channel Group', 'rules' => 'required'],
            ['field' => 'c_externalIdDefault', 'label' => 'External Default', 'rules' => 'required'],
            ['field' => 'c_feeType', 'label' => 'Fee Type', 'rules' => 'required'],
            ['field' => 'c_fee', 'label' => 'Fee', 'rules' => 'required|numeric'],
            ['field' => 'c_feeExternal', 'label' => 'External Fee', 'rules' => 'required|numeric'],
            ['field' => 'c_status', 'label' => 'Status', 'rules' => 'required']
        ];
        $this->form_validation->set_rules($rules);

        if ($this->form_validation->run() == false) {
            $this->_respond($isApi, false, validation_errors(' ', ' '), 'merchant/settingcashoutfee/' . $merchant_id);
            return;
        }

        $post = [
            'ref_merchantId'        => $merchant_id,
            'ref_cashoutChannelId'  => $this->input->post('ref_cashoutChannelId'),
            'c_cashoutChannelGroup' => $this->input->post('c_cashoutChannelGroup'),
            'c_externalIdDefault'   => $this->input->post('c_externalIdDefault'),
            'c_feeType'             => $this->input->post('c_feeType'),
            'c_fee'                 => $this->input->post('c_fee'),
            'c_feeExternal'         => $this->input->post('c_feeExternal'),
            'c_status'              => $this->input->post('c_status')
        ];

        $res = $this->lib_internallist->insertCashoutFee($post);
        $msg = $res['responseMessage'] ?? 'Process finished.';
        $ok = (isset($res['responseCode']) && $res['responseCode'] == '00');
        $this->_respond($isApi, $ok, $msg, 'merchant/settingcashoutfee/' . $merchant_id);
    }

    public function editSettingCashoutFee()
    {
        $this->_parseRawJson();
        $isApi = $this->_isApi();
        $merchant_id = $this->input->post('ref_merchantId');
        $id = $this->input->post('id');

        if (!$merchant_id || !$id) {
            $this->_respond($isApi, false, 'Invalid parameters.', 'merchant/manage');
            return;
        }

        $rules = [
            ['field' => 'ref_cashoutChannelId', 'label' => 'Channel ID', 'rules' => 'required'],
            ['field' => 'c_cashoutChannelGroup', 'label' => 'Channel Group', 'rules' => 'required'],
            ['field' => 'c_externalIdDefault', 'label' => 'External Default', 'rules' => 'required'],
            ['field' => 'c_feeType', 'label' => 'Fee Type', 'rules' => 'required'],
            ['field' => 'c_fee', 'label' => 'Fee', 'rules' => 'required|numeric'],
            ['field' => 'c_feeExternal', 'label' => 'External Fee', 'rules' => 'required|numeric'],
            ['field' => 'c_status', 'label' => 'Status', 'rules' => 'required']
        ];
        $this->form_validation->set_rules($rules);

        if ($this->form_validation->run() == false) {
            $this->_respond($isApi, false, validation_errors(' ', ' '), 'merchant/settingcashoutfee/' . $merchant_id);
            return;
        }

        $post = [
            'id'                    => $id,
            'ref_merchantId'        => $merchant_id,
            'ref_cashoutChannelId'  => $this->input->post('ref_cashoutChannelId'),
            'c_cashoutChannelGroup' => $this->input->post('c_cashoutChannelGroup'),
            'c_externalIdDefault'   => $this->input->post('c_externalIdDefault'),
            'c_feeType'             => $this->input->post('c_feeType'),
            'c_fee'                 => $this->input->post('c_fee'),
            'c_feeExternal'         => $this->input->post('c_feeExternal'),
            'c_status'              => $this->input->post('c_status')
        ];

        $res = $this->lib_internallist->updateCashoutFee($post);
        $msg = $res['responseMessage'] ?? 'Process finished.';
        $ok = (isset($res['responseCode']) && $res['responseCode'] == '00');
        $this->_respond($isApi, $ok, $msg, 'merchant/settingcashoutfee/' . $merchant_id);
    }

    public function deleteSettingCashoutFee($id)
    {
        $isApi = $this->_isApi();
        $merchant_id = $this->uri->segment(4);

        if (!$id || !$merchant_id) {
            $this->_respond($isApi, false, 'Invalid parameters.', 'merchant/manage');
            return;
        }

        $res = $this->lib_internallist->deleteCashoutFee(['id' => $id, 'ref_merchantId' => $merchant_id]);
        $msg = $res['responseMessage'] ?? 'Process finished.';
        $ok = (isset($res['responseCode']) && $res['responseCode'] == '00');
        $this->_respond($isApi, $ok, $msg, 'merchant/settingcashoutfee/' . $merchant_id);
    }
}
