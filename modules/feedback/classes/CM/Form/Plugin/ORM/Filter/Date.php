<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Form_Plugin_ORM_Filter_Date extends CM_Form_Plugin_ORM_Filter_Abstract
{
	public function populate(CM_Form_Abstract $form)
	{
		$date = $form->get_field($this->get_field_name())->get_value();
		
		$date_value = $date->get_raw();
		
		if($date->is_valid() && ! empty($date_value))
		{
		    $this->get_model()->where($this->get_field_name(), '>=', $date->get_raw().' 00:00:00');
		    $this->get_model()->where($this->get_field_name(), '<=', $date->get_raw().' 23:59:59');
		}
	}
}