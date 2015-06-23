<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2015-05-29 17:28:40 --- EMERGENCY: Database_Exception [ 1146 ]: Table 'chocolate.statisticses' doesn't exist [ SHOW FULL COLUMNS FROM `statisticses` ] ~ MODPATH/ariol/classes/Kohana/Database/MySQLi.php [ 174 ] in /home/vitaliy/www/1teh.by/modules/ariol/classes/Kohana/Database/MySQLi.php:338
2015-05-29 17:28:40 --- DEBUG: #0 /home/vitaliy/www/1teh.by/modules/ariol/classes/Kohana/Database/MySQLi.php(338): Kohana_Database_MySQLi->query(1, 'SHOW FULL COLUM...', false)
#1 /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php(1678): Kohana_Database_MySQLi->list_columns('statisticses')
#2 /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php(444): Kohana_ORM->list_columns()
#3 /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php(389): Kohana_ORM->reload_columns()
#4 /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php(254): Kohana_ORM->_initialize()
#5 /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php(46): Kohana_ORM->__construct(NULL)
#6 /home/vitaliy/www/1teh.by/modules/ariol/classes/Controller/Crud.php(123): Kohana_ORM::factory('Statistics')
#7 /home/vitaliy/www/1teh.by/system/classes/Kohana/Controller.php(84): Controller_Crud->action_index()
#8 [internal function]: Kohana_Controller->execute()
#9 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Admin_Statistics))
#10 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#11 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#12 /home/vitaliy/www/1teh.by/index.php(149): Kohana_Request->execute()
#13 {main} in /home/vitaliy/www/1teh.by/modules/ariol/classes/Kohana/Database/MySQLi.php:338