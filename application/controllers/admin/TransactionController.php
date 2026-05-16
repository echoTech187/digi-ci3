<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * TransactionController (Legacy)
 * 
 * @deprecated This controller has been modularized into domain-specific controllers:
 * - MerchantSubAccountController (Sub-Merchant Management)
 * - MerchantManagementController (Merchant & Balance Management)
 * - TransactionMutationController (Mutation)
 * - VirtualAccountTransactionController (VA, VA Dynamic, VA Recurring)
 * - QrisTransactionController (QRIS, QRIS Dynamic, QRIS Recurring)
 * - EwalletTransactionController (E-Wallet, E-Wallet Dynamic)
 * - BiFastTransactionController (BI-FAST / Disbursement)
 * - HistoryController (PPOB History)
 * - ReportController (Download Reports & Balance Logs)
 * - ServiceController (PPOB Products)
 * 
 * All routes have been redirected to the new controllers in application/config/routes.php.
 */
class TransactionController extends CI_Controller
{
   public function __construct()
   {
      parent::__construct();
      // Logic has been moved to specialized controllers.
      // This class is kept for backward compatibility during transition.
      is_logged_in();
   }

   public function index()
   {
      redirect('admin');
   }
}
