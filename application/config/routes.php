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
$route['admin/syncAvailableBalanceMerchant'] = 'admin/DashboardController/syncAvailableBalanceMerchant';
$route['admin/recent_mutations_json'] = 'admin/DashboardController/recent_mutations_json';
$route['welcome'] = 'admin/DashboardController/welcome';

// --- Merchant Module Extraction Routes ---
$route['admin/merchant'] = 'AdminMerchant/merchant';
$route['admin/merchant_spv'] = 'AdminMerchant/merchant_spv';
$route['admin/listMerchants/(:any)'] = 'AdminMerchant/listMerchants/$1';
$route['admin/registerMerchant'] = 'AdminMerchant/registerMerchant';
$route['admin/registerMerchantSpv'] = 'AdminMerchant/registerMerchantSpv';
$route['admin/editMerchant/(:any)'] = 'AdminMerchant/editMerchant/$1';
$route['admin/updateMerchant/(:any)'] = 'AdminMerchant/updateMerchant/$1';
$route['admin/settingcashinfee/(:any)'] = 'AdminMerchant/settingcashinfee/$1';
$route['admin/createSettingCashinFee/(:any)'] = 'AdminMerchant/createSettingCashinFee/$1';
$route['admin/bulkCreateSettingCashinFee/(:any)'] = 'AdminMerchant/bulkCreateSettingCashinFee/$1';
$route['admin/getCashinChannelGroups'] = 'AdminMerchant/getCashinChannelGroups';
$route['admin/getCashinChannelGroups/(:any)'] = 'AdminMerchant/getCashinChannelGroups/$1';
$route['admin/editSettingCashinFee/(:any)/(:any)'] = 'AdminMerchant/editSettingCashinFee/$1/$2';
$route['admin/deleteSettingCashinFee/(:any)/(:any)'] = 'AdminMerchant/deleteSettingCashinFee/$1/$2';
$route['admin/settingcashoutfee/(:any)'] = 'AdminMerchant/settingcashoutfee/$1';
$route['admin/createSettingCashoutFee/(:any)'] = 'AdminMerchant/createSettingCashoutFee/$1';
$route['admin/bulkCreateSettingCashoutFee/(:any)'] = 'AdminMerchant/bulkCreateSettingCashoutFee/$1';
$route['admin/getCashoutChannelGroups'] = 'AdminMerchant/getCashoutChannelGroups';
$route['admin/editSettingCashoutFee/(:any)/(:any)'] = 'AdminMerchant/editSettingCashoutFee/$1/$2';
$route['admin/deleteSettingCashoutFee/(:any)/(:any)'] = 'AdminMerchant/deleteSettingCashoutFee/$1/$2';
$route['admin/resetMerchant'] = 'AdminMerchant/resetMerchant';
$route['admin/merchant_spv/search'] = 'AdminMerchant/searchMerchants';
$route['admin/fetchMerchantPermissions/(:any)'] = 'AdminMerchant/fetchMerchantPermissions/$1';
$route['admin/saveDelegation/(:any)'] = 'AdminMerchant/saveDelegation/$1';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// --- Transaction Module Extraction Routes ---
$route['admin/Submerchant'] = 'admin/TransactionController/Submerchant';
$route['admin/Submerchant/(:any)'] = 'admin/TransactionController/Submerchant/$1';
$route['admin/Submerchant/(:any)/(:any)'] = 'admin/TransactionController/Submerchant/$1/$2';
$route['admin/submerchant'] = 'admin/TransactionController/submerchant';
$route['admin/submerchant/(:any)'] = 'admin/TransactionController/submerchant/$1';
$route['admin/submerchant/(:any)/(:any)'] = 'admin/TransactionController/submerchant/$1/$2';
$route['admin/resetsubmerchant'] = 'admin/TransactionController/resetsubmerchant';
$route['admin/resetsubmerchant/(:any)'] = 'admin/TransactionController/resetsubmerchant/$1';
$route['admin/resetsubmerchant/(:any)/(:any)'] = 'admin/TransactionController/resetsubmerchant/$1/$2';
$route['admin/registersubMerchant'] = 'admin/TransactionController/registersubMerchant';
$route['admin/registersubMerchant/(:any)'] = 'admin/TransactionController/registersubMerchant/$1';
$route['admin/registersubMerchant/(:any)/(:any)'] = 'admin/TransactionController/registersubMerchant/$1/$2';
$route['admin/edit_submerchant'] = 'admin/TransactionController/edit_submerchant';
$route['admin/edit_submerchant/(:any)'] = 'admin/TransactionController/edit_submerchant/$1';
$route['admin/edit_submerchant/(:any)/(:any)'] = 'admin/TransactionController/edit_submerchant/$1/$2';
$route['admin/mutation'] = 'admin/TransactionController/mutation';
$route['admin/mutation/(:any)'] = 'admin/TransactionController/mutation/$1';
$route['admin/mutation/(:any)/(:any)'] = 'admin/TransactionController/mutation/$1/$2';
$route['admin/resetMutation'] = 'admin/TransactionController/resetMutation';
$route['admin/resetMutation/(:any)'] = 'admin/TransactionController/resetMutation/$1';
$route['admin/resetMutation/(:any)/(:any)'] = 'admin/TransactionController/resetMutation/$1/$2';
$route['admin/download_mutation'] = 'admin/TransactionController/download_mutation';
$route['admin/download_mutation/(:any)'] = 'admin/TransactionController/download_mutation/$1';
$route['admin/download_mutation/(:any)/(:any)'] = 'admin/TransactionController/download_mutation/$1/$2';
$route['admin/history'] = 'admin/TransactionController/history';
$route['admin/history/(:any)'] = 'admin/TransactionController/history/$1';
$route['admin/history/(:any)/(:any)'] = 'admin/TransactionController/history/$1/$2';
$route['admin/resetHistory'] = 'admin/TransactionController/resetHistory';
$route['admin/resetHistory/(:any)'] = 'admin/TransactionController/resetHistory/$1';
$route['admin/resetHistory/(:any)/(:any)'] = 'admin/TransactionController/resetHistory/$1/$2';
$route['admin/download_history'] = 'admin/TransactionController/download_history';
$route['admin/download_history/(:any)'] = 'admin/TransactionController/download_history/$1';
$route['admin/download_history/(:any)/(:any)'] = 'admin/TransactionController/download_history/$1/$2';
$route['admin/virtual_account'] = 'admin/TransactionController/virtual_account';
$route['admin/virtual_account/(:any)'] = 'admin/TransactionController/virtual_account/$1';
$route['admin/virtual_account/(:any)/(:any)'] = 'admin/TransactionController/virtual_account/$1/$2';
$route['admin/resetVA'] = 'admin/TransactionController/resetVA';
$route['admin/resetVA/(:any)'] = 'admin/TransactionController/resetVA/$1';
$route['admin/resetVA/(:any)/(:any)'] = 'admin/TransactionController/resetVA/$1/$2';
$route['admin/VA_detail'] = 'admin/TransactionController/VA_detail';
$route['admin/VA_detail/(:any)'] = 'admin/TransactionController/VA_detail/$1';
$route['admin/VA_detail/(:any)/(:any)'] = 'admin/TransactionController/VA_detail/$1/$2';
$route['admin/download_VA'] = 'admin/TransactionController/download_VA';
$route['admin/download_VA/(:any)'] = 'admin/TransactionController/download_VA/$1';
$route['admin/download_VA/(:any)/(:any)'] = 'admin/TransactionController/download_VA/$1/$2';
$route['admin/qris'] = 'admin/TransactionController/qris';
$route['admin/qris/(:any)'] = 'admin/TransactionController/qris/$1';
$route['admin/qris/(:any)/(:any)'] = 'admin/TransactionController/qris/$1/$2';
$route['admin/resetqris'] = 'admin/TransactionController/resetqris';
$route['admin/resetqris/(:any)'] = 'admin/TransactionController/resetqris/$1';
$route['admin/resetqris/(:any)/(:any)'] = 'admin/TransactionController/resetqris/$1/$2';
$route['admin/qris_detail'] = 'admin/TransactionController/qris_detail';
$route['admin/qris_detail/(:any)'] = 'admin/TransactionController/qris_detail/$1';
$route['admin/qris_detail/(:any)/(:any)'] = 'admin/TransactionController/qris_detail/$1/$2';
$route['admin/download_qris'] = 'admin/TransactionController/download_qris';
$route['admin/download_qris/(:any)'] = 'admin/TransactionController/download_qris/$1';
$route['admin/download_qris/(:any)/(:any)'] = 'admin/TransactionController/download_qris/$1/$2';
$route['admin/ewallet'] = 'admin/TransactionController/ewallet';
$route['admin/ewallet/(:any)'] = 'admin/TransactionController/ewallet/$1';
$route['admin/ewallet/(:any)/(:any)'] = 'admin/TransactionController/ewallet/$1/$2';
$route['admin/resetewallet'] = 'admin/TransactionController/resetewallet';
$route['admin/resetewallet/(:any)'] = 'admin/TransactionController/resetewallet/$1';
$route['admin/resetewallet/(:any)/(:any)'] = 'admin/TransactionController/resetewallet/$1/$2';
$route['admin/download_ewallet'] = 'admin/TransactionController/download_ewallet';
$route['admin/download_ewallet/(:any)'] = 'admin/TransactionController/download_ewallet/$1';
$route['admin/download_ewallet/(:any)/(:any)'] = 'admin/TransactionController/download_ewallet/$1/$2';
$route['admin/bi_fast'] = 'admin/TransactionController/bi_fast';
$route['admin/bi_fast/(:any)'] = 'admin/TransactionController/bi_fast/$1';
$route['admin/bi_fast/(:any)/(:any)'] = 'admin/TransactionController/bi_fast/$1/$2';
$route['admin/resetbi_fast'] = 'admin/TransactionController/resetbi_fast';
$route['admin/resetbi_fast/(:any)'] = 'admin/TransactionController/resetbi_fast/$1';
$route['admin/resetbi_fast/(:any)/(:any)'] = 'admin/TransactionController/resetbi_fast/$1/$2';
$route['admin/bi_fast_detail'] = 'admin/TransactionController/bi_fast_detail';
$route['admin/bi_fast_detail/(:any)'] = 'admin/TransactionController/bi_fast_detail/$1';
$route['admin/bi_fast_detail/(:any)/(:any)'] = 'admin/TransactionController/bi_fast_detail/$1/$2';
$route['admin/download_bi_fast'] = 'admin/TransactionController/download_bi_fast';
$route['admin/download_bi_fast/(:any)'] = 'admin/TransactionController/download_bi_fast/$1';
$route['admin/download_bi_fast/(:any)/(:any)'] = 'admin/TransactionController/download_bi_fast/$1/$2';
$route['admin/Va_dynamic'] = 'admin/TransactionController/Va_dynamic';
$route['admin/Va_dynamic/(:any)'] = 'admin/TransactionController/Va_dynamic/$1';
$route['admin/Va_dynamic/(:any)/(:any)'] = 'admin/TransactionController/Va_dynamic/$1/$2';
$route['admin/resetVa_dynamic'] = 'admin/TransactionController/resetVa_dynamic';
$route['admin/resetVa_dynamic/(:any)'] = 'admin/TransactionController/resetVa_dynamic/$1';
$route['admin/resetVa_dynamic/(:any)/(:any)'] = 'admin/TransactionController/resetVa_dynamic/$1/$2';
$route['admin/VA_recurring'] = 'admin/TransactionController/VA_recurring';
$route['admin/VA_recurring/(:any)'] = 'admin/TransactionController/VA_recurring/$1';
$route['admin/VA_recurring/(:any)/(:any)'] = 'admin/TransactionController/VA_recurring/$1/$2';
$route['admin/resetVa_recurring'] = 'admin/TransactionController/resetVa_recurring';
$route['admin/resetVa_recurring/(:any)'] = 'admin/TransactionController/resetVa_recurring/$1';
$route['admin/resetVa_recurring/(:any)/(:any)'] = 'admin/TransactionController/resetVa_recurring/$1/$2';
$route['admin/qris_dynamic_list'] = 'admin/TransactionController/qris_dynamic_list';
$route['admin/qris_dynamic_list/(:any)'] = 'admin/TransactionController/qris_dynamic_list/$1';
$route['admin/qris_dynamic_list/(:any)/(:any)'] = 'admin/TransactionController/qris_dynamic_list/$1/$2';
$route['admin/qris_dynamic'] = 'admin/TransactionController/qris_dynamic';
$route['admin/qris_dynamic/(:any)'] = 'admin/TransactionController/qris_dynamic/$1';
$route['admin/qris_dynamic/(:any)/(:any)'] = 'admin/TransactionController/qris_dynamic/$1/$2';
$route['admin/getDetailQrisDynamicChannelExternal'] = 'admin/TransactionController/getDetailQrisDynamicChannelExternal';
$route['admin/getDetailEwalletDynamicChannelExternal'] = 'admin/TransactionController/getDetailEwalletDynamicChannelExternal';
$route['admin/getDetailBiFastChannelExternal'] = 'admin/TransactionController/getDetailBiFastChannelExternal';
$route['admin/getDetailEwalletChannelExternal'] = 'admin/TransactionController/getDetailEwalletChannelExternal';

$route['admin/getDetailVaDynamicChannelExternal'] = 'admin/TransactionController/getDetailVaDynamicChannelExternal';
$route['admin/getDetailVaRecurringChannelExternal'] = 'admin/TransactionController/getDetailVaRecurringChannelExternal';
$route['admin/getDetailQrisRecurringChannelExternal'] = 'admin/TransactionController/getDetailQrisRecurringChannelExternal';

$route['admin/resetqris_dynamic'] = 'admin/TransactionController/resetqris_dynamic';
$route['admin/resetqris_dynamic/(:any)'] = 'admin/TransactionController/resetqris_dynamic/$1';
$route['admin/resetqris_dynamic/(:any)/(:any)'] = 'admin/TransactionController/resetqris_dynamic/$1/$2';
$route['admin/ewallet_dynamic'] = 'admin/TransactionController/ewallet_dynamic';
$route['admin/ewallet_dynamic/(:any)'] = 'admin/TransactionController/ewallet_dynamic/$1';
$route['admin/ewallet_dynamic/(:any)/(:any)'] = 'admin/TransactionController/ewallet_dynamic/$1/$2';
$route['admin/ewallet_detail'] = 'admin/TransactionController/ewallet_detail';
$route['admin/ewallet_detail/(:any)'] = 'admin/TransactionController/ewallet_detail/$1';
$route['admin/ewallet_detail/(:any)/(:any)'] = 'admin/TransactionController/ewallet_detail/$1/$2';
$route['admin/resetewallet_dynamic'] = 'admin/TransactionController/resetewallet_dynamic';
$route['admin/resetewallet_dynamic/(:any)'] = 'admin/TransactionController/resetewallet_dynamic/$1';
$route['admin/resetewallet_dynamic/(:any)/(:any)'] = 'admin/TransactionController/resetewallet_dynamic/$1/$2';
$route['admin/qris_recurring'] = 'admin/TransactionController/qris_recurring';
$route['admin/qris_recurring/(:any)'] = 'admin/TransactionController/qris_recurring/$1';
$route['admin/qris_recurring/(:any)/(:any)'] = 'admin/TransactionController/qris_recurring/$1/$2';
$route['admin/resetqris_recurring'] = 'admin/TransactionController/resetqris_recurring';
$route['admin/resetqris_recurring/(:any)'] = 'admin/TransactionController/resetqris_recurring/$1';
$route['admin/resetqris_recurring/(:any)/(:any)'] = 'admin/TransactionController/resetqris_recurring/$1/$2';
$route['admin/report'] = 'admin/TransactionController/report';
$route['admin/report/(:any)'] = 'admin/TransactionController/report/$1';
$route['admin/report/(:any)/(:any)'] = 'admin/TransactionController/report/$1/$2';
$route['admin/resetdownload'] = 'admin/TransactionController/resetdownload';
$route['admin/resetdownload/(:any)'] = 'admin/TransactionController/resetdownload/$1';
$route['admin/resetdownload/(:any)/(:any)'] = 'admin/TransactionController/resetdownload/$1/$2';
$route['admin/get_submerchants'] = 'admin/TransactionController/get_submerchants';
$route['admin/get_submerchants/(:any)'] = 'admin/TransactionController/get_submerchants/$1';
$route['admin/get_submerchants/(:any)/(:any)'] = 'admin/TransactionController/get_submerchants/$1/$2';
$route['admin/getChannelsByPosition'] = 'admin/TransactionController/getChannelsByPosition';
$route['admin/createCreditBalance'] = 'admin/TransactionController/createCreditBalance';
$route['admin/createCreditBalance/(:any)'] = 'admin/TransactionController/createCreditBalance/$1';
$route['admin/createCreditBalance/(:any)/(:any)'] = 'admin/TransactionController/createCreditBalance/$1/$2';
$route['admin/createDebitBalance'] = 'admin/TransactionController/createDebitBalance';
$route['admin/createDebitBalance/(:any)'] = 'admin/TransactionController/createDebitBalance/$1';
$route['admin/createDebitBalance/(:any)/(:any)'] = 'admin/TransactionController/createDebitBalance/$1/$2';
$route['admin/download'] = 'admin/TransactionController/download';
$route['admin/download/(:any)'] = 'admin/TransactionController/download/$1';
$route['admin/download/(:any)/(:any)'] = 'admin/TransactionController/download/$1/$2';
$route['admin/SendnotifikasiVA'] = 'admin/TransactionController/SendnotifikasiVA';
$route['admin/SendnotifikasiVA/(:any)'] = 'admin/TransactionController/SendnotifikasiVA/$1';
$route['admin/SendnotifikasiVA/(:any)/(:any)'] = 'admin/TransactionController/SendnotifikasiVA/$1/$2';
$route['admin/SendnotifikasiQRIS'] = 'admin/TransactionController/SendnotifikasiQRIS';
$route['admin/SendnotifikasiQRIS/(:any)'] = 'admin/TransactionController/SendnotifikasiQRIS/$1';
$route['admin/SendnotifikasiQRIS/(:any)/(:any)'] = 'admin/TransactionController/SendnotifikasiQRIS/$1/$2';
$route['admin/Sendnotifikasiewallet'] = 'admin/TransactionController/Sendnotifikasiewallet';
$route['admin/Sendnotifikasiewallet/(:any)'] = 'admin/TransactionController/Sendnotifikasiewallet/$1';
$route['admin/Sendnotifikasiewallet/(:any)/(:any)'] = 'admin/TransactionController/Sendnotifikasiewallet/$1/$2';

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
$route['admin/cashin/external/add'] = 'admin/CashinExternalController/add';
$route['admin/cashin/external/update'] = 'admin/CashinExternalController/update';
$route['admin/cashin/external/delete/(:any)'] = 'admin/CashinExternalController/delete/$1';

// --- Cashout External Module Extraction Routes ---
$route['admin/cashout/external'] = 'admin/CashoutExternalController/index';
$route['admin/cashout/external/ajax_list'] = 'admin/CashoutExternalController/ajax_list';
$route['admin/cashout/external/add'] = 'admin/CashoutExternalController/add';
$route['admin/cashout/external/update'] = 'admin/CashoutExternalController/update';
$route['admin/cashout/external/delete/(:any)'] = 'admin/CashoutExternalController/delete/$1';

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
