<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Grid_Column_Text extends Grid_Column
{
	protected $_field_template = 'grid/column/text';

	protected $_orderable = TRUE;

	protected function _field($obj)
	{
		return View::factory($this->_field_template, array(
			'value' => $obj->{$this->get_name()},
			'align' => $this->_align
		));
	}
}