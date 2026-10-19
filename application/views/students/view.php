<h1 class="h3"><?php echo html_escape($student->name); ?></h1>
<p class="text-muted"><?php echo html_escape($student->batch_name); ?> · Phone <?php echo html_escape($student->phone); ?> · Joined <?php echo display_date($student->joined_on); ?></p>

<div class="row">
	<div class="col-md-4">
		<h2 class="h5">Guardians</h2>
		<ul class="list-unstyled">
		<?php foreach ($guardians as $g): ?>
			<li><?php echo html_escape($g->name); ?> (<?php echo html_escape($g->relation); ?>) · <?php echo html_escape($g->phone); ?></li>
		<?php endforeach; ?>
		</ul>
		<a class="btn btn-primary btn-sm" href="<?php echo site_url('payments/create?student_id=' . $student->id); ?>">Record payment</a>
	</div>
	<div class="col-md-8">
		<h2 class="h5">Installments</h2>
		<table class="table table-sm bg-white">
			<thead><tr><th>#</th><th>Due</th><th class="text-end">Amount</th><th>Status</th><th class="text-end">Late fee</th></tr></thead>
			<tbody>
			<?php foreach ($installments as $i): ?>
				<tr data-installment-id="<?php echo (int) $i->id; ?>" data-late-fee="<?php echo (int) $i->late_fee; ?>">
					<td><?php echo (int) $i->seq; ?></td>
					<td><?php echo display_date($i->due_date); ?></td>
					<td class="text-end"><?php echo format_inr($i->amount); ?></td>
					<td><?php echo $i->status === 'paid' ? '<span class="badge bg-success">Paid</span>' : '<span class="badge bg-secondary">Pending</span>'; ?></td>
					<td class="text-end"><?php echo $i->late_fee ? format_inr($i->late_fee) : ''; ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>

		<h2 class="h5">Payments</h2>
		<table class="table table-sm bg-white">
			<thead><tr><th>Date</th><th>Mode</th><th class="text-end">Amount</th><th>Status</th><th>Remarks</th><th></th></tr></thead>
			<tbody>
			<?php foreach ($payments as $p): ?>
				<tr>
					<td><?php echo date('d M Y, H:i', strtotime($p->paid_at)); ?></td>
					<td><?php echo html_escape(ucfirst($p->mode)); ?></td>
					<td class="text-end"><?php echo format_inr($p->amount); ?></td>
					<td><?php echo html_escape($p->status); ?></td>
					<td><?php echo html_escape($p->remarks); ?></td>
					<td><a href="<?php echo site_url('payments/receipt/' . $p->id); ?>">Receipt</a></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
