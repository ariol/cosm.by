<?php defined('SYSPATH') or die('No direct script access.');

class Extasy_Logger
{
	/**
	 * Message types
	 */
	const MSG = 0;
	const WRN = 1;
	const ERR = 2;
	
	/**
	 * @var Log
	 */
	public static $default_log;

	public static function assign_log(Logger_Abstract $log)
	{
		self::$default_log = $log;
	}

	public static function add($type, $message, $sender=NULL)
	{
		if (is_null(self::$default_log))
		{
			self::$default_log = new Logger_Blackhole();
		}

		if ( ! in_array($sender, Kohana::$config->load('logger')->ignored_senders))
		{
			self::$default_log->add($type, $message, $sender);
		}
	}
}
