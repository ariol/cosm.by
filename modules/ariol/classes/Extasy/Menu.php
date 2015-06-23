<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Menu
{
	static private $_instances = array();
	static private $_tpl;
	/**
	 * @param string $config
	 * @return Menu
	 */
	static public function instance($config = null, $tpl = null)
	{
		if( ! isset(self::$_instances[$config]) )
		{
			self::$_instances[$config] = new Menu($config);
		}
		self::$_tpl = $tpl;
		return self::$_instances[$config];
	}


	
	private $_config = NULL;

	private $_active = NULL;

	private $_active_id = NULL;

	protected function __construct($config)
	{
		$this->_config = Kohana::$config->load($config);
	}

	private function is_route_active($route_str)
	{
		return Navigation::instance()->crumbs()->has_route($route_str);
	}

	/**
	 * @param string $active
	 * @return Menu
	 */
	public function set_active($active)
	{
		$this->_active = $active;
		return $this;
	}

	public function __toString()
	{
		try
		{
			return (string) $this->render();
		}
		catch (Exception $e)
		{
			// Display the exception message
			Kohana_Exception::handler($e);

			return '';
		}
	}

	public function render()
	{
		$config = $this->_config->as_array();
		foreach($config[Application::NAME] as $name => $cfg)
		{
			if(!is_array($cfg) && !ACL::is_route_allowed($cfg)) {
				unset($config[Application::NAME][$name]);
			}
		}
		return View::factory('extasy/cms/'.self::$_tpl, array('menu' => $config[Application::NAME]));
		
	}

	protected function render_item( & $next_id, $name, $cfg, $parent)
	{
		$menu = '';
		if($parent == -1)
		{
			$menu .= Ext::menu_begin($name);
		}
		if(is_array($cfg))
		{
			$cur_id = $next_id;
			$cur_menu = '';
			if($parent != -1)
			{
				$cur_menu .= Ext::menu_row($next_id, $name, NULL, $parent);
			}
			$next_id++;
			$cur_menu_subitems = '';
			foreach( $cfg as $subname => $subcfg )
			{
				$cur_menu_subitems .= $this->render_item($next_id, $subname, $subcfg, $cur_id);
			}
			if( ! empty($cur_menu_subitems))
			{
				$menu .= $cur_menu.$cur_menu_subitems;
			}
		}
		else
		{
			if (ACL::is_route_allowed($cfg))
			{
				$menu .= Ext::menu_row($next_id, $name, $cfg, $parent);
				if($this->is_route_active($cfg))
				{
					$this->_active_id = $next_id;
				}
			}
			$next_id++;
		}

		if($parent == -1)
		{
			$menu .= Ext::menu_end($this->_active_id);
		}
		return $menu;
	}
}
