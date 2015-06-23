
    <!-- Main Container Starts -->
    <div id="main-container">
        <!-- Breadcrumb Starts -->
        <ol class="breadcrumb">
            <li><a href="/">Главная</a></li>
            <li class="active">Избранное</li>
        </ol>
        <!-- Breadcrumb Ends -->
        <!-- Main Heading Starts -->
        <h2 class="main-heading text-center">
            Избранные товары
        </h2>
        <!-- Main Heading Ends -->
        <!-- Compare Table Starts -->
        <?php $resprice = 0; if($likeitems) { ?>
        <div class="table-responsive compare-table">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <td>Фото</td>
                    <td>Наименование</td>
                    <td>Цена</td>
                    <td>В корзину</td>
                    <td>Наличие</td>
                    <td>Удалить</td>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach($likeitems as $key => $item){
                $prod = ORM::factory('Product')->fetchProdById($item['id']);
                $category = ORM::factory('Category')->where('id', '=', $prod->category_id)->find();
                $certificate = ORM::factory('Certificate')->fetchCertificateById($item['id']);
                ?>
                    <?if($item['key_type'] == 'prod'){?>
                    <input type="hidden" name="quantity" value="1"  />
                <tr data-type="<?php echo $key; ?>">
                    <td>
                        <a href="/<?php echo $category->url; ?>/<?php echo $prod->url; ?>">
                            <img src="<?php echo Lib_Image::resize_width($prod->main_image, 'product', $prod->id, 128, null); ?>" alt="image" title="image" class="img-thumbnail" />
                        </a>
                    </td>
                    <td class="name">
                        <a href="/<?php echo $category->url; ?>/<?php echo $prod->url; ?>"><?php echo $prod->name?></a>
                        <div><?php if($prod->volume) echo 'Объем: '.$prod->volume;?></div>
                    </td>
                    <td>
                        <?php echo number_format($prod->price, 0, '', ' ');?> руб.
                    </td>
                    <td>
                        <?php if ($active) { ?>
                        <div class="product-col">
                            <div class="cart-button button-group">
                                <button type="button" class="btn btn-cart add_cart" data-id="<?php echo $prod->id; ?>" data-price="<?php echo $prod->price ?>">
                                    <i class="fa fa-shopping-cart"></i><br>
                                    Добавить в корзину
                                </button>
                            </div>
                        </div>
                        <?php }?>
                    </td>

                    <td>
                        <span class="label <?php if ($active) { ?> label-success"> В наличии<?php } else{ ?> label-danger"> Отсутствует<?php } ?></span>
                    </td>
                    <td>
                        <button name="remove_wish" type="button" title="Удалить" class="btn btn-default tool-tip" data-id="<?php echo $key ?>">
                            <i class="fa fa-times-circle"></i>
                        </button>
                    </td>
                </tr>
                        <?php } ?>
                    <?if($item['key_type'] == 'cert'){?>
                        <input type="hidden" name="quantity" value="1"  />
                        <tr data-type="<?php echo $key; ?>">
                            <td>
                                <a href="certificate_product/<?php echo $certificate->url; ?>">
                                    <img src="<?php echo Lib_Image::resize_width($certificate->image, 'certificate', $certificate->id, 128, null); ?>" alt="image" title="image" class="img-thumbnail" />
                                </a>
                            </td>
                            <td class="name">
                                <a href="certificate_product/<?php echo $certificate->url; ?>"><?php echo $certificate->name?></a>
                            </td>
                            <td>
                                <?php echo number_format($certificate->price, 0, '', ' ');?> руб.
                            </td>
                            <td>
                                <?php if ($active) { ?>
                                    <div class="product-col">
                                        <div class="cart-button button-group">
                                            <button type="button" class="btn btn-cart add_cart_sertificate" data-id="<?php echo $certificate->id; ?>" data-price="<?php echo $certificate->price ?>">
                                                <i class="fa fa-shopping-cart"></i><br>
                                                Добавить в корзину
                                            </button>
                                        </div>
                                    </div>
                                <?php }?>
                            </td>

                            <td>
                                <span class="label <?php if ($active) { ?> label-success"> В наличии<?php } else{ ?> label-danger"> Отсутствует<?php } ?></span>
                            </td>
                            <td>
                                <button name="remove_wish" type="button" title="Удалить" class="btn btn-default tool-tip" data-id="<?php echo $key ?>">
                                    <i class="fa fa-times-circle"></i>
                                </button>
                            </td>
                        </tr>
                        <?php } ?>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <?php } else { ?>
            <h3>Избранных товаров нет</h3>
        <?php } ?>
        <!-- Compare Table Ends -->
    </div>
    <!-- Main Container Ends -->