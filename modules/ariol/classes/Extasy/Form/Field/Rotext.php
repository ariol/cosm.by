<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Form_Field_Rotext extends Form_Field
{
	protected function _input()
	{
		return $this->_ro_input().form::hidden($this->get_name(), $this->_value);
	}

	protected function _ro_input()
	{
		return $this->_value;
	}
}