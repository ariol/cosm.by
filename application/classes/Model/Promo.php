<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Model_Promo extends ORM
{
    protected $_table_name = 'banners';

    public function labels()
    {
        return array(
			'href' => 'Ссылка',
			'active' => 'Активность',
			'position' => 'Позиция',
			'image' => 'Изображение',
			'created_at' => 'Дата добавления',
        );
    }
	
	public function form()
    {
        return new Form_Admin_Promo($this);
    }
	
	protected $_grid_columns = array(
		'position' => null,
		'active' => 'bool',
		'image' => array(
			'type' => 'template',
			'template' => '${image_thumb}'
		),
		'edit' => array(
			'width' => '40',
			'type' => 'link',
			'route_str' => 'admin-promo:edit?id=${id}',
			'title' => '<i class="fa fa-edit"></i>',
			'color' => 'green',
			'alternative' => 'Редактировать'
		),
        'delete' => array(
            'width' => '40',
            'type' => 'link',
            'route_str' => 'admin-promo:delete?id=${id}',
            'title' => '<i class="fa fa-trash-o"></i>',
            'alternative' => 'Удалить',
			'color' => 'red',
            'confirm' => 'Вы уверены?'
        )
    );
	public function get_image_thumb()
	{
		return HTML::image(Lib_Image::crop($this->image, 'promo', $this->id, 220, 120));
	}
	
	public function fetchMain()
	{
		return $this->where('active', '=', 1)->order_by('position', 'ASC')->limit(4)->find_all();
	}
}
