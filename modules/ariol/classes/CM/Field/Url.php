<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Field_Url extends CM_Field_String
{
	protected $_value_class = 'CM_Value_String';
		protected $_max_len = 255;

	public function get_type_name()
	{
		return 'Урл';
	}

	protected function validate()
	{
		if ( ! is_null($this->_max_len) AND UTF8::strlen($this->get_value()->get_raw()) > $this->_max_len)
		{
			$this->set_error("Длина данных не должна превышать {$this->_max_len} символов");
		}
	}
}