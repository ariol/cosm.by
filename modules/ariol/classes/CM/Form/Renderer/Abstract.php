<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

abstract class CM_Form_Renderer_Abstract
{
	abstract public function render(CM_Form_Abstract $form);
}