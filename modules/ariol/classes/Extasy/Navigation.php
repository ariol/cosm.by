<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 *
 * @author m00t
 *
 * XXX: WARNING! Code dublicates detected. Refactor needs. m00t.
 */

class Extasy_Navigation
{
	private $_crumbs = NULL;
	private $_actions = NULL;
	private $_title = NULL;
	private $_places = NULL;

	private $_config = NULL;

	private $_cur_item = array();
	private $_cur_id = NULL;

	static private $_instance = NULL;

	/**
	 * @return Navigation
	 */
	static public function instance()
	{
		if(is_null(self::$_instance))
		{
			self::$_instance = new Navigation();
		}
		return self::$_instance;
	}

	protected function __construct()
	{
		$this->_actions = new Navigation_Actions();

		$this->_config = Kohana::$config->load('navigation');
		$directory = Request::initial()->directory();
		$controller = Request::initial()->controller();
		$action = Request::initial()->action();
		$path = $controller.'.'.$action;

		if($directory)
		{
			$path = $directory.'.'.$path;
		}

        $path = mb_strtolower($path);

		$this->_cur_item = $this->_config->get($path, array());

		$this->_cur_id = Request::initial()->param(Arr::get($this->_cur_item, 'id_key', 'id'), NULL);
	}

	/**
	 * @return Navigation_Crumbs
	 */
	public function crumbs()
	{
		if(is_null($this->_crumbs))
		{
			$this->_crumbs = new Navigation_Crumbs();
			$item = $this->_cur_item;
			$id = $this->_cur_id;
			while($item)
			{
				$title = Arr::get($item, 'title', '');
				$route = Arr::get($item, 'route', '');
				if($model = Arr::get($item, 'model', FALSE) AND $id)
				{
					$obj = array('model'=>ORM::factory($model, $id));
					$route = Extasy::obj_placeholders($obj, $route);
					$title = Extasy::obj_placeholders($obj, $title);
				}
				$this->_crumbs->unshift($route, $title);

				$parent = Arr::get($item, 'parent', FALSE);
				$parent = $this->_config->get($parent, FALSE);
				if($id AND $link = Arr::get($item, 'link_to_parent', FALSE))
				{
					$id = Extasy::obj_placeholders($obj, $link);
				}
				$item = $parent;
			}
		}

		return $this->_crumbs;
	}

	/**
	 * @return Navigation_Actions
	 */
	public function actions()
	{
		return $this->_actions;
	}

	/**
	 * @return Navigation_Title
	 */
	public function title()
	{
		if(is_null($this->_title))
		{
			$this->_title = new Navigation_Title();
			$title = Arr::get($this->_cur_item, 'title', '');
			if($model = Arr::get($this->_cur_item, 'model', FALSE) AND $id = $this->_cur_id)
			{
				$title = Extasy::obj_placeholders(array('model'=>ORM::factory($model, $id)), $title);
			}
			$this->_title->set($title);
		}

		return $this->_title;
	}

	/**
	 * @return Navigation_Places
	 */
	public function places()
	{
		if(is_null($this->_places))
		{
			$this->_places = new Navigation_Places();
			/*
			if($parent = Arr::get($this->_cur_item, 'parent', FALSE) AND $parent_item = $this->_config->get($parent, FALSE))
			{
				$parent_route = Arr::get($parent_item, 'route', FALSE);
				$parent_title = Arr::get($parent_item, 'title', 'Назад');
				if($parent_route)
				{
					$this->_places->add($parent_route, $parent_title);
				}
			}
			*/
		}
		return $this->_places;
	}
}