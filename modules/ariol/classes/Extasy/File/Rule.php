<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_File_Rule
{
	/**
	 * @var Extasy_File_Ruleset
	 */
	private $_ruleset = NULL;

	private $_regex = NULL;

	private $_config = NULL;

	/**
	 * @var Extasy_File_Factory
	 */
	private $_factory = NULL;

	public function __construct(Extasy_File_Ruleset $ruleset, $regex, $config)
	{
		$this->_ruleset = $ruleset;
		$this->_regex = $regex;
		$this->_config = $config;

		$this->_factory = arr::get($this->_config, 'factory');
		if ( ! $this->_factory)
		{
			$this->_factory = new Extasy_File_Factory();
		}

		if ( ! $this->_factory instanceof Extasy_File_Factory)
		{
			throw new Exception('File factory must be an instace of Extasy_File_Factory');
		}
	}

	/**
	 * @return Extasy_File_Ruleset
	 */
	public function get_ruleset()
	{
		return $this->_ruleset;
	}

	public function get_regex()
	{
		return $this->_regex;
	}

	/**
	 * @return Extasy_File_Factory
	 */
	public function get_file_factory()
	{
		return $this->_factory;
	}

	public function is_matched($filename)
	{
		return preg_match($this->_regex, $filename);
	}

	public function process($filename)
	{
		$file = NULL;

		$modifiers = (array) arr::get($this->_config, 'modifiers');

		foreach ($modifiers as $modifier)
		{
			$modifier->set_rule($this);
			$file = $modifier->process($filename, $file);
			$modifier->free();
		}

		return $file;
	}
}