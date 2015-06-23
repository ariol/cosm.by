<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Fieldselect_Config_Array extends CM_Fieldselect_Config
{
	public function init()
	{
		$fieldselect = new CM_Field_ArrayTypeField();
		$this->fieldschema()->set_field('field', $fieldselect);
		$this->fieldschema()->get_field('field')->set_label('Поле для значений');
	}

	public function set_value(CM_Value_Fieldselect $value)
	{
		$this->fieldschema()->get_field('field')->set_raw_value(
			$value->get_field()->get_value_field()
		);
	}

	public function apply_submitted_settings(CM_Field $field)
	{
		$value = $this->fieldschema()->get_field('field')->get_submitted_value();
		$field->set_value_field($value->get_field());
	}
}