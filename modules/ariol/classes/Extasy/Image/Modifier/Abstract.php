<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

abstract class Extasy_Image_Modifier_Abstract
{
	private $_options = array();
	
	final public function __construct(array $options = array())
	{
		$this->_options = $options;
	}
	
	protected function option($name, $default)
	{
		return arr::get($this->_options, $name, $default);
	}
	
	/**
	 * @param Image $image
	 * @return Image $generated_image
	 */
	abstract public function modify(Image $image);
}