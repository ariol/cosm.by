<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Controller_Site_Auth extends Controller_Site
{
    public function before()
    {
        if (!Auth::instance()->logged_in()) {
            $this->redirect('/');
        }

        parent::before();
    }
}