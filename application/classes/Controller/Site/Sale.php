<?php defined('SYSPATH') or die('No direct script access.');
class Controller_Site_Sale extends Controller_Site
{
	private $_items_on_page = 30;
    public function action_index()
    {
		$this->set_metatags_and_content('sale');
		$this->template->set_layout('layout/site/global_inner');
		$countItems = ORM::factory('Product')->where('active', '=', 1)
			->where(DB::expr('100 - (new_price/price * 100)'), '>=', 20)			->where('new_price', '!=', 0)			->where('main_image', 'IS NOT', NULL)			->where('active', '=', 1)
			->count_all();
		$page = intval($this->param('page'));
		if ($page == 1) {
			$url = str_replace('/' . $page, '', $this->request->url());
			$this->redirect($url, 301);
		}
		if (is_null($this->param('page'))) {
			$page = 1;
		}
		if ($page > ceil($countItems / $this->_items_on_page) || $page < 1) {
			$this->forward_404();
		}				$order = 'id';		$dest = 'desc';				if (Arr::get($_GET, 'order') && array_key_exists(Arr::get($_GET, 'order'), ORM::factory('Product')->list_columns())) {			$order = Arr::get($_GET, 'order');		}				if (Arr::get($_GET, 'dest') && in_array(Arr::get($_GET, 'dest'), array('desc', 'asc'))) {			$dest = Arr::get($_GET, 'dest');		}				if ($order == 'discount') {			$order = DB::expr('100 - (new_price/price * 100)');		}		$offset = $this->_items_on_page * $page - $this->_items_on_page;
		$products = ORM::factory('Product')			->where('active', '=', 1)			->where(DB::expr('100 - (new_price/price * 100)'), '>=', 20)			->where('new_price', '!=', 0)			->where('main_image', 'IS NOT', NULL)			->limit($this->_items_on_page)			->offset($offset)			->order_by($order, $dest)			->find_all()->as_array();
		$pagination =
			Pagination::factory(
				array(
					'current_page'   => array('source' => 'route', 'key' => 'page'),
					'total_items'    => $countItems,
					'items_per_page' => $this->_items_on_page,
					'view' => 'site/pagination/pagination'
				)
			)->render();
		$this->template->s_title = 'Скидки';
		$this->template->s_description = '';
		$this->template->s_keywords = '';
		$this->template->products = $products;
		$this->template->pagination = $pagination;		$looked_products_ids = array();		if (isset($_COOKIE['products']) && is_array($_COOKIE['products'])) {			foreach ($_COOKIE['products'] as $id => $value) {				if ($id) {					$looked_products_ids[] = $id;				}			}		}		$looked_products = array();		if ($looked_products_ids) {			$looked_products = ORM::factory('Product')->with('section')->where('product.id', 'IN', $looked_products_ids)->limit(12)->find_all()->as_array();		}		$this->template->looked_products = $looked_products;	}
}