		<p>В интернет-магазине <?=$_SERVER['SERVER_NAME']?> <?=date("d F Y");?> в <?=date("H:i:s");?> был создан заказ.</p>
		<p><strong>Имя: </strong><?=$name;?></p>
		<?php if ($email) { ?><p><strong>Email: </strong><?=$email;?></p><?php } ?>
		<p><strong>Телефон: </strong><?=$phone;?></p>
		<p><strong>Адрес: </strong><?=$adress;?></p>
		<p><strong>Комментарий: </strong><?=$comments;?></p>
		<?php if($data!=null) { ?>
		<?php foreach($data as $item) { 
			$prod = ORM::factory('Product')->fetchProdById($item->id);?>
			<p><a target="_blank" href="http://<?=$_SERVER['SERVER_NAME']?>/<?php echo $prod->url; ?>"><?=$prod->name;?></a></p>
			<p>Количество - <?=$item->quantity?></p>
			<p>Цена - <?=number_format($item->price, 0, '', ' ')?> руб</p>
			<p>Сумма - <?=number_format($item->quantity*$item->price, 0, '', ' ')?> руб</p>
			<p>_________________________</p>
		<?php } ?>
		<?php } ?>