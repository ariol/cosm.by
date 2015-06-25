<div id="main-container">
    <div class="row">
        <div class="col-md-3">
            <?php if($line) { ?>
            <h3 class="side-heading">Линии</h3>
            <form id="brand_lines" action="/brand/<?php echo $brand->url; ?>">
                <div class="list-group">
                    <?php if($line) { ?>
                    <div class="list-group-item">
                        <div class="filter-group">
                            <?php foreach($line as $item) { ?>
                                <div>
                                    <a href="/brand/<?php echo $brand->url; ?>/<?php echo $item['url'];?>"><?php echo $item['name'];?></a>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </form>
            <?php } ?>
            <div id="vk_groups"></div>
            <script type="text/javascript">
                VK.Widgets.Group("vk_groups", {mode: 0, width: "250", height: "400"}, 73341072);
            </script>
            <!-- Bestsellers Links Ends -->
        </div>
        <!-- Sidebar Ends -->
        <!-- Primary Content Starts -->
        <div class="col-md-9">
            <!-- Breadcrumb Starts -->
            <ol class="breadcrumb">
                <li><a href="/">Главная</a></li>
                <li><?php echo $brand->name; ?></li>
            </ol>
            <!-- Breadcrumb Ends -->
            <!-- Main Heading Starts -->
            <h2 class="main-heading2">
                <?php echo $brand->name; ?>
            </h2>
            <!-- Main Heading Ends -->
            <!-- Category Intro Content Starts -->
            <div class="row cat-intro">
                <div class="col-sm-3">
                    <?php if ($brand->main_image) { ?>
                        <img src="<?php echo $brand->main_image; ?>" alt="Image" class="img-responsive img-thumbnail" />
                    <?php } ?>
                </div>
                <div class="col-sm-9 cat-body">
                    <?php echo $barand->description; ?>
                </div>
            </div>
            <!-- Category Intro Content Ends -->
            <!-- Product Filter Starts -->
            <div class="product-filter">
                <div class="row">
                    <div class="col-md-4">
                        <div class="display">
                            <a href="?page_view=list">
                                <i class="fa fa-th-list" title="List View"></i>
                            </a>
                            <a href="?page_view=grid" class="active">
                                <i class="fa fa-th" title="Grid View"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-5 text-right">
                        <label class="control-label">Сортировать по:</label>
                    </div>
                    <div class="col-md-2 text-right">
                        <div class="sort_list">
                            <?php echo Sortable::get_button('price', 'цене', null, true); ?>
                            <?php echo Sortable::get_button('name', 'названию', null, true); ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Product Filter Ends -->
            <!-- Product Grid Display Starts -->
            <section class="products-list">
                <div class="row">
                    <?php $clearfix = 0; foreach ($product as $index => $prod) {
                        $price = ORM::factory('Product')->getPriceValue($prod->id);
                        $category = ORM::factory('Category')->where('id', '=', $prod->category_id)->find();
                        ?>
                        <input type="hidden" name="quantity" value="1"  />
                        <div class="col-md-4 col-sm-6">
                            <div class="product-col">
                                <div class="image">
                                    <a href="/<?php echo $category->url; ?>/<?php echo $prod->url; ?>">
                                        <img src="<?php echo Lib_Image::resize_bg($prod->main_image, 'product', $prod->id, 250, 250); ?>" alt="product" class="img-responsive" />
                                    </a>
                                </div>
                                <div class="caption">
                                    <div class="price">
                                        <span class="price-new"><?php echo number_format($price, 0, ' ', ' '); ?>руб.</span>
                                    </div>
                                    <?php if($prod->new_price){ ?>
                                        <div class="price">
                                            <span class="price-old"><?php echo number_format($prod->price, 0, ' ', ' '); ?>руб.</span>
                                        </div>
                                    <?php } ?>
                                    <h4><a href="/<?php echo $category->url; ?>/<?php echo $prod->url; ?>"><?php echo $prod->name; ?> </a></h4>
                                    <div class="cart-button button-group">
                                        <button type="button" class="btn btn-cart add_cart" data-id="<?php echo $prod->id; ?>" data-price="<?php echo $price ?>">
                                            <i class="fa fa-shopping-cart"></i><br>
                                            Добавить в корзину
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php $clearfix++; if($clearfix % 3 == 0) { ?><div class="clearfix"></div>  <?php }?> <?php  } ?>
                </div>
            </section>
            <div class="row">
                <div class="paginator">
                    <div class="col-sm-6 pagination-block">
                        <?php echo $pagination; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>