<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Json
{
	public static function response(array $array = array())
	{
		exit(json_encode($array));
	}
}