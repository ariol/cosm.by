<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Form_Plugin_Fieldschema extends CM_Form_Plugin_Abstract
{
	/**
	 * @var CM_Fieldschema_Container_Interface
	 */
	private $_item = NULL;

	public function construct_form(CM_Form_Abstract $form, $container)
	{
		$this->_item = $container;

		if ( ! $container instanceof CM_Fieldschema_Container_Interface)
		{
			throw new Exception('param cust be an instanceof CM_Fieldschema_Container_Interface');
		}

		$fieldschema = $container->get_fieldschema();

		foreach ($fieldschema->get_field_names() as $name)
		{
			$form->set_field($name, $fieldschema->get_field($name));
		}
	}

	public function populate(CM_Form_Abstract $form)
	{
		$values = array();
		foreach ($this->_item->get_fieldschema()->get_field_names() as $name)
		{
			$values[$name] = $form->get_field($name)->get_value();
		}
		$this->_item->set_values($values);

	}

	public function after_submit(CM_Form_Abstract $form)
	{
		$this->_item->save();
	}
}