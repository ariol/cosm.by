<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Form_Login_ChangePassword extends CM_Form_Abstract
{
	protected $_old_password = NULL;

	protected function construct_form($param)
	{
		$this->_old_password = $param->password;
	}

	protected function init()
	{
		$this->add_plugin(new CM_Form_Plugin_ORM(array('password'), array(), Model_User::get_password_validation()));

		$this->set_field('old_password', new CM_Field_Password(), 10);
		$this->get_field('old_password')->set_label('Старый пароль');

		$this->set_field('password', new CM_Field_Password(), 20);
		$this->set_field('password_confirm', new CM_Field_Password(), 30);
	}

	protected function validate()
	{
		$old_password = $this->get_field('old_password')->get_value()->get_raw();
		$old_password = Auth::instance()->hash($old_password);

		if ($old_password != $this->_old_password)
		{
			$this->set_error('old_password', 'Неверный старый пароль');
			return FALSE;
		}
		return TRUE;
	}
}