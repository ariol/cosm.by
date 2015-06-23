<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_View extends Kohana_View
{
	private $_layout = NULL;

	static protected $_files = array();

	public function set_layout($layout)
	{
		$this->_layout = $layout;

		return $this;
	}

	public function render($file = NULL)
	{
		if(is_null($this->_layout))
		{
			return parent::render($file);
		}
		else
		{
			$this->content = parent::render($file);
			return parent::render($this->_layout);
		}
	}

	public function set_filename($file)
	{
		if(isset(self::$_files[$file]))
		{
			return parent::set_filename(self::$_files[$file]);
		}

		// На основе данных из Request попробуем сообразить, где искать нужный скрипт вида
		$request = Request::current();

		if ($request)
		{
			// Ищем файл либо в <directory>/<controller> и <directory>, либо в <controller>
			// в зависимости от того, указана ли <directory>
            $directory = mb_strtolower(($request->directory()));
            $controller = mb_strtolower($request->controller());
			if($directory)
			{
				// <directory>/<controller>
				$new_file = $directory.'/'.$controller.'/'.$file;
				if(($path = Kohana::find_file('views', $new_file)) !== FALSE)
				{
					self::$_files[$file] = $new_file;
					return parent::set_filename($new_file);
				}

				// <directory>
				$new_file = $directory.'/'.$file;
				if(($path = Kohana::find_file('views', $new_file)) !== FALSE)
				{
					self::$_files[$file] = $new_file;
					return parent::set_filename($new_file);
				}
			}
			else
			{
				// <controller>
				$new_file = $controller.'/'.$file;
				if(($path = Kohana::find_file('views', $new_file)) !== FALSE)
				{
					self::$_files[$file] = $new_file;
					return parent::set_filename($new_file);
				}
			}

			// Если не нашли в подходящих директориях - пытаемся поискать в обычном месте
			if(($path = Kohana::find_file('views', $file)) !== FALSE)
			{
				self::$_files[$file] = $file;
				return parent::set_filename($file);
			}

			// Последний оплот - ядро экстази
			$new_file = 'extasy/'.$file;

			if(($path = Kohana::find_file('views', $new_file)) !== FALSE)
			{
				self::$_files[$file] = $new_file;
				return parent::set_filename($new_file);
			}
		}

		// Нигде не нашли - ну и пусть Kohana сама разбирается с этой ситуацией

		self::$_files[$file] = $file;
		return parent::set_filename($file);
	}

	static public function strip_tags($str)
	{
		return strip_tags($str);
	}
}