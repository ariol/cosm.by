<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

abstract class Controller_Cli_Abstract extends Kohana_Controller
{
	public function before()
	{
		if (Request::initial()->protocol() != 'CLI')
		{
			throw new HTTP_Exception_403();
		}

		while (ob_get_level())
		{
			ob_end_flush();
		}

		Kohana::$profiling = FALSE;
	}

	public function after()
	{

	}
}