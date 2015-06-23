<?php defined('SYSPATH') or die('No direct script access.');

class Task_Xml extends Minion_Task
{
	protected $_options = array(
	);

	protected function _execute(array $params)
	{
		$shopName = '1teh.by';
		$companyName = 'ООО "ОдинТех"';
		$siteURL = 'https://1teh.by';
		
		$full_market = new YandexMarket($shopName, $companyName, $siteURL);
		$full_market->addCurr('BYR', 1);
	
		$sections = ORM::factory('Section')->where('active', '=', 1)->find_all();
		
		foreach ($sections as $section) {
			$products = ORM::factory('Product')->PDO()->query(
				'select pr.id, pr.name, pr.price, pr.new_price, pr.article, pr.main_image, pr.active, pr.url, pr.to_upload, pr.s_description, 
				 c.name as cat_name, s.url as sec_url, c.id as cat_id, s.id as sec_id, s.name as sec_name, br.name as brand_name from product pr 
				 left join sections s on s.id = pr.section_id left join categories c on pr.category_id = c.id 
				 left join brand br on pr.brand_id = br.id where pr.active = 1 and pr.category_id is not null and pr.section_id = ' . $section->id
			)->fetchAll(PDO::FETCH_ASSOC);
			
			$categories = array();
			foreach ($products as $product) {
				$categories[$product['cat_id']] = $product['cat_name'];
				unset($product);
			}

			$market = new YandexMarket($shopName, $companyName, $siteURL);

			$market->addCurr('BUR', 1);
			
			foreach ($categories as $id => $category) {
				if ($id) {
					$market->addCat($category, $id);
					$full_market->addCat($category, $id);
				}
			}

			foreach ($products as $product) {
				$offer = new OfferYmt($product['id'], $product['active']);
				$offer->setUrl("https://1teh.by/" . $product['sec_url'] . "/" . $product['url']);
			
				$offer->setRequired(
					$product['new_price'] ? $product['new_price'] : $product['price'], 
					'BYR', 
					$product['cat_id'],  
					$product['name'],
					$product['brand_name'],
					$product['main_image'] ? 'https://1teh.by' . $product['main_image'] : null
				);
				
				if ($product['article']) {
					$offer->setElem('vendorCode', $product['article']);
				}

				if ($product['to_upload'] && $product['brand_name']) {
					$market->addOffer($offer->save());
					$full_market->addOffer($offer->save());
				}
			}

			$xml = $market->save();

			header('Content-type:application/xml');
			$xml = iconv('utf-8', 'windows-1251', $xml);
			
			@unlink(PUBLIC_ROOT . 'xml/' . $section->url . '.xml');
			file_put_contents(PUBLIC_ROOT . 'xml/' . $section->url . '.xml', $xml);
		}
		
		$full_xml = $full_market->save();
		
		
		header('Content-type:application/xml');
		$full_xml = iconv('utf-8', 'windows-1251', $full_xml);
		
		@unlink(PUBLIC_ROOT . 'xml/full.xml');
		file_put_contents(PUBLIC_ROOT . 'xml/full.xml', $full_xml);
	}
}