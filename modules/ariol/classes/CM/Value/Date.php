<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Value_Date implements CM_Value_Interface
{
	private $_date = NULL;

	public function __construct($raw_value = NULL)
	{
		if ($raw_value == '')
		{
			$raw_value = NULL;
		}
		$this->_date = $raw_value;
	}

	public function get_raw()
	{
		return $this->_date;
	}

	public function is_valid()
	{
		return is_null($this->_date) OR (bool) strtotime($this->_date.' 00:00:00');
	}

	protected function get_date_param($param)
	{
		if ( ! $time = strtotime($this->_date.' 00:00:00'))
		{
			return NULL;
		}
		return date($param, $time);
	}

	public function get_year()
	{
		return $this->get_date_param('Y');
	}

	public function get_month()
	{
		return $this->get_date_param('m');
	}

	public function get_day()
	{
		return $this->get_date_param('d');
	}

	public function __toString()
	{
		return (string) $this->get_raw();
	}
}