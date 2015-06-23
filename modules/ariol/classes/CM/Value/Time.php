<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Value_Time implements CM_Value_Interface
{
	private $_time = NULL;

	public function __construct($raw_value = NULL)
	{
		if ($raw_value == '')
		{
			$raw_value = NULL;
		}
		$this->_time = $raw_value;
	}

	public function get_raw()
	{
		return $this->_time;
	}

	public function is_valid()
	{
		return is_null($this->_time) OR (bool) strtotime('2010-01-01 '.$this->_time.':00');
	}

	protected function get_date_param($param)
	{
		if ( ! $time = strtotime('2010-01-01 '.$this->_time.':00'))
		{
			return NULL;
		}
		return date($param, $time);
	}

	public function get_hour()
	{
		return $this->get_date_param('H');
	}

	public function get_minute()
	{
		return $this->get_date_param('i');
	}

	public function __toString()
	{
		return (string)$this->get_raw();
	}
}