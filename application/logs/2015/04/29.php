<?php defined('SYSPATH') OR die('No direct script access.'); ?>

2015-04-29 11:11:36 --- EMERGENCY: Kohana_Exception [ 0 ]: The title property does not exist in the Model_Reviews class ~ MODPATH/orm/classes/Kohana/ORM.php [ 687 ] in /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php:603
2015-04-29 11:11:36 --- DEBUG: #0 /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php(603): Kohana_ORM->get('title')
#1 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Orm.php(293): Kohana_ORM->__get('title')
#2 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Orm.php(436): Extasy_Orm->__get('title')
#3 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Grid/Column/Empty.php(12): Extasy_Orm->offsetGet('title')
#4 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Grid/Column.php(71): Extasy_Grid_Column_Empty->_field(Object(Model_Reviews))
#5 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Orm.php(550): Extasy_Grid_Column->field(Object(Model_Reviews))
#6 /home/vitaliy/www/1teh.by/modules/ariol/classes/Controller/Crud.php(176): Extasy_Orm->grid_value('title')
#7 /home/vitaliy/www/1teh.by/system/classes/Kohana/Controller.php(84): Controller_Crud->action_get_grid_data()
#8 [internal function]: Kohana_Controller->execute()
#9 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Admin_Reviews))
#10 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#11 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#12 /home/vitaliy/www/1teh.by/index.php(149): Kohana_Request->execute()
#13 {main} in /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php:603
2015-04-29 13:11:17 --- EMERGENCY: Kohana_Exception [ 0 ]: The title property does not exist in the Model_Reviews class ~ MODPATH/orm/classes/Kohana/ORM.php [ 687 ] in /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php:603
2015-04-29 13:11:17 --- DEBUG: #0 /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php(603): Kohana_ORM->get('title')
#1 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Orm.php(293): Kohana_ORM->__get('title')
#2 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Orm.php(436): Extasy_Orm->__get('title')
#3 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Grid/Column/Empty.php(12): Extasy_Orm->offsetGet('title')
#4 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Grid/Column.php(71): Extasy_Grid_Column_Empty->_field(Object(Model_Reviews))
#5 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Orm.php(550): Extasy_Grid_Column->field(Object(Model_Reviews))
#6 /home/vitaliy/www/1teh.by/modules/ariol/classes/Controller/Crud.php(176): Extasy_Orm->grid_value('title')
#7 /home/vitaliy/www/1teh.by/system/classes/Kohana/Controller.php(84): Controller_Crud->action_get_grid_data()
#8 [internal function]: Kohana_Controller->execute()
#9 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Admin_Reviews))
#10 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#11 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#12 /home/vitaliy/www/1teh.by/index.php(149): Kohana_Request->execute()
#13 {main} in /home/vitaliy/www/1teh.by/modules/orm/classes/Kohana/ORM.php:603
2015-04-29 16:30:04 --- EMERGENCY: Kohana_Exception [ 0 ]: The requested route does not exist: admin-discounts ~ SYSPATH/classes/Kohana/Route.php [ 109 ] in /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/ACL.php:39
2015-04-29 16:30:04 --- DEBUG: #0 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/ACL.php(39): Kohana_Route::get('admin-discounts')
#1 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Menu.php(72): Extasy_ACL::is_route_allowed('admin-discounts')
#2 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Menu.php(56): Extasy_Menu->render()
#3 /home/vitaliy/www/1teh.by/modules/ariol/views/layout/admin/global.php(177): Extasy_Menu->__toString()
#4 /home/vitaliy/www/1teh.by/system/classes/Kohana/View.php(61): include('/home/vitaliy/w...')
#5 /home/vitaliy/www/1teh.by/system/classes/Kohana/View.php(348): Kohana_View::capture('/home/vitaliy/w...', Array)
#6 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/View.php(28): Kohana_View->render('layout/admin/gl...')
#7 /home/vitaliy/www/1teh.by/system/classes/Kohana/View.php(228): Extasy_View->render()
#8 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Controller.php(66): Kohana_View->__toString()
#9 /home/vitaliy/www/1teh.by/system/classes/Kohana/Controller.php(87): Extasy_Controller->after()
#10 [internal function]: Kohana_Controller->execute()
#11 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Admin_Order))
#12 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#13 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#14 /home/vitaliy/www/1teh.by/index.php(149): Kohana_Request->execute()
#15 {main} in /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/ACL.php:39
2015-04-29 16:32:07 --- EMERGENCY: Kohana_Exception [ 0 ]: The requested route does not exist: admin-coupons ~ SYSPATH/classes/Kohana/Route.php [ 109 ] in /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/ACL.php:39
2015-04-29 16:32:07 --- DEBUG: #0 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/ACL.php(39): Kohana_Route::get('admin-coupons')
#1 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Menu.php(72): Extasy_ACL::is_route_allowed('admin-coupons')
#2 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Menu.php(56): Extasy_Menu->render()
#3 /home/vitaliy/www/1teh.by/modules/ariol/views/layout/admin/global.php(177): Extasy_Menu->__toString()
#4 /home/vitaliy/www/1teh.by/system/classes/Kohana/View.php(61): include('/home/vitaliy/w...')
#5 /home/vitaliy/www/1teh.by/system/classes/Kohana/View.php(348): Kohana_View::capture('/home/vitaliy/w...', Array)
#6 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/View.php(28): Kohana_View->render('layout/admin/gl...')
#7 /home/vitaliy/www/1teh.by/system/classes/Kohana/View.php(228): Extasy_View->render()
#8 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Controller.php(66): Kohana_View->__toString()
#9 /home/vitaliy/www/1teh.by/system/classes/Kohana/Controller.php(87): Extasy_Controller->after()
#10 [internal function]: Kohana_Controller->execute()
#11 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Admin_Discount))
#12 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#13 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#14 /home/vitaliy/www/1teh.by/index.php(149): Kohana_Request->execute()
#15 {main} in /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/ACL.php:39
2015-04-29 16:32:28 --- EMERGENCY: Kohana_Exception [ 0 ]: The requested route does not exist: admin-coupons ~ SYSPATH/classes/Kohana/Route.php [ 109 ] in /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/ACL.php:39
2015-04-29 16:32:28 --- DEBUG: #0 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/ACL.php(39): Kohana_Route::get('admin-coupons')
#1 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Menu.php(72): Extasy_ACL::is_route_allowed('admin-coupons')
#2 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Menu.php(56): Extasy_Menu->render()
#3 /home/vitaliy/www/1teh.by/modules/ariol/views/layout/admin/global.php(177): Extasy_Menu->__toString()
#4 /home/vitaliy/www/1teh.by/system/classes/Kohana/View.php(61): include('/home/vitaliy/w...')
#5 /home/vitaliy/www/1teh.by/system/classes/Kohana/View.php(348): Kohana_View::capture('/home/vitaliy/w...', Array)
#6 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/View.php(28): Kohana_View->render('layout/admin/gl...')
#7 /home/vitaliy/www/1teh.by/system/classes/Kohana/View.php(228): Extasy_View->render()
#8 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Controller.php(66): Kohana_View->__toString()
#9 /home/vitaliy/www/1teh.by/system/classes/Kohana/Controller.php(87): Extasy_Controller->after()
#10 [internal function]: Kohana_Controller->execute()
#11 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Admin_Discount))
#12 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#13 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#14 /home/vitaliy/www/1teh.by/index.php(149): Kohana_Request->execute()
#15 {main} in /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/ACL.php:39
2015-04-29 16:33:49 --- EMERGENCY: Kohana_Exception [ 0 ]: The requested route does not exist: site-category ~ SYSPATH/classes/Kohana/Route.php [ 109 ] in /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Url.php:52
2015-04-29 16:33:49 --- DEBUG: #0 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Url.php(52): Kohana_Route::get('site-category')
#1 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Html.php(16): Extasy_Url::url_to_route('site-category:i...')
#2 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Grid/Column/Name.php(27): Extasy_Html::link_to_route('site-category:i...', '&nbsp;&nbsp;<i ...', Array)
#3 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Grid/Column.php(71): Extasy_Grid_Column_Name->_field(Object(Model_Category))
#4 /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Orm.php(550): Extasy_Grid_Column->field(Object(Model_Category))
#5 /home/vitaliy/www/1teh.by/modules/ariol/classes/Controller/Crud.php(176): Extasy_Orm->grid_value('name')
#6 /home/vitaliy/www/1teh.by/system/classes/Kohana/Controller.php(84): Controller_Crud->action_get_grid_data()
#7 [internal function]: Kohana_Controller->execute()
#8 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client/Internal.php(97): ReflectionMethod->invoke(Object(Controller_Admin_Category))
#9 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request/Client.php(114): Kohana_Request_Client_Internal->execute_request(Object(Request), Object(Response))
#10 /home/vitaliy/www/1teh.by/system/classes/Kohana/Request.php(986): Kohana_Request_Client->execute(Object(Request))
#11 /home/vitaliy/www/1teh.by/index.php(149): Kohana_Request->execute()
#12 {main} in /home/vitaliy/www/1teh.by/modules/ariol/classes/Extasy/Url.php:52