<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2014-12-19 17:31:46 --- EMERGENCY: Database_Exception [ 1103 ]: Incorrect table name '' [ SELECT * FROM `` WHERE = '41' ] ~ MODPATH/ariol/classes/Kohana/Database/MySQLi.php [ 174 ] in /home/user1167708/www/1teh.by/modules/database/classes/Kohana/Database/Query.php:251
2014-12-19 17:31:46 --- DEBUG: #0 /home/user1167708/www/1teh.by/modules/database/classes/Kohana/Database/Query.php(251): Kohana_Database_MySQLi->query(1, 'SELECT * FROM `...', false, Array)
#1 /home/user1167708/www/1teh.by/modules/ariol/classes/CM/Form/Plugin/ORM/Filter/Equals.php(38): Kohana_Database_Query->execute()
#2 /home/user1167708/www/1teh.by/modules/ariol/classes/CM/Form/Abstract.php(86): CM_Form_Plugin_ORM_Filter_Equals->populate(Object(Form_Filter_Product))
#3 /home/user1167708/www/1teh.by/application/classes/Controller/Admin/Product.php(41): CM_Form_Abstract->submit()
#4 /home/user1167708/www/1teh.by/modules/ariol/classes/Controller/Crud.php(137): Controller_Admin_Product->before_fetch(Object(Model_Product))
#5 /home/user1167708/www/1teh.by/system/classes/Kohana/Controller.php(84): Controller_Crud->action_index()
#6 [internal function]: Kohana_Controller->execute()
#7 /home/user1167708/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Admin_Product))
#8 /home/user1167708/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#9 /home/user1167708/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#10 /home/user1167708/www/1teh.by/index.php(149): Kohana_Request->execute()
#11 {main} in /home/user1167708/www/1teh.by/modules/database/classes/Kohana/Database/Query.php:251
2014-12-19 17:32:31 --- EMERGENCY: Database_Exception [ 1103 ]: Incorrect table name '' [ SELECT * FROM `` WHERE = '41' ] ~ MODPATH/ariol/classes/Kohana/Database/MySQLi.php [ 174 ] in /home/user1167708/www/1teh.by/modules/database/classes/Kohana/Database/Query.php:251
2014-12-19 17:32:31 --- DEBUG: #0 /home/user1167708/www/1teh.by/modules/database/classes/Kohana/Database/Query.php(251): Kohana_Database_MySQLi->query(1, 'SELECT * FROM `...', false, Array)
#1 /home/user1167708/www/1teh.by/modules/ariol/classes/CM/Form/Plugin/ORM/Filter/Equals.php(38): Kohana_Database_Query->execute()
#2 /home/user1167708/www/1teh.by/modules/ariol/classes/CM/Form/Abstract.php(86): CM_Form_Plugin_ORM_Filter_Equals->populate(Object(Form_Filter_Product))
#3 /home/user1167708/www/1teh.by/application/classes/Controller/Admin/Product.php(41): CM_Form_Abstract->submit()
#4 /home/user1167708/www/1teh.by/modules/ariol/classes/Controller/Crud.php(137): Controller_Admin_Product->before_fetch(Object(Model_Product))
#5 /home/user1167708/www/1teh.by/system/classes/Kohana/Controller.php(84): Controller_Crud->action_index()
#6 [internal function]: Kohana_Controller->execute()
#7 /home/user1167708/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Admin_Product))
#8 /home/user1167708/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#9 /home/user1167708/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#10 /home/user1167708/www/1teh.by/index.php(149): Kohana_Request->execute()
#11 {main} in /home/user1167708/www/1teh.by/modules/database/classes/Kohana/Database/Query.php:251
2014-12-19 17:32:48 --- EMERGENCY: Database_Exception [ 1103 ]: Incorrect table name '' [ SELECT * FROM `` WHERE = '41' ] ~ MODPATH/ariol/classes/Kohana/Database/MySQLi.php [ 174 ] in /home/user1167708/www/1teh.by/modules/database/classes/Kohana/Database/Query.php:251
2014-12-19 17:32:48 --- DEBUG: #0 /home/user1167708/www/1teh.by/modules/database/classes/Kohana/Database/Query.php(251): Kohana_Database_MySQLi->query(1, 'SELECT * FROM `...', false, Array)
#1 /home/user1167708/www/1teh.by/modules/ariol/classes/CM/Form/Plugin/ORM/Filter/Equals.php(38): Kohana_Database_Query->execute()
#2 /home/user1167708/www/1teh.by/modules/ariol/classes/CM/Form/Abstract.php(86): CM_Form_Plugin_ORM_Filter_Equals->populate(Object(Form_Filter_Product))
#3 /home/user1167708/www/1teh.by/application/classes/Controller/Admin/Product.php(41): CM_Form_Abstract->submit()
#4 /home/user1167708/www/1teh.by/modules/ariol/classes/Controller/Crud.php(137): Controller_Admin_Product->before_fetch(Object(Model_Product))
#5 /home/user1167708/www/1teh.by/system/classes/Kohana/Controller.php(84): Controller_Crud->action_index()
#6 [internal function]: Kohana_Controller->execute()
#7 /home/user1167708/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Admin_Product))
#8 /home/user1167708/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#9 /home/user1167708/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#10 /home/user1167708/www/1teh.by/index.php(149): Kohana_Request->execute()
#11 {main} in /home/user1167708/www/1teh.by/modules/database/classes/Kohana/Database/Query.php:251