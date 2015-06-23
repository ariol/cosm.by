<?php if($menu->as_array()): ?>
<ul>
    <?php foreach($menu as $point):?>
    <li>
        <?= HTML::anchor($point->url, $point->name);?>
    </li>
    <?php endforeach;?>
</ul>
<?php endif;?>