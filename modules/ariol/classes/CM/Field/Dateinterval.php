<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Field_DateInterval extends CM_Field
{
	protected $_value_class = 'CM_Value_DateInterval';

	protected function create_from_field()
	{
		$from_field = new CM_Field_Date();
		$from_field->set_name($this->get_name().'_from');
		$from_field->set_value_source($this->get_value_source());
		$from_field->set_value($this->get_value()->get_date_from());
		return $from_field;
	}

	protected function create_to_field()
	{
		$to_field = new CM_Field_Date();
		$to_field->set_name($this->get_name().'_to');
		$to_field->set_value_source($this->get_value_source());
		$to_field->set_value($this->get_value()->get_date_to());
		return $to_field;
	}

	public function is_submitted()
	{
		return $this->create_from_field()->is_submitted() AND $this->create_to_field()->is_submitted();
	}

	public function get_submitted_value()
	{
		return $this->create_raw_value(
			$this->create_from_field()->get_submitted_value()->get_raw()
			.' '
			.$this->create_to_field()->get_submitted_value()->get_raw()
		);
	}

	protected function validate()
	{
		$errors = array();
		if ($error = $this->create_from_field()->get_error())
		{
			$errors[] = $error;
		}
		if ($error = $this->create_to_field()->get_error())
		{
			$errors[] = $error;
		}
		if ( ! empty($errors))
		{
			$this->set_error('Произошли ошибки: '.implode(', ', $errors));
		}
	}

	public function render()
	{
		return View::factory('cm/field/date_interval', array(
			'name' => $this->get_name(),
			'date_from' => $this->create_from_field(),
			'date_to' => $this->create_to_field()
		));
	}

}