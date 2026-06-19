<?php defined('BASEPATH') or exit('No direct script access allowed');

class HelpCenterController extends CI_Controller
{
   public function __construct()
   {
      parent::__construct();
      // Load library dasar
      $this->load->library('session');
      $this->load->model('Model_user');
      
      // Pastikan user sudah login
      is_logged_in();
   }

   public function index()
   {
      $data['title'] = 'Help Center';
      $data['user'] = $this->Model_user->view_user()->row_array();
      
      // Load Parsedown library for dynamic Markdown rendering
      $this->load->library('parsedown');

      // Render index view

      
      $this->load->view('helpcenter/index', $data);
   }
}
