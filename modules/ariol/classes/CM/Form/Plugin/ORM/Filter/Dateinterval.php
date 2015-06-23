<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Form_Plugin_ORM_Filter_DateInterval extends CM_Form_Plugin_ORM_Filter_Abstract
{
	public function populate(CM_Form_Abstract $form)
	{
		$date_from = $form->get_field($this->get_field_name())->get_value()->get_date_from();
		$date_to = $form->get_field($this->get_field_name())->get_value()->get_date_to();

		if ($date_from->is_valid() AND ! is_null($date_from->get_raw()))
		{
			$this->get_model()->where($this->get_field_name(), '>=', strtotime($date_from->get_raw().' 00:00:00'));
		}
		if ($date_to->is_valid() AND ! is_null($date_to->get_raw()))
		{
			$this->get_model()->where($this->get_field_name(), '<=', strtotime($date_to->get_raw().' 23:59:59'));
		}
	}
}