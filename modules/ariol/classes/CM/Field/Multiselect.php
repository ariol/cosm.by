<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Field_Multiselect extends CM_Field_Array
{
	private $_options = array();
	
	public function __construct(array $options = array())
	{
		parent::__construct(new CM_Field_Select());
		$this->set_options($options);
	}
	
	public function set_options(array $options = array())
	{
		$this->_options = $options;
		$this->get_value_field()->set_options($options);
	}
	
	public function get_options()
	{
		return $this->_options;
	}
	
	public function get_submitted_value()
	{
		$value = parent::get_submitted_value();
		$values = unserialize($value->get_raw());
		
		foreach ($values as $index => $value)
		{
			if ( ! array_key_exists($value, $this->_options))
			{
				unset($values[$index]);
			}
		}
		return $this->create_raw_value(serialize($values));
	}
	
	public function render()
	{
		return View::factory('cm/field/multiselect', array(
			'name' => $this->get_name(),
			'options' => $this->get_options(),
			'value' => $this->get_value()
		));
	}
}