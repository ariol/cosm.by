<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Value_Array implements CM_Value_Array_Interface
{
	private $_values = array();

	private $_value_field = NULL;

	public function __construct($raw_value = NULL)
	{
		if ($raw_value == '')
		{
			$raw_value = NULL;
		}

		if ($raw_value instanceof CM_Value_Array)
		{
			$raw_value = $raw_value->get_raw();
		}

		if (is_array($raw_value))
		{
			$raw_value = serialize($raw_value);
		}

		if (is_null($raw_value))
		{
			$raw_value = serialize(array());
		}

		$values = unserialize($raw_value);

		foreach ($values as $value)
		{
			if ($value instanceof CM_Value_Interface)
			{
				$value = $value->get_raw();
			}
			$this->_values[] = $value;
		}
	}

	public function set_value_field(CM_Field $value_field = NULL)
	{
		$this->_value_field = $value_field;
	}

	/**
	 * @return CM_Field
	 */
	public function get_value_field()
	{
		if (is_null($this->_value_field))
		{
			$this->_value_field = new CM_Field_String;
		}
		return $this->_value_field;
	}

	public function get_raw()
	{
		return serialize($this->_values);
	}

	public function get_values()
	{
		$values = array();
		$value_field = clone $this->get_value_field();
		foreach ($this->_values as $value)
		{
			$value_field->set_raw_value($value);
			$values[] = $value_field->get_value();
		}

		return $values;
	}

	public function get_field_type()
	{
		return $this->get_value_field()->get_type();
	}

	public function is_valid()
	{
		return TRUE;
	}

	public function __toString()
	{
		return var_export($this->get_raw());
	}
}