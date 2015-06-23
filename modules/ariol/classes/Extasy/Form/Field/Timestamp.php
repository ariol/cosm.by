<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Form_Field_Timestamp extends Form_Field
{
	protected $_input_template = 'form/field/timestamp';
	
	protected function input_fields()
	{
		$fields = parent::input_fields();
		if($fields['value'])
		{
			$fields['value'] = date('Y-m-d H:i:s', $fields['value']);
		}
		return $fields;
	}
	
	public function prepare_to_populate($value)
	{
		if($value = strtotime($value))
		{
			return $value;
		}
		return NULL;
	}
}