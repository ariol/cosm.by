<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Fieldschema implements CM_Fieldschema_Interface
{
	protected $_fields = array();

	public function set_field($name, CM_Field $field, $position = NULL)
	{
		$this->_fields[$name] = $field;
		$this->_fields[$name]->set_name($name);

		if ( ! is_null($position))
		{
			$field->set_position($position);
		}

		return $this;
	}

	/**
	 * @param string $name
	 * @return CM_Field
	 */
	public function get_field($name)
	{
		$this->assert_has_field($name);

		return $this->_fields[$name];
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function has_field($name)
	{
		return isset($this->_fields[$name]);
	}

	private function sort_fields()
	{
		uasort($this->_fields, create_function(
			'$field1, $field2',
			'return $field1->get_position() <= $field2->get_position() ? -1 : 1;'
		));
	}

	public function remove_field($name)
	{
		$this->assert_has_field($name);
		unset($this->_fields[$name]);
	}

	/**
	 * Создает и возвращает новую схему на основе текущей и еще одной.
	 * Если какое-то имя поля встречается и в текущей, и в другой схеме -
	 * поле в текущей будет перезаписано.
	 *
	 * @param CM_Fieldschema $1
	 * @param CM_Fieldschema $2
	 * @return CM_Fieldschema $new_schema
	 */
	static public function merge(CM_Fieldschema $schema1, CM_Fieldschema $schema2)
	{
		$new_schema = new CM_Fieldschema;

		foreach (array($schema1, $schema2) as $schema)
		{
			foreach ($schema->get_field_names() as $name)
			{
				$new_schema->set_field($name, $schema->get_field($name));
			}
		}

		return $new_schema;
	}

	public function get_field_names()
	{
		$this->sort_fields();
		return array_keys($this->_fields);
	}

	private function assert_has_field($name)
	{
		if ( ! $this->has_field($name))
		{
			throw new CM_Fieldschema_Exception('Unknown field '.$name);
		}
	}

	public function set_values(array $values)
	{
		foreach ($values as $name => $value)
		{
			$this->get_field($name)->set_value($value);
		}
	}

	public function get_values()
	{
		$values = array();
		foreach ($this->get_field_names() as $name)
		{
			$values[$name] = $this->get_field($name)->get_value();
		}
		return $values;
	}

	public function __clone()
	{
		foreach ($this->_fields as $name => $field)
		{
			$this->_fields[$name] = clone $field;
		}
	}
}