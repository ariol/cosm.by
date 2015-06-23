<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Form_Field_HTML extends Form_Field_Textarea
{
	protected function _input()
	{
		return parent::_input().Extasy_Htmleditor::init($this->get_name());
	}
}