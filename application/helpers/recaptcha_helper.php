<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('verify_recaptcha')) {
    /**
     * Verifikasi respon reCAPTCHA dengan Google reCAPTCHA API.
     *
     * @param string $response Respon reCAPTCHA dari pengguna.
     * @param string $secret Kunci rahasia reCAPTCHA yang diberikan oleh Google.
     * @return array Hasil verifikasi reCAPTCHA dalam bentuk array.
     */
    function verify_recaptcha($response, $secret) {
        // Setup curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
            'secret' => $secret,
            'response' => $response
        )));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute curl
        $result = curl_exec($ch);
        curl_close($ch);

        // Decode result
        $result = json_decode($result, true);

        return $result;
    }
}
