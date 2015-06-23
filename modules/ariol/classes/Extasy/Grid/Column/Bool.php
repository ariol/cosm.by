<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Grid_Column_Bool extends Grid_Column_Text
{
	protected function _field($obj)
	{
		return View::factory('grid/column/boolean', array(
			'align' => $this->_align,
			'value' => $obj[$this->get_name()],
			'object' => $obj,
			'field' => $this->get_name()
		))->render();
	}
}