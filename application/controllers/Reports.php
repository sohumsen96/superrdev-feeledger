<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reports extends MY_Controller {

	public function collection()
	{
		$month = (string) $this->input->get('month');
		if ( ! preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $month))
		{
			$month = date('Y-m');
		}

		$this->render('reports/collection', array(
			'title'     => 'Monthly collection',
			'month'     => $month,
			'total'     => $this->payment_model->month_total($month),
			'breakdown' => $this->payment_model->month_breakdown($month),
		));
	}
}
