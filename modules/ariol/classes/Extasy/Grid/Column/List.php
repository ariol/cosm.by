<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Grid_Column_List extends Grid_Column_Template
{
	protected $_field_template = 'grid/column/list';
	
	protected function _field($obj)
	{
		$values = $obj[$this->get_name()];
		if($values instanceof ORM)
		{
			$values = $values->find_all();
		}
		$values_out = array();
		
		foreach($values as $value)
		{
			$values_out[] = Extasy::obj_placeholders($value, $this->_template);
		}
		
		return View::factory($this->_field_template, array(
			'values' => $values_out,
			'separator' => ', '
		));
	}
}