<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Lib_Image
{
	const RESIZE_PATH = "resized";
	const RESIZE_BG_PATH = "resizedbg";
	const CROPPED_PATH = "cropped";

	public static function resize_bg($image, $model, $id, $width, $height)
	{
		$startImage = $image;
		$test_name = 'test-' . $width.'-' . $height . '-' . time() . '.jpg';
	
		if (!$image) {
			return '/images/blank.png';
		}
	
		$directory = self::checkAndCreateDirectory($model, $id, self::RESIZE_BG_PATH, $width, $height);

		$imageParts = explode('/', $image);

		$imageName = end($imageParts);

		try {
			$image_factory = Image::factory(PUBLIC_ROOT . 'files/' . $model . '/' . $id . '/' . $imageName);
		}
		catch (Exception $e) {
			return $startImage;
		}
		
		try {
			if (!file_exists($directory.$imageName)) {
				$img = imagecreate($width, $height);
				$white = ImageColorAllocate($img, 255, 255, 255);
				ImageFill($img, 0, 0, $white);
				
				header('Content-Type: image/jpg;');
				
				$test_file = APPPATH . 'cache/' . $test_name;
				
				imageJPEG($img, $test_file);
				
				$image = Image::factory($test_file);
				
				$image->watermark($image_factory->resize(intval($width), intval($height), Kohana_Image::AUTO))
						->save($directory.$imageName, 99);
				
				imageDestroy($img);
				@unlink($test_file);
			}
		} catch (Exception $e) {
			return $startImage;
		}
		
		$path = '/files/' . self::RESIZE_BG_PATH . '/' . $width;
		if ($height) {
			$path .= 'x' . $height;
		}
		
		if (file_exists($directory.$imageName)) {
			return  $path . '/' . $model . '/'. $id . '/' . $imageName;
		}
		
		return $startImage;
	}

	public static function resize_width($image, $model, $id, $width, $height = null)
	{
		if (!$image) {
			return $image;
		}
	
		$directory = self::checkAndCreateDirectory($model, $id, self::RESIZE_PATH, $width, $height);

		$imageParts = explode('/', $image);

		$imageName = end($imageParts);

		try {
			$image_factory = Image::factory(PUBLIC_ROOT . 'files/' . $model . '/' . $id . '/' . $imageName);
		}
		catch (Exception $e) {
			return $image;
		}
		
		if ($image_factory->width <= $width) {
			return $image;
		}

		if (!file_exists($directory.$imageName)) {
			$image_factory
				->resize(intval($width), $height, $height ? Kohana_Image::PRECISE : Kohana_Image::WIDTH)
				->save($directory.$imageName, 99);
		}
		$path = '/files/' . self::RESIZE_PATH . '/' . $width;
		if ($height) {
			$path .= 'x' . $height;
		}
		return  $path . '/' . $model . '/'. $id . '/' . $imageName;
	}

	public static function crop($image, $model, $id, $width, $height)
	{
		if (!$image) {
			return $image;
		}
	
		$image = self::resize_width($image, $model, $id, $width, $height);

		$directory = self::checkAndCreateDirectory($model, $id, self::CROPPED_PATH, $width, $height);

		$imageParts = explode('/', $image);

		$imageName = end($imageParts);

		try {
			$image_factory = Image::factory(
				PUBLIC_ROOT . 'files/' . self::RESIZE_PATH . '/' . $width . 'x' . $height . '/' . $model . '/' . $id . '/' . $imageName
			);
		}
		catch (Exception $e) {
			$image_factory = Image::factory(
				PUBLIC_ROOT . 'files/' . $model . '/' . $id . '/' . $imageName
			);
		}

		if (!file_exists($directory.$imageName)) {
			$image_factory
				->crop(intval($width), intval($height))
				->save($directory.$imageName, 90);
		}
		
		return '/files/' . self::CROPPED_PATH . '/' . $width . 'x' . $height . '/' . $model . '/'. $id . '/' . $imageName;
	}

	private static function checkAndCreateDirectory($model, $id, $type, $width, $height)
	{
		switch ($type) {
			case self::RESIZE_PATH: {
				$path = PUBLIC_ROOT . "files/" . self::RESIZE_PATH . '/' . $width;
				if ($height) {
					$path .= 'x' . $height;
				}
				break;
			}
			case self::RESIZE_BG_PATH: {
				$path = PUBLIC_ROOT . "files/" . self::RESIZE_BG_PATH . '/' . $width . 'x' . $height;
				break;
			}
			case self::CROPPED_PATH: {
				$path = PUBLIC_ROOT . "files/" . self::CROPPED_PATH . '/' . $width . 'x' . $height;
				break;
			}
		}

		$path .= '/' . $model . '/' . $id . '/';

		if (!file_exists($path)) {
			$pathParts = explode('/', $path);
			$fullPath = '';
			foreach ($pathParts as $index => $part) {
				if ($index) {
					$fullPath .= '/' . $part;
					if(!file_exists($fullPath)) {
						mkdir($fullPath, 0777);
					}
				}
			}
		}

		return $path;
	}
}