<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Form_Field_Array extends Form_Field
{
	protected $_input_template = 'form/field/array';
	
	protected $_fields = array();
	protected $_rules = array();
	protected $_labels = array();
	
	/**
	 * @var Validate
	 */
	protected $_validate = NULL;
	
	public function __construct($field)
	{
		parent::__construct($field);
		if(empty($this->_value))
		{
			$this->_value = serialize(array());
		}
		
		$this->_validate = Validate::factory(array());
		
		foreach(Arr::get($field, 'fields', array()) as $name => $cfg)
		{
			if(is_null($cfg))
			{
				$cfg = 'text';
			}
			if( ! is_array($cfg))
			{
				$cfg = array(
					'type' => $cfg
				);
			}
			
			$this->_fields[$name] = Extasy_Form_Field::factory($cfg);
			$this->_fields[$name]->set_name($name.'_add_element');
			$this->_rules[$name] = Arr::path($field, 'rules.'.$name, array());
			$this->_labels[$name] = Arr::path($field, 'labels.'.$name, Inflector::humanize($name));
			
			$this->_validate->rules($name, $this->_rules[$name]);
			$this->_validate->label($name, $this->_labels[$name]);
		}
	}
	
	protected function input_fields()
	{
		$fields = parent::input_fields();
		
		foreach($this->_fields as $name => $field)
		{
			$this->_fields[$name]->set_attribute('class', $fields['name'].'_add_element');
		}
		
		$fields['value'] = unserialize($fields['value']);
		$fields['validate'] = $this->_validate;
		$fields['labels'] = $this->_labels;
		$fields['fields'] = $this->_fields;
		
		return $fields;
	}
	
	public function prepare_to_populate($value)
	{
		unset($value['default']);
		return serialize($value);
	}
}