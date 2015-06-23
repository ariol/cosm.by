<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Ext
{
	static public function header($header)
	{
		return View::factory('cms/header', array(
			'header' => $header,
		));
	}
	
	static public function actions($actions)
	{
		$actions_out = array();
		if( ! is_array($actions))
		{
			$actions = array();
			for ($i = 0, $total = func_num_args(); $i < $total; $i++)
			{
				$actions[] = func_get_arg($i);
			}
		}
		foreach($actions as $action)
		{
			if(is_array($action))
			{
				$actions_out[] = array(
					'route' => Arr::get($action, 'route'),
					'title' => Arr::get($action, 'title')
				);
			}
			else
			{
				$actions_out[] = array(
					'route' => $action,
					'title' => NULL
				);
			}
		}
		
		return View::factory('cms/actions', array(
			'actions' => $actions_out
		));
	}
	
	static public function actions_begin()
	{
		return View::factory('cms/actions_begin');
	}
	
	static public function actions_row($route, $title = NULL)
	{

		return View::factory('cms/actions_row', array(
			'route' => Extasy_Url::url_to_route($route),
			'title' => $title
		));
	}
	
	static public function actions_end()
	{
		return View::factory('cms/actions_end');
	}
	
	static public function buttons($buttons)
	{
		return View::factory('cms/buttons', array(
			'buttons' => $buttons
		));
	}
	
	static public function submit($name, $value, $confirm = NULL, $postAttributes = array())
	{
		if (!isset($postAttributes['class'])) {
			$postAttributes['class'] = '';
		}
		
		$attributes['class'] = $postAttributes['class'] . ' btn SaveButton';
	
		$attributes['style'] = 'float: none;';
		
		if($confirm) {
			$attributes['class'] .= ' btn-delete';
		}
		
		return Form::button($name, $value, $attributes);
	}
	
	static public function buttons_begin()
	{
		return View::factory('cms/buttons_begin');
	}
	
	static public function buttons_end()
	{
		return View::factory('cms/buttons_end');
	}
	
	static public function table_begin()
	{
		return View::factory('cms/table_begin');
	}
	
	static public function table_row_begin()
	{
		static $i = 0;
		return View::factory('cms/table_row_begin', array(
			'num' => $i++
		));
	}
	
	static public function table_row_end()
	{
		return View::factory('cms/table_row_end');
	}
	
	static public function table_header_begin()
	{
		return View::factory('cms/table_header_begin');
	}
	
	static public function table_header_end()
	{
		return View::factory('cms/table_header_end');
	}
	
	static public function table_end()
	{
		return View::factory('cms/table_end');
	}
	
	static public function form_begin($action = NULL, $attributes = array())
	{
		if( ! isset($attributes['enctype']))
		{
			$attributes['enctype'] = 'multipart/form-data';
		}
		return View::factory('cms/form_begin', array(
			'action' => $action,
			'attributes' => $attributes
		));
	}
	
	static public function form_row($input = NULL, $label = NULL, $error = NULL)
	{
		return View::factory('cms/form_row', array(
			'input' => $input,
			'label' => $label,
			'error' => $error
		));
	}
	
	static public function form_fields_begin()
	{
		return View::factory('cms/form_fields_begin');
	}
	
	static public function form_fields_end()
	{
		return View::factory('cms/form_fields_end');
	}
	
	static public function form_end()
	{
		return View::factory('cms/form_end');
	}
	
	static public function spacer()
	{
		return View::factory('cms/spacer');
	}
	
	static public function menu_begin($root)
	{
		return View::factory('cms/menu_begin', array(
			'root' => $root
		));
	}
	
	static public function menu_row($id, $name, $route = NULL, $parent = -1)
	{
		if ( ! is_null($route) )
		{
			$url = url::base().Extasy_Url::url_to_route($route);
		}
		else
		{
			$url = NULL;
		}
		$title = $name;

		return View::factory('cms/menu_row', array(
			'id' => $id,
			'name' => $name,
			'title' => $title,
			'url' => $url,
			'parent' => $parent
		));
	}
	
	static public function menu_end($open_id = NULL)
	{
		return View::factory('cms/menu_end', array(
			'open_id' => $open_id
		));
	}
	
	static public function crumbs(array $crumbs = array())
	{
		$links = array();
		$crumbs_out = array();
		foreach($crumbs as $crumb)
		{
			if($link = Extasy_Html::link_to_route($crumb['route'], $crumb['title']))
			{
				$links[] = $link;
				$crumbs_out[] = $crumb;
			}
		}
		return View::factory('cms/crumbs', array(
			'links' => $links,
			'crumbs' => $crumbs_out
		));
	}
}