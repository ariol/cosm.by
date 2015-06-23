<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */
?>
<?php echo Ext::form_begin(NULL, array('method' => 'POST'))?>
<?php echo $form->render()?>
<?php if ($write_allowed):?>
<?php echo Ext::spacer()?>
<?php echo Ext::buttons_begin()?>
<p><?php echo Ext::submit('submit', '<i class="fa fa-save"></i> Сохранить', null, array('class'=>'btn btn-success'))?> <?php echo Ext::submit('cancel', '<i class="fa fa-times-circle"></i> Отмена', null, array('class'=>'btn btn-danger'))?></p>
<?php echo Ext::buttons_end()?>
<?php endif;?>
<?php echo Ext::form_end()?>