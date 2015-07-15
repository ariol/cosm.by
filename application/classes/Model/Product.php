<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Model_Product extends ORM
{
	const MIN_WIDTH = 347;
	const MAX_WIDTH = 347;
	
    protected $_table_name = 'product';

	protected $_belongs_to  = array(
		'brand'    => array(
			'model'=> 'Brand',
			'foreign_key' => 'brand_id'
		),
        'category'    => array(
            'model'=> 'Category',
            'foreign_key' => 'category_id'
        )
	);
	

    protected $_grid_columns = array(
		'name' => array(
            'type' => 'name',
			'route_str' => 'admin-product:edit?id=${id}',
			'external_url' => 'site-product:index?product=${url}&category=${category.url}'
        ),
        'article' => null,
        'brand_id' => array(
            'model'=> 'Brand',
            'foreign_key' => 'brand_id'
        ),
        'price' => null,
        'purchase_price' => null,
        'active' => array(
            'type' => 'bool'
        ),

        'create_date' => null,
		'edit' => array(
			'width' => '40',
			'type' => 'link',
			'route_str' => 'admin-product:edit?id=${id}',
			'title' => '<i class="fa fa-edit"></i>',
			'color' => 'green',
			'alternative' => 'Редактировать'
		),
        'delete' => array(
            'width' => '40',
            'type' => 'link',
            'route_str' => 'admin-product:delete?id=${id}',
            'title' => '<i class="fa fa-trash-o"></i>',
            'alternative' => 'Удалить',
			'color' => 'red',
            'confirm' => 'Вы уверены?'
        )
    );

    public function labels()
    {
        return array(
			'name' => 'Название товара',
            'article' => 'Артикул',
			'url' => 'URL',
			'top' => 'Спец предложение',
			'important' => 'На главной',
			'main_image' => 'Главное изображение',
			'more_images' => 'Дополнительные изображения',
			'comments_enabled' => 'Отзывы разрешены',
			'create_date' => 'Дата создания',
			'update_date' => 'Дата обновления',
			'price' => 'Цена продажи',
			'new_price' => 'Цена со скидкой',
			'content' => 'Описание',
			'short_content' => 'Краткое описание',
			'active' => 'Активность',
			'brand_id' => 'Производитель',
			'category_id' => 'Категория',
            'purchase_price' => 'Цена закупки',
            'color' => 'Цвета',
            'line_id' => 'Линия',
        );
    }

	public function save(Validation $validation = NULL)
	{	
		$this->md5_url = md5($this->url);
		if (!$this->create_date) {
			$this->create_date = date('Y-m-d H:i:s');
		}

		$this->update_date = date('Y-m-d H:i:s');
		$this->updated_at = date('Y-m-d H:i:s');

		return parent::save($validation);
	}

    public function form()
    {
        return new Form_Admin_Product($this);
    }

    protected $_grid_options = array(
        'order_by' => 'id',
        'order_direction' => 'DESC'
    );

	public function fetchLast($limit)
	{
		if($limit > 0){
			return $this->where('product.active', '=', 1)->order_by('product.update_date', 'DESC')->limit($limit)->find_all();
		} else {
			return array();
		}
		
	}
	
	public function getSiteUrl()
	{
		return '/' . $this->category->url . '/' .$this->url;
	}
	

	public function getCountImages()
	{
		$count_images = count(json_decode($this->more_images));
		
		if ($this->main_image) {
			$count_images++;
		}
		
		return $count_images;
	}

	public function fetchProdForMain($limit)	{
		return $this->where('product.active', '=', 1)
            ->with('category')
            ->where('category.active', '=', 1)
            ->where('product.parent_product', '=', '')
			->where('product.important', '=', 1)
			->order_by('product.update_date', 'DESC')->limit($limit)->find_all();
	}

    public function fetchProdSpecial($id)
    {
        return $this->where('product.active', '=', 1)
            ->where('product.top', '=', 1)
            ->where('product.category_id', '=', $id)
            ->order_by('product.update_date', 'DESC')->limit(1)->find_all();
    }

    public function fetchProdVolume($id)
    {
        return $this->where('product.active', '=', 1)
            ->where( 'product.parent_product', '=', $id)
            ->order_by('product.update_date', 'DESC')->find_all();
    }
    public function fetchProdChildVolume($id)
    {
        return $this->where('product.active', '=', 1)
            ->where( 'product.id', '=', $id)
            ->order_by('product.update_date', 'DESC')->find_all();
    }

    public function fetchProdNewUrl($id)
    {
        return $this->where('product.active', '=', 1)
            ->where( 'product.id', '=', $id)
            ->find();
    }

	public function fetchProdById($id)
	{
		return $this->where('product.active', '=', 1)
			->where('product.id', '=', $id)->find();
	}
	public function fetchProdRelated($category, $id, $line_id)
	{
		return $this->where('product.category_id', '=', $category)
            ->where( 'product.line_id', '=', $line_id)
            ->where( 'product.parent_product', '=', '')
            ->where( 'product.id', '!=', $id)
            ->where('product.active', '=', 1)
            ->order_by('product.update_date', 'DESC')
            ->limit(4)->find_all();
	}

	public function get_size($index)
	{
		if (!$index || $this->important) {
			return self::MAX_WIDTH;
		}

		return self::MIN_WIDTH;
	}

	public function fetchCountByBrandId($brand_id, $price_start = 0, $price_end = 0)
	{
		$products = ORM::factory('Brand', $brand_id)->with('product')->where('product.active', '=', 1);
		$products = $products->where('product.price', '>=', $price_start);
		$products = $products->where('product.price', '<=', $price_end);
		
		return $products->product->count_all();
	}
	
	public function fetchProdByBrand($limit, $offset, $brand_id, $price_start = 0, $price_end = 0)
	{
		$products = ORM::factory('Brand', $brand_id)->product
			->where('product.active', '=', 1)
			->where('product.price', '>=', $price_start)
			->where('product.price', '<=', $price_end);
		return $products->limit($limit)->offset($offset)->find_all();
	}
	
//	public function fetchProdByPrice($limit, $offset,  $price_start = 0, $price_end = 0)
//	{
//		$products = ORM::factory('Product')->where('product.active', '=', 1)->with('section');
//		$products = $products->where('product.price', '>=', $price_start);
//		$products = $products->where('product.price', '<=', $price_end);
//
//		return $products->limit($limit)->offset($offset)->find_all();
//	}
	
	public function fetchCountByPrice($limit, $offset,  $price_start = 0, $price_end = 0)
	{
		$products = ORM::factory('Product')->where('product.active', '=', 1);
		$products = $products->where('product.price', '>=', $price_start);
		$products = $products->where('product.price', '<=', $price_end);
		
		return $products->count_all();
	}
	
	public function insertSelect(ORM $product, $productData)
	{
		if (!$product->id) {
			$product = ORM::factory('Product');
		}

        foreach ($productData as $field => $value) {
            $product->{$field} = $value;
        }
		if (!$product->created_at) {
			$product->created_at = date('Y-m-d H:i:s');
		}
        $product->save();

		return $product;
	}

    public function getPriceValue($id)
    {
        $price =  $this->where('product.id', '=', $id)->find();
        if($price->new_price)
            $price = $price->new_price;
        else
            $price = $price->price;
        return $price;
    }
}