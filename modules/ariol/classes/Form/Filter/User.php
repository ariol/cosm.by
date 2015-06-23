<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Form_Filter_User extends CM_Form_Abstract
{
	private $_item = NULL;

	protected function init()
	{
		$this->add_plugin(new CM_Form_Plugin_ORM_Labels());
		$this->add_plugin(new CM_Form_Plugin_ORM_Filter_Like('email'));

		$this->set_field('email', new CM_Field_String(), 10);

		$options = array(
			'0' => '-- выберите роль --',
		);

		foreach (Kohana::$config->load('auth.roles') as $role => $mask)
		{
			$options[$role] = arr::get(Kohana::$config->load('auth.roles_labels'), $role, $role);
		}

		$this->set_field('roles', new CM_Field_Select($options));

		$this->set_method('get');
	}

	protected function construct_form($param)
	{
		$this->_item = $param;
	}

	protected function after_submit()
	{
		if ($role = $this->get_field('roles')->get_value()->get_raw())
		{
			$this->_item->where_has_role($role);
		}
	}
}