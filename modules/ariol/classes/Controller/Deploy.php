<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Controller_Deploy extends Controller
{
	public function action_create_changescript()
	{
		$sql_dir = Kohana::$config->load('deploy')->sql_dir;
		$deltas_dir = $sql_dir.'/deltas/';

		// Вообще, это та директория?
		if ( ! file_exists($deltas_dir) OR ! is_dir($deltas_dir))
		{
			echo "Deltas dir not available\n";
			exit(1);
		}

		/**
		 * Получаем список доступных непримененных дельт
		 */
		$deltas = array();
		foreach (new DirectoryIterator($deltas_dir) as $file_info)
		{
			// Это должен быть файл
			if ( ! $file_info->isFile())
			{
				continue;
			}

			$path_info = pathinfo($file_info->getPathname());

			// Это должен быть .sql файл
			if ( strtolower(arr::get($path_info, 'extension')) !== 'sql')
			{
				continue;
			}

			// Его не должно быть в БД
			$applyed = ORM::factory('changelog', array('filename' => $file_info->getFilename()));
			if ($applyed->loaded())
			{
				continue;
			}

			// Ок, значит этот надо применить
			$deltas[$file_info->getFilename()] = $file_info->getPathname();
		}

		if (empty($deltas))
		{
			echo "Deltas not found\n";
			exit(0);
		}

		echo "Found deltas:\n";

		ksort($deltas);

		// Делаем changescript
		$changescript = '';
		$db = Database::instance();
		$table_name = $db->quote_table(ORM::factory('changelog')->table_name());
		foreach ($deltas as $filename => $pathname)
		{
			echo $filename."\n";
			$changescript .= file_get_contents($pathname)."\r";
			$changescript .= 'INSERT INTO '.$table_name.' (created_at, filename) VALUES (UNIX_TIMESTAMP(NOW()), '.$db->quote($filename).");\n";
		}
		file_put_contents($sql_dir.'/update.sql', $changescript);
		echo 'Changescript created: update.sql'."\n";
		exit(0);
	}
}