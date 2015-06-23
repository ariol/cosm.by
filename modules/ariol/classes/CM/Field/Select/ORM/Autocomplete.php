<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Field_Select_ORM_Autocomplete extends CM_Field_Select_ORM
{
	public function render()
	{
		return View::factory('cm/field/select_autocomplete', array(
			'name' => $this->get_name(),
			'value' => $this->render_value()
		));
	}
	
	public function set_name($name)
	{
		parent::set_name($name);
		
		if (arr::get($_GET, 'select_autocomplete') === $this->get_name())
		{
			$model = clone $this->get_model();
			
			$model->where($this->get_name_key(), 'LIKE', '%'.arr::get($_GET, 'term').'%');
			
			$items = array();
			foreach ($model->find_all() as $db_item)
			{
				$items[] = $db_item->{$this->get_name_key()};
			}
			echo json_encode($items);
			exit();
		}
	}
	
	public function get_submitted_value()
	{
		$model = clone $this->get_model();
		$submitted_name = parent::get_submitted_value();
		
		$item = $model->where($this->get_name_key(), '=', $submitted_name)->find();
		
		return $this->create_raw_value($item->{$this->get_id_key()});
	}

	public function validate()
	{
		if (is_null($this->get_value()->get_raw()))
		{
			return;
		}

		$model = clone $this->get_model();
		$item = $model->where($this->get_id_key(), '=', $this->get_value()->get_raw())->find();
		if ( ! $item->loaded())
		{
			$this->set_error('Выбрано несуществующее значение');
		}
	}
}
