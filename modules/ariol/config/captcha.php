<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

return array(
	'alphabet' => "0123456789abcdefghijklmnopqrstuvwxyz",
	'allowed_symbols' => "23456789",
	'length' => mt_rand(5,6),
	'width' => 120,
	'height' => 60,
	'show_credits' => FALSE,
	'credits' => '',
	'foreground_color' => array(mt_rand(0,100), mt_rand(0,100), mt_rand(0,100)),
	'background_color' => array(mt_rand(200,255), mt_rand(200,255), mt_rand(200,255))
);