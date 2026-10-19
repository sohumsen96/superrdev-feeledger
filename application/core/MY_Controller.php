<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('batch_model', 'student_model', 'installment_model', 'payment_model'));
	}

	protected function render($view, array $data = array())
	{
		$data['content'] = $this->load->view($view, $data, TRUE);
		$data['centre_name'] = $this->config->item('centre_name');
		$this->load->view('layout', $data);
	}
}
