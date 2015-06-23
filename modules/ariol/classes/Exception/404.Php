<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Exception_404 extends Extasy_Exception_404 
{
	public function __construct()
	{
		parent::__construct('Not found', NULL, 404);
	}
}