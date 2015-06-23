<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

interface CM_Value_Array_Interface extends CM_Value_Interface
{
	public function get_values();
	
	public function get_field_type();
}