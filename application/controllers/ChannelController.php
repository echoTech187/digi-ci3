<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * ChannelController
 * Manages Cashin and Cashout channel configurations, routing, and providers.
 */
class ChannelController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library(['session', 'rbac', 'pagination', 'form_validation']);
        $this->load->model(['Model_user', 'Chanel']);
        is_logged_in();
    }

    private function _respond($is_api, $status, $msg, $redirect_url)
    {
        if ($is_api) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'status' => $status,
                'message' => $msg
            ]));
            return;
        }
        $this->session->set_flashdata($status ? 'success' : 'error', $msg);
        redirect($redirect_url);
    }

    private function _formatDbError($code, $type)
    {
        if ($code == 1142) {
            return "Access Denied. You do not have sufficient database privileges to modify $type channels.";
        }
        if ($code == 1062) {
            return "A channel with this ID or configuration already exists.";
        }
        if ($code == 1451) {
            return "Cannot delete this channel because it is currently linked to existing merchant fee configurations or transactions.";
        }
        return "Unable to process $type channel due to a system constraint.";
    }

    public function cashin()
    {
        is_logged_in();
        $table = 'cashin_external_x_channel cc';
        $column_order = [null, 'cc.ref_cashinChannelId', 'cc.c_cashinChannelGroup', 'cc.c_cashinChannelGroup2', 'cc.c_cashinExternalId', 'cc.c_feeType', 'cc.c_fee', null];
        $column_search = ['cc.ref_cashinChannelId', 'cc.c_cashinChannelGroup', 'cc.c_cashinChannelGroup2', 'cc.c_cashinExternalId'];
        $order = ['cc.id' => 'desc'];
        $where = [];

        if ($this->input->post('channel_group')) $where['cc.c_cashinChannelGroup'] = $this->input->post('channel_group');
        if ($this->input->post('external_id')) $where['cc.c_cashinExternalId'] = $this->input->post('external_id');

        if (!$this->input->is_ajax_request() && $this->input->get('search_channel') === null && $this->input->post('search_channel') === null) {
            $this->session->unset_userdata('search_channel');
        }

        $search_channel = $this->input->get('search_channel') ?: $this->input->post('search_channel') ?: $this->session->userdata('search_channel');
        if ($search_channel) {
            $this->session->set_userdata('search_channel', $search_channel);
            $where['custom_search'] = $search_channel;
        }

        if ($this->input->is_ajax_request()) {
            try {
                return $this->Chanel->get_datatables_handler($table, $column_order, $column_search, $order, $where);
            } catch (\Throwable $e) {
                log_message('error', 'Cashin Channel AJAX error: ' . $e->getMessage());
                echo json_encode(["draw" => intval($this->input->post("draw")), "recordsTotal" => 0, "recordsFiltered" => 0, "data" => [], "error" => $e->getMessage()]);
                return;
            }
        }

        $summary = $this->Chanel->get_cashin_summary();
        $data = [
            'qty'                          => $summary->qty,
            'groups'                       => $summary->total_groups,
            'providers'                    => $summary->providers,
            'avg_fee'                      => $summary->avg_fee,
            'title'                        => 'Cash In Channels',
            'user'                         => $this->Model_user->view_user()->row_array(),
            'cashin_chanel'                => [],
            'pagination'                   => '',
            'start'                        => 0,
            'channel_groups'               => $this->Chanel->get_cashin_chanel_group(),
            'channel_external_id_defaults' => $this->Chanel->get_cashin_chanel_external_id_default()
        ];
        $this->load->view('chanel/cashin', $data);
    }

    public function cashout()
    {
        is_logged_in();
        $table = 'cashout_external_x_channel cc';
        $column_order = [null, 'cc.ref_cashoutChannelId', 'cc.c_cashoutChannelGroup', 'cc.c_cashoutChannelGroup2', 'cc.c_cashoutExternalId', 'cc.c_feeType', 'cc.c_fee', null];
        $column_search = ['cc.ref_cashoutChannelId', 'cc.c_cashoutChannelGroup', 'cc.c_cashoutChannelGroup2', 'cc.c_cashoutExternalId'];
        $order = ['cc.id' => 'desc'];
        $where = ['cc.c_cashoutChannelGroup !=' => 'ppob'];

        if ($this->input->post('channel_group')) $where['cc.c_cashoutChannelGroup'] = $this->input->post('channel_group');
        if ($this->input->post('external_id')) $where['cc.c_cashoutExternalId'] = $this->input->post('external_id');

        if (!$this->input->is_ajax_request() && $this->input->get('search_channel') === null && $this->input->post('search_channel') === null) {
            $this->session->unset_userdata('search_channel_out');
        }

        $search_channel = $this->input->get('search_channel') ?: $this->input->post('search_channel') ?: $this->session->userdata('search_channel_out');
        if ($search_channel) {
            $this->session->set_userdata('search_channel_out', $search_channel);
            $where['custom_search'] = $search_channel;
        }

        if ($this->input->is_ajax_request()) {
            try {
                return $this->Chanel->get_datatables_handler($table, $column_order, $column_search, $order, $where);
            } catch (\Throwable $e) {
                log_message('error', 'Cashout Channel AJAX error: ' . $e->getMessage());
                echo json_encode(["draw" => intval($this->input->post("draw")), "recordsTotal" => 0, "recordsFiltered" => 0, "data" => [], "error" => $e->getMessage()]);
                return;
            }
        }

        $summary = $this->Chanel->get_cashout_summary();
        $data = [
            'qty'                          => $summary->qty,
            'groups'                       => $summary->total_groups,
            'providers'                    => $summary->providers,
            'avg_fee'                      => $summary->avg_fee,
            'title'                        => 'Cash Out Channels',
            'user'                         => $this->Model_user->view_user()->row_array(),
            'cashout_chanel'               => [],
            'pagination'                   => '',
            'start'                        => 0,
            'cashout_channels'             => $this->Chanel->get_cashout_channels_all(),
            'channel_groups'               => $this->Chanel->get_cashout_chanel_group(),
            'channel_external_id_defaults' => $this->Chanel->get_cashout_chanel_external_id_default()
        ];
        $this->load->view('chanel/cashout', $data);
    }

    public function createCashinChanel()
    {
        $raw = json_decode($this->input->raw_input_stream, true);
        if (!empty($raw) && is_array($raw)) $_POST = array_merge($raw, $_POST);

        if (isset($_POST['ref_cashinChannelId']) && empty($_POST['id'])) $_POST['id'] = $_POST['ref_cashinChannelId'];
        if (isset($_POST['c_cashinChannelGroup']) && empty($_POST['chanelgroup'])) $_POST['chanelgroup'] = $_POST['c_cashinChannelGroup'];
        if (isset($_POST['c_cashinExternalId']) && empty($_POST['externaldefault'])) $_POST['externaldefault'] = $_POST['c_cashinExternalId'];
        if (isset($_POST['c_feeType']) && empty($_POST['feetype'])) $_POST['feetype'] = $_POST['c_feeType'];
        if (isset($_POST['c_fee']) && empty($_POST['fee'])) $_POST['fee'] = $_POST['c_fee'];
        if (isset($_POST['c_settlementInterval']) && empty($_POST['settlementinterval'])) $_POST['settlementinterval'] = $_POST['c_settlementInterval'];
        if (isset($_POST['c_amountMin']) && empty($_POST['amountmin'])) $_POST['amountmin'] = $_POST['c_amountMin'];
        if (isset($_POST['c_amountMax']) && empty($_POST['amountmax'])) $_POST['amountmax'] = $_POST['c_amountMax'];

        $is_api = $this->input->is_ajax_request() || strpos(strtolower($this->input->get_request_header('Accept') ?: ''), 'json') !== false || $this->input->get('json') == '1' || $this->input->method() === 'post';

        $this->form_validation->set_rules('id', 'Id', 'required');
        $this->form_validation->set_rules('chanelgroup', 'Chanel Group', 'required');
        $this->form_validation->set_rules('description', 'Description', 'trim|xss_clean');
        $this->form_validation->set_rules('externaldefault', 'External Default', 'required');
        $this->form_validation->set_rules('feetype', 'Fee Type', 'required');
        $this->form_validation->set_rules('fee', 'Fee', 'required|numeric');
        $this->form_validation->set_rules('settlementinterval', 'Settlement Interval', 'required|numeric');
        $this->form_validation->set_rules('amountmin', 'Amount Min', 'required|numeric');
        $this->form_validation->set_rules('amountmax', 'Amount Max', 'required|numeric');

        if ($this->form_validation->run() == FALSE) {
            $err = trim(preg_replace('/\s+/', ' ', strip_tags(validation_errors()))) ?: 'Validation failed.';
            $this->_respond($is_api, false, $err, 'channel/cashin');
            return;
        }

        $data = [
            'ref_cashinChannelId'   => $this->input->post('id'),
            'c_cashinChannelGroup'  => $this->input->post('chanelgroup'),
            'c_cashinChannelGroup2' => $this->input->post('description') ?: $this->input->post('chanelgroup'),
            'c_cashinExternalId'    => $this->input->post('externaldefault'),
            'c_feeType'             => $this->input->post('feetype'),
            'c_fee'                 => $this->input->post('fee'),
            'c_settlementInterval'  => $this->input->post('settlementinterval'),
            'c_amountMin'           => $this->input->post('amountmin'),
            'c_amountMax'           => $this->input->post('amountmax')
        ];

        $existing = $this->db->get_where('cashin_external_x_channel', ['ref_cashinChannelId' => $data['ref_cashinChannelId']])->row_array();
        $result = $existing ? $this->Chanel->updateCashinChannel($existing['id'], $data) : $this->Chanel->createCashinChannel($data);

        if ($result === true) {
            $this->_respond($is_api, true, 'Data successfully saved', 'channel/cashin');
        } else {
            $msg = $this->_formatDbError($result['code'] ?? 0, 'cashin');
            $this->_respond($is_api, false, $msg, 'channel/cashin');
        }
    }

    public function createCashOutChanel()
    {
        $raw = json_decode($this->input->raw_input_stream, true);
        if (!empty($raw) && is_array($raw)) $_POST = array_merge($raw, $_POST);

        if (isset($_POST['ref_cashoutChannelId']) && empty($_POST['id'])) $_POST['id'] = $_POST['ref_cashoutChannelId'];
        if (isset($_POST['c_cashoutChannelGroup']) && empty($_POST['chanelgroup'])) $_POST['chanelgroup'] = $_POST['c_cashoutChannelGroup'];
        if (isset($_POST['c_cashoutExternalId']) && empty($_POST['externaldefault'])) $_POST['externaldefault'] = $_POST['c_cashoutExternalId'];
        if (isset($_POST['c_feeType']) && empty($_POST['feetype'])) $_POST['feetype'] = $_POST['c_feeType'];
        if (isset($_POST['c_fee']) && empty($_POST['fee'])) $_POST['fee'] = $_POST['c_fee'];
        if (isset($_POST['c_amountMin']) && empty($_POST['amountmin'])) $_POST['amountmin'] = $_POST['c_amountMin'];
        if (isset($_POST['c_amountMax']) && empty($_POST['amountmax'])) $_POST['amountmax'] = $_POST['c_amountMax'];

        $is_api = $this->input->is_ajax_request() || strpos(strtolower($this->input->get_request_header('Accept') ?: ''), 'json') !== false || $this->input->get('json') == '1' || $this->input->method() === 'post';

        $this->form_validation->set_rules('id', 'Id', 'required');
        $this->form_validation->set_rules('chanelgroup', 'Chanel Group', 'required');
        $this->form_validation->set_rules('description', 'Description', 'trim|xss_clean');
        $this->form_validation->set_rules('externaldefault', 'External Default', 'required');
        $this->form_validation->set_rules('feetype', 'Fee Type', 'required');
        $this->form_validation->set_rules('fee', 'Fee', 'required|numeric');
        $this->form_validation->set_rules('amountmin', 'Amount Min', 'required|numeric');
        $this->form_validation->set_rules('amountmax', 'Amount Max', 'required|numeric');

        if ($this->form_validation->run() == FALSE) {
            $err = trim(preg_replace('/\s+/', ' ', strip_tags(validation_errors()))) ?: 'Validation failed.';
            $this->_respond($is_api, false, $err, 'channel/cashout');
            return;
        }

        $data = [
            'ref_cashoutChannelId'   => $this->input->post('id'),
            'c_cashoutChannelGroup'  => $this->input->post('chanelgroup'),
            'c_cashoutChannelGroup2' => $this->input->post('description') ?: $this->input->post('chanelgroup'),
            'c_cashoutExternalId'    => $this->input->post('externaldefault'),
            'c_feeType'              => $this->input->post('feetype'),
            'c_fee'                  => $this->input->post('fee'),
            'c_amountMin'            => $this->input->post('amountmin'),
            'c_amountMax'            => $this->input->post('amountmax')
        ];

        $existing = $this->db->get_where('cashout_external_x_channel', ['ref_cashoutChannelId' => $data['ref_cashoutChannelId']])->row_array();
        $result = $existing ? $this->Chanel->updateCashoutChannel($existing['id'], $data) : $this->Chanel->createCashoutChannel($data);

        if ($result === true) {
            $this->_respond($is_api, true, 'Data successfully saved', 'channel/cashout');
        } else {
            $msg = $this->_formatDbError($result['code'] ?? 0, 'cashout');
            $this->_respond($is_api, false, $msg, 'channel/cashout');
        }
    }

    public function updateCashinChanel()
    {
        $raw = json_decode($this->input->raw_input_stream, true);
        if (!empty($raw) && is_array($raw)) $_POST = array_merge($raw, $_POST);

        $is_api = $this->input->is_ajax_request() || strpos(strtolower($this->input->get_request_header('Accept') ?: ''), 'json') !== false || $this->input->get('json') == '1' || $this->input->method() === 'post';
        $id = $this->input->post('id') ?: $this->uri->segment(4);

        $this->form_validation->set_rules('id', 'Id', 'required');
        $this->form_validation->set_rules('chanelgroup', 'Chanel Group', 'required');
        $this->form_validation->set_rules('description', 'Description', 'trim|xss_clean');
        $this->form_validation->set_rules('externaldefault', 'External Default', 'required');
        $this->form_validation->set_rules('feetype', 'Fee Type', 'required');
        $this->form_validation->set_rules('fee', 'Fee', 'required|numeric');
        $this->form_validation->set_rules('settlementinterval', 'Settlement Interval', 'required|numeric');
        $this->form_validation->set_rules('amountmin', 'Amount Min', 'required|numeric');
        $this->form_validation->set_rules('amountmax', 'Amount Max', 'required|numeric');

        if ($this->form_validation->run() == FALSE) {
            $err = trim(preg_replace('/\s+/', ' ', strip_tags(validation_errors()))) ?: 'Validation failed.';
            $this->_respond($is_api, false, $err, 'channel/cashin');
            return;
        }

        $data = [
            'ref_cashinChannelId'  => $this->input->post('id'),
            'c_cashinChannelGroup' => $this->input->post('chanelgroup'),
            'c_cashinExternalId'   => $this->input->post('externaldefault'),
            'c_feeType'            => $this->input->post('feetype'),
            'c_fee'                => $this->input->post('fee'),
            'c_settlementInterval' => $this->input->post('settlementinterval'),
            'c_amountMin'          => $this->input->post('amountmin'),
            'c_amountMax'          => $this->input->post('amountmax')
        ];

        $result = $this->Chanel->updateCashinChannel($id, $data);
        if ($result === true) {
            $this->_respond($is_api, true, 'Data successfully updated', 'channel/cashin');
        } else {
            $msg = $this->_formatDbError($result['code'] ?? 0, 'cashin');
            $this->_respond($is_api, false, $msg, 'channel/cashin');
        }
    }

    public function updateCashOutChanel()
    {
        $raw = json_decode($this->input->raw_input_stream, true);
        if (!empty($raw) && is_array($raw)) $_POST = array_merge($raw, $_POST);

        $is_api = $this->input->is_ajax_request() || strpos(strtolower($this->input->get_request_header('Accept') ?: ''), 'json') !== false || $this->input->get('json') == '1' || $this->input->method() === 'post';
        $id = $this->input->post('id') ?: $this->uri->segment(4);

        $this->form_validation->set_rules('id', 'Id', 'required');
        $this->form_validation->set_rules('chanelgroup', 'Chanel Group', 'required');
        $this->form_validation->set_rules('description', 'Description', 'trim|xss_clean');
        $this->form_validation->set_rules('externaldefault', 'External Default', 'required');
        $this->form_validation->set_rules('feetype', 'Fee Type', 'required');
        $this->form_validation->set_rules('fee', 'Fee', 'required|numeric');
        $this->form_validation->set_rules('amountmin', 'Amount Min', 'required|numeric');
        $this->form_validation->set_rules('amountmax', 'Amount Max', 'required|numeric');

        if ($this->form_validation->run() == FALSE) {
            $err = trim(preg_replace('/\s+/', ' ', strip_tags(validation_errors()))) ?: 'Validation failed.';
            $this->_respond($is_api, false, $err, 'channel/cashout');
            return;
        }

        $data = [
            'ref_cashoutChannelId'   => $this->input->post('id'),
            'c_cashoutChannelGroup'  => $this->input->post('chanelgroup'),
            'c_cashoutChannelGroup2' => $this->input->post('description'),
            'c_cashoutExternalId'    => $this->input->post('externaldefault'),
            'c_feeType'              => $this->input->post('feetype'),
            'c_fee'                  => $this->input->post('fee'),
            'c_amountMin'            => $this->input->post('amountmin'),
            'c_amountMax'            => $this->input->post('amountmax')
        ];

        $result = $this->Chanel->updateCashoutChannel($id, $data);
        if ($result === true) {
            $this->_respond($is_api, true, 'Data successfully updated', 'channel/cashout');
        } else {
            $msg = $this->_formatDbError($result['code'] ?? 0, 'cashout');
            $this->_respond($is_api, false, $msg, 'channel/cashout');
        }
    }

    public function deleteCashInChanel($id = null)
    {
        is_logged_in();
        $is_api = $this->input->is_ajax_request() || strpos(strtolower($this->input->get_request_header('Accept') ?: ''), 'json') !== false || $this->input->get('json') == '1' || $this->input->method() === 'post';
        if (!$id) {
            $this->_respond($is_api, false, 'Invalid ID', 'channel/cashin');
            return;
        }

        $result = $this->Chanel->deleteCashinChannel($id);
        if ($result === true) {
            $this->_respond($is_api, true, 'Channel successfully deleted', 'channel/cashin');
        } else {
            $msg = $this->_formatDbError($result['code'] ?? 0, 'cashin');
            $this->_respond($is_api, false, $msg, 'channel/cashin');
        }
    }

    public function deleteCashOutChanel($id = null)
    {
        is_logged_in();
        $is_api = $this->input->is_ajax_request() || strpos(strtolower($this->input->get_request_header('Accept') ?: ''), 'json') !== false || $this->input->get('json') == '1' || $this->input->method() === 'post';
        if (!$id) {
            $this->_respond($is_api, false, 'Invalid ID', 'channel/cashout');
            return;
        }

        $result = $this->Chanel->deleteCashoutChannel($id);
        if ($result === true) {
            $this->_respond($is_api, true, 'Channel successfully deleted', 'channel/cashout');
        } else {
            $msg = $this->_formatDbError($result['code'] ?? 0, 'cashout');
            $this->_respond($is_api, false, $msg, 'channel/cashout');
        }
    }

    public function get_master_filter_options()
    {
        if (!$this->input->is_ajax_request()) return;
        $type = $this->input->post('type');
        $group = $this->input->post('group');

        $table = ($type === 'cashin') ? 'cashin_external_x_channel' : 'cashout_external_x_channel';
        $col_id = ($type === 'cashin') ? 'c_cashinExternalId' : 'c_cashoutExternalId';
        $col_group = ($type === 'cashin') ? 'c_cashinChannelGroup' : 'c_cashoutChannelGroup';

        $this->db->select("$col_id as provider")->from($table);
        if (!empty($group)) $this->db->where($col_group, $group);
        $this->db->where("$col_id !=", '')->where("$col_id IS NOT NULL", null, false)->group_by($col_id);
        $providers = $this->db->get()->result_array();

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['providers' => array_column($providers, 'provider')]));
    }
}
