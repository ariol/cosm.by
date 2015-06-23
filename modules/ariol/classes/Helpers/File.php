<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Helpers_File
{
	static public function getInformation($path)
	{
		$path_info = pathinfo($path);
		$extension = arr::get($path_info, 'extension');
		$file_name = arr::get($path_info, 'filename');

		try {
			$is_image = exif_imagetype($path);
		}
		catch (Exception $e) {
			$is_image = false;
		}

		return array(
			'extension' => $extension,
			'filename' => $file_name,
			'is_image' => $is_image
		);
	}

	static public function recursiveCreateDirectory($directory)
	{
		$uploadDirectory = '';

		foreach (explode('/', $directory) as $path) {
			$uploadDirectory .= '/' . $path;
			if (!is_dir($uploadDirectory)) {
				mkdir($uploadDirectory, 0777);
				chmod($uploadDirectory, 0777);
			}
		}
	}

	static public function image2thumb($imageUrl)
	{
		$imageUrlParts = explode('/', $imageUrl);

		$imageName = end($imageUrlParts);

		return $imageUrl;
	}
}