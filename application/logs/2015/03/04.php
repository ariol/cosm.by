<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2015-03-04 16:18:51 --- EMERGENCY: Database_Exception [ 2006 ]: MySQL server has gone away [ SELECT `section`.`id` AS `id`, `section`.`name` AS `name`, `section`.`url` AS `url`, `section`.`md5_url` AS `md5_url`, `section`.`active` AS `active`, `section`.`s_title` AS `s_title`, `section`.`s_description` AS `s_description`, `section`.`s_keywords` AS `s_keywords`, `section`.`description` AS `description`, `section`.`position` AS `position`, `section`.`h1` AS `h1`, `section`.`updated_at` AS `updated_at` FROM `sections` AS `section` WHERE `active` = '1' AND `id` = '2' LIMIT 1 ] ~ MODPATH/ariol/classes/Kohana/Database/MySQLi.php [ 174 ] in /home/user1167708/www/1teh.by/modules/database/classes/Kohana/Database/Query.php:251
2015-03-04 16:18:51 --- DEBUG: #0 /home/user1167708/www/1teh.by/modules/database/classes/Kohana/Database/Query.php(251): Kohana_Database_MySQLi->query(1, 'SELECT `section...', false, Array)
#1 /home/user1167708/www/1teh.by/modules/orm/classes/Kohana/ORM.php(1077): Kohana_Database_Query->execute(Object(Database_MySQLi))
#2 /home/user1167708/www/1teh.by/modules/orm/classes/Kohana/ORM.php(979): Kohana_ORM->_load_result(false)
#3 /home/user1167708/www/1teh.by/application/classes/Model/Section.php(200): Kohana_ORM->find()
#4 /home/user1167708/www/1teh.by/application/views/site/product/index.php(5): Model_Section->fetch_section_by_id('2')
#5 /home/user1167708/www/1teh.by/system/classes/Kohana/View.php(61): include('/home/user11677...')
#6 /home/user1167708/www/1teh.by/system/classes/Kohana/View.php(348): Kohana_View::capture('/home/user11677...', Array)
#7 /home/user1167708/www/1teh.by/modules/ariol/classes/Extasy/View.php(27): Kohana_View->render(NULL)
#8 /home/user1167708/www/1teh.by/system/classes/Kohana/View.php(228): Extasy_View->render()
#9 /home/user1167708/www/1teh.by/modules/ariol/classes/Extasy/Controller.php(66): Kohana_View->__toString()
#10 /home/user1167708/www/1teh.by/modules/ariol/classes/Controller/Site.php(83): Extasy_Controller->after()
#11 /home/user1167708/www/1teh.by/system/classes/Kohana/Controller.php(87): Controller_Site->after()
#12 [internal function]: Kohana_Controller->execute()
#13 /home/user1167708/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Site_Product))
#14 /home/user1167708/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#15 /home/user1167708/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#16 /home/user1167708/www/1teh.by/index.php(149): Kohana_Request->execute()
#17 {main} in /home/user1167708/www/1teh.by/modules/database/classes/Kohana/Database/Query.php:251
2015-03-04 16:19:22 --- EMERGENCY: PDOException [ 1203 ]: SQLSTATE[42000] [1203] User user1167708_1teh already has more than 'max_user_connections' active connections ~ MODPATH/ariol/classes/Extasy/Orm.php [ 27 ] in /home/user1167708/www/1teh.by/modules/ariol/classes/Extasy/Orm.php:27
2015-03-04 16:19:22 --- DEBUG: #0 /home/user1167708/www/1teh.by/modules/ariol/classes/Extasy/Orm.php(27): PDO->__construct('mysql:host=loca...', 'user1167708_1te...', 'VLllVkwF', Array)
#1 /home/user1167708/www/1teh.by/modules/ariol/classes/Extasy/Orm.php(35): Extasy_Orm->PDO()
#2 /home/user1167708/www/1teh.by/application/classes/Controller/Site/Category.php(309): Extasy_Orm->query('SELECT SQL_CALC...')
#3 /home/user1167708/www/1teh.by/system/classes/Kohana/Controller.php(84): Controller_Site_Category->action_index()
#4 [internal function]: Kohana_Controller->execute()
#5 /home/user1167708/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Site_Category))
#6 /home/user1167708/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#7 /home/user1167708/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#8 /home/user1167708/www/1teh.by/index.php(149): Kohana_Request->execute()
#9 {main} in /home/user1167708/www/1teh.by/modules/ariol/classes/Extasy/Orm.php:27