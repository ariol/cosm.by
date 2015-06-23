<?php defined('SYSPATH') or die('No direct script access.');

class Filters
{
	public function get_view($filter, array $values = array())
	{
		switch ($filter['type']) {
			case 'B': {
				$checked = !!isset($_GET['filter'][$filter['id']]) && $_GET['filter'][$filter['id']] == 'on';

				return View::factory('site/filters/boolean', array('filter' => $filter, 'checked' => $checked));
			}
			case 'I': {
				$checked = !!isset($_GET['filter'][$filter['id']]) && $_GET['filter'][$filter['id']] == 'on';
				return View::factory('site/filters/range', array('filter' => $filter, 'value' => $_GET['filter'][$filter['id']] , 'checked' => $checked));
			}
			case 'D': {
				$checked = !!isset($_GET['filter'][$filter['id']]) && $_GET['filter'][$filter['id']] == 'on';
				$dictionaries = array(null => 'Не важно');
				
				foreach ($values[$filter['property_id']] as $val) {
					$dictionaries[$val['id']] = $val['value'];
				}

				return View::factory('site/filters/select', array('filter' => $filter, 'dictionaries' => $dictionaries, 'value' => $_GET['filter'][$filter['id']], 'checked' => $checked));
			}
		}

		return '';
	}
}