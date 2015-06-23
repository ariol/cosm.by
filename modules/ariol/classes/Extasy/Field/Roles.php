<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Field_Roles extends CM_Field_Int
{
	private $_disabled_roles = array();

	public function set_role_disabled($role)
	{
		$this->_disabled_roles[$role] = TRUE;
	}

	private function create_checkboxes()
	{
		$checkboxes = array();

		foreach (Kohana::$config->load('auth.roles') as $role => $mask)
		{
			$role_checkbox = new CM_Field_Boolean();
			$role_checkbox->set_name($this->get_name().'_'.$role);
			$role_checkbox->set_value_source($this->get_value_source());
			$role_checkbox->set_raw_value($this->get_value()->get_raw() & $mask);
			$role_checkbox->set_label(
				arr::get(Kohana::$config->load('auth')->roles_labels, $role, $role)
			);
			$checkboxes[$role] = $role_checkbox;

			if (isset($this->_disabled_roles[$role]))
			{
				$role_checkbox->set_disabled(TRUE);
			}
		}

		return $checkboxes;
	}

	public function render()
	{
		return View::factory('extasy/field/roles', array(
			'checkboxes' => $this->create_checkboxes(),
			'roles' => Kohana::$config->load('auth.roles')
		));
	}

	public function is_submitted()
	{
		foreach ($this->create_checkboxes() as $checkbox)
		{
			if ($checkbox->is_submitted())
			{
				return TRUE;
			}
		}
		return FALSE;
	}

	public function get_submitted_value()
	{
		$value = 0;
		foreach ($this->create_checkboxes() as $role => $checkbox)
		{
			if ($checkbox->get_submitted_value()->get_raw())
			{
				$value |= Kohana::$config->load('auth.roles.'.$role);
			}
		}
		return $this->create_raw_value($value);
	}
}