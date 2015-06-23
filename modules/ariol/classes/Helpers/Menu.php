<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Helpers_Menu
{
	static public function getNameParts($name)
	{
		$nameParts = explode('|', $name);

		$icon = '<i class="fa fa-folder-o"></i>';
		$name = $name;

		if (isset($nameParts[1])) {
			$icon = $nameParts[0];
			$name = $nameParts[1];
		}

		return array(
			'icon' => $icon,
			'name' => $name
		);
	}
}