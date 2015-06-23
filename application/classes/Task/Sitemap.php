<?php defined('SYSPATH') or die('No direct script access.');

class Task_Sitemap extends Minion_Task
{
    protected $_options = array(
    );
	
    protected function _execute(array $params)
    {
        $categories = ORM::factory('Category')->fetchActive();
		$products = ORM::factory('Product')->fetchActive();
		$pages = ORM::factory('Page')->fetchActive();
		$sections = ORM::factory('Section')->fetchActive();

        $sitemap = new Sitemap;
        $url = new Sitemap_URL;
        $sitemap->gzip = true;
		
		$page = ORM::factory('Page')->where('url', '=', '')->find();
		
		$url->set_loc("https://1teh.by")
			->set_last_mod(strtotime($page->updated_at))
			->set_priority(1);
		$sitemap->add($url);
        
		foreach ($pages as $page) {
			if ($page->url) {
				$url->set_loc("https://1teh.by/page/" . $page->url)
					->set_last_mod(strtotime($page->updated_at))
					->set_change_frequency('monthly')
					->set_priority(0.2);
				$sitemap->add($url);
			}
		}
		
		$PDO = ORM::factory('Brand')->PDO();
		$brandsQuery = "SELECT br.id, br.url FROM brand br
						LEFT JOIN product pr ON pr.brand_id = br.id
						WHERE pr.active = 1
						GROUP BY br.id
						HAVING COUNT(pr.id) > 0";
		$brands = $PDO->query($brandsQuery)->fetchAll(PDO::FETCH_ASSOC);
		foreach ($brands as $brand) {
			$url->set_loc("https://1teh.by/brand/" . $brand['url'])
				->set_change_frequency('monthly')
				->set_priority(0.5);
			$sitemap->add($url);
		}
		
		foreach ($brands as $brand) {
			$query = "SELECT categories.url, categories.name, sections.url as section_url, categories.updated_at FROM categories
					LEFT JOIN categories_product ON (categories.id=categories_product.category_id)
					LEFT JOIN product ON product.id = categories_product.product_id
					LEFT JOIN sections ON sections.id = categories.section_id
					WHERE product.brand_id = {$brand['id']}
					AND product.active = 1
					GROUP BY categories.id
					HAVING COUNT(product.id) > 0
					ORDER BY categories.name ASC";
					
			$brandCategories = $PDO->query($query)->fetchAll(PDO::FETCH_ASSOC);
			foreach ($brandCategories as $category) {
				$url->set_loc("https://1teh.by/" . $category['section_url'] . "/" . $category['url'] . "/" . $brand['url'])
					->set_last_mod(strtotime($category['updated_at']))
					->set_change_frequency('weekly');
				$sitemap->add($url);
			}
		}
		
		foreach ($categories as $category) {
            $url->set_loc("https://1teh.by" . $category->getSiteUrl())
                ->set_last_mod(strtotime($category->updated_at))
                ->set_change_frequency('weekly');
            $sitemap->add($url);
        }
		
		foreach ($products as $product) {
            $url->set_loc("https://1teh.by".$product->getSiteUrl())
                ->set_last_mod(strtotime($product->updated_at))
                ->set_change_frequency('daily')
                ->set_priority(1);
            $sitemap->add($url);
        }

        foreach ($sections as $section) {
            $url->set_loc("https://1teh.by".$section->getSiteUrl())
                ->set_last_mod(strtotime($section->updated_at))
                ->set_change_frequency('monthly')
                ->set_priority(0.2);
            $sitemap->add($url);
        }

		$response = $sitemap->render();
        file_put_contents('sitemap.xml.gz', $response);
    }
}
