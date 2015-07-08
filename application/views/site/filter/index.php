<h3 class="side-heading">Подбор по параметрам</h3>
<div class="preloader filters_preloader"></div>
<form id="filters" action="/<?php echo $category->url; ?>" method="get" class="invisible">
<div class="list-group">
    <div class="list-group-item">
        Цена
    </div>
    <div class="list-group-item">
		<div class="price_slider">
      		<span class="min-price"><?php echo number_format($current_min_price, 0, ' ', ' '); ?> руб.</span> <span class="max-price"><?php echo number_format($current_max_price, 0, ' ', ' '); ?> руб.</span>
			<input id="price_slider0" name="price" type="text" class="span2" value="" data-slider-min="<?php echo $min_price; ?>" data-slider-max="<?php echo $max_price; ?>" data-slider-step="1000" data-slider-value="[<?php echo $current_min_price; ?>,<?php echo $current_max_price; ?>]"/> 
      	</div>
		<button type="submit" class="btn btn-black filter_button_count">Подобрать</button>
    </div>
    <?php if($brand) { ?>
    <div class="list-group-item">
        Бренды
    </div>
    <div class="list-group-item">
        <div class="filter-group">
            <?php foreach($brand as $item) { ?>
            <label class="checkbox">
                <input data-type="brand" name="brands[]" <?php if(in_array($item['id'], Arr::get($_GET, 'brands', array()))) { ?>checked<?php } ?> type="checkbox" value="<?php echo $item['id']; ?>" />
                <?php echo $item['name'];?>
            </label>
            <?php } ?>
        </div>
    </div>
    <?php } ?>
    <?php if ($line) { ?>
	<?php $lines = array(); ?>
	<?php foreach ($brand as $brandItem) { ?>
		<?php foreach($line as $item){ ?>
		<?php if ($item['brand_id'] != $brandItem['id']) continue; ?>
		<?php $lines[$brandItem['id']][] = $item; ?>
		<?php } ?>
	<?php } ?>
	<?php foreach ($lines as $brandId => $lineArray) { ?>
	<?php if (count($lineArray)) { ?>
	<?php foreach ($brand as $br) { ?>
	<?php if ($br['id'] == $brandId) break; ?>
	<?php } ?>
    <div class="list-group-item lines">
        Линии <?php echo $br['name']; ?>
    </div>
	<div class=" scrollbar-light">
    <div class="list-group-item lines">
        <div class="filter-group">
            <?php foreach($lineArray as $item){?>
                <label class="checkbox">
                    <input <?php if(isset($_GET['line'][$item['id']])) { ?>checked<?php } ?> name="line[<?php echo $item['id']; ?>]" type="checkbox" value="1" />
                    <?php echo $item['name'];?>
                </label>
            <?php } ?>
        </div>
    </div>
	</div>
	<?php } ?>
	<?php } ?>
	<?php } ?>
    <?php if($filters) { ?>
    <div class="list-group-item">
        Назначение
    </div>
	<div class=" scrollbar-light">
    <div class="list-group-item">
        <div class="filter-group">
            <?php foreach($filters as $item){?>
				<?php if ($item['property_type'] != 1) continue; ?>
                <label class="checkbox">
                    <input <?php if(isset($_GET['filter'][$item['property_id']])) { ?>checked<?php } ?> name="filter[<?php echo $item['property_id'];?>]" type="checkbox" value="1" />
                    <?php echo $item['name'];?>
                </label>
            <?php } ?>
        </div>
    </div>
    </div>
	<div class="list-group-item">
        Тип средства
    </div>
	<div class=" scrollbar-light">
    <div class="list-group-item">
        <div class="filter-group">
            <?php foreach($filters as $item){?>
				<?php if ($item['property_type'] != 2) continue; ?>
                <label class="checkbox">
                    <input <?php if(isset($_GET['filter'][$item['property_id']])) { ?>checked<?php } ?> name="filter[<?php echo $item['property_id'];?>]" type="checkbox" value="1" />
                    <?php echo $item['name'];?>
                </label>
            <?php } ?>
        </div>
    </div>
    </div>
    <?php } ?>
</div>
</form>