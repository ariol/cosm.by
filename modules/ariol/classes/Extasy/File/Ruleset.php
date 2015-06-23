<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_File_Ruleset
{
	private $_rules = array();

	public function __construct(array $config = NULL)
	{
		if (is_null($config))
		{
			$config = (array) Kohana::$config->load('file.ruleset');
		}

		foreach ($config as $regex => $rule_config)
		{
			$this->_rules[] = new Extasy_File_Rule($this, $regex, $rule_config);
		}
	}

	public function process($filename)
	{
		foreach ($this->_rules as $rule)
		{
			if ($rule->is_matched($filename))
			{
				return $rule->process($filename);
			}
		}
		return NULL;
	}
}