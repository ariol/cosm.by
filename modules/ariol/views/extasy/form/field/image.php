<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */
?>
<table width="100%">
<?php if( ! empty($value)):?>
		<tr >
			<td colspan="3" class="td_main"><?php echo html::image($value);?></td>
		</tr>
		<tr>
			<td width="70%"><b>Путь:</b></td>
			<td width="30%"><b>Тип:</b></td>
			<td width="20%"><b>Размер (width:height):</b></td>
		</tr>
		<tr >
<?php 
$info = getimagesize(PUBLIC_ROOT.'/'.$value);
?>
			<td colspan="1" class="td_main"><?php echo $value?></td>
			<td ><?php echo $info[0]?>:<?php echo $info[1]?></td>
			<td ><?php echo $info['mime']?></td>
		</tr>
		<tr>
			<td class="td_main" colspan="3">
				<?php echo form::checkbox($name.'[delete]');?> удалить
			</td>
		</tr>
<?php endif;?>
		<tr>
		<td class="td_main" colspan="3"><?php echo form::file($name.'[file]', $attributes);?>
			<?php echo form::hidden($name.'[hidden]', '1');?>
		</td>
	</tr>
</table>

