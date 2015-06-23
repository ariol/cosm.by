<?php if ($page_view == 'list') {
    echo View::factory('site/category/brand_list', array('brands' => $brands, 'brand' => $brand, 'category' => $category, 'category_url' => $category_url, 'product' => $product, 'pagination' => $pagination, 'price' => $price))->render();
}  elseif ($page_view == 'grid') {;
    echo View::factory('site/category/brand_grid', array('brands' => $brands,'brand' => $brand, 'category' => $category, 'category_url' => $category_url, 'product' => $product, 'pagination' => $pagination, 'price' => $price))->render();
} ?>