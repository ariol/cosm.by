<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_FileModifier_Save extends Extasy_FileModifier_Abstract
{
	public function process($filename, Extasy_File $file = NULL)
	{
		if (is_null($file))
		{
			throw new Exception('Can not save empty file');
		}

		$file->save_to(PUBLIC_ROOT.'/'.$filename);

		return $file;
	}
}