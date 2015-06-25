<h3 class="side-heading">Подбор по параметрам</h3>
<form id="filters" action="/<?php echo $category->url; ?>" method="get">
<div class="list-group">
    <div class="list-group-item">
        Цена
    </div>
    <div class="list-group-item">
        <div class="filter-group">
        <label class="input_price">
            От<input style="width: 80%; display: inline-block;" name="min_price" class="form-control" type="text" value="<?php echo $min_price; ?>" >
            До<input style="width: 80%; margin-top: 5px; display: inline-block;" name="max_price" class="form-control" type="text" value="<?php echo $max_price; ?>" >
        </label>
            <button type="submit" class="btn btn-black filter_button_count">Подобрать</button>
        </div>
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
    <div class="list-group-item lines">
        Линии
    </div>
    <div class="list-group-item lines">
        <div class="filter-group">
            <?php foreach($line as $item){?>
                <label class="checkbox">
                    <input <?php if(isset($_GET['line'][$item['id']])) { ?>checked<?php } ?> name="line[<?php echo $item['id']; ?>]" type="checkbox" value="1" />
                    <?php echo $item['name'];?>
                </label>
            <?php } ?>
        </div>
    </div>
    <?php } ?>
    <?php if($property) { ?>
    <div class="list-group-item">
        Назначение
    </div>
	<div class=" scrollbar-light">
    <div class="list-group-item">
        <div class="filter-group">
            <?php foreach($property as $item){?>
                <label class="checkbox">
                    <input <?php if(isset($_GET['filter'][$item['id']])) { ?>checked<?php } ?> name="filter[<?php echo $item['id'];?>]" type="checkbox" value="1" />
                    <?php echo $item['name'];?>
                </label>
            <?php } ?>
        </div>
    </div>
    </div>
    <?php } ?>
</div>
</form>