<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

abstract class Extasy_Controller extends Kohana_Controller
{
	protected $template = null;

	private $_view = null;

	private $_return_location = NULL;

	public function before()
	{
		$this->_view = $this->request->action();

		$this->template = View::factory();

		if($this->request->is_initial())
		{
			$layout = Kohana::$config->load('layout')->admin;
			$this->template->set_layout($layout);
		}

		$this->_return_location = arr::get(
			$_POST,
			'return_location',
			arr::get(
				$_GET,
				'return_location',
				arr::get(
					$_SERVER,
					'HTTP_REFERER'
				)
			)
		);

		$this->template->return_location = $this->_return_location;
	}

	protected function param($key)
	{
		return $this->request->param($key);
	}

	protected function forward_403()
	{
		throw new HTTP_Exception_403();
	}

	protected function forward_404()
	{
		throw new HTTP_Exception_404();
	}

	protected function set_view($view)
	{
		$this->_view = $view;
	}

	public function after()
	{
		$this->template->set_filename(mb_strtolower($this->_view));

		$response = (string) $this->template;

		$this->response->body($response);
	}

	public function back($default_route)
	{
		$url = $this->_return_location;
		if (is_null($url))
		{
			$url = Extasy_Url::url_to_route($default_route);
		}
		HTTP::redirect($url);
	}
}