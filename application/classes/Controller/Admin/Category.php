<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Controller_Admin_Category extends Controller_Crud
{
    protected $_model = 'Category';
	
	protected function before_create(ORM $item)
	{
		$item->parent_id = $this->param('id');
                
		return parent::before_create($item);
	}
	
	public function before_fetch(ORM $item)
    {
        if (isset($_GET['cancel_filter']))
        {
            $this->redirect('/' . Extasy_Url::url_to_route($this->get_index_route()));
        }

        $filter_form = new Form_Filter_Category($item);

        if (isset($_GET['filter']))
        {
            $filter_form->submit();
        }

        $this->template->filter_form = $filter_form;
	
	$item->where('parent_id', 'IS', NULL);
		
        return parent::before_fetch($item);
    }
	
	protected function delete_routine(ORM $item)
	{
		if (!$item->allow_delete())
		{
			$this->forward_403();
		}
		return parent::delete_routine($item);
	}
	
	public function action_delete() 
	{
	    $item = $this->get_item();

	    if ( ! $item->allow_delete())
	    {
		    $this->forward_403();
	    }
	    
	    $this->delete_routine($item);

		$this->redirect('/'.Extasy_Url::url_to_route($this->get_index_route()));
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
