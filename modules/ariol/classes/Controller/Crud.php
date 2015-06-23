<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

abstract class Controller_Crud extends Controller_Admin
{
	protected $_model = NULL;

	protected $_group_actions = array(
			'delete' => array(
			'handler' => 'delete_routine',
			'title' => 'Удалить',
			'class' => 'btn-danger',
			'confirm' => 'Вы уверены?',
			'one_item' => TRUE
		)
	);

	protected function get_filter_form(ORM $model)
	{
		return FALSE;
	}

	protected function get_item()
	{
		$item = ORM::factory($this->_model, $this->param('id'));

		if( ! $item->loaded())
		{
			$this->forward_404();
		}

		return $item;
	}

	protected function get_route_name()
	{
		return 'admin-'.$this->_model;
	}

	protected function get_index_route()
	{
		return $this->get_route_name().'?action=index';
	}

	protected function get_create_route()
	{
		return $this->get_route_name().'?action=create';
	}

	protected function get_edit_route()
	{
		return $this->get_route_name().'?action=edit';
	}

	protected function before_create(ORM $item)
	{
		return $item;
	}

	/**
	 * @return ORM
	 */
	protected function before_fetch(ORM $item)
	{
		return $item;
	}

	public function before()
	{
		parent::before();
		if(is_null($this->_model))
		{
			throw new Extasy_Exception('Model not defined');
		}
		$this->template->set_layout(Kohana::$config->load('layout')->admin);
	}

	public function action_index()
	{
		foreach ($this->_group_actions as $action => $cfg) {
			if (!ACL::is_action_allowed($this->request->directory(), $this->request->controller(), $action)) {
				unset($this->_group_actions[$action]);
			}
		}

		if (isset($_POST['group_actions'])) {
			$action = NULL;
			foreach(arr::get($_POST, 'action', array()) as $name => $value) {
				$action = $name;
			}

			$handler = arr::path($this->_group_actions, $action.'.handler');

			if ($action && $handler && method_exists($this, $handler)) {
				if (arr::path($this->_group_actions, $action.'.one_item')) {
					foreach($_POST['ids'] as $id) {

						$item = ORM::factory($this->_model, $id);
						if($item->loaded()) {
							$this->$handler($item);
						}
					}
				} else {
					$ids = array();
					foreach ($_POST['ids'] as $id) {
						if($id) {
							$ids[] = $id;
						}
					}
					if (count($ids)) {
						$this->$handler($ids);
					}
				}
			}
			$this->back($this->get_index_route());
		}

		$this->set_view('crud/index');
		Navigation::instance()->actions()->add($this->get_create_route(), 'Добавить');

		$model = ORM::factory($this->_model);
		$this->template->filter_form = $this->get_filter_form($model);
		if ($this->template->filter_form) {
			$this->template->filter_form->set_method('GET');
			if (isset($_GET['filter_cancel'])) {
				$this->redirect('/'.Extasy_Url::url_to_route($this->get_index_route()));
			}
			if (isset($_GET['filter']))	{
				$this->template->filter_form->submit();
			}
		}

		$this->template->unSortableFields = $model->getUnSortableSortableFields();

		$grid = $this->before_fetch($model)->grid()->set_group_actions($this->_group_actions);
		$this->template->grid = $this->prepare_grid($grid);
	}

	public function action_get_grid_data()
	{
		$model = ORM::factory($this->_model);

		$this->template->unSortableFields = $model->getUnSortableSortableFields();

		$offset = Arr::get($_REQUEST, 'iDisplayStart');
		$limit = Arr::get($_REQUEST, 'iDisplayLength');
		$draw = intval(Arr::get($_REQUEST, 'sEcho'));
		$sorting = Arr::get($_REQUEST, 'sSortDir_0');
		$sortingColumn = $model->getColumnByNumber(arr::get($_REQUEST, 'iSortCol_0'));

		$model = $this->before_fetch($model);
		$clone = clone $model;

		if ($sorting && $sortingColumn) {
			$model = $model->order_by($sortingColumn, $sorting);
		}

		$data = $model
			->offset($offset)
			->limit($limit)
			->find_all()
			->as_array();

		$json_data = array(
			'aaData' => array(),
			'sEcho' => $draw,
			'iTotalRecords' => count($data),
			'iTotalDisplayRecords' => $clone->count_all()
		);

		foreach ($data as $index => $item) {
			$json_data['aaData'][$index][] = '<input name="ids[]" value="' . $item->id . '" type="checkbox" class="checkbox" />';
			foreach ($item->grid_fields() as $field => $attributes) {
				$json_data['aaData'][$index][] = $item->grid_value($field);
			}
		}

		Json::response($json_data);
	}

	protected function prepare_grid(Grid $grid)
	{
		return $grid;
	}

	public function action_create()
	{
		$this->process_form($this->before_create(ORM::factory($this->_model)));
	}

	public function action_edit()
	{
		$item = $this->get_item();

		if ( ! $item->allow_edit())
		{
			$this->forward_403();
		}

		$this->process_form($item);
	}

	protected function process_form(ORM $item)
	{
		$this->set_view('crud/form');

		if (isset($_POST['cancel']))
		{
            $this->redirect('/'.Extasy_Url::url_to_route($this->get_index_route()));
		}

		$form = $item->form();

		if (isset($_POST['submit']) && $form->submit())
		{
            $this->redirect('/'.Extasy_Url::url_to_route($this->get_index_route()));
		}

		$this->template->form = $form;
	}

	public function action_delete()
	{
		$item = $this->get_item();

		if (!$item->allow_delete()) {
			$this->forward_403();
		}

		$this->delete_routine($item);
        $this->redirect('/'.Extasy_Url::url_to_route($this->get_index_route()));
	}

	protected function delete_routine(ORM $item)
	{
		if ($item->allow_delete())
		{
			$item->delete();
		}
	}
}