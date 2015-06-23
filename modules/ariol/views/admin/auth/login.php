<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @version SVN: $Id:$
 */

?>

<div class="row">
	<?php if($error):?>
		<div class="alert alert-danger">
			<button class="close" data-dismiss="alert" type="button">×</button>
			<?php echo $error; ?>
		</div>
	<?php endif;?>
	<div class="login-box">
		<div class="header">
			Войти в Ariol
		</div>
		<form class="form-horizontal login" method="post">
			<?php echo Form::hidden('return', $return);?>
			<fieldset class="col-sm-12">
				<div class="form-group">
					<div class="controls row">
						<div class="input-group col-sm-12">
							<input name="email" value="<?php echo $email; ?>" type="text" class="form-control" id="username" placeholder="E-mail"/>
							<span class="input-group-addon"><i class="fa fa-user"></i></span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="controls row">
						<div class="input-group col-sm-12">
							<input name="password" type="password" class="form-control" id="password" placeholder="Пароль"/>
							<span class="input-group-addon"><i class="fa fa-key"></i></span>
						</div>
					</div>
				</div>

				<div class="confirm">
					<?php echo Form::checkbox('remember', '1', $remember, array('id' => 'remember')); ?>
					<label for="remember">Запомнить меня</label>
				</div>
				<div class="row">
					<input type="submit" class="btn btn-lg btn-primary col-xs-12" value="Войти" name="login" />
				</div>
			</fieldset>
		</form>
		<div class="clearfix"></div>
	</div>
</div>
