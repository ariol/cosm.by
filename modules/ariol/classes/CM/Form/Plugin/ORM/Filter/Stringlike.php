<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Form_Plugin_ORM_Filter_Stringlike extends CM_Form_Plugin_ORM_Filter_Abstract {

	public function populate(CM_Form_Abstract $form)
	{
		$value = $form->get_field($this->get_field_name())->get_value()->get_raw();
		if ( ! is_null($value))
		{
			$value = preg_replace('#\*{2,}#', '*', $value);
			$value = strtr($value, array(
					'%' => '\%',
					'_' => '\_'
				)
			);
			if( ! empty($value))
			{
				$this->get_model()->where($this->get_field_name(), 'LIKE', str_replace('*', '%', $value));
			}
		}
	}
}