<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Model_Page extends ORM
{
    protected $_table_name = 'pages';

    protected $_grid_columns = array(
        'name' => NULL,
        's_title' => 'empty',
        's_description' => 'empty',
        's_keywords' => 'empty',
        'url' => array(
            'type' => 'template',
            'template' => '${url_href}'
        ),
        'active' => 'bool',
        'updated_at' => NULL,
        'edit' => array(
			'width' => '50',
            'type' => 'link',
            'route_str' => 'admin-page:edit?id=${id}',
            'title' => '<i class="fa fa-pencil"></i>',
			'color' => 'green',
            'alternative' => 'Редактировать'
        ),
        'delete' => array(
            'width' => '50',
            'type' => 'link',
            'route_str' => 'admin-page:delete?id=${id}',
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
            's_title' => 'Title',
            's_description' => 'Description',
            's_keywords' => 'Keywords',
            'url' => 'ЧПУ',
            'active' => 'Статус',
            'updated_at' => 'Последнее обновление',
            'content' => 'Контент'
        );
    }
    
    public function rules()
    {
        return array(
            'name' => array(
                array('not_empty')
            )
        );
    }

    public function form()
    {
	    return new Form_Admin_Page($this);
    }

    protected $_grid_options = array(
        'order_by' => 'updated_at',
        'order_direction' => 'ASC',
        'per_page' => 500
    );

    public function get_url_href()
    {
	    return HTML::anchor('/'.$this->url, $this->name, array('target' => '_blank'));
    }
    
    public function get_page_by_url($url = '')
    {
        return $this->where('url', '=', $url)
            ->where('active', '=', true)
                ->find();
    }
    
    public function get_page_type($id)
    {
	    return $this->where('id', '=', $id)->find()->static;
    }
}