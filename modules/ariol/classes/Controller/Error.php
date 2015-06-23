<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Controller_Error extends Controller
{
	public function before()
	{
		parent::before();
		$this->template->set_layout('error/global');
		$this->template->title = '';
	}

	public function action_index()
	{
		$code = $this->request->param('code');
		$message = arr::get(Response::$messages, $code);
		$this->template->title = sprintf('Error %s: %s', $code, $message);
		$this->response->status($code);
	}
}