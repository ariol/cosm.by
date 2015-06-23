<ul id="nav" class="sf-menu">
    <?php foreach($menu as $point):?>
    <li <?php if($point->url.'/' == arr::get($_SERVER, 'REQUEST_URI')) echo 'class="current-menu-item"'; ?>>
        <?= HTML::anchor($point->url, $point->name);?>
        <?= Controller_Site_Menu::get_children($point->id); ?>
    </li>
    <?php endforeach;?>
</ul>