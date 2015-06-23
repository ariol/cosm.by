<?php defined('SYSPATH') or die('No direct script access.');
class Controller_Site_Discount extends Controller_Site{    public function action_index()
    {
        $this->set_metatags_and_content('discount');
		$this->template->set_layout('layout/site/global_inner');
    }	
	public function action_item()
    {
        $this->set_metatags_and_content($this->param('url'), 'discount');
		$this->template->set_layout('layout/site/global_inner');
    }
}