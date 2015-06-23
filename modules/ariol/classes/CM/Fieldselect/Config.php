<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Fieldselect_Config
{
	/**
	 * @param string $type
	 * @return CM_Fieldselect_Config
	 */
	static public function factory($type)
	{
		$class = 'CM_Fieldselect_Config_'.ucfirst($type);
		$config = new CM_Fieldselect_Config($type);
		if (class_exists($class, TRUE))
		{
			$config = new $class($type);
		}
		return $config;
	}

	/**
	 * @var CM_Fieldschema
	 */
	private $_fieldschema = NULL;

	private $_type = NULL;

	final private function __construct($type)
	{
		$this->_fieldschema = new CM_Fieldschema;
		$this->_type = $type;
		$this->init();
	}

	/**
	 * @return CM_Fieldschema
	 */
	final public function fieldschema()
	{
		return $this->_fieldschema;
	}

	public function set_name($name)
	{
		foreach ($this->fieldschema()->get_field_names() as $field_name)
		{
			$new_name = $name.'_config_'.$this->_type.'_'.$field_name;
			$this->fieldschema()->get_field($field_name)->set_name($new_name);
		}
	}

	public function set_value_source($source)
	{
		foreach ($this->fieldschema()->get_field_names() as $field_name)
		{
			$this->fieldschema()->get_field($field_name)->set_value_source($source);
		}
	}

	public function init() {}

	public function set_value(CM_Value_Fieldselect $value) {}

	public function apply_submitted_settings(CM_Field $field) {}
}