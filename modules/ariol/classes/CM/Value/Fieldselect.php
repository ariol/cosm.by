<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Value_Fieldselect implements CM_Value_Interface
{
	/**
	 * @var CM_Field
	 */
	protected $_field = NULL;

	public function __construct($raw_value = NULL)
	{
		if (is_null($raw_value))
		{
			$raw_value = new CM_Field_String;
		}

		if (is_string($raw_value))
		{
			$raw_value = unserialize($raw_value);
		}
		if ( ! $raw_value instanceof CM_Field)
		{
			throw new Exception('Value for Fieldselect must be an instance of CM_Field');
		}
		$this->_field = $raw_value;
	}

	public function __toString()
	{
		return 'type: '.$this->get_field()->get_type();
	}

	public function is_valid()
	{
		return TRUE;
	}

	public function get_raw()
	{
		return serialize($this->_field);
	}

	public function get_field()
	{
		return $this->_field;
	}
}