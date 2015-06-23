<?php defined('SYSPATH') or die('No direct script access.');

class Model_Brand_Product extends ORM
{
    protected $_table_name = 'brand_product';
	
	public function insertRelation($product_id, $brand_id)
	{
		$relation = ORM::factory('Brand_Product')
			->where('product_id', '=', $product_id)
			->where('brand_id', '=', $brand_id)
			->find();
			
		if (!$relation->product_id) {
			$relation = ORM::factory('Brand_Product');
			$relation->product_id = $product_id;
			$relation->brand_id = $brand_id;
			$relation->save();
		}
	}
}