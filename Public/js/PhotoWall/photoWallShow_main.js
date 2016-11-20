jQuery.fn.extend({
	/**
     * 设置计时器.
     * @param (Function)fun 回调函数.
     * @param (Number)time 回调间隔.
     * @param (Number)iCount 回调次数.
     * @return (Object)this 支链式调用.
     * @author Eded.Wang
     */
    setTimer: function() {
        var fun = arguments[0] || $.noop, //设置回调函数名称.
                time = arguments[1] || 2000, //设置回调时间间隔.
                iCount = arguments[2] || 0, //设置回调次数.
                iNum = 0, //已经执行的次数.
                that = this, //缓存当前对象.
                id = this.data("timer"), //设置缓存变量.
                timer = null;  //缓存计时标识.

        switch (true) {
            case iCount == 0:  //循环一次.
                timer = setTimeout(function() {
                    fun.apply(that, [iNum])
                }, time)
                break;
            case iCount > 0:  //循环指定次数.
                timer = setInterval(function() {
                    iCount -= 1;
                    iNum += 1;
                    iCount >= 0 ? fun.apply(that, [iNum]) : clearTimeout(id);
                }, time);
                break;
            default:  //进行无限循环.
                timer = setInterval(function() {
                    iNum += 1;
                    fun.apply(that, [iNum]);
                }, time);
        }

        /* 添加计时器标识. */
        that.data("timer", timer);

        return that;
    },
    /**
     * 停止计时器.
     * @return (Object)this 支持链式调用.
     * @author Eded.Wang
     */
    stopTimer: function() {
        /* 清除计时器. */
        clearTimeout(this.data("timer-num", 0).data("timer"));
        return this;
    },
	/**
     * 改变透明度和高度的展示动画.
     * @return 执行动画并返回方法.
     */
    slideFadeShow: function(speed, easing, callback) {
        return this.animate({
            opacity: "show",
            height: "show"
        }, speed, easing, callback);
    },
	/**
     * 改变透明度和高度的隐藏动画.
     * @return 执行动画并返回方法.
     */
    slideFadeHide: function(speed, easing, callback) {
        return this.animate({
            opacity: "hide",
            height: "hide"
        }, speed, easing, callback);
    },
    /**
     * 改变透明度和高度的收放动画.
     * @return 执行动画并返回方法.
     */
    slideFadeToggle: function(speed, easing, callback) {
        return this.animate({
            opacity: "toggle",
            height: "toggle"
        }, speed, easing, callback);
    }
});

(function($) {

	var origin = location.origin,
		apiurl = origin+'/index.php';
	
	
	/**
	 * 初始化模板引擎数据.
	 * @author eded.wang
	 * @return unf;
	 */
	function inittm() {
		
		/* 主页模板初始化. */
		if( $("#tm-index-scene-list").length ) {
			/* Page index. */
			$.getJSON(apiurl, {
				"m":"content",
				"c":"index",
				"a":"json_getlist",
				"catid":"6",
				"page":""
			}, function(json) {
				$("#index-scene-list").html(
					template("tm-index-scene-list", {list:json.data})
				);
				
				/* Event Delegation */
				$(".scene-list .simple-fold").on("click", function() {
					$(this).toggleClass("actived").parent().prev().slideFadeToggle("fast");
				}).first().triggerHandler("click");
			});
		}
		
		return;
	}
	
	origin != 'file://' && inittm();
	
	
	var defaultCurrency = "cnytoeur",
		$exchangeRate = $("#exchange-rate");
	
	function exchangeRateInit() {
		
		var $exchangeRateHead = $exchangeRate.children(".head"),
			$exchangeRateBody = $exchangeRate.children(".body"),
			$exchangeRateSelects = $exchangeRateHead.find(".form-select"),
			$exchangeRateSearchBtn = $exchangeRate.find(".search-btn");
		
		$exchangeRateSelects.first().val( defaultCurrency.split("to")[0] );
		$exchangeRateSelects.last().val( defaultCurrency.split("to")[1] );
		
		/* Event Delegation */
		$exchangeRateSearchBtn.on("click", function() {
			
			if( origin == 'file://' ) { return; }
			
			/* Page exchange-rate. */
			$.getJSON(apiurl, {
				"m":"content",
				"c":"index",
				"a":"json_gethl"
			}, function(json) {
				
				var $sel1 = $exchangeRateSelects.eq(0),
					$sel2 = $exchangeRateSelects.eq(1),
					price = $exchangeRateHead.find("[name=price]").val(),
					key = $sel1.val() + "to" + $sel2.val(),
					data = {
					"num": json.data[key],
					"nowName": $sel1.find("option:selected").text(),
					"aimName": $sel2.find("option:selected").text(),
					"nowShow": 0,
					"aimShow": price
				};
				
				data.num && (
					data.nowShow = data.aimShow * data.num
				);
				
				$exchangeRateBody.find("tbody").html(
					template("tm-exchange-rate-table-body", data)
				);
				$exchangeRateBody.show();
			});
		});
		
		$("#exchange-rate-swop-btn").on("click", function() {
			var $parent = $(this).parent(),
				$select = $parent.find("select"),
				$sel1 = $select.eq(0).clone().val( $select.eq(0).val() ),
				$sel2 = $select.eq(1).clone().val( $select.eq(1).val() );
				
			$select.eq(0).replaceWith($sel2);
			$select.eq(1).replaceWith($sel1);
			
			/* up. */
			$exchangeRateSelects = $exchangeRateHead.find(".form-select");
			$exchangeRateSearchBtn.trigger("click");
		});
		
		return;
	}
	
	$exchangeRate.length && exchangeRateInit();
	
	
	
	/* Return Top. */
	$("#retop").on("click", function() {
		$("html,body").animate({"scrollTop":0}, "slow");
	});
	
	/**
	 * 缩放相册对象创建.
	 */
	function ZoomPhoto() {}
	
	/**
	 更换图片.
	 @param dir true:下一个 false:上一个.
	 */
	ZoomPhoto.up = function(dir) {
		var zp = ZoomPhoto;
			
		zp.now += dir ? 1 : -1;
		zp.now < 0 && (zp.now = zp.len - 1);
		zp.now >= zp.len && (zp.now = 0);
		
		zp.$img = zp.$imgs.eq(zp.now).clone();
		zp.$body.html( zp.$img.height(zp.initHeight) );
	}
	
	/**
	 控制图片放大和缩小.
	 @param up true:放大 false:缩小.
	 */
	ZoomPhoto.zoom = function(up) {
		var zp = ZoomPhoto,
			$img = zp.$img,
			height = $img.height(),
			val = (up ? "+" : "-") + "="+ zp.zoomNum;
		
		if( height <= 100 && !up ) { return; }  //过小保护.
		
		$img.height(val);
	}
	
	/**
	 * 初始化缩放相册程序.
	 * @param {ZoomPhoto} 缩写.
	 */
	ZoomPhoto.init = function(zp) {
		
		/* 捕获对象. */
		ZoomPhoto.$ = $("#zoom-photo");
		
		//if( zp.len <= 0 ) { return; }
		if( !ZoomPhoto.$.length ) { return; }
		
		/* 初始化属性. */
		ZoomPhoto.$body = ZoomPhoto.$.find(".body");
		ZoomPhoto.$foot = ZoomPhoto.$.find(".foot");
		ZoomPhoto.$btns = ZoomPhoto.$foot.find("span");
		ZoomPhoto.$link = $(".zoom");
		ZoomPhoto.$imgs = ZoomPhoto.$link.find("img");
		ZoomPhoto.$shade = $("#shade");
		ZoomPhoto.$zoomInBtn = ZoomPhoto.$btns.filter(".zoom-in");
		ZoomPhoto.$zoomOutBtn = ZoomPhoto.$btns.filter(".zoom-out");
		ZoomPhoto.$zoomNextBtn = ZoomPhoto.$btns.filter(".zoom-next");
		ZoomPhoto.$zoomPrevBtn = ZoomPhoto.$btns.filter(".zoom-prev");
		ZoomPhoto.$img = ZoomPhoto.$imgs.eq(0);
		ZoomPhoto.zoomNum = 50;  //图片放大闕值.
		ZoomPhoto.len = ZoomPhoto.$link.length;
		ZoomPhoto.now = 0;
		ZoomPhoto.initHeight = 250;
		
		
		/* Event Delegation */
		
		zp.$body.html("").on({
			"touchstart mousedown": function(e) {
				var e = e.originalEvent,
					oe = e.targetTouches,
					x = oe ? oe[0].pageX : e.pageX,
					y = oe ? oe[0].pageY : e.pageY;
				
				$(this).data("offset", {"x":x, "y":y});
				e.preventDefault();
			},
			"touchmove mousemove": function(e) {
				var $that = $(this),
					offset = $that.data("offset") || {"x":0, "y":0},
					pos = $that.data("pos") || {"x":0, "y":0},
					e = e.originalEvent,
					oe = e.targetTouches,
					x = oe ? oe[0].pageX : e.pageX,
					y = oe ? oe[0].pageY : e.pageY,
					mx = offset.x + pos.x - x,
					my = offset.y + pos.y - y;
				
				if( e.type == "mousemove" && e.which <= 0 ) { return; }
				
				$that.scrollLeft(mx).scrollTop(my);
			},
			"touchend mouseup": function(e) {
				$(this).data("pos", {
					"x": $(this).scrollLeft(),
					"y": $(this).scrollTop()
				});
			}
		});
		
		zp.$link.on("click", function() {
			
			zp.now = $(this).index(".zoom");
			zp.$img = zp.$imgs.eq(zp.now).clone();
			
			zp.$.delay(200).fadeIn("fast", function() {
				zp.$body.html( zp.$img.height(zp.initHeight) );
			})
			
			zp.$shade.fadeIn("fast").data("uid", zp.$).one("click", function() {
				var $that = $(this);
				zp.$.fadeOut("fast", function() {
					$that.fadeOut("fast");
				});
			});
			
			return false;
		});
		
		zp.$zoomInBtn.on("click", function() {
			zp.zoom(true);
		});
		
		zp.$zoomOutBtn.on("click", function() {
			zp.zoom(false);
		});
		
		zp.$zoomNextBtn.on("click", function() {
			zp.up(true);
		});
		
		zp.$zoomPrevBtn.on("click", function() {
			zp.up(false);
		});
		
	}
	
	ZoomPhoto.init(ZoomPhoto);
	
})(jQuery);