<?php defined('SYSPATH') or die('No direct script access.');

class Task_UploadProperty extends Minion_Task
{
    protected $_options = array(
        'force'
    );

    protected function _execute(array $params)
    {
        if (!Kohana::$config->load('price.update') && !Arr::get($params, 'force')) {
            exit();
        }

        $PDO = ORM::factory('Product')->PDO();
        $PDO->query('TRUNCATE product_properties');
		
        $propertiesHash  = array();
        $pricePropertiesHash = array();
		
		$filtersIds = array();

        header('Content-Type: text/html; charset=utf-8');

        for ($i = 1; $i < 100; $i++) {
            $filename = DOCROOT . 'EXP_property_' . $i . '.csv';
            $handle = fopen($filename, "r+");

            if (!file_exists($filename)) {
                continue;
            }

            $line = 0;
            Kohana::$config->load('price')->set('update', 0);

            while (!feof($handle)) {
                $matches = fgetcsv($handle, 0, ';');

                if (!isset($matches[2])) {
                    continue;
                }

                if (!$line) {
                    foreach ($matches as $index => $match) {
                        if ($match) {
                            $propertyName = trim(iconv('windows-1251', 'utf-8', $match));
                            $pricePropertiesHash[$index] = $propertyName;
                            $PDO->query(
                                "INSERT IGNORE INTO properties (name, type) VALUES ('{$propertyName}', 'B')"
                            );
                        }
                    }
                    $line++;
                    $properties = ORM::factory('Property')->find_all();
                    foreach ($properties as $property) {
                        $index = $this->generateIndex($property['name']);
                        $propertiesHash[$index] = $property;
                    }
                    continue;
                }

                $article = trim($matches[0]);
                $product = ORM::factory('Product')->where('article', '=', $article)->find();

                if (!$product->id) {
                    continue;
                }

                foreach ($matches as $index => $match) {
                    $index = $this->generateIndex(Arr::get($pricePropertiesHash, $index));
                    if (isset($propertiesHash[$index]) && $product->id && $match) {
                        $property = $propertiesHash[$index];
                        $PDO->query("INSERT INTO product_properties (product_id, property_id, value)
                                        VALUES ('{$product->id}', '{$property['id']}', $match)");
                    }
                }
				
				
				$filter = $PDO->query("
					SELECT id FROM filters WHERE category_id = '{$product->category_id}' AND property_id = '{$property['id']}'
				")->fetch(PDO::FETCH_COLUMN);
				
				if ($filter) {
					$filtersIds[] = $filter;
				} else {
					$PDO->query("INSERT IGNORE INTO filters (category_id, property_id, type, active)
                                        VALUES ('{$product->category_id}', '{$property['id']}', 2, 1)");
					$filter = $PDO->query("
						SELECT id FROM filters WHERE category_id = '{$product->category_id}' AND property_id = '{$property['id']}'
					")->fetch(PDO::FETCH_COLUMN);
					
					if ($filter) {
						$filtersIds[] = $filter;
					}
				}
            }
            fclose($handle);
        }
		
		$filtersIds = array_unique($filtersIds);
		$filtersStr = "'" . join("', '", $filtersIds) . "'";
		
		$PDO->query("UPDATE filters SET active = 0");
		$PDO->query("UPDATE filters SET active = 1 WHERE id IN ({$filtersStr})");
    }

    private function generateIndex($name)
    {
        return md5(trim(mb_strtolower($name)));
    }

}
