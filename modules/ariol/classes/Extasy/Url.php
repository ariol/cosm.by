<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Url extends Kohana_URL
{
	static public function parse_get($uri)
	{
		if(strpos($uri, '?') === FALSE)
		{
			return array();
		}
		$params = explode('?', $uri, 2);
		$params = end($params);
		$params = explode('&', $params);
		$params_out = array();
		foreach($params as $param)
		{
			$param = explode('=', $param);
			if( !isset($param[1]) )
			{
				$param[1] = NULL;
			}
			$params_out[ $param[0] ] = $param[1];
		}
		
		return $params_out;
	}
	
	static public function strip_get($uri)
	{
		$uri = explode('?', $uri, 2);
		return $uri[0];
	}
	
	static public function route_name($route_str)
	{
		return Route::get_route_name($route_str);
	}
	
	static public function route_params($route_str)
	{
		return Route::get_route_params($route_str);
	}
	
	static public function url_to_route($route_str)
	{
		$route_name = self::route_name($route_str);
        $route_name = mb_strtolower($route_name);
		$route_params = self::route_params($route_str);
		return Route::get($route_name)->uri($route_params);
	}
}