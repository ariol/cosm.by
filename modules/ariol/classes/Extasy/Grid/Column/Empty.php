<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Grid_Column_Empty extends Grid_Column_Text
{
	protected function _field($obj)
	{
		return View::factory($this->_field_template, array(
			'align' => $this->_align,
			'value' => $obj[$this->get_name()] ? '<span class="label label-success">Да</span>' : '<span class="label label-important">Нет</span>',
		));
	}
}