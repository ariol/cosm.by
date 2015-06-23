<?php defined('SYSPATH') or die('No direct script access.');

class Task_Index extends Minion_Task
{
    protected $_options = array(
    );
	
    protected function _execute(array $params)
    {
        $products = ORM::factory('Product')->limit(200)->find_all();

		Search::instance()->build_search_index($products);
	}
}