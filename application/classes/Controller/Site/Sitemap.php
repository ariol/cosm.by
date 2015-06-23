<?php defined('SYSPATH') or die('No direct script access.');

class Sitemap extends Kohana_Sitemap
{
	public function action_index()
	{	
		// Создаем экземпляр класса Sitemap.
		$sitemap = new Sitemap;
		// Через этот объект мы будем добавлять все УРЛы к нашей карте.
		$url = new Sitemap_URL;

		// Добавляем необходимые УРЛы к нашей карте сайта
		// Моя CMS хранит их в БД, но Вы можете и просто перечислить нужные ссылки вручную
		$allPages = DB::select('url')->from('categories')->where('active','=',true)->execute()->as_array(); // берем все ссылки из БД
		foreach ($allPages as $v) // для каждой ссылки в цикле
		{
			$priority = '0.9';
			// Выставляем приоритет индексирования. У меня - для главной страницы - 1, для остальных - 0.9. 
			if ($v['url']== '/') $priority = '1.0';
			$url->set_loc('http://'.$_SERVER['HTTP_HOST'].$v['url']) // Добавляем саму ссылку. У меня в БД они относительные, поэтому я вставляю домен перед ссылкой
						->set_last_mod(time()) // Устанавливаем время последнего редактирования. У меня временем последнего редактирования страницы всегда ставится текущее время, чтобы поисковики всегда обновляли индекс
						->set_change_frequency('always') // Показываем, что страницу нужно индексировать всегда
						->set_priority($priority);
			$sitemap->add($url); // Добавляем ссылку
		}

		// Генерируем xml
		$response = urldecode($sitemap->render());

		//Записываем в файл sitemap.xml в корне сайта
		file_put_contents('sitemap.xml', $response);
	}     
}