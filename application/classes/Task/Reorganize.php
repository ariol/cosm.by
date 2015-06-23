<?php defined('SYSPATH') or die('No direct script access.');

class Task_Reorganize extends Minion_Task
{
    protected $_options = array(
		'force'
    );
	
    protected function _execute(array $params)
    {
		if (!Arr::get($params, 'force')) {
			exit();
		}
		
		header('Content-Type: text/html; charset=utf-8');

		set_time_limit (200);
		ini_set('auto_detect_line_endings', TRUE);
		
		$handle = fopen(DOCROOT . 'var/price/EXP.csv', "r+");
		
		$line = 0;
		$i = 0;
		$images = 0;
		
		Kohana::$config->load('price')->set('update', 0);
		
		$last_category = '';
		$last_section = '';
		
		$section_id = 0;
		$category_id = 0;
		
		$PDO = ORM::factory('Product')->PDO();
		
		while (!feof($handle)) {
			$matches = fgetcsv($handle, 0, ';');
		
			if (!$line) {
				$line++;
				continue;
			}
			
			if (!isset($matches[3])) {
				continue;
			}
			
			$unique_code = intval($matches[13]);
	
			$product = ORM::factory('Product', array('unique_code' => $unique_code));
		
			if ($product->id) {
				$categories_str = $matches[0];
				list($section, $category) = array_map(function($category) {return trim($category);}, explode('/', $categories_str));
				
				if ($section != $last_section) {
					$section_id = ORM::factory('Section')->selectInsert($section);
					$last_section = $section;
				}
				
				if ($category != $last_category) {
				
					$categoty_id = ORM::factory('Category')->selectInsert($category, $section_id);

					$orm_category = ORM::factory('Category', $categoty_id);
					$orm_category->section_id = $section_id;
					$orm_category->save();
					
					$last_category = $category;
				}
				
				$PDO->query("UPDATE product SET section_id='{$section_id}' WHERE unique_code='{$unique_code}'");
				$PDO->query("UPDATE categories SET section_id='{$section_id}' WHERE category_id='{$categoty_id}'");
				$PDO->query("INSERT IGNORE INTO categories_product (category_id, product_id) VALUES ('{$categoty_id}', '{$product->id}')");
			}
			unset($product);
		}
        fclose($handle);
	}
}