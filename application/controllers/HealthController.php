<?php
defined('BASEPATH') or exit('No direct script access allowed');

class HealthController extends CI_Controller {
    public function __construct() {
        parent::__construct();
    }

    public function dbCheck() {
        // Load database configuration
        if (!file_exists(APPPATH . 'config/database.php')) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'offline', 'message' => 'Database config not found']));
            return;
        }

        include(APPPATH . 'config/database.php');
        if (!isset($active_group) || !isset($db[$active_group])) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'offline', 'message' => 'Active group not configured']));
            return;
        }

        $db_config = $db[$active_group];

        $host = $db_config['hostname'];
        $user = $db_config['username'];
        $pass = $db_config['password'];
        $dbname = $db_config['database'];
        $port = 3306;

        if (strpos($host, ':') !== false) {
            list($host, $port) = explode(':', $host);
            $port = (int)$port;
        }

        $link = mysqli_init();
        if (!$link) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'offline', 'message' => 'MySQLi init failed']));
            return;
        }

        // Set a 100-second timeout to allow robust connections to remote staging/production databases
        $link->options(MYSQLI_OPT_CONNECT_TIMEOUT, 100);

        @$link->real_connect($host, $user, $pass, $dbname, $port);

        if ($link->connect_error) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'offline', 'message' => $link->connect_error]));
        } else {
            $link->close();
            $this->output->set_content_type('application/json')->set_output(json_encode(['status' => 'online']));
        }
    }
}
