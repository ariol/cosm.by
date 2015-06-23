<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Model_Changelog extends ORM
{
	protected $_table_name = 'changelog';
	
	protected $_created_column = array('column' => 'created_at', 'format' => TRUE);
}