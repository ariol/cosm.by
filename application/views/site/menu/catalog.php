<nav id="main-menu" class="navbar" role="navigation">
    <!-- Nav Header Starts -->
    <div class="navbar-header">
        <button type="button" class="btn btn-navbar navbar-toggle" data-toggle="collapse" data-target=".navbar-cat-collapse">
            <span class="sr-only">Toggle Navigation</span>
            <i class="fa fa-bars"></i>
        </button>
    </div>
    <!-- Nav Header Ends -->
    <!-- Navbar Cat collapse Starts -->
    <div class="collapse navbar-collapse navbar-cat-collapse">
        <ul class="nav navbar-nav">
            <li class="dropdown">
                <a href="/" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="10">
                    Бренды
                </a>
                <ul class="dropdown-menu" role="menu">
                    <?php $brands = ORM::factory('Brand')->where('active', '=', 1)->find_all();?>
                    <?php foreach($brands as $brand){?>
                        <li><a tabindex="-1" href="/brand/<?php echo $brand->url?>"><?php echo $brand->name?></a></li>
                    <?php } ?>
                </ul>
            </li>
            <?php $category = ORM::factory('Category')->where('active', '=', 1)->find_all();?>
            <?php foreach($category as $item) { ?>
            <li>
                <a href="/<?php echo $item->url; ?>" class="dropdown-toggle" data-delay="10">
                    <?php echo $item->name; ?>
                </a>
            </li>
            <?php } ?>
            <li ><a href="/certificate" class="dropdown-toggle" data-delay="10">Подарочные сертификаты</a></li>
        </ul>
    </div>
    <!-- Navbar Cat collapse Ends -->
</nav>