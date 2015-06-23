<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Grid_Column_Select extends Grid_Column_Text
{
	protected function _field($obj)
	{
		return View::factory($this->_field_template, array(
			'value' => $obj->get_rendered($this->get_name()),
			'align' => $this->_align
		));
	}
}