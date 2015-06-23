<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Form_Plugin_Auth_Admin extends CM_Form_Plugin_Abstract
{
	public function construct_form(CM_Form_Abstract $form, $user)
	{
		if ($user->id == Auth::instance()->get_user()->id)
		{
			$form->get_field('roles')->set_role_disabled('admin');
		}

		if ( ! Auth::instance()->logged_in('admin'))
		{
			$form->get_field('roles')->set_role_disabled('admin');
		}
	}
}