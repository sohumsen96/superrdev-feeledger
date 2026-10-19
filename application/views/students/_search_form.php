<form class="row g-2 mb-3" method="get" action="<?php echo site_url('students'); ?>">
	<div class="col-auto"><input class="form-control" type="search" name="q" value="<?php echo html_escape($q); ?>" placeholder="Name or phone"></div>
	<div class="col-auto"><button class="btn btn-outline-secondary" type="submit">Search</button></div>
	<?php if ($q !== ''): ?><div class="col-auto"><a class="btn btn-link" href="<?php echo site_url('students'); ?>">Clear</a></div><?php endif; ?>
</form>
