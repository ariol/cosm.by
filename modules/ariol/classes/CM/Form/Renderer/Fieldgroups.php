<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Form_Renderer_Fieldgroups extends CM_Form_Renderer_Abstract
{
	private $_fields_fieldgroups = array();

	private $_fieldgroups = array('Default');

	public function add_fieldgroup($fieldgroup)
	{
		$this->_fieldgroups[] = $fieldgroup;
	}

	public function has_fieldgroup($fieldgroup)
	{
		return in_array($fieldgroup, $this->_fieldgroups);
	}

	public function set_field_fieldgroup($field, $fieldgroup)
	{
		if (is_array($field))
		{
			foreach ($field as $cur_field)
			{
				$this->set_field_fieldgroup($cur_field, $fieldgroup);
			}
		}
		else
		{
			if ( ! $this->has_fieldgroup($fieldgroup))
			{
				$this->add_fieldgroup($fieldgroup);
			}
			$this->_fields_fieldgroups[$field] = $fieldgroup;
		}
	}

	public function render(CM_Form_Abstract $form)
	{
		$fieldgroups = array();

		foreach ($this->_fieldgroups as $fieldgroup)
		{
			$fieldgroups[$fieldgroup] = array();
		}

		foreach ($form->get_field_names() as $name)
		{
			$fieldgroup = arr::get($this->_fields_fieldgroups, $name, 'Default');
			$fieldgroups[$fieldgroup][$name] = $form->get_field($name);
		}

		foreach ($fieldgroups as $fieldgroup_name => $fields)
		{
			if (empty($fields))
			{
				unset($fieldgroups[$fieldgroup_name]);
			}
		}

		return View::factory('cm/form/fieldgroups', array(
			'fieldgroups' => $fieldgroups,
			'form' => $form
		));
	}
}