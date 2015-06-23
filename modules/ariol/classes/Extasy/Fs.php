<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_FS
{
	static public function delete($filename)
	{
		if (self::is_dir($filename))
		{
			self::delete_dir($filename);
		}
		else if (self::is_file($filename))
		{
			self::delete_file($filename);
		}
	}

	static public function delete_dir_contents($dirname)
	{
		if ( ! self::is_dir($dirname))
		{
			return;
		}

		$dir = new DirectoryIterator($dirname);
		while ($dir->valid())
		{
			if ( ! $dir->isDot())
			{
				self::delete($dir->getRealPath());
			}
			$dir->next();
		}
	}

	static protected function delete_dir($dirname)
	{
		$dir = new DirectoryIterator($dirname);
		while ($dir->valid())
		{
			if ( ! $dir->isDot())
			{
				self::delete($dir->getRealPath());
			}
			$dir->next();
		}
		rmdir($dirname);
	}

	static protected function delete_file($filename)
	{
		unlink($filename);
	}

	static public function is_dir($filename)
	{
		return file_exists($filename) AND is_dir($filename);
	}

	static public function is_file($filename)
	{
		return file_exists($filename) AND ! is_dir($filename);
	}

	static public function move($source, $destination, $overwrite = FALSE)
	{
		if (self::is_dir($source))
		{
			self::move_dir($source, $destination, $overwrite);
		}
		else if (self::is_file($source))
		{
			self::move_file($source, $destination, $overwrite);
		}
	}

	static protected function move_dir($source, $destination, $overwrite)
	{
		if ( ! self::is_dir($source))
		{
			throw new Exception('Only directory must be passed to move_dir() as source');
		}
		if ( ! self::is_dir($destination))
		{
			throw new Exception('Only directory must be passed to move_dir() as destination');
		}

		$basename = pathinfo($source, PATHINFO_BASENAME);

		if ( ! self::is_dir($destination.'/'.$basename))
		{
			if ( ! mkdir($destination.'/'.$basename, fileperms($source), TRUE))
			{
				throw new Exception('Can not create directory '.$destination.'/'.$basename);
			}
		}

		$dir = new DirectoryIterator($source);
		while ($dir->valid())
		{
			if ( ! $dir->isDot())
			{
				self::move($dir->getRealPath(), $destination.'/'.$basename, $overwrite);
			}
			$dir->next();
		}
		self::delete_dir($source);
	}

	static protected function move_file($source, $destination, $overwrite)
	{
		if (self::is_dir($destination))
		{
			$basename = pathinfo($source, PATHINFO_BASENAME);
			self::move_file($source, $destination.'/'.$basename, $overwrite);
		}
		else
		{
			if ( ! $overwrite AND self::is_file($destination))
			{
				return;
			}

			if ( ! rename($source, $destination))
			{
				throw new Exception('Can not rename file '/$source.' to '.$destination);
			}
		}
	}
}