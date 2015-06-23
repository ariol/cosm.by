<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Field_String extends CM_Field
{
	protected $_value_class = 'CM_Value_String';

	protected $_max_len = 256;

	public function get_type_name()
	{
		return 'Строка';
	}

	protected function validate()
	{
		if ( ! is_null($this->_max_len) AND UTF8::strlen($this->get_value()->get_raw()) > $this->_max_len)
		{
			$this->set_error("Длина данных не должна превышать {$this->_max_len} символов");
		}
	}
}