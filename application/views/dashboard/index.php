<div class="d-flex justify-content-between align-items-center mb-3">
	<h1 class="h3 mb-0">Dashboard</h1>
	<form method="get" action="<?php echo site_url('dashboard'); ?>">
		<select class="form-select" name="batch" onchange="this.form.submit()">
		<?php foreach ($batches as $b): ?>
			<option value="<?php echo (int) $b->id; ?>" <?php echo (int) $b->id === (int) $batch->id ? 'selected' : ''; ?>><?php echo html_escape($b->name); ?><?php echo $b->status === 'paused' ? ' (paused)' : ''; ?></option>
		<?php endforeach; ?>
		</select>
	</form>
</div>

<div class="row g-3 mb-4">
	<div class="col-md-4"><div class="bg-white border rounded p-3"><div class="text-muted small">Students</div><div class="h3 mb-0"><?php echo (int) $students; ?></div></div></div>
	<div class="col-md-4"><div class="bg-white border rounded p-3"><div class="text-muted small">Overdue installments</div><div class="h3 mb-0" data-overdue="<?php echo (int) $overdue; ?>"><?php echo (int) $overdue; ?></div></div></div>
	<div class="col-md-4"><div class="bg-white border rounded p-3"><div class="text-muted small">Monthly fee</div><div class="h3 mb-0"><?php echo format_inr($batch->monthly_fee); ?></div></div></div>
</div>

<?php if ($batch->status === 'paused'): ?>
	<div class="alert alert-warning">This batch is paused. Installments don't count as overdue during the pause.</div>
<?php endif; ?>

<h2 class="h5">Recent payments</h2>
<?php if (empty($recent)): ?>
	<p class="text-muted">No recent payments.</p>
<?php else: ?>
	<table class="table table-sm bg-white">
		<thead><tr><th>Date</th><th>Student</th><th>Mode</th><th class="text-end">Amount</th><th></th></tr></thead>
		<tbody>
		<?php foreach ($recent as $p): ?>
			<tr data-payment-row data-paid-at="<?php echo html_escape($p->paid_at); ?>">
				<td><?php echo date('d M Y, H:i', strtotime($p->paid_at)); ?></td>
				<td><?php echo html_escape(isset($names[$p->student_id]) ? $names[$p->student_id] : ''); ?></td>
				<td><?php echo html_escape(ucfirst($p->mode)); ?></td>
				<td class="text-end"><?php echo format_inr($p->amount); ?></td>
				<td><a href="<?php echo site_url('payments/receipt/' . $p->id); ?>">Receipt</a></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>
