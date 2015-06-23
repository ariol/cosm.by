<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 * @author FlaBla
 */

class Form_Admin_Feedback extends CM_Form_Abstract
{
	protected function init()
	{
		$this->add_plugin(new CM_Form_Plugin_ORM());
	}
}