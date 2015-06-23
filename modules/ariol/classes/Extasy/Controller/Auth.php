<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

abstract class Extasy_Controller_Auth extends Extasy_Controller
{
	private $_user = NULL;

	protected $_login_route = NULL;

	protected function user()
	{
		return $this->_user;
	}

	public function before()
	{
		parent::before();

		if ( ! ACL::is_action_allowed(
			$this->request->directory(),
			$this->request->controller(),
			$this->request->action()
		))
		{
			$this->on_auth_error();
		}

		$this->_user = Auth::instance()->get_user();
	}

	protected function on_auth_error()
	{
		if ( ! is_null($this->_login_route) AND ! Auth::instance()->logged_in())
		{
			HTTP::redirect(Extasy_Url::url_to_route($this->_login_route).'?return='.$this->request->uri());
		}
		$this->forward_403();
	}
}