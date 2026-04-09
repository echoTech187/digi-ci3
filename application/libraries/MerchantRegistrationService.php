<?php defined('BASEPATH') or exit('No direct script access allowed');

class MerchantRegistrationService
{
    protected $CI;

    public function __construct()
    {
        // Mendapatkan instance utama CodeIgniter
        $this->CI =& get_instance();
        // Me-load library database jika belum
        $this->CI->load->database();
    }

    /**
     * Meregistrasikan Merchant Supervisor baru
     * 
     * @param array $requestData Data $_POST dari form
     * @return bool
     * @throws Exception Jika validasi atau query gagal
     */
    public function registerSupervisor($requestData)
    {
        // 1. Validasi Password
        if ($requestData['c_password'] !== $requestData['c_confirmPassword']) {
            throw new Exception('Password not match');
        }

        // 2. Hash Password
        $hashedPassword = password_hash($requestData['c_password'], PASSWORD_DEFAULT);

        // Siapkan data untuk tabel merchant_supervisor
        $dataSupervisor = [
            'c_name'         => $requestData['c_name'],
            'c_username'     => $requestData['c_username'],
            'c_email'        => $requestData['c_email'],
            'c_password'     => $hashedPassword,
            'c_status'       => $requestData['c_status'],
            'c_created_date' => date('Y-m-d H:i:s')
        ];

        // 3. Memulai Database Transaction
        $this->CI->db->trans_start();

        // Insert ke merchant_supervisor
        $this->CI->db->insert('merchant_supervisor', $dataSupervisor);
        $supervisorId = $this->CI->db->insert_id();

        // 4. Update tabel merchant untuk meng-assign supervisor_id
        if (!empty($requestData['c_merchant_spv']) && is_array($requestData['c_merchant_spv'])) {
            $this->assignMerchantsToSupervisor($requestData['c_merchant_spv'], $supervisorId);
        }

        // Commit transaksi
        $this->CI->db->trans_complete();

        if ($this->CI->db->trans_status() === FALSE) {
            throw new Exception('Database transaction failed. Data rolled back.');
        }

        return true;
    }

    /**
     * Assign merchant ke supervisor tertentu
     */
    private function assignMerchantsToSupervisor($merchantIds, $supervisorId)
    {
        foreach ($merchantIds as $merchantId) {
            // Ambil data merchant
            $merchant = $this->CI->db->get_where('merchant', ['id' => $merchantId])->row();

            if (!$merchant) {
                throw new Exception("Merchant with ID {$merchantId} not found.");
            }

            // Validasi apakah merchant sudah punya supervisor
            if (!empty($merchant->c_refSupervisor)) {
                throw new Exception("Merchant {$merchant->c_name} already belongs to another Supervisor.");
            }

            // Update merchant dengan ID Supervisor yang baru
            $this->CI->db->where('id', $merchantId);
            $this->CI->db->update('merchant', ['c_refSupervisor' => $supervisorId]);
        }
    }

    /**
     * Mendaftarkan Merchant Baru
     * 
     * @param array $postData Data dari $_POST
     * @param array $formValidationRules Rules validasi untuk mapping data
     * @param array $optionalFields Field opsional
     * @return bool
     * @throws Exception
     */
    public function registerMerchant($postData, $formValidationRules, $optionalFields)
    {
        $data = [];
        
        // Mapping data dari POST
        foreach ($formValidationRules as $rule) {
            $field = $rule['field'];
            if (in_array($field, $optionalFields)) {
                $data[$field] = !empty($postData[$field]) ? '1' : '0';
            } else {
                $data[$field] = isset($postData[$field]) ? $postData[$field] : null;
            }
        }

        foreach ($optionalFields as $field) {
            $data[$field] = !empty($postData[$field]) ? '1' : '0';
        }

        $gvconnectBusinessId = $data['c_gvconnectBusinessId'] != NULL ? $data['c_gvconnectBusinessId'] : null;
        $gvconnectBusinessName = $data['c_gvconnectBusinessName'] != NULL ? $data['c_gvconnectBusinessName'] : null;

        unset($data['c_gvconnectBusinessId']);
        unset($data['c_gvconnectBusinessName']);

        // Hashing password
        $data['c_password'] = password_hash($data['c_password'], PASSWORD_DEFAULT);

        // Generate ID (sebaiknya diganti ke UUID atau auto-increment di masa depan)
        $merchantId = rand(1111, 4444);
        $data['id'] = $merchantId;

        // Generate Credential Key
        $this->CI->load->helper('cstring');
        $data['c_openapiCredentialKey'] = generateCredentialKey();

        // Security Type
        $securityType = 'Not Both';
        if (!empty($data['c_openapiIPAllow'])) {
            $securityType = 'Whitelist IP';
        }
        $data['c_openapiSecurityType'] = $securityType;
        $data['c_dateCreated'] = date('Y-m-d H:i:s');
        $data['c_status'] = 'Active';
        
        unset($data['c_confirmPassword']);

        // Insert menggunakan Model
        $this->CI->load->model('Merchant');
        $result = $this->CI->Merchant->create_merchant($data, $gvconnectBusinessId, $gvconnectBusinessName);

        if ($result !== true) {
            throw new Exception('Failed to insert data: ' . json_encode($result));
        }

        return true;
    }
}
