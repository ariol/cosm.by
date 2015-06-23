<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Field_Captcha extends CM_Field
{
	protected $_value_class = 'CM_Value_String';
	
	public function is_valid()
	{
		return Captcha::test($this->get_value()->get_raw());
	}
	
	public function render()
	{
		return View::factory('cm/field/captcha', array(
			'name' => $this->get_name()
		));
	}
}