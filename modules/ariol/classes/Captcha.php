<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Captcha
{
	static public function render()
	{
		include_once(dirname(__FILE__).'/../libs/kcaptcha/kcaptcha.php');
		$captcha = new KCAPTCHA();
		Session::instance()->set('CAPTHCA_KEYSTRING', $captcha->getKeyString());
		exit(0);
	}
	
	static public function test($key)
	{
		return $key === Session::instance()->get('CAPTHCA_KEYSTRING');
	}
}