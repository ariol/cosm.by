<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Controller_Admin_User extends Controller_Crud
{
	protected $_model = 'User';

	protected function get_filter_form(ORM $item)
	{
		return new Form_Filter_User($item);
	}

	public function action_change_password()
	{
		$this->set_view('crud/improved/form');

		$form = new Form_User_ChangePassword($this->get_item());

		if (isset($_POST['cancel']))
		{
			$this->back($this->get_index_route());
		}

		if (isset($_POST['submit']) AND $form->submit())
		{
			$this->back($this->get_index_route());
		}

		$this->template->form = $form;
	}
}