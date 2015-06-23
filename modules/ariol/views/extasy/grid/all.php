<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

?>
<?php echo Ext::form_begin()?>
<?php echo Form::hidden('group_actions', 1)?>
<table class="table table-striped table-hover table-condensed table-bordered" id="dataTable">
	<thead>
		<th width="10">
			<input type="hidden" name="ids_all" value="0"/>
			<input type="checkbox" class="checkbox" name="ids_all" autocomplete="off" value="1"/>
		</th>
		<?php foreach($columns as $column):?>
			<?php echo $column?>
		<?php endforeach;?>
	</thead>
	<tbody>
	</tbody>
</table>
<?php if ( ! empty($group_actions)):?>
	<?php echo Ext::spacer()?>
	<?php echo Ext::buttons_begin()?>
	<?php foreach($group_actions as $name => $action):?>
		<?php $attributes = array(); ?>
		<?php if(isset($action['class'])) { ?>
			<?php $attributes = array('class' => $action['class']); ?>
		<?php } ?>
		<?php $attributes['class'] .= ' btn'; ?>
		<?php echo ext::submit('action['.$name.']', $action['title'], arr::get($action, 'confirm'), $attributes); ?>
		<?php echo Form::submit('action['.$name.']', $action['title'], array('style' => 'display: none;')); ?>
	<?php endforeach;?>
	<?php echo Ext::buttons_end()?>
<?php endif;?>
<?php echo Ext::form_end()?>