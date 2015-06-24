<?php defined('SYSPATH') or die('No direct script access.');
class Controller_Site_Brand extends Controller_Site
{	
    private $_items_on_page = 12;

    public function action_index()
    {
        if ($_GET['page_view']=='list') {
            $page_view = "list";
        } else {
            $page_view = "grid";
        }
        $this->template->page_view = $page_view;
		$brand_url = $this->param('url');
		$this->template->set_layout('layout/site/global');
        
        $lines = Arr::get($_GET, 'line');

        $brand = ORM::factory('Brand')->where('url', '=', $brand_url)->where('active', '=', 1)->find();
        if (!$brand->loaded()) {			$this->forward_404();		}
        $this->template->brand = $brand;
        $countItems = ORM::factory('Product')->where('brand_id', '=', $brand->id)->where('active', '=', 1);
        if ($lines) {
            $countItems = $countItems->where('line_id', 'IN', array_keys($lines));
        }
        $countItems = $countItems->count_all();
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
        if ($page < 1 || $page > $max_pages) {
            $this->forward_404();		
        }

        $products =  ORM::factory('Product')->where('product.brand_id', '=', $brand->id)
                    ->where('product.parent_product', '=', '')
                    ->limit($this->_items_on_page)->offset(($page - 1) * $this->_items_on_page)->where('product.active', '=', 1);
       
        if ($lines) {
            $products = $products->where('product.line_id', 'IN', array_keys($lines));
        }

        $products = $products->find_all();

        $this->template->product = $products;


        $pagination =Pagination::factory(array(	'current_page' => array('source' => 'route', 'key' => 'page'),
            'total_items'    => $countItems,
            'items_per_page' => $this->_items_on_page,
            'view' => 'site/pagination/pagination')	)->render();$this->template->pagination = $pagination;
        $PDO = ORM::factory('Brand')->PDO();
        $title = 'Производитель '.$brand->name.' в интернет-магазине 1teh. Продукция '.$name.' в Беларуси';
        if ($page > 1) {
            $title .= ' (страница ' . $page . ')';
        }
        $PDO_line = ORM::factory('Line')->PDO();
        $line_query = "SELECT line.url, line.name, line.id FROM line
                        LEFT JOIN product ON product.line_id = line.id
                        WHERE product.brand_id = {$brand->id}
                        AND product.active = 1
                        GROUP BY line.id
                        ORDER BY line.name ASC";
        $this->template->line = $PDO_line->query($line_query)->fetchAll(PDO::FETCH_ASSOC);
        $querymaxprice = "SELECT MAX(price) as max, MIN(price) as min FROM product
					WHERE product.brand_id = {$brand->id}";
        $maxprice = ORM::factory('Product')->PDO()->query($querymaxprice . " AND product.active = 1 AND product.price > 0")->fetch();
        $max_price = $maxprice['max'];
        $min_price = $maxprice['min'];

        if (isset($_GET['max_price'])) {
            $max_price = $_GET['max_price'];
        }
        if (isset($_GET['min_price'])) {
            $min_price = $_GET['min_price'];
        }
        $this->template->max_price = $max_price;
        $this->template->min_price = $min_price;
        $this->template->s_title = $title;
        $this->template->pagination = $pagination;
    }
}