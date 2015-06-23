<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Field_Phone extends CM_Field_String
{
	protected function validate()
	{
		parent::validate();
		if ( ! preg_match('#^[ 0-9\+\-\(\)]+$#', $this->get_value()->get_raw()))
		{
			$this->set_error('Телефон может содержать только цифры, скобки, пробелы, - и +');
		}
	}
}