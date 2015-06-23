<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Form_Field_Select_ORM_Simple extends Form_Field_Select_ORM
{
	public function get_selected_value()
	{
		return $this->_value;
	}
	
	public function prepare_to_populate($value)
	{
		return $value;
	}
}