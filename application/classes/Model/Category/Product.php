<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Model_Category_Product extends ORM
{
    protected $_table_name = 'categories_product';
	protected $_primary_key = 'category_id';
	public function count_prod_id_cat($id)
    {
		return $this->where('category_id', '=', $id)->count_all();
    }		public function insertRelation($product_id, $category_id)	{		$relation = ORM::factory('Category_Product')			->where('product_id', '=', $product_id)			->where('category_id', '=', $category_id)			->find();					if (!$relation->product_id) {			$relation = ORM::factory('Category_Product');			$relation->product_id = $product_id;			$relation->category_id = $category_id;			$relation->save();		}	}
}


