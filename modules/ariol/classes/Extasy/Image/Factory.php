<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Image_Factory extends Extasy_File_Factory
{
	public function create_file($filename)
	{
		return new Extasy_Image($filename);
	}

	public function is_file_suitable(Extasy_File $file)
	{
		return $file instanceof Extasy_Image;
	}
}