<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Helpers_Url
{
	static public function translit($name)
	{
		$url = Text::translit($name);
		$url = preg_replace ("/[^a-zA-ZР-пр-џ0-9\s]/"," ",$url);
		$url = trim($url);
		$url = preg_replace('%\s%', '-', $url);
		$url = preg_replace('%-+%', '-', $url);
		$url = mb_strtolower($url);
		
		return $url;
	}
}