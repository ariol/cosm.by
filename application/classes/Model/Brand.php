<?php defined('SYSPATH') or die('No direct script access.');

class Model_Brand extends ORM
{
    protected $_table_name = 'brand';
	
    protected $_has_many = array(
		'product' => array(
			'model' => 'Product',
            'through' => 'brand_product',
            'foreign_key' => 'brand_id',
            'far_key' => 'product_id',
		),
		'category' => array(
			'model' => 'Category',
            'through' => 'brand_category',
            'foreign_key' => 'brand_id',
            'far_key' => 'category_id',
		)
    );

    protected $_grid_columns = array(
        'name' => array(
            'type' => 'name',
			'route_str' => 'admin-brand:edit?id=${id}',
			'external_url' => 'site-brand:index?url=${url}'
        ),
        'active' => 'bool',
		'description' => array(
            'width' => '40%',
			'route_str' => 'admin-brand:edit?id=${id}',
            'type' => 'more'
        ),

        'delete' => array(
            'width' => '50',
            'type' => 'link',
			'color' => 'red',
            'route_str' => 'admin-brand:delete?id=${id}',
            'title' => '<i class="fa fa-trash-o"></i>',
			'color' => 'red',
            'alternative' => 'Удалить',
            'confirm' => 'Вы уверены?'
        )
    );

    public function labels()
    {
        return array(
			'name' => 'Название',
			's_title' => 'Seo title',
			's_description' => 'Seo description',
			's_keywords' => 'Seo keywords',
			'url' => 'URL',
			'category' => 'Участие в категориях',
			'active' => 'Активность',
			'position' => 'Позиция',
			'description' => 'Описание',
			'russian' => 'Название на русском'
        );
    }

    public function rules()
    {
        return array(
            'name' => array(
                array('not_empty')
            ),
        );
    }

	public function save(Validation $validation = NULL)
	{
		$this->md5_url = md5($this->url);

		return parent::save($validation);
	}

    public function form()
    {
        return new Form_Admin_Brand($this);
    }
	
	public function fetchWidthImage() {
		return $this->where('active', '=', true)
            ->where('main_image', '!=', '')
            ->order_by('position', 'ASC')->find_all()->as_array();
	}

	public function fetch_brand_by_url($url)
    {
        return $this->where('active', '=', true)
            ->where('url', '=', $url)
            ->find();
    }

	public function fetchBrandByCatId($id)
    {
        $category = ORM::factory('Category', $id);
		$products = $category->product;
		return $products->brands->find_all();
    }
	
	public function selectInsert($name)
	{
		$brand = ORM::factory('Brand')->where('name', '=', $name)->find();
		
		if (!$brand->loaded()) {
			$url = Helpers_Url::translit($name);
		
			$brand = ORM::factory('Brand');
			$brand->name = $name;
			$brand->url = $url;
			$brand->md5_url = md5($url);
			$brand->save();
		}
		
		return $brand->id;
	}
}

