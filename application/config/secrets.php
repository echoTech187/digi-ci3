<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Project Secrets Configuration
 * 
 * IMPORTANT: This file contains sensitive credentials.
 * It is excluded from Git to prevent exposure.
 */

/* reCAPTCHA Configuration */
$config['recaptcha_site_key']   = $_ENV['RECAPTCHA_SITE_KEY'] ?? '';
$config['recaptcha_secret_key'] = $_ENV['RECAPTCHA_SECRET_KEY'] ?? '';

/* SMTP Configuration */
$config['smtp_user'] = $_ENV['SMTP_USER'] ?? '';
$config['smtp_pass'] = $_ENV['SMTP_PASS'] ?? '';
