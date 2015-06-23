<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Form_Plugin_Unfilter extends CM_Form_Plugin_Abstract
{
	private $_fields = array();
	
	public function __construct(array $fields = array())
	{
		$this->_fields = $fields;
	}
	
	public function construct_form(CM_Form_Abstract $form, $param)
	{
		foreach ($this->_fields as $name)
		{
			$form->get_field($name)->set_raw_value(
				html_entity_decode($form->get_field($name)->get_value()->get_raw(), ENT_COMPAT, 'UTF-8')
			);
		}
	}
}