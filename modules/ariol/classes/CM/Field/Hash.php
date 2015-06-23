<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Field_Hash extends CM_Field
{
	protected $_value_class = 'CM_Value_Hash';

	private $_allow_add = TRUE;

	/**
	 * @var CM_Field
	 */
	protected $_value_field = NULL;

	public function get_type_name()
	{
		return 'Набор значений с ключами';
	}

	public function set_value_field(CM_Field $field)
	{
		$this->_value_field = $field;
		$this->_value_field->set_value_source($this->get_value_source());
		$this->_value_field->set_raw_value(NULL);
	}

	public function get_value_field()
	{
		if (is_null($this->_value_field))
		{
			$this->set_value_field(new CM_Field_String);
		}
		return $this->_value_field;
	}

	public function get_value()
	{
		$value = parent::get_value();
		$value->set_value_field($this->get_value_field());
		return $value;
	}

	public function set_allow_add($allow_add)
	{
		$this->_allow_add = (bool) $allow_add;
	}

	public function set_value_source($source)
	{
		parent::set_value_source($source);
		$this->get_value_field()->set_value_source($source);
	}

	protected function validate()
	{
		$value_field = $this->get_value_field();
		foreach ($this->get_value()->get_values() as $value)
		{
			$value_field->set_value($value);
			if ( ! $value_field->is_valid())
			{
				$this->set_error('Некоторые поля имеют неверные значения');
			}
		}
	}

	public function get_submitted_value()
	{
		$source = $this->get_value_source();

		$values = array();

		foreach ($source as $field_name => $field_value)
		{
			if (strpos($field_name, $this->get_name().'_') === 0)
			{
				$num = str_replace($this->get_name().'_', '', $field_name);
				// Нашли поле, отвечающее за имя
				if (preg_match('#^[0-9]+$#', $num))
				{
					$namefield = new CM_Field_String();
					$namefield->set_name($field_name);
					$namefield->set_value_source($this->get_value_source());

					$name = $namefield->get_submitted_value()->get_raw();

					$valuefield = clone $this->get_value_field();

					$value = arr::get(array_values($this->get_value()->get_values()), $num);
					if ($value)
					{
						$value = $value->get_raw();
					}

					$valuefield->set_raw_value($value);
					$valuefield->set_name($field_name.'_value');
					$valuefield->set_value_source($this->get_value_source());

					$values[$name] = $valuefield->get_submitted_value()->get_raw();
				}
			}
		}

		return $this->create_raw_value(serialize($values));
	}

	public function render()
	{
		$value_field = $this->get_value_field();
		$value_field->set_raw_value(NULL);
		return View::factory('cm/field/hash', array(
			'value_field' => $value_field,
			'value' => $this->get_value(),
			'name' => $this->get_name(),
			'allow_add' => $this->_allow_add
		));
	}

	public function render_value()
	{
		$rendered_value = '<table border="0">';
		foreach ($this->get_value()->get_values() as $name => $value)
		{
			$this->_value_field->set_value($value);

			$rendered_value .= '<tr><td width="30%">'.$name.'</td><td>'.$this->get_value_field()->render_value().'</td></tr>';
		}
		return $rendered_value.'</table>';
	}
}