<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Helpers_Debug
{
	static public function dump($value)
	{
		header('Content-Type: text/html; charset=utf-8');
		
		var_dump($value);

		exit();
	}
}