<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

abstract class Extasy_Grid_Column extends Widget_Item
{
	protected $_header = NULL;

	protected $_header_template = 'grid/column/header';
	protected $_field_template = NULL;

	protected $_width = NULL;

	protected $_orderable = FALSE;

	protected $_order_by = NULL;

	protected $_align = 'left';

	/**
	 * @param array $column
	 * @return Grid_Column
	 */
	static public function factory(array $column = array())
	{
		$class = Arr::get($column, 'type', 'text');
		$class = 'Grid_Column_'.ucfirst($class);

		if(isset($column['type']))
		{
			unset($column['type']);
		}

		return new $class($column);
	}

	public function order_by($direction)
	{
		$this->_order_by = $direction;
	}

	public function __construct(array $column = array())
	{
		$this->_header = Arr::get($column, 'header', '');
		$this->_width = Arr::get($column, 'width', $this->_width);
		$this->_orderable = Arr::get($column, 'orderable', $this->_orderable);
		$this->_align = Arr::get($column, 'align', $this->_align);
		$this->set_name(Arr::get($column, 'name'));
	}

	protected function _render()
	{
		return View::factory($this->_header_template, array(
			'header' => $this->_header,
			'width' => $this->_width,
			'order_by' => $this->_order_by,
			'orderable' => $this->_orderable,
			'name' => $this->get_name(),
			'align' => $this->_align
		));
	}

	public function field($obj)
	{
		if( ! $this->allowed('view') AND ! $this->allowed('read'))
		{
			return '';
		}

		$field = $this->_field($obj);

		return isset($field->value) ? $field->value : $field;
	}

	protected function _field($obj)
	{
		return View::factory($this->_field_template, array(
			'obj' => $obj,
			'name' => $this->get_name()
		));
	}
}