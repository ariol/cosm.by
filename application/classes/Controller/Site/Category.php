<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Site_Category extends Controller_Site
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
        $page = intval($this->param('page'));

        $brands = Arr::get($_GET, 'brands');

        $category = ORM::factory('Category')
            ->where('url', '=', $this->param('category'))
            ->find();

        if (!$category->id) {
            $this->forward_404();
        }

        $PDO = ORM::factory('Product')->PDO();

        $line_query = "SELECT line.url, line.name, line.id FROM line
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
                        ORDER BY line.name ASC";
        $brands_query = "SELECT brand.url, brand.name, brand.id FROM brand
                            LEFT JOIN product ON product.brand_id = brand.id
                            WHERE product.category_id = {$category->id}
                            AND product.active = 1
                            GROUP BY brand.id
                            ORDER BY brand.name ASC";

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


        if (isset($_GET['max_price'])) {
            $max_price = $_GET['max_price'];
        }

        if (isset($_GET['min_price'])) {
            $min_price = $_GET['min_price'];
        }

        $this->template->max_price = $max_price;
        $this->template->min_price = $min_price;

        $current_url = Arr::get($_SERVER, 'REQUEST_URI');

        $empty_filters = array();

        if (is_array(Arr::get($_GET, 'filter'))) {
            foreach (Arr::get($_GET, 'filter') as $id => $get_value) {
                if ($get_value === '') {
                    $empty_filters[] = '&filter[' . $id . ']=';
                }
                $from  = Arr::get($get_value, 0);
                $to  = Arr::get($get_value, 1);
                if ($from === '') {
                    $empty_filters[] = '&filter[' . $id . '][0]=';
                }
                if ($to === '') {
                    $empty_filters[] = '&filter[' . $id . '][1]=';
                }
            }
        }

        if ($empty_filters) {
            $current_url = str_replace($empty_filters, '', $current_url);
            if (!preg_match('%\?%', $current_url)) {
                $current_url = preg_replace('%&%', '?', $current_url, 1);
            }
            $this->redirect('http://' . $_SERVER['SERVER_NAME'] . $current_url);
        }

        $this->template->category = $category;
        $this->template->set_layout('layout/site/global');

        $filters = ORM::factory('Filter')->query(
            'select * from filters f'
            .' left join properties p on f.property_id = p.id'
            .' where f.category_id = ' . $category->id,
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
            $filters_hash[$filter['id']] = $filter;
        }

        $property_ids = array_unique($property_ids);

        $dictionaty_ids = array();
        if ($property_ids) {
            $dictionaty_ids = $PDO->query("SELECT DISTINCT pp.dictionary_id FROM product_properties pp
                LEFT JOIN product pr ON pp.product_id = pr.id
                LEFT JOIN categories c ON pr.category_id = c.id
                WHERE pp.property_id in (" . join(',', $property_ids) . ")
                AND c.id = {$category->id} AND pr.active = 1 AND pr.price > 0")->fetchAll(PDO::FETCH_COLUMN);
        }

        $dictionaries = Cache::instance()->get('dictionaries');

        if (!$dictionaries) {
            $dictionaries = array('total_items' => 0, 'items' => array());
            if ($dictionaty_ids) {
                foreach ($dictionaty_ids as $id) {
                    $result = ORM::factory('Filter')->query(
                        "select
							dv.id, dv.property_id, dv.value, dv.1cid, count(*) as count_products
						from dictionary_values dv
						left join product_properties pp on pp.property_id = dv.property_id
						left join product pr on pr.id = pp.product_id
							where pr.category_id = " . $category->id . "
							and dv.id = '{$id}'
							and pp.dictionary_id = '{$id}'
							and pr.active = 1 and pr.price > 0
							group by dv.id, pp.dictionary_id
							having count_products > 0",
                        true,
                        true
                    );

                    $dictionaries['items'] = array_merge(Arr::get($dictionaries, 'items', array()), $result['items']);
                    $dictionaries['total_items'] += $result['total_items'];
                }
            }
            Cache::instance()->set('dictionaries', $dictionaries, 3600);
        }

        $dictionariesHash = array();

        foreach (Arr::get($dictionaries, 'items', array()) as $dv) {
            $dictionariesHash[$dv['property_id']][] = $dv;
        }

        $this->template->dictionaries = $dictionariesHash;
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

        $query .= ' WHERE pr.parent_product = "" AND pr.active=1 AND pr.category_id=' . $category->id . ' AND pr.price > 0';

        if ($brands) {
            $brandsList = join('", "', $brands);
            $query .= ' AND br.id IN ("' . $brandsList . '")';
        }

        $query .= ' AND pr.price >= ' . $min_price . ' AND pr.price <= ' . $max_price;

        $properties_count = 0;
        $bool_query = '';
        $integer_query = '';
        $select_query = '';
        $bool_count = 0;
        $integer_count = 0;

        foreach ($get_filters as $filter_id => $filter_value) {
            if (is_array($filter_value)) {
                $properties_count++;
                if ($select_query) {
                    $select_query .= ' OR ';
                }
                $select_query .= 'pp.property_id=' . $filter_id . ' AND pp.dictionary_id IN ("' . join('","' , $filter_value) . '")';
            }

            if ($filters_hash[$filter_id]['type'] == 'I') {
                $from  = Arr::get($filter_value, 0) ? Arr::get($filter_value, 0) : 0;
                $to  = Arr::get($filter_value, 1) ? Arr::get($filter_value, 1) : 0;

                if ($from || $to) {
                    if ($integer_query) {
                        $integer_query  .= ' OR ';
                    }
                }

                if ($to && $from) {
                    $integer_query .= 'pp.property_id=' . $filter_id . ' AND pp.value >= ' . $from .' AND pp.value <= ' . $to;
                } elseif ($to) {
                    $integer_query .= 'pp.property_id=' . $filter_id . ' AND pp.value <= ' . $to;
                } elseif ($from) {
                    $integer_query .= 'pp.property_id=' . $filter_id . ' AND pp.value >= ' . $from;
                }

                if ($from || $to) {
                    $integer_count++;
                    $properties_count++;
                }
            }

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
        $query .= ' GROUP BY pr.id' . $order_by_str;
        $query .= ' LIMIT ' . $offset . ',' . $this->_items_on_page;

        $items = ORM::factory('Product')->query($query);

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
        $this->template->s_title = $category->name . ' - купить ' . $category->name . ' в Минске в интернет магазине euroshoes: лучшие цены, большой каталог, отзывы';
    }

    public function action_brand()
    {
        if ($_GET['page_view']=='list') {
            $page_view = "list";
        } else {
            $page_view = "grid";
        }

        $this->template->page_view = $page_view;
        $category_url = $this->param('category');
        $page = intval($this->param('page'));
        $brand_url = $this->param('brand');
        $brand = ORM::factory('Brand')->where('url', '=', $brand_url)->where('active', '=', 1)->find();
        $this->template->brand = $brand;


        if (preg_match('#%5B|%2C|%3F|%3D#', $_SERVER['REQUEST_URI'])) {
            $url = urldecode($_SERVER['REQUEST_URI']);
            if (preg_match('#filtered|Подобрать#', $url)) {
                $url = str_replace('&filtered=', '', $url);
                $url = str_replace('filtered=Подобрать', '', $url);
                $url = str_replace('/' . $page . '?', '?', $url);
                $url = str_replace(array('?&', '??'), '?', $url);
                $url = str_replace('&&', '&', $url);
                $url = str_replace('Подобрать', '', $url);
            }

            $this->redirect('http://' . $_SERVER['SERVER_NAME'] . $url);
        }


        $category = ORM::factory('Category')
            ->where('url', '=', $this->param('category'))
            ->find();


        $PDO = ORM::factory('Product')->PDO();

        $brands_query = "SELECT brand.url, brand.name, brand.id FROM product
					LEFT JOIN brand ON brand.id = product.brand_id
					WHERE product.category_id = {$category->id}
					AND product.active = 1
					AND brand.id IS NOT NULL
					GROUP BY brand.id
					HAVING COUNT(product.id) > 0
					ORDER BY brand.name ASC";

        $this->template->brands = $PDO->query($brands_query)->fetchAll(PDO::FETCH_ASSOC);

        $querymaxprice = "SELECT MAX(price) as max, MIN(price) as min FROM product
					WHERE product.category_id = {$category->id}";

        $maxprice = ORM::factory('Product')->PDO()->query($querymaxprice . " AND product.active = 1 AND product.price > 0")->fetch();

        $this->template->max_price = $maxprice['max'];
        $this->template->min_price = $maxprice['min'];

        $filter_price_values = array($this->template->min_price, $this->template->max_price);

        if (Arr::get($_GET, 'price')) {
            $filter_price_values = explode(',', $_GET['price']);
        }

        $current_url = Arr::get($_SERVER, 'REQUEST_URI');

        $empty_filters = array();

        if (is_array(Arr::get($_GET, 'filter'))) {
            foreach (Arr::get($_GET, 'filter') as $id => $get_value) {
                if ($get_value === '') {
                    $empty_filters[] = '&filter[' . $id . ']=';
                }
                $from  = Arr::get($get_value, 0);
                $to  = Arr::get($get_value, 1);
                if ($from === '') {
                    $empty_filters[] = '&filter[' . $id . '][0]=';
                }
                if ($to === '') {
                    $empty_filters[] = '&filter[' . $id . '][1]=';
                }
            }
        }

        if ($empty_filters) {
            $current_url = str_replace($empty_filters, '', $current_url);
            if (!preg_match('%\?%', $current_url)) {
                $current_url = preg_replace('%&%', '?', $current_url, 1);
            }
            $this->redirect('http://' . $_SERVER['SERVER_NAME'] . $current_url);
        }

        $this->template->filter_price_values = $filter_price_values;
        $this->template->category = $category;
        $this->template->set_layout('layout/site/global');

        $filters = ORM::factory('Filter')->query(
            'select * from filters f'
            .' left join properties p on f.property_id = p.id'
            .' where f.category_id = ' . $category->id,
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
            $filters_hash[$filter['id']] = $filter;
        }

        $property_ids = array_unique($property_ids);

        $dictionaty_ids = array();
        if ($property_ids) {
            $dictionaty_ids = $PDO->query("SELECT DISTINCT pp.dictionary_id FROM product_properties pp
                LEFT JOIN product pr ON pp.product_id = pr.id
                LEFT JOIN categories c ON pr.category_id = c.id
                WHERE pp.property_id in (" . join(',', $property_ids) . ")
                AND c.id = {$category->id} AND pr.active = 1 AND pr.price > 0")->fetchAll(PDO::FETCH_COLUMN);
        }

        $dictionaries = Cache::instance()->get('dictionaries');

        if (!$dictionaries) {
            $dictionaries = array('total_items' => 0, 'items' => array());
            if ($dictionaty_ids) {
                foreach ($dictionaty_ids as $id) {
                    $result = ORM::factory('Filter')->query(
                        "select
							dv.id, dv.property_id, dv.value, dv.1cid, count(*) as count_products
						from dictionary_values dv
						left join product_properties pp on pp.property_id = dv.property_id
						left join product pr on pr.id = pp.product_id
							where pr.category_id = " . $category->id . "
							and dv.id = '{$id}'
							and pp.dictionary_id = '{$id}'
							and pr.active = 1 and pr.price > 0
							group by dv.id, pp.dictionary_id
							having count_products > 0",
                        true,
                        true
                    );

                    $dictionaries['items'] = array_merge(Arr::get($dictionaries, 'items', array()), $result['items']);
                    $dictionaries['total_items'] += $result['total_items'];
                }
            }
            Cache::instance()->set('dictionaries', $dictionaries, 3600);
        }

        $dictionariesHash = array();

        foreach (Arr::get($dictionaries, 'items', array()) as $dv) {
            $dictionariesHash[$dv['property_id']][] = $dv;
        }

        $this->template->dictionaries = $dictionariesHash;
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

        if ($brand) {
            $query .= ' LEFT JOIN brand br ON br.id = pr.brand_id';
        }

        $query .= ' WHERE pr.active=1 AND pr.brand_id=' . $brand->id .  ' AND pr.category_id=' . $category->id;

        if ($brand) {
            $query .= ' AND br.id=' . $brand;
        }

        $price = Arr::get($_GET, 'price');

        if ($price) {
            list($price_from, $price_to) = explode(',', $price);
            $query .= ' AND pr.price >= ' . $price_from . ' AND pr.price <= ' . $price_to;
        }

        $this->template->brand_id = $brand;

        $properties_count = 0;
        $bool_query = '';
        $integer_query = '';
        $select_query = '';
        $bool_count = 0;
        $integer_count = 0;

        foreach ($get_filters as $filter_id => $filter_value) {
            if (is_array($filter_value)) {
                $properties_count++;
                if ($select_query) {
                    $select_query .= ' OR ';
                }
                $select_query .= 'pp.property_id=' . $filter_id . ' AND pp.dictionary_id IN ("' . join('","' , $filter_value) . '")';
            }

            if ($filters_hash[$filter_id]['type'] == 'I') {
                $from  = Arr::get($filter_value, 0) ? Arr::get($filter_value, 0) : 0;
                $to  = Arr::get($filter_value, 1) ? Arr::get($filter_value, 1) : 0;

                if ($from || $to) {
                    if ($integer_query) {
                        $integer_query  .= ' OR ';
                    }
                }

                if ($to && $from) {
                    $integer_query .= 'pp.property_id=' . $filter_id . ' AND pp.value >= ' . $from .' AND pp.value <= ' . $to;
                } elseif ($to) {
                    $integer_query .= 'pp.property_id=' . $filter_id . ' AND pp.value <= ' . $to;
                } elseif ($from) {
                    $integer_query .= 'pp.property_id=' . $filter_id . ' AND pp.value >= ' . $from;
                }

                if ($from || $to) {
                    $integer_count++;
                    $properties_count++;
                }
            }

            if ($filters_hash[$filter_id]['type'] == 'B' && $filter_value == 'on') {
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
        $this->template->s_title = $category->name . ' - купить ' . $category->name . ' в Минске в интернет магазине euroshoes: лучшие цены, большой каталог, отзывы';
    }

}