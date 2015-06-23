<?php defined('SYSPATH') or die('No direct script access.');

class Model_Filter extends ORM
{
	CONST TYPE_RANGE = 1;
	CONST TYPE_CHECKBOX = 2;
	CONST TYPE_SELECT = 3;
	CONST TYPE_MULTI_SELECT = 4;
	CONST TYPE_COLOR = 5;

	protected $_table_name = 'filters';

	protected $_belongs_to = array(
		'property' => array()
	);

	public function get_filter_types()
	{
		return array(
			self::TYPE_RANGE => 'Диапазон значений',
			self::TYPE_CHECKBOX => 'Наличие',
			self::TYPE_SELECT => 'Выбор одного значения',
			/*self::TYPE_MULTI_SELECT => 'Выбор нескольких значений',
			self::TYPE_COLOR => 'Выбор цвета'*/
		);
	}

	protected $_grid_columns = array(
		'name' => null,
		'type' => array(
			'type' => 'template',
			'template' => '${type_value}'
		),
		'active' => 'bool',
		'edit' => array(
			'width' => '50',
			'type' => 'link',
			'route_str' => 'admin-filter:edit?id=${id}&parent=${category_id}',
			'title' => '<i class="fa fa-pencil"></i>',
			'color' => 'green',
			'alternative' => 'Редактировать'
		),
		'delete' => array(
			'width' => '50',
			'type' => 'link',
			'color' => 'red',
			'route_str' => 'admin-filter:delete?id=${id}&parent=${category_id}',
			'title' => '<i class="fa fa-trash-o"></i>',
			'color' => 'red',
			'alternative' => 'Удалить',
			'confirm' => 'Вы уверены?'
		)
	);

	public function labels()
	{
		return array(
			'name' => 'Название',
			'type' => 'Тип'
		);
	}

	public function rules()
	{
		return array(
		);
	}

	public function save(Validation $validation = NULL)
	{
		return parent::save($validation);
	}

	public function form()
	{
		return new Form_Admin_Filter($this);
	}

	public function get_type_value()
	{
		$types = $this->get_filter_types();

		return $types[$this->type];
	}

	public function fetchByCategory($id)
	{
		$filters = ORM::factory('Filter')->where('category_id', '=', $id)->where('active', '=', 1)->find_all();

		return $filters;
	}

	public function getTemplate()
	{
		switch ($this->type) {
			case self::TYPE_CHECKBOX: {
				return View::factory('site/filter/checkbox', array('id' => $this->id))->render();
				break;
			}
			case self::TYPE_SELECT: {
				return View::factory('site/filter/select', array('id' => $this->id, 'values' => array()))->render();
				break;
			}
			case self::TYPE_MULTI_SELECT: {
				return View::factory('site/filter/multiselect', array('id' => $this->id, 'values' => array()))->render();
				break;
			}
		}
	}

	public function get_name()
	{
		return $this->property->name;
	}
}