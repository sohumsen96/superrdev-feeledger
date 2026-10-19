<h1 class="h3 mb-3">Enrol student</h1>

<?php echo validation_errors('<div class="alert alert-danger py-2">', '</div>'); ?>

<?php echo form_open('students/enrol', array('class' => 'bg-white p-3 border rounded', 'style' => 'max-width: 640px')); ?>
	<div class="mb-2"><label class="form-label">Student name</label><input class="form-control" name="name" value="<?php echo set_value('name'); ?>"></div>
	<div class="mb-2"><label class="form-label">Phone</label><input class="form-control" name="phone" value="<?php echo set_value('phone'); ?>"></div>
	<div class="mb-2"><label class="form-label">Email (optional)</label><input class="form-control" name="email" value="<?php echo set_value('email'); ?>"></div>
	<div class="mb-2"><label class="form-label">Batch</label>
		<select class="form-select" name="batch_id">
		<?php foreach ($batches as $b): ?>
			<option value="<?php echo (int) $b->id; ?>" <?php echo set_select('batch_id', $b->id); ?>><?php echo html_escape($b->name); ?> (starts <?php echo display_date($b->start_date); ?>, <?php echo format_inr($b->monthly_fee); ?> × <?php echo (int) $b->installments; ?>)</option>
		<?php endforeach; ?>
		</select>
	</div>
	<div class="mb-3"><label class="form-label">Discount, ₹ (optional)</label><input class="form-control" name="discount" value="<?php echo set_value('discount'); ?>"></div>
	<h2 class="h6">Guardian (optional)</h2>
	<div class="row g-2 mb-3">
		<div class="col"><input class="form-control" name="guardian_name" placeholder="Name" value="<?php echo set_value('guardian_name'); ?>"></div>
		<div class="col-3"><select class="form-select" name="guardian_relation"><option>Father</option><option>Mother</option><option>Other</option></select></div>
		<div class="col"><input class="form-control" name="guardian_phone" placeholder="Phone" value="<?php echo set_value('guardian_phone'); ?>"></div>
	</div>
	<button class="btn btn-primary" type="submit">Enrol</button>
<?php echo form_close(); ?>
