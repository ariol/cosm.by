<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Htmleditor
{
	static private $_initialized = FALSE;

	static public function init($name, $config_group = 'default')
	{
		$initialized = self::$_initialized;
		$config = Kohana::$config->load('htmleditor')->{$config_group};
		self::$_initialized = TRUE;

		return View::factory('htmleditor', compact('name', 'config', 'initialized'));
	}
}