<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */
?>
<?php echo Navigation::instance()->actions()?>
<?php echo Ext::form_begin(NULL, array('method' => 'GET'))?>
<?php echo $filter_form->render()?>
<?php echo Ext::buttons_begin()?>
<p>
    <?php echo Ext::submit('filter', 'Фильтр')?>
    <?php echo Ext::submit('cancel_filter', 'Очистить', NULL, array('ResetButton' => true))?>
</p>
<?php echo Ext::buttons_end()?>
<?php echo Ext::form_end()?>
<?php echo $grid?>