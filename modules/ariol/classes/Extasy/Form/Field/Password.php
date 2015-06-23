<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Form_Field_Password extends Form_Field
{
	public function _input()
	{
		return Form::password($this->get_name(), NULL, $this->_attributes);
	}
	
	public function _ro_input()
	{
		return '';
	}
}