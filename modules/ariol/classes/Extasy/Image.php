<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Image extends Extasy_File
{
	/**
	 *
	 * @var Image
	 */
	private $_image = NULL;

	public function __construct($filename)
	{
		parent::__construct($filename);

		$this->_image = Image::factory($this->get_full_path(), Kohana::$config->load('extimage.image_driver'));
	}

	/**
	 * @return Image
	 */
	public function get_image()
	{
		return $this->_image;
	}

	public function get_contents()
	{
		return $this->get_image()->render(NULL, 100);
	}

	public function save_to($path)
	{
		$this->prepare_dir($path);

		$this->get_image()->save($path, 100);

		$this->chmod_file($path);
	}
}