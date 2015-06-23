<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Grid_Column_Child extends Grid_Column_Template
{
	protected $_field;
	protected $_model;
	protected $_external_url;

	public function __construct(array $column = array())
	{
		parent::__construct($column);

		$this->_field = Arr::get($column, 'field');
		$this->_model = Arr::get($column, 'model');
		$this->_external_url = Arr::get($column, 'external_url');
	}
	
	protected function _field($obj)
	{	
		$model = $this->_model;
		$field = $this->_field;
		
		$name = $obj->$model->$field;
		
		$externalRoute = Extasy::obj_placeholders($obj, $this->_external_url);
		$externalUrl = Extasy_Html::link_to_route($externalRoute, $name, 
				array('target' => '_blank', 'class' => 'title-tooltip', 'title' => 'Перейти на страницу сайта'));
		
		return View::factory($this->_field_template, array(
			'align' => $this->_align,
			'value' => $externalUrl
		));
	}
}