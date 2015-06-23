<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Form_Field_Select_ORM extends Form_Field_Select
{
	protected $_model = NULL;
	protected $_key   = 'id';
	protected $_view_value = '${name}';

	protected $_rules = array();

	protected $_allow_empty = FALSE;
	protected $_empty_value = '-- не указан --';

	protected $_options = NULL;

	protected function __construct(array $field = array())
	{
		$this->_model = Arr::get($field, 'model', $this->_model);
		$this->_key = Arr::get($field, 'key', $this->_key);
		$this->_view_value = Arr::get($field, 'view_value', $this->_view_value);
		$this->_rules = Arr::get($field, 'rules', $this->_rules);
		$this->_allow_empty = Arr::get($field, 'allow_empty', $this->_allow_empty);
		$this->_empty_value = Arr::get($field, 'empty_value', $this->_empty_value);
		parent::__construct($field);
	}

	public function get_options()
	{
		if(is_null($this->_options))
		{
			$model = ORM::factory($this->_model);

			foreach($this->_rules as $rule)
			{
				$model->where($rule[0], $rule[1], Extasy::obj_placeholders($this->get_object(), $rule[2]));
			}

			$objects = $model->find_all();
			$options = array();

			if($this->_allow_empty)
			{
				$options[0] = $this->_empty_value;
			}

			foreach($objects as $object)
			{
				$options[$object[$this->_key]] = Extasy::obj_placeholders($object, $this->_view_value);
			}
			$this->_options = $options;
		}
		return $this->_options;
	}

	public function get_selected_value()
	{
		return $this->_value[$this->_key];
	}

	public function prepare_to_populate($value)
	{
		$model = ORM::factory($this->_model);
		foreach($this->_rules as $rule)
		{
			$model->where($rule[0], $rule[1], Extasy::obj_placeholders($this->get_object(), $rule[2]));
		}

		if ( ! $value)
		{
			$value = NULL;
		}

		$model->where($this->_key, '=', $value);

		$obj = $model->find();
		return $obj;
	}
}