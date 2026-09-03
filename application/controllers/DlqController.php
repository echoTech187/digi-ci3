<?php defined('BASEPATH') or exit('No direct script access allowed');

class DlqController extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->library('rbac');
        $this->load->model('Model_user');
        $this->load->model('DlqModel');

        global $internalUrlHit;
        $this->internalUrlHit = $internalUrlHit;    
        is_logged_in(); // Assumes this helper exists globally
    }

    public function index() {
        $data['title'] = 'Monitoring DLQ (Failed Notifications)';
        $data['user'] = $this->Model_user->view_user()->row_array();
        
        // Fetch active merchants for the filter dropdown
        $this->db->select('id, c_name');
        $this->db->where('c_status', 'Active');
        $this->db->order_by('c_name', 'ASC');
        $data['merchants'] = $this->db->get('merchant')->result_array();

        // 1. Clear session if direct access (not ajax) without parameters
        if (!$this->input->is_ajax_request() && $this->input->get('search_channel') === null && $this->input->post('search_channel') === null) {
            $this->session->unset_userdata([
                'last_dt_search_dlq',
                'search_dlq_merchant',
                'search_dlq_date1',
                'search_dlq_date2'
            ]);
        }
        
        // Pass session values to view for UI persistence
        $data['search_channel'] = $this->session->userdata('last_dt_search_dlq');
        $data['search_merchant'] = $this->session->userdata('search_dlq_merchant');
        $data['search_date1'] = $this->session->userdata('search_dlq_date1');
        $data['search_date2'] = $this->session->userdata('search_dlq_date2');
        
        $this->load->view('admin/dlq/index', $data);
    }

    public function ajax_list() {
        if (!$this->input->is_ajax_request()) return;

        // Get filter inputs from POST (AJAX from DataTables)
        $search_channel = $this->input->post('search_channel'); // actually dt-search is search[value] natively but we kept search_channel
        if ($search_channel === null) { // if not explicitly passed, fallback to standard DT search if any
            $dt_search = $this->input->post('search');
            $search_channel = isset($dt_search['value']) ? $dt_search['value'] : null;
        }

        $merchant_id = $this->input->post('merchant_id');
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');

        // Update sessions if values were submitted
        if ($search_channel !== null) $this->session->set_userdata('last_dt_search_dlq', $search_channel);
        if ($merchant_id !== null) $this->session->set_userdata('search_dlq_merchant', $merchant_id);
        if ($start_date !== null) $this->session->set_userdata('search_dlq_date1', $start_date);
        if ($end_date !== null) $this->session->set_userdata('search_dlq_date2', $end_date);

        // Read active filters from session
        $search_channel = $this->session->userdata('last_dt_search_dlq');
        $merchant_id = $this->session->userdata('search_dlq_merchant');
        $start_date = $this->session->userdata('search_dlq_date1');
        $end_date = $this->session->userdata('search_dlq_date2');
        
        $output = $this->DlqModel->getDlqDataTable($search_channel, $merchant_id, $start_date, $end_date);
        echo $output;
    }

    public function retry_single() {
        if (!$this->input->is_ajax_request()) return;

        $id = $this->input->post('id');
        if (!$id) {
            echo json_encode(['status' => false, 'message' => 'ID not provided']);
            return;
        }

        $res = $this->_process_retry($id);
        echo json_encode($res);
    }

    private function _process_retry($id) {
        $dlq = $this->DlqModel->get_dlq_by_id($id);
        if (!$dlq) {
            return ['status' => false, 'message' => 'Transaction not found'];
        }

        // Hit RabbitMQ API internally to create Queue
        $ref_col = '';
        $msgType = '';
        if ($dlq['type'] == 'ewallet') {
            $ref_col = 'ref_cashinPaymentEwalletId';
            $msgType = 'consumer_notification_ewallet';
        } else if ($dlq['type'] == 'virtual-account') {
            $ref_col = 'ref_cashinPaymentVaId';
            $msgType = 'consumer_notification_va';
        } else if ($dlq['type'] == 'qris-mpm') {
            $ref_col = 'ref_cashinPaymentQrisMpmId';
            $msgType = 'consumer_notification_qris_mpm';
        } else if ($dlq['type'] == 'transfer') {
            $ref_col = 'ref_cashoutPaymentBifastId';
            $msgType = 'consumer_notification_transfer';
        }

        if (!$ref_col) {
            return ['status' => false, 'message' => 'Unknown transaction type'];
        }

        $msgInfo = [
            'merchantId' => $dlq['ref_merchantId'],
            $ref_col => $dlq['ref_transactionId']
        ];

        $payload = [
            'msgType' => $msgType,
            'msgInfo' => $msgInfo,
            'is_manual_retry' => true
        ];

        // Push to internal rabbitmq creator
        // In local/production, adjust the base URL of internal gateway
        $internal_url = $this->internalUrlHit."/Rabbitmq/createQueue";

        $ch = curl_init($internal_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Content-Length: ' . strlen(json_encode($payload))]);
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $resData = json_decode($response, true);

        if ($httpcode == 200 && isset($resData['responseCode']) && $resData['responseCode'] === 'SUCCESS') {
            // Update status ke 0 (On-Process Retry) agar hilang dari tabel Failed
            $this->db->where('id', $id)->update('log_failed_notification_dlq', ['c_status' => 0]);
            
            // Note: We don't delete here. The Python consumer will delete it via DeleteDlq_post if it truly succeeds.
            return ['status' => true, 'message' => 'Transaction pushed to queue successfully. Waiting for consumer to process.'];
        } else {
            $errDetail = isset($resData['responseCode']) ? $resData['responseCode'] : ('HTTP Code ' . $httpcode);
            return ['status' => false, 'message' => 'Failed to push to queue. Error: ' . $errDetail];
        }
    }

    public function download() {
        $merchant_id = $this->input->get('merchant_id');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        $user = $this->Model_user->view_user()->row_array();
        $adminID = isset($user['id']) ? $user['id'] : 0;
        $additionalFilter = "";
        if($merchant_id){
            $additionalFilter .= $merchant_id . "|";
        }
        if($start_date){
            $additionalFilter .= $start_date . "|";
        }
        if($end_date){
            $additionalFilter .= $end_date;
        }
        $data = array(
           'ref_adminId' => $adminID,
           'c_datetime' => date('Y-m-d H:i:s'),
           'c_additionalFilter' => $additionalFilter,
           'c_type' => 'Notification',
        );

        if ($this->db->insert('admin_download', $data)) {
           $this->session->set_flashdata('success', 'Your download request is being processed. Please go to <a href="' . base_url('report/download') . '" class="font-weight-bold alert-link" style="text-decoration:underline;">Financial Exports</a> menu to retrieve the file.');
        } else {
           $this->session->set_flashdata('error', 'Failed to request download.');
        }

        redirect('notifications');
    }
}
