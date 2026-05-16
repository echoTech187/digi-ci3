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
      $data['title'] = 'Virtual Account';
      $data['user'] = $this->Model_user->view_user()->row_array();

      $search_name_va = $this->input->get('merchant') ?: $this->input->post('search_name_va');
      $search_date_va = $this->input->get('date') ?: $this->input->post('search_date_va');
      $search_date_va_to = $this->input->get('date_to') ?: $this->input->post('search_date_va_to');
      $search_date_va_settlement = $this->input->get('settlement') ?: $this->input->post('search_date_va_settlement');

      if (!$this->input->is_ajax_request()) {
         $search_va_number = $this->input->get('va_number') ?: ($this->input->post('search_va_number') ?: '');
         $search_va_transid = $this->input->get('transid') ?: ($this->input->post('search_va_transid') ?: '');
         $search_invoice_no = $this->input->get('invoice') ?: ($this->input->post('search_invoice_no') ?: '');
         
         if (!$this->input->get('va_number') && !$this->input->post('search_va_number') && 
             !$this->input->get('transid') && !$this->input->post('search_va_transid') &&
             !$this->input->get('invoice') && !$this->input->post('search_invoice_no')) {
            $this->session->unset_userdata('last_search_va');
         }
      } else {
         $search_va_number = $this->session->userdata('search_va_number');
         $search_va_transid = $this->session->userdata('search_va_transid');
         $search_invoice_no = $this->session->userdata('search_invoice_no');
      }

      if ($search_name_va !== null) $this->session->set_userdata('search_name_va', $search_name_va);
      if ($search_date_va !== null) $this->session->set_userdata('search_date_va', $search_date_va);
      if ($search_date_va_to !== null) $this->session->set_userdata('search_date_va_to', $search_date_va_to);
      if ($search_date_va_settlement !== null) $this->session->set_userdata('search_date_va_settlement', $search_date_va_settlement);
      
      $this->session->set_userdata('search_va_number', $search_va_number);
      $this->session->set_userdata('search_va_transid', $search_va_transid);
      $this->session->set_userdata('search_invoice_no', $search_invoice_no);

      if ($this->input->is_ajax_request()) {
         try {
            $dtSearch = $this->input->post('search')['value'] ?? '';
            $oldSearch = $this->session->userdata('last_search_va');
            
            if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
               $this->session->unset_userdata('search_date_va');
               $this->session->unset_userdata('search_date_va_to');
               $this->session->unset_userdata('search_name_va');
               $this->session->unset_userdata('search_date_va_settlement');
               $this->session->unset_userdata('search_va_number');
               $this->session->unset_userdata('search_va_transid');
               $this->session->unset_userdata('search_invoice_no');
            }

            if ($dtSearch !== '') {
               $this->session->set_userdata('search_va_number', $dtSearch);
               $this->session->set_userdata('last_search_va', $dtSearch);
            }

            $filters = [
               'date' => $this->session->userdata('search_date_va'),
               'date_to' => $this->session->userdata('search_date_va_to'),
               'merchant' => $this->session->userdata('search_name_va'),
               'settlement' => $this->session->userdata('search_date_va_settlement'),
               'va_number' => $this->session->userdata('search_va_number'),
               'transid' => $this->session->userdata('search_va_transid')
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
      $this->load->view('virtualaccount/list', $data);
   }

   public function resetVA()
   {
      $this->session->unset_userdata('search_date_va');
      $this->session->unset_userdata('search_date_va_to');
      $this->session->unset_userdata('search_name_va');
      $this->session->unset_userdata('search_date_va_settlement');
      $this->session->unset_userdata('search_va_number');
      $this->session->unset_userdata('search_va_transid');
      $this->session->unset_userdata('last_search_va');
      redirect('admin/virtual_account');
   }

   public function VA_detail($id = NULL)
   {
      if (!$id) $id = $this->uri->segment(3);

      if (!$id) {
         $this->session->set_flashdata('error', 'Transaction ID not found.');
         redirect('admin/virtual_account');
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
      $search_date_va = isset($_GET['search_date_va']) ? $_GET['search_date_va'] : '';
      $search_date_va_to = isset($_GET['search_date_va_to']) ? $_GET['search_date_va_to'] : '';
      $search_name_va = isset($_GET['search_name_va']) ? $_GET['search_name_va'] : '';
      $search_date_va_settlement = isset($_GET['search_date_va_settlement']) ? $_GET['search_date_va_settlement'] : '';

      if (empty($search_name_va) && (empty($search_date_va) || empty($search_date_va_settlement))) {
         $this->session->set_flashdata('error_message', 'Please fill all fields and search before continuing with download.');
         redirect('admin/virtual_account');
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

      redirect('admin/virtual_account');
   }

   public function Va_dynamic()
   {
      $data['title'] = 'VA Dynamic';
      $data['user'] = $this->Model_user->view_user()->row_array();

      if (!$this->input->is_ajax_request()) {
         $search_merchant_trxid = $this->input->get('transid') ?: ($this->input->post('search_merchant_trxid') ?: '');
         if (!$this->input->get('transid') && !$this->input->post('search_merchant_trxid') && !$this->input->get('va_number')) {
            $this->session->unset_userdata('search_name_vad');
            $this->session->unset_userdata('search_date_vad');
            $this->session->unset_userdata('search_date_vad_to');
            $this->session->unset_userdata('search_va_number');
            $this->session->unset_userdata('search_merchant_trxid');
            $this->session->unset_userdata('last_search_vad');
         }
      } else {
         $search_merchant_trxid = $this->session->userdata('search_merchant_trxid');
      }

      $search_name_vad = $this->input->post('search_name_vad') != NULL ? $this->input->post('search_name_vad') : $this->session->userdata('search_name_vad');
      $search_date_vad = $this->input->post('search_date_vad') != NULL ? $this->input->post('search_date_vad') : $this->session->userdata('search_date_vad');
      $search_date_vad_to = $this->input->post('search_date_vad_to') != NULL ? $this->input->post('search_date_vad_to') : $this->session->userdata('search_date_vad_to');
      $search_va_number = $this->input->get('va_number') ? $this->input->get('va_number') : ($this->input->post('search_va_number') != NULL ? $this->input->post('search_va_number') : $this->session->userdata('search_va_number'));

      $this->session->set_userdata([
         'search_name_vad' => $search_name_vad,
         'search_date_vad' => $search_date_vad,
         'search_date_vad_to' => $search_date_vad_to,
         'search_va_number' => $search_va_number,
         'search_merchant_trxid' => $search_merchant_trxid
      ]);

      if ($this->input->is_ajax_request()) {
         try {
            $dtSearch = $this->input->post('search')['value'] ?? '';
            $oldSearch = $this->session->userdata('last_search_vad');

            if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
               $this->session->unset_userdata('search_date_vad');
               $this->session->unset_userdata('search_date_vad_to');
               $this->session->unset_userdata('search_name_vad');
               $this->session->unset_userdata('search_va_number');
               $this->session->unset_userdata('search_merchant_trxid');
            }

            if ($dtSearch !== '') {
               $this->session->set_userdata('search_merchant_trxid', $dtSearch);
               $this->session->set_userdata('last_search_vad', $dtSearch);
            }

            $filters = [
               'merchant' => $this->session->userdata('search_name_vad'),
               'date' => $this->session->userdata('search_date_vad'),
               'date_to' => $this->session->userdata('search_date_vad_to'),
               'va_number' => $this->session->userdata('search_va_number'),
               'merchant_trxid' => $this->session->userdata('search_merchant_trxid')
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
      $this->load->view('virtualaccount/vadynamic', $data);
   }

   public function resetVa_dynamic()
   {
      $this->session->unset_userdata('search_name_vad');
      $this->session->unset_userdata('search_date_vad');
      $this->session->unset_userdata('search_date_vad_to');
      $this->session->unset_userdata('search_va_number');
      $this->session->unset_userdata('search_merchant_trxid');
      $this->session->unset_userdata('last_search_vad');
      redirect('admin/Va_dynamic');
   }

   public function VA_recurring()
   {
      $data['title'] = 'VA Recurring';
      $data['user'] = $this->Model_user->view_user()->row_array();

      if (!$this->input->is_ajax_request()) {
         $search_transid_var = $this->input->get('transid') ?: ($this->input->post('search_transid_var') ?: '');
         $search_va_number = $this->input->get('va_number') ?: ($this->input->post('search_va_number') ?: '');
         
         if (!$this->input->get('transid') && !$this->input->get('va_number') && !$this->input->post('search_name_var')) {
            $this->session->unset_userdata('search_date_var');
            $this->session->unset_userdata('search_date_var_to');
            $this->session->unset_userdata('search_name_var');
            $this->session->unset_userdata('search_submerchant_var');
            $this->session->unset_userdata('search_transid_var');
            $this->session->unset_userdata('search_va_number_var');
            $this->session->unset_userdata('last_search_var');
         }
      } else {
         $search_transid_var = $this->session->userdata('search_transid_var');
         $search_va_number = $this->session->userdata('search_va_number_var');
      }

      $search_name_var = $this->input->post('search_name_var');
      $search_date_var = $this->input->post('search_date_var');
      $search_date_var_to = $this->input->post('search_date_var_to');
      $search_submerchant_var = $this->input->post('search_submerchant_var');

      if ($search_date_var) $this->session->set_userdata('search_date_var', $search_date_var);
      else $search_date_var = $this->session->userdata('search_date_var');

      if ($search_date_var_to) $this->session->set_userdata('search_date_var_to', $search_date_var_to);
      else $search_date_var_to = $this->session->userdata('search_date_var_to');

      if ($search_name_var) $this->session->set_userdata('search_name_var', $search_name_var);
      else $search_name_var = $this->session->userdata('search_name_var');

      if ($search_submerchant_var) $this->session->set_userdata('search_submerchant_var', $search_submerchant_var);
      else $search_submerchant_var = $this->session->userdata('search_submerchant_var');

      $this->session->set_userdata('search_transid_var', $search_transid_var);
      $this->session->set_userdata('search_va_number_var', $search_va_number);

      if ($this->input->is_ajax_request()) {
         try {
            $dtSearch = $this->input->post('search')['value'] ?? '';
            $oldSearch = $this->session->userdata('last_search_var');

            if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
               $this->session->unset_userdata('search_date_var');
               $this->session->unset_userdata('search_date_var_to');
               $this->session->unset_userdata('search_name_var');
               $this->session->unset_userdata('search_submerchant_var');
               $this->session->unset_userdata('search_transid_var');
            }

            if ($dtSearch !== '') {
               $this->session->set_userdata('search_transid_var', $dtSearch);
               $this->session->set_userdata('last_search_var', $dtSearch);
            }

            $filters = [
               'merchant' => $this->session->userdata('search_name_var'),
               'date' => $this->session->userdata('search_date_var'),
               'submerchant' => $this->session->userdata('search_submerchant_var'),
               'transid' => $this->session->userdata('search_transid_var'),
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
      $this->load->view('virtualaccount/varecurring', $data);
   }

   public function resetVa_recurring()
   {
      $this->session->unset_userdata('search_date_var');
      $this->session->unset_userdata('search_name_var');
      $this->session->unset_userdata('search_submerchant_var');
      $this->session->unset_userdata('search_transid_var');
      $this->session->unset_userdata('search_va_number_var');
      $this->session->unset_userdata('last_search_var');
      redirect('admin/VA_recurring');
   }

   public function SendnotifikasiVA()
   {
      $ref_cashinPaymentVaId = $this->uri->segment(3);
      $refMerchantId = $this->uri->segment(4);

      if (!$ref_cashinPaymentVaId) {
         $this->session->set_flashdata('error', 'Transaction ID not found.');
         redirect('admin/virtual_account');
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
      redirect('admin/virtual_account');
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
