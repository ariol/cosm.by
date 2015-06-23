<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

class Controller_Admin_Page extends Controller_Crud
{
    protected $_model = 'Page';
    
    public function action_index() 
    {
        $referer = arr::get($_SERVER, 'HTTP_REFERER');

        $referer_parts = explode('/', $referer);

        foreach($referer_parts as $part) {
            $id = intval($part);
        }
        $referer_page_type = ORM::factory('Page')->get_page_type($id);
        if($referer_page_type) {
            HTTP::redirect('/' . Extasy_Url::url_to_route('admin-static'));
        }

        HTTP::redirect('/' . Extasy_Url::url_to_route('admin-dynamic'));
    }

    public function delete_routine(ORM $item) 
    {
        if($item->static) {
            $this->forward_404();
        }

        parent::delete_routine($item);
    }
}