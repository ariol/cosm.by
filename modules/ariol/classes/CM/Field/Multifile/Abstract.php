<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

abstract class CM_Field_Multifile_Abstract extends CM_Field
{
	protected $_value_class = 'CM_Value_File';

	public function get_type_name()
	{
		return 'Файл';
	}

	public function render()
	{
		$files = json_decode($this->get_value());

		if (is_array($files)) {
			foreach ($files as &$file) {
				$value = $file;
				$fileInfo = $this->get_file_info($file);
				$file = array('value' => $value, 'file_info' => $fileInfo);
			}
		}

		return View::factory('cm/field/multifile', array(
			'name' => $this->get_name(),
			'files' => $files,
			'field' => $this,
			'value' => $this->get_value()
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

		$fileType = 'file';

		try {
			$exifImageType = @exif_imagetype(PUBLIC_ROOT . $path);
			if ($exifImageType > 0 && $exifImageType < 18) {
				$fileType = 'image';
			}
		} catch (Exception $e) {}

		return array(
			'type' => $type,
			'size' => round($filesize, 2, PHP_ROUND_HALF_UP),
			'name' => $name,
			'file_type' => $fileType
		);
	}
}