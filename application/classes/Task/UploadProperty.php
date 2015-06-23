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
        $PDO->query('TRUNCATE filters');

        $propertiesHash  = array();
        $pricePropertiesHash = array();
        $properties = ORM::factory('Property')->find_all();
        foreach ($properties as $property) {
            $index = $this->generateIndex($property['name']);
            $propertiesHash[$index] = $property;
        }

        header('Content-Type: text/html; charset=utf-8');

        $handle = fopen(DOCROOT . 'EXP_property.csv', "r+");
        $line = 0;
        Kohana::$config->load('price')->set('update', 0);

        while (!feof($handle)) {
            $matches = fgetcsv($handle, 0, ';');

            if (!$line) {
                foreach ($matches as $index => $match) {
                    if ($index < 7) {
                        continue;
                    }
                    $pricePropertiesHash[$index] = $match;
                }
                $line++;
                continue;
            }

            if (!isset($matches[2])) {
                continue;
            }

            $product = ORM::factory('Product', $matches[0]);

            foreach ($matches as $index => $match) {
                $index = $this->generateIndex(Arr::get($pricePropertiesHash, $index));
                if (isset($propertiesHash[$index]) && $product->id && $match) {
                    $property = $propertiesHash[$index];
                    $PDO->query("INSERT INTO product_properties (product_id, property_id, value)
                                    VALUES ('{$product->id}', '{$property['id']}', $match)");
                }
            }

            $PDO->query("INSERT IGNORE INTO filters (category_id, property_id, type, active)
                                    VALUES ('{$product->category_id}', '{$property['id']}', 2, 1)");
        }
        fclose($handle);
    }

    private function generateIndex($name)
    {
        return md5(trim(mb_strtolower($name)));
    }

}
