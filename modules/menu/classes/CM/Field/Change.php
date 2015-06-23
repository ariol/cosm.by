<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Field_Change extends CM_Field
{
	protected $_value_class = 'CM_Value_String';

	protected $_options = NULL;
	
	protected $_mode = NULL;

	public function __construct(array $options = array(), $mode = NULL)
	{
		parent::__construct();
		
		$this->_mode = $mode;
		
		if ( ! empty($options))
		{
			$this->set_options($options);
		}
	}
	
	public function get_type_name()
	{
		return 'Выбор';
	}

	public function set_options(array $options)
	{
		$this->_options = $options;
	}

	/**
	 * @return CM_Value_Hash
	 */
	public function get_options()
	{
		if (is_null($this->_options))
		{
			$this->_options = array();
		}
		return $this->_options;
	}

	public function render()
	{
		$options = array();
		foreach ($this->get_options() as $key => $value)
		{
			if (is_array($value))
			{
				foreach ($value as $key2 => $value2)
				{
					$options[$key][$key2] = $value2;
				}
			}
			else
			{
				$options[$key] = $value;
			}
		}
		
		if($this->_mode == 'string') {
		    return View::factory('cm/field/change/string', array(
			    'name' => $this->get_name(),
			    'options' => $options,
			    'value' => $this->get_value(),
			    'attributes' => $this->get_attributes()
		    ));
		}
		
		return View::factory('cm/field/change', array(
			'name' => $this->get_name(),
			'options' => $options,
			'value' => $this->get_value(),
			'attributes' => $this->get_attributes()
		));
	}

	public function get_submitted_value()
	{
		$option_value = arr::get($this->get_value_source(), $this->get_name());
		return $this->create_raw_value($option_value);
	}

	public function render_value()
	{
		return arr::get($this->get_options(), (string) $this->get_value());
	}
}