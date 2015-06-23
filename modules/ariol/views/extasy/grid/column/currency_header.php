<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

?>
<th style="text-align: right;"<?php if( ! is_null($width)){echo 'width="'.$width.'"';}?>>
<?php if($orderable):?>
	<?php if($order_by):?>
		<?php echo html::anchor(
			URL::site(Request::instance()->uri).URL::query(array(
				'order_by' => $name,
				'order_direction' => $order_by == 'ASC' ? 'DESC' : 'ASC'
			)),
			$header .'&nbsp;'. ($order_by == 'DESC' ? '&uArr;' : '&dArr;')
		)?>
	<?php else:?>
		<?php echo html::anchor(
			URL::site(Request::instance()->uri).URL::query(array('order_by' => $name, 'order_direction' => 'ASC')),
			$header
		)?>
	<?php endif;?>
<?php else:?>
<b><?php echo $header?></b>
<?php endif;?>
</th>