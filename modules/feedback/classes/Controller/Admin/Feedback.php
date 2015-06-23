<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Controller_Admin_Feedback extends Controller_Crud
{
	protected $_model = 'Feedback';

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

	public function action_answer()
	{
	    $model = ORM::factory('Feedback')->where('id', '=', $this->param('id'))->find();
	    $form = new Form_Admin_Answer($model);
	    $this->template->form = $form;
	    if(isset($_POST['submit']))
	    {
            $form->submit();
            $this->redirect('admin/feedback');
	    }
	}

	public function change_status(ORM $item)
    {
        switch($item->status)
        {
            case 'open':
                $item->status = 'in_process';
                break;
            case 'in_process':
                $item->status = 'closed';
                break;
            case 'closed':
                $item->status = 'open';
                break;
        }
        $item->save();
    }

	public function after() {
	    Extasy_Navigation::instance()->actions()->clear();
	    parent::after();
	}

	public function before_fetch(ORM $item)
	{
		if (isset($_GET['cancel_filter']))
        {
            $this->redirect('/' . Extasy_Url::url_to_route($this->get_index_route()));
        }

		$filter_form = new Form_Filter_Feedback($item);

		if (isset($_GET['filter']))
		{
			$filter_form->submit();
		}

		$this->template->filter_form = $filter_form;

		return parent::before_fetch($item);
    }
}