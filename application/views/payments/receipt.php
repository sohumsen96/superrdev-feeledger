<div class="bg-white border rounded p-4" style="max-width: 560px">
	<div class="d-flex justify-content-between">
		<h1 class="h4">Receipt #<?php echo (int) $payment->id; ?></h1>
		<a href="<?php echo site_url('payments/edit/' . $payment->id); ?>" class="small">Edit</a>
	</div>
	<p class="mb-1"><strong><?php echo html_escape($payment->student_name); ?></strong>, <?php echo html_escape($payment->batch_name); ?></p>
	<p class="text-muted" data-paid-at="<?php echo date('Y-m-d H:i', strtotime($payment->paid_at)); ?>">
		<?php echo date('d M Y, h:i A', strtotime($payment->paid_at)); ?> · <?php echo html_escape(ucfirst($payment->mode)); ?>
		<?php if ($payment->gateway_ref): ?> · Ref <?php echo html_escape($payment->gateway_ref); ?><?php endif; ?>
	</p>
	<table class="table table-sm">
		<tr><th>Amount received</th><td class="text-end"><?php echo format_inr($payment->amount); ?></td></tr>
		<tr><td class="text-muted">Includes GST</td><td class="text-end text-muted"><?php echo format_inr($gst); ?></td></tr>
	</table>
	<?php if ($payment->remarks): ?><p class="mb-0">Remarks: <?php echo html_escape($payment->remarks); ?></p><?php endif; ?>
	<?php if ($payment->status !== 'captured'): ?><p class="text-danger mb-0">Status: <?php echo html_escape($payment->status); ?></p><?php endif; ?>
</div>
<p class="mt-3"><a href="<?php echo site_url('students/view/' . $payment->student_id); ?>">Back to student</a></p>
