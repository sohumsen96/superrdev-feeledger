<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo html_escape(isset($title) ? $title . ' · FeeLedger' : 'FeeLedger'); ?></title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand navbar-dark bg-dark mb-4">
	<div class="container">
		<a class="navbar-brand" href="<?php echo site_url('dashboard'); ?>">FeeLedger</a>
		<ul class="navbar-nav">
			<li class="nav-item"><a class="nav-link" href="<?php echo site_url('dashboard'); ?>">Dashboard</a></li>
			<li class="nav-item"><a class="nav-link" href="<?php echo site_url('students'); ?>">Students</a></li>
			<li class="nav-item"><a class="nav-link" href="<?php echo site_url('payments/create'); ?>">Record payment</a></li>
			<li class="nav-item"><a class="nav-link" href="<?php echo site_url('reports/collection'); ?>">Reports</a></li>
		</ul>
		<span class="navbar-text small"><?php echo html_escape($centre_name); ?></span>
	</div>
</nav>
<main class="container">
	<?php if ($this->session->flashdata('message')): ?>
		<div class="alert alert-success"><?php echo html_escape($this->session->flashdata('message')); ?></div>
	<?php endif; ?>
	<?php if ($this->session->flashdata('error')): ?>
		<div class="alert alert-danger"><?php echo html_escape($this->session->flashdata('error')); ?></div>
	<?php endif; ?>
	<?php echo $content; ?>
</main>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
