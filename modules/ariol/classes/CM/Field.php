<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

abstract class CM_Field
{
	protected $_value_class = NULL;

	private $_name = NULL;
	/**
	 * @var CM_Content_Value_Interface
	 */
	private $_value = NULL;
	private $_value_source = array();
	private $_error = NULL;

	private $_label = NULL;

	private $_position = 0;
	
	private $_attributes = array();

	private $_disabled = FALSE;

	public function __construct()
	{
		$this->set_raw_value(NULL);
	}

	public function __wakeup()
	{
		$this->_error = NULL;
	}
	
	public function set_disabled($disabled = TRUE)
	{
		$this->_disabled = $disabled;
	}

	public function set_attributes(array $attributes = array())
	{
		foreach ($attributes as $key => $value)
		{
			$this->_attributes[$key] = $value;
		}
		return $this;
	}
	
	public function get_attributes()
	{
		$attributes = $this->_attributes;
		if ($this->_disabled)
		{
			$attributes += array(
				'disabled' => 'disabled'
			);
		}
		return $attributes;
	}
	
	public function set_label($label)
	{
		$this->_label = $label;
	}

	public function get_label()
	{
		return $this->_label;
	}

	public function set_position($position)
	{
		$this->_position = (int) $position;
	}

	public function get_position()
	{
		return $this->_position;
	}

	public function get_type()
	{
		return str_replace('cm_field_', '', strtolower(get_class($this)));
	}

	public function get_type_name()
	{
		return $this->get_type();
	}

	public function set_value_source($source)
	{
		$this->_value_source = $source;
	}

	public function get_value_source()
	{
		return $this->_value_source;
	}

	public function get_name()
	{
		return $this->_name;
	}

	public function set_name($name)
	{
		$this->_name = $name;
	}

	public function get_submitted_value()
	{
		if ($this->_disabled)
		{
			return $this->get_value();
		}
		return $this->create_raw_value(arr::get($this->get_value_source(), $this->get_name()));
	}

	public function is_submitted()
	{
		$source = $this->get_value_source();
		return isset($source[$this->get_name()]);
	}

	public function render()
	{
		$attributes = $this->get_attributes();

		if (isset($attributes['class'])) {
			$attributes['class'] .= ' form-control';
		}
		else {
			$attributes['class'] = 'form-control';
		}

		return Form::input($this->get_name(), $this->get_value()->get_raw(), $attributes);
	}

	public function render_value()
	{
		return $this->get_value()->get_raw();
	}

	/**
	 * @return CM_Value_Interface
	 */
	public function get_value()
	{
		return clone $this->_value;
	}

	public function set_value(CM_Value_Interface $value)
	{
		$this->set_error(NULL);
		$this->_value = clone $value;
		$this->validate();
	}

	public function create_raw_value($raw_value = NULL)
	{
		if (is_null($this->_value_class))
		{
			throw new CM_Field_Exception('Value class not defined');
		}
		$class = $this->_value_class;
		return new $class($raw_value);
	}

	public function set_raw_value($raw_value = NULL)
	{
		$this->set_value($this->create_raw_value($raw_value));
	}

	protected function validate()
	{
	}

	public function is_valid()
	{
		return is_null($this->get_error());
	}

	public function get_error()
	{
		return $this->_error;
	}

	public function set_error($error)
	{
		$this->_error = $error;
	}
}