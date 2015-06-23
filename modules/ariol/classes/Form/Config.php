<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Form_Config extends CM_Form_Abstract
{
	protected function init()
	{
		$priorities = Kohana::$config->load('config.form.priorities');

		asort($priorities);

		$fieldgroups = array();
		$labels = array();

		foreach ($priorities as $group_label => $priority)
		{
			$fieldgroups[$group_label] = array();
			$position = 1;

			$group_config = Kohana::$config->load('config.form.fieldgroups.'.$group_label);

			if ( ! $group_config)
			{
				continue;
			}

			foreach ($group_config as $config_key => $field_options)
			{
				$field_name = str_replace('.', '-', $config_key);
				$labels[$field_name] = arr::get($field_options, 'label');
				$this->set_field($field_name, arr::get($field_options, 'field'), $position++);
				$this->get_field($field_name)->set_raw_value(
					Kohana::$config->load($config_key)
				);
				$fieldgroups[$group_label][] = $field_name;
			}
		}

		$this->add_plugin(new CM_Form_Plugin_Labels($labels));
		$this->add_plugin(new CM_Form_Plugin_Fieldgroups($fieldgroups));
	}

	protected function after_submit()
	{
		foreach ($this->get_field_names() as $name)
		{
			list($group, $key) = explode('-', $name, 2);
			Kohana::$config->load($group)->set(str_replace('-', '.', $key), $this->get_raw($name));
		}
	}
}