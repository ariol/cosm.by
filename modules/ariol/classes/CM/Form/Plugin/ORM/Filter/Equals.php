<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Form_Plugin_ORM_Filter_Equals extends CM_Form_Plugin_ORM_Filter_Abstract
{
	private $_allow_null = FALSE;
	private $_allow_empty = FALSE;
	private $_relation_field = FALSE;
	private $_relation_table = FALSE;
	private $_related_field = FALSE;

	public function __construct($field_name, $allow_null = FALSE, $allow_empty = FALSE, $relation_field = FALSE, $relation_table = FALSE, $related_field = FALSE)
	{
		parent::__construct($field_name);
		$this->_relation_field = $relation_field;
		$this->_relation_table = $relation_table;
		$this->_related_field = $related_field;
		$this->_allow_null = $allow_null;
	}

	public function populate(CM_Form_Abstract $form)
	{
		$value = $form->get_field($this->get_field_name())->get_value()->get_raw();
		
		if ($value == '') {
			return;
		}
		
		if (empty($value) && $value != 0 && ! $this->_allow_empty) {
			return;
		}
		
		if ( ! is_null($value) && $value OR $this->_allow_null OR $value == 0) {
			if (is_object($this->get_model()->{$this->get_field_name()})) {
				$ids = array();
				foreach(DB::select()->from($this->_relation_table)->where($this->_related_field, '=', $value)->execute()->as_array() as $model) {
					$ids[] = $model[$this->_relation_field];
				}
				if ($ids) {
					$this->get_model()->where('id', 'IN', $ids);
				} elseif($value != '') {
					$this->get_model()->where('id', '=', '-1');
				}
			} elseif($value || $value == 0) {
				$this->get_model()->where($this->get_field_name(), is_null($value) ? 'IS' : '=', $value);
			}
		}
	}
}