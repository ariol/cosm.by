<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Form_Plugin_Orm extends CM_Form_Plugin_ORM_Abstract
{
	private $_ignored_fields = array();
	private $_populated_fields = NULL;

	private $_labels_plugin = NULL;


	/**
	 * @var Validation
	 */
	private $_extra_validation = NULL;

	public function __construct($populated_fields = NULL, $ignored_fields = array(), Validation $extra_validation = NULL)
	{
		$this->_ignored_fields = (array)$ignored_fields;
		$this->_populated_fields = $populated_fields;

		$this->_labels_plugin = new CM_Form_Plugin_ORM_Labels();

		$this->_extra_validation = $extra_validation;
	}

	public function construct_form(CM_Form_Abstract $form, $params)
	{
		$this->set_model($params);
		$this->_labels_plugin->construct_form($form, $params);

		foreach ($form->get_field_names() as $name)
		{
			if ($this->is_field_populated($name))
			{
				$form->get_field($name)->set_raw_value($this->get_model()->$name);
			}
		}
	}

	public function populate(CM_Form_Abstract $form)
	{
		foreach ($form->get_field_names() as $name)
		{
			if ($this->is_field_populated($name))
			{
				$this->get_model()->$name = $form->get_field($name)->get_value()->get_raw();
			}
		}
	}

	public function validate(CM_Form_Abstract $form)
	{
		if ($this->_extra_validation)
		{
			$values = array();
			foreach ($form->get_values() as $name => $value)
			{
				$values[$name] = $value->get_raw();
				$this->_extra_validation->label($name, $form->get_field($name)->get_label());
			}
			// Validation только read-only, поэтому создаем новый объект
			$this->_extra_validation = $this->_extra_validation->copy($values);
		}

		try
		{
			$this->get_model()->check($this->_extra_validation);
		}
		catch (ORM_Validation_Exception $e)
		{
			$errors = $e->errors('validation');
			if ($external = arr::get($errors, '_external'))
			{
				$errors = arr::merge($errors, $external);
				unset($errors['_external']);
			}
			foreach ($errors as $name => $error)
			{
				$form->get_field($name)->set_error($error);
			}
			return FALSE;
		}
		return TRUE;
	}

	public function after_submit(CM_Form_Abstract $form)
	{
		$this->get_model()->save();
	}

    public function is_field_ignored($field_name)
    {
        return in_array($field_name, $this->_ignored_fields);
    }

	protected function is_field_populated($name)
	{
		return  ! in_array($name, $this->_ignored_fields) AND (is_null($this->_populated_fields) OR in_array($name, $this->_populated_fields));
	}
}