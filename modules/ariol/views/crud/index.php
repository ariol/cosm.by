<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */
?>
<div id="controller" style="display: none;"><?= trim(Request::initial()->controller()); ?></div>
<?php if ($filter_form):?>
<?php echo Ext::form_begin(NULL, array('method' => 'GET')) ?>
<?php echo $filter_form->render()?>
<?php echo Ext::buttons_begin()?>
<p>
	<?php echo Ext::submit('filter', 'Фильтр', null, array('class' => 'btn-primary'))?>
	<?php echo Ext::submit('filter_cancel', 'Очистить', null, array('class' => 'btn-primary'))?>
</p>
<?php echo Ext::buttons_end()?>
<?php echo Ext::form_end() ?>
<?php endif;?>
<?php echo Navigation::instance()->actions()?>
<?php echo $grid; ?>