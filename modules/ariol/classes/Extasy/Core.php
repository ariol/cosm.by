<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Core
{
	const VERSION = '3.2.2 dev';

	final private function __construct(){}

	static public function obj_placeholders($obj, $template)
	{
		preg_match_all('#\${(.*?)}#', $template, $matches);

		foreach($matches[1] as $cur)
		{
			$thunks = explode('|', $cur);
			$index = explode('.', array_shift($thunks));
			$value = $obj;
			while($key = array_shift($index))
			{
				$value = $value[$key];
			}

			while($filter = array_shift($thunks))
			{
				$value = call_user_func(array('Extasy', $filter), $value);
			}
			$template = str_replace('${'.$cur.'}', $value, $template);
		}

		return $template;
	}

	static protected function count($obj)
	{
		return $obj->count_all();
	}

	static protected function integer($obj)
	{
		return number_format($obj, 0, ',', ' ');
	}
}