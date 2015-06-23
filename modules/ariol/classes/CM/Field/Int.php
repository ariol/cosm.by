<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Field_Int extends CM_Field
{
	protected $_value_class = 'CM_Value_Int';

	protected function validate()
	{
		if ( ! $this->get_value()->is_valid())
		{
			$this->set_error('Неверный формат числа');
		}
	}

	public function get_type_name()
	{
		return 'Целое число';
	}
}