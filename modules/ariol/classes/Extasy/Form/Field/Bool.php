<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Form_Field_Bool extends Form_Field
{
	protected function _input()
	{
		return 
			Form::hidden($this->get_name(), '0').'<div class="check-select">'.
			Form::checkbox($this->get_name(), '1', (bool)$this->_value, $this->_attributes).'</div>';
	}
	
	protected function _ro_input()
	{
		return (bool)$this->_value ? 'YES' : 'NO';
	}
}