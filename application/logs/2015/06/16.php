<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2015-06-16 10:56:59 --- EMERGENCY: View_Exception [ 0 ]: The requested view admin/statistics/pagination could not be found ~ SYSPATH/classes/Kohana/View.php [ 257 ] in /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/View.php:97
2015-06-16 10:56:59 --- DEBUG: #0 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/View.php(97): Kohana_View->set_filename('admin/statistic...')
#1 /home/vitaliy/www/1teh.by/system/classes/Kohana/View.php(137): Extasy_View->set_filename('admin/statistic...')
#2 /home/vitaliy/www/1teh.by/system/classes/Kohana/View.php(30): Kohana_View->__construct('admin/statistic...', Array)
#3 /home/vitaliy/www/1teh.by/application/classes/Controller/Admin/Statistics.php(76): Kohana_View::factory('admin/statistic...', Array)
#4 /home/vitaliy/www/1teh.by/system/classes/Kohana/Controller.php(84): Controller_Admin_Statistics->action_clients_summ()
#5 [internal function]: Kohana_Controller->execute()
#6 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Admin_Statistics))
#7 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#8 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#9 /home/vitaliy/www/1teh.by/index.php(149): Kohana_Request->execute()
#10 {main} in /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/View.php:97
2015-06-16 12:19:13 --- EMERGENCY: Database_Exception [ 2006 ]: MySQL server has gone away [ SHOW FULL COLUMNS FROM `categories` ] ~ MODPATH/ariol/classes/Kohana/Database/MySQLi.php [ 174 ] in /home/vitaliy/www/1teh.by/modules/ariol/classes/Kohana/Database/MySQLi.php:338
2015-06-16 12:19:13 --- DEBUG: #0 /home/vitaliy/www/1teh.by/modules/ariol/classes/Kohana/Database/MySQLi.php(338): Kohana_Database_MySQLi->query(1, 'SHOW FULL COLUM...', false)
#1 /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php(1678): Kohana_Database_MySQLi->list_columns('categories')
#2 /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php(444): Kohana_ORM->list_columns()
#3 /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php(389): Kohana_ORM->reload_columns()
#4 /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php(254): Kohana_ORM->_initialize()
#5 /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php(46): Kohana_ORM->__construct(NULL)
#6 /home/vitaliy/www/1teh.by/application/views/site/cart/index.php(53): Kohana_ORM::factory('Category')
#7 /home/vitaliy/www/1teh.by/system/classes/Kohana/View.php(61): include('/home/vitaliy/w...')
#8 /home/vitaliy/www/1teh.by/system/classes/Kohana/View.php(348): Kohana_View::capture('/home/vitaliy/w...', Array)
#9 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/View.php(27): Kohana_View->render(NULL)
#10 /home/vitaliy/www/1teh.by/system/classes/Kohana/View.php(228): Extasy_View->render()
#11 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Controller.php(66): Kohana_View->__toString()
#12 /home/vitaliy/www/1teh.by/modules/ariol/classes/Controller/Site.php(89): Extasy_Controller->after()
#13 /home/vitaliy/www/1teh.by/system/classes/Kohana/Controller.php(87): Controller_Site->after()
#14 [internal function]: Kohana_Controller->execute()
#15 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Site_Cart))
#16 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#17 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#18 /home/vitaliy/www/1teh.by/index.php(149): Kohana_Request->execute()
#19 {main} in /home/vitaliy/www/1teh.by/modules/ariol/classes/Kohana/Database/MySQLi.php:338
2015-06-16 12:19:13 --- EMERGENCY: Session_Exception [ 1 ]: Error reading session data. ~ SYSPATH/classes/Kohana/Session.php [ 324 ] in /home/vitaliy/www/1teh.by/system/classes/Kohana/Session.php:125
2015-06-16 12:19:13 --- DEBUG: #0 /home/vitaliy/www/1teh.by/system/classes/Kohana/Session.php(125): Kohana_Session->read(NULL)
#1 /home/vitaliy/www/1teh.by/system/classes/Kohana/Session.php(54): Kohana_Session->__construct(Array, NULL)
#2 /home/vitaliy/www/1teh.by/application/classes/Controller/Site/Index.php(9): Kohana_Session::instance()
#3 /home/vitaliy/www/1teh.by/system/classes/Kohana/Controller.php(84): Controller_Site_Index->action_index()
#4 [internal function]: Kohana_Controller->execute()
#5 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Site_Index))
#6 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#7 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#8 /home/vitaliy/www/1teh.by/index.php(149): Kohana_Request->execute()
#9 {main} in /home/vitaliy/www/1teh.by/system/classes/Kohana/Session.php:125