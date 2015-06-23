<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

return array(
	'upload_root' => DOCROOT,
	'ruleset' => array(
		'#^(.*)$#' => array(
			'factory' => new Extasy_File_Factory,
			'modifiers' => array(
				new Extasy_FileModifier_Load('$1')
			)
		)
	)
);