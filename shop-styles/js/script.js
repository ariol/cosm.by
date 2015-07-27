$(document).ready(function(){
	//$('[rel=add-to-cart]').replaceWith('<b style="color: red">Продажи временно приостановлены!</b>');
	//$('.fast-buy').remove();

	function number_format( number, decimals, dec_point, thousands_sep ) {	
		var i, j, kw, kd, km;
		if( isNaN(decimals = Math.abs(decimals)) ){
			decimals = 2;
		}
		if( dec_point == undefined ){
			dec_point = ",";
		}
		if( thousands_sep == undefined ){
			thousands_sep = ".";
		}

		i = parseInt(number = (+number || 0).toFixed(decimals)) + "";

		if( (j = i.length) > 3 ){
			j = j % 3;
		} else{
			j = 0;
		}
		km = (j ? i.substr(0, j) + thousands_sep : "");
		kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
		kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : "");
		return km + kw + kd;
	}
	
	var _slideTo = function(id){
		id = 'sku-service-'+id;
		e=$('#'+id);b=$('#service-content');
        b.find('.service-full.active').hide(0);
		b.find('.service-block .active').removeClass('active');
		$('html, body').animate({scrollTop:(e.offset().top-116)},'1500','swing',function(){
			e.addClass('active');
			$('.'+id).slideDown(500).addClass('active');
		});
		return false;
	}
	
    $("#service-content .service-block a").click(function(){
		if($(this).hasClass('active')) return false;
		b=$('#service-content');
        b.find('.service-full.active').slideUp(300);
		b.find('.active').removeClass('active');
		$(this).addClass('active');
        var c = $(this).attr('id');
        b.find('.'+c).slideDown(300).addClass('active');
        return false;
    });

	$(".range input").on('slide', function(e){
		var val1 = number_format(e.value[0], 0, '', ' ');
		var val2 = number_format(e.value[1], 0, '', ' ');
		
		$(this).parent('.slider').parent('.range').find('.left').text(val1 + ' руб.');
		$(this).parent('.slider').parent('.range').find('.right').text(val2 + ' руб.');
	});

	if($(".range input").length>0){
		$(".range input").slider({
			tooltip: 'hide'
		});
	}
	
	$.noty.defaults = {
        layout: 'top',
        theme: 'bootstrapTheme',
        type: 'alert',
        text: '', // can be html or string
        dismissQueue: true, // If you want to use queue feature set this true
        template: '<div class="noty_message"><span class="noty_text"></span><div class="noty_close"></div></div>',
        animation: {
            open: {height: 'toggle'},
            close: {height: 'toggle'},
            easing: 'swing',
            speed: 300 // opening & closing animation speed
        },
        timeout: true, // delay for closing event. Set false for sticky notifications
        force: true, // adds notification to the beginning of queue when set to true
        modal: false,
        maxVisible: 5, // you can set max visible notification for dismissQueue true option,
        killer: true, // for close all notifications before show
        closeWith: ['button'], // ['click', 'button', 'hover']
        callback: {
            onShow: function() {},
            afterShow: function() {},
            onClose: function() {},
            afterClose: function() {}
        },
        buttons: false // an array of buttons
    };
	$('.close-filter, .header-filter .open-filter').tooltip();
	if($('.qutable').length>0){
		$('.qutable').addClass('table');
		$('.quhead_empt').remove();
		$(".quhead").each(function(){
		  if ( $(this).text() == ' ' ) 
		  {
			$(this).parent('.qugroup').remove();
		  }
		});
		
		$(".qutable td").each(function(){
		  if ( $(this).html() == 'Да &nbsp;' ) 
		  {
			$(this).html('<img src="/1teh/img/yes.png" />');
		  }
		   if ( $(this).html() == 'Нет &nbsp;' ) 
		  {
			$(this).html('<img src="/1teh/img/no.png" />');
		  }
		  if ( $(this).html() == 'Да&nbsp;' ) 
		  {
			$(this).html('<img src="/1teh/img/yes.png" />');
		  }
		  if ( $(this).html() == 'Нет&nbsp;' ) 
		  {
			$(this).html('<img src="/1teh/img/yes.png" />');
		  }
		   if ( $(this).html() == 'Да ' ) 
		  {
			$(this).html('<img src="/1teh/img/yes.png" />');
		  }
		});

	}
	
	$('.prod input[type="checkbox"], .add-to input[type="checkbox"], #filters input[type="checkbox"], .order-action input[type="radio"]').iCheck({
		checkboxClass: 'icheckbox_minimal',
		radioClass: 'iradio_minimal'
	});
	

	var maxHeight = 0;
 
	$(".polka > .prod, .carousel-inner > .item > .prod").each(function(){
	  if ( $(this).height() > maxHeight ) 
	  {
		maxHeight = $(this).height();
	  }
	});
	
	$(".polka > .prod, .carousel-inner > .item > .prod").height(maxHeight+45);
	
	
	var maxHeightTitle = 0;
 
	$(".discount .h1").each(function(){
	  if ( $(this).height() > maxHeight ) 
	  {
		maxHeightTitle = $(this).height();
	  }
	});
	
	$(".discount .h1").height(maxHeightTitle);
});
