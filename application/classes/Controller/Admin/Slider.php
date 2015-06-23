<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Admin_Slider extends Controller_Crud
{
    protected $_model = 'Slider';

    public function change_status(ORM $item)
    {
        $item->active = !$item->active;

        $item->save();
    }

    protected $_group_actions = array(
        'delete' => array(
            'handler' => 'delete_routine',
            'title' => 'Удалить',
            'confirm' => 'Вы уверены?',
            'one_item' => TRUE
        ),
        'change_status' => array (
            'handler' => 'change_status',
            'title' => 'Изменить статус',
            'one_item' => TRUE
        )
    );
}
