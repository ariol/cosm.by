<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Form_Plugin_ORM_Multiselect extends CM_Form_Plugin_ORM_Abstract
{
	private $_field_name = NULL;
	private $_value_model = NULL;
	private $_parent_field = NULL;
	
	public function __construct($field_name, ORM $value_model, $parent_field)
	{
		$this->_field_name = $field_name;
		$this->_value_model = clone $value_model;
		$this->_parent_field = $parent_field;
	}
	
	protected function get_db_values()
	{
		return $this->get_model()->{$this->_field_name}->order_by('index')->find_all();
	}
	
	public function construct_form(CM_Form_Abstract $form, $param)
	{
		$this->set_model($param);
		
		$values = array();
		foreach ($this->get_db_values() as $db_value)
		{
			$values[$db_value->index] = $db_value->value;
		}
		
		$form->get_field($this->_field_name)->set_raw_value($values);
	}
	
	public function populate(CM_Form_Abstract $form)
	{
		
	}
	
	public function after_submit(CM_Form_Abstract $form)
	{
		$values = unserialize($form->get_field($this->_field_name)->get_value()->get_raw());

		foreach ($this->get_db_values() as $db_value)
		{
			if ( ! isset($values[$db_value->index]))
			{
				$db_value->delete();
			}
			else if (is_null($values[$db_value->index]))
			{
				unset($values[$db_value->index]);
				$db_value->delete();
			}
			else
			{
				$db_value->value = $values[$db_value->index];
				$db_value->save();
				unset($values[$db_value->index]);
			}
		}
		
		foreach ($values as $index => $value)
		{
			if (is_null($value)) continue;
			
			$db_value = clone $this->_value_model;
			$db_value->index = $index;
			$db_value->value = $value;
			$db_value->{$this->_parent_field} = $this->get_model();
			$db_value->save();
		}
	}
}