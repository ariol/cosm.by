<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Site_Menu extends Controller
{
    public function action_top()
    {
        if(Request::initial() === Request::current()) {
            $this->forward_404();
        }

        $menu = ORM::factory('Menu')->get_parent_active_menus();

        $this->template->menu = $menu;
    }

    public static function get_children($id)
    {
         $menu = ORM::factory('Menu')->get_children_by_id($id);

        return View::factory(
            'site/menu/children',
            array(
                'menu' => $menu
            )
        );
    }
}
