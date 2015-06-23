
(function($) {

$.fn.extCrudDialog = function(url, success, options) {
		
	options = $.extend(true, {
		title: "",
		modal: true,
		autoOpen: false,
		width: 700,
		height: 500
	}, options);
	
	var dialog_buttons = {
		'Сохранить': function(){
			var form = $(this).find('form');
			form.attr('action', form.attr('action') + '?ajax');
			dialog.dialog('option', 'buttons', {});
			form.ajaxSubmit({
				target: dialog,
				success: function(data){
					if (data == "OK")
					{
						success();
						dialog.dialog('close');
					}
					else
					{
						dialog.dialog('option', 'buttons', dialog_buttons);
					}
				}
			});
		},
		'Отмена': function(){$(this).dialog('close');}
	};

	var dialog = $("<div></div>");
	dialog.dialog(options);
	
	dialog.html('Загрузка...');
	dialog.dialog('option', 'buttons', {});
	dialog.dialog('open');
	dialog.load(url, function() {
		dialog.dialog('option', 'buttons', dialog_buttons);
	});
}

$.fn.extAjaxDialog = function(url, options) {
	
	options = $.extend(true, {
		title: "",
		modal: true,
		autoOpen: false,
		width: 700,
		height: 300
	}, options);
	
	var dialog_buttons = {
		'Закрыть': function(){$(this).dialog('close');}
	};

	var dialog = $("<div></div>");
	dialog.dialog(options);
	
	dialog.html('Загрузка...');
	dialog.dialog('open');
	dialog.load(url, function() {
		dialog.dialog('option', 'buttons', dialog_buttons);
	});
}

})(jQuery);