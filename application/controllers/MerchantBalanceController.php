<?php defined('BASEPATH') or exit('No direct script access allowed');

class MerchantBalanceController extends CI_Controller
{
   public function __construct()
   {
      parent::__construct();
      $this->load->library('session');
      $this->load->library('rbac');
      $this->load->library('pagination');
      $this->load->library('form_validation');
      $this->load->model('Model_user');
      $this->load->model('Mutation_model');
      $this->load->model('Chanel');
      $this->load->model('Merchant');
      is_logged_in();
      global $internalUrlHit;
      global $externalUrlHit;
      $this->internalUrlHit = $internalUrlHit;
      $this->externalUrlHit = $externalUrlHit;
   }

   public function createCreditBalance()
   {
      is_logged_in();
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

      $merchantId  = $this->input->post('merchantId');
      $channelId   = $this->input->post('channelId') ?: 'SYSTEM';
      $description = $this->input->post('description');
      $amount      = $this->input->post('rawAmountCredit');

      if (empty($merchantId) || empty($amount)) {
         $errorMessage = 'Required fields merchantId and amount must be provided.';
         if ($is_api_request) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => $errorMessage]));
            return;
         }
         $this->session->set_flashdata('error_message', $errorMessage);
         redirect('merchant/manage');
         return;
      }

      $internalRequestBody = [
         "merchantId" => $merchantId,
         "channelId"  => $channelId,
         'description' => $description ?: 'Credit balance added by Admin',
         'amount'      => $amount
      ];
      $internalUrlHit = $this->internalUrlHit . "/Merchant/creditBalance";
      $response = $this->_internalCurl($internalUrlHit, $internalRequestBody);
      $decoded = json_decode($response, true);
      if ($is_api_request) {
         if ($response !== false && isset($decoded['responseCode']) && $decoded['responseCode'] === 'SUCCESS') {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => true, 'message' => 'Credit balance successfully added.']));
         } else {
            $msg = isset($decoded['responseMessage']) ? $decoded['responseMessage'] : (isset($decoded['responseCode']) ? $decoded['responseCode'] : 'Failed to process request.');
            if (is_array($msg)) $msg = implode(', ', $msg);
            $cleanMsg = trim(preg_replace('/\s+/', ' ', strip_tags($msg)));
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => "Internal Error: " . ($cleanMsg ?: 'Curl Failed')]));
         }
         return;
      }

      if ($response !== false && isset($decoded['responseCode']) && $decoded['responseCode'] === 'SUCCESS') {
         $this->session->set_flashdata('success', 'Credit Balance Success.');
      } else {
         $this->session->set_flashdata('error', 'Failed to send data.');
      }
      redirect('merchant/manage');
   }

   public function createDebitBalance()
   {
      is_logged_in();
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

      $merchantId  = $this->input->post('merchantIdDebit');
      $channelId   = $this->input->post('channelId') ?: 'SYSTEM';
      $description = $this->input->post('description');
      $amount      = $this->input->post('rawAmountDebit');

      if (empty($merchantId) || empty($amount)) {
         $errorMessage = 'Required fields merchantId and amount must be provided.';
         if ($is_api_request) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => $errorMessage]));
            return;
         }
         $this->session->set_flashdata('error_message', $errorMessage);
         redirect('merchant/manage');
         return;
      }

      // ── CHECK AVAILABLE BALANCE ──
      $balanceRequestBody = ["merchantId" => $merchantId];
      $balanceUrlHit = $this->internalUrlHit . "/Merchant/balanceQuery";
      $balanceResponseRaw = $this->_internalCurl($balanceUrlHit, $balanceRequestBody);
      $balanceResponse = json_decode($balanceResponseRaw, true);

      if (!$balanceResponse || !isset($balanceResponse['responseCode']) || $balanceResponse['responseCode'] !== 'SUCCESS') {
         $errorMessage = 'Failed to retrieve merchant balance.';
         if ($is_api_request) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => $errorMessage, 'response' => $balanceResponse]));
            return;
         }
         $this->session->set_flashdata('error', $errorMessage);
         redirect('merchant/manage');
         return;
      }

      $availableBalance = floatval($balanceResponse['responseDetail']['balanceAvailable']);
      if (floatval($amount) > $availableBalance) {
         $errorMessage = 'Debit amount cannot exceed available balance (Rp ' . number_format($availableBalance, 0, ',', '.') . ').';
         if ($is_api_request) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => $errorMessage]));
            return;
         }
         $this->session->set_flashdata('error', $errorMessage);
         redirect('merchant/manage');
         return;
      }

      $internalRequestBody = [
         "merchantId" => $merchantId,
         "channelId"  => $channelId,
         'description' => $description ?: 'Debit balance processed by Admin',
         'amount'      => $amount
      ];

      $internalUrlHit = $this->internalUrlHit . "/Merchant/debitBalance";
      $response = $this->_internalCurl($internalUrlHit, $internalRequestBody);
      $decoded = json_decode($response, true);

      if ($is_api_request) {
         if ($response !== false && isset($decoded['responseCode']) && $decoded['responseCode'] === 'SUCCESS') {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => true, 'message' => 'Debit balance successfully processed.']));
         } else {
            $msg = isset($decoded['responseMessage']) ? $decoded['responseMessage'] : (isset($decoded['responseCode']) ? $decoded['responseCode'] : 'Failed to process request.');
            if (is_array($msg)) $msg = implode(', ', $msg);
            $cleanMsg = trim(preg_replace('/\s+/', ' ', strip_tags($msg)));
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => $cleanMsg ?: 'Failed to process request.']));
         }
         return;
      }

      if ($response !== false && isset($decoded['responseCode']) && $decoded['responseCode'] === 'SUCCESS') {
         $this->session->set_flashdata('success', 'Debit Balance Success.');
      } else {
         $this->session->set_flashdata('error', 'Failed to send data.');
      }
      redirect('merchant/manage');
   }

   private function _internalCurl($url, $data)
   {
      $ch = curl_init();
      curl_setopt_array($ch, [
         CURLOPT_URL => $url,
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_TIMEOUT => 30,
         CURLOPT_FOLLOWLOCATION => true,
         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
         CURLOPT_SSL_VERIFYHOST => 0,
         CURLOPT_SSL_VERIFYPEER => 0,
         CURLOPT_CUSTOMREQUEST => 'POST',
         CURLOPT_POSTFIELDS => json_encode($data),
         CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
      ]);
      $response = curl_exec($ch);
      curl_close($ch);
      return $response;
   }

}
