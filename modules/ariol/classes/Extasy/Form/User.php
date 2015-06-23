<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Form_User extends CM_Form_Abstract
{
	protected function init()
	{
		$this->add_plugin(new CM_Form_Plugin_ORM(array(
			'email', 'name', 'roles'
		)));

		$this->set_field('email', new CM_Field_String(), 10);
		$this->set_field('name', new CM_Field_String(), 40);
		$this->set_field('roles', new Extasy_Field_Roles(), 50);

		foreach (Kohana::$config->load('auth.form_plugins') as $role => $plugin)
		{
			$this->add_plugin(new $plugin($role));
		}

		$renderer = new CM_Form_Renderer_Fieldgroups();

		$renderer->add_fieldgroup('Общее');
		$renderer->set_field_fieldgroup($this->get_field_names(), 'Общее');

		$this->set_renderer($renderer);
	}

	protected function construct_form($param)
	{
		if ( ! $param->loaded())
		{
			$this->set_field('password', new CM_Field_Password(), 20);
			$this->set_field('password_confirm', new CM_Field_Password(), 30);
			$this->add_plugin(new CM_Form_Plugin_Validate(Model_User::get_password_validation()));
			$this->get_renderer()->set_field_fieldgroup('password', 'Общее');
			$this->get_renderer()->set_field_fieldgroup('password_confirm', 'Общее');
		}
	}
}