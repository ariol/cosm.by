<div class="modal fade" id="<?php echo $id; ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <h1>Авторизация</h1>
                <form id="login_form" role="form">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Логин</label>
                        <input name="login" type="text" class="form-control" id="exampleInputEmail1">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Пароль</label>
                        <input name="password" type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
                    </div>
					<div id="auth_error" style="display: none;"></div>
                    <div class="checkbox">
                        <label>
                            <input name="remember" type="checkbox"> Запомнить
                        </label>
                    </div>
                    <button type="submit" class="btn btn-default">Войти на сайт</button>
                </form>
            </div>
        </div>
    </div>
</div>