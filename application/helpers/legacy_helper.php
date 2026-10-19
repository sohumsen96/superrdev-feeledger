<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * Helpers carried over from the old FeeLedger 1.x codebase.
 */

if ( ! function_exists('format_inr'))
{
	/**
	 * Format a rupee amount with Indian digit grouping, e.g. 1234567.5 -> "₹12,34,567.50"
	 */
	function format_inr($amount)
	{
		$amount = (float) $amount;
		$negative = $amount < 0;
		$parts = explode('.', number_format(abs($amount), 2, '.', ''));
		$whole = $parts[0];
		$last3 = substr($whole, -3);
		$rest = substr($whole, 0, -3);
		if ($rest !== '' && $rest !== FALSE)
		{
			$rest = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest);
			$whole = $rest . ',' . $last3;
		}
		return ($negative ? '-' : '') . '₹' . $whole . '.' . $parts[1];
	}
}

if ( ! function_exists('display_date'))
{
	function display_date($date)
	{
		return $date ? date('d M Y', strtotime($date)) : '';
	}
}
