<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class CM_Value_Image extends CM_Value_File
{
	private $_alt = NULL;

	public function __construct($raw_value = NULL)
	{
		if (is_string($raw_value))
		{
			$raw_exploded = explode('|', $raw_value, 2);
			$raw_value = $raw_exploded[0];
			$this->_alt = arr::get($raw_exploded, 1);
		}

		parent::__construct($raw_value);
	}

	protected function get_sizes()
	{
		$filename = $this->get_filename();
		if (file_exists($filename) AND is_readable($filename))
		{
			return getimagesize($filename);
		}
		return array();
	}

	public function get_width()
	{
		return arr::get($this->get_sizes(), 0);
	}

	public function get_height()
	{
		return arr::get($this->get_sizes(), 1);
	}

	public function get_alt()
	{
		return (string) $this->_alt;
	}

	public function get_raw()
	{
		return parent::get_raw().'|'.$this->get_alt();
	}
}