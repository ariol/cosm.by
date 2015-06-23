<?php defined('SYSPATH') or die('No direct script access.');
class Task_DisableCategories extends Minion_Task
{
    protected $_options = array(
    );	
	
    protected function _execute(array $params)
    {
		DB::update('product')->set(array('active' => 0))->where('main_image', 'IS', NULL)->execute();
		DB::update('product')->set(array('active' => 0))->where('main_image', '=', '')->execute();		
		
		$categories = ORM::factory('Category')->find_all();		
		foreach ($categories as $category) {			
			$products_count = ORM::factory('Product')->where('category_id', '=', $category->id)->where('active', '=', 1)->count_all();		
			if (!$products_count) {					
				$category->active = 0;				
				$category->save();				
			} else {				
				$category->active = 1;			
				$category->save();			
			}		
		}
		$sections = ORM::factory('Section')->find_all();
		foreach ($sections as $section) {
			$categories = ORM::factory('Category')->where('active', '=', 1)->where('section_id', '=', $section->id)->count_all();
			$section->active = !!$categories;
			$section->save();
		}
	}
}