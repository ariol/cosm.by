<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Route extends Kohana_Route
{
	private $_name = NULL;

	static public function get_route_name($route_str)
	{
		$route_str = Extasy_Url::strip_get($route_str);
		if(strpos($route_str, ':') !== FALSE)
		{
			$route_arr = explode(':', $route_str);
			return $route_arr[0];
		}
		else
		{
			return $route_str;
		}
	}

	public static function set($name, $uri_callback = NULL, $regex = NULL)
	{
		$route = new Route($uri_callback, $regex);
		$route->set_name($name);
		return Route::$_routes[$name] = $route;
	}

	public function set_name($name)
	{
		$this->_name = $name;
		return $this;
	}

	public function get_name()
	{
		return $this->_name;
	}

	static public function get_route_params($route_str)
	{
		$route_params = Extasy_Url::parse_get($route_str);
		$route_str = Extasy_Url::strip_get($route_str);

		if(strpos($route_str, ':') !== FALSE)
		{
			$route_arr = explode(':', $route_str);
			$route_name = $route_arr[0];
			$route_target = $route_arr[1];

			$route_target = explode('/', $route_target);
			$params['action'] = array_pop($route_target);
			if($controller = array_pop($route_target))
			{
				$params['controller'] = $controller;
			}
			if($directory = array_pop($route_target))
			{
				$params['directory'] = $directory;
			}
			return Arr::merge($params, $route_params);
		}
		else
		{
			return $route_params;
		}
	}

	public function get_route_str($params)
	{
		$route = $this->get_name();
		if( ! empty($params))
		{
			$route .= '?'.http_build_query($params, '', '&');
		}
		return $route;
	}
}