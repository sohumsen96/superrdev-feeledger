<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payments extends MY_Controller {

	public function create()
	{
		$this->form_validation->set_rules('student_id', 'Student', 'required|integer');
		$this->form_validation->set_rules('amount', 'Amount', 'required');
		$this->form_validation->set_rules('remarks', 'Remarks', 'trim|max_length[255]');

		if ($this->form_validation->run() === FALSE)
		{
			$this->render('payments/create', array(
				'title'      => 'Record payment',
				'students'   => $this->student_model->list_with_balances(),
				'student_id' => (int) $this->input->get('student_id'),
			));
			return;
		}

		$student = $this->student_model->get($this->input->post('student_id'));
		if ( ! $student)
		{
			show_404();
		}

		$payment_id = $this->payment_model->record_counter(array(
			'student_id' => $student->id,
			'batch_id'   => $student->batch_id,
			'amount'     => $this->input->post('amount'),
			'remarks'    => $this->input->post('remarks'),
		));
		$this->installment_model->allocate($student->id);

		$this->session->set_flashdata('message', 'Payment recorded.');
		redirect('payments/receipt/' . $payment_id);
	}

	public function receipt($id)
	{
		$payment = $this->payment_model->get($id);
		if ( ! $payment)
		{
			show_404();
		}
		$gst_rate = $this->config->item('gst_rate');
		$this->render('payments/receipt', array(
			'title'   => 'Receipt #' . $payment->id,
			'payment' => $payment,
			'gst'     => round($payment->amount - $payment->amount / (1 + $gst_rate), 2),
		));
	}

	public function edit($id)
	{
		$payment = $this->payment_model->get($id);
		if ( ! $payment)
		{
			show_404();
		}

		$this->form_validation->set_rules('amount', 'Amount', 'required');
		$this->form_validation->set_rules('remarks', 'Remarks', 'trim|max_length[255]');

		if ($this->form_validation->run() === FALSE)
		{
			$this->render('payments/edit', array('title' => 'Edit payment #' . $payment->id, 'payment' => $payment));
			return;
		}

		$this->payment_model->update($id, array(
			'amount'  => $this->input->post('amount'),
			'remarks' => $this->input->post('remarks'),
		));
		$this->installment_model->allocate($payment->student_id);

		$this->session->set_flashdata('message', 'Payment updated.');
		redirect('payments/receipt/' . $id);
	}

	/**
	 * Called by the payment gateway when an online payment is captured.
	 * The gateway can't send our CSRF token, so this URI is excluded from CSRF in config.php;
	 * requests are authenticated by the HMAC signature instead.
	 */
	public function webhook()
	{
		$body = $this->input->raw_input_stream;
		$signature = (string) $this->input->get_request_header('X-FeeLedger-Signature');
		$expected = hash_hmac('sha256', $body, $this->config->item('webhook_secret'));

		if ($signature === '' OR ! hash_equals($expected, $signature))
		{
			$this->output->set_status_header(401)->set_output('invalid signature');
			return;
		}

		$event = json_decode($body, TRUE);
		if ( ! is_array($event) OR (isset($event['event']) ? $event['event'] : '') !== 'payment.captured')
		{
			$this->output->set_output('ignored');
			return;
		}

		$p = $event['payment'];
		if ($this->payment_model->exists_ref($p['id']))
		{
			$this->output->set_output('duplicate');
			return;
		}

		$student = $this->student_model->get($p['notes']['student_id']);
		if ( ! $student)
		{
			$this->output->set_status_header(422)->set_output('unknown student');
			return;
		}

		$this->payment_model->record_online(array(
			'student_id'  => $student->id,
			'batch_id'    => $student->batch_id,
			'amount'      => $p['amount'] / 100,   // the gateway sends paise
			'gateway_ref' => $p['id'],
		));
		$this->installment_model->allocate($student->id);

		$this->output->set_output('ok');
	}
}
