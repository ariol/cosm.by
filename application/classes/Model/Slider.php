<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Model_Slider extends ORM
{
    protected $_table_name = 'slider';

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
        return new Form_Admin_Slider($this);
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
			'route_str' => 'admin-slider:edit?id=${id}',
			'title' => '<i class="fa fa-edit"></i>',
			'color' => 'green',
			'alternative' => 'Редактировать'
		),
        'delete' => array(
            'width' => '40',
            'type' => 'link',
            'route_str' => 'admin-slider:delete?id=${id}',
            'title' => '<i class="fa fa-trash-o"></i>',
            'alternative' => 'Удалить',
			'color' => 'red',
            'confirm' => 'Вы уверены?'
        )
    );
	
	public function get_image_thumb()
	{
		return HTML::image(Lib_Image::crop($this->image, 'slider', $this->id, 220, 120));
	}
}
