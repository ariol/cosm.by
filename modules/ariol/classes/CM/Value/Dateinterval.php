<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Value_DateInterval implements CM_Value_Interface
{
	private $_date_from = NULL;
	private $_date_to = NULL;

	public function __construct($raw_value = NULL)
	{
		if ($raw_value == '')
		{
			$raw_value = NULL;
		}

		if (is_null($raw_value))
		{
			$this->_date_from = new CM_Value_Date();
			$this->_date_to = new CM_Value_Date();
		}
		else
		{
			$value = explode(' ', $raw_value);
			$this->_date_from = new CM_Value_Date($value[0]);
			$this->_date_to = new CM_Value_Date($value[1]);
		}
	}

	public function get_raw()
	{
		return $this->_date_from->get_raw().' '.$this->_date_to->get_raw();
	}

	public function is_valid()
	{
		return $this->_date_from->is_valid() AND $this->_date_to->is_valid();
	}

	public function __toString()
	{
		return (string) $this->get_raw();
	}

	public function get_date_from()
	{
		return $this->_date_from;
	}

	public function get_date_to()
	{
		return $this->_date_to;
	}
}