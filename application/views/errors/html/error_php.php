<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// Secara statis menyembunyikan semua output error PHP ke layar agar tidak merusak layout.
// Error tetap dicatat (log) agar developer bisa melihatnya di file log (application/logs/).
log_message('error', "PHP Error [$severity]: $message in $filepath on line $line");
?>
