<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Navigation_Actions extends Navigation_Widget
{
	public $_actions = array();

	public function add($route, $title, $first = FALSE)
	{
		$action = array(
			'route' => $route,
			'title' => $title
		);

		if ($first)
		{
			array_unshift($this->_actions, $action);
		}
		else
		{
			$this->_actions[] = $action;
		}

		return $this;
	}

	public function render()
	{
		foreach ($this->_actions as $key => $cfg)
		{
			if ( ! ACL::is_route_allowed($cfg['route']))
			{
				unset($this->_actions[$key]);
			}
		}


		return Ext::actions($this->_actions);
	}

	public function clear()
	{
		$this->_actions = array();
		return $this;
	}
}