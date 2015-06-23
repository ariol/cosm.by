<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Field_Manytomany_ORM extends CM_Field
{
	protected $_value_class = 'CM_Value_Int';

	private $_relation_model = NULL;
	private $_model = NULL;
	private $_name_key = NULL;
	private $_id_key = NULL;

	public function __construct(ORM $relationModel, ORM $model, $name_key = 'name', $id_key = 'id')
	{
		parent::__construct();
		$this->_relation_model = clone $relationModel;
		$this->_model = clone $model;
		$this->_name_key = $name_key;
		$this->_id_key = $id_key;
	}

	protected function get_model()
	{
		return $this->_relation_model;
	}

	protected function get_name_key()
	{
		return $this->_name_key;
	}

	protected function get_id_key()
	{
		return $this->_id_key;
	}

	protected function get_options()
	{
		$options = array();

		$model = clone $this->_relation_model;

		foreach ($model->order_by($this->_name_key, 'ASC')->find_all() as $obj)
		{
			$options[$obj->{$this->_id_key}] = $obj->{$this->_name_key};
		}

		return $options;
	}

	public function render()
	{
		$hasMany = $this->_model->has_many();
		$relation = $hasMany[$this->get_name()];

		$relation_table = $relation['through'];
		$foreign_key = $relation['foreign_key'];
		$far_key = $relation['far_key'];

		$relation_result = DB::select($far_key)->from($relation_table)->where($foreign_key, '=', $this->_model->id)->execute()->as_array();

		$ids = array();

		foreach ($relation_result as $result) {
			$ids[] = $result[$far_key];
		}

		$attributes = $this->get_attributes();
		$attributes['multiple'] = 'multiple';
		if (isset($attributes['class'])) {
			$attributes['class'] .= ' form-control';
		}
		else {
			$attributes['class'] = 'form-control';
		}
		$attributes['data-rel'] = 'chosen';

		return Form::select($this->get_name() . '[]', $this->get_options(), $ids, $attributes);
	}

	public function render_value()
	{
		$options = $this->get_options();
		if (isset($options['']))
		{
			unset($options['']);
		}
		return arr::get($options, (string) $this->get_value());
	}

	public function validate()
	{
		if (is_null($this->get_value()->get_raw()))
		{
			return;
		}
	}

	public function get_submitted_value()
	{
		$source = $this->get_value_source();

		return  $this->create_raw_value(serialize($source[$this->get_name()]));
	}
}