<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Controller_Site_Page extends Controller_Site
{
    public function action_index()
    {
		$url = $this->param('url');
		if(preg_match('|.html|', arr::get($_SERVER, 'REQUEST_URI'))) {
			$url .= '.html';
		}
		
		$this->set_metatags_and_content($url);
    }
}