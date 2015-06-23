<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2015-05-31 18:30:15 --- EMERGENCY: CM_Fieldschema_Exception [ 0 ]: Unknown field s_title ~ MODPATH/ariol/classes/CM/Fieldschema.php [ 91 ] in /home/vitaliy/www/1teh.by/modules/ariol/classes/CM/Fieldschema.php:29
2015-05-31 18:30:15 --- DEBUG: #0 /home/vitaliy/www/1teh.by/modules/ariol/classes/CM/Fieldschema.php(29): CM_Fieldschema->assert_has_field('s_title')
#1 /home/vitaliy/www/1teh.by/modules/ariol/classes/CM/Form/Abstract.php(388): CM_Fieldschema->get_field('s_title')
#2 /home/vitaliy/www/1teh.by/modules/page/classes/CM/Form/Plugin/ORM/Autocomplete.php(20): CM_Form_Abstract->get_field('s_title')
#3 /home/vitaliy/www/1teh.by/modules/ariol/classes/CM/Form/Abstract.php(100): CM_Form_Plugin_ORM_Autocomplete->after_submit(Object(Form_Admin_Brand))
#4 /home/vitaliy/www/1teh.by/modules/ariol/classes/Controller/Crud.php(216): CM_Form_Abstract->submit()
#5 /home/vitaliy/www/1teh.by/modules/ariol/classes/Controller/Crud.php(202): Controller_Crud->process_form(Object(Model_Brand))
#6 /home/vitaliy/www/1teh.by/system/classes/Kohana/Controller.php(84): Controller_Crud->action_edit()
#7 [internal function]: Kohana_Controller->execute()
#8 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Admin_Brand))
#9 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#10 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#11 /home/vitaliy/www/1teh.by/index.php(149): Kohana_Request->execute()
#12 {main} in /home/vitaliy/www/1teh.by/modules/ariol/classes/CM/Fieldschema.php:29