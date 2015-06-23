<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Value_Null implements CM_Value_Interface
{
	public function __construct($raw_value = NULL)
	{

	}

	public function is_valid()
	{
		return TRUE;
	}

	public function get_raw()
	{
		return NULL;
	}

	public function __toString()
	{
		return (string) $this->get_raw();
	}
}