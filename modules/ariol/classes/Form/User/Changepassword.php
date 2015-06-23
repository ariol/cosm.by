<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Form_User_ChangePassword extends CM_Form_Abstract
{
	protected function init()
	{
		$this->add_plugin(new CM_Form_Plugin_ORM(array('password'), array(), Model_User::get_password_validation()));

		$this->set_field('password', new CM_Field_Password(), 10);
		$this->set_field('password_confirm', new CM_Field_Password(), 20);
	}
}