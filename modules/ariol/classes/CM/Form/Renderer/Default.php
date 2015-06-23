<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Form_Renderer_Default extends CM_Form_Renderer_Abstract
{
	public function render(CM_Form_Abstract $form)
	{
		return View::factory('cm/form', array(
			'form' => $form
		));
	}
}