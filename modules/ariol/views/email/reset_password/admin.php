<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

?>
Чтобы сбросить свой пароль, перейдите по следующей ссылке:

<?php echo URL::base(FALSE, 'http').Extasy_Url::url_to_route('admin-auth:reset_password_step_2?code='.$code)?>