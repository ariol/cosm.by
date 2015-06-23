<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */
class CM_Field_Array extends CM_Field
{
	protected $_value_class = 'CM_Value_Array';

	private $_allow_add = TRUE;

	private $deniedTypes = array(
		'file',
		'multifile'
	);

	/**
	 * @var CM_Field
	 */
	protected $_value_field = NULL;

	public function __construct(CM_Field $value_field = NULL)
	{
		if ($this->typeIsDenied($value_field->get_type())) {
			throw new Exception('CM field type is denied: ' . $value_field->get_type());
		}

		parent::__construct();
		if ( ! is_null($value_field))
		{
			$this->set_value_field($value_field);
		}
	}

	public function get_type_name()
	{
		return 'Набор значений';
	}

	public function set_name($name)
	{
		parent::set_name($name);

		// TODO: Костыль. Если в массиве есть поля, обрабатываемые аяксом - тут они должны создаться
		for ($num = 0; $num <= 20; $num++)
		{
			$field_name = $name.'_'.$num;
			$value = arr::get($this->get_value()->get_values(), $num);
			if ($value)
			{
				$value = $value->get_raw();
			}
			$valuefield = $this->get_value_field();
			$valuefield->set_raw_value($value);
			$valuefield->set_name($field_name.'_value');
		}
	}

	public function set_value_field(CM_Field $field)
	{
		$this->_value_field = $field;
		$this->_value_field->set_value_source($this->get_value_source());
		$this->_value_field->set_raw_value(NULL);
		$this->get_value()->set_value_field($field);
	}

	public function get_value_field()
	{
		if (is_null($this->_value_field))
		{
			$this->_value_field = new CM_Field_String;
		}
		return $this->_value_field;
	}

	public function set_value(CM_Value_Interface $value)
	{
		$value->set_value_field($this->get_value_field());
		parent::set_value($value);
	}

	public function set_allow_add($allow_add)
	{
		$this->_allow_add = (bool) $allow_add;
	}

	public function set_value_source($source)
	{
		parent::set_value_source($source);
		if ( ! is_null($this->_value_field))
		{
			$this->_value_field->set_value_source($source);
		}
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
				// Нашли поле, идентифицирующее группу полей
				if (preg_match('#^[0-9]+$#', $num))
				{
					$valuefield = clone $this->_value_field;

					$value = arr::get($this->get_value()->get_values(), $num);
					if ($value)
					{
						$value = $value->get_raw();
					}

					$valuefield->set_raw_value($value);
					$valuefield->set_name($field_name.'_value');
					$valuefield->set_value_source($this->get_value_source());

					$values[] = $valuefield->get_submitted_value()->get_raw();
				}
			}
		}

		return $this->create_raw_value(serialize($values));
	}

	public function render()
	{
		$this->_value_field->set_raw_value(NULL);
		return View::factory('cm/field/array', array(
			'value_field' => $this->_value_field,
			'value' => $this->get_value(),
			'name' => $this->get_name(),
			'allow_add' => $this->_allow_add
		));
	}

	public function render_value()
	{
		$rendered_value = '<table border="0">';
		foreach ($this->get_value()->get_values() as $value)
		{
			$this->_value_field->set_value($value);

			$rendered_value .= '<tr><td>'.$this->_value_field->render_value().'</td></tr>';
		}
		return $rendered_value.'</table>';
	}

	private function typeIsDenied($type)
	{
		return in_array($type, $this->deniedTypes);
	}
}