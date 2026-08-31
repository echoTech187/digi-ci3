<?php defined('BASEPATH') or exit('No direct script access allowed');

class ServiceController extends CI_Controller {
   public function __construct() {
      parent::__construct();
      $this->load->library('session');
      $this->load->library('rbac');
      $this->load->library('pagination');
      $this->load->library('form_validation');
      $this->load->model('Model_user');
      $this->load->model('Chanel');
      is_logged_in();
   }

    private function _render_product_datatable($where, $view_name, $page_title, $requires_provider = false)
    {
        is_logged_in();
        $table = 'cashout_channel cc';
        $column_order = array(null, 'cc.c_caption', 'cc.id', 'cc.c_fee', null);
        $column_search = array('cc.c_caption', 'cc.id', 'cc.c_fee');
        $order = array('cc.id' => 'asc');

        if ($this->input->is_ajax_request()) {
            try {
                if ($requires_provider) {
                    $provider = $this->input->post('provider');
                    if (!empty($provider)) {
                        $where["cc.c_channelGroup2 LIKE '%" . $this->db->escape_like_str($provider) . "%' ESCAPE '!'"] = NULL;
                    }
                }
                return $this->Chanel->get_datatables_handler($table, $column_order, $column_search, $order, $where);
            } catch (Throwable $e) {
                log_message('error', 'Product AJAX error: ' . $e->getMessage());
                echo json_encode(array(
                    "draw" => intval($this->input->post("draw")),
                    "recordsTotal" => 0,
                    "recordsFiltered" => 0,
                    "data" => array(),
                    "error" => "Error retrieving product data: " . $e->getMessage()
                ));
            }
        }

        $data['title'] = $page_title;
        $data['user'] = $this->Model_user->view_user()->row_array();
        
        $data['chanel'] = [];
        $data['token'] = [];
        $data['topup_ovo'] = [];
        $data['google_play'] = [];
        $data['free_fire'] = [];
        $data['hago'] = [];
        $data['diamond_mlbb'] = [];
        $data['pubgmobile'] = [];
        $data['topupgopay'] = [];
        $data['topup_dana'] = [];
        $data['pagination'] = '';
        $data['start'] = 0;

        $this->load->view('product/' . $view_name, $data);
    }

    public function pulsa_reguler() {
        $where = array('cc.c_channelGroup' => 'ppob', "cc.c_channelGroup2 LIKE '%pulsa%' ESCAPE '!'" => NULL);
        $this->_render_product_datatable($where, 'pulsa_reguler', 'Pulsa Reguler', true);
    }

    public function paket_data() {
        $where = array('cc.c_channelGroup' => 'ppob', "cc.c_channelGroup2 LIKE '%paket_data%' ESCAPE '!'" => NULL);
        $this->_render_product_datatable($where, 'paket_data', 'Paket Data', true);
    }

    public function token_listrik() {
        $where = array('cc.c_channelGroup2' => 'token_pln');
        $this->_render_product_datatable($where, 'token_listrik', 'Token Listrik');
    }

    public function topupgopay() {
        $where = array('cc.c_channelGroup2' => 'topup_gopay');
        $this->_render_product_datatable($where, 'topup_gopay', 'Top Up Gopay');
    }

    public function topupdana() {
        $where = array('cc.c_channelGroup2' => 'topup_dana');
        $this->_render_product_datatable($where, 'topup_dana', 'Top Up Dana');
    }

    public function topupovo() {
        $where = array('cc.c_channelGroup2' => 'topup_ovo');
        $this->_render_product_datatable($where, 'topup_ovo', 'Top Up Ovo');
    }

    public function googleplay() {
        $where = array('cc.c_channelGroup2' => 'google_play');
        $this->_render_product_datatable($where, 'googleplay', 'Google Play');
    }

    public function freefire() {
        $where = array('cc.c_channelGroup2' => 'free_fire');
        $this->_render_product_datatable($where, 'freefire', 'Free Fire');
    }

    public function hago() {
        $where = array('cc.c_channelGroup2' => 'hago');
        $this->_render_product_datatable($where, 'hago', 'Hago');
    }

    public function mobilelegend() {
        $where = array('cc.c_channelGroup2' => 'diamond_mlbb');
        $this->_render_product_datatable($where, 'mobilelegend', 'Diamond Mobile Legend');
    }

    public function pubgmobile() {
        $where = array('cc.c_channelGroup2' => 'pubg_mobile');
        $this->_render_product_datatable($where, 'pubgmobile', 'PUBG Mobile');
    }

    private function _get_route_by_view($view_name) {
        $mapping = [
            'pulsa_reguler' => 'product/pulsa-reguler',
            'paket_data' => 'product/paket-data',
            'token_listrik' => 'product/token-listrik',
            'topup_gopay' => 'product/ewallet/gopay',
            'topup_dana' => 'product/ewallet/dana',
            'topup_ovo' => 'product/ewallet/ovo',
            'mobilelegend' => 'product/games/mobile-legend',
            'pubgmobile' => 'product/games/pubg',
            'freefire' => 'product/games/free-fire',
            'hago' => 'product/games/hago',
            'googleplay' => 'product/games/google-play',
        ];
        return isset($mapping[$view_name]) ? $mapping[$view_name] : 'dashboard';
    }

   public function createProduk()
   {
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

      $this->form_validation->set_rules('caption', 'Caption', 'required');
      $this->form_validation->set_rules('price', 'Price', 'required|numeric');

      $name = $this->input->post('name');

      if ($this->form_validation->run() == FALSE) {
         $clean_error = trim(preg_replace('/\s+/', ' ', strip_tags(validation_errors())));
         if ($is_api_request) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => $clean_error ?: 'Validation failed.']));
            return;
         }
         $this->session->set_flashdata('error', validation_errors());
         redirect($this->_get_route_by_view($name));
         return;
      }

      $caption = $this->input->post('caption');
      $id = $this->input->post('id') ?: str_replace(' ', '_', strtolower($caption));
      $description = $this->input->post('description') ?: $caption;
      $price = $this->input->post('price');
      $channelgroup = $this->input->post('channelgroup') ? $this->input->post('channelgroup') : 'ppob';
      $channelgroup2 = $this->input->post('channelgroup2');
      if (empty($channelgroup2)) {
          $mapping = [
              'topupovo' => 'topup_ovo',
              'topupgopay' => 'topup_gopay',
              'topupdana' => 'topup_dana',
              'token_listrik' => 'token_pln',
              'pubgmobile' => 'pubg_mobile',
              'mobilelegend' => 'diamond_mlbb',
              'hago' => 'hago',
              'googleplay' => 'google_play',
              'freefire' => 'free_fire',
              'pulsa_reguler' => 'pulsa',
          ];
          if (!empty($name) && isset($mapping[$name])) {
              $channelgroup2 = $mapping[$name];
          } else {
              $channelgroup2 = !empty($name) ? $name : 'pulsa';
          }
      }

      $channel_id = $id;
      
      // Insert or Update the master channel in cashout_channel
      $data_channel = array(
         'id' => $channel_id,
         'c_channelGroup' => $channelgroup,
         'c_channelGroup2' => $channelgroup2 ?: 'pulsa',
         'c_description' => $description,
         'c_caption' => $caption,
         'c_externalIdDefault' => 'portalpulsa',
         'c_feeType' => 'Fixed',
         'c_fee' => $price,
         'c_amountMin' => 0,
         'c_amountMax' => 0
      );

      $db_debug = $this->db->db_debug;
      $this->db->db_debug = FALSE;

      // Handle potential duplicates gracefully by updating caption/desc
      $exists = $this->db->get_where('cashout_channel', ['id' => $channel_id])->num_rows() > 0;
      if ($exists) {
          $this->db->where('id', $channel_id);
          $res = $this->db->update('cashout_channel', $data_channel);
          $err = $this->db->error();
          $success = ($res || $err['code'] == 0);
      } else {
          $res = $this->db->insert('cashout_channel', $data_channel);
          $err = $this->db->error();
          $success = ($res || $err['code'] == 0);
      }

      $this->db->db_debug = $db_debug;

      if ($success) {
         if ($is_api_request) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => true, 'message' => 'Product created successfully']));
            return;
         }
         $this->session->set_flashdata('message', 'Product created successfully');
         redirect($this->_get_route_by_view($name));
      } else {
         $msg = !empty($err['message']) ? $err['message'] : 'An error occurred while creating the product';
         if ($is_api_request) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => $msg]));
            return;
         }
         $this->session->set_flashdata('error', $msg);
         redirect($this->_get_route_by_view($name));
      }
   }

   public function updateProduct()
   {
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

        $id = $this->input->post('id') ?: $this->uri->segment(4);
        $existing = $id ? $this->db->get_where('cashout_channel', ['id' => $id])->row_array() : null;

        $this->form_validation->set_rules('id', 'Product ID', 'required');
        $this->form_validation->set_rules('caption', 'Caption', 'required');
        $this->form_validation->set_rules('price', 'Price', 'required|numeric');

        $caption = $this->input->post('caption');
        $description = $this->input->post('description') ?: ($existing ? $existing['c_description'] : '');
        $price = $this->input->post('price');
        $view_name = $this->input->post('name') ? $this->input->post('name') : $this->input->post('view_name');
        $channelgroup = $this->input->post('channelgroup') ? $this->input->post('channelgroup') : ($existing ? $existing['c_channelGroup'] : 'ppob');
        $channelgroup2 = $this->input->post('channelgroup2') ?: ($existing ? $existing['c_channelGroup2'] : '');
        if (empty($channelgroup2) && !empty($view_name)) {
          $mapping = [
              'topupovo' => 'topup_ovo',
              'topupgopay' => 'topup_gopay',
              'topupdana' => 'topup_dana',
              'token_listrik' => 'token_pln',
              'pubgmobile' => 'pubg_mobile',
              'mobilelegend' => 'diamond_mlbb',
              'hago' => 'hago',
              'googleplay' => 'google_play',
              'freefire' => 'free_fire',
          ];
          if (isset($mapping[$view_name])) {
              $channelgroup2 = $mapping[$view_name];
          }
        }
        if ($this->form_validation->run() == FALSE) {
            $clean_error = trim(preg_replace('/\s+/', ' ', strip_tags(validation_errors())));
            if ($is_api_request) {
                $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => $clean_error ?: 'Validation failed.']));
                return;
            }
            $this->session->set_flashdata('error', validation_errors());
            redirect($this->_get_route_by_view($view_name));
            return;
        }
      
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;

        $exists = $this->db->get_where('cashout_channel', ['id' => $id])->num_rows() > 0;
        $data_channel = [
            'c_caption' => $caption,
            'c_description' => $description,
            'c_fee' => $price
        ];
        if (!empty($channelgroup2)) {
            $data_channel['c_channelGroup2'] = $channelgroup2;
        }
        if ($exists) {
            $this->db->where('id', $id);
            $this->db->update('cashout_channel', $data_channel);
        } else {
            $data_channel['id'] = $id;
            $data_channel['c_channelGroup'] = $channelgroup;
            $data_channel['c_channelGroup2'] = $channelgroup2;
            $data_channel['c_externalIdDefault'] = 'portalpulsa';
            $data_channel['c_feeType'] = 'Fixed';
            $data_channel['c_fee'] = $price;
            $data_channel['c_amountMin'] = 0;
            $data_channel['c_amountMax'] = 0;
            $this->db->insert('cashout_channel', $data_channel);
        }

      // Update cashout_external_x_channel (fee)
      $data_external = array(
         'c_fee' => $price
      );

      if (!empty($channelgroup2)) {
         $data_external['c_cashoutChannelGroup2'] = $channelgroup2;
      }

      $update_result = $this->Chanel->update_cashout_chanel($id, $data_external);
      $this->db->db_debug = $db_debug;

      if ($update_result) {
         if ($is_api_request) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => true, 'message' => 'Product updated successfully']));
            return;
         }
         $this->session->set_flashdata('message', 'Product updated successfully');
         redirect($this->_get_route_by_view($view_name));
      } else {
         if ($is_api_request) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => false, 'message' => 'An error occurred while updating the product']));
            return;
         }
         $this->session->set_flashdata('error', 'An error occurred while updating the product');
         redirect($this->_get_route_by_view($view_name));
      }
   }

   public function deleteProduct($id = null)
   {
      $accept = strtolower($this->input->get_request_header('Accept') ?: '');
      $is_api_request = $this->input->is_ajax_request() || strpos($accept, 'json') !== false || $this->input->get('json') == '1' || $this->input->method() === 'post';

      if (!$id) $id = $this->uri->segment(4);

      $result = $this->Chanel->deleteCashoutChannel($id);
      
      if ($result === true) {
         $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'success', 'message' => 'Product deleted successfully']));
         return;
      } else {
         $friendlyMessage = 'Failed to delete product. Access denied or data is currently in use.';
         $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'error', 'message' => $friendlyMessage]));
         return;
      }
   }

}
