<?php if ($page_view == 'list') {
    echo View::factory('site/line/line_list', array(
        'brand' => $brand,
        'line' => $line,
        'line_brand' => $line_brand,
        'category' => $category,
        'property' => $property,
        'product' => $product,
        'pagination' => $pagination,
        'price' => $price,
        'max_price' => $max_price,
		'filters' => $filters, 
		'lines' => $lines,
        'min_price' => $min_price,
		'current_max_price' => $current_max_price,
		'current_min_price' => $current_min_price
    ))->render();
}  elseif ($page_view == 'grid') {
    echo View::factory('site/line/line_grid', array(
        'brand' => $brand,
        'line' => $line,
        'line_brand' => $line_brand,
        'category' => $category,
        'property' => $property,
        'product' => $product,
        'pagination' => $pagination,
        'price' => $price,
        'max_price' => $max_price,
		'lines' => $lines,
		'filters' => $filters, 
        'min_price' => $min_price,
		'current_max_price' => $current_max_price,
		'current_min_price' => $current_min_price
    ))->render();
} ?>