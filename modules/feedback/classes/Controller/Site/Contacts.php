<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Site_Contacts extends Controller_Site
{
    public function action_index()
    {
        $this->set_metatags_and_content('feedback');
		$this->template->set_layout('site/global_inner');
    }
}
