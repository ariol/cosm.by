<p>Благодарим за покупку в нашем интерент-магазине <?=$_SERVER['SERVER_NAME']?>.</p>
<p><strong>Имя: </strong><?=$name;?></p>
<?php if ($email) { ?><p><strong>Email: </strong><?=$email;?></p><?php } ?>
<p><strong>Мы дарим вам купон на скидку в </strong><?php echo $discount?>%</p>
<p><strong>Код купона: </strong> <?php echo $code?></p>
<p><strong>Срок действия по: <?php date("Y-m-d", $time)?></p>