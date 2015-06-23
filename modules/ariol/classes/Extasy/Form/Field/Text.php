<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Form_Field_Text extends Form_Field
{
	protected function _input()
	{
		return Form::input($this->get_name(), $this->_value, $this->_attributes);
	}
	
	protected function _ro_input()
	{
		return $this->_value;
	}
}