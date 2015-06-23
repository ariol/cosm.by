<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2015-03-31 16:37:25 --- EMERGENCY: Database_Exception [ 2006 ]: MySQL server has gone away [ SELECT `section`.`id` AS `id`, `section`.`name` AS `name`, `section`.`url` AS `url`, `section`.`md5_url` AS `md5_url`, `section`.`active` AS `active`, `section`.`s_title` AS `s_title`, `section`.`s_description` AS `s_description`, `section`.`s_keywords` AS `s_keywords`, `section`.`description` AS `description`, `section`.`position` AS `position`, `section`.`h1` AS `h1`, `section`.`updated_at` AS `updated_at` FROM `sections` AS `section` WHERE `active` = '1' AND `id` = '1' LIMIT 1 ] ~ MODPATH/ariol/classes/Kohana/Database/MySQLi.php [ 174 ] in /home/user1167708/www/1teh.by/modules/database/classes/Kohana/Database/Query.php:251
2015-03-31 16:37:25 --- DEBUG: #0 /home/user1167708/www/1teh.by/modules/database/classes/Kohana/Database/Query.php(251): Kohana_Database_MySQLi->query(1, 'SELECT `section...', false, Array)
#1 /home/user1167708/www/1teh.by/modules/orm/classes/Kohana/ORM.php(1077): Kohana_Database_Query->execute(Object(Database_MySQLi))
#2 /home/user1167708/www/1teh.by/modules/orm/classes/Kohana/ORM.php(979): Kohana_ORM->_load_result(false)
#3 /home/user1167708/www/1teh.by/application/classes/Model/Section.php(200): Kohana_ORM->find()
#4 /home/user1167708/www/1teh.by/application/classes/Controller/Site/Category.php(97): Model_Section->fetch_section_by_id('1')
#5 /home/user1167708/www/1teh.by/system/classes/Kohana/Controller.php(84): Controller_Site_Category->action_index()
#6 [internal function]: Kohana_Controller->execute()
#7 /home/user1167708/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Site_Category))
#8 /home/user1167708/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#9 /home/user1167708/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#10 /home/user1167708/www/1teh.by/index.php(149): Kohana_Request->execute()
#11 {main} in /home/user1167708/www/1teh.by/modules/database/classes/Kohana/Database/Query.php:251