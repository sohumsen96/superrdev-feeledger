<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Student_model extends CI_Model {

	public function search($q)
	{
		$sql = "SELECT s.*, b.name AS batch_name FROM students s JOIN batches b ON b.id = s.batch_id
		        WHERE s.name LIKE '%" . $q . "%' OR s.phone LIKE '%" . $q . "%' ORDER BY s.name";
		return $this->db->query($sql)->result();
	}

	/**
	 * All students with their total fee and what they have paid so far.
	 */
	public function list_with_balances()
	{
		$this->db->select("s.id, s.name, s.phone, b.name AS batch_name,
		    GROUP_CONCAT(DISTINCT g.phone SEPARATOR ', ') AS guardian_phones,
		    (SELECT COALESCE(SUM(i.amount), 0) FROM installments i WHERE i.student_id = s.id) AS total_fee,
		    COALESCE(SUM(p.amount), 0) AS total_paid", FALSE)
			->from('students s')
			->join('batches b', 'b.id = s.batch_id')
			->join('guardians g', 'g.student_id = s.id', 'left')
			->join('payments p', "p.student_id = s.id AND p.status = 'captured'", 'left')
			->group_by('s.id')
			->order_by('s.name');
		return $this->db->get()->result();
	}

	public function get($id)
	{
		return $this->db->select('s.*, b.name AS batch_name')
			->from('students s')
			->join('batches b', 'b.id = s.batch_id')
			->where('s.id', (int) $id)
			->get()->row();
	}

	public function names(array $ids)
	{
		if (empty($ids))
		{
			return array();
		}
		$rows = $this->db->select('id, name')->where_in('id', array_map('intval', $ids))->get('students')->result();
		return array_column($rows, 'name', 'id');
	}

	public function count_in_batch($batch_id)
	{
		return $this->db->where('batch_id', (int) $batch_id)->count_all_results('students');
	}

	public function guardians($student_id)
	{
		return $this->db->get_where('guardians', array('student_id' => (int) $student_id))->result();
	}

	/**
	 * Enrol a student into a batch and create their installment schedule.
	 */
	public function enrol(array $data)
	{
		$batch = $this->batch_model->get($data['batch_id']);

		$this->db->trans_start();
		$this->db->insert('students', array(
			'batch_id'  => $batch->id,
			'name'      => $data['name'],
			'phone'     => $data['phone'],
			'email'     => $data['email'] !== '' ? $data['email'] : NULL,
			'joined_on' => date('Y-m-d'),
			'discount'  => $data['discount'],
		));
		$student_id = $this->db->insert_id();

		if ( ! empty($data['guardian_name']))
		{
			$this->db->insert('guardians', array(
				'student_id' => $student_id,
				'name'       => $data['guardian_name'],
				'relation'   => $data['guardian_relation'],
				'phone'      => $data['guardian_phone'],
			));
		}

		$this->installment_model->generate_schedule($student_id, $batch, $batch->installments, $batch->monthly_fee);
		$this->db->trans_complete();

		return $student_id;
	}
}
