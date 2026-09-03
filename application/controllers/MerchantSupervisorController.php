<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * MerchantSupervisorController
 * Manages supervisors, supervisor-assigned merchants, registration, and delegations.
 */
class MerchantSupervisorController extends CI_Controller
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

    public function merchant_spv()
    {
        $this->_parseRawJson();
        if (!$this->input->is_ajax_request() && $this->input->get('search_spv') === null && $this->input->post('search_spv') === null) {
            $this->session->unset_userdata('search_spv');
        }

        $search_spv = $this->input->get('search_spv') ?: $this->input->post('search_spv') ?: $this->session->userdata('search_spv');
        if ($search_spv !== null) {
            $this->session->set_userdata('search_spv', $search_spv);
        }

        if ($this->_isApi()) {
            try {
                $where = [];
                $status = $this->session->userdata('search_spv_status');
                if (!empty($status)) {
                    $where['c_status'] = $status;
                }

                $d1 = $this->session->userdata('search_spv_date_from');
                $d2 = $this->session->userdata('search_spv_date_to');
                if (!empty($d1)) {
                    $where['c_created_date >='] = $d1 . ' 00:00:00';
                }
                if (!empty($d2)) {
                    $where['c_created_date <='] = $d2 . ' 23:59:59';
                }

                $out = $this->Merchant->get_merchant_spv_handler($where, $this->session->userdata('search_spv'));
                return $this->output->set_content_type('application/json')->set_output(is_string($out) ? $out : json_encode($out));
            } catch (\Exception $e) {
                log_message('error', 'Supervisor AJAX error: ' . $e->getMessage());
                return $this->output->set_content_type('application/json')->set_output(json_encode([
                    "draw"            => intval($this->input->post("draw")),
                    "recordsTotal"    => 0,
                    "recordsFiltered" => 0,
                    "data"            => [],
                    "error"           => $e->getMessage()
                ]));
            }
        }

        if ($this->input->post('search_spv_status') !== null) {
            $this->session->set_userdata('search_spv_status', $this->input->post('search_spv_status'));
        }
        if ($this->input->post('search_spv_date_from') !== null) {
            $this->session->set_userdata('search_spv_date_from', $this->input->post('search_spv_date_from'));
        }
        if ($this->input->post('search_spv_date_to') !== null) {
            $this->session->set_userdata('search_spv_date_to', $this->input->post('search_spv_date_to'));
        }

        $data = [
            'title'                    => 'Merchant Supervisor',
            'user'                     => $this->Model_user->view_user()->row_array(),
            'total_merchants_assigned' => $this->db->where('c_refSupervisor IS NOT NULL')->count_all_results('merchant')
        ];
        $this->load->view('merchantspv/index', $data);
    }

    public function resetMerchantSpv()
    {
        $this->session->unset_userdata(['search_spv_status', 'search_spv_date_from', 'search_spv_date_to']);
        $accept = strtolower($this->input->get_request_header('Accept') ?: '');
        if (strpos($accept, 'json') !== false || $this->input->get('json') == '1') {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => true, 'message' => 'Supervisor filters reset.']));
        }
        redirect("merchant/supervisor");
    }

    public function listMerchants($supervisorId)
    {
        if (!$supervisorId) {
            $this->session->set_flashdata('error', 'Supervisor ID not found.');
            redirect('merchant/supervisor');
            return;
        }

        if ($this->input->is_ajax_request()) {
            try {
                $where = [];
                if ($this->session->userdata('search_spv_merchant_status')) {
                    $where['merchant.c_status'] = $this->session->userdata('search_spv_merchant_status');
                }
                if ($this->session->userdata('search_spv_merchant_openapi_status')) {
                    $where['merchant.c_openapiStatus'] = $this->session->userdata('search_spv_merchant_openapi_status');
                }
                if ($this->session->userdata('search_spv_merchant_level')) {
                    $where['merchant.c_merchantLevel'] = $this->session->userdata('search_spv_merchant_level');
                }

                $d1 = $this->session->userdata('search_spv_merchant_date_from');
                $d2 = $this->session->userdata('search_spv_merchant_date_to');
                if (!empty($d1)) {
                    $where['merchant.c_dateCreated >='] = $d1 . ' 00:00:00';
                }
                if (!empty($d2)) {
                    $where['merchant.c_dateCreated <='] = $d2 . ' 23:59:59';
                }

                $out = $this->Merchant->get_spv_merchants_handler($supervisorId, $where, $this->session->userdata('search_spv_merchant'));
                return $this->output->set_content_type('application/json')->set_output(is_string($out) ? $out : json_encode($out));
            } catch (\Exception $e) {
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

        if ($this->input->post('search_spv_merchant_status') !== null) {
            $this->session->set_userdata('search_spv_merchant_status', $this->input->post('search_spv_merchant_status'));
        }
        if ($this->input->post('search_spv_merchant_openapi_status') !== null) {
            $this->session->set_userdata('search_spv_merchant_openapi_status', $this->input->post('search_spv_merchant_openapi_status'));
        }
        if ($this->input->post('search_spv_merchant_level') !== null) {
            $this->session->set_userdata('search_spv_merchant_level', $this->input->post('search_spv_merchant_level'));
        }

        $spv = $this->db->get_where('merchant_supervisor', ['id' => $supervisorId])->row_array();
        if (!$spv) {
            $this->session->set_flashdata('error', 'Supervisor not found.');
            redirect('merchant/supervisor');
            return;
        }

        $data = [
            'title'          => 'Merchants managed by ' . ($spv['c_name'] ?? 'Supervisor'),
            'supervisor'     => $spv,
            'supervisor_id'  => $supervisorId,
            'user'           => $this->Model_user->view_user()->row_array(),
            'merchant_count' => $this->db->where('c_refSupervisor', $supervisorId)->count_all_results('merchant')
        ];
        $this->load->view('merchantspv/list_merchants', $data);
    }

    public function resetListMerchants($supervisorId)
    {
        $this->session->unset_userdata([
            'search_spv_merchant_status',
            'search_spv_merchant_openapi_status',
            'search_spv_merchant_level',
            'search_spv_merchant_date_from',
            'search_spv_merchant_date_to'
        ]);

        $accept = strtolower($this->input->get_request_header('Accept') ?: '');
        if (strpos($accept, 'json') !== false || $this->input->get('json') == '1') {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => true, 'message' => 'Merchant filters reset.']));
        }
        redirect("merchant/supervisor/merchants/" . $supervisorId);
    }

    public function detail_supervisor($supervisorId)
    {
        if (!$supervisorId) {
            redirect('merchant/supervisor');
            return;
        }

        $supervisor = $this->db->get_where('merchant_supervisor', ['id' => $supervisorId])->row_array();
        if (!$supervisor) {
            $this->session->set_flashdata('error', 'Supervisor data not found.');
            redirect('merchant/supervisor');
            return;
        }

        $data = [
            'title'          => 'Detail Supervisor: ' . ($supervisor['c_name'] ?? 'Unknown'),
            'user'           => $this->Model_user->view_user()->row_array(),
            'supervisor'     => $supervisor,
            'merchants'      => $this->db->get_where('merchant', ['c_refSupervisor' => $supervisorId])->result_array(),
            'merchant_count' => $this->db->where('c_refSupervisor', $supervisorId)->count_all_results('merchant')
        ];
        $this->load->view('merchantspv/detail', $data);
    }

    public function register()
    {
        $this->form_validation->set_rules('username', 'Username', 'required|trim|is_unique[merchant_supervisor.c_username]');
        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[merchant_supervisor.c_email]');
        $this->form_validation->set_rules('password', 'Password', 'required|trim|min_length[6]');

        if ($this->form_validation->run() == false) {
            $data = [
                'title' => 'Register Merchant Supervisor',
                'user'  => $this->Model_user->view_user()->row_array()
            ];
            $this->load->view('merchantspv/register', $data);
        } else {
            $insertData = [
                'c_username'     => $this->input->post('username', true),
                'c_name'         => $this->input->post('name', true),
                'c_email'        => $this->input->post('email', true),
                'c_password'     => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                'c_status'       => 'Active',
                'c_created_date' => date('Y-m-d H:i:s')
            ];
            $this->db->insert('merchant_supervisor', $insertData);
            $this->session->set_flashdata('message', '<div class="alert alert-success">Supervisor registered successfully!</div>');
            redirect('merchant/supervisor');
        }
    }

    public function update_status()
    {
        $id = $this->input->post('id');
        $status = $this->input->post('status');

        if (!$id || !in_array($status, ['Active', 'Not Active'])) {
            return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'Invalid parameters.']));
        }

        $this->db->where('id', $id)->update('merchant_supervisor', ['c_status' => $status]);
        return $this->output->set_content_type('application/json')->set_output(json_encode(['status' => true, 'message' => 'Status updated successfully.']));
    }
}
