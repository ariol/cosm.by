<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Text extends Kohana_Text
{
	static public function to_url($str)
	{
		$url = strtolower(self::translit($str));

		$url = preg_replace('#[^a-z0-9]#', '-', $url);

		$url = self::reduce_slashes($url);

		return trim($url, '-');
	}

	static public function filename($filename)
	{
		// 1) делаем транслитерацию
		$filename = self::translit($filename);
		// 2) Заменяем все символы не из белого листа на дефисы
		$filename = preg_replace('/[^\p{L}\p{Nd}\+\-_]+/ui', '-', $filename);
		// 3) Чистим дефисы в начале и конце
		$filename = trim($filename, '-');
		// 4) Заменяем все последовательные дефисы одинарными
		$filename = preg_replace('/-+/', '-', $filename);
		// 5) Переводим строку в нижний регистр
		// После транслита останется только латиница, поэтому можно сделать просто strtolower()
		$filename = strtolower($filename);

		return $filename;
	}

	static public function translit($str)
	{
		$tr = array(
			"А"=>"A","Б"=>"B","В"=>"V","Г"=>"G",
			"Д"=>"D","Е"=>"E","Ё"=>"YO","Ж"=>"J","З"=>"Z","И"=>"I",
			"Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N",
			"О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T",
			"У"=>"U","Ф"=>"F","Х"=>"H","Ц"=>"TS","Ч"=>"CH",
			"Ш"=>"SH","Щ"=>"SCH","Ъ"=>"","Ы"=>"YI","Ь"=>"",
			"Э"=>"E","Ю"=>"YU","Я"=>"YA","а"=>"a","б"=>"b",
			"в"=>"v","г"=>"g","д"=>"d","е"=>"e","ё"=>"yo","ж"=>"j",
			"з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
			"м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
			"с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
			"ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
			"ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya"
		);
		return strtr($str,$tr);
	}
}