$(document).ready(function(){
    $('input[name=type]').click(function(){
        $('input[name=type]').prop('checked', false);
        $(this).prop('checked', true);
    });

    $('#register_form').on('submit', function(e){
        e.preventDefault();
        var data = $(this).serializeArray();
        $.ajax({
            type: "POST",
            url: "/module_auth/ajax_register",
            data: data,
            dataType: "json",
            success: function(data){
                if (data.errors_exists) {
                    console.log(errors);
                    alert('Воу воу, ашыбка');
                }
                else {
                    alert('Пасыба, цыни почту');
                }
            }
        });
    });

    $('#login_form').on('submit', function(e){
        e.preventDefault();
		$('.error').removeClass('error');
        var data = $(this).serializeArray();
        $.ajax({
            type: "POST",
            url: "/module_auth/ajax_login",
            data: data,
            dataType: "json",
            success: function(data){
                if (data.logged_in) {
                    window.location.reload();
                }
                else {
                    var notice_html = '';
                    $.each(data.errors, function(index, value) {
                        notice_html += '<div class="alert alert-danger alert-dismissable">' +
                            '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' +
                            '<span id="error_email">' + value + '</span>' +
                        '</div>';
                    });
					$('#auth_error').html(notice_html);
					$('#auth_error').fadeIn();
					
					$('input[name=login]').addClass('error');
					$('input[name=password]').addClass('error');
                }
            }
        });
    })
})