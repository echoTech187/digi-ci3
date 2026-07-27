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
      $isAjax = $this->input->is_ajax_request();

      $merchantId  = $this->input->post('merchantId');
      $channelId   = $this->input->post('channelId');
      $description = $this->input->post('description');
      $amount      = $this->input->post('rawAmountCredit');

      if (empty($merchantId) || empty($channelId) || empty($amount)) {
         $errorMessage = 'All fields are required.';
         if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $errorMessage]);
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
      if ($isAjax) {
         header('Content-Type: application/json');
         if ($response !== false && isset($decoded['responseCode']) && $decoded['responseCode'] === 'SUCCESS') {
            echo json_encode(['status' => 'success', 'message' => 'Credit balance successfully added.']);
         } else {
            $msg = isset($decoded['responseMessage']) ? $decoded['responseMessage'] : (isset($decoded['responseCode']) ? $decoded['responseCode'] : 'Failed to process request.');
            if (is_array($msg)) $msg = implode(', ', $msg);
            $rawHtml = htmlspecialchars($response !== false ? $response : 'Curl Failed');
            echo json_encode(['status' => 'error', 'message' => "Internal Error: " . $msg . " <br><br>RAW Response: " . $rawHtml]);
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
      $isAjax = $this->input->is_ajax_request();

      $merchantId  = $this->input->post('merchantIdDebit');
      $channelId   = $this->input->post('channelId');
      $description = $this->input->post('description');
      $amount      = $this->input->post('rawAmountDebit');

      if (empty($merchantId) || empty($channelId) || empty($amount)) {
         $errorMessage = 'All fields are required.';
         if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $errorMessage]);
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
         if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $errorMessage,'response'=>$balanceResponse]);
            return;
         }
         $this->session->set_flashdata('error', $errorMessage);
         redirect('merchant/manage');
         return;
      }

      $availableBalance = floatval($balanceResponse['responseDetail']['balanceAvailable']);
      if (floatval($amount) > $availableBalance) {
         $errorMessage = 'Debit amount cannot exceed available balance (Rp ' . number_format($availableBalance, 0, ',', '.') . ').';
         if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $errorMessage]);
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

      if ($isAjax) {
         header('Content-Type: application/json');
         if ($response !== false && isset($decoded['responseCode']) && $decoded['responseCode'] === 'SUCCESS') {
            echo json_encode(['status' => 'success', 'message' => 'Debit balance successfully processed.']);
         } else {
            $msg = isset($decoded['responseMessage']) ? $decoded['responseMessage'] : (isset($decoded['responseCode']) ? $decoded['responseCode'] : 'Failed to process request.');
            if (is_array($msg)) $msg = implode(', ', $msg);
            echo json_encode(['status' => 'error', 'message' => $msg]);
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

}
