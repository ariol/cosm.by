<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_File
{
	private $_filename = NULL;

	public function __construct($filename)
	{
		$this->_filename = $filename;
	}

	public function get_full_path()
	{
		return Kohana::$config->load('file.upload_root').'/'.$this->_filename;
	}

	public function get_contents()
	{
		return file_get_contents($this->get_full_path());
	}

	protected function prepare_dir($path)
	{
		$dest_directory = dirname($path);
		if ( ! file_exists($dest_directory))
		{
			mkdir($dest_directory, 0777, TRUE);
		}
	}

	protected function chmod_file($path)
	{
		chmod($path, 0666);
	}

	public function save_to($path)
	{
		$this->prepare_dir($path);

		file_put_contents(
			$path,
			$this->get_contents()
		);

		$this->chmod_file($path);
	}

	public function get_mime_type()
	{
		$extension = strtolower(pathinfo($this->_filename, PATHINFO_EXTENSION));
		$mime = File::mime_by_ext($extension);

		if ( ! $mime)
		{
			$mime = 'application/octet-stream';
		}

		return $mime;
	}
}