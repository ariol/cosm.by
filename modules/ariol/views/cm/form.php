<?php echo Ext::form_fields_begin() ?>
<?php foreach ($form->get_field_names() as $name): ?>
<?php echo Ext::form_row(
	$form->get_field($name)->render(),
	form::label($name, $form->get_field($name)->get_label()),
	$form->get_error($name)
	? form::label($name, $form->get_error($name), array('class' => 'error'))
	: ''
) ?>
<?php endforeach ?>
<?php echo Ext::form_fields_end() ?>