<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

abstract class CM_Field_Fieldselect extends CM_Field
{
	protected $_value_class = 'CM_Value_Fieldselect';

	/**
	 * @var CM_Field_Select
	 */
	protected $_type_field = NULL;

	protected $_allowed_fields = NULL;

	public function __construct()
	{
		$select_options = array();
		foreach ($this->get_allowed_fields() as $group_name => $field_array)
		{
			if (is_array($field_array))
			{
				$select_options[$group_name] = array();
				foreach ($field_array as $field)
				{
					$this->_allowed_fields[$field->get_type()] = $field;
					$select_options[$group_name][$field->get_type()] = $field->get_type_name();
				}
			}
			else
			{
				$this->_allowed_fields[$field_array->get_type()] = $field_array;
				$select_options[$field_array->get_type()] = $field_array->get_type_name();
			}
		}

		$type_field = new CM_Field_Select();
		$type_field->set_options($select_options);
		$type_field->set_label('Тип поля');
		$this->_type_field = $type_field;

		parent::__construct();
	}

	abstract protected function get_allowed_fields();

	public function set_value(CM_Value_Interface $value)
	{
		$this->_type_field->set_raw_value($value->get_field()->get_type());

		parent::set_value($value);
	}

	public function render()
	{
		$fieldschemas = array();
		$cur_type = (string) $this->_type_field->get_value();
		foreach ($this->_allowed_fields as $type => $field)
		{
			$config = CM_Fieldselect_Config::factory($type);
			$config->set_name($this->get_name());
			if ($type == $cur_type)
			{
				$config->set_value($this->get_value());
			}
			$fieldschemas[$type] = clone $config->fieldschema();
		}

		return View::factory('cm/field/fieldselect', array(
			'name' => $this->get_name(),
			'type_field' => $this->_type_field,
			'fieldschemas' => $fieldschemas
		));
	}

	public function render_value()
	{
		return $this->get_value()->get_field()->get_type_name();
	}

	public function is_submitted()
	{
		return $this->_type_field->is_submitted();
	}

	public function get_submitted_value()
	{
		$type = $this->_type_field->get_submitted_value();

		$field = arr::get($this->_allowed_fields, (string) $type);

		if ( ! $field)
		{
			throw new Exception('Unknow field '.$type->get_raw());
		}

		$config = CM_Fieldselect_Config::factory($type);
		$config->set_name($this->get_name());
		$config->set_value_source($this->get_value_source());

		$config->apply_submitted_settings($field);

		return $this->create_raw_value($field);
	}

	public function set_name($name)
	{
		$this->_type_field->set_name($name.'_type');
		parent::set_name($name);
	}

	public function set_value_source($source)
	{
		$this->_type_field->set_value_source($source);
		parent::set_value_source($source);
	}
}