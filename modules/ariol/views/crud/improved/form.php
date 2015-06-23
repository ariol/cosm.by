<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */
?>
<?php echo Ext::form_begin(NULL, array('method' => 'POST'))?>
<?php echo form::hidden('return_location', $return_location)?>
<?php echo $form->render()?>
<?php echo Ext::buttons_begin()?>
<p><?php echo Ext::submit('submit', 'Сохранить')?> <?php echo Ext::submit('cancel', 'Отмена')?></p>
<?php echo Ext::buttons_end()?>
<?php echo Ext::form_end()?>