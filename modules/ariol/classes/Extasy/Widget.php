<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

abstract class Extasy_Widget
{
	private $_name = NULL;
	
	protected function set_name($name)
	{
		$this->_name = $name;
	}
	
	protected function get_name()
	{
		return $this->_name;
	}
	
	public function allowed($privilege)
	{
		return true;
	}
	
	final public function __toString()
	{
		try
		{
			return (string) $this->render();
		}
		catch (Exception $e)
		{
			// Display the exception message

			HTTP_Exception::factory($e->getCode(), $e->getMessage());
		}
	}
	
	final public function render()
	{
		if( ! $this->allowed('view'))
		{
			return '';
		}
		
		return $this->_render();
	}
	
	abstract protected function _render();
}