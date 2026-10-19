<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Installment_model extends CI_Model {

	protected $paused_batch_ids = array();

	public function __construct()
	{
		parent::__construct();
		$rows = $this->db->select('id')->where('status', 'paused')->get('batches')->result_array();
		$this->paused_batch_ids = array_map('intval', array_column($rows, 'id'));
	}

	/**
	 * Monthly installments starting on the batch start date.
	 */
	public function generate_schedule($student_id, $batch, $count, $amount)
	{
		for ($i = 0; $i < $count; $i++)
		{
			$due = date('Y-m-d', strtotime("+$i month", strtotime($batch->start_date)));
			$this->db->insert('installments', array(
				'student_id' => $student_id,
				'batch_id'   => $batch->id,
				'seq'        => $i + 1,
				'amount'     => $amount,
				'due_date'   => $due,
				'status'     => 'pending',
			));
		}
	}

	public function for_student($student_id)
	{
		return $this->db->order_by('seq')->get_where('installments', array('student_id' => (int) $student_id))->result();
	}

	/**
	 * Late fee for an installment that is still unpaid on $as_of. See docs/FEE_POLICY.md.
	 */
	public function late_fee($due_date, $as_of)
	{
		$days_late = (int) floor((strtotime($as_of) - strtotime($due_date)) / 86400);
		if ($days_late <= 0)
		{
			return 0;
		}
		return (int) floor($days_late / 7) * (int) $this->config->item('late_fee_per_week');
	}

	/**
	 * Mark installments paid, oldest first, as far as the student's captured payments cover them.
	 */
	public function allocate($student_id)
	{
		$paid = (float) $this->db->select_sum('amount')
			->where(array('student_id' => (int) $student_id, 'status' => 'captured'))
			->get('payments')->row()->amount;

		$covered = 0;
		foreach ($this->for_student($student_id) as $i)
		{
			$covered += $i->amount;
			$status = ($covered <= $paid) ? 'paid' : 'pending';
			if ($status !== $i->status)
			{
				$this->db->where('id', $i->id)->update('installments', array('status' => $status));
			}
		}
	}

	/**
	 * Unpaid installments past their due date for one batch.
	 */
	public function overdue_count($batch_id)
	{
		$this->db->where('batch_id', $batch_id)->where('status', 'pending');

		// Paused batches don't build up overdue installments during the break (docs/FEE_POLICY.md)
		if ($this->is_paused($batch_id))
		{
			return 0;
		}

		return $this->db->where('due_date <', date('Y-m-d'))->count_all_results('installments');
	}

	public function is_paused($batch_id)
	{
		return in_array((int) $batch_id, $this->paused_batch_ids, TRUE);
	}
}
