<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2014-12-07 14:29:28 --- EMERGENCY: Database_Exception [ 2006 ]: MySQL server has gone away [ SELECT `section`.`id` AS `id`, `section`.`name` AS `name`, `section`.`url` AS `url`, `section`.`md5_url` AS `md5_url`, `section`.`active` AS `active`, `section`.`s_title` AS `s_title`, `section`.`s_description` AS `s_description`, `section`.`s_keywords` AS `s_keywords`, `section`.`description` AS `description`, `section`.`position` AS `position`, `section`.`h1` AS `h1` FROM `sections` AS `section` WHERE `section`.`active` = 1 ORDER BY `section`.`position` DESC ] ~ MODPATH/ariol/classes/Kohana/Database/MySQLi.php [ 174 ] in /home/user1167708/www/1teh.by/modules/database/classes/Kohana/Database/Query.php:251
2014-12-07 14:29:28 --- DEBUG: #0 /home/user1167708/www/1teh.by/modules/database/classes/Kohana/Database/Query.php(251): Kohana_Database_MySQLi->query(1, 'SELECT `section...', 'Model_Section', Array)
#1 /home/user1167708/www/1teh.by/modules/orm/classes/Kohana/ORM.php(1063): Kohana_Database_Query->execute(Object(Database_MySQLi))
#2 /home/user1167708/www/1teh.by/modules/orm/classes/Kohana/ORM.php(1004): Kohana_ORM->_load_result(true)
#3 /home/user1167708/www/1teh.by/application/classes/Model/Section.php(177): Kohana_ORM->find_all()
#4 /home/user1167708/www/1teh.by/application/views/layout/site/global_inner.php(85): Model_Section->fetchSortedByPosition()
#5 /home/user1167708/www/1teh.by/system/classes/Kohana/View.php(61): include('/home/user11677...')
#6 /home/user1167708/www/1teh.by/system/classes/Kohana/View.php(348): Kohana_View::capture('/home/user11677...', Array)
#7 /home/user1167708/www/1teh.by/modules/ariol/classes/Extasy/View.php(28): Kohana_View->render('layout/site/glo...')
#8 /home/user1167708/www/1teh.by/system/classes/Kohana/View.php(228): Extasy_View->render()
#9 /home/user1167708/www/1teh.by/modules/ariol/classes/Extasy/Controller.php(66): Kohana_View->__toString()
#10 /home/user1167708/www/1teh.by/modules/ariol/classes/Controller/Site.php(68): Extasy_Controller->after()
#11 /home/user1167708/www/1teh.by/system/classes/Kohana/Controller.php(87): Controller_Site->after()
#12 [internal function]: Kohana_Controller->execute()
#13 /home/user1167708/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Site_Category))
#14 /home/user1167708/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#15 /home/user1167708/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#16 /home/user1167708/www/1teh.by/index.php(131): Kohana_Request->execute()
#17 {main} in /home/user1167708/www/1teh.by/modules/database/classes/Kohana/Database/Query.php:251