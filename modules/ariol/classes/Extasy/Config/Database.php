<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Extasy_Config_Database implements Kohana_Config_Writer, Kohana_Config_Reader
{
	protected $_table = 'config';

	public function __construct($table = NULL)
	{
		if ( ! is_null($table))
		{
			$this->_table = $table;
		}
	}

	public function load($group)
	{
		if ( ! $this->group_available($group))
		{
			return FALSE;
		}

		return DB::select('key','value')
				->from($this->_table)
				->where('group','=',$group)
				->execute()->as_array('key', 'value');
	}

	public function write($group, $key, $config)
	{
		if (DB::select()->from($this->_table)
			->where('group', '=', $group)
			->where('key', '=', $key)
			->execute()->count())
		{
			DB::update($this->_table)
			->value('value', $config)
			->where('group', '=', $group)
			->where('key', '=', $key)
			->execute();
		}
		else
		{
			DB::insert($this->_table, array('group', 'key', 'value'))
				->values(array($group, $key, $config))->execute();
		}


	}

	private function group_available($group)
	{
		return ! in_array($group, array('database'));
	}
}