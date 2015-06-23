<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

abstract class Extasy_Form_Field extends Widget_Item
{
	protected $_template = 'form/field';
	protected $_error_template = NULL;
	protected $_label_template = NULL;
	protected $_input_template = NULL;
	protected $_ro_input_template = NULL;
	
	protected $_error = NULL;
	protected $_label = NULL;
	protected $_value = NULL;
	
	protected $_hash = NULL;
	
	protected $_attributes = array();
	
	private $_object = NULL;
	
	/**
	 * @param array $field
	 * @return Form_Field
	 */
	static public function factory(array $field = array())
	{
		$class = Arr::get($field, 'type', 'text');
		$class = 'Form_Field_'.ucfirst($class);
		
		if(isset($field['type']))
		{
			unset($field['type']);
		}
		
		return new $class($field);
	}
	
	/**
	 * 
	 * @param string $name
	 * @param string $value
	 * @return Form_Field
	 */
	public function set_attribute($name, $value = NULL)
	{
		$this->_attributes[$name] = $value;
		return $this;
	}
	
	protected function __construct(array $field = array())
	{
		$this->_error = Arr::get($field, 'error');
		$this->_label = Arr::get($field, 'label');
		$this->set_name(Arr::get($field, 'name'));
		$this->_value = Arr::get($field, 'value');

		if( ! is_null($this->_error))
		{
			$this->_attributes['class'] = 'error';
		}
		
		$this->_hash = md5($this->get_name().$this->_label);
	}
	
	public function hash()
	{
		return $this->_hash;
	}
	
	public function set_value($value = NULL)
	{
		$this->_value = $value;
	}
	
	protected function _render()
	{
		if(is_null($this->_template))
		{
			throw new Extasy_Form_Field_Exception('field template not defined');
		}
		return View::factory($this->_template, array(
			'label' => $this->label(),
			'input' => $this->input(),
			'error' => $this->error()
		));
	}
	
	final public function label()
	{
		if( ! $this->allowed('view'))
		{
			return '';
		}
		
		return $this->_label();
	}
	
	protected function _label()
	{
		if( ! is_null($this->_label_template))
		{
			return View::factory($this->_label_template, array(
				'name' => $this->get_name(),
				'label' => $this->_label
			));
		}
		else
		{
			return Form::label($this->get_name(), $this->_label);
		}
	}
	
	public function valid()
	{
		return is_null($this->_error);
	}
	
	public function set_object($object)
	{
		$this->_object = $object;
	}
	
	public function get_object()
	{
		return $this->_object;
	}
	
	public function set_error($error = NULL)
	{
		$this->_error = $error;
		if( ! is_null($error))
		{
			$this->_attributes['class'] = join(' ', array_merge(array('error'),explode(' ', Arr::get($this->_attributes, 'class', ''))));
		}
	}
	
	final public function error()
	{
		if( ! $this->allowed('view') )
		{
			return '';
		}
		return $this->_error();
	}
	
	protected function _error()
	{
		if(is_null($this->_error))
		{
			return NULL;
		}
		if(is_null($this->_error_template))
		{
			return Form::label($this->get_name(), $this->_error, array(
				'class' => 'error',
				'generated' => 'false'
			));
		}
		else
		{
			return View::factory($this->_error_template, array(
				'error' => $this->_error,
				'name' => $this->get_name()
			));
		}
	}
	
	final public function input()
	{
		if( ! $this->allowed('view') AND ! $this->allowed('read'))
		{
			return '';
		}
		
		if( ! $this->allowed('write'))
		{
			return $this->ro_input();
		}
		
		return $this->_input();
	}
	
	final private function ro_input()
	{
		return $this->_ro_input();
	}
	
	protected function input_fields()
	{
		return array(
			'name' => $this->get_name(),
			'value' => $this->_value,
			'attributes' => $this->_attributes
		);
	}
	
	protected function _input()
	{
		if(is_null($this->_input_template))
		{
			throw new Extasy_Form_Field_Exception('input_template not defined');
		}
		return View::factory($this->_input_template, $this->input_fields());
	}
	
	protected function _ro_input()
	{
		if(is_null($this->_ro_input_template))
		{
			throw new Extasy_Form_Field_Exception('ro_input_template not defined');
		}
		return View::factory($this->_ro_input_template, $this->input_fields());
	}
	
	public function prepare_to_populate($value)
	{
		return $value;
	}
	
	public function perform(ORM $object) {}
	
	public function validate(Validate $array, $field)
	{
		return TRUE;
	}
}