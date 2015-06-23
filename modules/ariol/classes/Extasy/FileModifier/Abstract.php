<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

abstract class Extasy_FileModifier_Abstract
{
	private $_rule = NULL;

	abstract public function process($filename, Extasy_File $file = NULL);

	public function set_rule(Extasy_File_Rule $rule)
	{
		$this->_rule = $rule;
	}

	/**
	 * @return Extasy_File_Rule
	 */
	public function get_rule()
	{
		return $this->_rule;
	}

	public function free()
	{
		$this->_rule = NULL;
	}
}