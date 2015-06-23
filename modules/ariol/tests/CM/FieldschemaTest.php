<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

/**
 * @group cm
 */
class CM_FieldschemaTest extends Kohana_Unittest_TestCase
{
	public function test_set_field()
	{
		$fieldschema = new CM_Fieldschema();

		$fieldschema->set_field('test1', new CM_Field_String());

		$this->assertTrue($fieldschema->has_field('test1'));
	}
}