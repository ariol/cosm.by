<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2015-06-11 11:17:36 --- EMERGENCY: Kohana_Exception [ 0 ]: The video property does not exist in the Model_Category class ~ MODPATH/orm/classes/Kohana/ORM.php [ 687 ] in /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php:603
2015-06-11 11:17:36 --- DEBUG: #0 /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php(603): Kohana_ORM->get('video')
#1 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Orm.php(293): Kohana_ORM->__get('video')
#2 /home/vitaliy/www/1teh.by/modules/ariol/classes/CM/Form/Plugin/ORM.php(38): Extasy_Orm->__get('video')
#3 /home/vitaliy/www/1teh.by/modules/ariol/classes/CM/Form/Abstract.php(34): CM_Form_Plugin_Orm->construct_form(Object(Form_Admin_Category), Object(Model_Category))
#4 /home/vitaliy/www/1teh.by/application/classes/Model/Category.php(124): CM_Form_Abstract->__construct(Object(Model_Category))
#5 /home/vitaliy/www/1teh.by/modules/ariol/classes/Controller/Crud.php(214): Model_Category->form()
#6 /home/vitaliy/www/1teh.by/modules/ariol/classes/Controller/Crud.php(190): Controller_Crud->process_form(Object(Model_Category))
#7 /home/vitaliy/www/1teh.by/system/classes/Kohana/Controller.php(84): Controller_Crud->action_create()
#8 [internal function]: Kohana_Controller->execute()
#9 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Admin_Category))
#10 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#11 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#12 /home/vitaliy/www/1teh.by/index.php(149): Kohana_Request->execute()
#13 {main} in /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php:603
2015-06-11 17:29:06 --- EMERGENCY: Session_Exception [ 1 ]: Error reading session data. ~ SYSPATH/classes/Kohana/Session.php [ 324 ] in /home/cosm.by/system/classes/Kohana/Session.php:125
2015-06-11 17:29:06 --- DEBUG: #0 /home/cosm.by/system/classes/Kohana/Session.php(125): Kohana_Session->read(NULL)
#1 /home/cosm.by/system/classes/Kohana/Session.php(54): Kohana_Session->__construct(Array, NULL)
#2 /home/cosm.by/modules/ariol/classes/Kohana/Auth.php(58): Kohana_Session::instance('native')
#3 /home/cosm.by/modules/ariol/classes/Kohana/Auth.php(37): Kohana_Auth->__construct(Object(Config_Group))
#4 /home/cosm.by/modules/ariol/classes/Extasy/ACL.php(63): Kohana_Auth::instance()
#5 /home/cosm.by/modules/ariol/classes/ACL.php(17): Extasy_ACL->__construct()
#6 /home/cosm.by/modules/ariol/classes/Extasy/ACL.php(16): ACL::instance()
#7 /home/cosm.by/modules/ariol/classes/Extasy/Controller/Auth.php(25): Extasy_ACL::is_action_allowed('Admin', 'Order', 'change_order')
#8 /home/cosm.by/modules/ariol/classes/Controller/Crud.php(72): Extasy_Controller_Auth->before()
#9 /home/cosm.by/system/classes/Kohana/Controller.php(69): Controller_Crud->before()
#10 [internal function]: Kohana_Controller->execute()
#11 /home/cosm.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Admin_Order))
#12 /home/cosm.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#13 /home/cosm.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#14 /home/cosm.by/index.php(149): Kohana_Request->execute()
#15 {main} in /home/cosm.by/system/classes/Kohana/Session.php:125
2015-06-11 18:18:41 --- EMERGENCY: Session_Exception [ 1 ]: Error reading session data. ~ SYSPATH/classes/Kohana/Session.php [ 324 ] in /home/cosm.by/system/classes/Kohana/Session.php:125
2015-06-11 18:18:41 --- DEBUG: #0 /home/cosm.by/system/classes/Kohana/Session.php(125): Kohana_Session->read(NULL)
#1 /home/cosm.by/system/classes/Kohana/Session.php(54): Kohana_Session->__construct(Array, NULL)
#2 /home/cosm.by/modules/ariol/classes/Kohana/Auth.php(58): Kohana_Session::instance('native')
#3 /home/cosm.by/modules/ariol/classes/Kohana/Auth.php(37): Kohana_Auth->__construct(Object(Config_Group))
#4 /home/cosm.by/modules/ariol/classes/Extasy/ACL.php(63): Kohana_Auth::instance()
#5 /home/cosm.by/modules/ariol/classes/ACL.php(17): Extasy_ACL->__construct()
#6 /home/cosm.by/modules/ariol/classes/Extasy/ACL.php(16): ACL::instance()
#7 /home/cosm.by/modules/ariol/classes/Extasy/Controller/Auth.php(25): Extasy_ACL::is_action_allowed('Admin', 'Order', 'empty_cart')
#8 /home/cosm.by/modules/ariol/classes/Controller/Crud.php(72): Extasy_Controller_Auth->before()
#9 /home/cosm.by/system/classes/Kohana/Controller.php(69): Controller_Crud->before()
#10 [internal function]: Kohana_Controller->execute()
#11 /home/cosm.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Admin_Order))
#12 /home/cosm.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#13 /home/cosm.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#14 /home/cosm.by/index.php(149): Kohana_Request->execute()
#15 {main} in /home/cosm.by/system/classes/Kohana/Session.php:125