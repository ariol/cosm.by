<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2015-06-26 00:37:02 --- EMERGENCY: Kohana_Exception [ 0 ]: The name property does not exist in the Model_Filter class ~ MODPATH/orm/classes/Kohana/ORM.php [ 760 ] in /home/cosm.by/modules/orm/classes/Kohana/ORM.php:702
2015-06-26 00:37:02 --- DEBUG: #0 /home/cosm.by/modules/orm/classes/Kohana/ORM.php(702): Kohana_ORM->set('name', 'FRESH LOOK EXCL...')
#1 /home/cosm.by/modules/ariol/classes/Extasy/Orm.php(351): Kohana_ORM->__set('name', 'FRESH LOOK EXCL...')
#2 /home/cosm.by/modules/ariol/classes/CM/Form/Plugin/ORM.php(49): Extasy_Orm->__set('name', 'FRESH LOOK EXCL...')
#3 /home/cosm.by/modules/ariol/classes/CM/Form/Abstract.php(86): CM_Form_Plugin_Orm->populate(Object(Form_Admin_Filter))
#4 /home/cosm.by/modules/ariol/classes/Controller/Crud.php(216): CM_Form_Abstract->submit()
#5 /home/cosm.by/modules/ariol/classes/Controller/Crud.php(202): Controller_Crud->process_form(Object(Model_Filter))
#6 /home/cosm.by/system/classes/Kohana/Controller.php(84): Controller_Crud->action_edit()
#7 [internal function]: Kohana_Controller->execute()
#8 /home/cosm.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Admin_Filter))
#9 /home/cosm.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#10 /home/cosm.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#11 /home/cosm.by/index.php(149): Kohana_Request->execute()
#12 {main} in /home/cosm.by/modules/orm/classes/Kohana/ORM.php:702
2015-06-26 00:37:56 --- EMERGENCY: Kohana_Exception [ 0 ]: The name property does not exist in the Model_Filter class ~ MODPATH/orm/classes/Kohana/ORM.php [ 760 ] in /home/cosm.by/modules/orm/classes/Kohana/ORM.php:702
2015-06-26 00:37:56 --- DEBUG: #0 /home/cosm.by/modules/orm/classes/Kohana/ORM.php(702): Kohana_ORM->set('name', 'FRESH LOOK EXCL...')
#1 /home/cosm.by/modules/ariol/classes/Extasy/Orm.php(351): Kohana_ORM->__set('name', 'FRESH LOOK EXCL...')
#2 /home/cosm.by/modules/ariol/classes/CM/Form/Plugin/ORM.php(49): Extasy_Orm->__set('name', 'FRESH LOOK EXCL...')
#3 /home/cosm.by/modules/ariol/classes/CM/Form/Abstract.php(86): CM_Form_Plugin_Orm->populate(Object(Form_Admin_Filter))
#4 /home/cosm.by/modules/ariol/classes/Controller/Crud.php(216): CM_Form_Abstract->submit()
#5 /home/cosm.by/modules/ariol/classes/Controller/Crud.php(202): Controller_Crud->process_form(Object(Model_Filter))
#6 /home/cosm.by/system/classes/Kohana/Controller.php(84): Controller_Crud->action_edit()
#7 [internal function]: Kohana_Controller->execute()
#8 /home/cosm.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Admin_Filter))
#9 /home/cosm.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#10 /home/cosm.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#11 /home/cosm.by/index.php(149): Kohana_Request->execute()
#12 {main} in /home/cosm.by/modules/orm/classes/Kohana/ORM.php:702
2015-06-26 12:12:25 --- EMERGENCY: View_Exception [ 0 ]: The requested view site/global_inner could not be found ~ SYSPATH/classes/Kohana/View.php [ 257 ] in /home/cosm.by/modules/ariol/classes/Extasy/View.php:97
2015-06-26 12:12:25 --- DEBUG: #0 /home/cosm.by/modules/ariol/classes/Extasy/View.php(97): Kohana_View->set_filename('site/global_inn...')
#1 /home/cosm.by/system/classes/Kohana/View.php(339): Extasy_View->set_filename('site/global_inn...')
#2 /home/cosm.by/modules/ariol/classes/Extasy/View.php(28): Kohana_View->render('site/global_inn...')
#3 /home/cosm.by/system/classes/Kohana/View.php(228): Extasy_View->render()
#4 /home/cosm.by/modules/ariol/classes/Extasy/Controller.php(66): Kohana_View->__toString()
#5 /home/cosm.by/modules/ariol/classes/Controller/Site.php(89): Extasy_Controller->after()
#6 /home/cosm.by/system/classes/Kohana/Controller.php(87): Controller_Site->after()
#7 [internal function]: Kohana_Controller->execute()
#8 /home/cosm.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Site_Contacts))
#9 /home/cosm.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#10 /home/cosm.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#11 /home/cosm.by/index.php(149): Kohana_Request->execute()
#12 {main} in /home/cosm.by/modules/ariol/classes/Extasy/View.php:97