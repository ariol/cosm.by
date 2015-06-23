<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

/**
 * @group  test
 */
class SimpleTest extends Kohana_Unittest_TestCase
{
	public function test_add()
	{
		$this->assertEquals(4, 2 + 2);
	}
}