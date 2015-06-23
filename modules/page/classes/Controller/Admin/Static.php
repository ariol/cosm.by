<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Controller_Admin_Static extends Controller_Crud
{
    protected $_model = 'Page';

    protected $_group_actions = array(
    ); 

    public function after() {
        Extasy_Navigation::instance()->actions()->clear();
        parent::after();
    }
    
    public function before_fetch(ORM $item) 
    {
        $item->where('static', '=', true);

        return parent::before_fetch($item);
    }
}