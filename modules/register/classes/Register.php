<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Register
{
    private static $_reg_id = 'reg';
    private static $_login_id = 'enter';

    static public function registerModal()
    {
        return View::factory('site/module_auth/register', array('id' => self::$_reg_id))->render();
    }

    static public function registerButton($name, $class = null)
    {
		if ($class) {
			$class = $class . ' ' . self::$_reg_id;
		}
		else {
			$class = self::$_reg_id;
		}
		
        return HTML::anchor('#' . self::$_reg_id, $name, array(
            'data-toggle' => 'modal',
            'data-target' => '#' . self::$_reg_id,
            'class' => $class
        ));
    }

    static public function loginModal()
    {
        return View::factory('site/module_auth/login', array('id' => self::$_login_id))->render();
    }

    static public function loginButton($name, $class = null)
    {
		if ($class) {
			$class = $class . ' ' . self::$_login_id;
		}
		else {
			$class = self::$_login_id;
		}
		
        return HTML::anchor('#' . self::$_login_id, $name, array(
            'data-toggle' => 'modal',
            'data-target' => '#' . self::$_login_id,
            'class' => $class
        ));
    }

    static public function ajaxFunctions()
    {
        return '/module_auth/get_media';
    }

    static public function ajaxFunctionsLight()
    {
        return '/module_auth/get_light_media';
    }

    static public function logout()
    {
        return '/module_auth/logout';
    }
}