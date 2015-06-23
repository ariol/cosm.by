<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Site_Index extends Controller_Site
{
    public function action_index()
    {
        $this->set_metatags_and_content('', 'page');
		$this->template->prodForMain = ORM::factory('Product')->fetchProdForMain(8)->as_array();
		$this->template->cart = Session::instance()->get('data');
		$this->template->promo = ORM::factory('Promo')->fetchMain()->as_array();
		$this->template->slider = ORM::factory('Slider')->where('active', '=', 1)->order_by('position', 'ASC')->limit(6)->find_all();
    }

}
