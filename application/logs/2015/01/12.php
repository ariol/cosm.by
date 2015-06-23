<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2015-01-12 12:26:19 --- EMERGENCY: Kohana_Exception [ 0 ]: The name property does not exist in the Model_Filter class ~ MODPATH/orm/classes/Kohana/ORM.php [ 760 ] in /home/user1167708/www/1teh.by/modules/orm/classes/Kohana/ORM.php:702
2015-01-12 12:26:19 --- DEBUG: #0 /home/user1167708/www/1teh.by/modules/orm/classes/Kohana/ORM.php(702): Kohana_ORM->set('name', '\xD0\x94\xD0\xB8\xD1\x81\xD0\xBF\xD0\xBB\xD0\xB5\xD0\xB9 ...')
#1 /home/user1167708/www/1teh.by/modules/ariol/classes/Extasy/Orm.php(351): Kohana_ORM->__set('name', '\xD0\x94\xD0\xB8\xD1\x81\xD0\xBF\xD0\xBB\xD0\xB5\xD0\xB9 ...')
#2 /home/user1167708/www/1teh.by/modules/ariol/classes/CM/Form/Plugin/ORM.php(49): Extasy_Orm->__set('name', '\xD0\x94\xD0\xB8\xD1\x81\xD0\xBF\xD0\xBB\xD0\xB5\xD0\xB9 ...')
#3 /home/user1167708/www/1teh.by/modules/ariol/classes/CM/Form/Abstract.php(86): CM_Form_Plugin_Orm->populate(Object(Form_Admin_Filter))
#4 /home/user1167708/www/1teh.by/modules/ariol/classes/Controller/Crud.php(216): CM_Form_Abstract->submit()
#5 /home/user1167708/www/1teh.by/modules/ariol/classes/Controller/Crud.php(202): Controller_Crud->process_form(Object(Model_Filter))
#6 /home/user1167708/www/1teh.by/system/classes/Kohana/Controller.php(84): Controller_Crud->action_edit()
#7 [internal function]: Kohana_Controller->execute()
#8 /home/user1167708/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Admin_Filter))
#9 /home/user1167708/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#10 /home/user1167708/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#11 /home/user1167708/www/1teh.by/index.php(149): Kohana_Request->execute()
#12 {main} in /home/user1167708/www/1teh.by/modules/orm/classes/Kohana/ORM.php:702