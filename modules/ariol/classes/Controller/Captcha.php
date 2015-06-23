<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Controller_Captcha extends Controller
{
	public function action_index()
	{
		Captcha::render();
	}
}