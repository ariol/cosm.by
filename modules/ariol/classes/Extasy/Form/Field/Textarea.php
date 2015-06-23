<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Form_Field_Textarea extends Form_Field
{
	protected $_attributes = array(
		'style' => 'width:99%;height:150px;'
	);
	
	protected function _input()
	{
		return Form::textarea($this->get_name(), $this->_value, $this->_attributes);
	}
	
	protected function _ro_input()
	{
		return $this->_value;
	}
}