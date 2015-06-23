<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

?>
<?php echo Ext::form_fields_begin() ?>
	<ul class="nav nav-tabs" id="myTabs">
		<?php $i = 0; ?>
		<?php foreach ($fieldgroups as $fieldgroup_name => $fieldgroup): ?>
			<li <? if (!$i) { ?>class="active"<? } ?>><a id="<?= Text::filename(Text::translit($fieldgroup_name)); ?>_tab" href="#<?= Text::filename(Text::translit($fieldgroup_name)); ?>"><?= $fieldgroup_name; ?></a></li>
			<?php $i++; ?>
		<?php endforeach; ?>
	</ul>
	<div class="tab-content" style="overflow: visible;">
		<?php $i = 0; ?>
		<?php foreach ($fieldgroups as $fieldgroup_name => $fieldgroup): ?>
			<div class="tab-pane<? if (!$i) { ?> active<? } ?>" id="<?= Text::filename(Text::translit($fieldgroup_name)); ?>">
				<?php foreach ($fieldgroup as $name => $field):?>
					<?php echo Ext::form_row(
						$field->render(),
						Form::label($name, $field->get_label()),
						$form->get_error($name)
							? Form::label($name, $form->get_error($name), array('class' => 'error'))
							: ''
					) ?>
				<?php endforeach;?>
			</div>
			<?php $i++; ?>
		<?php endforeach;?>
	</div>
<?php echo Ext::form_fields_end() ?>

<div style="height: 15px;"></div>

<script type="text/javascript">
	$(document).ready(function(){
		$(window.location.hash + "_tab").tab('show');
	})
	$('#myTabs a').click(function (e) {
		e.preventDefault();
		$(this).tab('show');
	});
</script>