(function($) {

	$.noty.themes.bootstrapTheme = {
		name: 'bootstrapTheme',
		modal: {
			css: {
				position: 'fixed',
				width: '100%',
				height: '100%',
				backgroundColor: '#000',
				zIndex: 10000,
				opacity: 0.6,
				display: 'none',
				right: 0,
				top: 0
			}
		},
		style: function() {
		
			var containerSelector = this.options.layout.container.selector;
			$(containerSelector).addClass('list-group');
			
			this.$closeButton.append('<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>');
			this.$closeButton.addClass('close');
		
			this.$bar.addClass( "list-group-item" );

			switch (this.options.type) {
				case 'alert': case 'notification':
					this.$bar.addClass( "list-group-item-info" ); 
					break;
				case 'warning':
					this.$bar.addClass( "list-group-item-warning" );
					break;
				case 'error':
					this.$bar.addClass( "list-group-item-danger" );
					break;
				case 'information':
					this.$bar.addClass("list-group-item-info");
					break;
				case 'success':
					this.$bar.addClass( "list-group-item-success" );
					break;
			}
		},
		callback: {
			onShow: function() {  },
			onClose: function() {  }
		}
	};

})(jQuery);

