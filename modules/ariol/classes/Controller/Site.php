<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Site extends Controller
{
	private $_items_on_page;
	private $_object_name;

	protected $_model;

	public function before()
	{
		parent::before();
		
		$this->template->set_layout('layout/site/global');

		//$this->sslRedirect();
	}

	private function sslRedirect()
	{
   		$action_name = Request::current()->action();

		if (!Arr::get($_SERVER, 'HTTP_X_SSL_EMU') && !preg_match('%dev%', Arr::get($_SERVER, 'HTTP_HOST'))) {
			$query = '';
			if ($_SERVER['QUERY_STRING']) {
				$query = '?' . $_SERVER['QUERY_STRING'];
			}
			$this->redirect(URL::site(Request::current()->uri() . $query, 'https'));
		}
	}
	
	public function after()
	{
		$cart = Session::instance()->get('cart');
		$like = Session::instance()->get('like');
        $cart_certificate = Session::instance()->get('cart_certificate');
		
		$cartitems = json_decode($cart['cart']);
		$likeitems = json_decode($like['like'], true);
        $certificateitems = json_decode($cart_certificate['cart_certificate']);

		$summlikes = count($likeitems);

		$result_quantity = 0;
		$result_price = 0;
		$ender='';
		if($cartitems){
			foreach($cartitems as $key => $product){
				if($product->quantity > 1){
					$result_price = $product->price * $product->quantity + $result_price;
				} else {
				    $result_price = $product->price + $result_price;
				}
				$result_quantity = $product->quantity + $result_quantity;
			}
			if($result_quantity>1 && $result_quantity<5){$ender='а';}
			if($result_quantity>4){$ender='ов';}
			if($result_quantity==1){$ender='';}
		}

        if($certificateitems){
            foreach($certificateitems as $key => $certificate){
                if($certificate->quantity > 1){
                    $result_price = $certificate->price * $certificate->quantity + $result_price;
                } else {
                    $result_price = $certificate->price + $result_price;
                }
                $result_quantity = $certificate->quantity + $result_quantity;
            }
            if($result_quantity>1 && $result_quantity<5){$ender='а';}
            if($result_quantity>4){$ender='ов';}
            if($result_quantity==1){$ender='';}

        }

		$this->template->ender = $ender;
		$this->template->result_quantity = $result_quantity;
		$this->template->cartitems = $cartitems;
		$this->template->certificateitems = $certificateitems;
		$this->template->likeitems = $likeitems;
		$this->template->summlikes = $summlikes;
		$this->template->result_price = number_format($result_price, 0, '', ' ');
		
		if (Arr::get($_GET, 'debug')) {
            $debugbar = Debug::StandardDebugBar();
            $this->template->debugbarRenderer = $debugbar->getJavascriptRenderer();
        }
		
		parent::after();
		
	}
	
    public function set_metatags_and_content($url, $name = 'page', $items_on_page = null)
    {
        $name = mb_strtolower($name);
        $name = ucfirst($name);

        $model = ORM::factory($name)
            ->get_page_by_url($url);
			
		if (array_key_exists('main_image', $model->list_columns()) && !$model->main_image) {
			$this->forward_404();
		}

        if(!$model->loaded() || (isset($model->md5_url) && $model->md5_url != md5($url))) {
            $this->forward_404();
        }

		$this->_items_on_page = $items_on_page;
		$this->_model = $model;

        $this->template->model = $model;

		$this->_object_name = str_replace('Model_', '', get_class($this->_model));

		$list_columns = $this->_model->list_columns();

		foreach ($list_columns as $column => $params) {
			$this->template->$column = $this->_model->$column;
			switch ($column) {
				case 's_title': {
					$this->template->s_title = $this->set_title();
					break;
				}
			}
		}

		foreach ($this->_model->has_many() as $column => $params) {
			if ($this->_items_on_page && $column != 'children' && $column != 'filters') {
				$pagination_items = $this->get_pagination_items($column, $params);
				$this->template->$column = $pagination_items['items'];
				if ($pagination_items['pagination']) { 
					$this->template->pagination = $pagination_items['pagination'];
				}
			}
			else {
				if ($column != 'filters') {
					$this->template->$column = $this->_model->$column->where('active', '=', 1)->find_all();
				}
			}
		}

		foreach ($this->_model->belongs_to() as $column => $params) {
			if ($params['model'] == 'Brand' || $params['model'] == 'Category') {
				continue;
			}
			$model = ORM::factory($params['model'])->fetchByUrl($this->param($column));
			if ($model->id != $this->_model->{$params['foreign_key']} && $column != 'parent' && $column != 'parent') {
				$this->forward_404();
			}
			foreach ($model->list_columns() as $model_column => $model_params) {
				$this->template->{$column . '_' . $model_column} = $model->$model_column;
			}
		}

		if (isset($list_columns['more_images'])) {
			$this->template->content = $this->replace_images();
		}
		
		$device = new Device();
		
		$this->template->is_mobile = $device->is_mobile();
    }

	private function set_title()
	{
		$title = $this->_model->name;

		if($this->_model->s_title) {
			$title = $this->_model->s_title;
		}

		return $title;
	}

	private function replace_images()
	{
		$model_name = get_class($this->_model);
	
		$content = preg_replace_callback('%(\[.*\])%isU', function($match) {
			$more_images = json_decode($this->_model->more_images);
			if (!strstr($match[1], '|')) {
				$match[1] = str_replace(']', '|]', $match[1]);
			}
			$parts = explode('|', $match[1]);
			$image_num = str_replace('[image_', '', $parts[0]) - 1;
			$image_alt = str_replace(']', '', $parts[1]);
			if (isset($more_images[$image_num]) && $image = $more_images[$image_num]) {
				return '<img src="' . Lib_Image::resize_width(
							$image,
							mb_strtolower($this->_object_name),
							$this->_model->id,
							$model_name::BIG_WIDTH
						) . '" class="img-rounded" alt="' . $image_alt . '" />';
			}
		}, $this->_model->content);

		return $content;
	}
	

	private function get_pagination_items($column, array $params = array())
	{
		$result = array();

		$countItems = ORM::factory($this->_object_name)->fetchCountByModelId($this->_model->id, $column);
		$page = intval($this->param('page'));

		if ($page == 1) {
			$url = str_replace('/' . $page, '', $this->request->url());
			$this->redirect($url, 301);
		}

		if (is_null($this->param('page'))) {
			$page = 1;
		}
		
		$max_pages = intval(ceil($countItems / $this->_items_on_page));
		if (!$max_pages) {
			$max_pages = 1;
		}
		
		$required = Arr::get($params, 'required');
		
		if ($page < 1 || ($required && $page > $max_pages)) {
			$this->forward_404();
		}
		
		if ($page > 1) {
			$this->_model->s_title .= ' (страница ' . $page . ')';
		}

		$result['items'] =
			ORM::factory($this->_object_name)->fetchPageByParentModelId(
				$this->_model->id,
				$this->_items_on_page,
				/*offset*/($page - 1) * $this->_items_on_page,
				$column,
				Arr::get($_GET, 'order'),
				Arr::get($_GET, 'dest')
			);

		$result['pagination'] =
			Pagination::factory(
				array(
					'current_page'   => array('source' => 'route', 'key' => 'page'),
					'total_items'    => $countItems,
					'items_per_page' => $this->_items_on_page,
					'view' => 'site/pagination/pagination'
				)
			)->render();

		return $result;
	}
}