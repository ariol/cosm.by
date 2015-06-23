<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

abstract class CM_Template
{
	static public function validate_field_name($new_name, CM_Template $template, $old_name = NULL)
	{
		if ( ! is_null($old_name) AND ($new_name == $old_name))
		{
			return TRUE;
		}

		if ($template->has_field($new_name))
		{
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Родительский шаблон
	 * @var CM_Template
	 */
	protected $_parent = NULL;

	/**
	 * @var CM_Fieldschema
	 */
	protected $_fieldschema = NULL;

	public function get_parent()
	{
		return $this->_parent;
	}

	/**
	 * Возвращает _копию_ схемы, соединенную с родительской
	 *
	 * @return CM_Fieldschema
	 */
	public function get_fieldschema()
	{
		if ( ! is_null($this->_parent))
		{
			return CM_Fieldschema::merge($this->_parent->get_fieldschema(), $this->_fieldschema);
		}
		return clone $this->_fieldschema;
	}

	public function is_changeable()
	{
		return TRUE;
	}

	public function is_field_ro($name)
	{
		if ( ! $this->has_field($name))
		{
			throw new CM_Template_Exception('Unknown field '.$name);
		}
		if ( ! is_null($this->_parent) AND $this->_parent->has_field($name))
		{
			return TRUE;
		}
		return FALSE;
	}

	public function add_field($name, CM_Field $field, $position = 0)
	{
		if ( ! self::validate_field_name($name, $this))
		{
			throw new CM_Template_Exception('Can not add field - invalid name: '.$name);
		}

		if ($this->has_field($name))
		{
			throw new CM_Template_Exception('Can not add already existed field '.$name);
		}
		$this->_fieldschema->set_field($name, $field);
		$this->_fieldschema->get_field($name)->set_position($position);
	}

	public function set_field($name, CM_Field $field)
	{
		if ( ! $this->has_field($name))
		{
			throw new CM_Template_Exception('Can not set unknown field '.$name);
		}

		if ($this->is_field_ro($name))
		{
			throw new CM_Template_Exception('Can not modify read-only field '.$name);
		}

		if ( ! $this->is_changeable())
		{
			throw new CM_Template_Exception('Template not changeable');
		}

		$this->_fieldschema->set_field($name, $field);
	}

	public function set_field_position($name, $position)
	{
		if ($this->is_field_ro($name))
		{
			throw new CM_Template_Exception('Can not modify read-only field '.$name);
		}
		$this->_fieldschema->get_field($name)->set_position($position);
	}

	public function set_field_name($old_name, $new_name)
	{
		if ($this->is_field_ro($old_name))
		{
			throw new CM_Template_Exception('Can not remove read-only field '.$name);
		}

		if ($old_name == $new_name)
		{
			return;
		}

		if ( ! self::validate_field_name($new_name, $this, $old_name))
		{
			throw new CM_Template_Exception('Can not change name from '.$old_name.' to '.$new_name);
		}

		if ( ! $this->is_changeable())
		{
			throw new CM_Template_Exception('Template not changeable');
		}

		$field = $this->_fieldschema->get_field($old_name);

		$this->_fieldschema->remove_field($old_name);
		$this->_fieldschema->set_field($new_name, $field);
	}

	public function remove_field($name)
	{
		if ($this->is_field_ro($name))
		{
			throw new CM_Template_Exception('Can not remove read-only field '.$name);
		}
		if ( ! $this->is_changeable())
		{
			throw new CM_Template_Exception('Template not changeable');
		}
		$this->_fieldschema->remove_field($name);
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function has_field($name, $check_parent = TRUE)
	{
		if ($check_parent AND ! is_null($this->_parent))
		{
			return $this->_fieldschema->has_field($name) OR $this->_parent->has_field($name);
		}
		return $this->_fieldschema->has_field($name);
	}
}