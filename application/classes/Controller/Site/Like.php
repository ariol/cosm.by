<?php defined('SYSPATH') or die('No direct script access.');



class Controller_Site_Like extends Controller_Site

{

    public function action_index()
    {
        $this->set_metatags_and_content('', 'page');
		$this->template->set_layout('layout/site/global');
		$like = Session::instance()->get('like');
		$likeitems = json_decode($like['like'], true);
		$this->template->likeitems = $likeitems;
		$this->template->likejson = $like['like'];
    }

	public function action_add()

    {
		$this->set_metatags_and_content('', 'page');
		if ($this->request->is_ajax()) {
			$id = $this->request->post('id');
			$key_type = $this->request->post('key');
			$like_items = Session::instance()->get('like');
			$array_key = $id;
			if (isset($like_items['like'])) {
				$like = json_decode($like_items['like'], true);
			} else {
				$like = array();
			}
			$items = array();
			if ($like) {
				foreach ($like as $key => $item) {
					if($item['id']) {
						$items[$key] = array(
							'id' => $item['id'],
                            'key_type' => $item['key_type']
						);
					}
				}
			}
			$items[$array_key] = array(
					'id' => $array_key,
					'key_type' => $key_type
				);
			$like_items['like'] = json_encode($items);
			Session::instance()->set('like', $like_items);
			$like = Session::instance()->get('like');
			$likeitems = json_decode(Arr::get($like, 'like'), true);
			$summlikes = count($likeitems);
			if ($likeitems){
				exit(json_encode(array('summlikes' => $summlikes)));
			}
		}
		$this->forward_404();

    }



	public function action_delete()

    {

		$this->set_metatags_and_content('', 'page');
		$this->template->set_layout('site/global');

		if ($this->request->is_ajax()) {

			$id = $this->request->post('id');
			$products_s = Session::instance()->get('like');
			$like = json_decode($products_s['like'], true);
			$items = array();

			foreach ($like as $key => $item) {
				if($id!=$item['id']) {
					$items[$key] = array(
						'id' => $item['id'],
                        'key_type' => $item['key_type']
					);
				}
			}

			$products_s['like'] = json_encode($items);

			Session::instance()->set('like', $products_s);

			$like = Session::instance()->get('like');
			$likeitems = json_decode(Arr::get($like, 'like'), true);
			$summlikes = count($likeitems);

			exit(json_encode(array('summlikes' => $summlikes)));

		}

		$this->forward_404();

    }


}

