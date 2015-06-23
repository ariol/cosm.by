<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Form_Field_Multiselect extends Form_Field
{
	protected $_input_template = 'form/field/multiselect';
	
	protected $_model = NULL;
	
	protected $_rows = NULL;
	
	protected $_name_column = 'name';
	
	public function __construct(array $field = array())
	{
		parent::__construct($field);
		$this->_model = Arr::get($field, 'model', $this->_model);
		$this->_rows = ORM::factory($this->_model)->find_all();
		$this->_name_column = Arr::get($field, 'name_column', $this->_name_column);
	}
	
	protected function input_fields()
	{
		$rows = array();
		foreach($this->_rows as $row)
		{
			$rows[$row['id']] = $row[$this->_name_column];
		}
		
		$value = array();
		foreach($this->_value->find_all() as $object)
		{
			$value[] = $object->id;
		}

		return array(
			'name' => $this->get_name(),
			'rows' => $rows,
			'value' => $value
		);
	}
	
	public function prepare_to_populate($value)
	{
		$ids = array();
		foreach($value as $id)
		{
			if($id != 0)
			{
				$ids[] = $id;
			}
		}
		if(empty($ids))
		{
			return array();
		}
		return ORM::factory($this->_model)->where('id', 'IN', $ids);
	}
}
