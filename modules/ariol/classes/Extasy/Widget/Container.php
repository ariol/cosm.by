<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

abstract class Extasy_Widget_Container extends Widget implements ArrayAccess, Iterator
{
	private $_subwidgets = array();
	
	protected function subwidgets()
	{
		return $this->_subwidgets;
	}
	
	public function offsetExists($offset)
	{
		return isset($this->_subwidgets[$offset]);
	}

	public function offsetGet($offset)
	{
		if( ! isset($this[$offset]))
		{
			throw new Extasy_Widget_Exception('Subwidget "' . $offset . '" not defined');
		}
		return $this->_subwidgets[$offset];
	}

	public function offsetSet($offset, $value)
	{
		if( ! $value instanceof Widget_Item)
		{
			throw new Extasy_Widget_Exception('New subwidget must be a Widget_Item instance');
		}
		$value->set_widget($this);
		$this->_subwidgets[$offset] = $value;
	}

	public function offsetUnset($offset)
	{
		if(isset($this->_subwidgets[$offset]))
		{
			unset($this->_subwidgets[$offset]);
		}
	}
	
	private $_iterator_position = 0;
	
	public function current()
	{
		return Arr::get(array_values($this->_subwidgets), $this->_iterator_position);
	}

	public function next()
	{
		$this->_iterator_position++;
	}

	public function key()
	{
		return Arr::get(array_keys($this->_subwidgets), $this->_iterator_position);
	}

	public function valid()
	{
		return $this->_iterator_position < count($this->_subwidgets);
	}

	public function rewind()
	{
		$this->_iterator_position = 0;
	}
}