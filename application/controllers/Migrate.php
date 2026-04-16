<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migrate extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Hanya izinkan akses dari CLI atau environment development
        // Hapus atau sesuaikan blok ini jika ingin dijalankan via browser di local
        if (!is_cli() && ENVIRONMENT === 'production') {
            show_error('Migrations can only be run from the command line.');
        }
    }

    public function index()
    {
        $this->load->library('migration');

        if ($this->migration->latest() === FALSE) {
            show_error($this->migration->error_string());
        } else {
            echo "🚀 Migration executed successfully! All indexes and tables are up to date." . PHP_EOL;
        }
    }
}
