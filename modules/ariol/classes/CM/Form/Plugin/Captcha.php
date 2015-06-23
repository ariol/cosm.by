<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Form_Plugin_Captcha extends CM_Form_Plugin_Abstract
{
	private $_field_name = NULL;
	
	public function __construct($field_name)
	{
		$this->_field_name = $field_name;
	}
	
	public function validate(CM_Form_Abstract $form)
	{
		if ( ! $form->get_field($this->_field_name)->is_valid())
		{
			$form->get_field($this->_field_name)->set_error('Неверный код');
			return FALSE;
		}
		return TRUE;
	}
}