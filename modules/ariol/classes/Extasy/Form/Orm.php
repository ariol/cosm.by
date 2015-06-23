<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Form_ORM extends Widget_Container
{
	private $_fields = array();

	/**
	 * @var ORM
	 */
	private $_object = NULL;

	private $_hash = '';

	protected $_hash_input_name = 'ext-form-hash';

	protected $_messages_file = 'validate';

	protected $_open_template = 'form/open';
	protected $_close_template = 'form/close';
	protected $_submits_template = 'form/submits';
	protected $_fields_template = 'form/fields';

	protected $_id = NULL;

	static public function factory(ORM $object, array $fields = array())
	{
		return new Form_ORM($object, $fields);
	}

	protected function __construct(ORM $object, array $fields = array())
	{
		$this->_object = $object;
		$this->set_name($object->object_name());
		$hash = '';
		foreach($fields as $name => $field)
		{
			if( ! is_array($field))
			{
				$field = array(
					'type' => $field
				);
			}
			$field_cfg = array(
				'name' => $name,
				'label' => Arr::get($object->labels(), $name),
				'value' => $object[$name],
			);

			$field = Arr::merge($field_cfg, (array) $field);
			$field = Form_Field::factory($field);
			$field->set_object($object);
			$this[$name] = $field;
			$hash .= $field->hash();
		}
		$this->_hash = md5($hash);
	}

	public function open($action = NULL, array $attributes = NULL)
	{
		if(is_null($attributes))
		{
			$attributes = array();
		}
		$this->_id = Arr::get($attributes, 'id', 'ext-form-'.$this->_hash);
		$attributes['id'] = $this->_id;

		return View::factory($this->_open_template, array(
			'hash_input_name' => $this->_hash_input_name,
			'hash' => $this->_hash,
			'action' => $action,
			'attributes' => $attributes
		));
	}

	public function close()
	{
		return View::factory($this->_close_template, array(
			'id' => $this->_id,
			'validate' => $this->_object->validate()->to_jquery()
		));
	}

	public function submits(array $submits = array())
	{
		if( ! $this->allowed('write'))
		{
			$submits = array();
		}

		return View::factory($this->_submits_template, array(
			'submits' => $submits
		));
	}

	public function submit($name = null, $value = null)
	{
		return $this->submits(array(array(
			'name' => $name,
			'value' => $value
		)));
	}

	public function fields()
	{
		// Не пишу "'fields' => $this", чтобы не было лишних данных в скрипте вида
		return View::factory($this->_fields_template, array(
			'fields' => $this->subwidgets()
		));
	}

	/**
	 * @return bool
	 */
	public function populate()
	{
		if( ! $this->allowed('write') )
		{
			return FALSE;
		}

		$hash = $this->_hash_input_name;

		// nothing or the other form has been submitted
		if( ! isset($_POST[$hash]))
		{
			return FALSE;
		}

		$values = array();
		foreach($this as $name => $field)
		{
			if( $field->allowed('write') )
			{
				if( ! is_null($value = Arr::get($_POST, $name)))
				{
					$values[$name] = $field->prepare_to_populate($value);
				}
			}
		}

		$this->_object->values($values);

		foreach($this as $name => $field)
		{
			$field->set_value($this->_object->$name);
		}

		$valid = $this->_object->check();

		foreach($this as $name => $field)
		{
			$valid = ($valid AND $field->validate($this->_object->validate(), $name));
		}

		if($valid)
		{
			return TRUE;
		}

		$errors = $this->_object->validate()->errors($this->_messages_file);
		foreach($errors as $name => $error)
		{
			if(isset($this[$name]))
			{
				$this[$name]->set_error($error);
			}
		}
		return FALSE;
	}

	protected function _render()
	{
		throw new Extasy_Form_Exception('Not implemented');
	}
}