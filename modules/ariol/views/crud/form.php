<?php defined('SYSPATH') or die('No direct script access.');?>
<?php echo Ext::form_begin(NULL, array('method' => 'POST'))?>
<?php echo form::hidden('return_location', $return_location)?>
<?php echo $form->render()?>
<?php echo Ext::buttons_begin()?>
<p><?php echo Ext::submit('submit', '<i class="fa fa-save"></i> Сохранить', null, array('class'=>'btn-success'))?> <?php echo Ext::submit('cancel', '<i class="fa fa-times-circle"></i> Отмена', null, array('class'=>'btn-danger'))?></p>
<?php echo Ext::buttons_end()?>
<?php echo Ext::form_end()?>
