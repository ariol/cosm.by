<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Controller_Admin_Ajax extends Controller
{
	public function after() {}

	public function action_boolean()
	{
		$class_name = $this->request->post('class_name');
		$id = $this->request->post('id');
		$value = $this->request->post('value');
		$field = $this->request->post('field');

		$model_name = str_replace('Model_', '', $class_name);

		$model = ORM::factory($model_name, $id);
		$model->$field = (bool)($value == 'true');
		$model->save();
	}
}