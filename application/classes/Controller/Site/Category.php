<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Site_Category extends Controller_Site
{
    private $_items_on_page = 12;

    public function action_index()
    {
		$page_view = Cookie::get('page_view');
		if (!$page_view) {
			$page_view = 'grid';
		}
		
        if ($_GET['page_view']=='list') {
            $page_view = "list";
			Cookie::set('page_view', 'list', time() + 86400);
        } elseif ($_GET['page_view']=='grid') {
			Cookie::set('page_view', 'grid', time() + 86400);
            $page_view = "grid";
        }

        $this->template->page_view = $page_view;
        $page = intval($this->param('page'));

        $brands = Arr::get($_GET, 'brands', array());
		$lines = Arr::get($_GET, 'line');

        $category = ORM::factory('Category')
            ->where('url', '=', $this->param('category'))
            ->find();

        if (!$category->id) {
            $this->forward_404();
        }
		
        $PDO = ORM::factory('Product')->PDO();
		
		if ($lines) {
			$url = $_SERVER['REQUEST_URI'];
			$linesArray = $PDO->query("
				SELECT l.id, pr.brand_id FROM line l
				LEFT JOIN product pr ON pr.line_id = l.id
				WHERE l.id IN ('" . join("', '", array_keys($lines)) . "')")->fetchAll(PDO::FETCH_ASSOC); 
			$emptyLines = array();
			foreach ($linesArray as $linerow) {
				if (!in_array($linerow['brand_id'], $brands)) {
					$emptyLines[] = $linerow['id'];
					$url = str_replace('&line%5B' . $linerow['id'] . '%5D=1', '', $url);
				}
			}
			if ($emptyLines) {
				$this->redirect($url);
			}
		}

        $line_query = "SELECT line.url, line.name, line.id, product.brand_id, count(*) FROM line
                        LEFT JOIN product ON product.line_id = line.id
                        WHERE product.category_id = {$category->id}
                        AND product.active = 1";
        if ($brands) {
            $brandsList = join('", "', $brands);
            $line_query .= ' AND product.brand_id IN ("' . $brandsList . '")';
        } else {
            $line_query .= " AND product.brand_id = 'none'";
        }
		
        $line_query .= " GROUP BY line.id
						HAVING COUNT(*) > 1
                        ORDER BY line.name ASC";
        $brands_query = "SELECT brand.url, brand.name, brand.id, count(*) FROM brand
                            LEFT JOIN product ON product.brand_id = brand.id
                            WHERE product.category_id = {$category->id}
                            AND product.active = 1
                            GROUP BY brand.id
							HAVING count(*) > 1
                            ORDER BY brand.position ASC, brand.name ASC";

        $property = "SELECT properties.id, properties.name FROM product
                        LEFT JOIN product_properties ON product_properties.product_id = product.id
                        LEFT JOIN properties ON properties.id = product_properties.property_id
                        WHERE product.category_id = {$category->id}
                        AND product.active = 1
                        GROUP BY properties.id
                        ORDER BY properties.name ASC";

        $this->template->brand = $PDO->query($brands_query)->fetchAll(PDO::FETCH_ASSOC);
        $this->template->line = $PDO->query($line_query)->fetchAll(PDO::FETCH_ASSOC);
        $this->template->property = $PDO->query($property)->fetchAll(PDO::FETCH_ASSOC);

        $querymaxprice = "SELECT MAX(price) as max, MIN(price) as min FROM product
					WHERE product.category_id = {$category->id}";

        $maxprice = ORM::factory('Product')->PDO()->query($querymaxprice . " AND product.active = 1 AND product.price > 0")->fetch();
        $max_price = $maxprice['max'];
        $min_price = $maxprice['min'];


        $current_min_price = $min_price;
        $current_max_price = $max_price;
        if (isset($_GET['price']) && preg_match('%,%', $_GET['price'])) {
            list($current_min_price, $current_max_price) = explode(',', $_GET['price']);
        }

        $this->template->max_price = $max_price;
        $this->template->min_price = $min_price;

        $current_url = Arr::get($_SERVER, 'REQUEST_URI');

        $this->template->category = $category;
        $this->template->set_layout('layout/site/global');

        $filters = ORM::factory('Filter')->query(
            'select p.id as property_id, f.property_type, p.name, p.type from filters f'
            .' left join properties p on f.property_id = p.id'
			.' left join product_properties pp on pp.property_id = p.id'
            .' where f.category_id = ' . $category->id
			.' group by pp.property_id having count(*) > 1',
            true,
            true
        );

        $filters = Arr::get($filters, 'items', array());

        $property_ids = array();
        $filters_hash = array();

        foreach ($filters as $filter) {
            if ($filter['type'] == 'D') {
                $property_ids[] = $filter['property_id'];
            }
            $filters_hash[$filter['property_id']] = $filter;
        }
		
        $property_ids = array_unique($property_ids);

        $this->template->filters = $filters;

        $get_filters = Arr::get($_GET, 'filter');

        $query = 'SELECT SQL_CALC_FOUND_ROWS pr.id, pr.name as name, pr.url as url, pr.id as id'
            . ' , pr.price, pr.main_image, pr.short_content, pr.new_price, pr.article, sum(rw.rating) as reviews,  count(1) as cnt'
            . ' FROM product pr'
            . ' LEFT JOIN reviews rw ON rw.prod_id = pr.id';

        if ($get_filters) {
            $query .= ' LEFT JOIN product_properties pp ON pr.id = pp.product_id'
                . ' LEFT JOIN properties p ON p.id = pp.property_id';
        }

        if ($brands) {
            $query .= ' LEFT JOIN brand br ON br.id = pr.brand_id';
        }

        $query .= ' WHERE pr.parent_product = 0 AND pr.active=1 AND pr.category_id=' . $category->id . ' AND pr.price > 0';

        if ($brands) {
            $brandsList = join('", "', $brands);
            $query .= ' AND br.id IN ("' . $brandsList . '")';
        }
		
		if ($lines) {
			$linesList = join('", "', array_keys($lines));
            $query .= ' AND pr.line_id IN ("' . $linesList . '")';
		}

        if ($current_max_price && $current_min_price) {
            $query .= ' AND pr.price >= ' . $current_min_price . ' AND pr.price <= ' . $current_max_price;
        }

        $this->template->current_max_price = $current_max_price;
        $this->template->current_min_price = $current_min_price;

        $properties_count = 0;
        $bool_query = '';
        $integer_query = '';
        $select_query = '';
        $bool_count = 0;
        $integer_count = 0;

        foreach ($get_filters as $filter_id => $filter_value) {
            if ($filters_hash[$filter_id]['type'] == 'B' && $filter_value) {
                if (!$bool_count) {
                    $bool_query .= '(';
                }
                if ($bool_count) {
                    $bool_query .= ' OR ';
                }
                $bool_query .= 'pp.property_id=' . $filter_id . ' AND pp.value=1';
                $bool_count++;
                $properties_count++;
            }
        }

        if ($properties_count) {
            $query .= ' AND (';
        }

        if ($bool_count) {
            $bool_query .= ')';
        }

        $query .= $bool_query;

        if ($integer_query) {
            if ($bool_count) {
                $query  .= ' OR ';
            }
            $query  .= $integer_query;
        }

        if ($select_query) {
            if ($bool_count || $integer_count) {
                $query .= ' OR ';
            }
            $query .= $select_query;
        }

        if ($properties_count) {
            $query .= ')';
        }

        if ($page == 1) {
            $url = str_replace('/' . $page, '', $this->request->url());
            $this->redirect($url, 301);
        }

        if (is_null($this->param('page'))) {
            $page = 1;
        }

        $order = Arr::get($_GET, 'order');
        $dest = Arr::get($_GET, 'dest');
        $order_by_str = '';

        if (
            $order
            && $dest
            && in_array($dest, array('asc', 'desc', 'ASC', 'DESC'))
            && array_key_exists($order, ORM::factory('Product')->as_array())
        ) {
            $order_by_str = ' ORDER BY ' . 'pr.'.$order . ' ' . $dest;
        }

        $offset = $this->_items_on_page * $page - $this->_items_on_page;
        $query .= ' GROUP BY pr.id HAVING cnt >=' . $properties_count . $order_by_str;
        $query .= ' LIMIT ' . $offset . ',' . $this->_items_on_page;

        $items = ORM::factory('Product')->query($query);

		if ($this->request->is_ajax()) {
			exit(
				json_encode(array(
					'count' => $items['total_items']
				))
			);
		}
		
        $max_pages = intval(ceil($items['total_items'] / $this->_items_on_page));

        if (!$max_pages) {
            $max_pages = 1;
        }

        if ($page < 1 || $page > $max_pages) {
            $this->forward_404();
        }

        $img_width = 320;
        $img_height = 150;
        $mobile = '';

        $device = new Device();
        if ($device->is_mobile()) {
            $img_width = 700;
            $img_height = 300;
            $mobile = 'mobile';
        }

        $this->template->mobile = $mobile;
        $this->template->img_height = $img_height;
        $this->template->img_width = $img_width;
        $this->template->pagination =
            Pagination::factory(
                array(
                    'current_page'   => array('source' => 'route', 'key' => 'page'),
                    'total_items'    => $items['total_items'],
                    'items_per_page' => $this->_items_on_page,
                    'view' => 'site/pagination/pagination'
                )
            )->render();

        $this->template->product = $items['items'];
        $this->template->s_description = $category->s_description;
        $this->template->s_title = $category->name . ' - купить ' . $category->name . ' в Минске в интернет магазине cosm.by: лучшие цены, большой каталог, отзывы';
    }
}