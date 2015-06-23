<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Value_Boolean implements CM_Value_Interface
{
	private $_value = NULL;

	public function __construct($raw_value = NULL)
	{
		$this->_value = (bool) $raw_value;
	}

	public function get_raw()
	{
		return $this->_value;
	}

	public function is_valid()
	{
		return TRUE;
	}

	public function __toString()
	{
		return (int) $this->get_raw();
	}
}