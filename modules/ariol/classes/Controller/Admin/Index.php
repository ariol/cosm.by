<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Controller_Admin_Index extends Controller_Admin
{
	public function action_index()
	{
        $this->redirect('/'.Extasy_Url::url_to_route(Kohana::$config->load('extasy.redirect_from_index')));
	}
}