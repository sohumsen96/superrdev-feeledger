<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Batch_model extends CI_Model {

	public function all()
	{
		return $this->db->query('SELECT * FROM batches ORDER BY start_date')->result();
	}

	public function get($id)
	{
		return $this->db->get_where('batches', array('id' => (int) $id))->row();
	}
}
