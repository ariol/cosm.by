<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Controller_File_Uploader extends Controller_Admin
{
	public function after(){}

	public function action_index()
	{
		new JQFile();
	}
}