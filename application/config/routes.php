<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'auth';

// --- Dashboard Module Extraction Routes ---
$route['admin'] = 'admin/DashboardController/index';
$route['admin/analytics'] = 'admin/DashboardController/analytics';
$route['admin/toggleOpenApiStatus'] = 'admin/DashboardController/toggleOpenApiStatus';
$route['admin/getMaintenanceStatus'] = 'admin/DashboardController/getMaintenanceStatus';
$route['admin/globalSearch'] = 'admin/DashboardController/globalSearch';
$route['admin/syncAvailableBalanceMerchant'] = 'admin/DashboardController/syncAvailableBalanceMerchant';
$route['admin/recent_mutations_json'] = 'admin/DashboardController/recent_mutations_json';
$route['admin/ajax_today_stats_json'] = 'admin/DashboardController/ajax_today_stats_json';
$route['admin/ajax_monthly_stats_json'] = 'admin/DashboardController/ajax_monthly_stats_json';
$route['admin/ajax_dashboard_metadata_json'] = 'admin/DashboardController/ajax_dashboard_metadata_json';
$route['admin/ajax_analytics_data_json'] = 'admin/DashboardController/ajax_analytics_data_json';
$route['welcome'] = 'admin/DashboardController/welcome';

// --- Merchant Module Extraction Routes ---
$route['admin/merchant'] = 'AdminMerchant/merchant';
$route['admin/addMerchant'] = 'AdminMerchant/addMerchant';
$route['admin/merchant_spv'] = 'AdminMerchant/merchant_spv';
$route['admin/listMerchants/(:any)'] = 'AdminMerchant/listMerchants/$1';
$route['admin/merchant'] = 'admin/MerchantManagementController/merchant';
$route['admin/merchant/(:any)'] = 'admin/MerchantManagementController/merchant/$1';
$route['admin/merchant/(:any)/(:any)'] = 'admin/MerchantManagementController/merchant/$1/$2';
$route['admin/merchant_spv'] = 'admin/MerchantManagementController/merchant_spv';
$route['admin/merchant_spv/(:any)'] = 'admin/MerchantManagementController/merchant_spv/$1';
$route['admin/merchant_spv/(:any)/(:any)'] = 'admin/MerchantManagementController/merchant_spv/$1/$2';
$route['admin/listMerchants'] = 'admin/MerchantManagementController/listMerchants';
$route['admin/listMerchants/(:any)'] = 'admin/MerchantManagementController/listMerchants/$1';
$route['admin/listMerchants/(:any)/(:any)'] = 'admin/MerchantManagementController/listMerchants/$1/$2';
$route['admin/registerMerchant'] = 'admin/MerchantManagementController/registerMerchant';
$route['admin/registerMerchant/(:any)'] = 'admin/MerchantManagementController/registerMerchant/$1';
$route['admin/registerMerchant/(:any)/(:any)'] = 'admin/MerchantManagementController/registerMerchant/$1/$2';
$route['admin/addMerchant'] = 'admin/MerchantManagementController/addMerchant';
$route['admin/addMerchant/(:any)'] = 'admin/MerchantManagementController/addMerchant/$1';
$route['admin/addMerchant/(:any)/(:any)'] = 'admin/MerchantManagementController/addMerchant/$1/$2';
$route['admin/registerMerchantSpv'] = 'admin/MerchantManagementController/registerMerchantSpv';
$route['admin/registerMerchantSpv/(:any)'] = 'admin/MerchantManagementController/registerMerchantSpv/$1';
$route['admin/registerMerchantSpv/(:any)/(:any)'] = 'admin/MerchantManagementController/registerMerchantSpv/$1/$2';
$route['admin/deleteMerchantSpv'] = 'admin/MerchantManagementController/deleteMerchantSpv';
$route['admin/deleteMerchantSpv/(:any)'] = 'admin/MerchantManagementController/deleteMerchantSpv/$1';
$route['admin/deleteMerchantSpv/(:any)/(:any)'] = 'admin/MerchantManagementController/deleteMerchantSpv/$1/$2';
$route['admin/editMerchant'] = 'admin/MerchantManagementController/editMerchant';
$route['admin/editMerchant/(:any)'] = 'admin/MerchantManagementController/editMerchant/$1';
$route['admin/editMerchant/(:any)/(:any)'] = 'admin/MerchantManagementController/editMerchant/$1/$2';
$route['admin/updateMerchant'] = 'admin/MerchantManagementController/updateMerchant';
$route['admin/updateMerchant/(:any)'] = 'admin/MerchantManagementController/updateMerchant/$1';
$route['admin/updateMerchant/(:any)/(:any)'] = 'admin/MerchantManagementController/updateMerchant/$1/$2';
$route['admin/settingcashinfee'] = 'admin/MerchantManagementController/settingcashinfee';
$route['admin/settingcashinfee/(:any)'] = 'admin/MerchantManagementController/settingcashinfee/$1';
$route['admin/settingcashinfee/(:any)/(:any)'] = 'admin/MerchantManagementController/settingcashinfee/$1/$2';
$route['admin/createSettingCashinFee'] = 'admin/MerchantManagementController/createSettingCashinFee';
$route['admin/createSettingCashinFee/(:any)'] = 'admin/MerchantManagementController/createSettingCashinFee/$1';
$route['admin/createSettingCashinFee/(:any)/(:any)'] = 'admin/MerchantManagementController/createSettingCashinFee/$1/$2';
$route['admin/bulkCreateSettingCashinFee'] = 'admin/MerchantManagementController/bulkCreateSettingCashinFee';
$route['admin/bulkCreateSettingCashinFee/(:any)'] = 'admin/MerchantManagementController/bulkCreateSettingCashinFee/$1';
$route['admin/bulkCreateSettingCashinFee/(:any)/(:any)'] = 'admin/MerchantManagementController/bulkCreateSettingCashinFee/$1/$2';
$route['admin/getCashinChannelGroups'] = 'admin/MerchantManagementController/getCashinChannelGroups';
$route['admin/editSettingCashinFee'] = 'admin/MerchantManagementController/editSettingCashinFee';
$route['admin/editSettingCashinFee/(:any)'] = 'admin/MerchantManagementController/editSettingCashinFee/$1';
$route['admin/editSettingCashinFee/(:any)/(:any)'] = 'admin/MerchantManagementController/editSettingCashinFee/$1/$2';
$route['admin/deleteSettingCashinFee'] = 'admin/MerchantManagementController/deleteSettingCashinFee';
$route['admin/deleteSettingCashinFee/(:any)'] = 'admin/MerchantManagementController/deleteSettingCashinFee/$1';
$route['admin/deleteSettingCashinFee/(:any)/(:any)'] = 'admin/MerchantManagementController/deleteSettingCashinFee/$1/$2';
$route['admin/settingcashoutfee'] = 'admin/MerchantManagementController/settingcashoutfee';
$route['admin/settingcashoutfee/(:any)'] = 'admin/MerchantManagementController/settingcashoutfee/$1';
$route['admin/settingcashoutfee/(:any)/(:any)'] = 'admin/MerchantManagementController/settingcashoutfee/$1/$2';
$route['admin/createSettingCashoutFee'] = 'admin/MerchantManagementController/createSettingCashoutFee';
$route['admin/createSettingCashoutFee/(:any)'] = 'admin/MerchantManagementController/createSettingCashoutFee/$1';
$route['admin/createSettingCashoutFee/(:any)/(:any)'] = 'admin/MerchantManagementController/createSettingCashoutFee/$1/$2';
$route['admin/bulkCreateSettingCashoutFee'] = 'admin/MerchantManagementController/bulkCreateSettingCashoutFee';
$route['admin/bulkCreateSettingCashoutFee/(:any)'] = 'admin/MerchantManagementController/bulkCreateSettingCashoutFee/$1';
$route['admin/bulkCreateSettingCashoutFee/(:any)/(:any)'] = 'admin/MerchantManagementController/bulkCreateSettingCashoutFee/$1/$2';
$route['admin/getCashoutChannelGroups'] = 'admin/MerchantManagementController/getCashoutChannelGroups';
$route['admin/editSettingCashoutFee'] = 'admin/MerchantManagementController/editSettingCashoutFee';
$route['admin/editSettingCashoutFee/(:any)'] = 'admin/MerchantManagementController/editSettingCashoutFee/$1';
$route['admin/editSettingCashoutFee/(:any)/(:any)'] = 'admin/MerchantManagementController/editSettingCashoutFee/$1/$2';
$route['admin/deleteSettingCashoutFee'] = 'admin/MerchantManagementController/deleteSettingCashoutFee';
$route['admin/deleteSettingCashoutFee/(:any)'] = 'admin/MerchantManagementController/deleteSettingCashoutFee/$1';
$route['admin/deleteSettingCashoutFee/(:any)/(:any)'] = 'admin/MerchantManagementController/deleteSettingCashoutFee/$1/$2';
$route['admin/resetMerchant'] = 'admin/MerchantManagementController/resetMerchant';
$route['admin/resetMerchant/(:any)'] = 'admin/MerchantManagementController/resetMerchant/$1';
$route['admin/resetMerchant/(:any)/(:any)'] = 'admin/MerchantManagementController/resetMerchant/$1/$2';
$route['admin/searchMerchants'] = 'admin/MerchantManagementController/searchMerchants';
$route['admin/fetchMerchantPermissions'] = 'admin/MerchantManagementController/fetchMerchantPermissions';
$route['admin/fetchMerchantPermissions/(:any)'] = 'admin/MerchantManagementController/fetchMerchantPermissions/$1';
$route['admin/saveDelegation'] = 'admin/MerchantManagementController/saveDelegation';
$route['admin/saveDelegation/(:any)'] = 'admin/MerchantManagementController/saveDelegation/$1';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// --- Transaction Module Extraction Routes ---
$route['admin/submerchant'] = 'admin/MerchantSubAccountController/Submerchant';
$route['admin/submerchant/(:any)'] = 'admin/MerchantSubAccountController/Submerchant/$1';
$route['admin/submerchant/(:any)/(:any)'] = 'admin/MerchantSubAccountController/Submerchant/$1/$2';
$route['admin/resetsubmerchant'] = 'admin/MerchantSubAccountController/resetsubmerchant';
$route['admin/resetsubmerchant/(:any)'] = 'admin/MerchantSubAccountController/resetsubmerchant/$1';
$route['admin/resetsubmerchant/(:any)/(:any)'] = 'admin/MerchantSubAccountController/resetsubmerchant/$1/$2';
$route['admin/registersubMerchant'] = 'admin/MerchantSubAccountController/registersubMerchant';
$route['admin/registersubMerchant/(:any)'] = 'admin/MerchantSubAccountController/registersubMerchant/$1';
$route['admin/registersubMerchant/(:any)/(:any)'] = 'admin/MerchantSubAccountController/registersubMerchant/$1/$2';
$route['admin/edit_submerchant'] = 'admin/MerchantSubAccountController/edit_submerchant';
$route['admin/edit_submerchant/(:any)'] = 'admin/MerchantSubAccountController/edit_submerchant/$1';
$route['admin/edit_submerchant/(:any)/(:any)'] = 'admin/MerchantSubAccountController/edit_submerchant/$1/$2';
$route['admin/mutation'] = 'admin/TransactionMutationController/mutation';
$route['admin/mutation/(:any)'] = 'admin/TransactionMutationController/mutation/$1';
$route['admin/mutation/(:any)/(:any)'] = 'admin/TransactionMutationController/mutation/$1/$2';
$route['admin/resetMutation'] = 'admin/TransactionMutationController/resetMutation';
$route['admin/resetMutation/(:any)'] = 'admin/TransactionMutationController/resetMutation/$1';
$route['admin/resetMutation/(:any)/(:any)'] = 'admin/TransactionMutationController/resetMutation/$1/$2';
$route['admin/download_mutation'] = 'admin/TransactionMutationController/download_mutation';
$route['admin/download_mutation/(:any)'] = 'admin/TransactionMutationController/download_mutation/$1';
$route['admin/download_mutation/(:any)/(:any)'] = 'admin/TransactionMutationController/download_mutation/$1/$2';
$route['admin/history'] = 'admin/HistoryController/index';
$route['admin/history/(:any)'] = 'admin/HistoryController/index/$1';
$route['admin/history/(:any)/(:any)'] = 'admin/HistoryController/index/$1/$2';
$route['admin/resetHistory'] = 'admin/HistoryController/resetHistory';
$route['admin/resetHistory/(:any)'] = 'admin/HistoryController/resetHistory/$1';
$route['admin/resetHistory/(:any)/(:any)'] = 'admin/HistoryController/resetHistory/$1/$2';
$route['admin/download_history'] = 'admin/HistoryController/download_history';
$route['admin/download_history/(:any)'] = 'admin/HistoryController/download_history/$1';
$route['admin/download_history/(:any)/(:any)'] = 'admin/HistoryController/download_history/$1/$2';
$route['admin/virtual_account'] = 'admin/VirtualAccountTransactionController/virtual_account';
$route['admin/virtual_account/(:any)'] = 'admin/VirtualAccountTransactionController/virtual_account/$1';
$route['admin/virtual_account/(:any)/(:any)'] = 'admin/VirtualAccountTransactionController/virtual_account/$1/$2';
$route['admin/resetVA'] = 'admin/VirtualAccountTransactionController/resetVA';
$route['admin/resetVA/(:any)'] = 'admin/VirtualAccountTransactionController/resetVA/$1';
$route['admin/resetVA/(:any)/(:any)'] = 'admin/VirtualAccountTransactionController/resetVA/$1/$2';
$route['admin/VA_detail'] = 'admin/VirtualAccountTransactionController/VA_detail';
$route['admin/VA_detail/(:any)'] = 'admin/VirtualAccountTransactionController/VA_detail/$1';
$route['admin/VA_detail/(:any)/(:any)'] = 'admin/VirtualAccountTransactionController/VA_detail/$1/$2';
$route['admin/download_VA'] = 'admin/VirtualAccountTransactionController/download_VA';
$route['admin/download_VA/(:any)'] = 'admin/VirtualAccountTransactionController/download_VA/$1';
$route['admin/download_VA/(:any)/(:any)'] = 'admin/VirtualAccountTransactionController/download_VA/$1/$2';
$route['admin/qris'] = 'admin/QrisTransactionController/qris';
$route['admin/qris/(:any)'] = 'admin/QrisTransactionController/qris/$1';
$route['admin/qris/(:any)/(:any)'] = 'admin/QrisTransactionController/qris/$1/$2';
$route['admin/resetqris'] = 'admin/QrisTransactionController/resetqris';
$route['admin/resetqris/(:any)'] = 'admin/QrisTransactionController/resetqris/$1';
$route['admin/resetqris/(:any)/(:any)'] = 'admin/QrisTransactionController/resetqris/$1/$2';
$route['admin/qris_detail'] = 'admin/QrisTransactionController/qris_detail';
$route['admin/qris_detail/(:any)'] = 'admin/QrisTransactionController/qris_detail/$1';
$route['admin/qris_detail/(:any)/(:any)'] = 'admin/QrisTransactionController/qris_detail/$1/$2';
$route['admin/download_qris'] = 'admin/QrisTransactionController/download_qris';
$route['admin/download_qris/(:any)'] = 'admin/QrisTransactionController/download_qris/$1';
$route['admin/download_qris/(:any)/(:any)'] = 'admin/QrisTransactionController/download_qris/$1/$2';
$route['admin/ewallet'] = 'admin/EwalletTransactionController/ewallet';
$route['admin/ewallet/(:any)'] = 'admin/EwalletTransactionController/ewallet/$1';
$route['admin/ewallet/(:any)/(:any)'] = 'admin/EwalletTransactionController/ewallet/$1/$2';
$route['admin/resetewallet'] = 'admin/EwalletTransactionController/resetewallet';
$route['admin/resetewallet/(:any)'] = 'admin/EwalletTransactionController/resetewallet/$1';
$route['admin/resetewallet/(:any)/(:any)'] = 'admin/EwalletTransactionController/resetewallet/$1/$2';
$route['admin/download_ewallet'] = 'admin/EwalletTransactionController/download_ewallet';
$route['admin/download_ewallet/(:any)'] = 'admin/EwalletTransactionController/download_ewallet/$1';
$route['admin/download_ewallet/(:any)/(:any)'] = 'admin/EwalletTransactionController/download_ewallet/$1/$2';
$route['admin/bi_fast'] = 'admin/BiFastTransactionController/bi_fast';
$route['admin/bi_fast/(:any)'] = 'admin/BiFastTransactionController/bi_fast/$1';
$route['admin/bi_fast/(:any)/(:any)'] = 'admin/BiFastTransactionController/bi_fast/$1/$2';
$route['admin/resetbi_fast'] = 'admin/BiFastTransactionController/resetbi_fast';
$route['admin/resetbi_fast/(:any)'] = 'admin/BiFastTransactionController/resetbi_fast/$1';
$route['admin/resetbi_fast/(:any)/(:any)'] = 'admin/BiFastTransactionController/resetbi_fast/$1/$2';
$route['admin/bi_fast_detail'] = 'admin/BiFastTransactionController/bi_fast_detail';
$route['admin/bi_fast_detail/(:any)'] = 'admin/BiFastTransactionController/bi_fast_detail/$1';
$route['admin/bi_fast_detail/(:any)/(:any)'] = 'admin/BiFastTransactionController/bi_fast_detail/$1/$2';
$route['admin/download_bi_fast'] = 'admin/BiFastTransactionController/download_bi_fast';
$route['admin/download_bi_fast/(:any)'] = 'admin/BiFastTransactionController/download_bi_fast/$1';
$route['admin/download_bi_fast/(:any)/(:any)'] = 'admin/BiFastTransactionController/download_bi_fast/$1/$2';
$route['admin/Va_dynamic'] = 'admin/VirtualAccountTransactionController/Va_dynamic';
$route['admin/Va_dynamic/(:any)'] = 'admin/VirtualAccountTransactionController/Va_dynamic/$1';
$route['admin/Va_dynamic/(:any)/(:any)'] = 'admin/VirtualAccountTransactionController/Va_dynamic/$1/$2';
$route['admin/resetVa_dynamic'] = 'admin/VirtualAccountTransactionController/resetVa_dynamic';
$route['admin/resetVa_dynamic/(:any)'] = 'admin/VirtualAccountTransactionController/resetVa_dynamic/$1';
$route['admin/resetVa_dynamic/(:any)/(:any)'] = 'admin/VirtualAccountTransactionController/resetVa_dynamic/$1/$2';
$route['admin/VA_recurring'] = 'admin/VirtualAccountTransactionController/VA_recurring';
$route['admin/VA_recurring/(:any)'] = 'admin/VirtualAccountTransactionController/VA_recurring/$1';
$route['admin/VA_recurring/(:any)/(:any)'] = 'admin/VirtualAccountTransactionController/VA_recurring/$1/$2';
$route['admin/resetVa_recurring'] = 'admin/VirtualAccountTransactionController/resetVa_recurring';
$route['admin/resetVa_recurring/(:any)'] = 'admin/VirtualAccountTransactionController/resetVa_recurring/$1';
$route['admin/resetVa_recurring/(:any)/(:any)'] = 'admin/VirtualAccountTransactionController/resetVa_recurring/$1/$2';
$route['admin/qris_dynamic_list'] = 'admin/QrisTransactionController/qris_dynamic_list';
$route['admin/qris_dynamic_list/(:any)'] = 'admin/QrisTransactionController/qris_dynamic_list/$1';
$route['admin/qris_dynamic_list/(:any)/(:any)'] = 'admin/QrisTransactionController/qris_dynamic_list/$1/$2';
$route['admin/qris_dynamic'] = 'admin/QrisTransactionController/qris_dynamic';
$route['admin/qris_dynamic/(:any)'] = 'admin/QrisTransactionController/qris_dynamic/$1';
$route['admin/qris_dynamic/(:any)/(:any)'] = 'admin/QrisTransactionController/qris_dynamic/$1/$2';
$route['admin/getDetailQrisDynamicChannelExternal'] = 'admin/QrisTransactionController/getDetailQrisDynamicChannelExternal';
$route['admin/getDetailEwalletDynamicChannelExternal'] = 'admin/EwalletTransactionController/getDetailEwalletDynamicChannelExternal';
$route['admin/getDetailEwalletChannelExternal'] = 'admin/EwalletTransactionController/getDetailEwalletChannelExternal';
$route['admin/getDetailBiFastChannelExternal'] = 'admin/BiFastTransactionController/getDetailBiFastChannelExternal';

$route['admin/getDetailVaDynamicChannelExternal'] = 'admin/VirtualAccountTransactionController/getDetailVaDynamicChannelExternal';
$route['admin/getDetailVaRecurringChannelExternal'] = 'admin/VirtualAccountTransactionController/getDetailVaRecurringChannelExternal';

$route['admin/resetqris_dynamic'] = 'admin/QrisTransactionController/resetqris_dynamic';
$route['admin/resetqris_dynamic/(:any)'] = 'admin/QrisTransactionController/resetqris_dynamic/$1';
$route['admin/resetqris_dynamic/(:any)/(:any)'] = 'admin/QrisTransactionController/resetqris_dynamic/$1/$2';
$route['admin/ewallet_dynamic'] = 'admin/EwalletTransactionController/ewallet_dynamic';
$route['admin/ewallet_dynamic/(:any)'] = 'admin/EwalletTransactionController/ewallet_dynamic/$1';
$route['admin/ewallet_dynamic/(:any)/(:any)'] = 'admin/EwalletTransactionController/ewallet_dynamic/$1/$2';
$route['admin/ewallet_detail'] = 'admin/EwalletTransactionController/ewallet_detail';
$route['admin/ewallet_detail/(:any)'] = 'admin/EwalletTransactionController/ewallet_detail/$1';
$route['admin/ewallet_detail/(:any)/(:any)'] = 'admin/EwalletTransactionController/ewallet_detail/$1/$2';
$route['admin/resetewallet_dynamic'] = 'admin/EwalletTransactionController/resetewallet_dynamic';
$route['admin/resetewallet_dynamic/(:any)'] = 'admin/EwalletTransactionController/resetewallet_dynamic/$1';
$route['admin/resetewallet_dynamic/(:any)/(:any)'] = 'admin/EwalletTransactionController/resetewallet_dynamic/$1/$2';
$route['admin/qris_recurring'] = 'admin/QrisTransactionController/qris_recurring';
$route['admin/qris_recurring/(:any)'] = 'admin/QrisTransactionController/qris_recurring/$1';
$route['admin/qris_recurring/(:any)/(:any)'] = 'admin/QrisTransactionController/qris_recurring/$1/$2';
$route['admin/getDetailQrisRecurringChannelExternal'] = 'admin/QrisTransactionController/getDetailQrisRecurringChannelExternal';
$route['admin/resetqris_recurring'] = 'admin/QrisTransactionController/resetqris_recurring';
$route['admin/resetqris_recurring/(:any)'] = 'admin/QrisTransactionController/resetqris_recurring/$1';
$route['admin/resetqris_recurring/(:any)/(:any)'] = 'admin/QrisTransactionController/resetqris_recurring/$1/$2';
$route['admin/report'] = 'admin/ReportController/report';
$route['admin/report/(:any)'] = 'admin/ReportController/report/$1';
$route['admin/report/(:any)/(:any)'] = 'admin/ReportController/report/$1/$2';
$route['admin/resetdownload'] = 'admin/ReportController/resetdownload';
$route['admin/resetdownload/(:any)'] = 'admin/ReportController/resetdownload/$1';
$route['admin/resetdownload/(:any)/(:any)'] = 'admin/ReportController/resetdownload/$1/$2';
$route['admin/get_submerchants'] = 'admin/MerchantSubAccountController/get_submerchants';
$route['admin/get_submerchants/(:any)'] = 'admin/MerchantSubAccountController/get_submerchants/$1';
$route['admin/get_submerchants/(:any)/(:any)'] = 'admin/MerchantSubAccountController/get_submerchants/$1/$2';
$route['admin/getChannelsByPosition'] = 'admin/TransactionMutationController/getChannelsByPosition';
$route['admin/createCreditBalance'] = 'admin/MerchantManagementController/createCreditBalance';
$route['admin/createCreditBalance/(:any)'] = 'admin/MerchantManagementController/createCreditBalance/$1';
$route['admin/createCreditBalance/(:any)/(:any)'] = 'admin/MerchantManagementController/createCreditBalance/$1/$2';
$route['admin/createDebitBalance'] = 'admin/MerchantManagementController/createDebitBalance';
$route['admin/createDebitBalance/(:any)'] = 'admin/MerchantManagementController/createDebitBalance/$1';
$route['admin/createDebitBalance/(:any)/(:any)'] = 'admin/MerchantManagementController/createDebitBalance/$1/$2';
$route['admin/download'] = 'admin/ReportController/download';
$route['admin/download/(:any)'] = 'admin/ReportController/download/$1';
$route['admin/download/(:any)/(:any)'] = 'admin/ReportController/download/$1/$2';
$route['admin/SendnotifikasiVA'] = 'admin/VirtualAccountTransactionController/SendnotifikasiVA';
$route['admin/SendnotifikasiVA/(:any)'] = 'admin/VirtualAccountTransactionController/SendnotifikasiVA/$1';
$route['admin/SendnotifikasiVA/(:any)/(:any)'] = 'admin/VirtualAccountTransactionController/SendnotifikasiVA/$1/$2';
$route['admin/SendnotifikasiQRIS'] = 'admin/QrisTransactionController/SendnotifikasiQRIS';
$route['admin/SendnotifikasiQRIS/(:any)'] = 'admin/QrisTransactionController/SendnotifikasiQRIS/$1';
$route['admin/SendnotifikasiQRIS/(:any)/(:any)'] = 'admin/QrisTransactionController/SendnotifikasiQRIS/$1/$2';
$route['admin/Sendnotifikasiewallet'] = 'admin/EwalletTransactionController/Sendnotifikasiewallet';
$route['admin/Sendnotifikasiewallet/(:any)'] = 'admin/EwalletTransactionController/Sendnotifikasiewallet/$1';
$route['admin/Sendnotifikasiewallet/(:any)/(:any)'] = 'admin/EwalletTransactionController/Sendnotifikasiewallet/$1/$2';

// --- Service/PPOB Module Extraction Routes ---
$route['admin/pulsa_reguler'] = 'admin/ServiceController/pulsa_reguler';
$route['admin/pulsa_reguler/(:any)'] = 'admin/ServiceController/pulsa_reguler/$1';
$route['admin/pulsa_reguler/(:any)/(:any)'] = 'admin/ServiceController/pulsa_reguler/$1/$2';
$route['admin/paket_data'] = 'admin/ServiceController/paket_data';
$route['admin/paket_data/(:any)'] = 'admin/ServiceController/paket_data/$1';
$route['admin/paket_data/(:any)/(:any)'] = 'admin/ServiceController/paket_data/$1/$2';
$route['admin/token_listrik'] = 'admin/ServiceController/token_listrik';
$route['admin/token_listrik/(:any)'] = 'admin/ServiceController/token_listrik/$1';
$route['admin/token_listrik/(:any)/(:any)'] = 'admin/ServiceController/token_listrik/$1/$2';
$route['admin/topupgopay'] = 'admin/ServiceController/topupgopay';
$route['admin/topupgopay/(:any)'] = 'admin/ServiceController/topupgopay/$1';
$route['admin/topupgopay/(:any)/(:any)'] = 'admin/ServiceController/topupgopay/$1/$2';
$route['admin/topupdana'] = 'admin/ServiceController/topupdana';
$route['admin/topupdana/(:any)'] = 'admin/ServiceController/topupdana/$1';
$route['admin/topupdana/(:any)/(:any)'] = 'admin/ServiceController/topupdana/$1/$2';
$route['admin/topupovo'] = 'admin/ServiceController/topupovo';
$route['admin/topupovo/(:any)'] = 'admin/ServiceController/topupovo/$1';
$route['admin/topupovo/(:any)/(:any)'] = 'admin/ServiceController/topupovo/$1/$2';
$route['admin/googleplay'] = 'admin/ServiceController/googleplay';
$route['admin/googleplay/(:any)'] = 'admin/ServiceController/googleplay/$1';
$route['admin/googleplay/(:any)/(:any)'] = 'admin/ServiceController/googleplay/$1/$2';
$route['admin/freefire'] = 'admin/ServiceController/freefire';
$route['admin/freefire/(:any)'] = 'admin/ServiceController/freefire/$1';
$route['admin/freefire/(:any)/(:any)'] = 'admin/ServiceController/freefire/$1/$2';
$route['admin/hago'] = 'admin/ServiceController/hago';
$route['admin/hago/(:any)'] = 'admin/ServiceController/hago/$1';
$route['admin/hago/(:any)/(:any)'] = 'admin/ServiceController/hago/$1/$2';
$route['admin/mobilelegend'] = 'admin/ServiceController/mobilelegend';
$route['admin/mobilelegend/(:any)'] = 'admin/ServiceController/mobilelegend/$1';
$route['admin/mobilelegend/(:any)/(:any)'] = 'admin/ServiceController/mobilelegend/$1/$2';
$route['admin/pubgmobile'] = 'admin/ServiceController/pubgmobile';
$route['admin/pubgmobile/(:any)'] = 'admin/ServiceController/pubgmobile/$1';
$route['admin/pubgmobile/(:any)/(:any)'] = 'admin/ServiceController/pubgmobile/$1/$2';
$route['admin/createProduk'] = 'admin/ServiceController/createProduk';
$route['admin/createProduk/(:any)'] = 'admin/ServiceController/createProduk/$1';
$route['admin/createProduk/(:any)/(:any)'] = 'admin/ServiceController/createProduk/$1/$2';

// --- Cashin External Module Extraction Routes ---
$route['admin/cashin/external'] = 'admin/CashinExternalController/index';
$route['admin/cashin/external/ajax_list'] = 'admin/CashinExternalController/ajax_list';
$route['admin/cashin/external/add_view'] = 'admin/CashinExternalController/add_view';
$route['admin/cashin/external/edit_view/(:any)'] = 'admin/CashinExternalController/edit_view/$1';
$route['admin/cashin/external/add'] = 'admin/CashinExternalController/add';
$route['admin/cashin/external/update'] = 'admin/CashinExternalController/update';
$route['admin/cashin/external/delete/(:any)'] = 'admin/CashinExternalController/delete/$1';
$route['admin/cashin/external/bulk_update'] = 'admin/CashinExternalController/bulk_update';

// --- Cashout External Module Extraction Routes ---
$route['admin/cashout/external'] = 'admin/CashoutExternalController/index';
$route['admin/cashout/external/ajax_list'] = 'admin/CashoutExternalController/ajax_list';
$route['admin/cashout/external/add_view'] = 'admin/CashoutExternalController/add_view';
$route['admin/cashout/external/edit_view/(:any)'] = 'admin/CashoutExternalController/edit_view/$1';
$route['admin/cashout/external/add'] = 'admin/CashoutExternalController/add';
$route['admin/cashout/external/update'] = 'admin/CashoutExternalController/update';
$route['admin/cashout/external/delete/(:any)'] = 'admin/CashoutExternalController/delete/$1';
$route['admin/cashout/external/bulk_update'] = 'admin/CashoutExternalController/bulk_update';

// --- Channel Module Extraction Routes ---
$route['admin/cashin'] = 'admin/ChannelController/cashin';
$route['admin/cashin/(:any)'] = 'admin/ChannelController/cashin/$1';
$route['admin/cashout'] = 'admin/ChannelController/cashout';
$route['admin/cashout/(:any)'] = 'admin/ChannelController/cashout/$1';
$route['admin/createCashinChanel'] = 'admin/ChannelController/createCashinChanel';
$route['admin/createCashinChanel/(:any)'] = 'admin/ChannelController/createCashinChanel/$1';
$route['admin/createCashOutChanel'] = 'admin/ChannelController/createCashOutChanel';
$route['admin/createCashOutChanel/(:any)'] = 'admin/ChannelController/createCashOutChanel/$1';

// --- User Access Module Extraction Routes ---
$route['admin/holiday'] = 'admin/UserAccessController/holiday';
$route['admin/holiday/(:any)'] = 'admin/UserAccessController/holiday/$1';
$route['admin/listadmin'] = 'admin/UserAccessController/listAdmin';
$route['admin/listadmin/(:any)'] = 'admin/UserAccessController/listAdmin/$1';
$route['admin/listAdmin'] = 'admin/UserAccessController/listAdmin';
$route['admin/listAdmin/(:any)'] = 'admin/UserAccessController/listAdmin/$1';
// $route['admin/listadmin'] = 'admin/UserAccessController/listAdmin';
// $route['admin/listadmin/(:any)'] = 'admin/UserAccessController/listAdmin/$1';

$route['admin/manageUsers'] = 'admin/UserAccessController/manageUsers';
$route['admin/manageUsers/(:any)'] = 'admin/UserAccessController/manageUsers/$1';
$route['admin/manageusers'] = 'admin/UserAccessController/manageUsers';
$route['admin/manageusers/(:any)'] = 'admin/UserAccessController/manageUsers/$1';

$route['admin/manageHoliday'] = 'admin/UserAccessController/manageHoliday';
$route['admin/manageHoliday/(:any)'] = 'admin/UserAccessController/manageHoliday/$1';
$route['admin/manageholiday'] = 'admin/UserAccessController/manageHoliday';
$route['admin/manageholiday/(:any)'] = 'admin/UserAccessController/manageHoliday/$1';

// --- Report Module Extraction Routes ---
$route['admin/balance_log'] = 'admin/ReportController/balance_log';
