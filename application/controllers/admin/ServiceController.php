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
        $column_order = array(null, 'cc.c_caption', 'cc.c_description', 'cc.c_fee', null);
        $column_search = array('cc.c_caption', 'cc.c_description', 'cc.c_fee');
        $order = array('cc.id' => 'asc');

        if ($this->input->is_ajax_request()) {
            $draw = intval($this->input->post("draw"));
            $start = intval($this->input->post("start"));
            $length = intval($this->input->post("length"));

            if ($requires_provider) {
                $provider = $this->input->post('provider');
                if (!empty($provider)) {
                    $where["cc.c_channelGroup2 LIKE '%" . $this->db->escape_like_str($provider) . "%' ESCAPE '!'"] = NULL;
                }
            }

            $list = $this->Chanel->get_datatables($table, $column_order, $column_search, $order, $where);
            $dataItems = array();
            $no = $start;

            foreach ($list as $items) {
                $no++;
                $row = array();
                $row['no'] = $no;
                $row['c_caption'] = $items->c_caption;
                $row['c_description'] = $items->c_description;
                $row['c_fee'] = $items->c_fee;
                $row['c_channelGroup2'] = $items->c_channelGroup2;
                $row['id'] = $items->id;

                $dataItems[] = $row;
            }

            $output = array(
                "draw" => $draw,
                "recordsTotal" => $this->Chanel->count_all_dt($table, $where),
                "recordsFiltered" => $this->Chanel->count_filtered($table, $column_order, $column_search, $order, $where),
                "data" => $dataItems,
            );
            echo json_encode($output);
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

   public function createProduk()
   {
      $this->form_validation->set_rules('caption', 'Caption', 'required');
      $this->form_validation->set_rules('description', 'Description', 'required');
      $this->form_validation->set_rules('price', 'Price', 'required|numeric');

      $caption = $this->input->post('caption');
      $id = str_replace(' ', '_', $caption);
      $description = $this->input->post('description');
      $price = $this->input->post('price');
      $channelgroup = $this->input->post('channelgroup');
      $channelgroup2 = $this->input->post('channelgroup2');
      $name = $this->input->post('name');

      if ($this->form_validation->run() == FALSE) {

         $this->load->view('admin/' . $name);
      } else {
         $data = array(
            'id' => $id,
            'c_caption' => $caption,
            'c_description' => $description,
            'c_fee' => $price,
            'c_channelGroup' => $channelgroup,
            'c_channelGroup2' => $channelgroup2,
            'c_externalIdDefault' => 'portalpulsa',
            'c_feeType' => 'Fixed',
            'c_amountMin' => 0,
            'c_amountMax' => 0
         );

         if ($this->Chanel->insert_cashout_chanel($data)) {
            $this->session->set_flashdata('message', 'Product created successfully');
            redirect('admin/' . $name);
         } else {

            $this->session->set_flashdata('error', 'An error occurred while creating the product');
            redirect('admin/' . $name);
         }
      }
   }

   public function updateProduct()
   {
      $this->form_validation->set_rules('id', 'Product ID', 'required');
      $this->form_validation->set_rules('caption', 'Caption', 'required');
      $this->form_validation->set_rules('description', 'Description', 'required');
      $this->form_validation->set_rules('price', 'Price', 'required|numeric');

      $id = $this->input->post('id');
      $caption = $this->input->post('caption');
      $description = $this->input->post('description');
      $price = $this->input->post('price');
      $view_name = $this->input->post('view_name');
      $channelgroup2 = $this->input->post('channelgroup2');

      if ($this->form_validation->run() == FALSE) {
         $this->session->set_flashdata('error', validation_errors());
         redirect('admin/' . $view_name);
      } else {
         $data = array(
            'c_caption' => $caption,
            'c_description' => $description,
            'c_fee' => $price
         );

         if (!empty($channelgroup2)) {
            $data['c_channelGroup2'] = $channelgroup2;
         }

         if ($this->Chanel->update_cashout_chanel($id, $data)) {
            $this->session->set_flashdata('message', 'Product updated successfully');
            redirect('admin/' . $view_name);
         } else {
            $this->session->set_flashdata('error', 'An error occurred while updating the product');
            redirect('admin/' . $view_name);
         }
      }
   }

}
