<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Field_Float extends CM_Field_Int
{
	protected $_value_class = 'CM_Value_Float';

	public function get_type_name()
	{
		return 'Дробное число';
	}
}