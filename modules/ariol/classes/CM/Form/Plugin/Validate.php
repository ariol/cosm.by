<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Form_Plugin_Validate extends CM_Form_Plugin_Abstract
{
	/**
	 * @var Validation
	 */
	private $_validation = NULL;

	public function __construct(Validation $validation)
	{
		$this->_validation = $validation;
	}

	public function validate(CM_Form_Abstract $form)
	{
		$values = array();

		foreach ($form->get_values() as $name => $value)
		{
			$values[$name] = $value->get_raw();

			$this->_validation->label($name, $form->get_field($name)->get_label());
		}

		// Validation только read-only, поэтому создаем новый объект
		$this->_validation = $this->_validation->copy($values);

		if ($this->_validation->check())
		{
			return TRUE;
		}

		foreach ($this->_validation->errors('validation') as $name => $error)
		{
			$form->set_error($name, $error);
		}
		return FALSE;
	}
}