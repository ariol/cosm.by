<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Form_Field_Roselect extends Form_Field_Select
{
	protected function _input()
	{
		return $this->_ro_input().form::hidden($this->get_name(), $this->get_selected_value());
	}
}