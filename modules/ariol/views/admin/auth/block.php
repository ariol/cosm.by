<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */
?>
    <ul class="nav pull-right">
        <li class="dropdown">
<?php if($is_logged):?>
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Привет, <?php echo $username?> <b class="caret"></b></a>
            <ul class="dropdown-menu">
                <li><?php echo Extasy_Html::link_to_route('admin-auth:change_password', 'Изменить пароль')?></li>
                <li><?php echo Extasy_Html::link_to_route('admin-auth:logout', 'Выйти')?></li>
            </ul>
<?php else:?>
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Привет, Гость <b class="caret"></b></a>
    <ul class="dropdown-menu">
        <li><?php echo Extasy_Html::link_to_route('admin-auth:login', 'Войти')?></li>
        <li><?php echo Extasy_Html::link_to_route('admin-auth:reset_password_step_1', 'Забыл пароль')?></li>
    </ul>
<?php endif;?>
        </li>
    </ul>
