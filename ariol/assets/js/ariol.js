$(document).ready(function() {
    $.noty.defaults = {
        layout: 'top',
        theme: 'defaultTheme',
        type: 'alert',
        text: '', // can be html or string
        dismissQueue: true, // If you want to use queue feature set this true
        template: '<div class="noty_message"><span class="noty_text"></span><div class="noty_close"></div></div>',
        animation: {
            open: {height: 'toggle'},
            close: {height: 'toggle'},
            easing: 'swing',
            speed: 500 // opening & closing animation speed
        },
        timeout: false, // delay for closing event. Set false for sticky notifications
        force: false, // adds notification to the beginning of queue when set to true
        modal: false,
        maxVisible: 5, // you can set max visible notification for dismissQueue true option,
        killer: false, // for close all notifications before show
        closeWith: ['click'], // ['click', 'button', 'hover']
        callback: {
            onShow: function() {},
            afterShow: function() {},
            onClose: function() {},
            afterClose: function() {}
        },
        buttons: false // an array of buttons
    };

    $('.form-change').bootstrapSwitch({
        size: 'small',
        onColor: 'success',
        offColor: 'danger'
    });

    $('.change').bootstrapSwitch({
        size: 'small',
        onColor: 'success',
        offColor: 'danger',
        onSwitchChange: function(e) {
            var $input = $(e.target);
            $.ajax({
                url: "/ariol-admin/ajax/boolean",
                type: "post",
                data: ({
                    class_name : $input.attr('data-class'),
                    field : $input.attr('data-field'),
                    value : value,
                    id : $input.attr('data-id')
                }),
                dataType: "json"
            });
        }
    });

    $('input.checkbox').iCheck({
        checkboxClass: 'icheckbox_square-blue'
    });

    var urlParts = window.location.pathname.split('/');
	
    var controller_route = urlParts[urlParts.length - 1];
    if (!controller_route) {
        controller_route = urlParts[urlParts.length - 2];
    }
	
	var controller_filded = false;
	var href = '/';
	
	$.each(urlParts, function(index, value) {
		if (value == 'ariol-admin') {
			controller_filded = true;
		}
		
		if (controller_filded) {
			href += value + '/';
			if (value == controller_route) {
				controller_filded = false;
			}
		}
	});
	
    href += "get_grid_data" + window.location.search;
	
    $('#dataTable').dataTable({
        "aLengthMenu": [[50, 100, 500, 1000], [50, 100, 500, 1000]],
        "iDisplayLength" : 50,
        "bAutoWidth": true,
        "bSort": true,
        "processing": true,
        "bFilter": false,
        "aoColumnDefs": aryJSONColTable,
        "bServerSide": true,
        "sAjaxSource": href,
        "bProcessing": true,
        "fnDrawCallback": function () {
            $('.change').bootstrapSwitch({
                size: 'small',
                onColor: 'success',
                offColor: 'danger',
                onSwitchChange: function(e, value) {
                    var $input = $(e.target);
                    $.ajax({
                        url: "/ariol-admin/ajax/boolean",
                        type: "post",
                        data: ({
                            class_name : $input.attr('data-class'),
                            field : $input.attr('data-field'),
                            value : value,
                            id : $input.attr('data-id')
                        }),
                        dataType: "json"
                    });
                }
            });
            $('input.checkbox').iCheck({
                checkboxClass: 'icheckbox_square-blue'
            });
            $('[name=ids_all]').iCheck('uncheck');
            $('input[name=ids_all]').on('ifChanged', function(event){
                if (event.target.checked) {
                    $('[name=ids\\[\\]]').iCheck('check');
                } else {
                    $('[name=ids\\[\\]]').iCheck('uncheck');
                }
            });
        },
        "oLanguage": {
            "sProcessing":   "Подождите...",
            "sLengthMenu":   "Показать _MENU_ записей",
            "sZeroRecords":  "Записи отсутствуют.",
            "sInfo":         "Записи с _START_ до _END_ из _TOTAL_ записей",
            "sInfoEmpty":    "Записи с 0 до 0 из 0 записей",
            "sInfoFiltered": "(отфильтровано из _MAX_ записей)",
            "sInfoPostFix":  "",
            "sSearch":       "Поиск:",
            "sUrl":          "",
            "oPaginate": {
                "sFirst": "Первая",
                "sPrevious": "Предыдущая",
                "sNext": "Следующая",
                "sLast": "Последняя"
            },
            "oAria": {
                "sSortAscending":  ": активировать для сортировки столбца по возрастанию",
                "sSortDescending": ": активировать для сортировки столбцов по убыванию"
            }
        }
    });

    $('input[name=ids_all]').on('ifChanged', function(event){
        if (event.target.checked) {
            $('[name=ids\\[\\]]').iCheck('check');
        } else {
            $('[name=ids\\[\\]]').iCheck('uncheck');
        }
    });

    $('.btn-delete').click(function(e) {
        e.preventDefault();
        $('.noty_type_confirm').hide();
        var self = $(this);
        noty({
            text: 'Вы уверены?',
            type: 'confirm',
            buttons: [
                {
                    addClass: 'btn btn-primary', text: 'Да', type: 'success', onClick: function ($noty) {
                        $('input[name="' + self.attr('name') + '"]').trigger('click');
                        $noty.close();
                    }
                },
                {
                    addClass: 'btn btn-danger', text: 'Нет', type: 'error', onClick: function ($noty) {
                        $noty.close();
                    }
                }
            ]
        });
    });

    $('.fancybox').fancybox();
})