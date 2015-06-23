$(document).ready(function(){
	/* ---------- Placeholder Fix for IE ---------- */
	$('input').iCheck({
		checkboxClass: 'icheckbox_square-blue'
	});

    $("#login-form").validate({
        errorPlacement: function(error, element) {},
        rules: {
            email: {
                required: true,
                email: true
            },
            password: {
                required: true
            }
        },
        submitHandler: function(form) {
            $.ajax({
                type: "post",
                url: "/ariol-admin/auth/login",
                dataType: "json",
                data: {
                    email: $('input[name=email]').val(),
                    password: $('input[name=password]').val(),
                    remember: $('input[name=remember]').is(':checked')
                },
                success: function(data) {
                    if (data.status == 1) {
                        window.location.href = data.return;
                    } else {
                        noty({text: 'Неверные e-mail или пароль', type: 'error'});
                    }
                }
            });
        },
        invalidHandler: function(e, validator) {
            noty({text: 'Неверные e-mail или пароль', type: 'error'});
        }
    });
});