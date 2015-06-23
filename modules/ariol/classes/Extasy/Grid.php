<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Grid extends Widget_Container
{
	protected $_orm = NULL;

	protected $_template = 'grid/all';

	protected $_options = NULL;

	protected $_group_actions = array();

	public function __construct(ORM $orm, array $columns = array(), $options = array())
	{
		$this->_options = $options;

		$this->set_name($orm->object_name());

		foreach ($columns as $name => $column) {
			if(!is_array($column)) {
				$column = array(
					'type' => $column
				);
			}
			$column_cfg = array(
				'name' => $name,
				'header' => Arr::get($orm->labels(), $name),
				'params' => Arr::get($column, 'params', array())
			);
			$column = Arr::merge($column_cfg, (array) $column);
			$column = Grid_Column::factory($column);
			$this[$name] = $column;
		}

		$this->_orm = $orm;
	}

	static public function factory(ORM $orm, array $columns = array(), array $options = array())
	{
		return new Grid($orm, $columns, $options);
	}

	protected function _render()
	{
		return View::factory($this->_template, array(
			'grid' => $this,
			'group_actions' => $this->_group_actions,
			'columns' => $this->subwidgets(),
			'draw_checkboxes' => (bool) count($this->_group_actions)
		));
	}

	public function set_group_actions($actions = array())
	{
		$this->_group_actions = $actions;
		return $this;
	}
}