<?php foreach ($cat_result as $item) {
    ?>
    <div class="item category">
        <div class="name"><a href="<?php echo $item->url; ?>"><?=$item->name?></a></div>
    </div>
<?php } ?>
<?php foreach ($search_result as $item) {
    $category = ORM::factory('Category')->where('id', '=', $item->category_id)->find();
    if($item->new_price)
        $price = $item->new_price;
    else
        $price = $item->price;
    ?>
    <div class="item">
        <div class="image"><a href="/<?php echo $category->url; ?>/<?php echo $item->url; ?>"><img src="<?php echo Lib_Image::resize_width($item->main_image, 'product', $item->id, null, 150); ?>"></a></div>
        <div class="name"><a href="/<?php echo $category->url; ?>/<?php echo $item->url; ?>"><?=$item->name?></a></div>
        <div class="price two">
                <span class="new-price"><?php echo number_format($price, 0, '', ' ');?> руб.</span>
        </div>
    </div>
<?php } ?>