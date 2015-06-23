<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */
?>
<?php $num = 0;?>
<?php foreach($data as $row):?>
<?php
if (isset($row->level) && isset($row->parent_id)) {
	for ($i = 1; $i < $row->level; $i++) {
		$row->name = '> ' . $row->name;
	}
}
?>
<?php echo ext::table_row_begin()?>
<td><?php echo form::checkbox('ids[]', $row['id'], FALSE)?></td>
<?php foreach($columns as $column):?>
	<?php echo $column->field($row)?>
<?php endforeach;?>
<?php echo ext::table_row_end()?>
<?php echo $row->children->grid()->data()?>
<?php endforeach;?>