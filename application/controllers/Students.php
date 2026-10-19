<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Students extends MY_Controller {

	public function index()
	{
		$q = trim((string) $this->input->get('q'));

		if ($q !== '')
		{
			$this->render('students/search', array('title' => 'Students', 'q' => $q, 'students' => $this->student_model->search($q)));
			return;
		}

		$students = $this->student_model->list_with_balances();
		foreach ($students as $s)
		{
			$s->balance = $s->total_fee - $s->total_paid;
		}
		$this->render('students/index', array('title' => 'Students', 'q' => '', 'students' => $students));
	}

	public function view($id)
	{
		$student = $this->student_model->get($id);
		if ( ! $student)
		{
			show_404();
		}

		$today = date('Y-m-d');
		$installments = $this->installment_model->for_student($id);
		foreach ($installments as $i)
		{
			$i->late_fee = ($i->status === 'pending') ? $this->installment_model->late_fee($i->due_date, $today) : 0;
		}

		$this->render('students/view', array(
			'title'        => $student->name,
			'student'      => $student,
			'guardians'    => $this->student_model->guardians($id),
			'installments' => $installments,
			'payments'     => $this->payment_model->for_student($id),
		));
	}

	public function enrol()
	{
		$this->form_validation->set_rules('name', 'Name', 'required|trim|max_length[120]');
		$this->form_validation->set_rules('phone', 'Phone', 'required|trim|regex_match[/^[0-9+ ]{10,15}$/]');
		$this->form_validation->set_rules('email', 'Email', 'trim|valid_email');
		$this->form_validation->set_rules('batch_id', 'Batch', 'required|integer');

		if ($this->form_validation->run() === FALSE)
		{
			$this->render('students/enrol', array('title' => 'Enrol student', 'batches' => $this->batch_model->all()));
			return;
		}

		$student_id = $this->student_model->enrol(array(
			'name'              => $this->input->post('name'),
			'phone'             => $this->input->post('phone'),
			'email'             => (string) $this->input->post('email'),
			'batch_id'          => $this->input->post('batch_id'),
			'discount'          => $this->input->post('discount'),
			'guardian_name'     => trim((string) $this->input->post('guardian_name')),
			'guardian_relation' => $this->input->post('guardian_relation'),
			'guardian_phone'    => trim((string) $this->input->post('guardian_phone')),
		));

		$this->session->set_flashdata('message', 'Student enrolled.');
		redirect('students/view/' . $student_id);
	}
}
