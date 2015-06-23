<div class="modal fade" id="<?php echo $id; ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <h1>Регистрация</h1>
                <p>Зарегистрируйся и пользуйся всеми возможностями нашего сервиса. Это займет меньше минуты.</p>
                <p class="color-red">* - обязательно для заполнения</p>
                <form id="register_form" role="form">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Логин</label>
                        <input name="login" type="text" class="form-control" id="exampleInputText1">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Пароль</label>
                        <input name="password" type="password" class="form-control" id="exampleInputPassword1">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Email</label>
                        <input name="email" type="email" class="form-control" id="exampleInputEmail1">
                    </div>
                    <button type="submit" class="btn btn-default">Регистрация</button>
                </form>
            </div>
        </div>
    </div>
</div>