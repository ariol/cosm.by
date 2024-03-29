<?php if ($page_view == 'list') {
    echo View::factory('site/brand/brand_list', array(
        'brand' => $brand,
        'line' => $line,
        'category' => $category,
        'property' => $property,
        'product' => $product,
        'pagination' => $pagination,
        'mostImages' => $mostImages,
        'price' => $price,
        'max_price' => $max_price,
		'filters' => $filters, 
        'min_price' => $min_price,
		'current_max_price' => $current_max_price,
		'current_min_price' => $current_min_price
    ))->render();
}  elseif ($page_view == 'grid') {;
    echo View::factory('site/brand/brand_grid', array(
        'brand' => $brand,
        'line' => $line,
        'category' => $category,
        'property' => $property,
        'product' => $product,
        'pagination' => $pagination,
        'mostImages' => $mostImages,
        'price' => $price,
		'filters' => $filters, 
        'max_price' => $max_price,
        'min_price' => $min_price,
		'current_max_price' => $current_max_price,
		'current_min_price' => $current_min_price
    ))->render();
} ?>