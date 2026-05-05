<?php defined('BASEPATH') or exit('No direct script access allowed');

global $internalUrlHit;

class Model_user extends CI_Model
{
    public function __construct() {
        parent::__construct();
        
        global $internalUrlHit;

        $this->internalUrlHit = $internalUrlHit;
    }

    private static $user_cache = null;
    
    public function view_user()
    {
        if (self::$user_cache !== null) {
            return self::$user_cache;
        }

        self::$user_cache = $this->db->get_where('admin', ['c_email' => $this->session->userdata('c_email')]);
        return self::$user_cache;
    }

    public function saldo()
    {
        $this->db->select('id, c_name, c_email, c_status, c_merchantLevel, c_balanceTotal, c_balanceHold, c_openapiStatus', FALSE);
        $merchant = $this->db->get_where('merchant', ['c_email' => $this->session->userdata('c_email')])->row();
        if ($merchant) {
           
            $merchantId = $merchant->id;

            $url = $this->internalUrlHit.'/Merchant/balanceQuery';
            
            $data = json_encode(array(
                'merchantId' => $merchantId
            ));

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);

            curl_close($ch);

            $result = json_decode($response, true);

            return $result;
        } else {
            return false; 
        }
    }

    public function updatePassword($email, $newPassword)
    {
        $this->db->set('c_password', $newPassword);
        $this->db->where('c_email', $email);
        $this->db->update('admin');
    }

}
