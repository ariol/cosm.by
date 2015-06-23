<?php if ($page_view == 'list') {
		echo View::factory('site/category/category_list', array(
			'brand' => $brand, 
			'line' => $line, 
			'property' => $property, 
			'category' => $category, 
			'category_url' => $category_url,
			'product' => $product,
			'pagination' => $pagination, 
			'mostImages' => $mostImages, 
			'price' => $price,
			'max_price' => $max_price,
			'min_price' => $min_price
		))->render();
     }  elseif ($page_view == 'grid') {;
        echo View::factory('site/category/category_grid', array(
        	'brand' => $brand, 
			'line' => $line, 
			'property' => $property, 
			'category' => $category, 
			'category_url' => $category_url,
			'product' => $product,
			'pagination' => $pagination, 
			'mostImages' => $mostImages, 
			'price' => $price,
			'max_price' => $max_price,
			'min_price' => $min_price
		))->render();
 } ?>

