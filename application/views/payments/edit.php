<h1 class="h3 mb-3">Edit payment #<?php echo (int) $payment->id; ?></h1>
<p class="text-muted"><?php echo html_escape($payment->student_name); ?> · <?php echo date('d M Y, h:i A', strtotime($payment->paid_at)); ?></p>

<?php echo validation_errors('<div class="alert alert-danger py-2">', '</div>'); ?>

<?php echo form_open('payments/edit/' . $payment->id, array('class' => 'bg-white p-3 border rounded', 'style' => 'max-width: 560px')); ?>
	<div class="mb-2"><label class="form-label">Amount, ₹</label><input class="form-control" type="text" name="amount" value="<?php echo set_value('amount', $payment->amount); ?>"></div>
	<div class="mb-3"><label class="form-label">Remarks</label><input class="form-control" name="remarks" value="<?php echo set_value('remarks', $payment->remarks); ?>"></div>
	<button class="btn btn-primary" type="submit">Save changes</button>
<?php echo form_close(); ?>
