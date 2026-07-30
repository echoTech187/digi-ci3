<!DOCTYPE html>
<html lang="en">

<head>

	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="">
	<meta name="author" content="">

	<link rel="icon" href="<?= base_url('public/icon/favicon.ico'); ?>" type="image/x-icon">
	<title><?= $title; ?></title>
	<!-- CSRF Token for AJAX -->
	<meta name="csrf-token-name" content="<?= $this->security->get_csrf_token_name(); ?>">
	<meta name="csrf-token-hash" content="<?= $this->security->get_csrf_hash(); ?>">

	<!-- Theme Initialization -->
	<script>
		(function() {
			const savedTheme = localStorage.getItem('theme') || 'dark'; // default to dark for auth
			document.documentElement.setAttribute('data-theme', savedTheme);
		})();
	</script>

	<!-- Custom fonts for this template-->
	<link href="<?= base_url('assets/'); ?>vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

	<!-- Custom styles for this template-->
	<link href="<?= base_url('assets/'); ?>css/sb-admin-2.min.css" rel="stylesheet">
	<link href="<?= base_url('assets/'); ?>css/theme-1.css" rel="stylesheet">
	<!-- Premium Auth Override -->
	<link href="<?= base_url('assets/'); ?>css/premium-auth.css?v=<?= time(); ?>" rel="stylesheet">
</head>

<body>

	<div class="container">
