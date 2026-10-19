<div class="d-flex justify-content-between align-items-center mb-3">
	<h1 class="h3 mb-0">Students</h1>
	<a class="btn btn-primary" href="<?php echo site_url('students/enrol'); ?>">Enrol student</a>
</div>

<?php $this->load->view('students/_search_form', array('q' => $q)); ?>

<table class="table table-sm table-hover bg-white">
	<thead>
		<tr><th>Name</th><th>Batch</th><th>Guardian phone</th><th class="text-end">Total fee</th><th class="text-end">Paid</th><th class="text-end">Balance</th></tr>
	</thead>
	<tbody>
	<?php foreach ($students as $s): ?>
		<tr data-student-id="<?php echo (int) $s->id; ?>" data-balance="<?php echo number_format($s->balance, 2, '.', ''); ?>">
			<td><a href="<?php echo site_url('students/view/' . $s->id); ?>"><?php echo html_escape($s->name); ?></a></td>
			<td><?php echo html_escape($s->batch_name); ?></td>
			<td><?php echo html_escape($s->guardian_phones); ?></td>
			<td class="text-end"><?php echo format_inr($s->total_fee); ?></td>
			<td class="text-end"><?php echo format_inr($s->total_paid); ?></td>
			<td class="text-end<?php echo $s->balance > 0 ? ' text-danger' : ''; ?>"><?php echo format_inr($s->balance); ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
