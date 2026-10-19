<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

	public function index()
	{
		$batches = $this->batch_model->all();
		$batch_id = (int) $this->input->get('batch');
		if ( ! $batch_id && $batches)
		{
			$batch_id = (int) $batches[0]->id;
		}
		$batch = $this->batch_model->get($batch_id);
		if ( ! $batch)
		{
			show_404();
		}

		$students = $this->student_model->count_in_batch($batch_id);
		$overdue = $this->installment_model->overdue_count($batch_id);
		$recent = $this->payment_model->recent($batch_id);
		$names = $this->student_model->names(array_column($recent, 'student_id'));

		$this->render('dashboard/index', array(
			'title'    => 'Dashboard',
			'batches'  => $batches,
			'batch'    => $batch,
			'students' => $students,
			'overdue'  => $overdue,
			'recent'   => $recent,
			'names'    => $names,
		));
	}
}
