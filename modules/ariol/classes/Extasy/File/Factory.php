<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_File_Factory
{
	/**
	 * @param string $filename
	 * @return Extasy_File
	 */
	public function create_file($filename)
	{
		return new Extasy_File($filename);
	}

	public function is_file_suitable(Extasy_File $file)
	{
		return $file instanceof Extasy_File;
	}
}