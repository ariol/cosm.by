<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

abstract class Extasy_ACL extends Acl_Core
{
	static public function is_action_allowed($directory, $controller, $action)
	{
		if (Kohana::$profiling === TRUE AND class_exists('Profiler', FALSE))
		{
			// Start a new benchmark
			$benchmark = Profiler::start('ACL', __FUNCTION__);
		}

		$allowed = ACL::instance()->_is_action_allowed($directory, $controller, $action);

		if (isset($benchmark))
		{
			// Stop the benchmark
			Profiler::stop($benchmark);
		}

		return $allowed;
	}

	static public function is_route_allowed($route_str)
	{
		if (Kohana::$profiling === TRUE AND class_exists('Profiler', FALSE))
		{
			// Start a new benchmark
			$benchmark = Profiler::start('ACL', __FUNCTION__);
		}

        $route_name = Route::get_route_name($route_str);
        $route_name = mb_strtolower($route_name);
        $route_params = Route::get_route_params($route_str);

        $uri = Route::get($route_name)->uri($route_params);
        $request = Request::factory($uri);

        $matched_params = Route::get($route_name)->matches($request);

		$allowed = self::is_action_allowed(
            arr::get($matched_params, 'directory'),
            arr::get($matched_params, 'controller'),
			arr::get($matched_params, 'action')
		);

		if (isset($benchmark))
		{
			// Stop the benchmark
			Profiler::stop($benchmark);
		}

		return $allowed;
	}

	private $_user_roles = array();

	final protected function __construct()
	{
		if (Auth::instance()->logged_in())
		{
			$user = Auth::instance()->get_user();
			foreach ($user->get_roles_list() as $role)
			{
				$this->_user_roles[] = $role;
			}
		}
		else
		{
			$this->_user_roles[] = 'guest';
		}

		$this->add_role('guest');
		$this->add_role('logged', 'guest');
		$this->add_role('admin', 'logged');

		$this->add_resource('admin');
		$this->add_resource('admin_auth', 'admin');
		$this->add_resource('admin_index', 'admin');

		$this->allow('guest', 'admin_auth', array('login', 'block', 'reset_password_step_1', 'reset_password_step_1_thankyou', 'reset_password_step_2'));
		$this->deny('logged', 'admin_auth', array('login', 'reset_password_step_1', 'reset_password_step_2'));
		$this->allow('logged', 'admin_auth', array('logout', 'change_password'));

		$this->allow('admin', 'admin_index');

		$this->allow('admin', 'admin');

		$this->init();
	}

	private function _is_action_allowed($directory, $controller, $action)
	{
		$resource = array();
		if ( ! empty($directory))
		{
			$resource[] = $directory;
		}
		else
		{
			$directory = NULL;
		}

		$resource[] = $controller;

		$resource = strtolower(implode('_', $resource));

		if ( ! is_null($directory) AND ! $this->has_resource($directory))
		{
			$this->add_resource($directory);
		}

		if ( ! $this->has_resource($resource))
		{
			$this->add_resource($resource, $directory);
		}

		foreach ($this->get_user_roles() as $role)
		{
			if ( ! $this->has_role($role))
			{
				$this->add_role($role);
			}
		}

		return $this->is_allowed(
			$this->get_user_roles(),
			$resource,
			$action
		);
	}

	protected function add_user_role($role)
	{
		$this->_user_roles[] = $role;
	}

	protected function remove_user_role($role)
	{
		foreach ($this->_user_roles as $key => $cur_role)
		{
			if ($cur_role === $role)
			{
				unset($this->_user_roles[$key]);
			}
		}
	}

	protected function get_user_roles()
	{
		return $this->_user_roles;
	}

	abstract protected function init();
}