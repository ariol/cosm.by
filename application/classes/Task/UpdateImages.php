<?php defined('SYSPATH') or die('No direct script access.');

class Task_UpdateImages extends Minion_Task
{

    protected $_options = array(
    );

    protected function _execute(array $params)
    {
		set_time_limit (200);
		ini_set('auto_detect_line_endings', TRUE);
		
		$product = ORM::factory('Product')->where('main_image', 'IS', NULL)
					->where('empty', '=', 0)->find();
		
		if ($product->id) {
			$result = $this->downloadImage($product->original, $product->id);
			
			if ($result) {
				$product->main_image = $result;
				if ($product->price) {
					$product->active = 1;
				}
			}
			$product->update();
		}
	}
	
	private function downloadImage($image_url, $product_id)
	{
		$dir = PUBLIC_ROOT . 'files/product/' . $product_id;
	
		if (!is_dir($dir)) {
			mkdir($dir);
		}
		
		$fileParts = explode('.', $image_url);
		$filename = md5($image_url . microtime());
		
		if (count($fileParts) > 1) {
			$ext = end($fileParts);
			$filename .= '.' . $ext;
		}
		

	}
}