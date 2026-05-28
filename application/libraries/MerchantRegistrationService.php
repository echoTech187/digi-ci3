<?php defined('BASEPATH') or exit('No direct script access allowed');

class MerchantRegistrationService
{
    protected $CI;

    public function __construct()
    {
        // Get the main CodeIgniter instance
        $this->CI =& get_instance();
        // Load database library if not already loaded
        $this->CI->load->database();
    }

    /**
     * Register a new Merchant Supervisor
     * 
     * @param array $requestData $_POST data from form
     * @return bool|array
     * @throws Exception If validation or query fails
     */
    public function registerSupervisor($requestData)
    {
        // 1. Validate Required Fields Presence
        $requiredFields = ['c_name', 'c_username', 'c_email', 'c_status', 'c_password', 'c_confirmPassword'];
        foreach ($requiredFields as $field) {
            if (!isset($requestData[$field]) || trim($requestData[$field]) === '') {
                throw new Exception('All fields marked with * are required.');
            }
        }
        // 2. Validate Password Matching
        if ($requestData['c_password'] !== $requestData['c_confirmPassword']) {
            throw new Exception('Password not match');
        }

        // 2. Hash Password
        $hashedPassword = password_hash($requestData['c_password'], PASSWORD_DEFAULT);

        // Prepare data for merchant_supervisor table
        $dataSupervisor = [
            'c_name'         => $requestData['c_name'],
            'c_username'     => $requestData['c_username'],
            'c_email'        => $requestData['c_email'],
            'c_password'     => $hashedPassword,
            'c_status'       => $requestData['c_status'],
            'c_created_date' => date('Y-m-d H:i:s')
        ];


        // 3. Begin Database Transaction
        $this->CI->db->trans_start();

        // Insert into merchant_supervisor
        $success = $this->CI->db->insert('merchant_supervisor', $dataSupervisor);
        if (!$success) {
            $err = $this->CI->db->error();
            $this->CI->db->trans_rollback();
            return $err;
        }
        $supervisorId = $this->CI->db->insert_id();

        // 4. Update merchant table to assign supervisor_id
        if (!empty($requestData['c_merchant_spv']) && is_array($requestData['c_merchant_spv'])) {
            $errAssign = $this->assignMerchantsToSupervisor($requestData['c_merchant_spv'], $supervisorId);
            if ($errAssign !== true) {
                $this->CI->db->trans_rollback();
                return $errAssign;
            }
        }

        // Commit transaction
        $this->CI->db->trans_complete();

        if ($this->CI->db->trans_status() === FALSE) {
            throw new Exception('Database transaction failed. Data rolled back.');
        }

        return true;
    }

    /**
     * Update Merchant Supervisor
     * 
     * @param int $id Supervisor ID
     * @param array $requestData $_POST data from form
     * @return bool|array
     * @throws Exception If validation or transaction fails
     */
    public function updateSupervisor($id, $requestData)
    {
        // 1. Validate Required Fields Presence
        $requiredFields = ['c_name', 'c_username', 'c_email', 'c_status'];
        foreach ($requiredFields as $field) {
            if (!isset($requestData[$field]) || trim($requestData[$field]) === '') {
                throw new Exception('All fields marked with * are required.');
            }
        }
        // 2. Prepare update data for merchant_supervisor table
        $dataSupervisor = [
            'c_name'     => $requestData['c_name'],
            'c_username' => $requestData['c_username'],
            'c_email'    => $requestData['c_email'],
            'c_status'   => $requestData['c_status'],
        ];

        // 2. Hash Password if provided
        if (!empty($requestData['c_password'])) {
            if ($requestData['c_password'] !== $requestData['c_confirmPassword']) {
                throw new Exception('Password not match');
            }
            $dataSupervisor['c_password'] = password_hash($requestData['c_password'], PASSWORD_DEFAULT);
        }


        // 3. Begin Database Transaction
        $this->CI->db->trans_start();

        // Update merchant_supervisor
        $this->CI->db->where('id', $id);
        $success = $this->CI->db->update('merchant_supervisor', $dataSupervisor);
        if (!$success) {
            $err = $this->CI->db->error();
            $this->CI->db->trans_rollback();
            return $err;
        }

        // 4. Reset previous merchant assignments for this supervisor
        $this->CI->db->where('c_refSupervisor', $id);
        $successReset = $this->CI->db->update('merchant', ['c_refSupervisor' => NULL]);
        if (!$successReset) {
            $err = $this->CI->db->error();
            $this->CI->db->trans_rollback();
            return $err;
        }

        // 5. Update merchant table to assign new supervisor_id
        if (!empty($requestData['c_merchant_spv']) && is_array($requestData['c_merchant_spv'])) {
            $errAssign = $this->assignMerchantsToSupervisor($requestData['c_merchant_spv'], $id);
            if ($errAssign !== true) {
                $this->CI->db->trans_rollback();
                return $errAssign;
            }
        }

        // Commit transaction
        $this->CI->db->trans_complete();

        if ($this->CI->db->trans_status() === FALSE) {
            throw new Exception('Database transaction failed. Data rolled back.');
        }

        return true;
    }

    /**
     * Assign merchants to a specific supervisor
     */
    private function assignMerchantsToSupervisor($merchantIds, $supervisorId)
    {
        foreach ($merchantIds as $merchantId) {
            // Fetch merchant data
            $merchant = $this->CI->db->get_where('merchant', ['id' => $merchantId])->row();

            if (!$merchant) {
                throw new Exception("Merchant with ID {$merchantId} not found.");
            }

            // Update merchant with new Supervisor ID
            $this->CI->db->where('id', $merchantId);
            $success = $this->CI->db->update('merchant', ['c_refSupervisor' => $supervisorId]);
            if (!$success) {
                return $this->CI->db->error();
            }
        }
        return true;
    }

    /**
     * Register a new Merchant
     * 
     * @param array $postData Data from $_POST
     * @param array $formValidationRules Validation rules for data mapping
     * @param array $optionalFields Optional fields
     * @return bool
     * @throws Exception
     */
    public function registerMerchant($postData, $formValidationRules, $optionalFields)
    {
        $data = [];
        
        // Map data from $_POST
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

        // Hash password
        $data['c_password'] = password_hash($data['c_password'], PASSWORD_DEFAULT);

        // Generate ID (should be replaced with UUID or auto-increment in the future)
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

        // Insert using Model
        $this->CI->load->model('Merchant');
        return $this->CI->Merchant->create_merchant($data, $gvconnectBusinessId, $gvconnectBusinessName);
    }
}
