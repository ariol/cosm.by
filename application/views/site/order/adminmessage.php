		<p>В интернет-магазине https://<?=$_SERVER['SERVER_NAME']?> <?=date("d F Y");?> в <?=date("H:i:s");?> был создан заказ.
		<p><strong>Имя: </strong><?=$name;?></p>
		<?php if ($email) { ?><p><strong>Email: </strong><?=$email;?></p><?php } ?>
		<p><strong>Телефон: </strong><?=$phone;?></p>
		<p><strong>Адрес: </strong><?=$adress;?></p>
        <?php if($city){?><p><strong>Город: </strong><?php echo $city;?></p><?php } ?>
        <?php if($index){?><p><strong>Индекс: </strong><?php echo $index;?></p><?php } ?>
		<?php foreach($cart as $item) { 
			$prod = ORM::factory('Product')->fetchProdById($item->id);?>
			<p><a target="_blank" href="https://<?=$_SERVER['SERVER_NAME']?>/<?php echo $prod->url; ?>"><?=$prod->name;?></a></p>
			<p>Количество - <?=$item->quantity?></p>
            <p>Цена - <?=number_format($item->price, 0, '', ' ')?> руб</p>
			<p>_________________________</p>
		<?php } ?>
        <?php if($cert_mail) { ?>
            <?php foreach($cert_mail as $item) {
                $certificate = ORM::factory('Product')->fetchCertificateById($item->id);?>
                <p><a target="_blank" href="http://<?=$_SERVER['SERVER_NAME']?>/<?php echo $certificate->url; ?>"><?=$certificate->name;?></a></p>
                <p>Количество - <?=$item->quantity?></p>
                <p>Цена - <?=number_format($item->price, 0, '', ' ')?> руб</p>
                <p>_________________________</p>
            <?php } ?>
        <?php } ?>
        <?php if($code){ ?>
            <p>Был использован купон: <?php echo $code?></p>
        <?php } ?>
        <?php if($code_certificate){ ?>
            <p>Был использован сертификат: <?php echo $code_certificate?></p>
        <?php } ?>
        <p>_________________________</p>