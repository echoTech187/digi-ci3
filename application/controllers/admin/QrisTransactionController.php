<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Controller khusus untuk menangani transaksi QRIS.
 * Bagian dari refactoring TransactionController untuk mengikuti standar OOP yang lebih modular.
 */
class QrisTransactionController extends CI_Controller
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
      $this->load->model('Qris');
      $this->load->model('QRISDynamic');
      $this->load->model('QRISRecurring');
      
      // Pastikan user sudah login
      is_logged_in();
      
      // Sinkronisasi variabel global untuk URL hit
      global $internalUrlHit;
      global $externalUrlHit;
      $this->internalUrlHit = $internalUrlHit;
      $this->externalUrlHit = $externalUrlHit;
   }

   public function qris()
   {
      $data['title'] = 'QRIS';
      $data['user'] = $this->Model_user->view_user()->row_array();

      $search_name_qris = $this->input->get('merchant') ?: $this->input->post('search_name_qris');
      $search_date_qris = $this->input->get('date_from') ?: $this->input->post('search_date_qris');
      $search_date_qris_to = $this->input->get('date_to') ?: $this->input->post('search_date_qris_to');
      $search_date_qris_settlement = $this->input->get('settlement') ?: $this->input->post('search_date_qris_settlement');

      if (!$this->input->is_ajax_request()) {
         $search_rrn = $this->input->get('rrn') ?: ($this->input->post('search_rrn') ?: '');
         $search_invoice_no = $this->input->get('invoice') ?: ($this->input->post('search_invoice_no') ?: '');
         $search_transid_qriss = $this->input->get('transid') ?: ($this->input->post('search_transactionid_ht') ?: '');
         
         if (!$this->input->get('rrn') && !$this->input->post('search_rrn') && 
             !$this->input->get('invoice') && !$this->input->post('search_invoice_no') &&
             !$this->input->get('transid') && !$this->input->post('search_transactionid_ht')) {
            $this->session->unset_userdata('last_search_qris');
         }
      } else {
         $search_rrn = $this->session->userdata('search_rrn');
         $search_invoice_no = $this->session->userdata('search_invoice_no');
         $search_transid_qriss = $this->session->userdata('search_transactionid_ht');
      }

      if ($search_name_qris !== null) $this->session->set_userdata('search_name_qris', $search_name_qris);
      if ($search_date_qris !== null) $this->session->set_userdata('search_date_qris', $search_date_qris);
      if ($search_date_qris_to !== null) $this->session->set_userdata('search_date_qris_to', $search_date_qris_to);
      if ($search_date_qris_settlement !== null) $this->session->set_userdata('search_date_qris_settlement', $search_date_qris_settlement);
      if ($search_invoice_no !== null) $this->session->set_userdata('search_invoice_no', $search_invoice_no);
      if ($search_rrn !== null) $this->session->set_userdata('search_rrn', $search_rrn);
      if ($search_transid_qriss !== null) $this->session->set_userdata('search_transactionid_ht', $search_transid_qriss);

      if ($this->input->is_ajax_request()) {
         try {
            $dtSearch = $this->input->post('search')['value'] ?? '';
            $oldSearch = $this->session->userdata('last_search_qris');

            if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
               $this->session->unset_userdata('search_date_qris');
               $this->session->unset_userdata('search_date_qris_to');
               $this->session->unset_userdata('search_name_qris');
               $this->session->unset_userdata('search_date_qris_settlement');
               $this->session->unset_userdata('search_transactionid_ht');
               $this->session->unset_userdata('search_rrn');
               $this->session->unset_userdata('search_invoice_no');
            }

            if ($dtSearch !== '') {
               $this->session->set_userdata('search_invoice_no', $dtSearch);
               $this->session->set_userdata('last_search_qris', $dtSearch);
            }

            $filters = [
               'merchant' => $this->session->userdata('search_name_qris'),
               'date_from' => $this->session->userdata('search_date_qris'),
               'date_to' => $this->session->userdata('search_date_qris_to'),
               'settlement' => $this->session->userdata('search_date_qris_settlement'),
               'rrn' => $this->session->userdata('search_rrn'),
               'invoice' => $this->session->userdata('search_invoice_no'),
               'transid' => $this->session->userdata('search_transactionid_ht')
            ];
            return $this->Qris->get_datatables_handler($filters);
         } catch (Throwable $e) {
            log_message('error', 'QRIS AJAX error: ' . $e->getMessage());
            echo json_encode(array(
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => array(),
               "error" => "Error retrieving QRIS data: " . $e->getMessage()
            ));
         }
      }

      $data['qriss'] = [];
      $data['start'] = 0;
      $data['pagination'] = '';
      $data['merchants'] = $this->Qris->get_merchant();

      $this->load->view('qris/list', $data);
   }

   public function resetqris()
   {
      $this->session->unset_userdata('search_date_qris');
      $this->session->unset_userdata('search_date_qris_to');
      $this->session->unset_userdata('search_name_qris');
      $this->session->unset_userdata('search_date_qris_settlement');
      $this->session->unset_userdata('search_invoice_no');
      $this->session->unset_userdata('search_rrn');
      $this->session->unset_userdata('search_transactionid_ht');
      $this->session->unset_userdata('last_search_qris');
      redirect('admin/qris');
   }

   public function qris_detail($id = NULL)
   {
      if (!$id) $id = $this->uri->segment(3);

      if (!$id) {
         $this->session->set_flashdata('error', 'Transaction ID not found.');
         redirect('admin/qris');
      }

      $data['user'] = $this->Model_user->view_user()->row_array();
      $data['title'] = 'Detail QRIS';
      $data['qris_data'] = $this->Qris->qris_detail($id);

      $displayId = '#' . $id;
      if (!empty($data['qris_data'])) {
         $displayId = '#' . $data['qris_data'][0]['c_invoiceNo'];
      }
      $data['breadcrumb_replace'] = [$id => $displayId];

      $this->load->view('qris/detail_qris', $data);
   }

   public function download_qris()
   {
      $search_date_qris = isset($_GET['search_date_qris']) ? $_GET['search_date_qris'] : '';
      $search_name_qris = isset($_GET['search_name_qris']) ? $_GET['search_name_qris'] : '';
      $search_date_qris_to = isset($_GET['search_date_qris_to']) ? $_GET['search_date_qris_to'] : '';
      $search_date_qris_settlement = isset($_GET['search_date_qris_settlement']) ? $_GET['search_date_qris_settlement'] : '';

      if (empty($search_name_qris) && (empty($search_date_qris) && empty($search_date_qris_settlement))) {
         $this->session->set_flashdata('error_message', 'Please fill all fields and search before continuing with download.');
         redirect('admin/qris');
      }
      
      $user = $this->Model_user->view_user()->row_array();
      $adminID = $user['id'];

      $additionalFilter = $search_name_qris . '|' . $search_date_qris . '|' . $search_date_qris_settlement;
      $data = array(
         'ref_adminId' => $adminID,
         'c_datetime' => date('Y-m-d H:i:s'),
         'c_additionalFilter' => $additionalFilter,
         'c_type' => 'Qris',
         'c_status' => 'Pending',
         'c_filename' => '',
      );

      if ($this->db->insert('admin_download', $data)) {
         $this->session->set_flashdata('success', 'Your request is being processed. Please go to Download Report menu.');
      } else {
         $this->session->set_flashdata('error', 'Failed request download');
      }

      redirect('admin/qris');
   }

   public function qris_dynamic_list()
   {
      if ($this->input->is_ajax_request()) {
         try {
            return $this->QRISDynamic->get_datatables_handler();
         } catch (Throwable $e) {
            log_message('error', 'QRIS Dynamic List AJAX error: ' . $e->getMessage());
            echo json_encode(array(
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => array(),
               "error" => "Error retrieving QRIS dynamic data: " . $e->getMessage()
            ));
         }
      }
   }

   public function qris_dynamic()
   {
      $data['title'] = 'QRIS Dynamic';
      $data['user'] = $this->Model_user->view_user()->row_array();

      if (!$this->input->is_ajax_request()) {
         $search_transid_qd = $this->input->get('transid') ?: ($this->input->post('search_transid_qd') ?: '');
         if (!$this->input->get('transid') && !$this->input->post('search_transid_qd')) {
            $this->session->unset_userdata('search_date_qd');
            $this->session->unset_userdata('search_date_qd_to');
            $this->session->unset_userdata('search_name_qd');
            $this->session->unset_userdata('search_transid_qd');
            $this->session->unset_userdata('search_status_transaction_qd');
            $this->session->unset_userdata('search_reff_label');
            $this->session->unset_userdata('last_search_qd');
         }
      } else {
         $search_transid_qd = $this->session->userdata('search_transid_qd');
      }

      $search_name_qd = $this->input->post('search_name_qd') != NULL ? $this->input->post('search_name_qd') : $this->session->userdata('search_name_qd');
      $search_date_qd = $this->input->post('search_date_qd') != NULL ? $this->input->post('search_date_qd') : $this->session->userdata('search_date_qd');
      $search_date_qd_to = $this->input->post('search_date_qd_to') != NULL ? $this->input->post('search_date_qd_to') : $this->session->userdata('search_date_qd_to');
      $search_status_transaction_qd = $this->input->post('search_status_transaction_qd') != NULL ? $this->input->post('search_status_transaction_qd') : $this->session->userdata('search_status_transaction_qd');
      $search_reff_label = $this->input->post('search_reff_label') != NULL ? $this->input->post('search_reff_label') : $this->session->userdata('search_reff_label');

      $this->session->set_userdata([
         'search_name_qd' => $search_name_qd,
         'search_date_qd' => $search_date_qd,
         'search_date_qd_to' => $search_date_qd_to,
         'search_transid_qd' => $search_transid_qd,
         'search_status_transaction_qd' => $search_status_transaction_qd,
         'search_reff_label' => $search_reff_label
      ]);

      if ($this->input->is_ajax_request()) {
         try {
            $dtSearch = $this->input->post('search')['value'] ?? '';
            $oldSearch = $this->session->userdata('last_search_qd');

            if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
               $this->session->unset_userdata('search_date_qd');
               $this->session->unset_userdata('search_date_qd_to');
               $this->session->unset_userdata('search_name_qd');
               $this->session->unset_userdata('search_status_transaction_qd');
               $this->session->unset_userdata('search_transid_qd');
               $this->session->unset_userdata('search_reff_label');
            }

            if ($dtSearch !== '') {
               $this->session->set_userdata('search_transid_qd', $dtSearch);
               $this->session->set_userdata('last_search_qd', $dtSearch);
            }

            $filters = [
               'merchant' => $this->session->userdata('search_name_qd'),
               'date' => $this->session->userdata('search_date_qd'),
               'date_to' => $this->session->userdata('search_date_qd_to'),
               'transid' => $this->session->userdata('search_transid_qd'),
               'status' => $this->session->userdata('search_status_transaction_qd'),
               'reff' => $this->session->userdata('search_reff_label')
            ];
            return $this->QRISDynamic->get_datatables_handler($filters);
         } catch (Throwable $e) {
            log_message('error', 'QRIS Dynamic AJAX error: ' . $e->getMessage());
            echo json_encode(array(
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => array(),
               "error" => "Error retrieving QRIS Dynamic data: " . $e->getMessage()
            ));
         }
      }

      $data['merchants'] = $this->QRISDynamic->get_merchant();
      $data['search_reff_label'] = $search_reff_label;
      $this->load->view('qris/qrisdynamic', $data);
   }

   public function resetqris_dynamic()
   {
      $this->session->unset_userdata('search_date_qd');
      $this->session->unset_userdata('search_date_qd_to');
      $this->session->unset_userdata('search_name_qd');
      $this->session->unset_userdata('search_transid_qd');
      $this->session->unset_userdata('search_status_transaction_qd');
      $this->session->unset_userdata('search_reff_label');
      $this->session->unset_userdata('last_search_qd');
      redirect('admin/qris_dynamic');
   }

   public function qris_recurring()
   {
      $data['title'] = 'QRIS Recurring';
      $data['user'] = $this->Model_user->view_user()->row_array();

      if (!$this->input->is_ajax_request()) {
         $search_transid_qr = $this->input->get('transid') ?: ($this->input->post('search_transid_qr') ?: '');
         if (!$this->input->get('transid') && !$this->input->post('search_name_qr')) {
            $this->session->unset_userdata('search_date_qr');
            $this->session->unset_userdata('search_date_qr_to');
            $this->session->unset_userdata('search_name_qr');
            $this->session->unset_userdata('search_submerchant_qr');
            $this->session->unset_userdata('search_transid_qr');
            $this->session->unset_userdata('last_search_qr');
         }
      } else {
         $search_transid_qr = $this->session->userdata('search_transid_qr');
      }

      $search_date_qr = $this->input->post('search_date_qr');
      $search_date_qr_to = $this->input->post('search_date_qr_to');
      $search_name_qr = $this->input->post('search_name_qr');
      $search_submerchant_qr = $this->input->post('search_submerchant_qr');

      if ($search_date_qr !== null) $this->session->set_userdata('search_date_qr', $search_date_qr);
      else $search_date_qr = $this->session->userdata('search_date_qr');

      if ($search_date_qr_to !== null) $this->session->set_userdata('search_date_qr_to', $search_date_qr_to);
      else $search_date_qr_to = $this->session->userdata('search_date_qr_to');

      if ($search_name_qr !== null) $this->session->set_userdata('search_name_qr', $search_name_qr);
      else $search_name_qr = $this->session->userdata('search_name_qr');

      if ($search_submerchant_qr !== null) $this->session->set_userdata('search_submerchant_qr', $search_submerchant_qr);
      else $search_submerchant_qr = $this->session->userdata('search_submerchant_qr');

      $this->session->set_userdata('search_transid_qr', $search_transid_qr);

      if ($this->input->is_ajax_request()) {
         try {
            $dtSearch = $this->input->post('search')['value'] ?? '';
            $oldSearch = $this->session->userdata('last_search_qr');

            if ($dtSearch === '' && $oldSearch !== '' && $oldSearch !== null) {
               $this->session->unset_userdata('search_date_qr');
               $this->session->unset_userdata('search_date_qr_to');
               $this->session->unset_userdata('search_name_qr');
               $this->session->unset_userdata('search_submerchant_qr');
               $this->session->unset_userdata('search_transid_qr');
            }

            if ($dtSearch !== '') {
               $this->session->set_userdata('search_transid_qr', $dtSearch);
               $this->session->set_userdata('last_search_qr', $dtSearch);
            }

            $filters = [
               'merchant' => $this->session->userdata('search_name_qr'),
               'date' => $this->session->userdata('search_date_qr'),
               'date_to' => $this->session->userdata('search_date_qr_to'),
               'transid' => $this->session->userdata('search_transid_qr'),
               'submerchant' => $this->session->userdata('search_submerchant_qr')
            ];
            return $this->QRISRecurring->get_datatables_handler($filters);
         } catch (Throwable $e) {
            log_message('error', 'QRIS Recurring AJAX error: ' . $e->getMessage());
            echo json_encode(array(
               "draw" => intval($this->input->post("draw")),
               "recordsTotal" => 0,
               "recordsFiltered" => 0,
               "data" => array(),
               "error" => "Error retrieving QRIS Recurring data: " . $e->getMessage()
            ));
         }
      }

      $data['merchants'] = $this->QRISRecurring->get_merchant();
      $this->load->view('qris/qrisrecurring', $data);
   }

   public function resetqris_recurring()
   {
      $this->session->unset_userdata('search_date_qr');
      $this->session->unset_userdata('search_date_qr_to');
      $this->session->unset_userdata('search_name_qr');
      $this->session->unset_userdata('search_submerchant_qr');
      $this->session->unset_userdata('search_transid_qr');
      redirect('admin/qris_recurring');
   }

   public function SendnotifikasiQRIS()
   {
      $ref_cashinPaymentQrisMpmId = $this->uri->segment(3);
      $refMerchantId = $this->uri->segment(4);

      if (!$ref_cashinPaymentQrisMpmId) {
         $this->session->set_flashdata('error', 'Transaction ID not found.');
         redirect('admin/qris');
      }

      $internalRequestBody = array(
         "msgType" => "consumer_notification_qris_mpm",
         "msgInfo" => array(
            "ref_cashinPaymentQrisMpmId" => $ref_cashinPaymentQrisMpmId,
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
      redirect('admin/qris');
   }

   public function getDetailQrisDynamicChannelExternal()
   {
      if (!$this->session->userdata('c_email')) {
         redirect('auth');
      }

      header('Content-Type: application/json');
      $ref_cashinExternalId = $this->input->post('ref_cashinExternalId');
      $parentId = $this->input->post('parentId');
      $ref_cashinExternalLogQrisMpmIdCreate = $this->input->post('ref_cashinExternalLogQrisMpmIdCreate');

      if (empty($ref_cashinExternalId)) {
         echo json_encode(['error' => 'Invalid data sent to server']);
         return;
      }

      $detailData = $this->QRISDynamic->getDataQrisDynamicChannelExternal($ref_cashinExternalId, $ref_cashinExternalLogQrisMpmIdCreate, $parentId);
      echo json_encode($detailData);
   }

   public function getDetailQrisRecurringChannelExternal()
   {
      if (!$this->session->userdata('c_email')) {
         redirect('auth');
      }

      header('Content-Type: application/json');
      $ref_cashinExternalId = $this->input->post('ref_cashinExternalId');
      $parentId = $this->input->post('parentId');
      $ref_cashinExternalLogQrisMpmIdCreate = $this->input->post('ref_cashinExternalLogQrisMpmIdCreate');

      if (empty($ref_cashinExternalId)) {
         echo json_encode(['error' => 'Invalid data sent to server']);
         return;
      }

      $detailData = $this->QRISRecurring->getDataQrisRecurringChannelExternal($ref_cashinExternalId, $ref_cashinExternalLogQrisMpmIdCreate, $parentId);
      echo json_encode($detailData);
   }
}
