<?php defined('BASEPATH') OR exit('No direct script access allowed');

class BalanceExternal extends CI_Model
{
    public function get_balance_external()
    {
        $sql = "SELECT id, c_datetimeCreated, gidi, paylabs, gv, paydgn
                FROM external_balance_log
                ORDER BY c_datetimeCreated DESC";

        return $this->db->query($sql)->result();
    }
}