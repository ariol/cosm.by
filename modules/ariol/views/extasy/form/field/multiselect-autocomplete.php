<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

$id = $name.'_autocomplete_field';

$rows_auto = array();
foreach($rows as $cur_id => $cur_name)
{
	$rows_auto[] = '{id: "'.$cur_id.'", name: "'.str_replace('"', '\"', $cur_name).'"}';
}

?>
<?php echo form::input($name, $value, array('id'=>$id));?>
<script type="text/javascript">
$("#<?php echo $id?>").autocomplete([<?php echo join(',', $rows_auto)?>], {
	width: 300,
	multiple: true,
	matchContains: true,
	formatItem: function(row) {
		return row.name;
	},
	formatResult: function (row) {
		return row.name;
	}
});
</script>