<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Route_Resource implements Acl_Resource_Interface, ArrayAccess
{
	private $_params = array();
	
	public function __construct(array $params = array())
	{
		$this->_params = $params;
	}
	
	public function get_resource_id()
	{
		$resource = $this->_params['controller'];
		$parent = NULL;
		if(isset($this->_params['directory']))
		{
			$parent = $this->_params['directory'];
			$resource = $this->_params['directory'].'/'.$resource;
		}
		
		if( ! A2::instance()->has_resource($resource) )
		{
			A2::instance()->add_resource($resource, $parent);
		}
		
		return $resource;
	}
	
	public function __get($key)
	{
		return isset($this->_params[$key]) ? $this->_params[$key] : NULL;
	}
	
	/**
	 * @param offset
	 */
	public function offsetExists ($offset) 
	{
		return isset($this->_params[$key]);
	}

	/**
	 * @param offset
	 */
	public function offsetGet ($offset) 
	{
		return $this->__get($offset);
	}

	/**
	 * @param offset
	 * @param value
	 */
	public function offsetSet ($offset, $value) {}

	/**
	 * @param offset
	 */
	public function offsetUnset ($offset) {}
}