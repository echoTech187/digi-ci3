<?php defined('BASEPATH') or exit('No direct script access allowed');

class ReportController extends CI_Controller {
   public function __construct() {
      parent::__construct();
      $this->load->library('session');
      $this->load->library('rbac');
      $this->load->library('pagination');
      $this->load->library('form_validation');
      $this->load->model('Model_user');
      $this->load->model('BalanceLogModel');
      is_logged_in();
   }

   public function balance_log()
   {
      is_logged_in();

      if (!$this->input->is_ajax_request()) {
         if ($this->input->post('search_merchant_balance_log') === null && 
             $this->input->get('search_merchant_balance_log') === null &&
             $this->input->post('search_date_balance_log') === null && 
             $this->input->post('search_date_balance_log_to') === null) {
            $this->session->unset_userdata('search_merchant_balance_log');
            $this->session->unset_userdata('search_date_balance_log');
            $this->session->unset_userdata('search_date_balance_log_to');
            $this->session->unset_userdata('search_ref_id_balance');
         }

         $post_search_merchant = $this->input->post('search_merchant_balance_log');
         $get_search_merchant = $this->input->get('search_merchant_balance_log');
         $search_merchant = $post_search_merchant !== null ? $post_search_merchant : ($get_search_merchant !== null ? $get_search_merchant : $this->session->userdata('search_merchant_balance_log'));
         
         $search_date_from = $this->input->post('search_date_balance_log') !== null ? $this->input->post('search_date_balance_log') : $this->session->userdata('search_date_balance_log');
         $search_date_to = $this->input->post('search_date_balance_log_to') !== null ? $this->input->post('search_date_balance_log_to') : $this->session->userdata('search_date_balance_log_to');

         $this->session->set_userdata('search_merchant_balance_log', $search_merchant);
         $this->session->set_userdata('search_date_balance_log', $search_date_from);
         $this->session->set_userdata('search_date_balance_log_to', $search_date_to);
      }

      $raw_json = json_decode($this->input->raw_input_stream, true);
      if (!empty($raw_json) && is_array($raw_json)) {
         foreach ($raw_json as $k => $v) {
            if ($this->input->post($k) === NULL) {
               $_POST[$k] = $v;
            }
         }
      }

      $is_api = $this->input->is_ajax_request()
         || strtolower((string)$this->input->get_request_header('X-Requested-With')) === 'xmlhttprequest'
         || strpos((string)$this->input->get_request_header('Content-Type'), 'json') !== false
         || strpos((string)$this->input->get_request_header('Accept'), 'json') !== false
         || $this->input->method() === 'post';

      if ($is_api) {
         try {
            $filters = [
               'merchant' => $this->session->userdata('search_merchant_balance_log'),
               'date_from' => $this->session->userdata('search_date_balance_log'),
               'date_to' => $this->session->userdata('search_date_balance_log_to')
            ];
            $out = $this->BalanceLogModel->get_datatables_handler($filters);
            $this->output
               ->set_content_type('application/json')
               ->set_output(is_string($out) ? $out : json_encode($out));
            return;
         } catch (Throwable $e) {
            log_message('error', 'Balance Log AJAX error: ' . $e->getMessage());
            $this->output
               ->set_content_type('application/json')
               ->set_output(json_encode(array(
                  "draw" => intval($this->input->post("draw")),
                  "recordsTotal" => 0,
                  "recordsFiltered" => 0,
                  "data" => array(),
                  "error" => "Error retrieving balance log data: " . $e->getMessage()
               )));
            return;
         }
      }

      $data['title'] = 'Balance Log';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['merchants'] = $this->BalanceLogModel->get_merchant();
      $data['search_merchant'] = $this->session->userdata('search_merchant_balance_log');
      $data['search_date_from'] = $this->session->userdata('search_date_balance_log');
      $data['search_date_to'] = $this->session->userdata('search_date_balance_log_to');
      $data['balance_logs'] = [];

      // Summary data for KPI cards
      $summary = $this->BalanceLogModel->get_summary();
      $data['total_logs']      = $summary->total_logs;
      $data['total_merchants'] = $summary->total_merchants;
      $data['total_settled']   = $summary->total_settled;
      $data['avg_settled']     = $summary->avg_settled;

      $this->load->view('admin/balance_log', $data);
   }
   
   public function report()
   {
      $this->load->model('AdminDownload');
      is_logged_in();

      if (!$this->input->is_ajax_request()) {
         if (empty($this->input->get()) && !$this->input->post()) {
            $this->session->unset_userdata('search_date');
            $this->session->unset_userdata('search_date_to');
            $this->session->unset_userdata('search_type');
            $this->session->unset_userdata('search_status');
         }

         $search_date = $this->input->post('search_date') !== null ? $this->input->post('search_date') : ($this->input->get('search_date') !== null ? $this->input->get('search_date') : $this->session->userdata('search_date'));
         $search_date_to = $this->input->post('search_date_to') !== null ? $this->input->post('search_date_to') : ($this->input->get('search_date_to') !== null ? $this->input->get('search_date_to') : $this->session->userdata('search_date_to'));
         $search_type = $this->input->post('search_type') !== null ? $this->input->post('search_type') : ($this->input->get('search_type') !== null ? $this->input->get('search_type') : $this->session->userdata('search_type'));
         $search_status = $this->input->post('search_status') !== null ? $this->input->post('search_status') : ($this->input->get('search_status') !== null ? $this->input->get('search_status') : $this->session->userdata('search_status'));

         if ($search_date !== null) $this->session->set_userdata('search_date', $search_date);
         if ($search_date_to !== null) $this->session->set_userdata('search_date_to', $search_date_to);
         if ($search_type !== null) $this->session->set_userdata('search_type', $search_type);
         if ($search_status !== null) $this->session->set_userdata('search_status', $search_status);
      }

      $raw_json = json_decode($this->input->raw_input_stream, true);
      if (!empty($raw_json) && is_array($raw_json)) {
         foreach ($raw_json as $k => $v) {
            if ($this->input->post($k) === NULL) {
               $_POST[$k] = $v;
            }
         }
      }

      $is_api = $this->input->is_ajax_request()
         || strtolower((string)$this->input->get_request_header('X-Requested-With')) === 'xmlhttprequest'
         || strpos((string)$this->input->get_request_header('Content-Type'), 'json') !== false
         || strpos((string)$this->input->get_request_header('Accept'), 'json') !== false
         || $this->input->method() === 'post';

      if ($is_api) {
         try {
            $filters = [
               'date' => $this->session->userdata('search_date'),
               'date_to' => $this->session->userdata('search_date_to'),
               'type' => $this->session->userdata('search_type'),
               'status' => $this->session->userdata('search_status')
            ];
            $out = $this->AdminDownload->get_datatables_handler($filters);
            $this->output
               ->set_content_type('application/json')
               ->set_output(is_string($out) ? $out : json_encode($out));
            return;
         } catch (Throwable $e) {
            log_message('error', 'Report AJAX error: ' . $e->getMessage());
            $this->output
               ->set_content_type('application/json')
               ->set_output(json_encode(array(
                  "draw" => intval($this->input->post("draw")),
                  "recordsTotal" => 0,
                  "recordsFiltered" => 0,
                  "data" => array(),
                  "error" => "Error retrieving report data: " . $e->getMessage()
               )));
            return;
         }
      }

      $data['title'] = 'Report';
      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['search_date'] = $this->session->userdata('search_date');
      $data['search_date_to'] = $this->session->userdata('search_date_to');
      $data['search_type'] = $this->session->userdata('search_type');
      $data['search_status'] = $this->session->userdata('search_status');
      $data['downloads'] = [];
      $data['pagination'] = '';
      $data['start'] = 0;

      $this->load->view('report/index', $data);
   }

   public function reset_download()
   {
      $this->session->unset_userdata('search_date');
      $this->session->unset_userdata('search_date_to');
      $this->session->unset_userdata('search_type');
      $this->session->unset_userdata('search_status');

      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $is_api_request = strpos($accept, 'json') !== false || $this->input->get('json') == '1';

      if ($is_api_request) {
         $this->output->set_content_type('application/json')->set_output(json_encode([
            'status' => true,
            'message' => 'Report download search filters reset successfully.'
         ]));
         return;
      }

      redirect('report/download');
   }

   public function download()
   {
      $filename = $this->input->get('filename') ?: $this->input->post('filename');
      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1';

      if (!empty($filename)) {
          // Standard report download path with candidate paths check
          $candidate_paths = [
              FCPATH . 'download_report/' . $filename,
              'C:/xampp74/htdocs/digi-ci3/download_report/' . $filename,
              'C:/xampp74/htdocs/gatewayservice/download_report/' . $filename,
              '/var/www/download_report/' . $filename,
              'C:/var/www/download_report/' . $filename,
              '/home/admin/public_html/download_report/' . $filename
          ];

          $filepath = null;
          foreach ($candidate_paths as $path) {
              if (file_exists($path)) {
                  $filepath = $path;
                  break;
              }
          }

         if ($filepath && file_exists($filepath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));

            readfile($filepath);
            return;
         } else {
            if ($is_api_request) {
               $this->output->set_content_type('application/json')->set_output(json_encode([
                  'status' => false,
                  'message' => 'Requested report file was not found on server.'
               ]));
               return;
            }
            echo 'File not found.';
         }
      } else {
         if ($is_api_request) {
            $this->output->set_content_type('application/json')->set_output(json_encode([
               'status' => false,
               'message' => 'Filename parameter is missing.'
            ]));
            return;
         }
         echo 'Filename parameter is missing.';
      }
   }

   public function reset_balance_log()
   {
      $this->session->unset_userdata('search_merchant_balance_log');
      $this->session->unset_userdata('search_date_balance_log');
      $this->session->unset_userdata('search_date_balance_log_to');

      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $is_api_request = strpos($accept, 'json') !== false || $this->input->get('json') == '1';

      if ($is_api_request) {
         $this->output->set_content_type('application/json')->set_output(json_encode([
            'status' => true,
            'message' => 'Balance log search filters reset successfully.'
         ]));
         return;
      }

      redirect('report/balance-log');
   }
}
