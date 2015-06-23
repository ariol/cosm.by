<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

abstract class CM_Field_File_Abstract extends CM_Field
{
	protected $_value_class = 'CM_Value_File';

	public function get_type_name()
	{
		return 'Файл';
	}

	public function render()
	{
		return View::factory('cm/field/file', array(
			'name' => $this->get_name(),
			'value' => $this->get_value(),
			'field' => $this,
			'file_info' => $this->get_file_info($this->get_value())
		));
	}

	private function get_file_info($file)
	{
		$filesize = 0;

		$fileParts = parse_url($file);
		$path = arr::get($fileParts, 'path');
		$path = substr_replace($path, '', 0, 1);
		$path = urldecode($path);

		$pathParts = explode('/', $path);
		$name = end($pathParts);

		if (is_file(PUBLIC_ROOT . $path)) {
			$filesize = filesize(PUBLIC_ROOT . $path) / 1000;
		}

		$mbSize = $filesize / 1000;

		$type = 'KB';

		if ($mbSize > 1) {
			$filesize = $mbSize;
			$type = 'MB';
		}

		try {
			$exifImageType = @exif_imagetype(PUBLIC_ROOT . $path);
		}
		catch (Exception $e) {
			$exifImageType = 0;
		}

		if ($exifImageType > 0 && $exifImageType < 18) {
			$fileType = 'image';
		}
		else {
			$fileType = 'file';
		}

		return array(
			'type' => $type,
			'size' => round($filesize, 2, PHP_ROUND_HALF_UP),
			'name' => $name,
			'file_type' => $fileType
		);
	}
}