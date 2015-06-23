<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Controller_Extasy_File extends Controller
{
	public function before() {}

	protected function process_file($file)
	{
		if ( ! file_exists(Kohana::$config->load('file.upload_root').'/'.$file))
		{
			$this->forward_404();
		}
		return Kohana::$config->load('file.upload_root').'/'.$file;
	}

	public function action_get()
	{
		$filename = $this->param('file');
		$filename = preg_replace('#\.+#', '.', $filename);

		$file = NULL;

		$ruleset = new Extasy_File_Ruleset;
		$file = $ruleset->process($filename);

		if (is_null($file))
		{
			$this->forward_404();
		}

		header('Content-Type: '.$file->get_mime_type());

		echo $file->get_contents();

		exit(0);
	}

	public function after() {}
}