
<div id="main-container">
    <div class="row">
        <!-- Sidebar Starts -->
        <div class="col-md-3">
            <!-- Categories Links Starts -->
            <h3 class="side-heading">Катагории</h3>
            <div class="list-group categories">
                    <a href="#" class="list-group-item">
                        <i class="fa fa-chevron-right"></i>
                gdfgfg
                    </a>
            </div>
            <!-- Categories Links Ends -->
            <!-- Shopping Options Starts -->
            <h3 class="side-heading">Фильтрация</h3>
            <div class="list-group">
                <div class="list-group-item">
                    Производители
                </div>
                <div class="list-group-item">
                    <div class="filter-group">
                        <?php foreach ($brands as $brandModel) { ?>
                            <label class="checkbox">
                                <input name="filter1" type="checkbox" value="<?php echo $brandModel['name']; ?>" />
                                <?php echo $brandModel['name']; ?>
                            </label>
                        <?php } ?>
                    </div>
                </div>
                <div class="list-group-item">
                    Характеристики
                </div>
                <div class="list-group-item">
                    <div class="filter-group">
                        <label class="radio">
                            Минимальная стоимость<input name="min_price" type="text" class="span2" value="0" >
                            Максимальная стоимость<input name="min_price" type="text" class="span2" value="0" >
                        </label>
                        <label class="radio">
                            Производитель
                            <select name="brand">
                                <option value="">Не важно</option>
                                <?php foreach ($brands as $br) { ?>
                                    <option<?php if ($br['id'] == $brand_id){ ?> selected="selected" <?php } ?> value="<?php echo $br['id']; ?>"><?php echo $br['name']; ?></option>
                                <?php } ?>
                            </select>
                        </label>
                        <label class="radio">
                            <input name="filter-manuf" type="radio" value="mr3" />
                            Manufacturer Name 3
                        </label>
                    </div>
                </div>
                <div class="list-group-item">
                    <button type="button" class="btn btn-main">Подобрать</button>
                </div>
            </div>
            <!-- Shopping Options Ends -->
            <!-- Bestsellers Links Starts -->
            <h3 class="side-heading">Спецпредложение</h3>
            <?php $specialProduct = ORM::factory('Product')->fetchProdSpecial($category->id); ?>
            <?php foreach($specialProduct as $topprod){
                if($topprod->new_price)
                    $price = $topprod->new_price;
                else
                    $price = $topprod->price;
                ?>
                <div class="product-col">
                    <div class="image">
                        <img src="<?php echo Lib_Image::resize_width($topprod->main_image, 'product', $topprod->id, 250, 250); ?>" alt="product" class="img-responsive" />
                    </div>
                    <div class="caption">
                        <div class="price">
                            <span class="price-new"><?php echo number_format($price, 0, ' ', ' '); ?>руб.</span>
                        </div>
                        <?php if($topprod->new_price){ ?>
                            <div class="price">
                                <span class="price-old"><?php echo number_format($topprod->price, 0, ' ', ' '); ?>руб.</span>
                            </div>
                        <?php } ?>
                        <h4><a href="/<?php echo $topprod->url; ?>"><?php echo $topprod->name;?></a></h4>
                        <div class="description">
                            <?php echo $topprod->short_content?>
                        </div>
                        <div class="cart-button button-group">
                            <button type="button" class="btn btn-cart add_cart" data-id="<?php echo $topprod->id; ?>" data-price="<?php echo $price ?>">
                                <i class="fa fa-shopping-cart"></i><br>
                                Добавить в корзину
                            </button>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div id="vk_groups"></div>
            <script type="text/javascript">
                VK.Widgets.Group("vk_groups", {mode: 0, width: "250", height: "400"}, 73341072);
            </script>
            <!-- Bestsellers Links Ends -->
            <!-- Bestsellers Links Ends -->
        </div>
        <!-- Sidebar Ends -->
        <!-- Primary Content Starts -->
        <div class="col-md-9">
            <!-- Breadcrumb Starts -->
            <ol class="breadcrumb">
                <li><a href="/">Home</a></li>
                <li><a href="/cat/<?php echo $category->url; ?>"><?php echo $category->name; ?></a></li>
                <li class="active"><?php echo $brand->name; ?></li>
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
                    <img src=" <?php echo $brand->main_image; ?>" alt="Image" class="img-responsive img-thumbnail" />
                </div>
                <div class="col-sm-9 cat-body">
                    <p>
                        <?php echo $brand->description; ?>
                    </p>

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
        <div class="row">
            <?php foreach ($product as $index => $prod) {
                if($prod->new_price)
                    $price = $prod->new_price;
                else
                    $price = $prod->price;
                ?>
                <input type="hidden" name="quantity" value="1"  />
                <div class="col-md-4 col-sm-6">
                    <div class="product-col">
                        <div class="image">
                            <a href="/<?php echo $prod->url; ?>">
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
                            <h4><a href="/<?php echo $prod->url; ?>"><?php echo $prod->name; ?> </a></h4>
                            <div class="description">
                                <?php echo $prod->short_content?>
                            </div>
                            <div class="cart-button button-group">
                                <button type="button" class="btn btn-cart add_cart" data-id="<?php echo $prod->id; ?>" data-price="<?php echo $price ?>">
                                    <i class="fa fa-shopping-cart"></i><br>
                                    Добавить в корзину
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="row">
            <div class="paginator">
                <div class="col-sm-10 pagination-block">
                    <?php echo $pagination; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</div>