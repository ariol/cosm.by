<?php defined('SYSPATH') or die('No direct script access.');
class Controller_Site_Line extends Controller_Site
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
        $line_url = $this->param('line');
        $brand_url = $this->param('brand');
        $this->template->set_layout('layout/site/global');
        $line = ORM::factory('Line')->where('url', '=', $line_url)->where('active', '=', 1)->find();
        $brand = ORM::factory('Brand')->where('url', '=', $brand_url)->where('active', '=', 1)->find();
        $this->template->line = $line;
        $this->template->brand = $brand;
        $PDO_line = ORM::factory('Line')->PDO();
        $line_query = "SELECT line.url, line.name, line.id FROM line
                        LEFT JOIN product ON product.line_id = line.id
                        WHERE product.brand_id = {$brand->id}
                        AND product.active = 1
                        GROUP BY line.id
                        ORDER BY line.name ASC";
        $this->template->line_brand = $PDO_line->query($line_query)->fetchAll(PDO::FETCH_ASSOC);
        $countItems = ORM::factory('Product')->where('line_id', '=', $line->id)->where('active', '=', 1)->count_all();;
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
        $products =  ORM::factory('Product')->where('product.line_id', '=', $line->id)
            ->where('product.parent_product', '=', '')
            ->limit($this->_items_on_page)->offset(($page - 1) * $this->_items_on_page)->where('product.active', '=', 1)->find_all();;
        $this->template->product = $products;
        $pagination =Pagination::factory(array(	'current_page' => array('source' => 'route', 'key' => 'page'),
                                                'total_items'    => $countItems,
                                                'items_per_page' => $this->_items_on_page,
                                                'view' => 'site/pagination/pagination')	)->render();$this->template->pagination = $pagination;
        $title = 'Производитель '.$line->name;
        if ($page > 1) {
            $title .= ' (страница ' . $page . ')';
        }
         $this->template->s_title = $title;
        $this->template->pagination = $pagination;
    }
}