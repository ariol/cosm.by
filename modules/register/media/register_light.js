$(document).ready(function(){
    $('#register_form').on('submit', function(e){
        e.preventDefault();
        var data = $(this).serializeArray();
        $.ajax({
            type: "POST",
            url: "/module_auth/ajax_light_register",
            data: data,
            dataType: "json",
            success: function(data){
                if (data.errors_exists) {
                    var notice_html = '';
                    $.each(data.errors, function(index, value) {
                        notice_html += '<div class="alert alert-danger alert-dismissable">' +
                            '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' +
                            '<span id="error_email">' + value + '</span>' +
                        '</div>';
                    });
                }
                else {
                    var notice_html = '<div class="alert alert-success alert-dismissable">' +
                        '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' +
                        '<span id="error_email">Регистрация успешна. Письмо с подтверждением отправлено на указанный email адрес.</span>' +
                        '</div>';
                }
                $('#email_valid').html(notice_html);
                $('#email_valid').fadeIn();
            }
        });
    });
})