<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Exception_403 extends Extasy_Exception_403
{
	public function __construct()
	{
		parent::__construct('Permission denied', NULL, 403);
	}
}