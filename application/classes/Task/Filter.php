<?php defined('SYSPATH') or die('No direct script access.');

class Task_Filter extends Minion_Task
{
    protected $_options = array(
    );
	
    protected function _execute(array $params)
    {
        $PDO = ORM::factory('Filter')->PDO();

        header('Content-Type: text/html; charset=utf-8');

        set_time_limit (200);
        ini_set('auto_detect_line_endings', TRUE);

		for ($i = 1; $i <= 40; $i++) {
			$handle = fopen(DOCROOT . 'var/filter/exp_part' . $i . '.csv', "r+");
		
			$line = 0;
			$last_name = '';
			$category_id = 0;
			$category_ids = array();

			$insert_query_start = "INSERT IGNORE INTO product_properties (product_id, property_id, value, dictionary_id) VALUES ";
			$insert_query_parts = array();

			$insert_dictionaries_start = "INSERT IGNORE INTO dictionary_values (property_id, category_id, value) VALUES ";
			$insert_dictionaries_parts = array();

			while (!feof($handle)) {
				$cols = fgetcsv($handle, 0, ';');

				if (!$line) {
					$function_name = 'properties_skeleton_' . $i;
					$properties = $this->$function_name($cols);
					$properties_pack =  $PDO->query('SELECT *  FROM properties')->fetchAll(PDO::FETCH_ASSOC);
					$properties_hash = array();
					foreach ($properties_pack as $row) {
						$properties_hash[$row['name']] = $row['id'];
					}
					unset($row);
					unset($properties_pack);
					$line++;
					continue;
				}

				$line++;

				if (!isset($cols[3])) {
					continue;
				}

				if (($line % 500) == 0) {
					$PDO->exec($insert_query_start . join(',', $insert_query_parts));
					$PDO->exec($insert_dictionaries_start . join(',', $insert_dictionaries_parts));
					$insert_query_parts = array();
					$insert_dictionaries_parts = array();
				}

				$product_id =  $PDO->query('SELECT id FROM product WHERE unique_code = ' . $cols[13])->fetchColumn();

				$category_parts = explode('/', $cols[0]);
				$category_name = iconv('windows-1251', 'utf-8', trim(end($category_parts)));

				if ($category_name != $last_name) {
					$last_name = $category_name;
					$category_id = ORM::factory('Category')->where('name', '=', $category_name)->find()->id;
					$category_ids[] = $category_id;
				}
				
				foreach ($cols as $index => $col) {
					$property = $properties[$index];
					if (!$property) {
						continue;
					}
					$property_id = $properties_hash[iconv('windows-1251', 'utf-8', $property['name'])];
					if ($property['type'] == 'I') {
						preg_match('/\d+(?:[\., ]\d+)?/i', $col, $match);
						$value = str_replace(' ', '', reset($match));
						if ($value != '' && $value != null) {
							$insert_query_parts[] = "('{$product_id}', '{$property_id}', '{$value}', null)";
						}
					} elseif ($property['type'] == 'B') {
						$value = preg_match('%да%isuU', iconv('windows-1251', 'utf-8', $col));
						if ($value != '' && $value != null) {
							$insert_query_parts[] = "('{$product_id}', '{$property_id}', '{$value}', null)";
						}
					} elseif ($property['type'] == 'D') {
						$value = iconv('windows-1251', 'utf-8', $col);
						if ($value != '' && $value != null) {
							$insert_query_parts[] = "('{$product_id}', '{$property_id}', '{$value}', 0)";
							$insert_dictionaries_parts[] = "('{$property_id}', '{$category_id}', '{$value}')";
						}
					}
				}
			}
			fclose($handle);

			$PDO->exec($insert_query_start . join(',', $insert_query_parts));
			$PDO->exec($insert_dictionaries_start . join(',', $insert_dictionaries_parts));

			foreach (ORM::factory('Category')->where('active', '=', 1)->where('id', 'IN', $category_ids)->find_all() as $category) {
				$products_ids = array_map(
					function($category_product) {return $category_product['product_id'];},
					$PDO->query("select product_id from categories_product where category_id = {$category->id}")->fetchAll(PDO::FETCH_ASSOC)
				);

				$product_properties = $PDO->query('select pp.property_id, pr.type from properties pr left join product_properties pp on pr.id = pp.property_id where pp.product_id in (\'' . join('\',\'', $products_ids) . '\') group by pp.property_id')->fetchAll(PDO::FETCH_ASSOC);

				$insertValues = array();

				$query = "insert ignore into `filters` (`id`, `category_id`, `property_id`, `type`) values ";

				foreach ($product_properties as $property) {
					$type = Model_Filter::TYPE_CHECKBOX;
					if ($property['type'] == 'I') {
						$type = Model_Filter::TYPE_RANGE;
					} elseif ($property['type'] == 'B') {
						$type = Model_Filter::TYPE_CHECKBOX;
					} elseif ($property['type'] == 'D') {
						$type = Model_Filter::TYPE_SELECT;
					}
					$insertValues[] = "(null, '{$category->id}', '{$property['property_id']}', '{$type}')";
				}

				if ($insertValues) {
					$query .= join(',', $insertValues) . ';';
					$PDO->exec($query);
				}

				$empty_properties = $PDO->query("select * from product_properties pp left join categories_product cp on cp.product_id = pp.product_id where cp.category_id = {$category->id} and dictionary_id = 0")->fetchAll(PDO::FETCH_ASSOC);
				$dictionary_values = $PDO->query("select * from dictionary_values where category_id={$category->id}")->fetchAll(PDO::FETCH_ASSOC);
				$dictionary_values_hash = array();
				foreach ($dictionary_values as $dv) {
					$dictionary_values_hash[$dv['value'] . '_' . $dv['property_id'] . '_' . $dv['category_id']] = $dv['id'];
					unset($dv);
				}
				unset($dictionary_values);
				foreach ($empty_properties as $ep) {
					$dictionary_id = $dictionary_values_hash[$ep['value'] . '_' . $ep['property_id'] . '_' . $ep['category_id']];
					if ($dictionary_id) {
						$PDO->exec("UPDATE product_properties SET dictionary_id={$dictionary_id} WHERE id = {$ep['id']}");
					}
					unset($ep);
				}
				unset($empty_properties);
			}
		}
		
        ini_set('auto_detect_line_endings',FALSE);
	}

    private function properties_skeleton_8($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
                case "Атрибут: Исполнение": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'D'
                    );
                    break;
                }
                case "Атрибут: Конструкция": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'D'
                    );
                    break;
                }
                case "Атрибут: Расположение морозильной камеры": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'D'
                    );
                    break;
                }
                case "Атрибут: No Frost": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'D'
                    );
                    break;
                }
                case "Атрибут: Цвет": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'D'
                    );
                    break;
                }
                case "Атрибут: Специальная отделка": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'D'
                    );
                    break;
                }
                case "Атрибут: Ретро-дизайн": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Количество компрессоров": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'D'
                    );
                    break;
                }
                case "Атрибут: Общий объём": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'I'
                    );
                    break;
                }
                case "Атрибут: Полезный объём": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'I'
                    );
                    break;
                }
                case "Атрибут: Общий объём холодильной камеры": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'I'
                    );
                    break;
                }
                case "Атрибут: Полезный объём холодильной камеры": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'I'
                    );
                    break;
                }
                case "Атрибут: Общий объём морозильной камеры": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'I'
                    );
                    break;
                }
                case "Атрибут: Полезный объём морозильной камеры": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'I'
                    );
                    break;
                }
                case "Атрибут: Настраиваемая зона (мультизона)": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Перенавешиваемые двери": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Двухстороннее открывание дверей": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Ручки": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'D'
                    );
                    break;
                }
                case "Атрибут: Электропривод дверей": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Автодоводчик дверей": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Мини-бар": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Диспенсер": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Подсветка корпуса": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Инверторная технология": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Годовой расход электроэнергии": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'I'
                    );
                    break;
                }
                case "Атрибут: Уровень шума": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'I'
                    );
                    break;
                }
                case "Атрибут: Управление": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'D'
                    );
                    break;
                }
                case "Атрибут: Дисплей": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'D'
                    );
                    break;
                }
                case "Атрибут: Режим «Отпуск»": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Расположение блока управления": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'D'
                    );
                    break;
                }
                case "Атрибут: Независимая регулировка температуры": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'D'
                    );
                    break;
                }
                case "Атрибут: Индикация температуры": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Сигнал открытой двери": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'D'
                    );
                    break;
                }
                case "Атрибут: Блокировка от детей": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'D'
                    );
                    break;
                }
                case "Атрибут: Зона свежести": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Количество полок": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'I'
                    );
                    break;
                }
                case "Атрибут: Материал полок": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'D'
                    );
                    break;
                }
                case "Атрибут: Выдвижная полка": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Складная полка": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Полка для вина": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Ящик для овощей и фруктов": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Количество дверных балконов": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'I'
                    );
                    break;
                }
                case "Атрибут: Материал дверных балконов": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'D'
                    );
                    break;
                }
                case "Атрибут: Лоток для яиц": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Антибактериальное покрытие": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Ионизация": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Количество отделений морозильной камеры": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'I'
                    );
                    break;
                }
                case "Атрибут: Генератор льда": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Освещение морозильной камеры": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Высота": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'I'
                    );
                    break;
                }
                case "Атрибут: Ширина": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'I'
                    );
                    break;
                }
                case "Атрибут: Глубина": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'I'
                    );
                    break;
                }
                case "Атрибут: Мощность замораживания": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'I'
                    );
                    break;
                }
                case "Атрибут: Время размораживания": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'I'
                    );
                    break;
                }
                case "Атрибут: Регулировка влажности": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Вес": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'I'
                    );
                    break;
                }
                case "Атрибут: Тип": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'D'
                    );
                    break;
                }
                case "Атрибут: Максимальная загрузка": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'I'
                    );
                    break;
                }
                case "Атрибут: Макс. скорость отжима": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'D'
                    );
                    break;
                }
                case "Атрибут: Прямой привод": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Защита от протечек": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Автоматическая экономия электроэнергии и воды": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'D'
                    );
                    break;
                }
                case "Атрибут: Обработка паром": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Обработка ионами серебра": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Диапазон температур": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'D'
                    );
                    break;
                }
                case "Атрибут: Уровень шума при стирке": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'I'
                    );
                    break;
                }
                case "Атрибут: Уровень шума при отжиме": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'I'
                    );
                    break;
                }
                case "Атрибут: Возможность дозагрузки во время стирки": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Количество программ": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'I'
                    );
                    break;
                }
                case "Атрибут: Предварительная стирка": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Дополнительное полоскание": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Класс энергопотребления": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'D'
                    );
                    break;
                }
                case "Атрибут: Класс стирки": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'D'
                    );
                    break;
                }
                case "Атрибут: Класс отжима": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'D'
                    );
                    break;
                }
                case "Атрибут: Индикация": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'D'
                    );
                    break;
                }
                case "Атрибут: Звуковой сигнал": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Индикация ошибок": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Таймер отложенного старта": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Быстрая стирка": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Лёгкая глажка (без складок)": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Биоэнзимная фаза": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Метод сушки": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'D'
                    );
                    break;
                }
                case "Атрибут: Класс сушки": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'D'
                    );
                    break;
                }
                case "Атрибут: Запись передач (PVR)": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Пауза воспроизведения (Timeshift)": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Проигрывание файлов": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Диагональ экрана": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'I'
                    );
                    break;
                }
                case "Атрибут: Разрешение": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'D'
                    );
                    break;
                }
                case "Атрибут: Соотношение сторон экрана": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'D'
                    );
                    break;
                }
                case "Атрибут: Эквивалентная частота обновления экрана": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'I'
                    );
                    break;
                }
                case "Атрибут: Поддержка 3D": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Проигрыватель дисков": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Онлайн-сервисы (Smart TV)": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Видеозвонки": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Цвет корпуса": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'D'
                    );
                    break;
                }
                case "Атрибут: Технология экрана": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'D'
                    );
                    break;
                }
                case "Атрибут: Подсветка матрицы": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'D'
                    );
                    break;
                }
                case "Атрибут: Датчик освещенности": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Датчик присутствия": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: TV-тюнер": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'D'
                    );
                    break;
                }
                case "Атрибут: HbbTV": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Сабвуфер": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Картинка в картинке": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'D'
                    );
                    break;
                }
                case "Атрибут: Игровой режим": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Управление голосом/жестами": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Wi-Fi": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: WiDi": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: MHL": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: HDMI": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'I'
                    );
                    break;
                }
                case "Атрибут: Цифровой вход S/PDIF": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Цифровой выход S/PDIF": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Выход на наушники": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: USB": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'I'
                    );
                    break;
                }
                case "Атрибут: Common Interface (CI)": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Ethernet": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Соответствие DLNA": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Управление со смартфона": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Глубина (с учетом подставки)": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Толщина панели": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'I'
                    );
                    break;
                }
                case "Атрибут: Локальное затемнение": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Частота обновления экрана": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'I'
                    );
                    break;
                }
                case "Атрибут: Wi-Fi Ready": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'B'
                    );
                    break;
                }
                case "Атрибут: Изогнутый экран": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'D'
                    );
                    break;
                }
                case "Атрибут: Функция 3D": {
                    $properties[$index] = array(
                        'name' => iconv('utf-8', 'windows-1251', $column),
                        'type' => 'D'
                    );
                    break;
                }
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_9($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
				case "Атрибут: Тип": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'D'
					);
					break;
				}
				case "Атрибут: Цвет": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'D'
					);
					break;
				}
				case "Атрибут: Потребляемая мощность": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'I'
					);
					break;
				}
				case "Атрибут: Объём чаши": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'I'
					);
					break;
				}
				case "Атрибут: Управление": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'D'
					);
					break;
				}
				case "Атрибут: Таймер": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Дисплей": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'D'
					);
					break;
				}
				case "Атрибут: Отсрочка старта": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Режим разогрева": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Пользовательские программы": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'I'
					);
					break;
				}
				case "Атрибут: Высота": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'I'
					);
					break;
				}
				case "Атрибут: Ширина": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'I'
					);
					break;
				}
				case "Атрибут: Глубина": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'I'
					);
					break;
				}
				case "Атрибут: Функция двух таймеров": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Голосовой гид": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Индикатор уровня воды": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Таймер автоотключения": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Функция самоочистки": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Материал корпуса": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'D'
					);
					break;
				}
				case "Атрибут: Регулировка давления": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Вес": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'I'
					);
					break;
				}
				case "Атрибут: Мощность": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'I'
					);
					break;
				}
				case "Атрибут: Количество скоростей": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'I'
					);
					break;
				}
				case "Атрибут: Объем чаши": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'I'
					);
					break;
				}
				case "Атрибут: Материал корпуса": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'D'
					);
					break;
				}
				case "Атрибут: Импульсный режим": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Режим «Турбо»": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Автоматическая блокировка": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Нож для колки льда": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Материал чаши": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'D'
					);
					break;
				}
				case "Атрибут: Материал ножки": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'D'
					);
					break;
				}
				case "Атрибут: Дополнительные приспособления": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Регулировка температуры": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Индикатор питания": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Индикатор готовности к работе": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Антипригарное покрытие": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Тип варочной поверхности": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'D'
					);
					break;
				}
				case "Атрибут: Тип духового шкафа": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'D'
					);
					break;
				}
				case "Атрибут: Цвет": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'D'
					);
					break;
				}
				case "Атрибут: Класс энергопотребления": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'D'
					);
					break;
				}
				case "Атрибут: Управление": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'D'
					);
					break;
				}
				case "Атрибут: Таймер": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Звуковой сигнал": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Количество конфорок": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'I'
					);
					break;
				}
				case "Атрибут: Общая мощность": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'I'
					);
					break;
				}
				case "Атрибут: Гриль": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Ящик для посуды и принадлежностей": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Дверца духовки": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'D'
					);
					break;
				}
				case "Атрибут: Количество стекол дверцы духовки": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'D'
					);
					break;
				}
				case "Атрибут: Вертел": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Дополнительные принадлежности": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Глубина": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'I'
					);
					break;
				}
				case "Атрибут: Термостат": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Мощность гриля": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'I'
					);
					break;
				}							
			}
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_10($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
				case "Атрибут: Мощность": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'I'
					);
					break;
				}
				case "Атрибут: Количество емкостей": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'I'
					);
					break;
				}
				case "Атрибут: Таймер": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Нагревательный элемент": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'D'
					);
					break;
				}
				case "Атрибут: Управление": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'D'
					);
					break;
				}
				case "Атрибут: Рабочий объем колбы": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'I'
					);
					break;
				}
				case "Атрибут: Исполнение": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'D'
					);
					break;
				}
				case "Атрибут: Тип микроволновой печи": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'D'
					);
					break;
				}
				case "Атрибут: Цвет": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'D'
					);
					break;
				}
				case "Атрибут: Объем": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'I'
					);
					break;
				}
				case "Атрибут: Вертел": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Управление": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'D'
					);
					break;
				}
				case "Атрибут: Индикация": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'D'
					);
					break;
				}
				case "Атрибут: Блокировка от детей": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Часы": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: 3D-распространение микроволн": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Очистка паром": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Автоматические режимы приготовления": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Автоматические программы размораживания": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Приготовление пиццы": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Внутреннее покрытие стенок": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'D'
					);
					break;
				}
				case "Атрибут: Книга рецептов": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Высота": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'I'
					);
					break;
				}
				case "Атрибут: Ширина": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'I'
					);
					break;
				}
				case "Атрибут: Глубина": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'I'
					);
					break;
				}
				case "Атрибут: Вес": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'I'
					);
					break;
				}
				case "Атрибут: Комбинированный режим": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'I'
					);
					break;
				}
				case "Атрибут: Пароварка": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Тип гриля": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'D'
					);
					break;
				}
				case "Атрибут: Мощность конвекции": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'I'
					);
					break;
				}
				case "Атрибут: Термопот": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Подставка": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'D'
					);
					break;
				}
				case "Атрибут: Материал корпуса": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'D'
					);
					break;
				}
				case "Атрибут: Фильтр": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Наружная шкала уровня воды": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Защита от включения без воды (перегрева)": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Блокировка крышки": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Подсветка воды": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Подогрев": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Поддержание температуры": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Терморегулятор": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Самоочистка": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Таймер": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'B'
					);
					break;
				}
				case "Атрибут: Покрытие нагревательного элемента": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'D'
					);
					break;
				}
				case "Атрибут: Способы розлива воды": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'D'
					);
					break;
				}
				case "Атрибут: Длина сетевого шнура": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'I'
					);
					break;
				}
				case "Атрибут: Материал колбы": {
					$properties[$index] = array(
						'name' => iconv('utf-8', 'windows-1251', $column),
						'type' => 'D'
					);
					break;
				}						
			}
		}

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_1($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
            case "Атрибут: Тип": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Конструкция": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
case "Атрибут: Материал чаши/платформы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
case "Атрибут: Максимальная нагрузка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
case "Атрибут: Цена деления": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
case "Атрибут: Дисплей": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
case "Атрибут: Единицы измерения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
case "Атрибут: Функция довешивания": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
case "Атрибут: Дополнительные показания": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
case "Атрибут: Память": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
case "Атрибут: Таймер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
case "Атрибут: Вертикальное хранение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
case "Атрибут: Объем чаши": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
case "Атрибут: Автовыключение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
case "Атрибут: Ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
case "Атрибут: Длина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
case "Атрибут: Высота": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
case "Атрибут: Высота цифр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
case "Атрибут: Мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
case "Атрибут: Вместимость": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
case "Атрибут: Антипригарное покрытие": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
case "Атрибут: Индикатор нагрева": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
case "Атрибут: Индикатор готовности": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
case "Атрибут: Вафельница": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
case "Атрибут: Гриль": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
case "Атрибут: Место для хранения шнура": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
case "Атрибут: Разделитель": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
case "Атрибут: Съемные плитки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
case "Атрибут: Хранение в вертикальном положении": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
case "Атрибут: Объем чаши": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
case "Атрибут: Вес картофеля": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
case "Атрибут: Материал корпуса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
case "Атрибут: Съемная чаша": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Смотровое окно": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Материал чаши": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Импульсный режим": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Ножи для измельчения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Нагревательный элемент": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Управление": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Циркуляция воздуха": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Автоотключение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Вместимость": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Звуковой сигнал": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Мерный стакан": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Длина рабочей поверхности": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Ширина рабочей поверхности": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Вес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Размещение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Материал рабочей поверхности": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Рабочая поверхность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Съемная рабочая поверхность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Съемный поддон для жира": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Противни для раклета": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Регулировка температуры": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_2($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
            case "Атрибут: Мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Объем чаши": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Материал чаши": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Книга рецептов в комплекте": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Блендер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Мясорубка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Мороженица": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Мельница для круп": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Картофелечистка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Соковыжималка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Импульсный режим": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Планетарное смешивание": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Взвешивание": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Таймер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Место для хранения шнура": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Ножи для измельчения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Эмульгатор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Крюк для теста": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Насадки для смешивания": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Тёрки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Диски для шинковки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Ломтерезка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Насадка для картофеля фри": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Насадка для пюре": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Диск-жюльен": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Диапазон скоростей вращения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Конструкция": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Регулировка скорости": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Материал корпуса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Блокировка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Диаметр отверстия для загрузки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Отброс мякоти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Импульсный режим": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Емкость для сока": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Прямая подача сока": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Реверсивное вращение конуса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Регулировка количества мякоти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Прессовка мякоти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Контейнер для отходов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Емкость для сока с пеносепаратором": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Место для хранения шнура": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Количество отделений": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Управление": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Подогрев": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Количество степеней обжаривания": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Автоматическое отключение при застревании тостов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Съемный поддон": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Решетка для подогрева булочек": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Экстра-подъем маленьких ломтиков": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Количество тостов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Индикация": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Высота": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Глубина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Вес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Максимальная мощность при блокировке вала": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Формовочные диски": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Насадка Кеббе": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Насадка для домашней колбасы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Соковыжималка универсальная": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Тёрки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Дополнительные насадки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Производительность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_3($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
            case "Атрибут: Мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Количество емкостей для готовки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Объем": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Индикатор уровня воды": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Управление": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Таймер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Автоотключение при отсутствии воды": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Режим разогрева": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Дополнительные принадлежности": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Мультиварка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Емкость для приготовления риса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Долив воды во время работы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Отсрочка старта": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: «Быстрый пар»": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Автоматические программы приготовления": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Вес выпечки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Форма выпечки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Регулировка веса выпечки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Выбор цвета корочки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Отсрочка запуска": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Количество программ": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Замес теста": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Основные программы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Диспенсер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Материал корпуса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Поддержание температуры": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Ускоренная выпечка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Защита от перегрева": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Русский повар": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Защита от детей": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Тип": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Число скоростей": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Турборежим": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Импульсный режим": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Материал корпуса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Универсальный измельчитель": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Крюк для теста": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Насадка-венчик": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Вращение чаши": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Материал чаши": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Кнопка отсоединения насадок": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Насадка для приготовления пюре": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Насадка-блендер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Приспособление для хранения насадок": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Количество насадок": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Защитная крышка на чашу": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Объем": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Микроволны": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Конфорки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Конвекция": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Потребляемая мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Управление": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Индикация": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Утапливаемые ручки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Звуковой сигнал": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Термостат": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Количество режимов работы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Автоматические программы приготовления": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Нижний нагрев": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Верхний нагрев": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Нижний + верхний нагрев": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Размораживание": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Поддержание температуры (подогрев)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Гриль + конвекция": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Нижний нагрев + конвекция": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Вертел": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Решетка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Форма для пиццы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Поддон для сбора крошек": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Высота": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Глубина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Мощность гриля": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Поверхность внутренних стенок": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Освещение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Нижний нагрев + гриль + конвекция": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_4($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
            case "Атрибут: Время нагрева воды": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Назначение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Применение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Конструктивное исполнение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Мощность нагревателя": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Макс. выход пара": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Ёмкость котла": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Съемный второй резервуар": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Регулятор расхода пара": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Длина шнура питания": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Длина шланга": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Блокировка от детей": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Розетка для утюга": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Телескопическая штанга": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Паровая насадка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Паровой пистолет": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Утюг": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Круглая щетка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Высоконапорная насадка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Ручная насадка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Насадка для мойки окон": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Стяжка для мойки окон": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Насадка для мытья полов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Насадка для ткани": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Паровая турбо-щётка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Удлинительные трубки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Разъемная штанга": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Регулятор влажности пара": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Скорость нагрева воды": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Предохранительный клапан": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Вес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Макс. давление пара": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Макс. количество воды": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Питание": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Регулировка высоты среза": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Насадка для шерсти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Щётка для чистки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Тип": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Объем обогрева": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Управление": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Регулировка уровня мощности": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Турбонаддув": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Встроенный ионизатор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Термостат": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Таймер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Пульт ДУ": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Светодиодный индикатор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Настенный монтаж": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Обдув без нагрева": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Высота": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Глубина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Количество секций маслянного радиатора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Регулировка температуры": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Вращение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Пылевой фильтр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Увлажнитель воздуха": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Дисплей": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Влагозащитный корпус": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Автоотключение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Защита от детей": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Держатель для полотенца": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Нагревательный элемент": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Площадь обогрева": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Каминный эффект": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Тип инфракрасного обогревателя": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Челнок": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Выполнение петли": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Видов строчек": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Регулировка ширины строчки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Максимальная ширина строчки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Нитевдеватель": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Реверс": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Регулировка давления лапки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Рукавная платформа": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Чехол": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Лапки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Иглы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Вышивальный блок": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Вид петли": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Встроенные рисунки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Цвет": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Количество температурных зон": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Перенавешиваемые двери": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Годовой расход электроэнергии": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Управление": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Расположение блока управления": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Индикация температуры": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Регулировка температуры": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Сигнал открытой двери": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Регулировка влажности": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Сигнал о неисправностях": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Количество полок": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Материал полок": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Дверь": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Количество бутылок": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_5($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
            case "Атрибут: Максимальная загрузка (комплекты посуды)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Сушка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Исполнение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Цвет": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Теплообменник": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Защита от протечек": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Количество программ": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Нормальная программа": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Экономичная программа": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Деликатная мойка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Быстрая мойка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: БИО-программа": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Интенсивная мойка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Замачивание": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Класс энергопотребления": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Класс эффективности мытья": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Класс сушки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Управление": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Таймер отложенного старта": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Блокировка от детей": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Половинная загрузка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Самоочищающийся фильтр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Высота": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Глубина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Звуковой сигнал": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Датчик загрузки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Добавить соль": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Добавить ополаскиватель": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Регулировка жесткости воды": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Интенсивная мойка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Компактная": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Защита от случайного включения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Складной корпус": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Отсек для шнура": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Ножки-присоски": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Макс. толщина нарезки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Режим работы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Электронная стабилизация вращения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Волнообразная заточка ножа": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Наклон рабочего стола": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Приёмный лоток (поднос)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Тип": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Количество конфорок/зон нагрева": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Общая мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Гриль": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Управление": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Регулировка мощности": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Таймер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Тип используемого кофе": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Количество порций": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Емкость для молока": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Регулировка крепости напитка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Регулировка температуры": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Капучинатор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Подогрев чашек": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Автоматическая чистка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Противокапельная система": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Подача горячей воды": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Давление": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Регулировка объема порции": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Предварительное смачивание кофе": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Приготовление нескольких чашек одновременно": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Регулировка высоты чашек": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Индикация": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Тип фильтра": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Фильтр в комплекте": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Латте": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Отсек для кофе": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Отсек для отходов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Вес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Вместимость контейнера для зерен": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Материал корпуса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Регулировка степени помола": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Порционный помол": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Импульсный режим": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Блокировка при открытой крышке": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Измельчение зёрен": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Углубление для шнура": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Пуск": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Емкость для молотого кофе": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_6($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
            case "Атрибут: Конструкция": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Пылесборник": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
case "Атрибут: Уборка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
case "Атрибут: Потребляемая мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
case "Атрибут: Мощность всасывания": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
case "Атрибут: Труба": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
case "Атрибут: Материал трубы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
case "Атрибут: Длина сетевого шнура": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
case "Атрибут: Автоматическое сматывание шнура": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
case "Атрибут: Регулировка мощности": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
case "Атрибут: Фильтр тонкой очистки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
case "Атрибут: Всасывание жидкостей": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
case "Атрибут: Ароматизация": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
case "Атрибут: Ионизация": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
case "Атрибут: Турбощётка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
case "Атрибут: Электрощетка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
case "Атрибут: Питание": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
case "Атрибут: Уровень шума": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
case "Атрибут: Индикатор заполнения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
case "Атрибут: Объём пылесборника": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
case "Атрибут: Вес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
case "Атрибут: Цвет корпуса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
case "Атрибут: Радиус действия": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
case "Атрибут: Резиновые колеса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
case "Атрибут: Выдув": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
case "Атрибут: Режим автоматической регулировки мощности": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
case "Атрибут: Двухпозиционная щётка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
case "Атрибут: Щелевая насадка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
case "Атрибут: Щётка для мебели": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
case "Атрибут: Насадка для твердых покрытий": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
case "Атрибут: Щетка для шерсти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Малая щётка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Насадка для влажной уборки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Насадка для сбора воды": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Паровая насадка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Высота": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Глубина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Подключение к электроинструменту": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Следование за пользователем": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Розетка для электроинструмента": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Прессование пыли": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Беспроводной утюг": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Тип утюга": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Подошва": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Максимальная мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Шарнирное крепление сетевого шнура": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Автоматическое сматывание шнура": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Спрей": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Объём резервуара для воды": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Постоянная подача пара": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Регулировка подачи пара": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Паровой удар": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Вертикальное отпаривание": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Система защиты от накипи": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Функция самоочистки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Противокапельная система": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Автоматическое отключение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Длина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Время уборки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Датчики перепада высоты": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Автоматический возврат на базу": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Режим локальной уборки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Режим быстрой уборки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Пульт ДУ": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: База с мусоросборником": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Влажная уборка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Влажная салфетка (мини-швабра)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Ультрафиолетовая лампа": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Датчики загрязнения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Виртуальная стена в комплекте": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Навигационная камера": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Датчики препятствий": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Тип аккумулятора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Время работы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Емкость аккумулятора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Время зарядки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Тип": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Размещение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Питание": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Регулировка обдува": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Дополнительные режимы обдува": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Таймер отключения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Дополнительные функции": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Управление": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Дисплей": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Материал корпуса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Материал лопастей": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Вращение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Направление вращения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Регулировка высоты": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Регулировка наклона": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Влагозащищенный корпус": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Диаметр лопастей": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_7($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
            case "Атрибут: Мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Управление": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Объем": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
case "Атрибут: Помпа": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
case "Атрибут: Таймер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
case "Атрибут: Максимальная загрузка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
case "Атрибут: Производительность уборки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
case "Атрибут: Рабочая ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
case "Атрибут: Емкость бака": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
case "Атрибут: Длина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
case "Атрибут: Вес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
case "Атрибут: Ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
case "Атрибут: Высота": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
case "Атрибут: Тип варочной поверхности": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
case "Атрибут: Цвет": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
case "Атрибут: Управление": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
case "Атрибут: Звуковой сигнал": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
case "Атрибут: Тип установки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
case "Атрибут: Количество конфорок": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
case "Атрибут: Общая мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
case "Атрибут: Глубина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
case "Атрибут: Газовые конфорки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
case "Атрибут: Чугунные конфорки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
case "Атрибут: Спиральные конфорки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
case "Атрибут: Ленточные (HiLight) конфорки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
case "Атрибут: Галогенные конфорки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
case "Атрибут: Индукционные конфорки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
case "Атрибут: Электроподжиг": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
case "Атрибут: Газ-контроль": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
case "Атрибут: Решетки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
case "Атрибут: Класс энергопотребления": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Конструкция": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Монтаж": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Цвет": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Количество моторов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Мощность мотора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Жироулавливающий фильтр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Индикация": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Количество скоростей": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Таймер автоотключения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Тип освещения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Количество ламп": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Режим работы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Стиль": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Конструкция": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Угольный фильтр (в комплекте)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Периметральное воздухопоглощение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Индикатор загрязнения фильтров": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Постоянная вентиляция": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Датчик температуры": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Ионизация": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Интенсивный режим": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Уровень шума в интенсивном режиме": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Минимальный уровень шума": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Максимальная производительность отвода": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Производительность отвода в интенсивном режиме": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Минимальная производительность отвода": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Максимальная производительность циркуляции": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Объём": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Максимальная температура": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Очистка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Конвекция": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Дверца духовки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Ящик для посуды и принадлежностей": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Количество стекол дверцы духовки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Мощность гриля": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Количество режимов работы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Гриль": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Вертел": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Дополнительные принадлежности": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Термостат": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Вентилятор охлаждения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Газ-контроль": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Мощность гриля": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Материал рабочей поверхности": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_11($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
				case "Атрибут: Тип": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Объем": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Кардридер в комплекте": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: SD-класс": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Скорость чтения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Скорость записи": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;				
			}	
			case "Атрибут: Форматирование": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Вид аксессуара": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Высота": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Глубина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Тип 3D-очков": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Время автономной работы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Вес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Стандарты беспроводной связи": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Макс. скорость беспроводной связи": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Интерфейс подключения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Цвет": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Протоколы безопасности беспроводной сети": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Поддержка 802.1X": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Приоритезация беспроводного трафика (WMM)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
				
			}	
			case "Атрибут: Количество антенн": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: MIMO": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Съёмная антенна": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Диапазон частот": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Эквивалентная излучаемая мощность (EIRP)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Кнопка Wi-Fi Protected Setup (WPS)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Эквивалентная излучаемая мощность (EIRP)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Наличие экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Стерео": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Multipoint": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: DSP": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Аудиовыход 3.5 мм": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: NFC": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Cъемный аккумулятор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Время разговора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Время ожидания": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Длина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Толщина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Вес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: FM-приёмник": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: A2DP": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Емкость аккумулятора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Вибровызов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_12($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
				case "Атрибут: Исходная версия операционной системы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Тактовая частота процессора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: 3G": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Разъём для внешней антенны": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Пыле- и влагозащита": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Длина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;				
			}	
			case "Атрибут: Толщина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Вес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Акселерометр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Электромагнитный компас": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Разрешение экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Размер экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Сенсорный экран": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Встроенная камера": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Дополнительная камера": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: ТВ-тюнер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Запись звука": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: FM-приёмник": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Объем оперативной памяти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Объем энергонезависимой памяти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Поддержка карт памяти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;				
			}	
			case "Атрибут: Количество каналов приемника": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Bluetooth": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Аудиовыход 3.5 мм": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Wi-Fi": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: USB": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Видеовход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: mini HDMI": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: GPRS": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Емкость аккумулятора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Тип аккумулятора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Тип экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Поддержка TMC (FM-трафик)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Количество ядер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: WAAS": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: EGNOS": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Время работы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Разрешение видеосъёмки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Формат сжатия видео": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Носитель": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: 3D": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Встроенный проектор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Длина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Толщина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Вес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Тип экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Количество пикселей экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Размер экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Видоискатель": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Физический размер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Тип матрицы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Возможность смены объектива": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Ручная фокусировка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Светосила объектива (F-число)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Фокусное расстояние (35 мм эквивалент)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Минимальное фокусное расстояние": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Максимальное фокусное расстояние": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Оптическая стабилизация": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Автоматическая фокусировка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Фотосъемка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Количество пикселей при видеосъемке": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Оптический зум": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Цифровой зум": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Макс. кол-во кадров в секунду": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Количество точек матрицы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Встроенная вспышка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Крепление внешней вспышки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Ночная съемка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Минимальная освещенность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Встроенный динамик": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Встроенный микрофон": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Стерео": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поддержка карт памяти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Bluetooth": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Wi-Fi": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: USB": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: FireWire (IEEE 1394, iLink)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Аудио выход 3.5мм": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Линейный аудиовход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Дистанционный контроль": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Видеовыход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Видеовход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: S-video выход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: S-video вход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: HDMI-выход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Сенсорный экран": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Мин. дистанция фокусировки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Подключение жесткого диска": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Жесткий диск": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Оптический привод": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Кардридер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: ТВ-тюнер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Контейнеры": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Кодеки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Изображения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Интернет браузер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Клиент": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Сервер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Объявление сервисов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Компонентный видеовыход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Композитный видеовыход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Аудиовыход RCA": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Аудио выход 3.5мм": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: SCART": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: VGA-выход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: DVI": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: HDMI": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Ethernet": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: USB Slave": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: S/PDIF": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Высота": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Назначение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Форм-фактор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Автоматическая настройка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Ручная настройка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Интерфейс": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Работа вне зоны прямой видимости": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Радиус действия": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Лазерная указка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Экран": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Сенсорный экран": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Подсветка клавиатуры": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Cъемный аккумулятор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Тип аккумулятора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Зарядное устройство (крэдл)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Зарядное устройство (крэдл)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Зарядное устройство (крэдл)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Зарядное устройство (крэдл)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Зарядное устройство (крэдл)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Зарядное устройство (крэдл)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_13($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
				case "Атрибут: Дата выхода на рынок": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Класс": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Ценовой диапазон": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Тип": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Процессор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Платформа": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Количество ядер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;				
			}	
			case "Атрибут: Тактовая частота": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Энергопотребление процессора (TDP)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Фактура поверхности корпуса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Цвет корпуса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Фактура поверхности крышки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Цвет крышки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Подсветка корпуса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Пыле-, влаго-, ударопрочность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Глубина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Толщина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Толщина передней грани": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Вес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Диагональ экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Разрешение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;				
			}	
			case "Атрибут: Технология экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Объем памяти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Сенсорный экран": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: 3D-экран": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Датчик освещенности": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Объем памяти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Количество слотов памяти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Щелевая загрузка дисков": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Тип жесткого диска (дисков)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Емкость жесткого диска": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Оптический привод (ODD)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Карты памяти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Тип графического адаптера": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Графический адаптер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Встроенные динамики": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Цифровое поле (Numpad)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Защита от проливания": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Подсветка клавиатуры": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Заводская \"кириллица\" на клавишах": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Аудио входы (3.5 мм jack)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: NFC": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: ExpressCard/34": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: ExpressCard/54": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Слот для смарт-карт": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Bluetooth": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: LAN": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Wi-Fi": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: WiMax": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Dialup-модем (RJ11)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: 3G-модем": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Всего USB-портов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: USB 2.0": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: USB 3.0": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: COM-порт (RS-232)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: IEEE 1394 (FireWire)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: VGA (RGB)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: HDMI": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: DisplayPort": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Thunderbolt": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Thunderbolt 2.0": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Аудио выходы (3.5 мм jack)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: S/PDIF": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Док-порт": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Порт для второй батареи": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Сканер отпечатков пальцев": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: TV-тюнер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Количество ячеек": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Емкость аккумулятора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Тип аккумулятора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Солнечная батарея": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Сумка или чехол": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Операционная система": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Мышь": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Материал корпуса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Материал крышки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Поверхность экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: WirelessHD (WiDi)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Запас энергии": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Защита от падения и ударов (акселерометр)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Время работы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Емкость SSD": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Дата выхода на рынок": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Подключение сотового модема": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Стандарты беспроводной связи": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Макс. скорость беспроводной связи": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Дополнительные SSID (поддержка VLAN 802.1Q)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Протоколы безопасности беспроводной сети": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Поддержка WDS": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поддержка сотовой связи": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Встроенный модем": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поддерживаемые виды интернет-соединений": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Статическая маршрутизация": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Межсетевой экран (файрвол)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Проброс портов (forwarding)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Фильтрация трафика": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: VPN-сервер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Приоритезация беспроводного трафика (WMM)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поддержка IGMP": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Медиасервер (USB sharing)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Принт-сервер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Количество антенн": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: MIMO": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Съёмная антенна": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Диапазон частот": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Управление мощностью антенны": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Фильтрация по МАС-адресу клиента": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Кнопка Wi-Fi Protected Setup (WPS)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Gigabit Ethernet": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: PoE": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: LAN-порты": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: WAN-порты": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: DMZ-порты": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Порты USB": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Приоритезация трафика (QoS)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Эквивалентная излучаемая мощность (EIRP)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Управление полосой пропускания": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: \"Родительский контроль\"": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Коэффициент усиления антенны": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Radius-сервер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Общая выходная мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Усилитель": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Общий вес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Материал корпуса сателлитов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Пиковая мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Количество полос": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Импеданс": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Чувствительность (эффективность)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Аудиовыход 3.5 мм": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Аудиовход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: HDMI-вход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: HDMI-выход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Цифровой вход S/PDIF": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Пыле- и влагозащита": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Варианты цвета корпуса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Длина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Акселерометр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Тип экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Размер экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Сенсорный экран": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: 3D-экран": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поддержка фото форматов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Поддержка видео форматов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Поддержка текстовых форматов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Встроенная камера": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поддержка аудио форматов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Запись звука": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Встроенный динамик": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Встроенный микрофон": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: FM-приёмник": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: FM-трансмиттер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Тип памяти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Объем памяти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Поддержка карт памяти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: GPS": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Bluetooth": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Wi-Fi": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: USB": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: FireWire (IEEE 1394, iLink)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Композитный видеовыход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Видеовход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Тип аккумулятора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Cъемный аккумулятор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Емкость аккумулятора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Время работы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Вес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: ТВ-тюнер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Размещение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: CD": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: DVD": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Blu-ray": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Blu-ray": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: HDD": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: USB Flash": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Кардридер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: MPEG4 (DivX, XviD, AVI)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: MP3": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: WMA": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: JPEG": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: OGG": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Аудиосистема": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Подключение к парктронику": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Системы цветности": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Встроенные декодеры": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Громкая связь": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Размер экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Технология экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Аудиовыход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: S-video выход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Композитный видеовыход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Ethernet": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Кронштейн для крепления к потолку": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Антенна": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Питание": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_14($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
				case "Атрибут: Материал корпуса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Цвет корпуса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Длина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Толщина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Акселерометр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Разрешение экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;				
			}	
			case "Атрибут: Мультитач": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Тип экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Технология экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Сенсорный экран": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Подсветка экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Встроенная камера": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поддержка AZW": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поддержка AZW3": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Поддержка DjVu": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Поддержка DOC": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Поддержка DOCX": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Поддержка CBR": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Поддержка CBZ": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поддержка CHM": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поддержка ePub": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;				
			}	
			case "Атрибут: Поддержка FB2": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поддержка HTML": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поддержка MOBI": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поддержка PDF": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поддержка PPT": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поддержка PPTX": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поддержка RTF": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Поддержка TXT": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поддержка XLS": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поддержка XLSX": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: DSP": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поддержка фото форматов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поддержка аудио форматов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поддержка архивов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поддержка видео форматов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Встроенный динамик": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Встроенный микрофон": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поддержка карт памяти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Bluetooth": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Аудиовыход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Видеовыход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Wi-Fi": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: 3G-модем": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Длительность чтения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Количество градаций серого": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Тип матрицы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Количество точек матрицы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Максимальное разрешение снимка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Запись видео": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Максимальное разрешение видео": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Автоматическая фокусировка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Мин. дистанция фокусировки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Макс. кол-во кадров в секунду": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Цифровой зум": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: USB": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: FireWire (IEEE 1394, iLink)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Встроенный микрофон": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Кнопка съемки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Гарнитура в комплекте": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Длина кабеля": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Кардридер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Форм-фактор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Объём": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Питание": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Материал корпуса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Пыле-, влаго-, ударопрочность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Кнопка синхронизации": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Индикатор заполнения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Вес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: USB 3.0": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: USB 2.0": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Thunderbolt": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Ethernet": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: FireWire (iLink)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: eSATA": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Чехол": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Y-кабель USB (c двумя разъемами)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Функция авто-отключения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Аппаратное шифрование": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Скорость вращения шпинделя": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Интерфейсный адаптер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_15($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
				case "Атрибут: Экран": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Функция доступа в Интернет": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Флеш-память": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Оперативная память": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Видеостандарты": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Стандарт спутникового телевидения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Разрешение изображения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;				
			}	
			case "Атрибут: Управление позиционером": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Количество каналов в памяти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Количество тюнеров": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Картинка в картинке": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Игры": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Телетекст": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Родительский код": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поддержка карт памяти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Wi-Fi": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: USB": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Компонентный видеовыход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Композитный видеовыход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: TV SCART": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: VCR SCART": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: DVD SCART": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;				
			}	
			case "Атрибут: DVI": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: HDMI": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Цифровой выход S/PDIF": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: RCA аудиовыход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Ethernet": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: IDE": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: RS-232": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Диагональ": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Соотношение сторон экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Поддержка 3D": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Тип матрицы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Подсветка матрицы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Разрешение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Тип покрытия": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Сенсорный экран": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Частота обновления экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Время отклика": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Яркость": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Динамическая контрастность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Угол обзора по горизонтали": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Угол обзора по вертикали": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Встроенные динамики": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: TV-тюнер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Камера": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Интерфейс подключения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Композитный вход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Композитный видеовыход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Цифровой вход S/PDIF": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Выход на наушники": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: USB хаб": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: FireWire хаб": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: HDMI хаб": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Ethernet": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Кардридер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Регулировка высоты подставки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поворот в портретный режим": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Контрастность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Блок питания": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Время отклика (GtG)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_16($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
				case "Атрибут: Тип камеры": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Комплект": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Материал корпуса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Пыле- и влагозащита": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Встроенный проектор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Длина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;				
			}	
			case "Атрибут: Толщина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Вес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Сенсорный экран": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Live View": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Видоискатель": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Физический размер матрицы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Тип матрицы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Мин. чувствительность (ISO)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Макс. чувствительность (ISO)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Возможность смены объектива": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Ручная фокусировка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Светосила объектива (F-число)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Фокусное расстояние (35 мм эквивалент)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Мин. дистанция фокусировки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Оптическая стабилизация": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;				
			}	
			case "Атрибут: Количество точек матрицы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Максимальное разрешение видео": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Макс. кол-во кадров в секунду": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Ведущее число вспышки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Регулировка мощности вспышки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Крепление внешней вспышки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Приоритет диафрагмы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Приоритет выдержки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Ручная выдержка / диафрагма": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Баланс белого": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Сюжетные программы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Минимальная выдержка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Максимальная выдержка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Скорость съемки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Съемка в 3D": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Запись в формате RAW": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Объем буфера": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case " Атрибут: Запись звука": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Стереомикрофон": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Диктофон (аудиозаметки)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Поддержка карт памяти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: GPS": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Geotagging": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Wi-Fi": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: 3G/4G": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: USB": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поддержка дистанционного управления": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Композитный видеовыход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: HDMI-выход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: NFC": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Тип аккумулятора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Цвет корпуса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Встроенный фотопринтер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Максимальное фокусное расстояние": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Оптический зум": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Цифровой зум": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Контрастность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Емкость аккумулятора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Ресурс аккумулятора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Тип": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Общая выходная мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Усилитель": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Магнитное экранирование": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Материал корпуса сателлитов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Пульт ДУ": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;				
			}	
			case "Атрибут: FM-приёмник": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Наличие экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Материал корпуса сабвуфера": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Выходная мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Количество полос": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Количество динамиков": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Аудиовыход 3.5 мм": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Аудиовход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Цифровой вход S/PDIF": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Микрофонный вход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Кардридер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: USB": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Аудиовыход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Компонентный видеовыход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Экран": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;				
			}	
			case "Атрибут: Запись на USB-носитель": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case " Атрибут: Режим гарнитуры": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Bluetooth": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: NFC": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Беспроводной интерфейс": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Wi-Fi": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: AirPlay": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Онлайн-сервисы (Smart TV)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Соотношение сторон экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: USB Flash": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Воспроизведение в 3D": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Кардридер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поддержка HD контента": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: MPEG4 (DivX, XviD, AVI)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: MP3": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: WMA": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: JPEG": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: WMV9 (VC-1)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: H.264 (AVCHD)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: SACD": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: DVD-Audio": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Караоке": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Прогрессивная развертка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Цифровой выход S/PDIF": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: eSATA": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Композитный вход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: SCART": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: DVI": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Выход на наушники": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Микрофонный вход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Ethernet": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: HDMI-вход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Ethernet": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Кардридер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Регулировка высоты подставки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поворот в портретный режим": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Контрастность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Блок питания": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Время отклика (GtG)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_17($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
				case "Атрибут: Радио": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Воспроизведение с USB-накопителей": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Док-порт для устройств Apple": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Карты памяти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Выходная мощность звука": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Многофункциональный экран": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Проекционные часы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;				
			}	
			case "Атрибут: Стереодинамики": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Термометр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Подсветка кнопок": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Датчик приближения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Функция светильника": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Цвет": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Резервная батарейка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Тип экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Размер экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Цвет индикации": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Поворотный экран": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Регулировка яркости экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Датчик освещенности": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Сигнал будильника": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Два будильника": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;				
			}	
			case "Атрибут: Будильник для выходных дней": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Максимальное разрешение видео": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Повторный сигнал (Snooze)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Календарь": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Таймер автоотключения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Аудиовход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Тип тюнера": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Модуляция": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Расширенный диапазон частот FM": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Сохранение радиостанций": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Подсветка экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Отдельная настройка времени для проектора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Индикация частоты радиоприёма": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Количество мелодий будильника": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: USB": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Конструкция": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Диагональ экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case " Атрибут: Разрешение экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Операционная система": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Тип матрицы экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Версия операционной системы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Процессор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Графический ускоритель": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Внутренняя память": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Оперативная память": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Поддержка нескольких SIM-карт": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Мобильная связь": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Количество ядер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Тактовая частота": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Материал корпуса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Цвет корпуса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Длина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Толщина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Акселерометр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Гироскоп": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Барометр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Датчик приближения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поддержка ввода пером": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Поддержка ввода пером": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Датчик освещенности": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Карты памяти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Авто-фокус": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Вспышка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Количество активных пикселей фронтальной камеры": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;				
			}	
			case "Атрибут: Встроенный микрофон": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Встроенные динамики": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: GPS/A-GPS": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Электронный компас": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: MHL": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Bluetooth": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Wi-Fi": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: 3G-модем": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: USB 2.0": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: USB 3.0": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: HDMI": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: DisplayPort": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Аудио выходы (3.5 мм jack)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Док-порт": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: TV-тюнер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;				
			}	
			case "Атрибут: FM-тюнер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case " Атрибут: FM-трансмиттер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Емкость аккумулятора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Док-станция": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Клавиатура": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Дополнительный аккумулятор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Сумка или чехол": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Вес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Время работы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Беспроводная зарядка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Тип": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Подключение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Подсветка клавиш": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Колесо прокрутки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Дополнительные кнопки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Сенсорная панель": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Экран": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Динамики": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Встроенный микрофон": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: USB-хаб": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Аудио вход 3.5мм": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Аудио выход 3.5мм": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Проводной интерфейс": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Количество кнопок": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Назначение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Влагозащита": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Гибкая клавиатура": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Cъемный аккумулятор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Время работы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Солнечная батарея": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Длина провода": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Радиус действия": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Акустическое оформление": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Беспроводной интерфейс": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Пыле-, влаго-, ударопрочность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Цвет": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Штекер для микрофона": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Крепление": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Складная конструкция": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Подключение кабеля": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Съёмный кабель": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Длина кабеля": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Объёмное звучание": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Активное шумоподавление": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Штекер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Форма штекера": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Проводной пульт": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Регулятор громкости": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поддержка игровых консолей": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Количество наушников гарнитуры": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Тип излучателя": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Нижняя граница част. диапазона": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Верхняя граница част. диапазона": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Номинальное сопротивление": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Чувствительность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Диаметр излучателя": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Амбушюры": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Чехол": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Складная конструкция": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Кабель USB": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Переходник на 6.3 мм": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Переходник на 3.5 мм": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Переходник для самолета (два штекера 3.5 мм)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Переходник 2x3.5 мм на USB": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Удлинительный кабель": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Гарнитурный кабель": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Клипса для шнура": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Импеданс": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Тип аккумулятора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Материал амбушюр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Время работы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Радиус действия": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Макс. входная мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Чувствительность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Шумоподавление микрофона": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Количество арматурных излучателей": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Коэф. гармонических искажений (THD)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Комплект": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Тип базы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: АОН": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: CallerID": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Автоответчик": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Подключение смартфона": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Светодиодный индикатор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Количество трубок на одной базе": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Набор номера на базе": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Номера в памяти телефона": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Расширенная телефонная книга": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Присвоение мелодий записям в тел. книге": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Группы абонентов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Календарь": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Напоминания": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: SMS": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Сенсорный экран": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Подсветка экран": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Подсветка клавиатуры": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Голосовой набор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Голосовое управление": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Громкая связь": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Интерком (связь база-трубка)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Конференц-связь": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Связь между трубками": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Радионяня (прослушивание)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Голосовой АОН": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Пейджинг (поиск трубки)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Тип мелодий звонка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Количество стандартных мелодий": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Ночной режим": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Разъем для подключения к PC": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Покрытие в помещении": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Покрытие вне помещения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Функция резервного питания": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Время разговора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Время ожидания": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Время зарядки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Материал амбушюр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Материал амбушюр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Материал амбушюр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_18($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
				case "Атрибут: Разрешение видеосъёмки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Формат сжатия видео": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: 3D": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Конструкция": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Пыле- и влагозащита": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Встроенная вспышка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Разрешения видеосъёмки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;				
			}	
			case "Атрибут: Количество пикселей при видеосъемке": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Макс. кол-во кадров в секунду": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Timelapse-съёмка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Баланс белого": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Сюжетные программы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Ночная съемка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Встроенный микрофон": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: GPS": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Время работы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Встроенный экран": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Сенсорный экран": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Поддержка карт памяти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Bluetooth": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Аудиовыход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Аудиовход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;				
			}	
			case "Атрибут: Поддержка дистанционного управления": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: HDMI-выход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: NFC": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Wi-Fi": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: USB": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Вес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Длина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Толщина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Физический размер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Светосила объектива (F-число)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Мин. дистанция фокусировки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Минимальное фокусное расстояние": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Фотосъемка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Минимальная освещенность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Встроенный динамик": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Стерео": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case " Атрибут: Стандарт": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Прием HD-каналов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Проигрывание файлов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: EPG (электронный гид)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Телетекст": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Пауза воспроизведения (Timeshift)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Изображения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Компонентный видеовыход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Композитный видеовыход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Аудиовыход RCA": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Аудио выход 3.5мм": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Тактовая частота": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: SCART": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: HDMI": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Максимальное проекционное расстояние": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Ethernet": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Кардридер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: S/PDIF": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Питание для антенны через RF-вход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Глубина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Форматы сжатия видео": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Кодеки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Контейнеры": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: \"Родительский контроль\"": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Карты памяти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Определитель номера": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: ЖК-экран": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Телефонная книга": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;				
			}	
			case "Атрибут: Журнал исходящих вызовов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Набор на трубке": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Регулировка громкости": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Подсветка кнопок": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Возможность монтажа на стену": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Журнал входящих вызовов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Цифровой автоответчик": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Дуплексный спикерфон": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Однокнопочный набор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Ускоренный набор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Индикатор вызова": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Удержание вызова": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Автодозвон": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Повторный набор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Блокировка набора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;				
			}	
			case "Атрибут: \"Детский звонок\"": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case " Атрибут: Отключение микрофона (Mute)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Будильник и календарь": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Калькулятор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Тип матрицы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Назначение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Соотношение сторон": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Разрешение матрицы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Поддержка 3D-изображения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Количество ламп": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Мощность лампы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Световой поток": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Ресурс лампы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Контрастность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Минимальный размер по диагонали": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Максимальный размер по диагонали": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Цифровая коррекция \"трапеции\": вертикальная": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Звук": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Сдвиг объектива: вертикальный": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поддержка карт памяти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Компонентный вход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Композитный вход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: S-video вход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: S-Video выход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: VGA-вход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: VGA-выход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: DVI": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Цифровой вход S/PDIF": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Цифровой выход S/PDIF": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Минимальное проекционное расстояние": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Цифровая коррекция \"трапеции\": горизонтальная": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Сдвиг объектива: горизонтальный": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Размер экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Сенсорный экран": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Формат экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Поддержка фото форматов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Поддержка видео форматов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Поддержка текстовых форматов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Поддержка карт памяти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: USB-flash": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Bluetooth": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Аудиовыход 3.5 мм": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: IrDA": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: LAN": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Встроенные динамики": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Таймер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Часы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Календарь": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Аудио проигрыватель": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут:  FM приёмник": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Пульт ДУ": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: RSS/e-mail reader": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Сменные панели": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_19($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
				case "Атрибут: Материал корпуса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Цвет корпуса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Встроенный микрофон": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Вариомикрофон (с управляемым зумом)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Позиционируемые микрофоны": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Настройка чувствительности микрофона": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Хранение записей": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;				
			}	
			case "Атрибут: Длительность записи на встр. память": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Подключение к ПК": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Встроенный штекер USB": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Разъем для внешнего микрофона": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Разъем для наушников": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Встроенный динамик, диаметр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Встроенный микрофон": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: FM-радио (с возможностью записи)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Встроенная подставка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Сенсорное управление": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Дистанционное управление": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Голосовое управление": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Голосовой гид": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Автоматические режимы (сцены)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Голосовая активация записи (VOR/VAS/VCVA)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;				
			}	
			case "Атрибут: Монитор записи/сигнала": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Эквалайзер записи": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Низкочастный (шумовой) фильтр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Запись телефонных разговоров": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Запись по таймеру": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Голосовой фильтр (фильтр четкости)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Тип экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Элементы питания": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Время работы при записи": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Зарядка через порт USB": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Чехол": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Ветрозащитный экран": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Адаптер питания": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Длина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Толщина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Наилучшее качество записи": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case " Атрибут: Ёмкость аккумулятора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Предварительная запись": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Ручное управление уровнем записи": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Индексация записей (маркеры)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Перезапись (запись поверх)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Вставка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Частичное удаление": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Разделение файла": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Синхронизированная запись": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Отсечение шумов при воспроизведении": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поиск по календарю": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Воспроизведение по времени (будильник)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поддержка подкастов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поддержка фото": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Время работы при воспроизведении": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Зарядка через адаптер питания": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_20($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
				case "Атрибут: Тип": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Число скоростей": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Плавающая головка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Массажер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Подсветка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Количество пинцетов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут:  Насадка для бритья": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;				
			}	
			case "Атрибут: Насадка для точечного удаления волос": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Насадка для пилинга": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Насадка-триммер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Насадка-ограничитель": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Насадка для деликатных зон": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Питание от АКБ": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Водонепроницаемый корпус": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Минимальная длина волос для эпиляции": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Индикатор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Питание от сети переменного тока": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Устройство для зарядки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Способ бритья": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Время автономной работы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Количество оборотов мотора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут:  Количество бритвенных головок": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;				
			}	
			case "Атрибут: Плавающие головки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Водонепроницаемый корпус": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Отсек для сбора волосков": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Время зарядки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Триммер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Дисплей": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Индикатор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Устройство для автоматической очистки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Устройство для зарядки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Система бритья": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Количество направлений головки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Дополнительное действие": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Питание": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Движения головки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Частота пульсации": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Контроль давления": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Индикатор заряда аккумулятора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case " Атрибут: Таймер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Приспособление для хранения насадок": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Возможность крепления на стене": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Комплектация": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
				}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_21($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
				case "Атрибут: Тип": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Число скоростей": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Плавающая головка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Массажер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Подсветка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Количество пинцетов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут:  Насадка для бритья": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;				
			}	
			case "Атрибут: Насадка для точечного удаления волос": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Насадка для пилинга": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Насадка-триммер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Насадка-ограничитель": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Насадка для деликатных зон": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Питание от АКБ": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Водонепроницаемый корпус": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Минимальная длина волос для эпиляции": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Индикатор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Питание от сети переменного тока": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Устройство для зарядки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Способ бритья": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Время автономной работы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Количество оборотов мотора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут:  Количество бритвенных головок": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;				
			}	
			case "Атрибут: Плавающие головки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Водонепроницаемый корпус": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Отсек для сбора волосков": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Время зарядки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Триммер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Дисплей": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Индикатор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Устройство для автоматической очистки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Устройство для зарядки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Система бритья": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Количество направлений головки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Дополнительное действие": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Питание": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Движения головки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Частота пульсации": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Контроль давления": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Индикатор заряда аккумулятора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case " Атрибут: Таймер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Приспособление для хранения насадок": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Возможность крепления на стене": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Комплектация": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_22($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
				case "Атрибут: Тип": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Термодатчик": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Регулировка нагрева": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Регулировка воздушного потока": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Подача холодного воздуха": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Турбо-обдув": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;				
			}	
			case "Атрибут: Функция ионизации": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Складная конструкция": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Концентратор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Диффузор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Круглая расчёска": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Массажная расчёска": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Выпрямитель": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Термощётка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Объёмная расчёска": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Вращающийся шнур": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Ионизация": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Назначение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Длина стрижки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Количество установок длины": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Материал лезвий": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;				
			}	
			case "Атрибут: Питание": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Система сбора состриженных волос": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут:  Использование в душе": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Влажная чистка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Не требует смазки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Насадки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Время зарядки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Время автономной работы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Вес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Скорость работы двигателя": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: В комплекте": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Рабочая поверхность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Дисплей": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Индикатор работы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Вращающийся шнур": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Подставка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case " Атрибут: Регулировка температуры": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Максимальная температура нагрева": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Настройка режимов по типу волос": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Быстрый нагрев": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Ионизация": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Пароувлажнение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Автоотключение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Выпрямитель": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Насадка для гофре": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Круглая плойка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Плойка с несколькими стержнями": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Фигурная плойка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Спиральная плойка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Минимальная температура нагрева": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Фиксация в закрытом положении": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Количество диаметров бигуди": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Материал платформы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Максимальная нагрузка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Цена деления": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Определение доли воды": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Определение доли жировой ткани": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Определение доли мышечной ткани": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Определение доли костной ткани": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Расчет ИМТ (BMI)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Автоматическое выключение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Измерение роста": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Функция памяти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Автоматическое включение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;				
			}	
			case "Атрибут: Дисплей": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Длина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Толщина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;		
				}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_23($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
				case "Атрибут: Мощность нагрева": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Вибромассаж": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Пузырьковый массаж": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Магниты": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Люминотерапия (подсветка)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Диффузор (обдув, ароматерапия)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Педикюрный центр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;				
			}	
			case "Атрибут: Таймер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Количество режимов работы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: ИК-прогрев": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Струйный (турбо) массаж": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Массажный центр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Длина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Высота": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Объем": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Вес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Тип двигателя": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Напряжение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Режущая система": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Штанга": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Ременная оснастка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;				
			}	
			case "Атрибут: Колеса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Вес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут:  Антивибрационная система": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поворотная косильная головка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Регулировка угла наклона": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Комби-привод": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Расположение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Вес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Скорость работы двигателя": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Скорость вращения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Диаметр лески": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Длина штанги": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Легкий пуск": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Подача лески": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Ёмкость топливного бака": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Способ нагрева": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case " Атрибут: Объем": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Максимальная мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Максимальная температура нагрева": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Регулировка температуры": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Время нагрева": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Расположение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Внутреннее покрытие бака": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Теплоизоляция": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Антибактериальная защита": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Термостат": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Дисплей": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Давление": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Термостат безопасности": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Высота": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Глубина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Исполнение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Обслуживаемая площадь": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Объем резервуара для воды": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Гигростат": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Минимальный уровень шума": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Автоматическая смена режимов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Индикация": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Таймер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Кол-во режимов увлажнения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Дистанционное управление": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Метод увлажнения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Нагрев воды": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;				
			}	
			case "Атрибут: Защита от известкового налета": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Ионизация": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Озонирование": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Ароматизация": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: HEPA-фильтр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: ULPA-фильтр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;		
			}
			case "Атрибут: Антибактериальная защита воды": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Обслуживаемый объем": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Ночной режим": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;		
			}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_24($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
				case "Атрибут: Био-фильтр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Плазменный фильтр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Ионизация": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Угольный фильтр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Фотокаталитический фильтр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Электростатический фильтр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Дезодорирование": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;				
			}	
			case "Атрибут: Тип кондиционера": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Тип внутреннего блока": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Режим работы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Обслуживаемая площадь": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Хладагент (фреон)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Мощность охлаждения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Потребляемая мощность при охлаждении": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Расход воздуха": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Осушение воздуха": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Увлажнение воздуха": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Пульт ДУ": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Турбо-режим": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Режим «Сон»": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Самоочистка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Шум внутреннего блока": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;				
			}	
			case "Атрибут: Мощность обогрева": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Потребляемая мощность при обогреве": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут:  Шум внешнего блока": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Вес внешнего блока": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Тип устройства": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Встроенный проектор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Прогноз погоды": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Календарь": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Часы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Будильник": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Лунный календарь": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Время восхода и захода солнца": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Штормовое предупреждение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Предупреждение о заморозках": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Температура внутри помещения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Влажность внутри помещения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Давление внутри помещения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case " Атрибут: Температура снаружи помещения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Влажность снаружи помещения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Давление снаружи помещения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Скорость и направление ветра": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Количество и интенсивность осадков": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Температура точки росы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Температура охлаждения ветром": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Выпрямитель": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Количество ультрафиолета": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Количество поддерживаемых датчиков": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Радиус действия радиодатчиков": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Пульт дистанционного управления": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Подключение к компьютеру": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Варианты размещения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Установка предельных параметров": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Высота": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Тип": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Обслуживаемая площадь": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Автоматическая смена режимов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Таймер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Дистанционное управление": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Макс. производительность очистки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Электростатический фильтр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Плазменный фильтр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Ионизация": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Размещение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Питание": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Обслуживаемый объем": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;				
			}	
			case "Атрибут: Ночной режим": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: HEPA-фильтр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Угольный фильтр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Фотокаталитический фильтр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Ультрафиолетовая лампа": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Озонирование": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Индикация": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Водный фильтр (мойка воздуха)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Кол-во вырабатываемой влаги": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Кол-во ступеней очистки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Длина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Высота": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Объем резервуара для воды": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Минимальный уровень шума": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Кол-во режимов очистки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Защита от известкового налета": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Кроссовер в комплекте": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Мощность максимальная": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Мощность номинальная": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Диапазон частот": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Чувствительность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Сопротивление": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Количество полос": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Диаметр ВЧ динамика": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Диаметр НЧ динамика": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Типоразмеры": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Форма диффузора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Диаметр СЧ динамика": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Установочная глубина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
				}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_25($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
				case "Атрибут: Пульт ДУ": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Типоразмер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Тип": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Съемная панель": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Моторизованная панель": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Поддержка форматов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Соотношение сигнал/шум": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;				
			}	
			case "Атрибут: Максимальная мощность на канал": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Количество каналов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Эквалайзер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Стерео Bluetooth (A2DP)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: USB": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Карты памяти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: iPod адаптер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Bluetooth": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Аудио выход RCA": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Подключение камеры заднего вида": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Диапазоны частот": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: TV-тюнер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Тип экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Сенсорный экран": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: GPS-навигатор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;				
			}	
			case "Атрибут: RDS": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Пульт в комплекте": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут:  Видео выход RCA": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Размер экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Аудио вход RCA": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Управление CD-чейнджером": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Тип питания": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Дисплей": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Пульт ДУ": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Слот для карт памяти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: USB": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Bluetooth": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Аудиовход 3.5мм": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Диапазон частот": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Радиус действия": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Комплектация": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Эквалайзер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case " Атрибут: Встроенная память": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Адаптер для iPod/iPhone": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Стерео Bluetooth (A2DP)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Количество каналов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Поддержка RDS": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Подсветка дисплея": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Количество каналов усиления": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Нижняя граница част. диапазона": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Верхняя граница част. диапазона": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Соотношение сигнал/шум": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Мощность на канал при 2 Ом": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Коэф. гармонических искажений (THD)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Аудиовходы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Высота": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Глубина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Напряжение DC": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Потребляемая мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Объем": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Максимальное охлаждение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Теплоизоляция": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Напряжение AC": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Антибактериальное покрытие": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
				}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_26($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
				case "Атрибут: Тип питания": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Потребляемая мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Производительность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Минимальное рабочее давление": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Максимальное рабочее давление": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Длина кабеля": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Макс. температура воды на входе": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;				
			}	
			case "Атрибут: Подача пара": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Длина шланга": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Насадки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Автоматическое отключение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Фильтр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Длина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Высота": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Бак для воды": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Максимальное допустимое давление": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Бак для моющего средства": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Материал поршней насоса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Подогрев воды": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Забор воды": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Материал корпуса насоса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;				
			}	
			case "Атрибут: Предохранительный клапан": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Защита от накипи": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут:  Скрытая установка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Визуальная индикация": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Звуковая индикация": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Обнаружение ККДДАС \"Стрелка\"": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Обнаружение X": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Обнаружение короткоимпульсных сигналов X": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Обнаружение K": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Обнаружение короткоимпульсных сигналов K": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Обнаружение Ka": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Обнаружение Ku": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Обнаружение короткоимпульсных сигналов Ku": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Обнаружение лазера": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Питание от прикуривателя а/м": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Периметр обнаружения лазера": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Вольтметр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case " Атрибут: Датчик наружной температуры": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Разъем для внешнего динамика": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Доп. питание от солнечной батареи": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Цифровой компас": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Измерение частоты сигнала радара": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Возможность отключения Ka": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Возможность отключения X": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Возможность отключения K": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Возможность отключения лазера": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Защита от помех": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Защита от обнаружения (VG-2)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Антисон (Stay Alert)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут:  Система сохранения АКБ автомобиля": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Обнаружение короткоимпульсных сигналов Ka": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Возрастная группа": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Глубина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Крепление": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Ремни для ребенка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Способ установки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Усиленная боковая защита": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Регулировка высоты подголовника": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Регулировка наклона спинки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Анатомический вкладыш": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Вращение на основании": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Ручка для переноски": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Качающееся основание": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Использование в самолете": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Защита от солнца": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Москитная сетка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Съемный чехол": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Возможность хранения вещей": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Горизонтальное положение спинки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Регулировка ширины кресла": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Совместимость с шасси": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Регулировка высоты ремней": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Накладки на внутренние ремни": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Совместимость с базой": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Управление натяжением ремней": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Назначение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Тип датчика": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Продувание": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Единицы измерения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Диапазон измерений в ВАС": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Время ожидания показаний": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Дисплей": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Диапазон измерений в промилле": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Температура применения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Погрешность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Автоотключение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Возможность защиты паролем": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Фонарик": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Индикация уровня заряда батареи": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Питание": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Диапазон измерений в мг/л": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Время очистки сенсора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Звуковой сигнал": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Подключение к компьютеру": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Функция \"антиобман\"": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Самостоятельная замена сенсора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Самокалибровка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_27($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
				case "Атрибут: Максимальное давление": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Встроенный фонарь": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Производительность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Выходная мощность постоянная": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Выходная мощность пиковая": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Защита": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Порт USB": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;				
			}	
			case "Атрибут: Давление": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Назначение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Метод установки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Тип": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Форма": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Длина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Высота": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Производительность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Напор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Напряжение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Размер пропускаемых частиц": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Глубина погружения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Диаметр разъема соединения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;				
			}	
			case "Атрибут: Длина кабеля": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Качество воды": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут:  Температура воды": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Вес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Вид домкрата": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Механизм": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Грузоподъемность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Минимальная высота подъема": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Максимальная высота подъема": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
				}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_28($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
				case "Атрибут: Чехол": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Тип": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Длина рабочей поверхности": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Ширина рабочей поверхности": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Высота": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Основа": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Вес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;				
			}	
			case "Атрибут: Подставка для утюга": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Полочка для простыней": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Полочка для глажения рукавов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Кронштейн для плечиков": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Розетка для утюга": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Регулировка высоты": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Держатель для шнура": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Толщина ножек": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Технология печати": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Тип чернил": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Оригинал": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Цвет": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Ресурс": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Совместимость с принтерами": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Объем": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;				
			}	
			case "Атрибут: Диагональ экрана ноутбука": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Целевая аудитория": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут:  Цвет": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Органайзер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Пыле-, влаго-, ударопрочность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Отделение для планшета": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Высота": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Глубина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Материал": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Количество внешних отделений": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Количество внутренних отделений": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Карман для мобильного телефона": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Вес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Отделение для ноутбука": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Отделение для штатива": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Световозвращающие нашивки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Тип": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Макс. длина устройства": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Макс. ширина устройства": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Макс. толщина устройства": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Назначение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Назначение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Назначение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Назначение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Назначение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Назначение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
				}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_29($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
				case "Атрибут: Тип": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Исполнение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Номинальная активная мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Объём двигателя": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Ёмкость топливного бака": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Запуск": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Время непрерывной работы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;				
			}	
			case "Атрибут: Уровень шума": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Индикатор уровня топлива": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Индикатор уровня масла": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Число фаз": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Вольтметр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Высота": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Длина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Тип двигателя": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Мощность двигателя": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Ширина захвата (культивации)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Трансмиссия": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Количество передач": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Количество оборотов фрез": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Редуктор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;				
			}	
			case "Атрибут: Надувные колеса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Защитные колесные диски": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут:  Вес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Диаметр фрез": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Глубина вспашки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Система агрегатирования": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Вал отбора мощности": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Тормоз": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Пуск двигателя": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Установка навесного оборудования": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Регулируемый по высоте руль": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Защитные кожухи": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Диаметр колес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Подвеска штанг управления": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Движитель": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Ширина захвата": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Высота захвата": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Самоходная": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Вариатор (CVT)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Количество скоростей движения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Двухступенчатая система отбрасывания": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Раструб, управляемый с панели оператора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Удлинитель дефлектора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Регулируемые полозья": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Нож для подрезки сугробов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Подогрев рукояток": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Пуск двигателя": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Диаметр колес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Дальность выброса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Выбор ведущего колеса (с отключением блокировки дифференциала)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
				}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_30($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
				case "Атрибут: Тип": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Подключение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Проводной интерфейс": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Сенсор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Встроенная память": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Грузы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Количество кнопок": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;				
			}	
			case "Атрибут: Тип колеса прокрутки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Максимальное разрешение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Индикатор заряда батареи": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Радиус действия": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Тип аккумулятора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Количество колёс прокрутки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Назначение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Изменяемое разрешение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Тип элементов питания": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Зарядное устройство (крэдл)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Зарядка от USB": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Вес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Подключение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Проводной интерфейс": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Тип колеса прокрутки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;				
			}	
			case "Атрибут: Время отклика": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Место крепления": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут:  Цвет": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Совместимость с креплением VESA": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Максимальная нагрузка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Угол наклона": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Расстояние от стены (потолка)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Угол поворота": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Встроенный уровень": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Диагональ телевизора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Диагональ экрана ноутбука": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Охлаждение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Материал корпуса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Кол-во вентиляторов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Расположение вентиляторов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Настройка вентиляторов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Питание": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Диаметр вентилятора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Компактный дизайн": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Регулируемый наклон": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Фиксация ноутбука": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Мягкое основание": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Платформа для мыши": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Отсек для хранения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: USB порт": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Встроенные динамики": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Внешние устройства": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Диаметр колес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Клавиатура в комплекте": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Мышь в комплекте": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Подсветка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Длина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Высота": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
				}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_31($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
				case "Атрибут: Тип": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Потребляемая мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Тип питания": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Глубина пропила (дерево)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Глубина пропила (металл)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Число ходов полотна в минуту": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Вес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;				
			}	
			case "Атрибут: Выходная мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Глубина реза 90°": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Диаметр режущего диска": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Диаметр посадочного гнезда диска": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Глубина реза 45°": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Скорость вращения шпинделя": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Плавный пуск": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Подсветка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Лазерный маркер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Пылесборник": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Сдувание опилок": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Режимы работы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Тип патрона": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Питание": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Регулировка скорости вращения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;				
			}	
			case "Атрибут: Максимальный крутящий момент": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Диаметр патрона": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут:  Реверс": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Дополнительная рукоятка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Штатив": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Индикатор уровня заряда аккумулятора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Кейс": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Дополнительный аккумулятор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Зарядное устройство": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Биты (насадки-отвертки)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Емкость": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Максимальная глубина пропила (сталь)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Режимы скорости вращения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Предохранительная муфта": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Ограничитель глубины сверления": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Защита от пыли": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Время зарядки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Блокировка кнопки включения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Максимальный диаметр сверления, бетон": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Держатель для бит": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Быстрозажимной патрон": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Стандартный кулачковый патрон": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Режим маятникого хода": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Тип корпуса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Прозрачный предохранительный щиток": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Прозрачный предохранительный щиток": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Автоматический сдув опилок": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Подключение к пылесосу": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Система быстрой замены пилок": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Кейс": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Максимальная глубина пропила (цветные металлы)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Механизм защиты от прикосновения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_32($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
				case "Атрибут: Тип": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Вид": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Тип питания": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Длина шины": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Вес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Скорость движения цепи": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;				
			}	
			case "Атрибут: Тормоз цепи": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Питание": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Реверс": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Максимальный крутящий момент": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Блокировка шпинделя": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Подсветка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Напряжение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Тип патрона": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Лазерный маркер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Пылесборник": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Сдувание опилок": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Режимы работы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
				}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_33($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
				case "Атрибут: Режимы работы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Тип патрона": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Питание": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Потребляемая мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Режимы скорости вращения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Регулировка скорости вращения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Максимальное число ударов/мин": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;				
			}	
			case "Атрибут: Максимальная энергия удара": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Максимальный диаметр сверления, бетон": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Максимальный диаметр сверления, дерево": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Реверс": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Дополнительная рукоятка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Предохранительная муфта": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Ограничитель глубины сверления": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Штатив": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Блокировка кнопки включения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Пылесборник": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Подсветка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Потребляемая мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Выходная мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Частота вращения (холостой ход)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Вес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Питание": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Полировальная машина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Фиксация шпинделя": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Электронная система стабилизации": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: \"Константная\" электроника": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Ограничение пускового тока": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Рукоятка для двустороннего использования": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Регулировка скорости оборотов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Защита от непреднамеренного включения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Антивибрационная система": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Система быстрой замены диска": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Защитная блокировка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Отключающиеся угольные щетки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
				}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_34($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
				case "Атрибут: Частота вращения вала": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Рабочая ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Питание": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Потребляемая мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Глубина строгания": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Глубина выборки паза": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: V-образный паз": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;				
			}	
			case "Атрибут: Выходная мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Тип ножей": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Параллельный упор, дерево": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Возможность стационарной установки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Вес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Количество ножей": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Длина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Напряжение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Сварочный ток": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Продолжительность нагрузки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Напряжение без нагрузки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Диаметр электрода": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Класс изоляции": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Высота": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Тип": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Частота колебаний": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Диапазон колебаний": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Крепление на зажимах": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Крепление на липучке": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Стационарное использование": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Пылеотвод": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Возможность подсоединения пылесоса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Электронная система стабилизации": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Защитная блокировка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Ограничение пускового тока": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Эксцентриситет": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Скорость движения ленты": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Система автоматического центрирования шлифленты": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Выходная мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Диаметр шейки шпинделя": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
				}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_35($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
				case "Атрибут: Тип двигателя": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Мощность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Макс. толщина веток": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Режущий инструмент": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Скорость вращения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Контейнер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Вес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;				
			}	
			case "Атрибут: Электропитание": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Сохранение факсов": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Автоответчик": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Определитель номера": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Быстрый набор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Телефонная трубка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: PC Fax": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Максимальная месячная нагрузка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Формат": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Печать": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Технология печати": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Копир": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Сканер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Факс": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Встроенный принтсервер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Поддержка прямой печати": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Встроенный сшиватель": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Тип сканера": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Ретуширование изображения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Входной лоток": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Наличие экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: USB-flash": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Поддержка карт памяти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Жесткий диск": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Ethernet": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: FireWire (iLink)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: LPT": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: IrDA": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Bluetooth": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Wi-Fi": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Объем оперативной памяти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Сканирующий элемент": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Высота": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Глубина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Уровень шума при работе": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Время выхода первой страницы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Время выхода первого снимка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Время вывода первой ч/б копии": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Время вывода первой цветной копии": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Копирование без подключения к компьютеру": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Скорость цветной печати ": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Выходной лоток": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Поддержка AirPrint": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Время печати 10x15см": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Совместимость": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Тип": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Количество кнопок": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Аналоговые джойстики": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Обратная связь": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Беспроводной": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Рычаг управления двигателем": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Диаметр руля": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Подрулевые переключатели": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Полный угол поворота руля": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Блок переключения передач": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Педали": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Cъемный аккумулятор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Объем": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Интерфейс": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Конструкция": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Кардридер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Необычный дизайн": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Компактный дизайн": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Пыле-, влаго-, ударопрочность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Светодиодный индикатор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Блокировка записи": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Аппаратное шифрование": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Сканер отпечатка пальцев": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Скорость последовательного чтения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Скорость последовательной записи": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
				}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_36($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
				case "Атрибут: Тип накопителя": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Объём": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Форм-фактор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Интерфейс": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Буфер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Скорость последовательного чтения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Скорость последовательной записи": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;				
			}	
			case "Атрибут: Уровень шума при чтении/записи": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Уровень шума в режиме ожидания": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Ударная нагрузка при работе": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Ударная нагрузка в нерабочем состоянии": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Энергопотребление (чтение/запись)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Энергопотребление (ожидание)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Среднее время доступа": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Толщина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Количество пластин": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Материал": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Складная конструкция": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Грузоподъемность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Количество мест": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Рекомендуемый возраст": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Спинка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Ручка для толкания": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Полозья": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Материал полозьев": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Веревка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Длина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: USB-flash": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Поддержка карт памяти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Жесткий диск": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Ethernet": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: FireWire (iLink)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: LPT": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: IrDA": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Bluetooth": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Wi-Fi": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Объем оперативной памяти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Сканирующий элемент": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Высота": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Руль управления": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Съемное сиденье": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Амортизация": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Ремни безопасности": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Тормоз": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Сигнал": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Подсветка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Материал сиденья": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Вес ": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Утепленный чехол (сиденье)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Язык": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Тип": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Жанр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Тип": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Количество игроков": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Особенность количества игроков": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Возраст": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Время на партию": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Двигатель": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Частота": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Количество каналов управления": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Управление с мобильного устройства": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Схема вертолета": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Дальность действия": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Время работы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Тип аккумулятора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Емкость аккумулятора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Требует сборки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Время заряда": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Масштаб": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
				}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_37($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
				case "Атрибут: Тип": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Вид автомодели": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Масштаб": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Двигатель": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Частота": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Количество каналов управления": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Управление с мобильного устройства": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;				
			}	
			case "Атрибут: Привод": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Время заряда": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Максимальная скорость": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Время работы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Дальность действия": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Длина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Высота": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Тип аккумулятора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Емкость аккумулятора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Тематическая серия": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Питание пульта управления": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Рекомендуемый возраст": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Для девочек": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Количество деталей": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Тип коляски": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Количество точек опоры": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Количество мест": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Модульная система": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Материал рамы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Перекидная ручка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Сдвоенные колёса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Диски колёс": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Поворотные колёса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Фиксация поворотных колёс": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Нижняя корзина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Регулируемый наклон спинки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Страховочные ремни": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Регулируемый капюшон": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Окошко в капюшоне": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Держатель для бутылочки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Карман для мелочей": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Дождевик": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Сумка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Глубина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Переноска": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Регулируемая жесткость подвески": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Регулируемая по высоте ручка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Количество ступеней наклона": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Регулируемая подножка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Передняя панель": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Светоотражающие нашивки ": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Макс. угол откидывания спинки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Матрасик": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Чехол на ноги": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Противомоскитная сетка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Рюкзак": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Глубина сиденья": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Ширина сиденья": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Диаметр основных колёс": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Диаметр передних колёс": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Подножка для второго ребенка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Расположение сидений": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Независимая регулировка каждого сиденья": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Тип древесины": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Цвет": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Количество уровней дна": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Механизм качания": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Направление качания": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Съемные прутья": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Тип днища": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Ящики": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Пеленальный столик": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Накладки-прорезыватели": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Стопор колес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Матрац": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Длина спального места": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Ширина спального места": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
				}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_38($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
				case "Атрибут: Тип": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Вид электромобиля": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Рекомендуемый возраст": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Количество мест": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Макс. вес катающихся": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Дистанционное управление": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Напряжение питания двигателя": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;				
			}	
			case "Атрибут: Количество двигателей": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Количество скоростей (включая задний ход)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Скорость движения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Ёмкость аккумулятора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Время непрерывной езды": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Время зарядки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Амортизация": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Колёса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Открываемые двери": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Багажник": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Педали": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Лобовое стекло": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Передние фары": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Задние фары": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Поворотники": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Зеркала заднего вида": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Музыкальный блок": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Подключение плеера": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: FM-радио": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Мягкие сиденья": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Ремни безопасности": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Сигнал (клаксон)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Назначение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Конструкция": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Рекомендуемый возраст": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Допустимый вес ребенка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Механизм складывания": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Количество уровней дна": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Боковой вход (лаз)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Капюшон": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Карман": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Столик-органайзер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Столик-органайзер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Кольца-держатели": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Колеса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Пеленальный столик": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Москитная сетка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Электронный блок": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Подвесные игрушки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Сумка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Длина спального места": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Высота": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Длина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Длина в сложенном виде": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Ширина в сложенном виде": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Высота в сложенном виде": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Люлька": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Ширина сиденья": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Тип лодки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Днище": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Количество отсеков": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Надувной киль": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Транец": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Грузоподъемность": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Пассажировместимость": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Диаметр баллона": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Максимальная мощность мотора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
				}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_39($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
				case "Атрибут: Возраст детей": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Назначение": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Конструкция": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Джакузи": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Форма": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Объём": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Длина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;				
			}	
			case "Атрибут: Высота": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Прозрачные стенки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Сливной клапан": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Фильтр-насос": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Распылитель (фонтан)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Генератор пузырьков": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Скиммер (устройство сбора мусора)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}	
			case "Атрибут: Комплект для чистки бассейна": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Подстилка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Тент-чехол": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Лестница": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Сетка для волейбола": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Вес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Время установки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Тип фильтр-насоса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Базовый блок": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Жёсткость": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Симметричный матрас": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Детский матрас": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Количество мест": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Макс. нагрузка на спальное место": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Плотность пружин": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Расположение пружин": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Латекс": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Пенополиуретан (поролон)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Пенополиуретан с добавлением латекса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Струтофайбер (периотек)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Термоскрепленный войлок": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Кокосовая койра": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Сизаль": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Вязкоэластичная пена": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Мебельная сетка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Лекан (льняное полотно)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Спанбонд": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Хлопковое полотно (х/б ватин)": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Полушерстяной ватин": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Конский волос": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Тип ткани чехла": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Стёганый чехол": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Чехол \"зима-лето\"": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Съёмный чехол": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Высота базового блока": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Процент хлопка в ткани чехла": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Высота в сложенном виде": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Количество зон жёсткости": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
				}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
	
	private function properties_skeleton_40($columns)
    {
        $properties = array();

        foreach ($columns as $index => $column) {
            $column = iconv('windows-1251', 'utf-8', $column);

            switch ($column) {
				case "Атрибут: Стандарт связи": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Тип": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Операционная система": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Исходная версия операционной системы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Тактовая частота процессора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Количество ядер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}	
			case "Атрибут: Фирменный интерфейс": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;				
			}	
			case "Атрибут: Поддержка нескольких SIM-карт": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Процессор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Графический ускоритель": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Конструкция корпуса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Стереодинамики": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Материал корпуса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Формат SIM-карты": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}	
			case "Атрибут: Фонарик": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Светодиодный индикатор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Длина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Ширина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Толщина": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Вес": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Акселерометр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Барометр": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Гироскоп": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Технология экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Количество цветов экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Размер экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Разрешение экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Разрешающая способность экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Работа в перчатках": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Сенсорный экран": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Оптическая стабилизация": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Встроенная камера": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Дополнительная камера": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: ТВ-тюнер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Встроенная вспышка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Громкая связь": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Wi-Fi": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Запись звука": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Аудио проигрыватель": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: FM-приёмник": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: FM-трансмиттер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Объем оперативной памяти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Объем энергонезависимой памяти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Поддержка карт памяти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: GPS": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Geotagging": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Bluetooth": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Аудиовыход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: WiMAX ": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: USB": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: HDMI-выход": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: USB On-The-Go": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: NFC": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: ИК-порт": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: MHL": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Емкость аккумулятора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Несъёмный аккумулятор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Беспроводная зарядка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Сменные панели": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Датчик освещенности": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Автоматическая фокусировка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: ГЛОНАСС": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Время разговора": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Время ожидания": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Дополнительный экран": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: SAR": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Время зарядки": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Разрешение экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Диагональ экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Тип матрицы экрана": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Операционная система": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Версия операционной системы": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Процессор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Графический ускоритель": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Оперативная память": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Внутренняя память": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Поддержка нескольких SIM-карт": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Мобильная связь": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Количество ядер": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Тактовая частота": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Материал корпуса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Цвет корпуса": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Датчик приближения": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Поддержка ввода пером": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: 3D-экран": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Датчик освещенности": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Карты памяти": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'D'
				);
				break;
			}
			case "Атрибут: Авто-фокус": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Вспышка": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Количество активных пикселей фронтальной камеры": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'I'
				);
				break;
			}
			case "Атрибут: Встроенный микрофон": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: GPS/A-GPS": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Электронный компас": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: USB 2.0": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: USB 3.0": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: DisplayPort": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Док-порт": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Док-станция": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Клавиатура": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
			}
			case "Атрибут: Дополнительный аккумулятор": {
				$properties[$index] = array(
					'name' => iconv('utf-8', 'windows-1251', $column),
					'type' => 'B'
				);
				break;
				}
            }
        }

        $PDO = ORM::factory('Filter')->PDO();

        foreach ($properties as &$property) {
            $name = iconv('windows-1251', 'utf-8', $property['name']);
            $name = str_replace('Атрибут: ', '', $name);
            $property['name'] = iconv('utf-8', 'windows-1251', $name);

            $PDO->exec("INSERT IGNORE INTO properties (name, type) VALUES ('{$name}', '{$property['type']}')");
        }

        return $properties;
    }
}