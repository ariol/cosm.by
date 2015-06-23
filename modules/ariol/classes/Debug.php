<?php defined('SYSPATH') or die('No direct script access.');

use DebugBar\StandardDebugBar;

class Debug extends Kohana_Debug
{
	protected static $_debug;

	public static function StandardDebugBar()
	{
		if (!self::$_debug) {
			self::$_debug = new StandardDebugBar();
		}

		return self::$_debug;
	}
}