<ul>
    <?php foreach($categories as $category):?>
    <li>
        <?= $category->get_url_site(); ?>
    </li>
    <?php endforeach;?>
</ul>