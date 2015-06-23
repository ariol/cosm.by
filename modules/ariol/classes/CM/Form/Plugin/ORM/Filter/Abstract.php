<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Form_Plugin_ORM_Filter_Abstract extends CM_Form_Plugin_ORM_Abstract
{
	private $_field_name = NULL;

	public function __construct($field_name)
	{
		$this->_field_name = $field_name;
	}

	protected function get_field_name()
	{
		return $this->_field_name;
	}

	public function construct_form(CM_Form_Abstract $form, $param)
	{
		$this->set_model($param);
	}
}