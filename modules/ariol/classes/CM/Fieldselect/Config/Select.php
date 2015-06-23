<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Fieldselect_Config_Select extends CM_Fieldselect_Config
{
	public function init()
	{
		$this->fieldschema()->set_field('options', new CM_Field_Hash);
		$this->fieldschema()->get_field('options')->set_label('Список значений');
	}

	public function set_value(CM_Value_Fieldselect $value)
	{
		$this->fieldschema()->get_field('options')->set_raw_value(
			$value->get_field()->get_options()
		);
	}

	public function apply_submitted_settings(CM_Field $field)
	{
		$options = $this->fieldschema()->get_field('options')->get_submitted_value();
		$field->set_options(unserialize($options->get_raw()));
	}
}