<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller khusus untuk menangani transaksi Virtual Account.
 * Bagian dari refactoring TransactionController untuk mengikuti standar OOP yang lebih modular.
 */
class VirtualAccountTransactionController extends CI_Controller
{
   public function __construct()
   {
      parent::__construct();
      // Load library dasar
      $this->load->library('session');
      $this->load->library('rbac');
      $this->load->library('pagination');
      $this->load->library('form_validation');
      
      // Load model dasar
      $this->load->model('Model_user');
      $this->load->model('Mutation_model');
      $this->load->model('Chanel');
      $this->load->model('VirtualAccount');
      $this->load->model('VADynamic');
      $this->load->model('VARecurring');
      
      // Pastikan user sudah login
      is_logged_in();
      
      // Sinkronisasi variabel global untuk URL hit
      global $internalUrlHit;
      global $externalUrlHit;
      $this->internalUrlHit = $internalUrlHit;
      $this->externalUrlHit = $externalUrlHit;
   }

   public function virtual_account()
   {
      // Auto-reset if accessed directly without any parameters (GET or POST)
      if (!$this->input->is_ajax_request() && empty($this->input->get()) && !$this->input->post()) {
         $this->resetVA(false);
      }

      $data['title'] = 'Virtual Account';
      $data['user'] = $this->Model_user->view_user()->row_array();

      // Sync from GET/POST to Session
      $field_map = [
         'search_va_name'            => 'search_name_va',
         'search_va_date1'           => 'search_date_va',
         'search_va_date2'           => 'search_date_va_to',
         'search_va_date_settlement' => 'search_date_va_settlement',
         'search_va_channel'         => 'search_channel_va',
         'search_va_number'          => 'search_va_number',
         'search_va_transid'         => 'search_va_transid',
         'search_va_invoice_no'      => 'search_invoice_no',
      ];

      $get_fallback = [
         'search_va_name'            => 'merchant',
         'search_va_date1'           => 'date',
         'search_va_date2'           => 'date_to',
         'search_va_date_settlement' => 'settlement',
         'search_va_channel'         => 'channel',
         'search_va_number'          => 'va_number',
         'search_va_transid'         => 'transid',
         'search_va_invoice_no'      => 'invoice',
      ];

      foreach ($field_map as $session_key => $post_key) {
         $val = $this->input->post($post_key);
         if ($val === NULL && isset($get_fallback[$session_key])) {
            $val = $this->input->get($get_fallback[$session_key]);
         }
         if ($val !== NULL) $this->session->set_userdata($session_key, $val);
      }

      // Deep Linking & Main Search Sync
      $active_search = $this->input->get('q') ?: $this->input->get('invoice') ?: $this->input->get('transid') ?: $this->input->get('va_number');
      if ($active_search) $this->session->set_userdata('last_dt_search_va', $active_search);

      if ($this->input->is_ajax_request()) {
         try {
            $dtSearch = $this->input->post('search')['value'] ?? '';
            $oldSearch = $this->session->userdata('last_dt_search_va');

            if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
               $this->session->unset_userdata(['last_dt_search_va', 'search_va_invoice_no', 'search_va_transid', 'search_va_number']);
            }

            if ($dtSearch !== '') {
               if (is_numeric($dtSearch)) {
                  $this->session->set_userdata('search_va_number', $dtSearch);
               } else {
                  $this->session->set_userdata('search_va_invoice_no', $dtSearch);
               }
               $this->session->set_userdata('last_dt_search_va', $dtSearch);
            }

            $filters = [
               'date' => $this->session->userdata('search_va_date1'),
               'date_to' => $this->session->userdata('search_va_date2'),
               'merchant' => $this->session->userdata('search_va_name'),
               'settlement' => $this->session->userdata('search_va_date_settlement'),
               'channel' => $this->session->userdata('search_va_channel'),
               'va_number' => $this->session->userdata('search_va_number'),
               'transid' => $this->session->userdata('search_va_transid'),
               'invoice_no' => $this->session->userdata('search_va_invoice_no')
            ];
            return $this->VirtualAccount->get_datatables_handler($filters);
         } catch (Throwable $e) {
            log_message('error', 'VA AJAX error: ' . $e->getMessage());
            echo json_encode(array(
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => array(),
               "error" => "Error retrieving VA data: " . $e->getMessage()
            ));
         }
      }

      $data['merchants'] = $this->VirtualAccount->get_merchant();
      $data['internal_channels'] = $this->VirtualAccount->get_internal_channels();
      $this->load->view('virtualaccount/list', $data);
   }

   public function resetVA($redirect = true)
   {
      $this->session->unset_userdata([
         'search_va_date1',
         'search_va_date2',
         'search_va_date_settlement',
         'search_va_name',
         'search_va_channel',
         'search_va_number',
         'search_va_transid',
         'search_va_invoice_no',
         'last_dt_search_va'
      ]);
      if ($redirect) redirect('finance/virtual-account');
   }

   public function VA_detail($id = NULL)
   {
      if (!$id) {
         redirect('finance/virtual-account');
      }

      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['title'] = 'Detail VA';
      $data['va_data'] = $this->VirtualAccount->va_detail($id);

      $displayId = '#' . $id;
      if (!empty($data['va_data'])) {
         $displayId = '#' . $data['va_data'][0]['c_invoiceNo'];
      }
      $data['breadcrumb_replace'] = [$id => $displayId];

      $this->load->view('virtualaccount/detail_va', $data);
   }

   public function download_VA()
   {
      $search_date_va = isset($_GET['search_va_date1']) ? $_GET['search_va_date1'] : '';
      $search_date_va_to = isset($_GET['search_va_date2']) ? $_GET['search_va_date2'] : '';
      $search_name_va = isset($_GET['search_va_name']) ? $_GET['search_va_name'] : '';
      $search_date_va_settlement = isset($_GET['search_va_date_settlement']) ? $_GET['search_va_date_settlement'] : '';

      if (empty($search_name_va) && (empty($search_date_va) || empty($search_date_va_settlement))) {
         $this->session->set_flashdata('error_message', 'Please fill all fields and search before continuing with download.');
         redirect('finance/virtual-account');
      }

      $user = $this->Model_user->view_user()->row_array();
      $adminID = $user['id'];

      $additionalFilter = $search_name_va . '|' . $search_date_va . '|' . $search_date_va_to . '|' . $search_date_va_settlement;
      $data = array(
         'ref_adminId' => $adminID,
         'c_datetime' => date('Y-m-d H:i:s'),
         'c_additionalFilter' => $additionalFilter,
         'c_type' => 'Va',
      );

      if ($this->db->insert('admin_download', $data)) {
         $this->session->set_flashdata('success', 'Your request is being processed. Please go to Download Report menu.');
      } else {
         $this->session->set_flashdata('error', 'Failed request download');
      }

      redirect('finance/virtual-account');
   }

   public function Va_dynamic()
   {
      // Auto-reset if accessed directly without any parameters (GET or POST)
      if (!$this->input->is_ajax_request() && empty($this->input->get()) && !$this->input->post()) {
         $this->resetVa_dynamic(false);
      }

      $data['title'] = 'VA Dynamic';
      $data['user'] = $this->Model_user->view_user()->row_array();

      // Sync from GET/POST to Session
      $field_map = [
         'search_vadynamic_name'      => 'search_name_vad',
         'search_vadynamic_date1'     => 'search_date_vad',
         'search_vadynamic_date2'     => 'search_date_vad_to',
         'search_vadynamic_status'    => 'search_status_transaction_vad',
         'search_vadynamic_transid'   => 'search_transid_vad',
         'search_vadynamic_va_number' => 'search_va_number',
         'search_vadynamic_channel'   => 'search_channel_vadynamic',
         'search_vadynamic_external'  => 'search_external_vadynamic'
      ];

      $get_fallback = [
         'search_vadynamic_name'      => 'merchant',
         'search_vadynamic_transid'   => 'transid',
         'search_vadynamic_va_number' => 'va_number'
      ];

      foreach ($field_map as $session_key => $post_key) {
         $val = $this->input->post($post_key);
         if ($val === NULL && isset($get_fallback[$session_key])) {
            $val = $this->input->get($get_fallback[$session_key]);
         }
         if ($val !== NULL) $this->session->set_userdata($session_key, $val);
      }

      $active_search = $this->input->get('q') ?: $this->input->get('transid') ?: $this->input->get('va_number');
      if ($active_search) $this->session->set_userdata('last_dt_search_vadynamic', $active_search);

      if ($this->input->is_ajax_request()) {
         try {
            $dtSearch = $this->input->post('search')['value'] ?? '';
            $oldSearch = $this->session->userdata('last_dt_search_vadynamic');

            if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
               $this->session->unset_userdata(['last_dt_search_vadynamic', 'search_vadynamic_transid', 'search_vadynamic_va_number']);
            }

            if ($dtSearch !== '') {
               if (is_numeric($dtSearch)) {
                  $this->session->set_userdata('search_vadynamic_va_number', $dtSearch);
               } else {
                  $this->session->set_userdata('search_vadynamic_transid', $dtSearch);
               }
               $this->session->set_userdata('last_dt_search_vadynamic', $dtSearch);
            }

            $filters = [
               'merchant' => $this->session->userdata('search_vadynamic_name'),
               'date' => $this->session->userdata('search_vadynamic_date1'),
               'date_to' => $this->session->userdata('search_vadynamic_date2'),
               'va_number' => $this->session->userdata('search_vadynamic_va_number'),
               'merchant_trxid' => $this->session->userdata('search_vadynamic_transid'),
               'status' => $this->session->userdata('search_vadynamic_status'),
               'channel' => $this->session->userdata('search_vadynamic_channel'),
               'external_channel' => $this->session->userdata('search_vadynamic_external')
            ];
            return $this->VADynamic->get_datatables_handler($filters);
         } catch (Throwable $e) {
            log_message('error', 'VA Dynamic AJAX error: ' . $e->getMessage());
            echo json_encode(array(
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => array(),
               "error" => "Error retrieving VA Dynamic data: " . $e->getMessage()
            ));
         }
      }

      $data['merchants'] = $this->VADynamic->get_merchant();
      $data['internal_channels'] = $this->VirtualAccount->get_internal_channels();
      $data['external_channels'] = $this->VirtualAccount->get_external_channels();
      $this->load->view('virtualaccount/vadynamic', $data);
   }

   public function resetVa_dynamic($redirect = true)
   {
      $this->session->unset_userdata([
         'search_vadynamic_name',
         'search_vadynamic_date1',
         'search_vadynamic_date2',
         'search_vadynamic_status',
         'search_vadynamic_transid',
         'search_vadynamic_va_number',
         'search_vadynamic_channel',
         'search_vadynamic_external',
         'last_dt_search_vadynamic'
      ]);
      if ($redirect) redirect('virtual-account/dynamic');
   }

   public function VA_recurring()
   {
      // Auto-reset if accessed directly without any parameters (GET or POST)
      if (!$this->input->is_ajax_request() && empty($this->input->get()) && !$this->input->post()) {
         $this->resetVa_recurring(false);
      }

      $data['title'] = 'VA Recurring';
      $data['user'] = $this->Model_user->view_user()->row_array();

      // Sync from GET/POST to Session
      $field_map = [
         'search_varecurring_name'        => 'search_name_var',
         'search_varecurring_date1'       => 'search_date_var',
         'search_varecurring_date2'       => 'search_date_var_to',
         'search_varecurring_submerchant' => 'search_submerchant_var',
         'search_varecurring_transid'     => 'search_transid_var',
         'search_varecurring_va_number'   => 'search_va_number_var',
         'search_varecurring_status'      => 'search_status_transaction_var',
         'search_varecurring_channel'     => 'search_channel_varecurring',
         'search_varecurring_external'    => 'search_external_varecurring'
      ];

      $get_fallback = [
         'search_varecurring_name'        => 'merchant',
         'search_varecurring_transid'     => 'transid',
         'search_varecurring_va_number'   => 'va_number'
      ];

      foreach ($field_map as $session_key => $post_key) {
         $val = $this->input->post($post_key);
         if ($val === NULL && isset($get_fallback[$session_key])) {
            $val = $this->input->get($get_fallback[$session_key]);
         }
         if ($val !== NULL) $this->session->set_userdata($session_key, $val);
      }

      $active_search = $this->input->get('q') ?: $this->input->get('transid') ?: $this->input->get('va_number');
      if ($active_search) $this->session->set_userdata('last_dt_search_varecurring', $active_search);

      if ($this->input->is_ajax_request()) {
         try {
            $dtSearch = $this->input->post('search')['value'] ?? '';
            $oldSearch = $this->session->userdata('last_dt_search_varecurring');

            if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
               $this->session->unset_userdata(['last_dt_search_varecurring', 'search_varecurring_transid', 'search_varecurring_va_number']);
            }

            if ($dtSearch !== '') {
               if (is_numeric($dtSearch)) {
                  $this->session->set_userdata('search_varecurring_va_number', $dtSearch);
               } else {
                  $this->session->set_userdata('search_varecurring_transid', $dtSearch);
               }
               $this->session->set_userdata('last_dt_search_varecurring', $dtSearch);
            }

            $filters = [
               'merchant' => $this->session->userdata('search_varecurring_name'),
               'date' => $this->session->userdata('search_varecurring_date1'),
               'date_to' => $this->session->userdata('search_varecurring_date2'),
               'submerchant' => $this->session->userdata('search_varecurring_submerchant'),
               'transid' => $this->session->userdata('search_varecurring_transid'),
               'va_number' => $this->session->userdata('search_varecurring_va_number'),
               'status' => $this->session->userdata('search_varecurring_status'),
               'channel' => $this->session->userdata('search_varecurring_channel'),
               'external_channel' => $this->session->userdata('search_varecurring_external')
            ];
            return $this->VARecurring->get_datatables_handler($filters);
         } catch (Throwable $e) {
            log_message('error', 'VA Recurring AJAX error: ' . $e->getMessage());
            echo json_encode(array(
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => array(),
               "error" => "Error retrieving VA Recurring data: " . $e->getMessage()
            ));
         }
      }

      $data['merchants'] = $this->VARecurring->get_merchant();
      $data['internal_channels'] = $this->VirtualAccount->get_internal_channels();
      $data['external_channels'] = $this->VirtualAccount->get_external_channels();
      $this->load->view('virtualaccount/varecurring', $data);
   }

   public function resetVa_recurring($redirect = true)
   {
      $this->session->unset_userdata([
         'search_varecurring_name',
         'search_varecurring_date1',
         'search_varecurring_date2',
         'search_varecurring_submerchant',
         'search_varecurring_transid',
         'search_varecurring_va_number',
         'search_varecurring_status',
         'search_varecurring_channel',
         'search_varecurring_external',
         'last_dt_search_varecurring'
      ]);
      if ($redirect) redirect('virtual-account/recurring');
   }

   public function SendnotifikasiVA()
   {
      $ref_cashinPaymentVaId = $this->uri->segment(3);
      $refMerchantId = $this->uri->segment(4);

      if (!$ref_cashinPaymentVaId) {
         $this->session->set_flashdata('error', 'Transaction ID not found.');
         redirect('finance/virtual-account');
      }

      $internalRequestBody = array(
         "msgType" => "consumer_notification_va",
         "msgInfo" => array(
            "ref_cashinPaymentVaId" => $ref_cashinPaymentVaId,
            "merchantId" => $refMerchantId
         )
      );

      $internalUrlHit = $this->internalUrlHit . "/Rabbitmq/createQueue";

      $internalCurl = curl_init();
      curl_setopt_array($internalCurl, array(
         CURLOPT_URL => $internalUrlHit,
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_ENCODING => '',
         CURLOPT_MAXREDIRS => 10,
         CURLOPT_TIMEOUT => 30,
         CURLOPT_FOLLOWLOCATION => true,
         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
         CURLOPT_SSL_VERIFYHOST => 0,
         CURLOPT_SSL_VERIFYPEER => 0,
         CURLOPT_CUSTOMREQUEST => 'POST',
         CURLOPT_POSTFIELDS => json_encode($internalRequestBody),
         CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
      ));

      curl_exec($internalCurl);
      curl_close($internalCurl);

      $this->session->set_flashdata('success', 'Notification has resend');
      redirect('finance/virtual-account');
   }

   public function getDetailVaDynamicChannelExternal()
   {
      if (!$this->session->userdata('c_email')) {
         redirect('auth');
      }

      header('Content-Type: application/json');
      $ref_cashinExternalId = $this->input->post('ref_cashinExternalId');
      $parentId = $this->input->post('parentId');
      $ref_cashinExternalLogVaIdCreate = $this->input->post('ref_cashinExternalLogVaIdCreate');

      if (empty($ref_cashinExternalId)) {
         echo json_encode(['error' => 'Invalid data sent to server']);
         return;
      }

      $detailData = $this->VADynamic->getDataVaDynamicChannelExternal($ref_cashinExternalId, $ref_cashinExternalLogVaIdCreate, $parentId);
      echo json_encode($detailData);
   }

   public function getDetailVaRecurringChannelExternal()
   {
      if (!$this->session->userdata('c_email')) {
         redirect('auth');
      }

      header('Content-Type: application/json');
      $ref_cashinExternalId = $this->input->post('ref_cashinExternalId');
      $parentId = $this->input->post('parentId');
      $ref_cashinExternalLogVaIdCreate = $this->input->post('ref_cashinExternalLogVaIdCreate');

      if (empty($ref_cashinExternalId)) {
         echo json_encode(['error' => 'Invalid data sent to server']);
         return;
      }

      $detailData = $this->VARecurring->getDataVaRecurringChannelExternal($ref_cashinExternalId, $ref_cashinExternalLogVaIdCreate, $parentId);
      echo json_encode($detailData);
   }
}
