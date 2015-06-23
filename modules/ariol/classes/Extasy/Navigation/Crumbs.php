<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Navigation_Crumbs extends Navigation_Widget
{
	protected $_crumbs = array();
	
	public function render()
	{
		foreach ($this->_crumbs as $key => $cfg)
		{
			if ( ! ACL::is_route_allowed($cfg['route']))
			{
				unset($this->_crumbs[$key]);
			}
		}
		
		return Ext::crumbs($this->_crumbs);
	}
	
	public function unshift($route, $title)
	{
		array_unshift($this->_crumbs, array(
			'route' => $route,
			'title' => $title
		));
	}
	
	public function add($route, $title)
	{
		$this->_crumbs[] = array(
			'route' => $route,
			'title' => $title
		);
	}
	
	public function has_route($route)
	{
		foreach ($this->_crumbs as $crumb)
		{
			if ($crumb['route'] == $route)
			{
				return TRUE;
			}
		}
		return FALSE;
	}
}