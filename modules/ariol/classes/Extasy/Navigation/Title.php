<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Navigation_Title extends Navigation_Widget
{
	protected $_title = '';
	
	public function set($title)
	{
		$this->_title = $title;
	}
	
	public function render()
	{
		return $this->_title;
	}
}