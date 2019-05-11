(function($){$.extend({
	notifier: {
		options: {
			core:				"notifier",
			box_class: 			"n-box",
			notice_class: 		"n-notice",
			error_class: 		"n-error",
			close_class: 		"n-close",
			duration:			10000
		},
		notices:{},


		broadcast: function(title, message, type, url, blank){
			this.core();

			var id = "notice-" + this.timestamp();

			// set notices object
			this.notices[id] = {id: id};
			let notice = {
				id: id,
				ttl: title,
				msg: message,
				url: url,
				blank: blank
			}

			// box
			$("#" + this.options.core).append(this.box(notice).addClass(type));
		},


		notice: function(title, message){
			this.broadcast(title, message, this.options.notice_class);
		},


		error: function(title, message){
			this.broadcast(title, message, this.options.error_class);
		},


		core: function(){
			var core	= this.options.core;
			return $("#" + core).length == 0 ? $('body').append("<div id=\"" + core + "\"></div>") : $("#" + core);
		},


		box: function(notice){
			var box	= $(`<a id="${notice.id}" class="${this.options.box_class}" href="${notice.url}" ${notice.blank?`target="_blank"`:""}></a>`);
			$(`<a onclick="return false;" href="#"><i class="close material-icons">close</i></a>`).appendTo(box).click(function(){
                var seed = $(this).parent('.n-box').attr("id");
                $.notifier.destroy(seed, true);
                return false;
			})
			if (notice.ttl != null) box.append($("<h3></h3>").append(notice.ttl));
			box.append($("<p></p>").append(notice.msg));
			box.hide().show();
			this.life(box, notice.id);
			this.events(box, notice.id);
			return box;
		},


		events: function(box, seed){
			$(box).bind(
				'mouseover',
				function(){
					if($.notifier.notices[$(this).attr("id")].interval){
						let seed = $(this).attr("id");
						$.notifier.destroy(seed)
					}
				}
			)
			$(box).bind(
				'mouseout',
				function(){
					$.notifier.life(this, $(this).attr("id"));
				}
			)
		},


		life: function(box, seed){
			if(!this.notices[seed].duration){this.notices[seed].duration = this.options.duration}
			this.notices[seed].interval = {};
			this.notices[seed].interval	= setInterval(
				function(){
					(function(seed){
						$.notifier.destroy(seed, true)
					})
					(seed)
				},
				this.notices[seed].duration
			)
		},


		destroy: function(seed, remove){
			clearInterval($.notifier.notices[seed].interval);
			delete $.notifier.notices[seed].interval;
			if(remove == true){$("#" + seed).slideUp(250, function(){$(this).remove()});}
		},


		timestamp:function(){
			return new Date().getTime();
		}
	}
})})(jQuery)
