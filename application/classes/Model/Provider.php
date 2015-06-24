<?php defined('SYSPATH') or die('No direct script access.');

class Model_Provider extends ORM
{
    protected $_table_name = 'provider';
    protected $_belongs_to  = array(
        'brand'    => array(
            'model'=> 'Brand',
            'foreign_key' => 'brand_id'
        )
    );
    protected $_grid_columns = array(
        'name' => null,
        'delete' => array(
            'width' => '50',
            'type' => 'link',
            'color' => 'red',
            'route_str' => 'admin-provider:delete?id=${id}',
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
        );
    }
    public function form()
    {
        return new Form_Admin_Provider($this);
    }
    public function selectInsert($name)
    {

    }
}