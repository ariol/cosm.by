<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_FileModifier_Process extends Extasy_FileModifier_Abstract
{
	private $_filename_pattern = NULL;

	public function __construct($filename_pattern)
	{
		$this->_filename_pattern = $filename_pattern;
	}

	public function process($filename, Extasy_File $file = NULL)
	{
		$real_filename = preg_replace($this->get_rule()->get_regex(), $this->_filename_pattern, $filename);

		$file = $this->get_rule()->get_ruleset()->process($real_filename);

		if ( ! $this->get_rule()->get_file_factory()->is_file_suitable($file))
		{
			throw new Exception('Created file dont suit to this rule');
		}

		return $file;
	}
}