<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Form_Field_Select extends Form_Field
{
	protected $_options = array();
	
	protected function __construct(array $field = array())
	{
		$this->_options = Arr::get($field, 'options', $this->_options);
		parent::__construct($field);
	}
	
	public function get_options()
	{
		return $this->_options;
	}
	
	public function get_selected_value()
	{
		return $this->_value;
	}
	
	protected function _input()
	{
		return Form::select($this->get_name(), $this->get_options(), $this->get_selected_value(), $this->_attributes);
	}
	
	protected function _ro_input()
	{
		return Arr::get($this->get_options(), $this->get_selected_value(), $this->get_selected_value());
	}
}