<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Value_String implements CM_Value_Interface
{
	private $_string = NULL;

	public function __construct($raw_value = NULL)
	{
		if ($raw_value instanceof CM_Value_Interface)
		{
			$raw_value = $raw_value->get_raw();
		}
		$this->_string = $raw_value;
	}

	public function get_raw()
	{
		return $this->_string;
	}

	public function is_valid()
	{
		return TRUE;
	}

	public function __toString()
	{
		return (string)$this->get_raw();
	}
}