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
$route['default_controller'] = 'DashboardController';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// ── Welcome ─────────────────────────────────────────────
$route['welcome'] = 'DashboardController/welcome';

// ── DashboardController ─────────────────────────────────────────────
$route['dashboard'] = 'DashboardController/index';
$route['dashboard/analytics'] = 'DashboardController/analytics';
$route['dashboard/toggle-openapi'] = 'DashboardController/toggleOpenApiStatus';
$route['dashboard/maintenance-status'] = 'DashboardController/getMaintenanceStatus';
$route['dashboard/sync-balance'] = 'DashboardController/syncAvailableBalanceMerchant';
$route['dashboard/recent-mutations/json'] = 'DashboardController/recent_mutations_json';
$route['dashboard/today-stats/json'] = 'DashboardController/ajax_today_stats_json';
$route['dashboard/monthly-stats/json'] = 'DashboardController/ajax_monthly_stats_json';
$route['dashboard/metadata/json'] = 'DashboardController/ajax_dashboard_metadata_json';
$route['dashboard/analytics-data/json'] = 'DashboardController/ajax_analytics_data_json';

// ── GlobalSearchController ─────────────────────────────────────────────
$route['dashboard/global-search'] = 'GlobalSearchController/globalSearch';
$route['dashboard/recent-search'] = 'GlobalSearchController/recentTransactionsAjax';

// ── Auth ─────────────────────────────────────────────
$route['auth'] = 'AuthController/index';
$route['auth/login'] = 'AuthController/index';
$route['auth/blocked'] = 'AuthController/blocked';
$route['auth/change-password'] = 'AuthController/changePassword';
$route['auth/forgot-password'] = 'AuthController/forgotPassword';
$route['auth/logout'] = 'AuthController/logout';
$route['auth/register'] = 'AuthController/register';
$route['auth/reset-password'] = 'AuthController/resetPassword';
$route['auth/verify'] = 'AuthController/verify';

// ── BiFastTransactionController ─────────────────────────────────────────────
$route['finance/bi-fast'] = 'BiFastTransactionController/bi_fast';
$route['finance/bi-fast/reset'] = 'BiFastTransactionController/resetbi_fast';
$route['finance/bi-fast/detail/(:num)'] = 'BiFastTransactionController/bi_fast_detail/$1';
$route['finance/bi-fast/download'] = 'BiFastTransactionController/download_bi_fast';
$route['finance/bi-fast/channel/external'] = 'BiFastTransactionController/getDetailBiFastChannelExternal';

// ── VirtualAccountTransactionController ─────────────────────────────────────────────
$route['finance/virtual-account'] = 'VirtualAccountTransactionController/virtual_account';
$route['finance/virtual-account/reset'] = 'VirtualAccountTransactionController/resetVA';
$route['finance/virtual-account/detail/(:num)'] = 'VirtualAccountTransactionController/VA_detail/$1';
$route['finance/virtual-account/download'] = 'VirtualAccountTransactionController/download_VA';
$route['virtual-account/dynamic'] = 'VirtualAccountTransactionController/Va_dynamic';
$route['virtual-account/dynamic/reset'] = 'VirtualAccountTransactionController/resetVa_dynamic';
$route['virtual-account/recurring'] = 'VirtualAccountTransactionController/VA_recurring';
$route['virtual-account/recurring/reset'] = 'VirtualAccountTransactionController/resetVa_recurring';
$route['virtual-account/notification/resend/(:any)/(:any)'] = 'VirtualAccountTransactionController/SendnotifikasiVA/$1/$2';
$route['virtual-account/dynamic/channel/external'] = 'VirtualAccountTransactionController/getDetailVaDynamicChannelExternal';
$route['virtual-account/recurring/channel/external'] = 'VirtualAccountTransactionController/getDetailVaRecurringChannelExternal';

// ── QrisTransactionController ─────────────────────────────────────────────
$route['finance/qris'] = 'QrisTransactionController/qris';
$route['finance/qris/reset'] = 'QrisTransactionController/resetqris';
$route['finance/qris/detail/(:num)'] = 'QrisTransactionController/qris_detail/$1';
$route['finance/qris/download'] = 'QrisTransactionController/download_qris';
$route['qris/dynamic'] = 'QrisTransactionController/qris_dynamic';
$route['qris/dynamic/reset'] = 'QrisTransactionController/resetqris_dynamic';
$route['qris/recurring'] = 'QrisTransactionController/qris_recurring';
$route['qris/recurring/reset'] = 'QrisTransactionController/resetqris_recurring';
$route['qris/notification/resend/(:any)/(:any)'] = 'QrisTransactionController/SendnotifikasiQRIS/$1/$2';
$route['qris/dynamic/channel/external'] = 'QrisTransactionController/getDetailQrisDynamicChannelExternal';
$route['qris/recurring/channel/external'] = 'QrisTransactionController/getDetailQrisRecurringChannelExternal';
$route['qris/dynamic/list'] = 'QrisTransactionController/qris_dynamic_list';

// ── EwalletTransactionController ─────────────────────────────────────────────
$route['finance/e-wallet'] = 'EwalletTransactionController/ewallet';
$route['finance/e-wallet/reset'] = 'EwalletTransactionController/resetewallet';
$route['finance/e-wallet/detail/(:num)'] = 'EwalletTransactionController/ewallet_detail/$1';
$route['finance/e-wallet/download'] = 'EwalletTransactionController/download_ewallet';
$route['e-wallet/dynamic'] = 'EwalletTransactionController/ewallet_dynamic';
$route['e-wallet/dynamic/reset'] = 'EwalletTransactionController/resetewallet_dynamic';
$route['finance/e-wallet/notification/resend/(:any)/(:any)'] = 'EwalletTransactionController/Sendnotifikasiewallet/$1/$2';
$route['finance/e-wallet/channel/external'] = 'EwalletTransactionController/getDetailEwalletChannelExternal';
$route['e-wallet/dynamic/channel/external'] = 'EwalletTransactionController/getDetailEwalletDynamicChannelExternal';

// ── HistoryController ─────────────────────────────────────────────
$route['finance/history'] = 'HistoryController/index';
$route['finance/history/reset'] = 'HistoryController/resetHistory';
$route['finance/history/download'] = 'HistoryController/download_history';

// ── TransactionMutationController ─────────────────────────────────────────────
$route['finance/mutation'] = 'TransactionMutationController/mutation';
$route['finance/mutation/reset/(:num)'] = 'TransactionMutationController/resetMutation/$1';
$route['finance/mutation/download'] = 'TransactionMutationController/download_mutation';
$route['finance/mutation/channels'] = 'TransactionMutationController/getChannelsByPosition';
$route['finance/mutation/(:num)'] = 'TransactionMutationController/mutation/$1';

// ── MerchantManagementController ─────────────────────────────────────────────
$route['merchant/manage'] = 'MerchantManagementController/merchant';
$route['merchant/manage/reset'] = 'MerchantManagementController/resetMerchant';
$route['merchant/supervisor'] = 'MerchantManagementController/merchant_spv';
$route['merchant/supervisor/reset'] = 'MerchantManagementController/resetMerchantSpv';
$route['merchant/supervisor/delete/(:num)'] = 'MerchantManagementController/deleteMerchantSpv/$1';
$route['merchant/manage/register'] = 'MerchantManagementController/registerMerchant';
$route['merchant/supervisor/register'] = 'MerchantManagementController/registerMerchantSpv';
$route['merchant/supervisor/get/(:num)'] = 'MerchantManagementController/getSupervisorJson/$1';
$route['merchant/supervisor/update/(:num)'] = 'MerchantManagementController/updateMerchantSpv/$1';
$route['merchant/supervisor/search'] = 'MerchantManagementController/searchMerchants';
$route['merchant/manage/list/reset/(:num)'] = 'MerchantManagementController/resetListMerchants/$1';
$route['merchant/manage/list/(:num)'] = 'MerchantManagementController/listMerchants/$1';
$route['merchant/manage/edit/(:num)'] = 'MerchantManagementController/editMerchant/$1';
$route['merchant/manage/update/(:num)'] = 'MerchantManagementController/updateMerchant/$1';
$route['merchant/manage/search'] = 'MerchantManagementController/searchMerchants';
$route['merchant/manage/add'] = 'MerchantManagementController/addMerchant';
$route['merchant/manage/detail/(:num)'] = 'MerchantManagementController/detailMerchant/$1';
$route['merchant/manage/history-ajax/(:num)'] = 'MerchantManagementController/detailHistoryAjax/$1';
$route['merchant/manage/mutation-ajax/(:num)'] = 'MerchantManagementController/detailMutationAjax/$1';
$route['merchant/manage/submerchant-ajax/(:num)'] = 'MerchantManagementController/detailSubmerchantAjax/$1';
$route['merchant/manage/overview-ajax/(:num)'] = 'MerchantManagementController/detailOverviewAjax/$1';

// Cashin Fee Setting Routes
$route['merchant/setting-cashin-fee/(:num)'] = 'MerchantManagementController/settingcashinfee/$1';
$route['merchant/setting-cashin-fee/create'] = 'MerchantManagementController/createSettingCashinFee';
$route['merchant/setting-cashin-fee/bulk-create/(:num)'] = 'MerchantManagementController/bulkCreateSettingCashinFee/$1';
$route['merchant/setting-cashin-fee/edit/(:num)/(:num)'] = 'MerchantManagementController/editSettingCashinFee/$1/$2';
$route['merchant/setting-cashin-fee/delete/(:num)/(:num)'] = 'MerchantManagementController/deleteSettingCashinFee/$1/$2';
$route['merchant/setting-cashin-fee/groups'] = 'MerchantManagementController/getCashinChannelGroups';

// Cashout Fee Setting Routes
$route['merchant/setting-cashout-fee/(:num)'] = 'MerchantManagementController/settingcashoutfee/$1';
$route['merchant/setting-cashout-fee/create'] = 'MerchantManagementController/createSettingCashoutFee';
$route['merchant/setting-cashout-fee/bulk-create/(:num)'] = 'MerchantManagementController/bulkCreateSettingCashoutFee/$1';
$route['merchant/setting-cashout-fee/edit/(:num)/(:num)'] = 'MerchantManagementController/editSettingCashoutFee/$1/$2';
$route['merchant/setting-cashout-fee/delete/(:num)/(:num)'] = 'MerchantManagementController/deleteSettingCashoutFee/$1/$2';
$route['merchant/setting-cashout-fee/groups'] = 'MerchantManagementController/getCashoutChannelGroups';

$route['merchant/balance/credit'] = 'MerchantManagementController/createCreditBalance';
$route['merchant/balance/debit'] = 'MerchantManagementController/createDebitBalance';
$route['merchant/permissions/(:any)'] = 'MerchantManagementController/fetchMerchantPermissions/$1';
$route['merchant/permissions/(:any)/save'] = 'MerchantManagementController/saveDelegation/$1';

// ── MerchantSubAccountController ─────────────────────────────────────────────
$route['merchant/sub-account'] = 'MerchantSubAccountController/Submerchant';
$route['merchant/sub-account/reset'] = 'MerchantSubAccountController/resetsubmerchant';
$route['merchant/sub-account/register'] = 'MerchantSubAccountController/registersubMerchant';
$route['merchant/sub-account/edit/(:num)'] = 'MerchantSubAccountController/edit_submerchant/$1';
$route['merchant/sub-account/list'] = 'MerchantSubAccountController/get_submerchants';
$route['merchant/sub-account/(:num)'] = 'MerchantSubAccountController/Submerchant/$1';

// ── ReportController ─────────────────────────────────────────────
$route['report/download'] = 'ReportController/report';
$route['report/download/reset'] = 'ReportController/reset_download';
$route['report/download/export'] = 'ReportController/download';
$route['report/balance-log'] = 'ReportController/balance_log';
$route['report/balance-log/reset'] = 'ReportController/reset_balance_log';

// ── UserAccessController ─────────────────────────────────────────────
$route['access-control/holiday'] = 'UserAccessController/holiday';
$route['access-control/holiday/manage'] = 'UserAccessController/manageHoliday';
$route['access-control/accounts'] = 'UserAccessController/listAdmin';
$route['access-control/accounts/create'] = 'UserAccessController/createAdmin';
$route['access-control/accounts/update/(:num)'] = 'UserAccessController/updateAdmin/$1';
$route['access-control/accounts/delete/(:num)'] = 'UserAccessController/deleteAdmin/$1';

// ── Menu ─────────────────────────────────────────────
$route['menu'] = 'MenuController/index';
$route['menu/sub-menu'] = 'MenuController/subMenu';
$route['menu/change-menu/(:num)'] = 'MenuController/changeMenu/$1';
$route['menu/update-menu'] = 'MenuController/updateMenu';
$route['menu/update-menu/ajax'] = 'MenuController/updateMenuAjax';
$route['menu/sub-menu/edit/(:num)'] = 'MenuController/editSubMenu/$1';
$route['menu/sub-menu/update'] = 'MenuController/updateSubMenu';
$route['menu/delete/(:num)'] = 'MenuController/hapus/$1';
$route['menu/sub-menu/delete/(:num)'] = 'MenuController/hapus_subMenu/$1';
$route['access-control/roles'] = 'MenuController/role';
$route['access-control/roles/access/(:num)'] = 'MenuController/roleAccess/$1';
$route['access-control/roles/change-access'] = 'MenuController/changeAccess';
$route['menu/save/ajax'] = 'MenuController/saveMenuAjax';
$route['menu/delete/ajax'] = 'MenuController/deleteMenuAjax';
$route['menu/get/(:num)'] = 'MenuController/getMenuById/$1';

// ── User ─────────────────────────────────────────────
$route['user'] = 'UserController/index';
$route['user/change-password'] = 'UserController/changePassword';

// ── CashinExternalController ─────────────────────────────────────────────
$route['external/cashin'] = 'CashinExternalController/index';
$route['external/cashin/list'] = 'CashinExternalController/ajax_list';
$route['external/cashin/create'] = 'CashinExternalController/add_view';
$route['external/cashin/edit/(:num)'] = 'CashinExternalController/edit_view/$1';
$route['external/cashin/add'] = 'CashinExternalController/add';
$route['external/cashin/update'] = 'CashinExternalController/update';
$route['external/cashin/delete/(:num)'] = 'CashinExternalController/delete/$1';
$route['external/cashin/bulk-update'] = 'CashinExternalController/bulk_update';
$route['external/cashin/get-channels'] = 'CashinExternalController/get_channel_ids';
$route['external/cashin/get-filter-options'] = 'CashinExternalController/get_filter_options';
$route['external/cashin/get-merchant-mappings'] = 'CashinExternalController/get_merchant_mappings';

// ── CashoutExternalController ─────────────────────────────────────────────
$route['external/cashout'] = 'CashoutExternalController/index';
$route['external/cashout/list'] = 'CashoutExternalController/ajax_list';
$route['external/cashout/create'] = 'CashoutExternalController/add_view';
$route['external/cashout/edit/(:num)'] = 'CashoutExternalController/edit_view/$1';
$route['external/cashout/add'] = 'CashoutExternalController/add';
$route['external/cashout/update'] = 'CashoutExternalController/update';
$route['external/cashout/delete/(:num)'] = 'CashoutExternalController/delete/$1';
$route['external/cashout/bulk-update'] = 'CashoutExternalController/bulk_update';
$route['external/cashout/get-channels'] = 'CashoutExternalController/get_channel_ids';
$route['external/cashout/get-filter-options'] = 'CashoutExternalController/get_filter_options';
$route['external/cashout/get-merchant-mappings'] = 'CashoutExternalController/get_merchant_mappings';

// ── ChannelController ─────────────────────────────────────────────
$route['channel/cashin'] = 'ChannelController/cashin';
$route['channel/cashout'] = 'ChannelController/cashout';
$route['channel/cashin/create'] = 'ChannelController/createCashinChanel';
$route['channel/cashout/create'] = 'ChannelController/createCashOutChanel';
$route['channel/cashin/update'] = 'ChannelController/updateCashinChanel';
$route['channel/cashout/update'] = 'ChannelController/updateCashOutChanel';
$route['channel/cashin/delete/(:any)'] = 'ChannelController/deleteCashInChanel/$1';
$route['channel/cashout/delete/(:any)'] = 'ChannelController/deleteCashOutChanel/$1';
$route['channel/get-master-filter-options'] = 'ChannelController/get_master_filter_options';

// ── ServiceController ─────────────────────────────────────────────
$route['product/pulsa-reguler'] = 'ServiceController/pulsa_reguler';
$route['product/paket-data'] = 'ServiceController/paket_data';
$route['product/token-listrik'] = 'ServiceController/token_listrik';
$route['product/ewallet/gopay'] = 'ServiceController/topupgopay';
$route['product/ewallet/dana'] = 'ServiceController/topupdana';
$route['product/ewallet/ovo'] = 'ServiceController/topupovo';
$route['product/games/mobile-legend'] = 'ServiceController/mobilelegend';
$route['product/games/pubg'] = 'ServiceController/pubgmobile';
$route['product/games/free-fire'] = 'ServiceController/freefire';
$route['product/games/hago'] = 'ServiceController/hago';
$route['product/games/google-play'] = 'ServiceController/googleplay';
$route['product/create'] = 'ServiceController/createProduk';
$route['product/update'] = 'ServiceController/updateProduct';
$route['product/delete/(:any)'] = 'ServiceController/deleteProduct/$1';

// ── HealthCheck ─────────────────────────────────────────────
$route['health/db-check'] = 'HealthController/dbCheck';