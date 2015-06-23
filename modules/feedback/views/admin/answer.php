<?php echo Ext::form_begin();?>

<?php echo $form->render()?>

<?php echo Ext::buttons_begin();?>
<?php echo Ext::submit('submit', 'Ответить', 'Вы уверены?');?>
<?php echo Ext::buttons_end();?>

<?php echo Ext::form_end();?>