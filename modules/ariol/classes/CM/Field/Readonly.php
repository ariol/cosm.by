<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Field_Readonly extends CM_Field
{
	protected $_value_class = 'CM_Value_Null';

	private $_field = NULL;

	public function set_field(CM_Field $field)
	{
		$this->_field = $field;
	}

	public function get_field()
	{
		return $this->_field;
	}

	public function render()
	{
		return $this->render_value();
	}

	public function render_value()
	{
		return $this->get_field()->render_value();
	}

	public function get_label()
	{
		return $this->get_field()->get_label();
	}

	public function set_label($label)
	{
		$this->get_field()->set_label($label);
	}

	public function get_position()
	{
		return $this->get_field()->get_position();
	}

	public function set_position($position)
	{
		$this->get_field()->set_position($position);
	}
}