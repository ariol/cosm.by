<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Model_Menu extends ORM
{
    protected $_table_name = 'menu';

    protected $_has_many = array(
        'children' => array(
            'model' => 'Menu',
            'foreign_key' => 'parent_id'
        ),
    );

    protected $_belongs_to = array(
        'parent' => array(
            'model' => 'Menu',
            'foreign_key' => 'parent_id'
        )
    );

    protected $_grid_columns = array(
        'name' => 'tree',
        'link' => array(
            'type' => 'template',
            'template' => '${link}',
			'width' => '15px'
        ),
        'position' => 'position',
        'active' => 'bool',
        'add_child' => array(
			'width' => '50',
            'type' => 'link',
            'route_str' => 'admin-menu:create?id=${id}',
            'title' => '<i class="fa fa-arrow-down"></i>',
            'alternative' => 'Добавить вложенность',
			'color' => 'blue'
			
        ),
        'edit' => array(
			'width' => '50',
            'type' => 'link',
            'route_str' => 'admin-menu:edit?id=${id}',
            'title' => '<i class="fa fa-pencil"></i>',
			'color' => 'green',
            'alternative' => 'Править'
        ),
        'delete' => array(
            'width' => '50',
            'type' => 'link',
            'route_str' => 'admin-menu:delete?id=${id}',
            'title' => '<i class="fa fa-trash-o"></i>',
            'alternative' => 'Удалить',
			'color' => 'red',
            'confirm' => 'Вы уверены?'
        )
    );

    public function labels()
    {
        return array(
            'name' => 'Название',
            'url' => 'URL',
            'position' => 'Позиция',
            'active' => 'Активность'
        );
    }

    public function rules()
    {
        return array(
            'name' => array(
           	 	array('not_empty')
            ),
            'url' => array(
            	array('not_empty')
            ),
        );
    }

    public function form()
    {
	    return new Form_Admin_Menu($this);
    }

    public function allow_delete()
    {
	    return ! $this->children->count_all();
    }

    public function get_link()
    {
	    return HTML::anchor($this->url, '<i class="fa fa-external-link"></i>');
    }
    
    public function get_parent_active_menus()
    {
        return $this->where('active', '=', true)
            ->where('parent_id', 'is', NULL)
			->order_by('position', 'ASC')->find_all();
    }
	
	public function get_children_active_menus()
    {
        return $this->where('active', '=', true)
            ->where('parent_id', 'is not', NULL)
                ->find_all();
    }

    public function get_children_by_id($id)
    {
        return $this->where('parent_id', '=', $id)
            ->where('active', '=', true)
            ->order_by('position', 'ASC')
            ->find_all();
    }

	public function sortable_fields()
	{
		return array(
			'name', 'active', 'position'
		);
	}
}