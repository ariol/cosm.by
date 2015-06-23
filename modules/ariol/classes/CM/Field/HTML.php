<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Field_HTML extends CM_Field_Text
{
	public function render()
	{
		return View::factory('cm/field/html', array(
			'name' => $this->get_name(),
			'textarea' => parent::render()
		));
	}

	public function get_type_name()
	{
		return 'HTML';
	}
}