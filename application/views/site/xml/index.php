<?php echo '<?xml version="1.0" encoding="utf-8"?>'?>
<!DOCTYPE yml_catalog SYSTEM "shops.dtd">
<yml_catalog date="<?php echo date('Y-M-D H:m'); ?>">
	<shop>
		<name>1teh.by</name>
		<company>1teh</company>
		<url>http://1teh.by/</url>
		<platform>Ariol</platform>
		<version>2.1</version>
		<agency>1teh.by</agency>
		<email>1teh@tut.by</email>
		<currencies>
			<currency id="BYR" rate="1"/>
		</currencies>
		<categories>
			<category id="<?php echo $section->id; ?>"><?php echo $section->name; ?></category>
			<?php foreach ($categories as $id => $category) { ?>
			<?php if ($id) { ?>
			<category id="<?php echo $id; ?>" parentId="<?php echo $section->id; ?>"><?php echo $category; ?></category>
			<?php } ?>
			<?php } ?>
		</categories>
		<offers>
			<?php foreach ($products as $product) { ?><?php if ($product['main_image']) { ?><offer id="<?php echo $product['id']; ?>" available="<?php echo $product['active'] ? 'true' : 'false'; ?>">
				<url>https://1teh.by/<?php echo $product['sec_url']; ?>/<?php echo $product['url']; ?></url>
				<price><?php echo $product['new_price'] ? $product['new_price'] : $product['price']; ?></price>
				<?php if ($product['new_price']) { ?>
				<oldprice><?php echo $product['price']; ?></oldprice>
				<?php } ?>
				<currencyId>BUR</currencyId>
				<categoryId><?php echo $product['cat_id']; ?></categoryId>
				<picture>https://1teh.by<?php echo $product['main_image']; ?></picture>
				<name><?php echo $product['name']; ?></name>
				<vendor><?php echo $product['brand_name']; ?></vendor>
				<description><?php echo $product['s_description']; ?></description>
			</offer><?php } ?><?php } ?>
		</offers>
	</shop>
</yml_catalog>