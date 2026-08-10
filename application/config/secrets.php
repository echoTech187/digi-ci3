<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Project Secrets Configuration
 * 
 * IMPORTANT: This file contains sensitive credentials.
 * It is excluded from Git to prevent exposure.
 */

/* reCAPTCHA Configuration */
$config['recaptcha_site_key']   = '6LcohZQsAAAAABZs36_69j5-9aKaLdewFK05foHx';
$config['recaptcha_secret_key'] = '6LcohZQsAAAAABuu38QT8AMjD_s9vgLv9fu-rdj8';

/* SMTP Configuration */
$config['smtp_user'] = $_ENV['SMTP_USER'] ?? '';
$config['smtp_pass'] = $_ENV['SMTP_PASS'] ?? '';
