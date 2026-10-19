<h1 class="h3 mb-3">Students</h1>

<?php $this->load->view('students/_search_form', array('q' => $q)); ?>

<p class="text-muted"><?php echo count($students); ?> result(s) for "<?php echo html_escape($q); ?>"</p>
<table class="table table-sm table-hover bg-white">
	<thead><tr><th>Name</th><th>Batch</th><th>Phone</th><th>Joined</th></tr></thead>
	<tbody>
	<?php foreach ($students as $s): ?>
		<tr data-student-id="<?php echo (int) $s->id; ?>">
			<td><a href="<?php echo site_url('students/view/' . $s->id); ?>"><?php echo html_escape($s->name); ?></a></td>
			<td><?php echo html_escape($s->batch_name); ?></td>
			<td><?php echo html_escape($s->phone); ?></td>
			<td><?php echo display_date($s->joined_on); ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
