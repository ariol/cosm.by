<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Controller_Admin_Discount extends Controller_Crud
{
    protected $_model = 'Discount';
    
	public function before_fetch(ORM $item)
    {
        if (isset($_GET['cancel_filter']))
        {
            $this->redirect('/' . Extasy_Url::url_to_route($this->get_index_route()));
        }

        $filter_form = new Form_Filter_Discount($item);

        if (isset($_GET['filter']))
        {
            $filter_form->submit();
        }

        $this->template->filter_form = $filter_form;

        return parent::before_fetch($item);
    }
	
	public function change_status(ORM $item)
    {
        $item->active = !$item->active;

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