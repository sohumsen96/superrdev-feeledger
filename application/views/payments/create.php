<h1 class="h3 mb-3">Record payment</h1>

<?php echo validation_errors('<div class="alert alert-danger py-2">', '</div>'); ?>

<?php echo form_open('payments/create', array('class' => 'bg-white p-3 border rounded', 'style' => 'max-width: 560px')); ?>
	<div class="mb-2"><label class="form-label">Student</label>
		<select class="form-select" name="student_id">
		<?php foreach ($students as $s): ?>
			<option value="<?php echo (int) $s->id; ?>" <?php echo ((int) $s->id === $student_id) ? 'selected' : set_select('student_id', $s->id); ?>><?php echo html_escape($s->name . ' (' . $s->batch_name . ')'); ?></option>
		<?php endforeach; ?>
		</select>
	</div>
	<div class="mb-2"><label class="form-label">Amount, ₹</label><input class="form-control" type="text" name="amount" placeholder="e.g. 1,500" value="<?php echo set_value('amount'); ?>"></div>
	<div class="mb-3"><label class="form-label">Remarks</label><input class="form-control" name="remarks" value="<?php echo set_value('remarks'); ?>"></div>
	<button class="btn btn-primary" type="submit">Save payment</button>
<?php echo form_close(); ?>
