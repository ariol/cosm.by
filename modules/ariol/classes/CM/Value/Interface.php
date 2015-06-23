<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

interface CM_Value_Interface
{
	public function __construct($raw_value = NULL);
	public function get_raw();
	public function is_valid();
	public function __toString();
}