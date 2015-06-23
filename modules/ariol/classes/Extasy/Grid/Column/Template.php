<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Grid_Column_Template extends Grid_Column
{
	protected $_field_template = 'grid/column/template';
	
	protected $_template = '';

	public function __construct(array $column = array())
	{
		parent::__construct($column);
		$this->_template = Arr::get($column, 'template', $this->_template);
	}
	
	protected function _field($obj)
	{
		return View::factory($this->_field_template, array(
			'value' => Extasy::obj_placeholders($obj, $this->_template)
		));
	}
}