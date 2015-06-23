<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Form_Field_Multiselect_Autocomplete extends Form_Field_Multiselect
{
	protected $_input_template = 'form/field/multiselect-autocomplete';
	
	protected $_allow_add = FALSE;
	
	public function __construct(array $field = array())
	{
		parent::__construct($field);
		$this->_allow_add = Arr::get($field, 'allow_add', $this->_allow_add);
	}
	
	protected function input_fields()
	{
		$rows = array();
		foreach($this->_rows as $row)
		{
			$rows[$row['id']] = $row[$this->_name_column];
		}
		
		$value = '';
		foreach($this->_value->find_all() as $object)
		{
			$value .= $object->{$this->_name_column}.', ';
		}

		return array(
			'name' => $this->get_name(),
			'rows' => $rows,
			'value' => $value
		);
	}
	
	public function prepare_to_populate($value)
	{
		$value = explode(', ', $value);
		$value = array_unique($value);
		
		$model = ORM::factory($this->_model);
		
		foreach($value as $cur)
		{
			if(empty($cur) OR preg_match('#^\s*$#', $cur)) continue;
			
			$cur = trim($cur);
			
			if($this->_allow_add)
			{
				$item = ORM::factory($this->_model, array($this->_name_column => $cur));
				if( ! $item->loaded())
				{
					$item->{$this->_name_column} = $cur;
					$item->save();
				}
			}
			$model->or_where($this->_name_column, '=', $cur);
		}
		
		return $model;
	}
}