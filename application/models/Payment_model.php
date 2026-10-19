<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment_model extends CI_Model {

	public function get($id)
	{
		return $this->db->select('p.*, s.name AS student_name, b.name AS batch_name')
			->from('payments p')
			->join('students s', 's.id = p.student_id')
			->join('batches b', 'b.id = p.batch_id')
			->where('p.id', (int) $id)
			->get()->row();
	}

	public function for_student($student_id)
	{
		return $this->db->order_by('paid_at', 'DESC')->get_where('payments', array('student_id' => (int) $student_id))->result();
	}

	/**
	 * Latest captured payments for a batch.
	 */
	public function recent($batch_id, $limit = 10)
	{
		return $this->db->where('batch_id', (int) $batch_id)
			->where('status', 'captured')
			->order_by('paid_at', 'DESC')
			->get('payments', $limit)
			->result();
	}

	/**
	 * Payment taken at the front desk.
	 */
	public function record_counter(array $data)
	{
		$this->db->set('paid_at', 'NOW()', FALSE);
		$this->db->insert('payments', array(
			'student_id' => $data['student_id'],
			'batch_id'   => $data['batch_id'],
			'amount'     => $data['amount'],
			'mode'       => 'counter',
			'status'     => 'captured',
			'remarks'    => $data['remarks'],
		));
		return $this->db->insert_id();
	}

	/**
	 * Payment confirmed by the gateway webhook.
	 */
	public function record_online(array $data)
	{
		$this->db->insert('payments', array(
			'student_id'  => $data['student_id'],
			'batch_id'    => $data['batch_id'],
			'amount'      => $data['amount'],
			'mode'        => 'online',
			'status'      => 'captured',
			'gateway_ref' => $data['gateway_ref'],
			'paid_at'     => date('Y-m-d H:i:s'),
		));
		return $this->db->insert_id();
	}

	public function exists_ref($gateway_ref)
	{
		return $this->db->where('gateway_ref', $gateway_ref)->count_all_results('payments') > 0;
	}

	public function update($id, array $data)
	{
		$this->db->where('id', (int) $id)->update('payments', $data);
	}

	/**
	 * First and last day of a month, e.g. '2026-09' => ['2026-09-01', '2026-09-30'].
	 */
	public function month_range($month)
	{
		$from = $month . '-01';
		$to = date('Y-m-t', strtotime($from));
		return array($from, $to);
	}

	public function month_total($month)
	{
		list($from, $to) = $this->month_range($month);
		$row = $this->db->select_sum('amount')
			->where('status', 'captured')
			->where('paid_at >=', $from)
			->where('paid_at <=', $to)
			->get('payments')->row();
		return $row->amount === NULL ? '0.00' : $row->amount;
	}

	public function month_breakdown($month)
	{
		list($from, $to) = $this->month_range($month);
		$sql = "SELECT b.name AS batch_name, p.mode, COUNT(*) AS payments, SUM(p.amount) AS amount
		        FROM payments p JOIN batches b ON b.id = p.batch_id
		        WHERE p.status = 'captured' AND p.paid_at >= ? AND p.paid_at <= ?
		        GROUP BY b.name, p.mode ORDER BY b.name, p.mode";
		return $this->db->query($sql, array($from, $to))->result();
	}
}
