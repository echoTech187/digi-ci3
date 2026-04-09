--
-- Truncate tables before seeding
--

TRUNCATE TABLE `user_menu`;
TRUNCATE TABLE `user_access_menu`;

--
-- Dumping data for table `user_menu`
--

INSERT INTO `user_menu` (`id`, `title`, `url`, `icon`, `parent_id`, `menu_order`) VALUES
(1, 'Dashboard', 'admin', 'fas fa-fw fa-tachometer-alt', 0, 1),
(2, 'Merchant', 'admin/merchant', 'fas fa-wallet', 0, 2),
(3, 'Merchant Supervisor', 'admin/merchant_spv', 'fas fa-wallet', 0, 3),
(4, 'Analytics', 'admin/analytics', 'fas fa-wallet', 0, 4),
(5, 'History Transaction', '#', 'fas fa-history', 0, 5),
(6, 'Purchase', 'admin/history', '', 5, 1),
(7, 'Virtual Account', 'admin/virtual_account', '', 5, 2),
(8, 'QRIS', 'admin/qris', '', 5, 3),
(9, 'Disbursement', 'admin/bi_fast', '', 5, 4),
(10, 'Ewallet', 'admin/ewallet', '', 5, 5),
(11, 'Virtual Account Dynamic', '#', 'fas fa-money-check', 0, 6),
(12, 'History VA Dynamic', 'admin/Va_dynamic', '', 11, 1),
(13, 'Virtual Account Recurring', '#', 'fas fa-money-check', 0, 7),
(14, 'History VA Recurring', 'admin/VA_recurring', '', 13, 1),
(15, 'QRIS Dynamic', '#', 'fas fa-certificate', 0, 8),
(16, 'History QRIS Dynamic', 'admin/qris_dynamic', '', 15, 1),
(17, 'QRIS Recurring', '#', 'fas fa-certificate', 0, 9),
(18, 'History QRIS Recurring', 'admin/qris_recurring', '', 17, 1),
(19, 'Ewallet Dynamic', '#', 'fas fa-certificate', 0, 10),
(20, 'History Ewallet Dynamic', 'admin/ewallet_dynamic', '', 19, 1),
(21, 'Service', '#', 'fas fa-bookmark', 0, 11),
(22, 'Pulsa Reguler', 'admin/pulsa_reguler', '', 21, 1),
(23, 'Paket Data', 'admin/paket_data', '', 21, 2),
(24, 'Token Listrik', 'admin/token_listrik', '', 21, 3),
(25, 'Topup Gopay', 'admin/topupgopay', '', 21, 4),
(26, 'Topup Dana', 'admin/topupdana', '', 21, 5),
(27, 'Topup Ovo', 'admin/topupovo', '', 21, 6),
(28, 'Mobile Legend', 'admin/mobilelegend', '', 21, 7),
(29, 'PUBG Mobile', 'admin/pubgmobile', '', 21, 8),
(30, 'Free Fire', 'admin/freefire', '', 21, 9),
(31, 'Hago', 'admin/hago', '', 21, 10),
(32, 'Google Play', 'admin/googleplay', '', 21, 11),
(33, 'Channel', '#', 'fas fa-marker', 0, 12),
(34, 'Cash In Channel', 'admin/cashin', '', 33, 1),
(35, 'Cash Out Channel', 'admin/cashout', '', 33, 2),
(36, 'Balance History', 'admin/balance_log', 'far fa-save', 0, 13),
(37, 'Report', 'admin/report', 'far fa-save', 0, 14),
(38, 'Holiday', 'admin/holiday', 'far fa-calendar', 0, 15),
(39, 'Management Users', 'admin/listadmin', 'far fa-user', 0, 16),
(40, 'Managemen Access', 'menu/role', 'fas fa-user-cog', 0, 17);

--
-- Dumping data for table `user_access_menu` (Superadmin Role 1)
--

INSERT INTO `user_access_menu` (`role_id`, `menu_id`) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6), (1, 7), (1, 8), (1, 9), (1, 10),
(1, 11), (1, 12), (1, 13), (1, 14), (1, 15), (1, 16), (1, 17), (1, 18), (1, 19), (1, 20),
(1, 21), (1, 22), (1, 23), (1, 24), (1, 25), (1, 26), (1, 27), (1, 28), (1, 29), (1, 30),
(1, 31), (1, 32), (1, 33), (1, 34), (1, 35), (1, 36), (1, 37), (1, 38), (1, 39), (1, 40);
