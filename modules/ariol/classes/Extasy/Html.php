<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Html extends Kohana_HTML
{
	static public function link_to_route($route_str, $title = NULL, array $attributes = NULL, $protocol = NULL)
	{
		if( ! isset($attributes['title']))
		{
			$attributes['title'] = $title;
			$attributes['class'] = 'name';
		}
		
		return HTML::anchor(Extasy_Url::url_to_route($route_str), $title, $attributes, $protocol);
	}
}