<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Grid_Column_Name extends Grid_Column_Template
{
	protected $_route_str;
	protected $_external_url;

	public function __construct(array $column = array())
	{
		parent::__construct($column);

		$this->_route_str = Arr::get($column, 'route_str');
		$this->_external_url = Arr::get($column, 'external_url');
	}
	
	protected function _field($obj)
	{	
		$editRoute = Extasy::obj_placeholders($obj, $this->_route_str);
		$editUrl = Extasy_Html::link_to_route($editRoute, $obj->name);
		
		$externalRoute = Extasy::obj_placeholders($obj, $this->_external_url);
		$externalIcon = '&nbsp;&nbsp;<i class="fa fa-share-square"></i>';
		$externalUrl = Extasy_Html::link_to_route($externalRoute, $externalIcon, 
				array('target' => '_blank', 'class' => 'title-tooltip noborder', 'title' => 'Перейти на страницу сайта'));
		
		return View::factory($this->_field_template, array(
			'align' => $this->_align,
			'value' => $editUrl . $externalUrl
		));
	}
}