<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_FileModifier_Load extends Extasy_FileModifier_Abstract
{
	private $_filename_pattern = NULL;

	public function __construct($filename_pattern)
	{
		$this->_filename_pattern = $filename_pattern;
	}

	public function process($filename, Extasy_File $file = NULL)
	{
		$real_filename = preg_replace($this->get_rule()->get_regex(), $this->_filename_pattern, $filename);

		return $this->get_rule()->get_file_factory()->create_file($real_filename);
	}
}