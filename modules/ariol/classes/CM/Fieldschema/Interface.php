<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

interface CM_Fieldschema_Interface
{
	public function set_field($name, CM_Field $field, $position = NULL);

	public function get_field($name);

	public function has_field($name);

	public function remove_field($name);

	public function get_field_names();

	public function set_values(array $values);

	public function get_values();
}