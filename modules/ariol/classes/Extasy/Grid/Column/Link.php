<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Grid_Column_Link extends Grid_Column_Template
{
	protected $_route_str = NULL;
	protected $_title = NULL;
	protected $_confirm = NULL;

	public function __construct(array $column = array())
	{
		parent::__construct($column);

		$this->_route_str = Arr::get($column, 'route_str');
		$this->_color = Arr::get($column, 'color');
		$this->_title = Arr::get($column, 'title');
		$this->_confirm = Arr::get($column, 'confirm');
		$this->_alternative = Arr::get($column, 'alternative');
	}

	protected function _draw_field($obj)
	{
		$route_str = Extasy::obj_placeholders($obj, $this->_route_str);
		$title = Extasy::obj_placeholders($obj, $this->_title);
		$alternative = Extasy::obj_placeholders($obj, $this->_alternative);
		$color = Extasy::obj_placeholders($obj, $this->_color);
		$attributes = array();
		
		if($this->_confirm)
		{
			$attributes['onclick'] = 'javascript: return confirm("'.Extasy::obj_placeholders($obj, $this->_confirm).'")';
		}
		
		$attributes['class'] = 'btn btn-default';
		
		if($color=='red'){
			$attributes['class'] = 'btn btn-danger';
		}
		
		if($color=='green'){
			$attributes['class'] = 'btn btn-success';
		}
		
		if($color=='blue'){
			$attributes['class'] = 'btn btn-primary';
		}
		
		$attributes['data-toggle'] = 'tooltip';
		$attributes['data-placement'] = 'top';
		$attributes['title'] = $alternative;

		return '<td align="'.$this->_align.'">'.Extasy_Html::link_to_route($route_str, $title, $attributes).'</td>';
	}

	protected function _field($obj)
	{
		$route_str = Extasy::obj_placeholders($obj, $this->_route_str);

		if ( ! ACL::is_route_allowed($route_str))
		{
			return '<td></td>';
		}

		return $this->_draw_field($obj);
	}
}