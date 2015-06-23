<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

abstract class Extasy_Widget_Item extends Widget
{
	protected $_widget = NULL;
	
	public function set_widget(Widget $widget)
	{
		$this->_widget = $widget;
	}
	
	public function get_widget()
	{
		return $this->_widget;
	}
}