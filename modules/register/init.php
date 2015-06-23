<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

Route::set('site-auth', 'module_auth(/<action>)')
    ->defaults(array(
        'directory' => 'module',
        'controller' => 'auth',
    ));