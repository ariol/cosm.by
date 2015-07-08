<div class="col-md-9">
    <!-- Breadcrumb Starts -->
    <ol class="breadcrumb">
        <li><a href="/">Главная</a></li>
        <li><?php echo $name; ?></li>
    </ol>
</div>
<div class="container catalog">
    <div class="row">
        <div class="col-xs-12"><h1><?php echo $name; ?></h1></div>
        <article class="col-xs-12 polka"><?php echo $content; ?></article>
        <div class="col-sm-8">
            <div class="success_message hidden"> <h2>Спасибо за оставленное сообщение</h2></div>
            <div class="panel panel-smart feedback">
                <div class="panel-heading">
                    <h3 class="panel-title">Обратная связь</h3>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form">
                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">
                                Имя
                            </label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="name" id="name" placeholder="Имя">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email" class="col-sm-2 control-label">
                                Email
                            </label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="email" id="email" placeholder="Email">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="subject" class="col-sm-2 control-label">
                                Телефон
                            </label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="phone" id="subject" placeholder="Телефон">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="message" class="col-sm-2 control-label">
                                Сообщение
                            </label>
                            <div class="col-sm-10">
                                <textarea name="message" id="message" class="form-control" rows="5" placeholder="Сообщение"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" data-type="feedback" class="btn btn-black text-uppercase">
                                    Отправить
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>