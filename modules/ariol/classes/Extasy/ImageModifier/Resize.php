<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_ImageModifier_Resize extends Extasy_FileModifier_Abstract
{
	private $_width = NULL;
	private $_height = NULL;

	public function __construct($width, $height)
	{
		$this->_width = $width;
		$this->_height = $height;
	}

	public function process($filename, Extasy_File $file = NULL)
	{
		if (is_null($file))
		{
			throw new Exception('Can not resize empty image');
		}

		$file->get_image()->resize($this->_width, $this->_height);

		return $file;
	}
}