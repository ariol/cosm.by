<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

interface CM_Fieldschema_Container_Interface
{
	public function get_fieldschema();

	public function set_values($values = array());

	public function save();
}