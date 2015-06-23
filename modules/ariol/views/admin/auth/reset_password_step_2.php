<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

?>
<?php echo Ext::form_begin(NULL, array('method' => 'POST'))?>
<?php $form->get_field('password')->set_attributes(array('style' => 'width:150px;'))?>
<?php $form->get_field('password_confirm')->set_attributes(array('style' => 'width:150px;'))?>
<?php echo $form->render()?>
<?php echo Ext::buttons_begin()?>
<?php echo Ext::submit('submit', 'Сменить пароль')?>
<?php echo Ext::buttons_end()?>
<?php echo Ext::form_end()?>