<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2015-04-03 16:50:59 --- EMERGENCY: Kohana_Exception [ 0 ]: Installed GD does not support 00_jpg_srz images ~ MODPATH/image/classes/Kohana/Image/GD.php [ 636 ] in /home/user1167708/www/1teh.by/modules/image/classes/Kohana/Image/GD.php:548
2015-04-03 16:50:59 --- DEBUG: #0 /home/user1167708/www/1teh.by/modules/image/classes/Kohana/Image/GD.php(548): Kohana_Image_GD->_save_function('00_jpg_srz', 100)
#1 /home/user1167708/www/1teh.by/modules/image/classes/Kohana/Image.php(639): Kohana_Image_GD->_do_save('/home/user11677...', 100)
#2 /home/user1167708/www/1teh.by/modules/ariol/classes/CM/Form/Abstract.php(177): Kohana_Image->save('/home/user11677...')
#3 /home/user1167708/www/1teh.by/modules/ariol/classes/CM/Form/Abstract.php(97): CM_Form_Abstract->after_submit()
#4 /home/user1167708/www/1teh.by/modules/ariol/classes/Controller/Crud.php(216): CM_Form_Abstract->submit()
#5 /home/user1167708/www/1teh.by/modules/ariol/classes/Controller/Crud.php(202): Controller_Crud->process_form(Object(Model_Brand))
#6 /home/user1167708/www/1teh.by/system/classes/Kohana/Controller.php(84): Controller_Crud->action_edit()
#7 [internal function]: Kohana_Controller->execute()
#8 /home/user1167708/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Admin_Brand))
#9 /home/user1167708/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#10 /home/user1167708/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#11 /home/user1167708/www/1teh.by/index.php(149): Kohana_Request->execute()
#12 {main} in /home/user1167708/www/1teh.by/modules/image/classes/Kohana/Image/GD.php:548