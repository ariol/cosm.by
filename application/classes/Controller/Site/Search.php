<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Site_Search extends Controller_Site
{
    public function action_index()
    {
		$this->template->set_layout('layout/site/global');
	
		$limit = 15;
		
		$page = Arr::get($_GET, 'p');
		if (!$page) {
			$page = 1;
		}
		
		$offset = $limit * $page - $limit;
	
		$search_result = ORM::factory('Product')->search(
			array('name', 'article', 'short_content'),
			Arr::get($_GET, 'q'),
			$limit,
			$offset,
			Arr::get($_GET, 'order'),
			Arr::get($_GET, 'dest'),
			array('price')
		);
		
		$count_pages = ceil($search_result['count_all'] / $limit);
		
		if ($count_pages && $page > $count_pages || $page < 1) {
			$this->forward_404();
		}
		
		$this->template->pagination = View::factory('site/search/pagination', array(
			'page' => $page,
			'count_pages' => $count_pages
		));
		$this->template->countall = $search_result['count_all'];
		$this->template->items = $search_result['result'];
		
		$this->template->s_title = 'Результаты поиска по запросу: ' . Arr::get($_GET, 'q');
	}
	
	public function action_ajax()
    {
		if ($this->request->is_ajax()) {
			$limit = 5;
			
			$q = $this->request->post('q');
			$order = $this->request->post('order');
			$dest = $this->request->post('dest');
			
			$cat_result = ORM::factory('Category')->search(
				array('name'),
				$q,
				$limit
			);
			
			$search_result = ORM::factory('Product')->search(
				array('name', 'short_content', 'article'),
				$q,
				$limit,
				0,
				$order,
				$dest,
				array('price')
			);

			$view = View::factory('site/search/ajax', array(
					'search_result' => $search_result['result'],
					'cat_result' => $cat_result['result'],
					
				))->render();
				
			echo json_encode(array('view' => $view));
			exit();
		}
		$this->forward_403();
	}
}

