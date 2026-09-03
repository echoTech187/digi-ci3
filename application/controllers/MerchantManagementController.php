<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * MerchantManagementController
 * Handles merchant registration, management, edit, analytics detail, and secret credential publishing.
 */
class MerchantManagementController extends CI_Controller
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

    public function merchant()
    {
        $this->_parseRawJson();
        if ($this->_isApi()) {
            try {
                $this->load->library('datatables');
                $where = ['m.c_merchantLevel' => 0];

                if ($this->session->userdata('ref_entity')) {
                    $where['m.ref_entity'] = $this->session->userdata('ref_entity');
                }
                if ($this->session->userdata('search_merchant_status')) {
                    $where['m.c_status'] = $this->session->userdata('search_merchant_status');
                }
                if ($this->session->userdata('search_merchant_openapi_status')) {
                    $where['m.c_openapiStatus'] = $this->session->userdata('search_merchant_openapi_status');
                }
                if ($this->session->userdata('search_merchant_date_from')) {
                    $where['m.c_dateCreated >='] = $this->session->userdata('search_merchant_date_from') . ' 00:00:00';
                }
                if ($this->session->userdata('search_merchant_date_to')) {
                    $where['m.c_dateCreated <='] = $this->session->userdata('search_merchant_date_to') . ' 23:59:59';
                }

                $hasBalancePermission = $this->rbac->has_permission($this->session->userdata('role'), 'balance_merchant_module');
                session_write_close();

                $out = $this->Merchant->getMerchantDataTable($where, $hasBalancePermission, $this->session->userdata('search_merchant'));
                return $this->output->set_content_type('application/json')->set_output(is_string($out) ? $out : json_encode($out));
            } catch (\Throwable $e) {
                log_message('error', 'Merchant AJAX error: ' . $e->getMessage());
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    "draw"            => intval($this->input->post("draw")),
                    "recordsTotal"    => 0,
                    "recordsFiltered" => 0,
                    "data"            => [],
                    "error"           => $e->getMessage()
                ]));
            }
        }

        if (!$this->input->is_ajax_request() && $this->input->get('search_merchant') === null && $this->input->post('search_merchant') === null) {
            $this->session->unset_userdata('search_merchant');
        }

        $search = $this->input->get('search_merchant') ?: $this->input->post('search_merchant') ?: $this->session->userdata('search_merchant');
        if ($search !== null) {
            $this->session->set_userdata('search_merchant', $search);
        }
        if ($this->input->post('search_merchant_status') !== null) {
            $this->session->set_userdata('search_merchant_status', $this->input->post('search_merchant_status'));
        }
        if ($this->input->post('search_merchant_openapi_status') !== null) {
            $this->session->set_userdata('search_merchant_openapi_status', $this->input->post('search_merchant_openapi_status'));
        }
        if ($this->input->post('search_merchant_date_from') !== null) {
            $this->session->set_userdata('search_merchant_date_from', $this->input->post('search_merchant_date_from'));
        }
        if ($this->input->post('search_merchant_date_to') !== null) {
            $this->session->set_userdata('search_merchant_date_to', $this->input->post('search_merchant_date_to'));
        }

        $this->db->select('SUM(c_balanceTotal) as total_balance, SUM(c_balanceHold) as total_hold, COUNT(id) as total_merchants')
            ->from('merchant')
            ->where('c_status', 'Active')
            ->where('c_merchantLevel', 0);

        if ($this->session->userdata('ref_entity')) {
            $this->db->where('ref_entity', $this->session->userdata('ref_entity'));
        }
        if (!empty($search)) {
            $this->db->group_start()
                ->like('c_name', $search)
                ->or_like('id', $search)
                ->or_like('c_email', $search)
                ->group_end();
        }
        $summary = $this->db->get()->row();

        $data = [
            'title'            => 'Merchant',
            'user'             => $this->Model_user->view_user()->row_array(),
            'cashin_channels'  => $this->Merchant->get_cashin_channel(),
            'cashout_channels' => $this->Merchant->get_cashout_channel(),
            'total_balance'    => $summary->total_balance ?: 0,
            'total_hold'       => $summary->total_hold ?: 0,
            'total_available'  => ($summary->total_balance ?: 0) - ($summary->total_hold ?: 0),
            'total_merchants'  => $summary->total_merchants ?: 0
        ];
        $this->load->view('merchant/index', $data);
    }

    public function resetMerchant()
    {
        $this->session->unset_userdata([
            'search_merchant',
            'search_merchant_status',
            'search_merchant_openapi_status',
            'search_merchant_date_from',
            'search_merchant_date_to'
        ]);
        $accept = strtolower($this->input->get_request_header('Accept') ?: '');
        if (strpos($accept, 'json') !== false || $this->input->get('json') == '1') {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'  => true,
                'message' => 'Search filters reset successfully.'
            ]));
        }
        redirect("merchant/manage");
    }

    public function addMerchant()
    {
        $this->load->library('MerchantRegistrationService');
        $this->merchantregistrationservice->register();
    }

    public function editMerchant()
    {
        $this->_parseRawJson();
        $isApi = $this->_isApi();
        $id = $this->uri->segment(3) ?: $this->input->post('id');

        if (!$id) {
            if ($isApi) {
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Merchant ID not found.'
                ]));
            }
            redirect('merchant/manage');
            return;
        }

        $this->form_validation->set_rules('c_name', 'Nama', 'required');
        $this->form_validation->set_rules('c_phoneNumber', 'Nomor Handphone', 'required');
        $this->form_validation->set_rules('c_status', 'Status', 'required');
        $this->form_validation->set_rules('c_openapiStatus', 'Open API Status', 'required');

        if ($this->form_validation->run() == false) {
            $data = [
                'title'              => 'Edit Merchant',
                'user'               => $this->Model_user->view_user()->row_array(),
                'merchant'           => $this->Merchant->get_merchant_by_id($id),
                'supervisors'        => $this->Merchant->get_all_supervisors(),
                'breadcrumb_replace' => [$id => $this->Merchant->get_merchant_by_id($id)['c_name'] ?? 'Merchant']
            ];
            $this->load->view('merchant/edit_merchant', $data);
        } else {
            $updateData = [
                'c_name'                                   => $this->input->post('c_name'),
                'c_phoneNumber'                            => $this->input->post('c_phoneNumber'),
                'c_status'                                 => $this->input->post('c_status'),
                'c_openapiStatus'                          => $this->input->post('c_openapiStatus'),
                'c_refSupervisor'                          => $this->input->post('c_refSupervisor') ?: null,
                'c_allowTransferFromDashboard'             => $this->input->post('c_allowTransferFromDashboard') ? '1' : '0',
                'c_openapiChannelTransferToBifast'         => $this->input->post('c_openapiChannelTransferToBifast') ? '1' : '0',
                'c_openapiChannelTransferToRealtimeOnline' => $this->input->post('c_openapiChannelTransferToRealtimeOnline') ? '1' : '0',
                'c_openapiChannelTransferToEmoney'         => $this->input->post('c_openapiChannelTransferToEmoney') ? '1' : '0',
                'c_settlementDynamicVa'                    => $this->input->post('c_settlementDynamicVa') ?: 'H+1',
                'c_settlementRecurringVa'                  => $this->input->post('c_settlementRecurringVa') ?: 'H+1',
                'c_settlementDynamicQris'                  => $this->input->post('c_settlementDynamicQris') ?: 'H+1',
                'c_settlementRecurringQris'                => $this->input->post('c_settlementRecurringQris') ?: 'H+1',
                'c_settlementDynamicEwallet'               => $this->input->post('c_settlementDynamicEwallet') ?: 'H+1'
            ];

            if ($this->input->post('c_pin')) {
                $updateData['c_pin'] = password_hash($this->input->post('c_pin'), PASSWORD_DEFAULT);
            }

            $this->db->where('id', $id)->update('merchant', $updateData);
            if ($isApi) {
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'  => true,
                    'message' => 'Merchant updated successfully.'
                ]));
            }
            $this->session->set_flashdata('message', '<div class="alert alert-success">Merchant updated successfully!</div>');
            redirect('merchant/manage');
        }
    }

    public function detailMerchant($id)
    {
        $this->load->library('MerchantAnalyticsService');
        $this->merchantanalyticsservice->renderMerchantDetail($id);
    }

    public function ajax_merchant_analytics_overview()
    {
        $this->load->library('MerchantAnalyticsService');
        $this->merchantanalyticsservice->ajaxMerchantAnalyticsOverview();
    }

    public function ajax_merchant_analytics_trends()
    {
        $this->load->library('MerchantAnalyticsService');
        $this->merchantanalyticsservice->ajaxMerchantAnalyticsTrends();
    }

    public function ajax_merchant_analytics_channel_breakdown()
    {
        $this->load->library('MerchantAnalyticsService');
        $this->merchantanalyticsservice->ajaxMerchantAnalyticsChannelBreakdown();
    }

    public function ajax_merchant_submerchants()
    {
        $this->load->library('MerchantAnalyticsService');
        $this->merchantanalyticsservice->ajaxMerchantSubmerchants();
    }

    public function publish_secret()
    {
        $this->_parseRawJson();
        $isApi = $this->_isApi();
        $merchantId = $this->input->post('merchant_id') ?: $this->uri->segment(3);

        if (!$merchantId) {
            if ($isApi) {
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Merchant ID not found.'
                ]));
            }
            redirect('merchant/manage');
            return;
        }

        $merchant = $this->db->get_where('merchant', ['id' => $merchantId])->row_array();
        if (!$merchant) {
            if ($isApi) {
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    'status'  => false,
                    'message' => 'Merchant not found.'
                ]));
            }
            redirect('merchant/manage');
            return;
        }

        $oneTimeSecret = bin2hex(random_bytes(24));
        $hashedSecret = password_hash($oneTimeSecret, PASSWORD_DEFAULT);
        $this->db->where('id', $merchantId)->update('merchant', [
            'c_openapiCredentialSecret' => $hashedSecret
        ]);

        $this->session->set_flashdata('one_time_secret', $oneTimeSecret);
        $this->session->set_flashdata('secret_merchant_name', $merchant['c_name']);
        $this->session->set_flashdata('secret_merchant_id', $merchant['id']);

        if ($isApi) {
            return $this->output->set_content_type('application/json')->set_output(json_encode([
                'status'          => true,
                'message'         => 'Secret credential generated.',
                'one_time_secret' => $oneTimeSecret,
                'merchant_id'     => $merchantId
            ]));
        }
        redirect('merchant/manage');
    }
}
