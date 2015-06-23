<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Value_Int implements CM_Value_Interface
{
	private $_value = NULL;

	public function __construct($raw_value = NULL)
	{
		if ($raw_value == '')
		{
			$raw_value = NULL;
		}
		$this->_value = $raw_value;
	}

	public function get_raw()
	{
		return $this->_value;
	}

	public function is_valid()
	{
		return is_null($this->_value) OR preg_match('#^\-?[0-9]+$#si', $this->_value);
	}

	public function __toString()
	{
		return (string) $this->get_raw();
	}
}