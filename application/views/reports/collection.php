<h1 class="h3 mb-3">Monthly collection</h1>

<form class="row g-2 mb-3" method="get" action="<?php echo site_url('reports/collection'); ?>">
	<div class="col-auto"><input class="form-control" type="month" name="month" value="<?php echo html_escape($month); ?>"></div>
	<div class="col-auto"><button class="btn btn-outline-secondary" type="submit">Show</button></div>
</form>

<div class="bg-white border rounded p-3 mb-3" style="max-width: 420px">
	<div class="text-muted small"><?php echo date('F Y', strtotime($month . '-01')); ?></div>
	<div class="display-6" data-total="<?php echo number_format((float) $total, 2, '.', ''); ?>"><?php echo format_inr($total); ?></div>
</div>

<table class="table table-sm bg-white" style="max-width: 720px">
	<thead><tr><th>Batch</th><th>Mode</th><th class="text-end">Payments</th><th class="text-end">Amount</th></tr></thead>
	<tbody>
	<?php foreach ($breakdown as $r): ?>
		<tr><td><?php echo html_escape($r->batch_name); ?></td><td><?php echo html_escape(ucfirst($r->mode)); ?></td><td class="text-end"><?php echo (int) $r->payments; ?></td><td class="text-end"><?php echo format_inr($r->amount); ?></td></tr>
	<?php endforeach; ?>
	</tbody>
</table>
