<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Grid_Column_Position extends Grid_Column_Text
{
	protected function _field($obj)
	{
		$value = $obj[$this->get_name()];

		return View::factory($this->_field_template, array(
			'value' => $value,
			'align' => $this->_align
		));
	}
}