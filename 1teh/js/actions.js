$(document).ready(function(){

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
	if($('.qutable .qugroup_empt').size()<1) {
		if($('.qutable').length>0){
		
			$( ".qutable tbody tr" ).each(function(index) {
			
				var counter = 0;
				if(index%2==0){
					counter = 2;
				} else {
					counter = '';
				}

				if($(this).find('td').size()==1 && $(this).find('td').html()!='&nbsp;'){
					$(this).addClass('qugroup');
					$(this).find('td').addClass('quhead');
				}
				if($(this).find('td').size()==2){
					$(this).addClass('qurow'+counter);
					$(this).find('td:first-child').addClass('qurowtitle'+counter);
					$(this).find('td:last-child').addClass('qurowvalue'+counter);
				}
				if($(this).find('td').html()==' ' || $(this).find('td').html()=='&nbsp;'){
					$(this).addClass('qugroup_empt');
					$(this).html('');
				}
			 
			});
		}
	}
	
	function isValidEmailAddress(emailAddress) {
		var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
		return pattern.test(emailAddress);
	}
	var ender = '';
	
	//поиск
	$(".search input").keyup(function(e){
		
		$('.search a.action-search').addClass('loading');
		
		var q = $(".search input").val();
		var ajax = null;
		
		
		function search_func(value)
		{
			if (ajax != null) ajax.abort();

			ajax = $.ajax({
				url: "/search/ajax",
				data: {q: q, order: 'price',dest: 'asc'},
				type: "post",
				dataType : "json",                           
				success: function(result){
					$('.search a.action-search').removeClass('loading');
					$('.search.hide-res').removeClass('hide-res');
					$("#result").html(result.view);
					var load = true;
				}
			});
		}
		
		search_func(q);
		
		return false;
   });

	//добавление в избранное
	$('.prod input[type="checkbox"], .add-to input[type="checkbox"]').on('ifChecked', function(){
		
		var id = $(this).attr('data-id');
		var label = $(this).attr('id');
		
		if(id){
			$.ajax({
				url : "/like/add",
				dataType : "json",
				type : "post",
				data : {id : id},
				success : function(jsondata) {
					if(jsondata.summlikes>1 && jsondata.quantity<5){ender='а';}
					if(jsondata.summlikes>4){ender='ов';}
					if(jsondata.summlikes==1){ender='';}
					$(".right-top-menu .favorit").html('<span>Избранное - ' + jsondata.summlikes + ' товар'+ ender +'</span>');
					$("label[for="+ label +"]").text('Добавлено');
					noty({
						text: 'Товар добавлен в <a href="/like"><strong>избранное</strong></a>',
						type: 'success',
						layout: 'topRight',
						timeout: 2000
					});
				}
			});
		}
		return false;
	});
	
	//удаление из избранного
	$('.prod input[type="checkbox"], .add-to input[type="checkbox"]').bind('ifUnchecked', function(){
	
		var id = $(this).attr('data-id');
		var label = $(this).attr('id');
		
		if(id){
			$.ajax({
				url : "/like/delete",
				dataType : "json",
				type : "post",
				data : {id : id},
				success : function(jsondata) {
					if(jsondata.summlikes>1 && jsondata.quantity<5){ender='а';}
					if(jsondata.summlikes>4){ender='ов';}
					if(jsondata.summlikes==1){ender='';}
					$(".right-top-menu .favorit").html('<span>Избранное - ' + jsondata.summlikes + ' товар'+ ender +'</span>');
					$("label[for="+ label +"]").text('Добавить в избранное');
					noty({
						text: 'Товар удалён из <a href="/like"><strong>избранного</strong></a>',
						type: 'error',
						layout: 'topRight',
						timeout: 5000
					});
				}
			});
		}
	});
	
	var price = $('.cartlayer .price.big span').text().replace(/\s+/g, '');
	
	$('.payment input[type="radio"]').on('ifChecked', function(){
		
		var val = $(this).val();
		var pricebig = $('.cartlayer .price.big span').text().replace(/\s+/g, '');
		if(val == 2){
			$('.cartlayer .price.big span').text(number_format(Math.round(pricebig*1.03), 0, '', ' '));
		} else {
			$('.cartlayer .price.big span').text(number_format(price, 0, '', ' '));
		}
		return false;
	});

	//добавление в корзину
	
	$('[data-type=add_to_cart]').click(function(e) {
		
		e.preventDefault();
		var btn = $(this);
		var id = $(this).attr('data-id');
		var quantity = $('input[name="quantity['+id+']"]').val();
		var price = $('input[name="price['+id+']"]').val();
		var errors = 0;
		
		if (!id) {
			errors++;		
		}
		if (!quantity) {
			quantity = 1;		
		}
		if (errors) {
			return false;			
		}
		if(btn.attr('data-type')=='go_to_cart'){
			document.location.href = "/cart";
			return false;
		}
		$.ajax({
			url : "/cart/add",
			dataType : "json",
			type : "post",
			data : {id : id, quantity : quantity, price : price },
			beforeSend: function() {
				$(".right-top-menu .mycart a").removeClass('lighter');
				},
			success : function(jsondata) {
				if(jsondata.quantity>1 && jsondata.quantity<5){ender='а';}
				if(jsondata.quantity>4){ender='ов';}
				if(jsondata.quantity==1){ender='';}
				if(jsondata.quantity>0){
					$(".right-top-menu .mycart").html('<span>' + jsondata.quantity + ' товар' + ender + ' на ' + number_format(jsondata.price, 0, '', ' ') + ' руб.</span>');
					$(".right-top-menu .mycart").addClass('lighter');
					noty({
							text: 'Товар добавлен в <a href="/cart"><strong>корзину</strong></a>',
							type: 'success',
							layout: 'topRight',
							timeout: 2000
						});
					btn.attr('data-type', 'go_to_cart');
					btn.attr('rel', 'go-to-cart');
					btn.html('Оформить');
				}
			}
		});
	});
	$('[data-type=go_to_cart]').click(function(e) {
		e.preventDefault();
		document.location.href = "/cart";
		return false;
	});
	//удаление из корзины
	$('a[data-type=remove_from_cart]').click(function(e) {
			
			e.preventDefault();
			var id = $(this).attr('data-id');
			
			$.ajax({
				url : "/cart/delete",
				dataType : "json",
				type : "post",
				data : {id : id},
				success : function(jsondata) {
					if(jsondata.price!=0){
						if(jsondata.quantity>1 && jsondata.quantity<5){ender='а';}
						if(jsondata.quantity>4){ender='ов';}
						if(jsondata.quantity==1){ender='';}
						$("tr[data-id=" +id + "]").slideUp(100);
						$("tr[data-id=" +id + "]").remove();
						$(".right-top-menu .mycart").addClass('lighter');
						$(".right-top-menu .mycart").html('<span>' + jsondata.quantity + ' товар' + ender + ' на ' + number_format(jsondata.price, 0, '', ' ' ) + ' руб.</span>');
						$(".price.big").text(number_format(jsondata.price, 0, '', ' ' ) + ' руб.');
						noty({
							text: 'Товар удалён из <a href="/cart"><strong>корзины</strong></a>',
							type: 'error',
							layout: 'topRight',
							timeout: 2000
						});
					} else {
						window.top.location = '/cart';
					}
				}
			});
	});
	
	//удаление из избранного
	$('a[data-type=remove_from_like]').click(function(e) {
		
		e.preventDefault();
		var id = $(this).attr('data-id');
		
		if(id){
			$.ajax({
				url : "/like/delete",
				dataType : "json",
				type : "post",
				data : {id : id},
				success : function(jsondata) {
					if(jsondata.summlikes!=0){
						if(jsondata.summlikes>1 && jsondata.quantity<5){ender='а';}
						if(jsondata.summlikes>4){ender='ов';}
						if(jsondata.summlikes==1){ender='';}
						$("tr[data-id=" +id + "]").slideUp(100);
						$("tr[data-id=" +id + "]").remove();
						$(".right-top-menu .favorit").html('<span>Избранное - ' + jsondata.summlikes + ' товар'+ ender +'</span>');
						noty({
							text: 'Товар удалён из <a href="/cart"><strong>избранного</strong></a>',
							type: 'error',
							layout: 'topRight',
							timeout: 2000
						});
					} else {
						window.top.location = '/like';
					}
				}
			});
		}
	});
	
	
	//оформление заказа
	$('a[data-type=add_order]').click(function(e) {
		
		e.preventDefault();
		var name = $('input[name="name"]').val();
		var email = $('input[name="email"]').val();
		var phone = $('input[name="phone"]').val();
		var adress = $('input[name="adress"]').val();
		var comments = $('textarea[name="comments"]').val();
		var errors = 0;
		
		if (!name) {
			$('input[name=name]').addClass('error');
			errors++;		
		} else {
			$('input[name=name]').removeClass('error');
		}
		if(email != '') {
            if(isValidEmailAddress(email)){
                $('input[name=email]').removeClass('error');
            } else {
                $('input[name=email]').addClass('error');
				errors++;	
            }
        } else {
            $('input[name=email]').addClass('error');
			errors++;	
        }
		if (!phone) {
			$('input[name=phone]').addClass('error');
			errors++;			
		} else {
			$('input[name=phone]').removeClass('error');
		}
		if (!adress) {
			$('input[name=adress]').addClass('error');
			errors++;		
		} else {
			$('input[name=adress]').removeClass('error');
		}
		if (!comments) {
			comments = '';		
		}
		if (errors) {
			return false;			
		}
		$.ajax({
			url : "/cart/order",
			dataType : "json",
			type : "post",
			data : {name : name, email : email, phone : phone, adress : adress, comments : comments},
			success : function(jsondata) {
				$(".cartlayer").html('<div class="alert alert-success">Спасибо, ваш заказ принят! В ближайшее время с Вами свяжется наш менеджер.</div>');
				$(".right-top-menu .mycart").html('<span>Корзина пуста</span>');
			}
		});
	});
		
	//оставить отзыв
	$('a[data-type=add_review]').click(function(e) {
		
		e.preventDefault();
		$('#reviews').addClass('loading');
		
		var prod_id = $(this).attr('data-id');
		var name = $('#rev-name').val();
		var title = $('input[name="title"]').val();
		var rating = $('input[name="rating"]').val();
		var content = $('textarea[name="content"]').val();
		var errors = 0;
		
		if (!name) {
			$('input[name=name]').addClass('error');
			errors++;		
		} else {
			$('input[name=name]').removeClass('error');
		}
		if (!title) {
			$('input[name=title]').addClass('error');
			errors++;		
		} else {
			$('input[name=title]').removeClass('error');
		}
		if (!content) {
			$('textarea[name=content]').addClass('error');
			errors++;			
		} else {
			$('textarea[name=content]').removeClass('error');
		}
		if (errors) {
			$('#reviews').removeClass('loading');
			return false;			
		} 
		$.ajax({
			url : "/reviews/add",
			dataType : "json",
			type : "post",
			data : {prod_id: prod_id, name : name, title : title, content : content, rating : rating},
			success : function(jsondata) {
				$('#reviews').removeClass('loading');
				$('#reviews').html('<h3>Спасибо за оставленный отзыв!</h3>');
				
			}
		});
	});
	
	//купить в 1 клик
	$('a[data-type=fast_order]').click(function(e) {
	
		e.preventDefault();
		var alert = '<div class="clear"></div><div class="alert alert-success" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><strong>Ваш заказ принят!</strong> В ближайшее время с Вами свяжется наш менеджер.</div>';
		var name = $('input[name="name"]').val();
		var phone = $('input[name="phone"]').val();
		var adress = $('input[name="adress"]').val();
		var id = $(this).attr('data-id');
		var errors = 0;
		
		if (!name) {
			$('input[name=name]').addClass('error');
			errors++;		
		} else {
			$('input[name=name]').removeClass('error');
		}
		if (!phone) {
			$('input[name=phone]').addClass('error');
			errors++;			
		} else {
			$('input[name=phone]').removeClass('error');
		}
		if (!adress) {
			$('input[name=adress]').addClass('error');
			errors++;		
		} else {
			$('input[name=adress]').removeClass('error');
		}
		if (errors) {
			return false;			
		}
		$.ajax({
			url : "/cart/oneclick",
			dataType : "json",
			type : "post",
			data : {id : id, name : name, phone : phone, adress : adress},
			success : function(jsondata) {
				$('#fastbuy').modal('hide');
				$(".product .add-to-cart").append(alert);
			}
		});
	});
	
	//задать вопрос по товару
	$('.submit_qa').click(function(e) {
		
		e.preventDefault();
		var product_id = $('input[name="product_id"]').val();
		var name_qa = $('input[name="name_qa"]').val();
		var phone_qa = $('input[name="phone_qa"]').val();
		var product_qa = $('input[name="product_qa"]').val();
		var qa = $('textarea[name="qa"]').val();
		var errors = 0;
		
		if (!name_qa) {
			$('input[name=name_qa]').addClass('error');
			errors++;		
		} else {
			$('input[name=name_qa]').removeClass('error');
		}
		if (!product_qa) {
			$('input[name=product_qa]').addClass('error');
			errors++;		
		} else {
			$('input[name=product_qa]').removeClass('error');
		}
		if (!phone_qa) {
			$('input[name=phone_qa]').addClass('error');
			errors++;		
		} else {
			$('input[name=phone_qa]').removeClass('error');
		}
		if (!qa) {
			$('textarea[name=qa]').addClass('error');
			errors++;			
		} else {
			$('textarea[name=qa]').removeClass('error');
		}
		if (errors) {
			return false;			
		} 
		$.ajax({
			url : "/qa/addq",
			dataType : "json",
			type : "post",
			data : {product_id: product_id, product_qa: product_qa, name_qa : name_qa, phone_qa : phone_qa, qa : qa},
			success : function(jsondata) {
				$('#qa').html('<h4>Ваш вопрос отправлен администратору магазина, в ближайшее время с вами свяжется наш специалист.</h4>');
			}
		});
	});
	
	$("ul[data-type=rating] li").hover(
	  function() {
		destination = $(this).offset();
		$(this).find('menu').css('top',destination+49)
	  }, 
	  function() {

	  }
	);
	var fix = 0;
	$("#reviews ul[data-type=rating] li").click(function () {
		var fix = 1;
		var start = 0;
		var end = $(this).attr('star');
		$(this).parent('ul').find('li').slice(1, 5).removeClass('active');
		$(this).parent('ul').find('li').slice(1, 5).removeClass('notactive');
		$(this).parent('ul').find('li').slice(start, end).addClass('active');
		$(this).parent('ul').find('li').slice(5-end, 5).addClass('notactive');
		$('#rev-rating').val(end);
	});
	
	$("#reviews ul[data-type=rating] li").hover(
	  function() {
		var start = 0;
		var end = $(this).attr('star');
		$(this).parent('ul').find('li').slice(start, end).addClass('hover');
		$(this).parent('ul').find('li').slice(end, 5).addClass('nothover');
	  }, 
	  function() {
		if(fix==0){
		var start = 0;
		var end = $(this).attr('star');
		$(this).parent('ul').find('li').slice(start, end).removeClass('hover');
		$(this).parent('ul').find('li').slice(end, 5).removeClass('nothover');
		}
	  }
	);

	/* $(".main-menu > ul > li > a").click(function (e) { 
		
		if($(this).parent('li').find('.menu').length>0) { 
		$(".menu-catalog").addClass('overflower');
		e.preventDefault();
		//if(this).parent('li.active'){
		$(e.target).closest(".main-menu").length;
		$(this).parent('li').addClass('clicked');
		$(".main-menu > ul > li:not(.clicked)").removeClass('active');
		//}
		$(this).parent('li').toggleClass('active');
		$(this).parent('li').removeClass('clicked');
		//alert(123)
		}
	});
	
	$(".menu-catalog .before, .menu-catalog .after, .menu-catalog .burger").click(function (e) { 
		$(".menu-catalog").toggleClass('toggle-top');
		$(".main-menu > ul > li .menu").removeClass('clicked');
		$(".main-menu > ul > li").removeClass('active');
		
	});
	
	$(".main-menu > ul > li.active a").click(function (e) { 
		e.preventDefault();	
		$(".menu-catalog").removeClass('overflower');
		$(".main-menu > ul > li").removeClass('active');
		$(".main-menu > ul > li .menu").removeClass('clicked');
	}); */
	
	$('.nav-btn').click(function(event){
		$(this).parent('.main-menu').toggleClass('open');
	});
	
	var time = 0;
		
	$( ".menu-container > li" ).hover(
	  function() {
		var self = $(this);
		timer = setTimeout ( function () {
			$('.menu-container').removeClass('hover');
			$('.menu-container > li.active').removeClass('active');
			self.parent('.menu-container').addClass('hover');
			self.addClass('active');
			time = 70;
		}, time);
	  }, function() {
		clearTimeout(timer);
	  }
	);
	

	$(document).click( function(event){
		if($(event.target).closest(".main-menu").length == 0) {
			$(".main-menu").removeClass('open');
			$(".main-menu .menu-container li.active").removeClass('active');
		}
		if($(event.target).closest(".search").length== 0) {
			$(".search").addClass('hide-res');
		}
		event.stopPropagation();
    });
	
	
	$(".main-menu .glyphicon-remove").click( function(e){
		$(".main-menu > ul > li.active").removeClass('active');
	});
	
	
	$("a.to-reviews").click(function () {  
		$('a[href="#reviews"]').tab('show');
	});
	$("a.to-prop").click(function () {  
		$('a[href="#short-prop"]').tab('show');
	});
	//переместиться к id
	$("a.scrollhref").click(function () {  
		elementClick = $(this).attr('href'); 
		destination = $(elementClick).offset();
		$("body").animate({ 'scrollTop': destination.top-50}, 666 ); 
		return false; 
	});
	
	$( ".header-filter" ).click(function() {
	  $('.left-side').hide();
	  $('.content-side').removeClass('col-xs-8');
	  $('.content-side').addClass('col-xs-12');
	   $('.content-side .polka .col-md-6').removeClass('col-md-6').addClass('col-md-4');
	});
	
	$( ".open-filter" ).click(function() {
	  $('.left-side').show();
	  $('.content-side').removeClass('col-xs-12');
	  $('.content-side').addClass('col-xs-8');
	   $('.content-side .polka .col-md-4').removeClass('col-md-4').addClass('col-md-6');
	});
	
	$( "#all_filters_link" ).click(function(e) {
		e.preventDefault();	
	  $('.support_filters').toggle();
	});
	
});
