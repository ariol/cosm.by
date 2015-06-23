<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Controller_Admin_Dynamic extends Controller_Crud
{
    protected $_model = 'Page';

    public function before_fetch(ORM $item) 
    {
        $item->where('static', '=', false);

        return parent::before_fetch($item);
    }
    
    public function change_status(ORM $item) 
    {
        if($item->status) {
            $item->status = false;
        }
        else {
            $item->status = true;
        }
        $item->save();
    }

	protected $_group_actions = array(
		'delete' => array(
			'handler' => 'delete_routine',
			'title' => '<i class="fa fa-trash-o"></i> Удалить',
			'confirm' => 'Вы уверены?',
			'class' => 'btn-danger',
			'one_item' => TRUE
		),
		'change_status' => array (
			'handler' => 'change_status',
			'title' => '<i class="fa fa-refresh"></i>',
			'class' => 'btn-success',
			'one_item' => TRUE
		),
	);
}