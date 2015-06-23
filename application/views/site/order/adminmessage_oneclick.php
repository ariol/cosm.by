		<p>В интернет-магазине https://<?=$_SERVER['SERVER_NAME']?> <?=date("d F Y");?> в <?=date("H:i:s");?> был создан быстрый заказ.

		<p><strong>Имя: </strong><?=$name;?></p>

		<p><strong>Телефон: </strong><?=$phone;?></p>

		<p><strong>Адрес: </strong><?=$adress;?></p>

		<?php $prod = ORM::factory('Product')->where('id', '=', $cart)->find();	?>

		<p><a target="_blank" href="https://<?=$_SERVER['SERVER_NAME']?>/<?php echo $prod->url; ?>"><?=$prod->name;?></a></p>
		<p>Цена - 
		<?php if($prod->new_price && !$prod->discount) { ?><span class="new-price"><?php echo number_format($prod->new_price, 0, '', ' ');?> руб.</span><?php } ?>
		<?php if(!$prod->new_price && $prod->discount) { ?><span class="new-price"><?php echo number_format($prod->price/100*(100-$prod->discount), 0, '', ' ');?> руб.</span><?php } ?>
		<span class="<?php if($prod->new_price || $prod->discount) { echo 'old-'; }?>price" style="<?php if($prod->new_price || $prod->discount) { echo 'text-decoration:line-through; color: red;'; }?>"><?php echo number_format($prod->price, 0, '', ' ');?> руб.</span></p>
		<p>_________________________</p>