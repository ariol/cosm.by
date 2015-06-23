<div class="container">	<div class="row">		<div class="breadcrumbs col-xs-12" itemscope itemtype="http://data-vocabulary.org/Breadcrumb">			<strong>Вы здесь:</strong> <div><a href="/" itemprop="url"><span itemprop="title">Главная</span></a></div><div><span>Акции</span></div>		</div>	</div></div>
<div class="clear"></div><div class="container">	<div class="row">		<div class="col-xs-12">
			<h1>Акции</h1>		</div>	</div></div><div class="col-xs-12">	<?php echo $pagination; ?></div><div class="container contant">	<div class="row">		<div class="polka">
		<?php $discount = ORM::factory('Discount')->fetchActive();
		foreach ($discount as $item) { ?>
		<div class="discount col-xs-4">
			<div class="col-xs-12 image"><a href="/discount/<?=$item->url?>"><img src="<?php echo Lib_Image::resize_width($item->image, 'discount', $item->id, 290, null); ?>" alt="Акция - <?=$item->name?>" /></a></div>
			<div class="col-xs-12"><span class="from_to"><?=$item->from_to;?></span></div>			<div class="col-xs-12 h1"><a href="/discount/<?=$item->url?>"><?=$item->name?></a></div>			<div class="col-xs-12"><?=$item->short_text?></div>
			<div class="clear"></div>
		</div>
		<?php } ?>		<div class="clear"></div>		</div>	</div></div><div class="col-xs-12">	<?php echo $pagination; ?></div>
