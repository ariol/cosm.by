<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Form_Filter_Feedback extends CM_Form_Abstract
{
	private $_item = NULL;
	
	protected function init()
	{
		$this->add_plugin(new CM_Form_Plugin_ORM_Labels());
		$this->set_method('get');
		
		$this->set_field('email', new CM_Field_String(), 10);
		$this->set_field('status', new CM_Field_Select(array('open' => 'Открыт', 'in_process' => 'Принят в работу', 'closed' => 'Закрыт')), 20);
		
		$this->add_plugin(new CM_Form_Plugin_ORM_Filter_Equals('status'));
		$this->add_plugin(new CM_Form_Plugin_ORM_Filter_Like('email'));
		
	}
	
	protected function construct_form($param)
	{
		$this->_item = $param;
	}
}