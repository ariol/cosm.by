<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

abstract class CM_Form_Plugin_Abstract
{
	public function construct_form(CM_Form_Abstract $form, $param) {}

	public function populate(CM_Form_Abstract $form) {}

	public function validate(CM_Form_Abstract $form) { return TRUE; }

	public function after_submit(CM_Form_Abstract $form) {}
}