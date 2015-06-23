<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Grid_Column_Position extends Grid_Column_Text
{
	protected function _field($obj)
	{
		return View::factory($this->_field_template, array(
			'align' => $this->_align,
			'value' => '<i style="cursor: pointer;" action="minus" class="fa fa-minus minus" data-value="' . $obj->id . '" type="increment"></i>
							<span style="padding: 15px;">' . $obj[$this->get_name()] . '</span>
							<i style="cursor: pointer;" data-value="' . $obj->id . '" type="increment" action="plus" class="fa fa-plus minus"></i>',
		));
	}
}