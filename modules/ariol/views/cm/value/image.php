<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

try
{
	echo html::anchor($value->get_download_filename(), html::image(
		$thumb_filename,
		array(
			'alt' => $value->get_alt()
		)
	));
}
catch (Exception $e)
{
	echo 'error';
}