(function($){
	$.fn.extend({
		Tabs:function(options){
			options = $.extend({
				event : 'mouseover',
				timeout : 0,
				auto : 0,
				callback : null
			}, options);
			
			var self = $(this),
				tabList = self.children('.tab-list').children('div'),
				menu = self.find('.ui-tab'),
				items = menu.find('li'),
				timer;
				
			var tabHandle = function( elem ){
					elem.siblings('li')
						.removeClass('cur')
						.end()
						.addClass('cur');
						
					tabList.siblings('div')
						.addClass('hide')
						.end()
						.eq(elem.index())
						.removeClass('hide');
				},
					
				delay = function(elem, time){
					time ? setTimeout(function(){ tabHandle( elem ); }, time) : tabHandle( elem );
				},
				
				start = function(){
					if(!options.auto) return;
					timer = setInterval( autoRun, options.auto );
				},
				
				autoRun = function(){
					var on = menu.find('.cur'),
						firstItem = items.eq(0),
						len = items.length,
						index = on.index() + 1,
						item = index === len ? firstItem : on.next('li'),
						i = index === len ? 0 : index;
					
					on.removeClass('cur');
					item.addClass('cur');
					
					tabList.siblings('div')
						.addClass('hide')
						.end()
						.eq(i)
						.removeClass('hide');
				};
							
			items.bind(options.event, function(){
				delay($(this), options.timeout);
				if(options.callback){
					options.callback(self);
				}
			});
			
			if(options.auto){
				start();
				self.hover(function(){
					clearInterval(timer);
					timer = undefined;
				},function(){
					start();
				});
			}
			return this;
		}
	});
})(jQuery);