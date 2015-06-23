<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Field_Time extends CM_Field
{
	protected $_value_class = 'CM_Value_Time';

	public function render()
	{
		return View::factory('cm/field/time', array(
			'name' => $this->get_name(),
			'value' => $this->get_value()->get_raw()
		));
	}

	protected function validate()
	{
		if ( ! $this->get_value()->is_valid())
		{
			$this->set_error("Неверный формат времени (ЧЧ:ММ)");
		}
	}

	public function get_type_name()
	{
		return 'Время';
	}
}